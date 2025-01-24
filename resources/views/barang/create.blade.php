@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')
<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-header text-white" style="background-color: #06615E;">
            <h4 class="mb-0">Tambah Barang</h4>
        </div>
        <div class="card-body">
            @if (session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: '{{ session('success') }}',
                            icon: 'success',
                            confirmButtonColor: '#06615E',
                            confirmButtonText: 'OK'
                        });
                    });
                </script>
            @endif

            <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <!-- ID Barang -->
                    <div class="col-md-6 mb-3">
                        <label for="id_barang" class="form-label">ID Barang</label>
                        <input type="text" class="form-control" id="id_barang" name="id_barang" value="{{ $nextId }}" readonly required>
                    </div>

                    <!-- Nama Barang -->
                    <div class="col-md-6 mb-3">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                    </div>

                    <!-- Jenis Barang -->
                    <div class="col-md-6 mb-3">
                        <label for="jenis_barang" class="form-label">Jenis Barang</label>
                        <input type="text" class="form-control" id="jenis_barang" name="jenis_barang">
                    </div>

                    <!-- Foto Barang -->
                    <div class="col-md-6 mb-3">
                        <label for="foto_barang" class="form-label">Foto Barang</label>
                        <input type="file" class="form-control" id="foto_barang" name="foto_barang" accept="image/*" required onchange="previewImage(event)">
                        <img id="preview" src="#" alt="Pratinjau Gambar" class="img-thumbnail mt-3" style="display: none; max-width: 200px;">
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        Simpan
                    </button>
                </div>
            </form>

            <!-- Section to display QR Code after saving -->
            @if (session('qr_code_url') && session('last_generated_id'))
            <div class="mt-4 border-top pt-3">
                <h5 class="text-center text-primary"> QR Code Barang</h5>
                <div class="text-center my-4">
                    <img src="{{ session('qr_code_url') }}" alt="QR Code" class="img-fluid rounded shadow" style="max-width: 200px; border: 2px solid #007bff; padding: 5px;">
                </div>
                <div class="d-flex justify-content-center mt-3">
                    <a href="{{ route('barang.download_qr', ['id_barang' => session('last_generated_id')]) }}" class="btn btn-success me-2 shadow">
                        Download QR Code
                    </a>
                    <a class="btn btn-primary shadow" href="{{ route('barcode.index') }}">
                         Kembali ke Dashboard
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Preview Image Function
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '#';
            preview.style.display = 'none';
        }
    }
</script>
@endpush