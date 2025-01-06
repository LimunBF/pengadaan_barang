@extends('layouts.app')

@section('title', 'Form Barang Masuk/Keluar')

@section('content')
<h1>Form Barang Masuk/Keluar</h1>
<form method="POST" action="{{ route('transaksi.store') }}">
    @csrf
    <div class="mb-3">
        <label for="tipe_transaksi" class="form-label">Proses</label>
        <select id="tipe_transaksi" name="tipe_transaksi" class="form-control" required>
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
        <input type="number" id="kuantitas" name="kuantitas" class="form-control" required>
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

{{-- QR Scanner Modal --}}
<div id="qrModal" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scan QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <video id="qr-video" style="width: 100%;"></video>
            </div>
        </div>
    </div>
</div>

<script>
    // QR Code Scanner Initialization
    let scanner;
    function startQrScanner() {
        const modal = new bootstrap.Modal(document.getElementById('qrModal'));
        modal.show();

        scanner = new Html5QrcodeScanner("qr-video", { fps: 10, qrbox: 250 });
        scanner.render((decodedText) => {
            document.getElementById('id_barang').value = decodedText;
            modal.hide();
            scanner.clear();
        });
    }
</script>
<script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
@endsection
