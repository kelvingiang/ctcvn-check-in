<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
// đọc file excel ==================================================
use PhpOffice\PhpSpreadsheet\IOFactory;

function export_excel_check_in($data)
{
    require_once __DIR__ . '/../vendor/autoload.php';

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('CHECK IN');  // 設定工作表名稱為 "我的工作表"
    $sheet->setCellValue('A1', '姓名');
    $sheet->setCellValue('B1', '分會');
    $sheet->setCellValue('C1', '職稱');
    $sheet->setCellValue('D1', '電話');
    $sheet->setCellValue('E1', 'E-mail');
    $sheet->setCellValue('F1', '時間');
    $sheet->setCellValue('G1', '日期');
    // // set do rong cua cot
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

    // 設定 A1 儲存格的背景顏色 (使用 RGB 色碼)
    $sheet->getStyle('A1:G1')->getFill()->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FF999999'); // 設定為黃色 (RGB:rgb(162, 199, 247))

    // 設定 A1 儲存格的框線 (黑色)
    $sheet->getStyle('A1:G1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)
        ->setColor(new Color("FF333333"));
    // set chu canh giua
    $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A1:G1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

    $i = 2;
    if (!empty($data)) {
        foreach ($data as $key => $val) {

            require_once DIR_CODES . 'my-list.php';
            $myList = new Codes_My_List();
            $country = $myList->get_country($val['country']);


            $sheet->setCellValue('A' . $i, $val['full_name']);
            $sheet->setCellValue('B' . $i, $country);
            $sheet->setCellValue('C' . $i, $val['position']);
            $sheet->setCellValue('D' . $i, $val['phone']);
            $sheet->setCellValue('E' . $i, $val['email']);
            $sheet->setCellValue('F' . $i, $val['time']);
            $sheet->setCellValue('G' . $i, $val['date']);

            //            $checkInAll ="";
            // if ($row[1]['Kind'] == "m") {
            //     //$objPHPExcel->setActiveSheetIndex(0)->getStyle( $cell )->getFont()->setSize( 10 );
            //     $exExport->setActiveSheetIndex(0)->getStyle("A$i:G$i")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00e9ebed');
            // }
            $i++;
        }
    }



    $filename = 'check_in_ctcvn_' . date('dmYHis') . '.xlsx';
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

    // 清空之前的所有輸出緩衝
    if (ob_get_length()) {
        ob_end_clean();
    }

    // 設定 HTTP 標頭
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;
}

function export_excel_guests($data)
{
    require_once __DIR__ . '/../vendor/autoload.php';

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('CHECK IN');  // 設定工作表名稱為 "我的工作表"
    $sheet->setCellValue('A1', '姓名');
    $sheet->setCellValue('B1', '分會');
    $sheet->setCellValue('C1', '職稱');
    $sheet->setCellValue('D1', '電話');
    $sheet->setCellValue('E1', 'E-mail');
    $sheet->setCellValue('F1', '條碼');
    $sheet->setCellValue('G1', '會員');
    // // set do rong cua cot
    $sheet->getColumnDimension('A')->setWidth(15);
    $sheet->getColumnDimension('B')->setWidth(20);
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(50);
    $sheet->getColumnDimension('F')->setAutoSize(true);
    $sheet->getColumnDimension('G')->setAutoSize(true);

    // set chieu cao cua dong
    $sheet->getRowDimension('1')->setRowHeight(30);
    // set to dam chu
    $sheet->getStyle('A')->getFont()->setBold(TRUE);
    $sheet->getStyle('A1:G1')->getFont()->setBold(TRUE);

    // 設定 A1 儲存格的背景顏色 (使用 RGB 色碼)
    $sheet->getStyle('A1:G1')->getFill()->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FF999999'); // 設定為黃色 (RGB:rgb(162, 199, 247))

    // 設定 A1 儲存格的框線 (黑色)
    $sheet->getStyle('A1:G1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)
        ->setColor(new Color("FF333333"));
    // set chu canh giua
    $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A1:G1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

    $i = 2;
    if (!empty($data)) {
        foreach ($data as $key => $val) {

            require_once DIR_CODES . 'my-list.php';
            $myList = new Codes_My_List();
            $country = $myList->get_country($val['country']);

            $sheet->setCellValue('A' . $i, $val['full_name']);
            $sheet->setCellValue('B' . $i, $country);
            $sheet->setCellValue('C' . $i, $val['position']);
            $sheet->setCellValue('D' . $i, $val['phone']);
            $sheet->setCellValue('E' . $i, $val['email']);
            $sheet->setCellValue('F' . $i, $val['barcode']);
            $sheet->setCellValue('G' . $i, $val['status']);
            $i++;
        }
    }



    $filename = 'guests_ctcvn_' . date('dmYHis') . '.xlsx';
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

    // 清空之前的所有輸出緩衝
    if (ob_get_length()) {
        ob_end_clean();
    }

    // 設定 HTTP 標頭
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;
}

function import_excel_guests($filePart)
{
    require_once __DIR__ . '/../vendor/autoload.php';
    $spreadsheet = IOFactory::load($filePart);
    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray();
    return $data;
}
