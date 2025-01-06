@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<h1>Riwayat Transaksi</h1>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID Transaksi</th>
            <th>ID Barang</th>
            <th>Tipe Transaksi</th>
            <th>Kuantitas</th>
            <th>Waktu</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        {{-- Loop data transaksi di sini --}}
    </tbody>
</table>
@endsection
