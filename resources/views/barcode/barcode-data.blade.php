@extends('layouts.app')

@section('title', 'Barcode Data')

@section('content')
<div id="main-content" class="container d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 230px);">
    <!-- Card with buttons -->
    <div id="button-card" class="card shadow-lg p-4" style="background-color: #f8f9fa; border-radius: 12px; width: 650px;">
        <h1 class="mb-4 text-primary text-center">Pilih Aksi</h1>
        <div class="d-grid gap-4">
            <button 
                class="btn btn-primary fw-bold shadow-sm" 
                style="border-radius: 30px; height: 125px; font-size: 2rem;" 
                onclick="startQrScanner()">
                <i class="bi bi-upc-scan me-2"></i> Scan QR Code
            </button>
            <button 
                class="btn btn-success fw-bold shadow-sm" 
                style="border-radius: 30px; height: 125px; font-size: 2rem;" 
                onclick="window.location.href='{{ route('barang.create') }}'">
                <i class="bi bi-plus-circle me-2"></i> Tambah Barang
            </button>
        </div>
    </div>

    <!-- Scanner Container -->
    <div id="scanner-container" class="d-none text-center">
        <!-- Kamera live atau hasil gambar akan ditampilkan di sini -->
        <div id="camera-container">
            <video id="qr-video" autoplay playsinline style="width: 100%; max-width: 600px; border: 2px solid #ccc; border-radius: 12px;"></video>
            <canvas id="photo-canvas" style="display: none;"></canvas>
        </div>
        <div class="d-grid gap-3 mt-3">
            <button 
                id="take-photo-btn" 
                class="btn btn-primary" 
                onclick="takePhoto()">Ambil Gambar</button>
            <input 
                type="file" 
                id="upload-file-input" 
                class="form-control" 
                accept="image/*" 
                onchange="uploadFile()">
            <button 
                class="btn btn-danger" 
                onclick="stopQrScanner()">Batal</button>
        </div>
        <div id="process-button-container" class="mt-3 d-none">
            <button 
                id="process-btn" 
                class="btn btn-success" 
                onclick="processScan()">Proses</button>
        </div>
    </div>       
