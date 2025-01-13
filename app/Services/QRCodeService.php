<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;

class QRCodeService
{
    private $qrFolder = 'barang_qr_codes';

    public function generateQRCode($data, $id_barang, $nama_barang)
    {
        $qrStoragePath = storage_path("app/public/{$this->qrFolder}");
        $qrFileName = "{$id_barang}.png";
        $finalImagePath = "{$qrStoragePath}/{$qrFileName}";

        // Buat folder jika belum ada
        if (!is_dir($qrStoragePath)) {
            mkdir($qrStoragePath, 0755, true);
        }

        // Generate QR Code
        $qrCode = new QrCode(
            data: $data,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        // Simpan QR Code sementara
        $writer = new PngWriter();
        $qrTempPath = "{$qrStoragePath}/temp_{$id_barang}.png";
        $writer->write($qrCode)->saveToFile($qrTempPath);

        // Tambahkan teks ke gambar QR Code
        $this->addTextToImage($qrTempPath, $finalImagePath, $nama_barang);

        // Hapus QR Code sementara
        unlink($qrTempPath);

        return asset("storage/{$this->qrFolder}/{$qrFileName}");
    }

    private function addTextToImage($sourcePath, $destinationPath, $text)
    {
        $image = imagecreatefrompng($sourcePath);

        $width = imagesx($image);
        $height = imagesy($image);
        $newHeight = $height + 70;

        $newImage = imagecreatetruecolor($width, $newHeight);
        $white = imagecolorallocate($newImage, 255, 255, 255);
        imagefill($newImage, 0, 0, $white);

        imagecopy($newImage, $image, 0, 0, 0, 0, $width, $height);

        $black = imagecolorallocate($newImage, 0, 0, 0);
        $fontPath = storage_path('fonts/Rubik-Bold.ttf');
        $fontSize = 30;

        $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
        $textWidth = abs($bbox[2] - $bbox[0]);
        $xPosition = ($width - $textWidth) / 2;
        $yPosition = $height + 40;

        imagettftext($newImage, $fontSize, 0, $xPosition, $yPosition, $black, $fontPath, $text);

        imagepng($newImage, $destinationPath);

        imagedestroy($image);
        imagedestroy($newImage);
    }

    public function deleteQRCode($id_barang)
    {
        $filePath = storage_path("app/public/{$this->qrFolder}/{$id_barang}.png");

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
