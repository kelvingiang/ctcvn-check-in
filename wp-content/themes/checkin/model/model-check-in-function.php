<?php
class Model_Check_In_Function
{
    private $_table;
    private $_table_detail;
    private $_table_event;
    public function __construct()
    {
        global $wpdb;
        $this->_table = $wpdb->prefix . 'guests';
        $this->_table_detail = $wpdb->prefix . 'guests_check_in';
        $this->_table_event = $wpdb->prefix . 'guests_check_in_event';
    }

    public function get_item($arrData = array(), $option = array())
    {
        global $wpdb;
        $id = absint($arrData['id']);
        $sql = "SELECT * FROM $this->_table WHERE ID = $id";
        $row = $wpdb->get_row($sql, ARRAY_A);
        return $row;
    }

    public function trashItem($arrData = array(), $option = array())
    {
        global $wpdb;
        // KIEM TRA PHAN  CÓ PHAN DANG CHUOI HAY KHONG
        if (!is_array($arrData['id'])) {
            $data = array('status' => 0);
            $where = array('id' => absint($arrData['id']));
            $wpdb->update($this->_table, $data, $where);
        } else {
            // $arrData['id] chuyen qua ID-barcode  vidu : 1111-22222222
            // do su dung array_map('absint) no chi lay so cho nen khi lay de dau '-' khong tiep tuc lay
            // vay la no chi lay phan dau la ID dung voi gi muon
            // khong can tach chuoi
            $arrData['id'] = array_map('absint', $arrData['id']);
            $ids = join(',', $arrData['id']);
            $sql = "UPDATE $this->_table SET `status` =  '0'   WHERE ID IN ($ids)";
            $wpdb->query($sql);
        }
    }

    public function restoreItem($arrData = array(), $option = array())
    {
        global $wpdb;
        // KIEM TRA PHAN DELETE CÓ PHAN DANG CHUOI HAY KHONG
        if (!is_array($arrData['id'])) {
            $data = array('status' => 1);
            $where = array('id' => absint($arrData['id']));
            $wpdb->update($this->_table, $data, $where);
        } else {
            // $arrData['id] chuyen qua ID-barcode  vidu : 1111-22222222
            // do su dung array_map('absint) no chi lay so cho nen khi lay de dau '-' khong tiep tuc lay
            // vay la no chi lay phan dau la ID dung voi gi muon
            // khong can tach chuoi
            $arrData['id'] = array_map('absint', $arrData['id']);
            $ids = join(',', $arrData['id']);
            $sql = "UPDATE $this->_table SET `status` =  '1'   WHERE ID IN ($ids)";
            $wpdb->query($sql);
        }
    }

    //---------------------------------------------------------------------------------------------
    // chuyen ID co 2 phan id-barcode, khi nhan value se tach 2 phan id va barcode de su dung 
    // phan chinh sua data trong table guests_check_in phai sua dung barcode ko the dung guests_id 
    // vi guests_id duoc luu vao tu 2 table member va guests cho nen co kha nang trung ID 
    // vi vay dung barcode lam chuan khi thao tac voi table guests_check_in
    //---------------------------------------------------------------------------------------------
    public function uncheckin($arrData = array(), $option = array())
    {
        global $wpdb;
        // die('79999889');

        if (!is_array($arrData['id'])) {
            $data = array('check_in' => 0);

            $where2 = array(
                'guests_id' => $arrData['id'],
                'event_id'  => $arrData['event_id'] // 修正拼寫錯誤
            );
            $wpdb->delete($this->_table_detail, $where2);
        }
    }

    public function checkin($arrData = array(), $option = array())
    {
        global $wpdb;
        $data = array(
            'guests_id' => $arrData['id'],
            'event_id' => $arrData['event_id'],
            'time' => date('H:i:s'),
            'date' => date('m-d-Y'),
        );
        $wpdb->insert($this->_table_detail, $data);
    }

    public function deleteItem($arrData = array(), $option = array())
    {
        global $wpdb;
        $this->deleteImg($arrData['id']);
        if (!is_array($arrData['id'])) {
            $where = array('ID' => absint($arrData['id']));
            $wpdb->delete($this->_table, $where);
        } else {
            // $arrData['id] chuyen qua ID-barcode  vidu : 1111-22222222
            // do su dung array_map('absint) no chi lay so cho nen khi lay de dau '-' khong tiep tuc lay
            // vay la no chi lay phan dau la ID dung voi gi muon
            // khong can tach chuoi
            $arrData['id'] = array_map('absint', $arrData['id']);
            $ids = join(',', $arrData['id']);
            $sql = "DELETE FROM $this->_table WHERE ID IN ($ids)";
            $wpdb->query($sql);
        }
    }

