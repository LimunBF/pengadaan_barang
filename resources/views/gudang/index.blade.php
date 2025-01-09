@extends('layouts.app')

@section('title', 'Data Gudang')

@section('content')
<h1 class="mb-4">Data Gudang</h1>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th style="text-align: center;">ID Barang</th>
            <th style="text-align: center;">Nama Barang</th>
            <th style="text-align: center;">Jenis Barang</th>
            <th style="text-align: center;">Lokasi Rak</th>
            <th style="text-align: center;">Stok</th>
            <th style="text-align: center;">satuan</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($gudangs as $barang)
        <tr>
            <td style="text-align: center;">{{ $barang['id_barang'] }}</td>
            <td>{{ $barang['nama_barang'] }}</td>
            <td>{{ $barang['jenis_barang'] }}</td>
            <td>{{ $barang['lokasi_rak'] }}</td>
            <td style="text-align: center;">{{ $barang['stok'] }}</td>
            <td style="text-align: center;">{{ $barang['satuan'] }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align: center;">Tidak ada data barang</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
