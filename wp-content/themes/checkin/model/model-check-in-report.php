<?php

class Admin_Model_Check_In_Report
{
    private $tbl_guests;
    private $tbl_check_in;
    private $tbl_event;
    private $current_event;

    public function __construct()
    {
        global $wpdb;
        $this->tbl_guests = $wpdb->prefix . 'guests';
        $this->tbl_check_in = $wpdb->prefix . 'guests_check_in';
        $this->tbl_event = $wpdb->prefix . 'guests_check_in_event';

        // LAY EVENT HIEN TAI =============
        $sql = "SELECT ID FROM $this->tbl_event WHERE status = 1";
        $row = $wpdb->get_results($sql, ARRAY_A);
        $this->current_event = $row[0]['ID'];
    }


    public function ReportjoinView()
    {
        global $wpdb;
        $sql = "SELECT A.guests_ID, MIN(A.date) as date, MIN(A.time) as time, B.full_name, B.country, B.position, B.phone, B.email 
        FROM $this->tbl_check_in AS A 
        LEFT JOIN $this->tbl_guests AS B ON  A.guests_ID = B.ID 
        WHERE event_id = $this->current_event
        GROUP BY A.guests_ID, B.full_name, B.country, B.position, B.phone, B.email
        ORDER BY time DESC";
        $row = $wpdb->get_results($sql, ARRAY_A);
        return $row;
    }

    public function RegisterTotal()
    {
        global $wpdb;
        $sql = "SELECT COUNT(ID) FROM $this->tbl_guests WHERE status = 1";
        $row = $wpdb->get_row($sql, ARRAY_A);
        return $row;
    }

    //^^ add new at 14/03/2018
    public function ReportBranchView()
    {
        global $wpdb;

        $sql = "SELECT 
            g.country AS code, 
            (SELECT COUNT(*) 
             FROM $this->tbl_guests 
             WHERE status = 1 AND country = g.country) AS register, 

            (SELECT COUNT(DISTINCT guests_ID) 
             FROM $this->tbl_check_in 
             WHERE event_ID = $this->current_event 
             AND guests_ID IN (SELECT ID FROM $this->tbl_guests WHERE status = 1 AND country = g.country)) AS arrived 

        FROM $this->tbl_guests AS g
        WHERE g.status = 1 
        GROUP BY g.country 
        ORDER BY arrived DESC";

        $row = $wpdb->get_results($sql, ARRAY_A);

        $newBranchitem = array();
        $newBranch = array();
        require_once DIR_CODES . 'my-list.php';
        $myList = new Codes_My_List();
        foreach ($row as $val) {
            $newBranchitem['code'] = $myList->get_country($val['code']);
            $newBranchitem['register'] = $val['register'];
            $newBranchitem['arrived'] = $val['arrived'];
            $newBranchitem['percent'] = round($val['arrived'] / $val['register'] * 100, 2);
            $newBranch[] = $newBranchitem;
        }
        return $newBranch;
    }

    public function ReportJoinViewByID($id)
    {
        global $wpdb;

        $sql = "SELECT * FROM $this->tbl_check_in AS a 
         LEFT JOIN $this->tbl_guests AS b on a.guests_ID = b.ID
         WHERE a.event_ID = $id 
         GROUP BY a.guests_ID";
        $row = $wpdb->get_results($sql, ARRAY_A);
        return $row;
    }

    public function getActionEventById($id) {}
    /////==================================================================================================


