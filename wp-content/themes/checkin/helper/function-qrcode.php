<?php


use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// $code là mã của QRCOde
// $name là họ tên thành viên
// $flag là nếu = 0 tên file không có tên thành viên, nếu = 1 tên file sẽ có tên thành viên
function create_QRCode($code, $name, $flag)
{
    require_once __DIR__ . '/../../../vendor/autoload.php';

    $qrCode = new QrCode($code);
    $qrCode->setSize(200);
    $qrCode->setMargin(2);

    $writer = new PngWriter();
    $result = $writer->write($qrCode);

    $imageData = $result->getString();
    $qrImage = imagecreatefromstring($imageData);

    $fontPath = __DIR__ . '/../fonts/NotoSansTC-Regular.ttf';
    if (!file_exists($fontPath)) {
        die('字型檔不存在：' . $fontPath);
    }

    $fontSize = 12;
    $text = $name;

    // 計算文字寬高
    $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
    $textWidth = abs($bbox[2] - $bbox[0]);
    $textHeight = abs($bbox[7] - $bbox[1]);

    // QR Code 圖片大小
    $qrWidth = imagesx($qrImage);
    $qrHeight = imagesy($qrImage);

    // 新圖片高度：原 QR 高度 + 額外 20px 空間
    $newHeight = $qrHeight + 15;
    $newImage = imagecreatetruecolor($qrWidth, $newHeight);

    // 填滿白底
    $white = imagecolorallocate($newImage, 255, 255, 255);
    imagefill($newImage, 0, 0, $white);

    // 把 QR Code 複製到新圖像上（靠上貼）
    imagecopy($newImage, $qrImage, 0, 0, 0, 0, $qrWidth, $qrHeight);

    // 設定文字顏色
    $black = imagecolorallocate($newImage, 0, 0, 0);
    // ✅ 文字靠右對齊，並離底部 2px
    // $textX = $qrWidth - $textWidth - 5; // 靠右，留 5px 邊界
    $textX = ($qrWidth - $textWidth) / 2; // 在中間
    $textY = $newHeight - 3;            // 離底 2px

    imagettftext($newImage, $fontSize, 0, $textX, $textY, $black, $fontPath, $text);

    // // 計算文字位置（水平置中，垂直在新增空間內）
    // $textX = ($qrWidth - $textWidth) / 2;
    // $textY = $qrHeight + ($textHeight + 5); // 放在下方空白內部中偏上

    // // 寫文字
    // imagettftext($newImage, $fontSize, 0, $textX, $textY, $black, $fontPath, $text);

    // 儲存圖片
    if ($flag == 0) {
        $outputPath = __DIR__ . '/../images/qrcode/' . $code . '.png';
        imagepng($newImage, $outputPath);
    } elseif ($flag == 1) {
        $outputPath = __DIR__ . '/../images/qrcode_name/' . $code . '-' . $name . '.png';
        imagepng($newImage, $outputPath);
    }

    // 清理
    imagedestroy($qrImage);
    imagedestroy($newImage);
}