</div>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- JavaScript --}}
@push('scripts')
<script>
    let videoElement = document.getElementById('qr-video');
    let canvasElement = document.getElementById('photo-canvas');
    let cameraContainer = document.getElementById('camera-container');
    let buttonCard = document.getElementById('button-card');
    let scannerContainer = document.getElementById('scanner-container');
    let processButtonContainer = document.getElementById('process-button-container');
    let takePhotoButton = document.getElementById('take-photo-btn');
    let uploadFileInput = document.getElementById('upload-file-input');
    let videoStream = null;
    let photoTaken = false; // Flag untuk mengontrol pengambilan gambar

    function startQrScanner() {
        // Reset flag dan tampilan
        photoTaken = false;
        takePhotoButton.disabled = false;
        uploadFileInput.disabled = false;
        canvasElement.style.display = 'none'; // Sembunyikan canvas
        videoElement.style.display = 'block'; // Tampilkan video

        // Sembunyikan tombol awal, tampilkan kamera
        buttonCard.classList.add('d-none');
        scannerContainer.classList.remove('d-none');
        processButtonContainer.classList.add('d-none');

        // Akses kamera
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
                .then(function(stream) {
                    videoStream = stream;
                    videoElement.srcObject = stream;
                })
                .catch(function(err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Akses Kamera Ditolak',
                        text: 'Harap izinkan akses kamera untuk melanjutkan.',
                    });
                    console.error("User denied camera access:", err);
                    stopQrScanner();
                });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Perangkat Tidak Didukung',
                text: 'Perangkat Anda tidak mendukung akses kamera.',
            });
            stopQrScanner();
        }
    }

    function takePhoto() {
        if (photoTaken) return; // Cegah pengambilan gambar kedua

        const context = canvasElement.getContext('2d');
        canvasElement.width = videoElement.videoWidth;
        canvasElement.height = videoElement.videoHeight;
        context.drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);

        // Tampilkan gambar hasil tangkapan di tempat video
        videoElement.style.display = 'none'; // Sembunyikan video
        canvasElement.style.display = 'block'; // Tampilkan canvas dengan hasil gambar

        // Berhenti streaming kamera
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }
        videoElement.srcObject = null;

        // Tampilkan tombol proses
        processButtonContainer.classList.remove('d-none');
        takePhotoButton.disabled = true; // Disable tombol "Ambil Gambar"
        uploadFileInput.disabled = true; // Disable tombol "Unggah Gambar"
        photoTaken = true; // Tandai bahwa gambar sudah diambil

        Swal.fire({
            icon: 'success',
            title: 'Gambar Berhasil Diambil',
            text: 'Silakan klik tombol "Proses" untuk melanjutkan.',
        });
    }

    function uploadFile() {
        const file = uploadFileInput.files[0];
        if (!file) {
            Swal.fire({
                icon: 'error',
                title: 'File Tidak Dipilih',
                text: 'Silakan pilih file gambar terlebih dahulu.',
            });
            return;
        }

        // Berhenti streaming kamera jika sedang aktif
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
            videoElement.srcObject = null;
        }

        const reader = new FileReader();
        reader.onload = function(event) {
            const image = new Image();
            image.onload = function() {
                // Tampilkan gambar hasil upload di canvas
                const context = canvasElement.getContext('2d');
                canvasElement.width = image.width;
                canvasElement.height = image.height;
                context.drawImage(image, 0, 0, canvasElement.width, canvasElement.height);

                // Sembunyikan video, tampilkan canvas
                videoElement.style.display = 'none';
                canvasElement.style.display = 'block';

                // Tampilkan tombol proses
                processButtonContainer.classList.remove('d-none');
                takePhotoButton.disabled = true; // Disable tombol "Ambil Gambar"
                uploadFileInput.disabled = true; // Disable tombol "Unggah Gambar"

                Swal.fire({
                    icon: 'success',
                    title: 'File Berhasil Diunggah',
                    text: 'Silakan klik tombol "Proses" untuk melanjutkan.',
                });
            };
            image.src = event.target.result;
        };
        reader.readAsDataURL(file);
    }

    function processScan() {
        const photoCanvas = document.getElementById('photo-canvas');
        const photoDataUrl = photoCanvas.toDataURL('image/png'); // Ambil gambar dari canvas
        const formData = new FormData();

        // Konversi Data URL ke File
        const blob = dataURLtoBlob(photoDataUrl);
        formData.append('qr_image', blob, 'qr-code.png');

        fetch('{{ route('proses.scan') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                        timer: 2000, // Tampilkan selama 2 detik
                        showConfirmButton: false,
                    }).then(() => {
                        resetScanner(); // Reset elemen setelah scan berhasil
                        window.location.href = data.redirect_url; // Alihkan ke halaman dashboard
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message,
                    });
                    resetScanner(); // Reset elemen setelah scan gagal
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Tidak dapat memproses hasil scan.',
                });
                resetScanner(); // Reset elemen setelah scan gagal
            });
    }

    function dataURLtoBlob(dataURL) {
        const byteString = atob(dataURL.split(',')[1]);
        const mimeString = dataURL.split(',')[0].split(':')[1].split(';')[0];
        const ab = new ArrayBuffer(byteString.length);
        const ia = new Uint8Array(ab);

        for (let i = 0; i < byteString.length; i++) {
            ia[i] = byteString.charCodeAt(i);
        }

        return new Blob([ab], { type: mimeString });
    }

    function resetScanner() {
        // Berhenti streaming kamera jika sedang aktif
        if (videoStream) {
            videoStream.getTracks().forEach((track) => track.stop());
            videoStream = null;
        }
        videoElement.srcObject = null;

        // Reset unggahan file
        uploadFileInput.value = ""; // Kosongkan input file

        // Kembalikan ke tampilan awal
        buttonCard.classList.remove('d-none');
        scannerContainer.classList.add('d-none');
        videoElement.style.display = 'block';
        canvasElement.style.display = 'none';
        processButtonContainer.classList.add('d-none');
        photoTaken = false;
        takePhotoButton.disabled = false;
        uploadFileInput.disabled = false;
    }


    function stopQrScanner() {
        // Berhenti streaming kamera jika sedang aktif
        if (videoStream) {
            videoStream.getTracks().forEach((track) => track.stop());
            videoStream = null;
        }
        videoElement.srcObject = null;

        // Reset unggahan file
        uploadFileInput.value = ""; // Kosongkan input file

        // Kembalikan ke tampilan awal
        buttonCard.classList.remove('d-none');
        scannerContainer.classList.add('d-none');
        videoElement.style.display = 'block';
        canvasElement.style.display = 'none';
        processButtonContainer.classList.add('d-none');
        photoTaken = false;
        takePhotoButton.disabled = false;
        uploadFileInput.disabled = false;

        Swal.fire({
            icon: 'info',
            title: 'Reset',
            text: 'Semua input telah direset.',
            timer: 1500,
            showConfirmButton: false,
        });
    }
</script>
@endpush
@endsection
