<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GudangExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'ID Barang',
            'Nama Barang',
            'Jenis Barang',
            'Foto Barang',
            'Lokasi Rak',
            'Stok',
            'Satuan',
        ];
    }

    public function map($gudang): array
    {
        return [
            $gudang->id,
            $gudang->nama_barang,
            $gudang->jenis_barang,
            $gudang->photo,
            $gudang->lokasi_rak,
            $gudang->stok,
            $gudang->satuan,
        ];
    }
}
