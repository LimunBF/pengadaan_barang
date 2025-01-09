@extends('layouts.app')

@section('title', 'Daftar Transaksi')

@section('content')
<div class="container">
    <h2>Daftar Transaksi</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Transaksi</th>
                <th>ID Barang</th>
                <th>Jenis Transaksi</th>
                <th>Kuantitas</th>
                <th>Nama Pengirim/Penerima</th>
                <th>Waktu</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksi as $t)
                <tr>
                    <td>{{ $t->id_transaksi }}</td>
                    <td>{{ $t->id_barang }}</td>
                    <td>{{ $t->tipe_transaksi }}</td>
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
