@extends('layouts.app')

@section('title', 'Tambah Barang Baru')

@section('content')
<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-header text-white" style="background-color: #06615E;">
            <h4 class="mb-0">Tambah Barang Baru (Master Data)</h4>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ID Barang</label>
                        <input type="text" class="form-control" name="id_barang" value="{{ $nextId }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" name="nama_barang" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jenis Barang</label>
                        <input type="text" class="form-control" name="jenis_barang">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Foto Barang</label>
                        <input type="file" class="form-control" name="foto_barang" accept="image/*" required onchange="previewImage(event)">
                        <img id="preview" src="#" class="img-thumbnail mt-3" style="display: none; max-width: 200px;">
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Simpan Barang Baru</button>
                </div>
            </form>

            @if (session('qr_code_url'))
            <div class="mt-4 border-top pt-3 text-center">
                <h5 class="text-primary">QR Code Berhasil Dibuat</h5>
                <img src="{{ session('qr_code_url') }}" class="img-fluid rounded shadow my-3" style="max-width: 200px;">
                <br>
                <a href="{{ route('barang.downloadQRCode', session('last_generated_id')) }}" class="btn btn-success">
                    Download QR Code
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    function previewImage(event) {
        const preview = document.getElementById('preview');
        preview.src = URL.createObjectURL(event.target.files[0]);
        preview.style.display = 'block';
    }
</script>
@endsection