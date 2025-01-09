@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="container">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-danger">Logout</button>
    </form>
    <h2>Form Pengambilan dan Penerimaan Barang</h2>
    <a href="{{ route('barang.create') }}" class="btn btn-success float-end">Tambah Barang</a>

    {{-- Feedback Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Formulir --}}
    <form method="POST" action="{{ route('transaksi.store') }}">
        @csrf
        <div class="mb-3">
            <label for="proses" class="form-label">Proses</label>
            <select id="proses" name="proses" class="form-control" required>
                <option value="masuk">Barang Masuk</option>
                <option value="keluar">Barang Keluar</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="id_barang" class="form-label">Scan QR Code / Masukkan ID Barang</label>
            <div class="input-group">
                <input type="text" id="id_barang" name="id_barang" class="form-control" placeholder="Scan QR atau masukkan ID barang" required>
                <button type="button" class="btn btn-outline-secondary" onclick="startQrScanner()">Scan QR</button>
            </div>
        </div>

        {{-- Autofill Fields --}}
        <div class="mb-3">
            <label for="nama_barang" class="form-label">Nama Barang</label>
            <input type="text" id="nama_barang" name="nama_barang" class="form-control" readonly>
        </div>
        <div class="mb-3">
            <label for="jenis_barang" class="form-label">Jenis Barang</label>
            <input type="text" id="jenis_barang" name="jenis_barang" class="form-control" readonly>
        </div>
        <div class="mb-3">
            <label for="deskripsi_barang" class="form-label">Deskripsi Barang</label>
            <textarea id="deskripsi_barang" name="deskripsi_barang" class="form-control" rows="3" readonly></textarea>
        </div>

        <div class="mb-3">
            <label for="kuantitas" class="form-label">Kuantitas</label>
            <input type="number" id="kuantitas" name="kuantitas" class="form-control" min="1" required>
        </div>

        <div class="mb-3">
            <label for="nama_pengirim_penerima" class="form-label">Nama Pengirim/Penerima</label>
            <input type="text" id="nama_pengirim_penerima" name="nama_pengirim_penerima" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="catatan" class="form-label">Catatan Tambahan</label>
            <textarea id="catatan" name="catatan" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

{{-- Modal untuk QR Code Scanner --}}
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrModalLabel">Scan QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="stopQrScanner()"></button>
            </div>
            <div class="modal-body">
                <div id="camera-container">
                    <video id="qr-video" style="width: 100%; height: 300px; border: 1px solid #ccc;" autoplay></video>
                    <button id="take-photo-btn" class="btn btn-primary mt-3" onclick="takePhoto()">Ambil Foto</button>
                </div>
                <canvas id="photo-canvas" style="display: none;"></canvas>
                <div id="photo-preview" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

{{-- Script untuk QR Code Scanner --}}
<script>
    let videoElement = document.getElementById('qr-video');
    let canvasElement = document.getElementById('photo-canvas');
    let photoPreview = document.getElementById('photo-preview');
    let cameraContainer = document.getElementById('camera-container');
    let videoStream = null;
    let photoTaken = false;

    function startQrScanner() {
        const modal = new bootstrap.Modal(document.getElementById('qrModal'));
        modal.show();

        photoTaken = false; // Reset state
        document.getElementById('take-photo-btn').disabled = false; // Enable photo button
        photoPreview.innerHTML = ""; // Clear photo preview
        cameraContainer.style.display = 'block';

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
                .then(function(stream) {
                    videoStream = stream;
                    videoElement.srcObject = stream;
                })
                .catch(function(err) {
                    alert("Kamera tidak diizinkan. Harap izinkan akses kamera untuk melanjutkan.");
                    console.error("User denied camera access:", err);
                });
        } else {
            alert("Perangkat Anda tidak mendukung akses kamera.");
        }
    }

    function takePhoto() {
        if (photoTaken) return; // Prevent multiple photos

        const context = canvasElement.getContext('2d');
        canvasElement.width = videoElement.videoWidth;
        canvasElement.height = videoElement.videoHeight;
        context.drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);

        const photoData = canvasElement.toDataURL('image/png');
        photoPreview.innerHTML = `<img src="${photoData}" alt="Photo" class="img-fluid mt-3" />`;
        canvasElement.style.display = 'none';

        photoTaken = true; // Mark photo as taken
        document.getElementById('take-photo-btn').disabled = true; // Disable photo button
        cameraContainer.style.display = 'none'; // Hide camera after photo taken
    }

    function stopQrScanner() {
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }
        videoElement.srcObject = null;
        photoPreview.innerHTML = "";
        photoTaken = false; // Reset state
        cameraContainer.style.display = 'block'; // Reset camera visibility
    }
    
    document.getElementById('id_barang').addEventListener('change', function () {
    let idBarang = this.value;

    // Pastikan ID barang tidak kosong
    if (idBarang.trim() === '') {
        clearBarangForm();
        return;
    }

    // AJAX request untuk mengambil data barang
    fetch(`/barang/${idBarang}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Data barang tidak ditemukan');
            }
            return response.json();
        })
        .then(data => {
            // Pastikan elemen tersedia sebelum mengisi nilai
            if (document.getElementById('nama_barang')) {
                document.getElementById('nama_barang').value = data.nama_barang || '';
            }
            if (document.getElementById('jenis_barang')) {
                document.getElementById('jenis_barang').value = data.jenis_barang || '';
           
            }
            if (document.getElementById('deskripsi_barang')) {
                document.getElementById('deskripsi_barang').value = data.deskripsi_barang || '';
            }
        }
    )
    }
)
</script>
@endsection
