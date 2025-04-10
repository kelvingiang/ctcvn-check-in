<?php

require_once DIR_CODES . 'my-list.php';

class Admin_Model_Check_In_Setting
{

    private $attenList;
    private $myList;


    public function __construct()
    {
        $this->myList = new Codes_My_List();

        $this->AttenDetail();
    }

    private function CountryName($id)
    {
        return $this->myList->get_country($id);
    }


    //---------------------------------------------------------------------------------------------
    // them moi de kiem tra check trong ca hai table member va guests
    // lay barcode trong table check-in de lay data trong hai table
    //---------------------------------------------------------------------------------------------

    public function AttendTime()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'guests_check_in';
        $sql = "SELECT barcode, time, date  FROM $table GROUP BY barcode ";
        $row = $wpdb->get_results($sql, ARRAY_A);
        return $row;
    }

    public function AttenDetail()
    {
        global $wpdb;
        $table_guests = $wpdb->prefix . 'guests';
        $table_member = $wpdb->prefix . 'member';
        //$barcode = $this->AttendTime();
        $guestsList = array();
        $memberList = array();

        foreach ($this->AttendTime() as $val) {
            $sql = "SELECT full_name AS Name, country AS Country,  position AS Position, phone AS Phone, email AS Email, barcode AS Barcode  FROM $table_guests WHERE  barcode =" . $val['barcode'];
            $row = $wpdb->get_results($sql, ARRAY_A);
            array_push($row, array("Time" => $val['time'], "Date" => $val['date']));
            $guestsList[] = $row;

            //            if ($val['kind'] == 'm') {
            //                $sql2 = "SELECT full_name AS Name, country AS Country,  position AS Position, phone AS Phone, email AS Email, barcode AS Barcode  FROM $table_member WHERE  barcode =" . $val['barcode'];
            //                $row2 = $wpdb->get_results($sql2, ARRAY_A);
            //                array_push($row2, array("Time" => $val['time'], "Date" => $val['date'], "Kind" => $val['kind']));
            //                $memberList[] = $row2;
            //            }
        }



        // PHAN SAP XEP LAI THU TU THEO THOI GIAN CHECK IN
        uasort($guestsList, function ($a, $b) {
            return $b[1]['Time'] - $a[1]['Time'];
        });

        $this->attenList = array_merge($guestsList, $memberList);

        // return array_merge($guestsList,$memberList);
    }

    ////=================================================================  
    public function ReportView()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'guests';
        $sql = "SELECT * FROM $table WHERE check_in = 1 AND status = 1";
        $row = $wpdb->get_results($sql, ARRAY_A);
        return $row;
    }

    public function ReportjoinView()
    {
        global $wpdb;
        $table_guests = $wpdb->prefix . 'guests';
        $table_check = $wpdb->prefix . 'guests_check_in';
        $sql = "SELECT * FROM $table_guests AS A LEFT JOIN $table_check AS B ON A.ID = B.guests_id
                  WHERE A.status = 1 AND A.check_in =1
                  GROUP BY B.guests_id
                  ORDER BY B.time DESC";
        $row = $wpdb->get_results($sql, ARRAY_A);
        return $row;
    }

    //^^ add new at 14/03/2018
    public function ReportBranchView()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'guests';

        $sql = "SELECT country AS code, Count(country) AS register, 
            (SELECT  Count(country) FROM $table WHERE check_in = 1 AND status = 1 AND country = code) AS arrived
             FROM $table WHERE status = 1 GROUP BY country ORDER BY arrived DESC ";
        $row = $wpdb->get_results($sql, ARRAY_A);

        $newBranchitem = array();
        $newBranch = array();
        foreach ($row as $val) {
            $newBranchitem['code'] = $val['code'];
            $newBranchitem['register'] = $val['register'];
            $newBranchitem['arrived'] = $val['arrived'];
            $newBranchitem['percent'] = round($val['arrived'] / $val['register'] * 100, 2);
            $newBranch[] = $newBranchitem;
        }
        return $newBranch;
    }

    public function BarcodeInfo()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'guests';
        $sql = "SELECT * FROM $table WHERE  status = 1";
        $row = $wpdb->get_results($sql, ARRAY_A);
        return $row;
    }

    public function ReportDetailView($barcode)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'guests_check_in';
        $sql = "SELECT * FROM $table WHERE barcode = $barcode";
        $row = $wpdb->get_results($sql, ARRAY_A);
        return $row;
    }

    /// =============================================

    public function ExportGuests()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'guests';
        $sql = "SELECT * FROM $table WHERE 1=1 ORDER BY `country` ASC, `status` DESC";
        $row = $wpdb->get_results($sql, ARRAY_A);
        export_excel_guests($row);
    }

    public function ExportBarcode()
    {
        require_once DIR_CLASS . 'PHPExcel.php';
        $exExport = new PHPExcel();

        // TAO COT TITLE
        $exExport->setActiveSheetIndex(0)
            ->setCellValue('A1', '姓名')
            ->setCellValue('B1', '職稱')
            ->setCellValue('C1', '分會')
            ->setCellValue('D1', '條碼');

        // TAO NOI DUNG CHEN TU DONG 2
        $i = 2;
        $list = $this->BarcodeInfo();

        foreach ($list as $row) {
            $exExport->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $row['full_name'])
                ->setCellValue('B' . $i, $row['position'])
                ->setCellValue('C' . $i, $this->CountryName($row['country']))
                ->setCellValueExplicit('D' . $i, $row['barcode'], PHPExcel_Cell_DataType::TYPE_STRING);
            $i++;
        }
        // TAO FILE EXCEL VA SAVE LAI THEO PATH
        //$objWriter = PHPExcel_IOFactory::createWriter($exExport, 'Excel2007');
        //$full_path = EXPORT_DIR . date("YmdHis") . '_report.xlsx'; //duong dan file
        //$objWriter->save($full_path);
        //
        // TAO FILE EXCEL VA DOWN TRUC TIEP XUONG CLINET
        $filename = date("YmdHis") . '_barcode.xlsx';
        $objWriter = PHPExcel_IOFactory::createWriter($exExport, 'Excel2007');
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');
        ob_end_clean();
        //        ob_start();
        $objWriter->save('php://output');
    }

    //===================================================================================

    public function create_QRCode()
    {

        // create_QRCode('dddd');
        global $wpdb;
        $table = $wpdb->prefix . 'guests';
        $sql = "SELECT full_name, barcode FROM $table";
        $row = $wpdb->get_results($sql, ARRAY_A);

        // // XOA HET CAC FILE QRCODE .png CO TRONG FOLDER
        $files = glob(DIR_IMAGES_QRCODE . '*.png'); //get all file names
        foreach ($files as $file) {
            if (is_file($file))
                unlink($file); //delete file
        }

        // TAO TAT CA CAC FILE QRCODE MOI
        // require_once(DIR_CLASS . 'qrcode' . DS . 'qrlib.php');
        foreach ($row as $item) {
            create_QRCode($item['barcode'], $item['full_name']);
        }
    }


    //=================================================================================
    public function ImportGuests($filename)
    {
        $arrData = import_excel_guests($filename);
        global $wpdb;
        $table = $wpdb->prefix . 'guests';
        $wpdb->query("TRUNCATE TABLE $table");

        foreach ($arrData as $item) {
            $note = $item[10] == null ? "" : $item[10];
            $img = $item[6] == null ? "" : $item[6];
            $phone = $item[4] == null ? "" : $item[4];
            $email = $item[3] == null ? "" : $item[3];
            $data = array(
                'full_name' => $item[0],
                'country' => $item[1],
                'position' => $item[2],
                'email' => $email,
                'phone' => $phone,
                'barcode' => $this->setQRCode($item[1]),
                'img' => $img,
                // 'check_in' => $item[8],
                'create_date' => date('d-m-Y'),
                'status' => 1,
                'note' => $note,
            );
            $wpdb->insert($table, $data);
        }
    }


    public function ImportGuestsAdditional($filename)
    {
        $arrData = import_excel_guests($filename);
        global $wpdb;
        $table = $wpdb->prefix . 'guests';

        foreach ($arrData as $item) {
            $note = $item[10] == null ? "" : $item[10];
            $img = $item[6] == null ? "" : $item[6];
            $phone = $item[4] == null ? "" : $item[4];
            $email = $item[3] == null ? "" : $item[3];
            $data = array(
                'full_name' => $item[0],
                'country' => $item[1],
                'position' => $item[2],
                'email' => $email,
                'phone' => $phone,
                'barcode' => $this->setQRCode($item[1]),
                'img' => $img,
                // 'check_in' => $item[8],
                'create_date' => date('d-m-Y'),
                'status' => 1,
                'note' => $note,
            );
            $wpdb->insert($table, $data);
        }
    }

    public function setQRCode($code)
    {
        $length = 8;
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $code . $randomString;
    }
}
