<?php

namespace App\Exports;

use App\Models\Gudang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GudangExport implements FromCollection, WithHeadings,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Gudang::all();
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
            'satuan'
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
