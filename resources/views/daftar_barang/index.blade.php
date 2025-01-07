@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Daftar Barang</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Barang</th>
                <th>Nama Barang</th>
                <th>Jenis Barang</th>
                <th>Deskripsi Barang</th>
                <th>QR Code</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barang as $item)
            <tr>
                <td>{{ $item->id_barang }}</td>
                <td>{{ $item->nama_barang }}</td>
                <td>{{ $item->jenis_barang }}</td>
                <td>{{ $item->deskripsi_barang ?? '-' }}</td>
                <td>
                    @if($item->qr_code_path)
                        <img src="{{ asset($item->qr_code_path) }}" alt="QR Code" width="100">
                        <br>
                        <a href="{{ route('barang.downloadQRCode', $item->id_barang) }}" class="btn btn-primary btn-sm mt-2">
                            Download QR Code
                        </a>                        
                    @else
                        Tidak ada QR Code
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
