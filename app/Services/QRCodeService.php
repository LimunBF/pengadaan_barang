<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;
use Illuminate\Support\Facades\Storage;

class QRCodeService
{
    private $qrFolder = 'barang_qr_codes';

    /**
     * Generate QR Code dengan Text Wrapping (Multi-line)
     * @param string $data Isi Data QR (JSON)
     * @param string $filename Nama file fisik (HARUS AMAN: laptop-lenovo.png)
     * @param string $textLabel Tulisan pada gambar (Boleh panjang banget)
     */
    public function generateQRCode($data, $filename, $textLabel)
    {
        $qrStoragePath = storage_path("app/public/{$this->qrFolder}");
        if (!is_dir($qrStoragePath)) {
            mkdir($qrStoragePath, 0755, true);
        }

        if (!str_ends_with($filename, '.png')) {
            $filename .= '.png';
        }
        $finalImagePath = "{$qrStoragePath}/{$filename}";

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

        $writer = new PngWriter();
        $tempFile = "temp_" . uniqid() . ".png";
        $qrTempPath = "{$qrStoragePath}/{$tempFile}";
        
        $writer->write($qrCode)->saveToFile($qrTempPath);
        $this->addTextToImageWithWrap($qrTempPath, $finalImagePath, $textLabel);

        if (file_exists($qrTempPath)) {
            unlink($qrTempPath);
        }

        return "{$this->qrFolder}/{$filename}";
    }

    private function addTextToImageWithWrap($sourcePath, $destinationPath, $text)
    {
        if (!file_exists($sourcePath)) return;

        // Load gambar QR
        $image = imagecreatefrompng($sourcePath);
        $qrWidth = imagesx($image);
        $qrHeight = imagesy($image);

        // Setup Font
        $fontPath = storage_path('fonts/Rubik-Bold.ttf');
        $fontSize = 16; // Ukuran font
        $lineHeight = 25; // Jarak antar baris
        $padding = 20; // Padding atas/bawah teks

        // Logika Wrapping Text
        $lines = [];
        if (file_exists($fontPath)) {
            // Pecah kalimat jadi kata-kata
            $words = explode(' ', $text);
            $currentLine = '';

            foreach ($words as $word) {
                // Coba gabungkan kata ke baris saat ini
                $testLine = $currentLine . ($currentLine ? ' ' : '') . $word;
                
                // Cek lebar teks jika digabung
                $bbox = imagettfbbox($fontSize, 0, $fontPath, $testLine);
                $lineWidth = abs($bbox[2] - $bbox[0]);

                // Jika lebar melebihi lebar QR (dikurangi margin 20px), pindah baris
                if ($lineWidth > ($qrWidth - 20)) {
                    $lines[] = $currentLine;
                    $currentLine = $word;
                } else {
                    $currentLine = $testLine;
                }
            }
            // Masukkan sisa kata terakhir
            if (!empty($currentLine)) {
                $lines[] = $currentLine;
            }
        } else {
            // Fallback jika font tidak ada: Potong kasar per 30 karakter
            $lines = str_split($text, 30);
        }

        // Hitung tinggi tambahan yang dibutuhkan berdasarkan jumlah baris
        $textBlockHeight = (count($lines) * $lineHeight) + ($padding * 2);
        $newHeight = $qrHeight + $textBlockHeight;

        // Buat Kanvas Baru
        $newImage = imagecreatetruecolor($qrWidth, $newHeight);
        $white = imagecolorallocate($newImage, 255, 255, 255);
        imagefill($newImage, 0, 0, $white);

        // Copy QR Code ke posisi atas
        imagecopy($newImage, $image, 0, 0, 0, 0, $qrWidth, $qrHeight);

        $black = imagecolorallocate($newImage, 0, 0, 0);

        // Loop untuk menggambar setiap baris
        $yPosition = $qrHeight + $padding + 10; // Posisi awal Y (di bawah QR)

        foreach ($lines as $line) {
            if (file_exists($fontPath)) {
                // Hitung posisi X biar Center
                $bbox = imagettfbbox($fontSize, 0, $fontPath, $line);
                $lineWidth = abs($bbox[2] - $bbox[0]);
                $xPosition = ($qrWidth - $lineWidth) / 2;

                imagettftext($newImage, $fontSize, 0, $xPosition, $yPosition, $black, $fontPath, $line);
            } else {
                // Fallback font bawaan
                $fontWidth = imagefontwidth(5) * strlen($line);
                $xPosition = ($qrWidth - $fontWidth) / 2;
                imagestring($newImage, 5, $xPosition, $yPosition - 15, $line, $black);
            }
            
            // Geser ke bawah untuk baris berikutnya
            $yPosition += $lineHeight;
        }

        // Simpan Gambar
        imagepng($newImage, $destinationPath);
    }

    public function deleteQRCode($path)
    {
        $fullPath = storage_path("app/public/{$path}");
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}