    private function deleteImg($arrID)
    {
        global $wpdb;

        if (!is_array($arrID)) {
            $sql = "SELECT * FROM $this->_table WHERE ID =" . $arrID;
            $row = $wpdb->get_row($sql, ARRAY_A);
            //            XOA HINH TRONG FOLDER
            unlink(DIR_IMAGES . 'guests' . DS . $row['img']);
            unlink(DIR_IMAGES . 'qrcode' . DS . $row['barcode'] . '.png');
        } else {
            foreach ($arrID as $key) {
                $sql = "SELECT * FROM $this->_table WHERE ID =" . $key;
                $row = $wpdb->get_row($sql, ARRAY_A);
                // XOA HINH CUA GUESTS
                unlink(DIR_IMAGES . 'guests' . DS . $row['img']);
                unlink(DIR_IMAGES . 'qrcode' . DS . $row['barcode'] . '.png');
            }
        }
    }

    public function saveItem($arrData = array(), $option = array())
    {
        global $wpdb;
        if (isset($arrData['hidden_barcode']) and empty($arrData['hidden_barcode'])) {
            // $barcode = $this->createQRcode($arrData['sel_country'], $arrData['txt_fullname']);
            $t = time();
            $cc = substr($t, -8);
            $barcode =  $arrData['sel_country'] . $cc;
            create_QRCode($barcode, $arrData['txt_fullname']);
            // $barcode = $this->createQRcode($arrData['sel_country'], $arrData['txt_fullname']);
        } else {
            if (isset($arrData['sel_country']) && $arrData['sel_country'] != $arrData['hidden_country']) {
                // $barcode = $this->createQRcode($arrData['sel_country'], $arrData['txt_fullname']);
                // delete the old barcode picture    
                //$oldBarcode =    iconv('UTF-8','BIG5', DIR_IMAGES_QRCODE .$arrData['hidden_fullname'].'-'.$arrData['hidden_barcode'] . '.png');
                // $oldBarcode = $arrData['hidden_barcode'] . '.png';

                // if (is_file(DIR_IMAGES_QRCODE . $oldBarcode)) {
                //  unlink(DIR_IMAGES_QRCODE . $oldBarcode);
                //}
            } else {
                // $barcode = $arrData['hidden_barcode'];
                $barcode = $arrData['hidden_barcode'] ?? null;
            }
        }

        if (!empty($_FILES['guests_img']['name'])) {
            $errors = array();
            $file_name = $_FILES['guests_img']['name'];
            $file_size = $_FILES['guests_img']['size'];
            $file_tmp = $_FILES['guests_img']['tmp_name'];
            $file_type = $_FILES['guests_img']['type'];

            $file_trim = ((explode('.', $_FILES['guests_img']['name'])));
            $trim_name = strtolower($file_trim[0]);
            $trim_type = strtolower($file_trim[1]);
            // $name = 'hinh';
            if (!empty($arrData['hidden_barcode'])) {
                $cus_name = $arrData['hidden_barcode'] . '.' . $trim_type;  //tao name moi cho file tranh trung va mat file
            } else {
                $cus_name = $barcode . '.' . $trim_type;
            }
            $extensions = array("jpeg", "jpg", "png", "bmp");
            if (in_array($trim_type, $extensions) === false) {
                $errors[] = "上傳照片檔案是 JPEG, PNG, BMP.";
            }
            if ($file_size > 2097152) {
                $errors[] = '上傳檔案容量不可大於 2 MB';
            }
            //   $path = DIR_IMAGES . 'guests' . DS .; // get function path upload img dc khai bao tai file hepler

            if (empty($errors) == true) {
                //=== upload hinh ==============================
                // delete the old barcode picture 
                if (is_file(DIR_IMAGES . 'guests' . DS . $arrData['hidden_img'])) {
                    unlink(DIR_IMAGES . 'guests' . DS . $arrData['hidden_img']);
                }
                move_uploaded_file($file_tmp, (DIR_IMAGES . 'guests' . DS . $cus_name));
            } else {
                return $errors;
            }
        } else {
            $cus_name = $arrData['hidden_img'] ?? null;
            // $barcode = $arrData['hidden_barcode'] ?? null;
        }

        $appCode = isset($arrData['txt_appcode']) ? $arrData['txt_appcode'] : $arrData['hidden_appcode'] ?? null;



        $data1 = array(
            'full_name' => $arrData['txt_fullname'] ?? null,
            // 'barcode' => $barcode,
            //'app_code' => $appCode,
            'country' => $arrData['sel_country'] ?? null,
            'position' => $arrData['txt_position'] ?? null,
            'email' => $arrData['txt_email'] ?? null,
            'phone' => $arrData['txt_phone'] ?? null,
            'img' => $cus_name,
            'note' => $arrData['txt_note'] ?? null,
        );

        $data2 = array(
            'barcode' => $barcode,
            'app_code' => $appCode,
            'full_name' => $arrData['txt_fullname'] ?? null,
            'country' => $arrData['sel_country'] ?? null,
            'position' => $arrData['txt_position'] ?? null,
            'email' => $arrData['txt_email'] ?? null,
            'phone' => $arrData['txt_phone'] ?? null,
            'check_in' => '0',
            'img' => $cus_name,
            'note' => $arrData['txt_note'] ?? null,
            'create_date' => date('d-m-Y'),
            'status' => '1'
        );


        if (!empty($arrData['hidden_ID'])) {
            $where = array('ID' => absint($arrData['hidden_ID']));
            $wpdb->update($this->_table, $data1, $where);
        } else {
            $wpdb->insert($this->_table, $data2);
        }
    }


