@extends('layouts.app')
@section('title', 'Dashboard - Data Gudang')
@section('content')

{{-- Jika belum ada barang discan, tampilkan tombol scan --}}
@if(!$barang)
<div class="container my-5 text-center">
    <div class="card shadow-sm p-5">
        <h2 class="text-muted mb-4">Belum ada barang dipilih</h2>
        <i class="bi bi-qr-code-scan text-primary" style="font-size: 5rem;"></i>
        <p class="mt-3">Silakan scan barcode barang terlebih dahulu.</p>
        <div class="mt-3">
            <a href="{{ route('barcode.index') }}" class="btn btn-primary btn-lg px-5">
                <i class="bi bi-camera me-2"></i> Mulai Scan
            </a>
        </div>
    </div>
</div>
@else

{{-- Jika sudah ada barang, tampilkan Form Transaksi --}}
<form method="POST" action="{{ route('transaksi.store') }}" autocomplete="off" class="p-4 shadow-sm rounded bg-light container mt-4">
    @csrf
    
    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-4">
        <label for="proses" class="form-label fw-bold">Proses Transaksi</label>
        <input type="hidden" id="proses" name="proses" value="">
        <div class="d-flex gap-3">
            <button type="button" class="btn btn-outline-success px-4 w-50" onclick="setValue('masuk', this);">
                <i class="bi bi-arrow-down-circle me-2"></i> Barang Masuk
            </button>
            <button type="button" class="btn btn-outline-danger px-4 w-50" onclick="setValue('keluar', this);">
                <i class="bi bi-arrow-up-circle me-2"></i> Barang Keluar
            </button>
        </div>
    </div>

    {{-- Form Container (Hidden by default until process selected) --}}
    <div id="form-container" style="display: none;">
        <div class="row">
            {{-- Kolom Kiri: Detail Barang --}}
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-white fw-bold">Detail Barang</div>
                    <div class="card-body text-center">
                        {{-- Logic Gambar Barang dengan Asset Storage --}}
                        @php
                            $fotoPath = $barang['foto_barang'] ?? null;
                            $fotoUrl = $fotoPath ? asset('storage/' . $fotoPath) : 'https://via.placeholder.com/150?text=No+Image';
                        @endphp
                        <img id="foto-barang"
                            src="{{ $fotoUrl }}"
                            alt="Foto Barang" class="img-thumbnail mb-3"
                            style="max-height: 200px; cursor: pointer;" 
                            data-bs-toggle="modal" data-bs-target="#fotoBarangModal">
                        
                        <div class="input-group mb-2">
                            <span class="input-group-text">ID</span>
                            <input type="text" name="id_barang" class="form-control bg-white" value="{{ $barang['id_barang'] }}" readonly>
                        </div>
                        <div class="input-group mb-2">
                            <span class="input-group-text">Nama</span>
                            <input type="text" class="form-control bg-white" value="{{ $barang['nama_barang'] }}" readonly>
                        </div>
                        <div class="input-group mb-2">
                            <span class="input-group-text">Jenis</span>
                            <input type="text" class="form-control bg-white" value="{{ $barang['jenis_barang'] }}" readonly>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Input Transaksi --}}
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Stok Gudang: <span class="badge bg-primary fs-6">{{ $barang['stok'] }}</span></label>
                    <input type="number" id="kuantitas" name="kuantitas" class="form-control form-control-lg"
                        placeholder="Masukkan jumlah..." min="1" required>
                </div>

                <div class="mb-3">
                    <label for="lokasi_rak" class="form-label fw-bold">Lokasi Rak</label>
                    <input type="text" id="lokasi_rak" name="lokasi_rak" class="form-control"
                        value="{{ $barang['lokasi_rak'] }}" placeholder="Update rak jika perlu">
                </div>

                <div class="mb-3">
                    <label for="nama_pengirim_penerima" class="form-label fw-bold">Petugas / Penanggung Jawab</label>
                    <select id="nama_pengirim_penerima" name="nama_pengirim_penerima" class="form-select" required>
                        <option value="" disabled selected>-- Pilih Petugas --</option>
                        @foreach($petugas as $p)
                            <option value="{{ $p->nama }}">{{ $p->nama }}</option>
                        @endforeach
                    </select>            
                </div>

                <div class="mb-3">
                    <label for="catatan" class="form-label fw-bold">Catatan</label>
                    <textarea id="catatan" name="catatan" class="form-control" rows="2"></textarea>
                </div>

                {{-- Fitur Kamera (Original) --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Foto Bukti (Kamera)</label>
                    <div class="border p-2 rounded text-center bg-white">
                        <button type="button" id="open-camera" class="btn btn-sm btn-outline-secondary mb-2">
                            <i class="bi bi-camera"></i> Buka Kamera
                        </button>
                        <div style="position: relative;">
                            <video id="video" autoplay style="width: 100%; max-height: 200px; display: none; object-fit: cover;" class="rounded"></video>
                            <canvas id="canvas" style="display: none;"></canvas>
                        </div>
                        <button type="button" id="capture" class="btn btn-sm btn-primary mt-2" style="display: none;">Ambil Foto</button>
                        
                        <input type="hidden" id="image_data" name="image_data">
                        <img id="preview" src="#" alt="Hasil Foto" class="img-thumbnail mt-2" style="display: none; max-height: 200px; margin: 0 auto;">
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2 fw-bold">SIMPAN TRANSAKSI</button>
            </div>
        </div>
    </div>
</form>
@endif

{{-- Modal Zoom Gambar --}}
<div class="modal fade" id="fotoBarangModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-0">
                @php
                    $modalFotoUrl = isset($barang['foto_barang']) ? asset('storage/' . $barang['foto_barang']) : '#';
                @endphp
                <img src="{{ $modalFotoUrl }}" class="img-fluid" alt="Foto Barang">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const setValue = (value, button) => {
        document.getElementById('proses').value = value;
        
        // Visual feedback tombol aktif
        document.querySelectorAll('.d-flex .btn').forEach(btn => {
            btn.classList.remove('active', 'btn-success', 'btn-danger');
            if(btn.classList.contains('btn-outline-success')) btn.classList.add('btn-outline-success');
            if(btn.classList.contains('btn-outline-danger')) btn.classList.add('btn-outline-danger');
        });

        button.classList.remove(value === 'masuk' ? 'btn-outline-success' : 'btn-outline-danger');
        button.classList.add(value === 'masuk' ? 'btn-success' : 'btn-danger');
        
        document.getElementById('form-container').style.display = 'block';
    };

    // Logic Kamera (Persis Original)
    const openBtn = document.getElementById('open-camera');
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureBtn = document.getElementById('capture');
    const preview = document.getElementById('preview');
    const imageInput = document.getElementById('image_data');
    let stream = null;

    if(openBtn) {
        openBtn.addEventListener('click', async () => {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = stream;
                video.style.display = 'block';
                captureBtn.style.display = 'inline-block';
                preview.style.display = 'none';
                openBtn.style.display = 'none';
            } catch (err) {
                alert("Gagal akses kamera: " + err);
            }
        });

        captureBtn.addEventListener('click', () => {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            
            const dataUrl = canvas.toDataURL('image/png');
            imageInput.value = dataUrl;
            preview.src = dataUrl;
            preview.style.display = 'block';
            
            // Matikan kamera
            stream.getTracks().forEach(track => track.stop());
            video.style.display = 'none';
            captureBtn.style.display = 'none';
            openBtn.innerText = 'Ambil Ulang';
            openBtn.style.display = 'inline-block';
        });
    }
</script>
@endpush
@endsection