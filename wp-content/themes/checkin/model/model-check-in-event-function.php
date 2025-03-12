<?php

class Model_Check_In_Event_Function
{
    private $tbl_guests;
    private $tbl_check_in;
    private $tbl_event;

    public function __construct()
    {
        global $wpdb;
        $this->tbl_guests = $wpdb->prefix . 'guests';
        $this->tbl_check_in = $wpdb->prefix . 'guests_check_in';
        $this->tbl_event = $wpdb->prefix . 'guests_check_in_event';
    }

    public function getAll()
    {
        global $wpdb;
        $sql = "SELECT * FROM $this->tbl_event";
        $row = $wpdb->get_results($sql, ARRAY_A);
        return $row;
    }

    public function getItem($id)
    {
        global $wpdb;
        $sql = "SELECT * FROM $this->tbl_event WHERE ID = $id";
        $row = $wpdb->get_row($sql, ARRAY_A);
        return $row;
    }

    public function getActiveItem()
    {
        global $wpdb;
        $sql = "SELECT * FROM $this->tbl_event WHERE status = 1";
        $row = $wpdb->get_row($sql, ARRAY_A);
        return $row;
    }

    public function saveItem($arrData = array(), $option = array())
    {
        global $wpdb;
        $data = array(
            'title' => $arrData['txt_title'],
        );

        if ($option == 'edit') {
            // thêm phần tử vào array 
            $data['update_date'] = date('Y-m-d');
            $where = array('ID' => absint($arrData['hidden_ID']));
            $wpdb->update($this->tbl_event,  $data, $where);
        } else if ($option == 'add') {
            $data['create_date'] = date('Y-m-d');
            $wpdb->insert($this->tbl_event, $data);
        }
    }

    public function activeItem($arrData = array(), $option = array())
    {
        global $wpdb;
        $sql = "UPDATE $this->tbl_event SET `status` =  '0' ";
        $wpdb->query($sql);

        $data = array('status' => 1);
        $where = array('id' => absint($arrData['id']));
        $wpdb->update($this->tbl_event, $data, $where);
    }
 
    public function resetItem($arrData = array(), $option = array()){
        global $wpdb;
        $id = $arrData['id'];

        $delDetail = "DELETE FROM $this->tbl_check_in WHERE event_id = $id";
        $wpdb->query($delDetail);
    }

    public function trashItem($arrData = array(), $option = array())
    {
        global $wpdb;

        $trash = $arrData['action'] == 'trash' ? '1' : '0';

        $data = array('trash' => $trash);
        $where = array('id' => absint($arrData['id']));
        $wpdb->update($this->tbl_check_in, $data, $where);
    }

    public function deleteItem($arrData = array(), $option = array())
    {

        global $wpdb;
        $id = $arrData['id'];

        $delDetail = "DELETE FROM $this->tbl_check_in WHERE event_id = $id";
        $wpdb->query($delDetail);

        $delEvent = "DELETE FROM $this->tbl_guests WHERE ID = $id";
        $wpdb->query($delEvent);
    }

    public function ExCheckInToExcelByID($id)
    {
        require_once DIR_CLASS . 'PHPExcel.php';
        $exExport = new PHPExcel();
        $exExport->setActiveSheetIndex(0);
        $sheet = $exExport->getActiveSheet()->setTitle("check in");
        $sheet->setCellValue('A1', '姓名');
        $sheet->setCellValue('B1', '分會');
        $sheet->setCellValue('C1', '職稱');
        $sheet->setCellValue('D1', '電話');
        $sheet->setCellValue('E1', 'E-mail');
        $sheet->setCellValue('F1', '時間');
        $sheet->setCellValue('G1', '日期');
        // set do rong cua cot
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        // set chieu cao cua dong
        $sheet->getRowDimension('1')->setRowHeight(30);
        // set to dam chu
        $sheet->getStyle('A')->getFont()->setBold(TRUE);
        $sheet->getStyle('A1:G1')->getFont()->setBold(TRUE);
        // set nen backgroup cho dong
        $sheet->getStyle('A1:G1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('0008bdf8');
        // set chu canh giua
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:G1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $row = $this->ReportJoinViewByID($id);
        $i = 2;

        if (!empty($row)) {
            foreach ($row as $key => $val) {
                // $sql2 = "SELECT * FROM $this->_table_detail  WHERE member_ID = " . $val['ID'] . " GROUP BY member_id";
                // $rowDetail = $wpdb->get_row($sql2, ARRAY_A);
                require_once DIR_CODES . 'my-list.php';
                $myList = new Codes_My_List();
                $country = $myList->get_country($val['country']);

                $exExport->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val['full_name'])
                    ->setCellValue('B' . $i, $country)
                    ->setCellValue('C' . $i, $val['position'])
                    ->setCellValue('D' . $i, $val['phone'])
                    ->setCellValue('E' . $i, $val['email'])
                    ->setCellValue('F' . $i, $val['time'])
                    ->setCellValue('G' . $i, $val['date']);

                //            $checkInAll ="";
                if ($row[1]['Kind'] == "m") {
                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle( $cell )->getFont()->setSize( 10 );
                    $exExport->setActiveSheetIndex(0)->getStyle("A$i:G$i")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00e9ebed');
                }
                $i++;
            }
        }
        // phan set border 
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        //cho tat ca 
        $sheet->getStyle('A1:' . 'G' . ($i - 1))->applyFromArray($styleArray);

        $filename = 'ctcvn_checkin_' . date("ymdHis") . '.xlsx';
        $objWriter = PHPExcel_IOFactory::createWriter($exExport, 'Excel2007');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');
        ob_end_clean();
        //        ob_start();
        $objWriter->save('php://output');
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
}