    //TAO QRCODE
    public function createQRcode($code, $name)
    {


        // $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
        require_once(DIR_CLASS . 'qrcode' . DS . 'qrlib.php');

        $t = time();
        $cc = substr($t, -8);
        $filename = $code . $cc;

        $filePath = DIR_IMAGES . 'qrcode' . DS . $filename . '.png';
        // L M Q H
        $errorCorrectionLevel = "L";
        // size 1 - 10
        $matrixPointSize = 3;
        QRcode::png($filename, $filePath, $errorCorrectionLevel, $matrixPointSize, 2);


        //********************************************************* */
        //=== tạo thêm chữ trên file QRCode /   28/02/2025 
        // them font NotoSansTC-Regular.ttf vào mục font tạo thêm define DIR_FONTS
        //start  *********************************************************/
        // 讀取 QR Code 圖片
        $qrImage = imagecreatefrompng($filePath);
        $qrWidth = imagesx($qrImage);
        $qrHeight = imagesy($qrImage);

        // **設定中文字型**
        $fontPath = DIR_FONTS . 'NotoSansTC-Regular.ttf'; // 確保字型路徑正確
        $fontSize = 9; // 字體大小
        $textPadding = 5; // 文字與 QR Code 之間的距離

        // **計算文字寬度**
        $box = imagettfbbox($fontSize, 0, $fontPath, $name);
        $textWidth = abs($box[2] - $box[0]);
        $textHeight = abs($box[7] - $box[1]);

        // **建立新圖片（比 QR Code 高一點來放文字）**
        $finalImage = imagecreatetruecolor($qrWidth, $qrHeight + $textHeight + $textPadding);
        $white = imagecolorallocate($finalImage, 255, 255, 255);
        $black = imagecolorallocate($finalImage, 0, 0, 0);

        // **填充背景為白色**
        imagefilledrectangle($finalImage, 0, 0, $qrWidth, $qrHeight + $textHeight + $textPadding, $white);
        imagecopy($finalImage, $qrImage, 0, 0, 0, 0, $qrWidth, $qrHeight);

        // **在 QR Code 下方添加中文文字**
        $textX = ($qrWidth - $textWidth) / 2;
        $textY = $qrHeight + $textHeight; // 文字放在 QR Code 下方
        imagettftext($finalImage, $fontSize, 0, $textX, $textY, $black, $fontPath, $name);

        // **儲存最終圖片**
        imagepng($finalImage, $filePath);

        // **釋放記憶體**
        imagedestroy($qrImage);
        imagedestroy($finalImage);

        return $filename;
    }
}
