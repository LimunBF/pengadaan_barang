@extends('layouts.app')

@section('title', 'Daftar Transaksi Keluar')

@section('content')
<div class="container">
    <h2>Daftar Transaksi Keluar</h2>

    {{-- Tampilkan notifikasi jika ada --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Tabel untuk daftar transaksi keluar --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Transaksi</th>
                <th>ID Barang</th>
                <th>Jenis Transaksi</th>
                <th>Kuantitas</th>
                <th>Nama Petugas</th>
                <th>Waktu</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksi as $t)
                <tr>
                    <td>{{ $t->id_transaksi }}</td>
                    <td>{{ $t->id_barang }}</td>
                    <td>{{ $t->tipe_transaksi}}</td>
                    <td>{{ $t->kuantitas }}</td>
                    <td>{{ $t->nama_pengirim_penerima }}</td>
                    <td>{{ $t->waktu }}</td>
                    <td>{{ $t->catatan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
