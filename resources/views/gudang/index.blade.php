@extends('layouts.app')

@section('title', 'Data Gudang')

@section('content')
<h1 class="mb-4">Data Gudang</h1>

<!-- SweetAlert Notifikasi -->
@if (session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
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

<table class="table table-bordered table-striped">
    <thead class="table">
        <tr>
            <th style="text-align: center;">ID Barang</th>
            <th style="text-align: center;">Nama Barang</th>
            <th style="text-align: center;">Jenis Barang</th>
            <th style="text-align: center;">Lokasi Rak</th>
            <th style="text-align: center;">Stok</th>
            <th style="text-align: center;">Satuan</th>
            <th style="text-align: center;">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($gudangs as $barang)
        <tr>
            <td style="text-align: center;">{{ $barang['id_barang'] }}</td>
            <td>{{ $barang['nama_barang'] }}</td>
            <td>{{ $barang['jenis_barang'] }}</td>
            <td style="text-align: center;">{{ $barang['lokasi_rak'] }}</td>
            <td style="text-align: center;">{{ $barang['stok'] }}</td>
            <td style="text-align: center;">{{ $barang['satuan'] }}</td>
            <td style="text-align: center;">
                <button class="btn btn-primary btn-sm" onclick="openEditModal({{ $barang['id_barang'] }}, '{{ $barang['nama_barang'] }}', '{{ $barang['lokasi_rak'] }}', {{ $barang['stok'] }}, '{{ $barang['satuan'] }}')">Edit</button>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align: center;">Tidak ada data barang</td>
        </tr>
        @endforelse
    </tbody>
</table>

<!-- Modal untuk Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Data Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Tampilkan Nama Barang -->
                    <p class="mb-3"><strong>Nama Barang:</strong> <span id="nama_barang_display"></span></p>
                    <input type="hidden" name="id_barang" id="id_barang">
                    <div class="mb-3">
                        <label for="lokasi_rak" class="form-label">Lokasi Rak</label>
                        <input type="text" class="form-control" id="lokasi_rak" name="lokasi_rak" required>
                    </div>
                    <div class="mb-3">
                        <label for="stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stok" name="stok" required>
                    </div>
                    <div class="mb-3">
                        <label for="satuan" class="form-label">Satuan</label>
                        <input type="text" class="form-control" id="satuan" name="satuan">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Variabel untuk menyimpan instansi modal
    let modalInstance;

    /**
     * Membuka modal dengan data yang telah diisi
     * @param {int} id_barang
     * @param {string} nama_barang
     * @param {string} lokasi_rak
     * @param {int} stok
     * @param {string} satuan
     */
    function openEditModal(id_barang, nama_barang, lokasi_rak, stok, satuan) {
        // Reset dan buka modal
        if (modalInstance) {
            modalInstance.hide();
        }

        // Set data ke dalam modal
        document.getElementById('id_barang').value = id_barang;
        document.getElementById('nama_barang_display').textContent = nama_barang;
        document.getElementById('lokasi_rak').value = lokasi_rak;
        document.getElementById('stok').value = stok;
        document.getElementById('satuan').value = satuan;
        document.getElementById('editForm').action = `/gudang/edit/${id_barang}`;
        
        modalInstance = new bootstrap.Modal(document.getElementById('editModal'));
        modalInstance.show();
    }
</script>
@endpush
