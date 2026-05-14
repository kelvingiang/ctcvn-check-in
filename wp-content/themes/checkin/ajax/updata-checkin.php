<?php
define('WP_USE_THEMES', false);
// Load wp-load.php an toàn
require_once(dirname(__FILE__) . '/../../../../wp-load.php'); 
date_default_timezone_set('Asia/Ho_Chi_Minh');

$a_barcode = isset($_POST['id']) ? sanitize_text_field(trim($_POST['id'])) : '';

if (!empty($a_barcode)) {
    global $wpdb;
    $tbl_guests    = $wpdb->prefix . 'guests';
    $tbl_check_in  = $wpdb->prefix . 'guests_check_in';
    $tbl_event     = $wpdb->prefix . 'guests_check_in_event';

    // BẢO MẬT: Chống SQL Injection
    $sqlGuests = $wpdb->prepare(
        "SELECT * FROM $tbl_guests WHERE (barcode = %s OR app_code = %s) LIMIT 1", 
        $a_barcode, 
        $a_barcode
    );
    $row = $wpdb->get_row($sqlGuests, ARRAY_A);

    if (!empty($row) && $row['status'] == 1) {
        
        $eventRow = $wpdb->get_row("SELECT ID FROM $tbl_event WHERE status = 1 LIMIT 1", ARRAY_A);
        
        $checkInCount = 0;
        $lastCheckInDate = '';
        $lastCheckInTime = '';

        if (!empty($eventRow)) {
            $eventId = $eventRow['ID'];

            // TỐI ƯU: Lấy thẳng giá trị số (string) thay vì lấy cả mảng
            $sqlCount = $wpdb->prepare(
                "SELECT COUNT(*) FROM $tbl_check_in WHERE guests_id = %d AND event_id = %d",
                $row['ID'], 
                $eventId
            );
            $checkInCount = $wpdb->get_var($sqlCount);

            $sqlLast = $wpdb->prepare(
                "SELECT date, time FROM $tbl_check_in WHERE guests_id = %d AND event_id = %d ORDER BY date DESC, time DESC LIMIT 1",
                $row['ID'], 
                $eventId
            );
            $checkInLast = $wpdb->get_row($sqlLast, ARRAY_A);
            
            if ($checkInLast) {
                $lastCheckInDate = $checkInLast['date'];
                $lastCheckInTime = $checkInLast['time'];
            }

            $wpdb->insert(
                $tbl_check_in, 
                array(
                    'guests_id' => $row['ID'],
                    'event_id'  => $eventId,
                    'date'      => date('m-d-Y'),
                    'time'      => date('H:i:s'),
                ), 
                array('%d', '%d', '%s', '%s')
            );
        }

        $img = '';
        if (!empty($row['img'])) {
            $img = "<img id='guest-pic' name='guest-pic' class='img-fluid shadow-sm rounded' src='" . PART_IMAGES_GUESTS . esc_attr($row['img']) . "'/>";
        } else {
            $img = "<img id='guest-pic' name='guest-pic' class='img-fluid opacity-25' src='" . PART_IMAGES . "logo.png'/>";
        }

        require_once DIR_CODES . 'my-list.php';
        $myList = new Codes_My_List();

        $info = array(
            'FullName'    => $row['full_name'],
            'Country'     => $myList->get_country($row['country']),
            'Position'    => $row['position'],
            'Email'       => $row['email'],
            'Phone'       => $row['phone'],
            'Note'        => $row['note'],
            'Img'         => $img,
            // SỬA LỖI Ở ĐÂY: Gán trực tiếp $checkInCount, tuyệt đối không dùng $checkInCount['count'] nữa
            'TotalTimes'  => $checkInCount, 
            'LastCheckIn' => $lastCheckInDate ? ($lastCheckInDate . ' / ' . $lastCheckInTime) : ''
        );

        $response = array('status' => 'done', 'message' => $row['ID'], 'info' => $info);

    } elseif (!empty($row) && $row['status'] == 0) {
        $response = array('status' => 'unactive', 'message' => 'Tài khoản chưa kích hoạt');
    } else {
        $response = array('status' => 'error', 'message' => 'Không tìm thấy mã vạch');
    }

    // Gửi JSON và tự động ngắt an toàn
    wp_send_json($response);
}