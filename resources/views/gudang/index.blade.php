@extends('layouts.app')

@section('title', 'Data Gudang')

@section('content')
<h1 class="mb-4">Data Gudang</h1>

<a href="#" id="exportGudangExcel" class="btn btn-success mb-5">Export ke Excel</a>



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

    <div class="container-fluid mb-3">
        <div class="row g-3">
            <!-- Searchbox -->
            <div class="col-lg-4 col-md-6">
                <label for="searchBox" class="form-label">Cari Nama Barang:</label>
                <input type="text" id="searchBox" class="form-control" placeholder="Ketik nama barang...">
            </div>

            <!-- Filter Range Stok -->
            <div class="col-lg-4 col-md-6">
                <label for="stokRange" class="form-label">Filter Stok:</label>
                <div class="d-flex">
                    <input type="number" id="stokMin" class="form-control me-2" placeholder="Min">
                    <input type="number" id="stokMax" class="form-control" placeholder="Max">
                </div>
            </div>

            <!-- Filter Lokasi Rak -->
            <div class="col-lg-4 col-md-12">
                <label for="lokasiRakFilter" class="form-label">Filter Lokasi Rak:</label>
                <select id="lokasiRakFilter" class="form-control">
                    <option value="">Semua Rak</option>
                </select>
            </div>
        </div>
    </div>
    
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th class="tengah">ID Barang</th>
                <th class="tengah">Nama Barang</th>
                <th class="tengah">Jenis Barang</th>
                <th class="tengah">Foto Barang</th>
                <th class="tengah">Lokasi Rak</th>
                <th class="tengah">Stok</th>
                <th class="tengah">Satuan</th>
                <th class="tengah">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($gudangs as $barang)
            <tr>
                <td class="tengah-kolom" data-bs-toggle="tooltip" title="{{ $barang['id_barang'] }}">{{ $barang['id_barang'] }}</td>
                <td class="tengah-kolom" data-bs-toggle="tooltip" title="{{ $barang['nama_barang'] }}">{{ Str::limit($barang['nama_barang'], 20) }}</td>
                <td class="tengah-kolom">
                    <span data-bs-toggle="tooltip" title="{{ $barang['jenis_barang'] }}">
                        {{ Str::limit($barang['jenis_barang'], 15) }}
                    </span>
                </td>
                <td class="tengah">
                    @if ($barang->barang && $barang->barang->foto_barang)
                    <img src="{{ $barang->barang->foto_barang }}" 
                        alt="Foto {{ $barang->nama_barang }}" 
                        style="width: 100px; height: auto; cursor: pointer;" 
                        onclick="openImageModal('{{ $barang->barang->foto_barang }}')">
                    @else
                    <span>Tidak ada foto</span>
                    @endif
                </td>                
                <td class="tengah-kolom" data-bs-toggle="tooltip" title="{{ $barang['lokasi_rak'] }}">{{ Str::limit($barang['lokasi_rak'], 15) }}</td>
                <td class="tengah-kolom" data-bs-toggle="tooltip" title="{{ $barang['stok'] }}">{{ $barang['stok'] }}</td>
                <td class="tengah-kolom" data-bs-toggle="tooltip" title="{{ $barang['satuan'] }}">{{ $barang['satuan'] }}</td>
                <td class="tengah-kolom">
                    <button class="btn btn-sm btn-primary" onclick="openEditModal({{ $barang['id_barang'] }}, '{{ $barang['nama_barang'] }}', '{{ $barang['lokasi_rak'] }}', {{ $barang['stok'] }}, '{{ $barang['satuan'] }}')" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="tengah">Tidak ada data barang</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $gudangs->links('pagination::bootstrap-5') }}
    </div>
</div>

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

<!-- Modal untuk memperbesar gambar -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Foto Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <!-- Gambar akan diisi dengan JavaScript -->
                <img id="imageModalContent" src="" class="img-fluid" alt="Foto Barang">
            </div>
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

    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });

        /**
     * Membuka modal untuk menampilkan gambar besar
     * @param {string} imageUrl - URL gambar
     */
    function openImageModal(imageUrl) {
        // Set gambar ke dalam modal
        document.getElementById('imageModalContent').src = imageUrl;

        // Tampilkan modal
        const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
        imageModal.show();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const searchBox = document.getElementById('searchBox');
        const stokMin = document.getElementById('stokMin');
        const stokMax = document.getElementById('stokMax');
        const lokasiRakFilter = document.getElementById('lokasiRakFilter');
        const tableRows = document.querySelectorAll('tbody tr');

        // Populasi pilihan lokasi rak
        const lokasiRakOptions = new Set([...tableRows].map(row => row.querySelector('td:nth-child(5)').textContent.trim()));
        lokasiRakOptions.forEach(option => {
            const newOption = document.createElement('option');
            newOption.value = option;
            newOption.textContent = option;
            lokasiRakFilter.appendChild(newOption);
        });

        // Fungsi untuk filter tabel
        function filterTable() {
            const searchValue = searchBox.value.toLowerCase();
            const minStok = stokMin.value ? parseInt(stokMin.value) : null;
            const maxStok = stokMax.value ? parseInt(stokMax.value) : null;
            const selectedRak = lokasiRakFilter.value;

            tableRows.forEach(row => {
                const namaBarang = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const stok = parseInt(row.querySelector('td:nth-child(6)').textContent);
                const lokasiRak = row.querySelector('td:nth-child(5)').textContent;

                let isMatch = true;

                // Filter Nama Barang
                if (searchValue && !namaBarang.includes(searchValue)) {
                    isMatch = false;
                }

                // Filter Range Stok
                if (minStok !== null && stok < minStok) {
                    isMatch = false;
                }
                if (maxStok !== null && stok > maxStok) {
                    isMatch = false;
                }

                // Filter Lokasi Rak
                if (selectedRak && lokasiRak !== selectedRak) {
                    isMatch = false;
                }

                // Tampilkan atau sembunyikan baris
                row.style.display = isMatch ? '' : 'none';
            });
        }

        // Event listeners
        searchBox.addEventListener('input', filterTable);
        stokMin.addEventListener('input', filterTable);
        stokMax.addEventListener('input', filterTable);
        lokasiRakFilter.addEventListener('change', filterTable);
    });

    //export excel
    document.getElementById('exportGudangExcel').addEventListener('click', function (e) {
    e.preventDefault();

    const searchValue = document.getElementById('searchBox').value;
    const stokMin = document.getElementById('stokMin').value;
    const stokMax = document.getElementById('stokMax').value;
    const lokasiRak = document.getElementById('lokasiRakFilter').value;

    // Buat parameter filter
    const filter = {
        search: searchValue,
        stok_min: stokMin,
        stok_max: stokMax,
        lokasi_rak: lokasiRak
    };

    // Redirect ke URL dengan query string
    const queryParams = new URLSearchParams(filter).toString();
    window.location.href = `/export-gudang?${queryParams}`;
});

</script>
@endpush
