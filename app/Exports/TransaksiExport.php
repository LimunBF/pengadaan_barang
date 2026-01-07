<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class TransaksiExport implements WithMultipleSheets
{
    protected $allData;
    protected $filteredData;

    public function __construct($allData, $filteredData)
    {
        $this->allData = $allData;
        $this->filteredData = $filteredData;
    }

    public function sheets(): array
    {
        return [
            // Sheet 1: Semua data transaksi
            new TransaksiSheet($this->allData, 'Semua Data'),

            // Sheet 2: Data terfilter (atau pesan "Filter Tidak Digunakan")
            new TransaksiSheet(
                $this->filteredData->isNotEmpty() ? $this->filteredData : collect([['Filter Tidak Digunakan']]),
                $this->filteredData->isNotEmpty() ? 'Data Terfilter' : 'Filter Tidak Digunakan'
            ),
        ];
    }
}

class TransaksiSheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $data;
    protected $title;

    public function __construct($data, $title)
    {
        $this->data = $data;
        $this->title = $title;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        // Hanya tampilkan heading jika data bukan pesan "Filter Tidak Digunakan"
        if ($this->data->first() && $this->data->first()['photo'] === 'Filter Tidak Digunakan') {
            return ['Pesan'];
        }

        return [
            'ID Transaksi',
            'ID Barang',
            'Nama Barang',
            'Jenis Transaksi',
            'Kuantitas',
            'Nama Pengirim/Penerima',
            'Waktu',
            'Catatan',
            'Foto Bukti',
        ];
    }

    public function map($transaksi): array
    {
        if (is_array($transaksi)) {
            // Jika ini cuma pesan teks (contoh: ["Filter Tidak Digunakan"])
            if (isset($transaksi[0])) {
                 return $transaksi;
            }
            
            // Jika ini data dari JS (Associative Array), kita mapping manual
            // agar urutannya sesuai dengan headings()
            return [
                $transaksi['id_transaksi'] ?? '',
                $transaksi['id_barang'] ?? '',
                $transaksi['nama_barang'] ?? '',
                $transaksi['tipe_transaksi'] ?? '',
                $transaksi['kuantitas'] ?? '',
                $transaksi['nama_pengirim_penerima'] ?? '',
                $transaksi['waktu'] ?? '',
                $transaksi['catatan'] ?? '',
                $transaksi['photo'] ?? '',
            ];
        }   

        // Jika data dari Database (Eloquent Model)
        return [
            $transaksi->id_transaksi,
            $transaksi->id_barang,
            $transaksi->barang->nama_barang ?? 'Tidak Ditemukan',
            $transaksi->tipe_transaksi,
            $transaksi->kuantitas,
            $transaksi->nama_pengirim_penerima,
            $transaksi->waktu,
            $transaksi->catatan,
            $transaksi->photo ?? 'Tidak Ada Foto',
        ];
    }

    public function title(): string
    {
        return $this->title;
    }
}