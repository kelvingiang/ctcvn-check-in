<?php

define('WP_USE_THEMES', false);
require('../../../../wp-load.php');
date_default_timezone_set('Asia/Ho_Chi_Minh');

$a_barcode = $_POST['id'];

if (!empty($a_barcode)) {
    global $wpdb;
    $tbl_guests    = $wpdb->prefix . 'guests';
    $tbl_check_in  = $wpdb->prefix . 'guests_check_in';
    $tbl_event     = $wpdb->prefix . 'guests_check_in_event';


    $sqlGuests    = "SELECT * FROM $tbl_guests WHERE barcode = '$a_barcode' OR app_code = '$a_barcode'";
    $row   = $wpdb->get_row($sqlGuests, ARRAY_A);

    if (!empty($row) && $row['status'] == 1) {

        // add 11/10/2017 KIEM TRA SO LAN CHECK IN =======================================================================================  
        $sql2 = "SELECT time, date,  (SELECT COUNT(*) FROM $tbl_check_in WHERE guests_id =" . $row['ID'] . ") as count FROM $tbl_check_in WHERE guests_id =" . $row['ID'] . " ORDER BY time DESC LIMIT 1";
        $row2 = $wpdb->get_row($sql2, ARRAY_A);
        // end ================================================================================================  

        $sql3 = "SELECT ID FROM $tbl_event WHERE status = 1";
        $eventRow = $wpdb->get_row($sql3, ARRAY_A);

        // UPDATE TABLE guests CHECK_IN 
        $data = array('check_in' => 1);
        $where = array('ID' => $row['ID']);
        $wpdb->update($table, $data, $where);



        $sql3 = "SELECT COUNT(*) As count FROM $tbl_check_in WHERE guests_id =" . $row['ID'] . " and event_id = " . $eventRow['ID'];
        $checkInCount = $wpdb->get_row($sql3, ARRAY_A);

        // 取得最後一筆的 dat 和 time
        $sqlLast = "SELECT date, time FROM $tbl_check_in WHERE guests_id = " . $row['ID'] . " AND event_id = " . $eventRow['ID'] . " ORDER BY date DESC, time DESC LIMIT 1";
        $checkInLast = $wpdb->get_row($sqlLast, ARRAY_A);


        // ADD ROW CHECK IN INFO VAO TABLE  guests_check_in
        $data2 = array(
            'guests_id' => $row['ID'],
            'event_id' => $eventRow['ID'],
            'date' => date('m-d-Y'),
            'time' => date('H:i:s'),
            // 'kind' => $row['kind'],
        );
        $wpdb->insert($tbl_check_in, $data2);

        // add 11/10/2017 GIA TRI TRA VE =======================================================================================                         
        // LAY HINH ANH DAI DIEN
        if (!empty($row['img'])) {
            $img = "<img id='guest-pic'  name='guest-pic' src= '" . PART_IMAGES_GUESTS . $row['img'] . "'/>";
        } else {
            $img = "<img id= 'guest-pic' style='width:500px; opacity:0.2' name = 'guest-pic' src ='" . PART_IMAGES . 'logo.png' . "'/>";
        }
        require_once DIR_CODES . 'my-list.php';
        $myList = new Codes_My_List();

        $info = array(
            'FullName' => $row['full_name'],
            'Country' => $myList->get_country($row['country']),
            'Position' => $row['position'],
            'Email' => $row['email'],
            'Phone' => $row['phone'],
            'Note' => $row['note'],
            'Img' => $img,
            'TotalTimes' => $checkInCount['count'],
            'LastCheckIn' => $checkInLast['date'] . ' / ' . $checkInLast['time']
        );
        $response = array('status' => 'done', 'message' => $row['ID'], 'info' => $info);
        // end ================================================================================================     
    } elseif (!empty($row) && $row['status'] == 0) {
        $response = array('status' => 'unactive', 'message' => 'chua kich hoat tai khoan');
    } else {
        $response = array('status' => 'error', 'message' => '0000');
    }

    echo json_encode($response);
}
