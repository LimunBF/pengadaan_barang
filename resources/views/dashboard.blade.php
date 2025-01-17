@extends('layouts.app')
@section('title', 'Dashboard - Data Gudang')
@section('content')
<form method="POST" action="{{ route('transaksi.store') }}" autocomplete="off" class="p-4 shadow-sm rounded bg-light">
    @csrf
    <div class="mb-4">
        <label for="proses" class="form-label fw-bold">Proses</label>
        <input type="hidden" id="proses" name="proses" value="">
        <div class="d-flex gap-3">
            <button type="button" class="btn btn-outline-primary px-4" onclick="setValue('masuk', this);">Barang Masuk</button>
            <button type="button" class="btn btn-outline-secondary px-4" onclick="setValue('keluar', this);">Barang Keluar</button>
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
                <video id="video" autoplay style="border: 1px solid #ccc; width: 100%; max-width: 480px; display: none;"></video>
                <canvas id="canvas" style="display: none;"></canvas>
            </div>
            <button type="button" id="capture" class="btn btn-outline-primary mt-3" style="display: none;">Ambil Foto</button>
            <input type="hidden" id="image_data" name="image_data">
            <img id="preview" src="#" alt="Pratinjau Gambar" class="img-thumbnail mt-3" style="display: none; max-width: 200px;">
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
             document.addEventListener('DOMContentLoaded', function () {
        function setValue(value, button) {
            document.getElementById('proses').value = value;

            // Tambahkan kelas aktif untuk tombol
            const buttons = document.querySelectorAll('.d-flex .btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            // Tampilkan form
            const formContainer = document.getElementById('form-container');
            if (formContainer) {
                console.log("Form ditemukan di DOM.");
                formContainer.style.display = 'block';
            } else {
                console.error("Form tidak ditemukan di DOM.");
            }
        }

        // Attach setValue to global scope for inline usage
        window.setValue = setValue;
    });

            document.getElementById('id_barang').addEventListener('change', function() {
                let idBarang = this.value;

                // Pastikan ID barang tidak kosong
                if (idBarang.trim() === '') {
                    clearBarangForm();
                    return;
                }

                console.log('Mengambil data barang untuk ID: ' + idBarang);

                // AJAX request untuk mengambil data barang
                fetch(`/barang/${idBarang}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Data barang tidak ditemukan');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Data barang diterima:', data);

                        // Perbarui form dengan data barang
                        document.getElementById('nama_barang').value = data.nama_barang || '';
                        document.getElementById('jenis_barang').value = data.jenis_barang || '';
                        document.getElementById('stok-gudang').innerText = data.stok || 0;
                        document.getElementById('kuantitas').value = data.stok || '';

                        // Perbarui tampilan foto barang
                        const fotoBarang = data.foto_barang || 'https://via.placeholder.com/150?text=No+Image';
                        document.getElementById('foto-barang').src = fotoBarang;
                        document.getElementById('modal-foto-barang').src = fotoBarang;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        clearBarangForm();
                    });
            });
            document.addEventListener('DOMContentLoaded', () => {
    const formContainer = document.getElementById('form-container');
    if (formContainer) {
        console.log("Form-container ditemukan di DOM.");
    } else {
        console.error("Form-container tidak ditemukan di DOM.");
    }
});



            document.addEventListener('DOMContentLoaded', function () {
                // Ambil nilai id_barang dari input
                let idBarang = document.getElementById('id_barang').value.trim();

                // Jika id_barang tidak kosong, gunakan data dari session
                if (idBarang && typeof window.barangFromSession !== 'undefined') {
                    console.log('Menggunakan data barang dari session:', window.barangFromSession);

                    // Perbarui elemen HTML dengan data dari session
                    document.getElementById('nama_barang').value = window.barangFromSession.nama_barang || '';
                    document.getElementById('jenis_barang').value = window.barangFromSession.jenis_barang || '';
                    document.getElementById('stok-gudang').innerText = window.barangFromSession.stok || '0';
                    document.getElementById('kuantitas').value = '';
                    const fotoBarang = window.barangFromSession.foto_barang || 'https://via.placeholder.com/150?text=No+Image';
                    document.getElementById('foto-barang').src = fotoBarang;
                    document.getElementById('modal-foto-barang').src = fotoBarang;
                } else {
                    console.warn('Tidak ada data barang di session. Fetching dari server...');
                    // Lakukan fetch jika session kosong
                }

                const form = document.querySelector('form');
                form.addEventListener('submit', function (event) {
                    // Cegah submit jika ingin simulasi di client-side saja
                    // event.preventDefault();
                    
                    // Kosongkan form
                    clearBarangForm();

                    // Tampilkan pesan berhasil di log atau alert
                    console.log('Form berhasil dikirim dan data session telah dihapus.');
                });
            });

            // Fungsi untuk mengosongkan form barang
            function clearBarangForm() {
                document.getElementById('nama_barang').value = '';
                document.getElementById('jenis_barang').value = '';
                document.getElementById('stok-gudang').innerText = '0';
                document.getElementById('kuantitas').value = '';
                const placeholder = 'https://via.placeholder.com/150?text=No+Image';
                document.getElementById('foto-barang').src = placeholder;
                document.getElementById('modal-foto-barang').src = placeholder;
            }

            const openCameraButton = document.getElementById('open-camera');
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const captureButton = document.getElementById('capture');
            const imageDataInput = document.getElementById('image_data');
            const preview = document.getElementById('preview');
            const context = canvas.getContext('2d');

            openCameraButton.addEventListener('click', () => {
                navigator.mediaDevices.getUserMedia({ video: true })
                    .then(stream => {
                        video.srcObject = stream;
                        video.style.display = 'block';
                        captureButton.style.display = 'block';
                    })
                    .catch(error => {
                        alert("Anda tidak memberikan izin untuk menggunakan kamera.");
                        console.error("Kamera tidak dapat diakses:", error);
                    });
            });

            captureButton.addEventListener('click', () => {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                const imgURL = canvas.toDataURL('image/png');
                preview.src = imgURL;
                preview.style.display = 'block';
                imageDataInput.value = imgURL;
            });
        </script>
        <style>
            #form-container {
                display: none;
            }
        
            #form-container.show {
                display: block;
            }

    html, body {
        height: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
    }

        </style>
    @endpush
@endsection
