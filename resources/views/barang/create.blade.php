@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Tambah Barang</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('barang.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="id_barang">ID Barang</label>
            <input type="text" class="form-control" id="id_barang" name="id_barang" value="{{ $nextId }}" readonly required>
        </div>
        <div class="form-group">
            <label for="nama_barang">Nama Barang</label>
            <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
        </div>
        <div class="form-group">
            <label for="jenis_barang">Jenis Barang</label>
            <input type="text" class="form-control" id="jenis_barang" name="jenis_barang" required>
        </div>
        <div class="form-group">
            <label for="deskripsi_barang">Deskripsi Barang</label>
            <textarea class="form-control" id="deskripsi_barang" name="deskripsi_barang" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Simpan</button>
    </form>

    <!-- Section to display QR Code after saving -->
    @if (session('qr_code_url'))
        <div class="mt-4">
            <h4>QR Code Barang</h4>
            <img src="{{ session('qr_code_url') }}" alt="QR Code" class="img-fluid" style="max-width: 150px;">
        </div>
    @endif
</div>
@endsection
