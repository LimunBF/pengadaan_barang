@extends('layouts.app')

@section('title', 'Data Gudang')

@section('content')
<h1 class="mb-4">Data Gudang</h1>

<a href="#" id="exportGudangExcel" class="btn btn-success mb-5">Export ke Excel</a>

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
        <div class="col-lg-4 col-md-6">
            <label for="searchBox" class="form-label">Cari Nama Barang:</label>
            <input type="text" id="searchBox" class="form-control" placeholder="Ketik nama barang...">
        </div>

        <div class="col-lg-4 col-md-6">
            <label for="stokRange" class="form-label">Filter Stok:</label>
            <div class="d-flex">
                <input type="number" id="stokMin" class="form-control me-2" placeholder="Min">
                <input type="number" id="stokMax" class="form-control" placeholder="Max">
            </div>
        </div>

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
                <td class="tengah-kolom" data-bs-toggle="tooltip" title="{{ $barang->id_barang }}">{{ $barang->id_barang }}</td>
                <td class="tengah-kolom" data-bs-toggle="tooltip" title="{{ $barang->nama_barang }}">{{ Str::limit($barang->nama_barang, 20) }}</td>
                <td class="tengah-kolom">
                    <span data-bs-toggle="tooltip" title="{{ $barang->jenis_barang }}">
                        {{ Str::limit($barang->jenis_barang, 15) }}
                    </span>
                </td>
                
                <td class="tengah">
                    @if ($barang->barang && $barang->barang->foto_barang)
                    <img src="{{ asset('storage/' . $barang->barang->foto_barang) }}?{{ time() }}"
                        alt="Foto {{ $barang->nama_barang }}" 
                        style="width: 100px; height: auto; cursor: pointer;" 
                        onclick="openImageModal('{{ asset('storage/' . $barang->barang->foto_barang) }}?{{ time() }}')">
                    @else
                    <span>Tidak ada foto</span>
                    @endif
                </td>                
                
                <td class="tengah-kolom" data-bs-toggle="tooltip" title="{{ $barang->lokasi_rak }}">{{ Str::limit($barang->lokasi_rak, 15) }}</td>
                <td class="tengah-kolom" data-bs-toggle="tooltip" title="{{ $barang->stok }}">{{ $barang->stok }}</td>
                <td class="tengah-kolom" data-bs-toggle="tooltip" title="{{ $barang->satuan }}">{{ $barang->satuan }}</td>
                
                <td class="tengah-kolom">
                <button class="btn btn-sm btn-primary" 
                    onclick="openEditModal('{{ $barang->id_barang }}', '{{ $barang->nama_barang }}', '{{ $barang->lokasi_rak }}', {{ $barang->stok }}, '{{ $barang->satuan }}')" 
                    title="Edit">
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
    <div class="d-flex justify-content-center mt-4">
        {{ $gudangs->links('pagination::bootstrap-5') }}
    </div>
</div>

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

<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Foto Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="imageModalContent" src="" class="img-fluid" alt="Foto Barang">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let modalInstance;

    // Fungsi Open Edit Modal
    function openEditModal(id_barang, nama_barang, lokasi_rak, stok, satuan) {
        if (modalInstance) {
            modalInstance.hide();
        }

        // Isi data ke Form
        document.getElementById('id_barang').value = id_barang;
        document.getElementById('nama_barang_display').textContent = nama_barang;
        document.getElementById('lokasi_rak').value = lokasi_rak;
        document.getElementById('stok').value = stok;
        document.getElementById('satuan').value = satuan;

        // Set URL Action untuk Update (Route: /gudang/{id})
        document.getElementById('editForm').action = `/gudang/${id_barang}`;
        
        modalInstance = new bootstrap.Modal(document.getElementById('editModal'));
        modalInstance.show();
    }

    // Tooltip Init
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });

    // Fungsi Open Image Modal
    function openImageModal(imageUrl) {
        document.getElementById('imageModalContent').src = imageUrl;
        const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
        imageModal.show();
    }

    // Script Filter Tabel (Javascript Murni)
    document.addEventListener('DOMContentLoaded', function () {
        const searchBox = document.getElementById('searchBox');
        const stokMin = document.getElementById('stokMin');
        const stokMax = document.getElementById('stokMax');
        const lokasiRakFilter = document.getElementById('lokasiRakFilter');
        const tableRows = document.querySelectorAll('tbody tr');

        // Populasi Dropdown Lokasi Rak
        const lokasiRakOptions = new Set();
        tableRows.forEach(row => {
            // Cek apakah row bukan "Tidak ada data"
            const cell = row.querySelector('td:nth-child(5)');
            if(cell) lokasiRakOptions.add(cell.textContent.trim());
        });
        
        lokasiRakOptions.forEach(option => {
            if(option) {
                const newOption = document.createElement('option');
                newOption.value = option;
                newOption.textContent = option;
                lokasiRakFilter.appendChild(newOption);
            }
        });

        function filterTable() {
            const searchValue = searchBox.value.toLowerCase();
            const minStok = stokMin.value ? parseInt(stokMin.value) : null;
            const maxStok = stokMax.value ? parseInt(stokMax.value) : null;
            const selectedRak = lokasiRakFilter.value;

            tableRows.forEach(row => {
                // Skip jika baris "tidak ada data"
                if(row.cells.length < 2) return;

                const namaBarang = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const stok = parseInt(row.querySelector('td:nth-child(6)').textContent);
                const lokasiRak = row.querySelector('td:nth-child(5)').textContent;

                let isMatch = true;

                if (searchValue && !namaBarang.includes(searchValue)) isMatch = false;
                if (minStok !== null && stok < minStok) isMatch = false;
                if (maxStok !== null && stok > maxStok) isMatch = false;
                if (selectedRak && lokasiRak !== selectedRak) isMatch = false;

                row.style.display = isMatch ? '' : 'none';
            });
        }

        searchBox.addEventListener('input', filterTable);
        stokMin.addEventListener('input', filterTable);
        stokMax.addEventListener('input', filterTable);
        lokasiRakFilter.addEventListener('change', filterTable);
    });

    // Export Excel Script
    document.getElementById('exportGudangExcel').addEventListener('click', function (e) {
        e.preventDefault();
        const filter = {
            search: document.getElementById('searchBox').value,
            stok_min: document.getElementById('stokMin').value,
            stok_max: document.getElementById('stokMax').value,
            lokasi_rak: document.getElementById('lokasiRakFilter').value
        };
        const queryParams = new URLSearchParams(filter).toString();
        window.location.href = `/export-gudang?${queryParams}`;
    });
</script>
@endpush