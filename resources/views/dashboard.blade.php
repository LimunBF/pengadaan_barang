@extends('layouts.app')
@section('title', 'Dashboard', 'Data Gudang')
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
                    <input type="text" id="id_barang" name="id_barang" class="form-control"
                        value="{{ $barang->id_barang ?? '' }}" placeholder="Scan QR atau masukkan ID barang" readonly>
                </div>
            </div>

            {{-- Autofill Fields --}}
            <div class="mb-3">
                <label for="nama_barang" class="form-label">Nama Barang</label>
                <input type="text" id="nama_barang" name="nama_barang" class="form-control"
                    value="{{ $barang->nama_barang ?? '' }}" readonly>
            </div>
            <div class="mb-3">
                <label for="jenis_barang" class="form-label">Jenis Barang</label>
                <input type="text" id="jenis_barang" name="jenis_barang" class="form-control"
                    value="{{ $barang->jenis_barang ?? '' }}" readonly>
            </div>

            <div class="mb-3">
                <label for="kuantitas" class="form-label">
                    Stok Saat Ini: <span id="stok-gudang" class="badge bg-info text-dark">{{ $barang->stok ?? 0 }}</span>
                </label>
                <input type="number" id="kuantitas" name="kuantitas" class="form-control" value="{{ $barang->stok ?? '' }}"
                    placeholder="Stok saat ini" min="0" required>
            </div>

            <div class="mb-3">
                <label for="lokasi_rak" class="form-label">Lokasi Rak</label>
                <input type="text" id="lokasi_rak" name="lokasi_rak" class="form-control"
                    value="{{ $barang->lokasi_rak ?? '' }}" placeholder="Masukkan lokasi rak" required>
            </div>

            <div class="mb-3">
                <label for="nama_pengirim_penerima" class="form-label">Nama Pengirim/Penerima</label>
                <select id="nama_pengirim_penerima" name="nama_pengirim_penerima" class="form-control" required>
                    <option value="joko">Joko</option>
                    <option value="rizki">Rizki</option>
                    <option value="limun">Limun</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="catatan" class="form-label">Catatan Tambahan</label>
                <textarea id="catatan" name="catatan" class="form-control" rows="3"></textarea>
            </div>

            {{-- Tampilan Foto Barang --}}
            <div class="mb-3">
                <label for="foto_barang" class="form-label">Foto Barang</label>
                <div>
                    <img id="foto-barang"
                        src="{{ $barang->foto_barang ?? 'https://via.placeholder.com/150?text=No+Image' }}"
                        alt="Foto Barang" class="img-thumbnail"
                        style="max-width: 300px; max-height: 300px; cursor: pointer;" data-bs-toggle="modal"
                        data-bs-target="#fotoBarangModal">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

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

    {{-- JavaScript --}}
    @push('scripts')
        <script>
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
        </script>
    @endpush
@endsection
