<?php


use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

function create_QRCode($code, $name)
{
    require_once __DIR__ . '/../vendor/QRCODE/autoload.php'; // 根據實際路徑調整

    // 1. 建立 QRCode
    $qrCode = new QrCode($code);
    $qrCode->setSize(200);
    $qrCode->setMargin(12);

    $writer = new PngWriter();
    $result = $writer->write($qrCode);

    // 2. 取得圖像資源
    $imageData = $result->getString();
    $image = imagecreatefromstring($imageData);

    // 3. 設定中文字型
    $fontPath = __DIR__ . '/../fonts/NotoSansTC-Regular.ttf'; // 確保這個檔案存在
    if (!file_exists($fontPath)) {
        die('字型檔不存在：' . $fontPath);
    }

    // 4. 設定文字參數
    $fontSize = 8; // 點數
    $text = $name;
    $textColor = imagecolorallocate($image, 0, 0, 0); // 黑色

    // 5. 計算文字尺寸（預估）
    $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
    $textWidth = abs($bbox[2] - $bbox[0]);
    $textHeight = abs($bbox[7] - $bbox[1]);

    $imgWidth = imagesx($image);
    $imgHeight = imagesy($image);

    // 右下角座標（預留 5px 邊界）
    $x = $imgWidth - $textWidth - 5;
    $y = $imgHeight - 5;

    // 6. 畫出中文字
    imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontPath, $text);

    // 7. 儲存圖片
    $outputPath = __DIR__ . '/../images/qrcode/' . $code . '.png';
    imagepng($image, $outputPath);

    // 8. 清除圖像記憶體
    imagedestroy($image);
}