    public function ReportView()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'guests';
        $sql = "SELECT * FROM $table WHERE check_in = 1 AND status = 1";
        $row = $wpdb->get_results($sql, ARRAY_A);
        return $row;
    }

    public function ReportjoinViewMember()
    {
        global $wpdb;
        $table_member = $wpdb->prefix . 'member';
        $table_check = $wpdb->prefix . 'guests_check_in';
        $sql = "SELECT * FROM $table_member AS A LEFT JOIN $table_check AS B ON A.barcode = B.barcode
                  WHERE A.status = 1 AND A.check_in =1
                  GROUP BY B.guests_id
                  ORDER BY B.time DESC";
        $row = $wpdb->get_results($sql, ARRAY_A);
        return $row;
    }

    public function BarcodeInfo()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'guests';
        $sql = "SELECT * FROM $table WHERE  status = 1";
        $row = $wpdb->get_results($sql, ARRAY_A);
        return $row;
    }

    public function ReportDetailView($guests_id)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'guests_check_in';
        $sql = "SELECT * FROM $table WHERE guests_id = $guests_id";
        $row = $wpdb->get_results($sql, ARRAY_A);
        return $row;
    }

    public function AttendTime()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'guests_check_in';
        $sql = "SELECT barcode, time, date  FROM $table GROUP BY guests_id ";
        $row = $wpdb->get_results($sql, ARRAY_A);

        return $row;
    }

    public function AttenDetail()
    {
        global $wpdb;
        $table_guests = $wpdb->prefix . 'guests';
        $guestsList = array();

        foreach ($this->AttendTime() as $val) {
            $sql = "SELECT full_name AS Name, country AS Country,  position AS Position, phone AS Phone, email AS Email, barcode AS Barcode  FROM $table_guests WHERE  barcode =" . $val['barcode'];
            $row = $wpdb->get_results($sql, ARRAY_A);

            array_push($row, array("Time" => $val['time'], "Date" => $val['date']));
            $guestsList[] = $row;
        }

        // PHAN SAP XEP LAI THU TU THEO THOI GIAN CHECK IN
        uasort($guestsList, function ($a, $b) {
            return $b[1]['Time'] - $a[1]['Time'];
        });

        return $guestsList;
    }


        // public function ExportBarcode()
    // {
    //     require_once DIR_CLASS . 'PHPExcel.php';
    //     $exExport = new PHPExcel();

    //     // TAO COT TITLE
    //     $exExport->setActiveSheetIndex(0)
    //         ->setCellValue('A1', '姓名')
    //         ->setCellValue('B1', '條碼')
    //         ->setCellValue('C1', '職稱')
    //         ->setCellValue('D1', '國家')
    //         ->setCellValue('E1', '國碼');

    //     // TAO NOI DUNG CHEN TU DONG 2
    //     $i = 2;
    //     $list = $this->BarcodeInfo();

    //     foreach ($list as $row) {
    //         $checkInDetail = $this->ReportDetailView($row['ID']);
    //         foreach ($checkInDetail as $item) {
    //             $checkInItem .= $item['time'] . '__' . $item['date'] . '  ';
    //         }

    //         $exExport->setActiveSheetIndex(0)
    //             ->setCellValue('A' . $i, $row['full_name'])
    //             ->setCellValueExplicit('B' . $i, $row['barcode'], PHPExcel_Cell_DataType::TYPE_STRING)
    //             ->setCellValue('C' . $i, $row['position'])
    //             ->setCellValue('D' . $i, get_country($row['country']))
    //             ->setCellValueExplicit('E' . $i, $row['country'], PHPExcel_Cell_DataType::TYPE_STRING);
    //         $i++;
    //         $checkInItem = '';
    //     }

    //     // TAO FILE EXCEL VA SAVE LAI THEO PATH
    //     //$objWriter = PHPExcel_IOFactory::createWriter($exExport, 'Excel2007');
    //     //$full_path = EXPORT_DIR . date("YmdHis") . '_report.xlsx'; //duong dan file
    //     //$objWriter->save($full_path);
    //     // TAO FILE EXCEL VA DOWN TRUC TIEP XUONG CLINET
    //     $filename = date("YmdHis") . '_barcode.xlsx';
    //     $objWriter = PHPExcel_IOFactory::createWriter($exExport, 'Excel2007');
    //     header('Content-Type: application/vnd.ms-excel');
    //     header("Content-Disposition: attachment;filename=$filename");
    //     header('Cache-Control: max-age=0');
    //     ob_end_clean();
    //     $objWriter->save('php://output');
    // }

}
