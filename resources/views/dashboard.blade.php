@extends('layouts.app')
@section('title', 'Dashboard - Data Gudang')
@section('content')
<form method="POST" action="{{ route('transaksi.store') }}" autocomplete="off" class="p-4 shadow-sm rounded bg-light">
    @csrf
    <div class="mb-4">
        <label for="proses" class="form-label fw-bold">Proses</label>
        <input type="hidden" id="proses" name="proses" value="">
        <div class="d-flex gap-3">
            <button type="button" class="btn btn-primary px-4" onclick="setValue('masuk', this);">Barang Masuk</button>
            <button type="button" class="btn btn-primary px-4" onclick="setValue('keluar', this);">Barang
                Keluar</button>
        </div>
    </div>

    <div id="form-container" style="display: none;">
        <div class="mb-3">
            <label for="id_barang" class="form-label fw-bold">Scan QR Code / Masukkan ID Barang</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                <input type="text" id="id_barang" name="id_barang" class="form-control"
                    value="{{ $barang->id_barang ?? '' }}" placeholder="Scan QR atau masukkan ID barang" readonly>
            </div>
        </div>

        {{-- Autofill Fields --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nama_barang" class="form-label fw-bold">Nama Barang</label>
                <i class="fa-solid fa-boxes-stacked"></i>
                <input type="text" id="nama_barang" name="nama_barang" class="form-control"
                    value="{{ $barang->nama_barang ?? '' }}" readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label for="jenis_barang" class="form-label fw-bold">Jenis Barang</label>
                <i class="fa-solid fa-boxes-stacked"></i>
                <input type="text" id="jenis_barang" name="jenis_barang" class="form-control"
                    value="{{ $barang->jenis_barang ?? '' }}" readonly>
            </div>
        </div>

        <div class="mb-3">
            <label for="kuantitas" class="form-label fw-bold">
                Stok Saat Ini: <span id="stok-gudang" class="badge bg-info text-dark">{{ $barang->stok ?? 0 }}</span>
            </label>
            <input type="number" id="kuantitas" name="kuantitas" class="form-control"
                placeholder="Masukkan jumlah stok baru" min="0" required>
        </div>

        <div class="mb-3">
            <label for="lokasi_rak" class="form-label fw-bold">Lokasi Rak</label>
            <input type="text" id="lokasi_rak" name="lokasi_rak" class="form-control"
                value="{{ $barang->lokasi_rak ?? '' }}" placeholder="Masukkan lokasi rak" required>
        </div>

        <div class="mb-3">
            <label for="nama_pengirim_penerima" class="form-label fw-bold">Nama Pengirim/Penerima</label>
            <select id="nama_pengirim_penerima" name="nama_pengirim_penerima" class="form-select" required>
                <option value="" disabled selected>Pilih Pengirim/Penerima</option>
                <option value="joko">Joko</option>
                <option value="rizki">Rizki</option>
                <option value="limun">Limun</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="catatan" class="form-label fw-bold">Catatan Tambahan</label>
            <textarea id="catatan" name="catatan" class="form-control" rows="3"></textarea>
        </div>

        {{-- Tampilan Foto Barang --}}
        <div class="mb-3">
            <label for="foto_barang" class="form-label fw-bold">Foto Barang</label>
            <div class="text-center">
                <img id="foto-barang"
                    src="{{ $barang->foto_barang ?? 'https://via.placeholder.com/150?text=No+Image' }}"
                    alt="Foto Barang" class="img-thumbnail"
                    style="max-width: 300px; max-height: 300px; cursor: pointer;" data-bs-toggle="modal"
                    data-bs-target="#fotoBarangModal">
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="foto_barang" class="form-label fw-bold">Foto Bukti</label>
            <div>
                <button type="button" id="open-camera" class="btn btn-outline-secondary mb-3">Buka Kamera</button>
                <video id="video" autoplay
                    style="border: 1px solid #ccc; width: 100%; max-width: 480px; display: none;"></video>
                <canvas id="canvas" style="display: none;"></canvas>
            </div>
            <button type="button" id="capture" class="btn btn-outline-primary mt-3" style="display: none;">Ambil
                Foto</button>
            <input type="hidden" id="image_data" name="image_data">
            <img id="preview" src="#" alt="Pratinjau Gambar" class="img-thumbnail mt-3"
                style="display: none; max-width: 200px;">
        </div>

        <button type="submit" class="btn btn-success w-100">Submit</button>
    </div>
</form>

{{-- Modal Zoom Gambar --}}
<div class="modal fade" id="fotoBarangModal" tabindex="-1" aria-labelledby="fotoBarangModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fotoBarangModalLabel">Foto Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modal-foto-barang"
                    src="{{ $barang->foto_barang ?? 'https://via.placeholder.com/150?text=No+Image' }}"
                    alt="Foto Barang" class="img-fluid">
            </div>
        </div>
    </div>
</div>


{{--FOTO BUKTI BARANG--}}

{{-- JavaScript --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Inisialisasi Toast Notification
            const showToast = (icon, title, text, timer = 3000) => {
                Swal.fire({
                    icon,
                    title,
                    text,
                    timer,
                    showConfirmButton: false,
                }).then(() => {
                    if (icon === 'success') {
                        window.location.href = "{{ route('transaksi.index') }}";
                    }
                });
            };

            // Tampilkan notifikasi berdasarkan session
            @if (session('success'))
                showToast('success', 'Berhasil!', '{{ session('success') }}', 2000);
            @endif
            @if (session('error'))
                showToast('error', 'Gagal!', '{{ session('error') }}', 3000);
            @endif

            // Fungsi Mengatur Nilai Form
            const setValue = (value, button) => {
                document.getElementById('proses').value = value;

                // Highlight tombol aktif
                document.querySelectorAll('.d-flex .btn').forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                // Tampilkan form
                const formContainer = document.getElementById('form-container');
                if (formContainer) {
                    formContainer.style.display = 'block';
                } else {
                    console.error("Form tidak ditemukan di DOM.");
                }
            };

            // Attach ke global scope
            window.setValue = setValue;

            // Validasi Form Sebelum Submit
            document.querySelector('form').addEventListener('submit', function () {
                const proses = document.getElementById('proses').value;
                const kuantitas = document.getElementById('kuantitas').value;
                const imageData = document.getElementById('image_data').value;
                const catatan = document.getElementById('catatan').value.trim();

                if (!proses) {
                    alert('Silakan pilih proses (Barang Masuk atau Keluar) terlebih dahulu.');
                    return false;
                }
                if (!kuantitas) {
                    alert('Silakan masukkan jumlah kuantitas.');
                    return false;
                }
                // Cek jika catatan kosong
                if (!catatan) {
                    event.preventDefault(); // Cegah form dari submit
                    showToast('error', 'Gagal!', 'Silakan isi catatan sebelum mengirim.');
                    return;
                }
                if (!imageData) {
                    event.preventDefault(); // Cegah form dari submit
                    showToast('error', 'Gagal!', 'Silakan ambil foto bukti sebelum mengirim.');
                    return;
                }
            });

            // Kamera dan Screenshot
            const openCameraButton = document.getElementById('open-camera');
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const captureButton = document.getElementById('capture');
            const preview = document.getElementById('preview');
            const imageDataInput = document.getElementById('image_data');

            let stream = null; // Untuk menyimpan stream kamera
            let timer = null;  // Untuk menyimpan timer

            // Fungsi untuk menutup kamera
            const stopCamera = () => {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop()); // Hentikan semua track kamera
                    stream = null;
                }
                video.style.display = 'none';
                captureButton.style.display = 'none';
                clearTimeout(timer); // Hentikan timer jika ada

                // Tampilkan notifikasi dengan SweetAlert2
                Swal.fire({
                    icon: 'info',
                    title: 'Waktu Habis',
                    text: 'Waktu pengambilan gambar telah habis. Silakan coba lagi.',
                    confirmButtonText: 'OK'
                });
            };
            openCameraButton.addEventListener('click', () => {
                navigator.mediaDevices.getUserMedia({ video: true })
                    .then(mediaStream => {
                        stream = mediaStream;
                        video.srcObject = stream;
                        video.style.display = 'block';
                        captureButton.style.display = 'block';

                        // Mulai timer untuk mematikan kamera otomatis setelah 30 detik
                        timer = setTimeout(() => {
                            stopCamera();
                        }, 30000); // 30 detik
                    })
                    .catch(error => {
                        alert("Tidak dapat mengakses kamera.");
                        console.error("Kamera gagal:", error);
                    });
            });

            captureButton.addEventListener('click', () => {
                if (!stream) return; // Jika kamera tidak aktif, abaikan

                // Tangkap gambar dari video
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

                const imgURL = canvas.toDataURL('image/png');
                preview.src = imgURL;
                preview.style.display = 'block';
                imageDataInput.value = imgURL;

                // Matikan kamera setelah mengambil gambar
                stopCamera();
            });
        });
    </script>
    <style>
        #form-container {
            display: none;
        }

        #form-container.show {
            display: block;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }
    </style>
@endpush
@endsection