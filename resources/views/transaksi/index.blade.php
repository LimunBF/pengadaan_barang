@extends('layouts.app')

@section('title', 'Daftar Transaksi')

@section('content')


<div class="container">
    <h2>Daftar Transaksi</h2>
    <button id="exportExcel" class="btn btn-success mb-3">Export to Excel</button>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-md-3">
            <label for="filterTanggalMulai">Rentang Waktu</label>
            <div class="d-flex">
                <input 
                    type="date" 
                    id="filterTanggalMulai" 
                    class="form-control" 
                    placeholder="Mulai"
                    min="{{ $tanggalPertama }}" 
                    max="{{ $tanggalTerakhir }}">
                <input 
                    type="date" 
                    id="filterTanggalAkhir" 
                    class="form-control ms-2" 
                    placeholder="Akhir"
                    min="{{ $tanggalPertama }}" 
                    max="{{ $tanggalTerakhir }}">
            </div>
        </div>   
        <div class="col-md-3">
            <label for="searchbox">Search</label>
            <input type="text" id="searchbox" class="form-control" placeholder="Search...">
        </div>
        <div class="col-md-3">
            <label for="filterNamaPengirim">Nama Pengirim/Penerima</label>
            <input type="text" id="filterNamaPengirim" class="form-control" placeholder="Cari nama pengirim/penerima">
        </div> 
        <div class="col-md-3">
            <label for="filterJenisTransaksi">Jenis Transaksi</label>
            <select id="filterJenisTransaksi" class="form-control">
                <option value="">Semua</option>
                <option value="masuk">Masuk</option>
                <option value="keluar">Keluar</option>
            </select>
        </div>    
    </div>
    
    <!-- Table Section -->
<div class="table-responsive">
    <table class="table table-bordered" id="transaksiTable">
        <thead>
            <tr>
                <th class="tengah">ID</th>
                <th class="tengah">Waktu</th>
                <th class="tengah">Id Barang | Nama Barang</th>
                <th class="tengah">Jenis Transaksi</th>
                <th class="tengah">Kuantitas</th>
                <th class="tengah">Nama Petugas</th>
                <th class="tengah">Catatan</th>
                <th class="tengah">Bukti Foto</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksi as $t)
                <tr>
                    <td class="tengah-kolom">{{ $t->id_transaksi }}</td>
                    <td class="tengah-kolom">{{ $t->waktu }}</td>
                    <td class="tengah-kolom">{{ $t->id_barang }} | {{ $t->barang->nama_barang ?? 'Tidak Ditemukan' }}</td>
                    <td class="tengah-kolom">{{ $t->tipe_transaksi }}</td>
                    <td class="tengah-kolom">{{ $t->kuantitas }}</td>
                    <td class="tengah-kolom">{{ $t->nama_pengirim_penerima }}</td>
                    <td class="tengah-kolom">{{ $t->catatan }}</td>
                    <td class="tengah-kolom">
                        @if ($t->photo)
                            <img src="{{ $t->photo }}" alt="Foto Bukti" style="max-width: 100px; max-height: 100px; cursor: pointer;"
                                data-bs-toggle="modal" data-bs-target="#imageModal" onclick="showImageModal('{{ $t->photo }}')">
                        @else
                            <img src="{{ asset('images/placeholder.png') }}" alt="Tidak ada foto" style="max-width: 100px; max-height: 100px;">
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Foto Bukti</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Foto Bukti" class="img-fluid">
            </div>
        </div>
    </div>
</div>
</div>
<script>
    function showImageModal(imageUrl) {
        const modalImage = document.getElementById('modalImage');
        modalImage.src = imageUrl;
    }

    document.getElementById('exportExcel').addEventListener('click', function () {
        const rows = Array.from(document.querySelectorAll('#transaksiTable tbody tr'));
        const filteredData = [];
        let filterApplied = false;

        // Cek apakah ada filter yang diterapkan
        const searchValue = document.getElementById('searchbox').value.trim();
        const jenisValue = document.getElementById('filterJenisTransaksi').value.trim();
        const namaValue = document.getElementById('filterNamaPengirim').value.trim();
        const tanggalMulai = document.getElementById('filterTanggalMulai').value.trim();
        const tanggalAkhir = document.getElementById('filterTanggalAkhir').value.trim();

        // Jika salah satu filter diisi, tandai filterApplied sebagai true
        if (searchValue || jenisValue || namaValue || tanggalMulai || tanggalAkhir) {
            filterApplied = true;
        }

        // Ambil data yang terlihat (hanya jika ada filter)
        if (filterApplied) {
            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    filteredData.push({
                        id_transaksi: row.cells[0].textContent.trim(),
                        waktu: row.cells[1].textContent.trim(),
                        id_barang_nama_barang: row.cells[2].textContent.trim(),
                        tipe_transaksi: row.cells[3].textContent.trim(),
                        kuantitas: row.cells[4].textContent.trim(),
                        nama_pengirim_penerima: row.cells[5].textContent.trim(),
                        catatan: row.cells[6].textContent.trim(),
                        photo: row.cells[7].querySelector('img')?.src || '',
                    });
                }
            });
        }

        // Kirimkan data filter ke server
        fetch('{{ route('transaksi.export') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                filteredData: filterApplied ? filteredData : [], // Kirim data kosong jika tidak ada filter
            }),
        })
            .then(response => {
                if (!response.ok) throw new Error('Gagal mengekspor data.');
                return response.blob();
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'transaksi.xlsx';
                a.click();
                window.URL.revokeObjectURL(url);
            })
            .catch(error => console.error('Error:', error));
    });


    document.addEventListener('DOMContentLoaded', function () {
        const searchbox = document.getElementById('searchbox');
        const filterJenisTransaksi = document.getElementById('filterJenisTransaksi');
        const filterNamaPengirim = document.getElementById('filterNamaPengirim');
        const filterTanggalMulai = document.getElementById('filterTanggalMulai');
        const filterTanggalAkhir = document.getElementById('filterTanggalAkhir');
        const table = document.getElementById('transaksiTable');
        const rows = Array.from(table.tBodies[0].rows);

        function filterTable() {
            const searchValue = searchbox.value.toLowerCase();
            const jenisValue = filterJenisTransaksi.value;
            const namaValue = filterNamaPengirim.value.toLowerCase();
            const tanggalMulai = new Date(filterTanggalMulai.value);
            const tanggalAkhir = new Date(filterTanggalAkhir.value);

            rows.forEach(row => {
                const idTransaksi = row.cells[0].textContent.toLowerCase();
                const waktu = new Date(row.cells[1].textContent);
                const idBarangNamaBarang = row.cells[2].textContent.toLowerCase();
                const jenisTransaksi = row.cells[3].textContent.toLowerCase();
                const kuantitas = row.cells[4].textContent.toLowerCase();
                const namaPengirim = row.cells[5].textContent.toLowerCase();
                const catatan = row.cells[6].textContent.toLowerCase();

                let showRow = true;

                // Filter berdasarkan searchbox
                if (
                    searchValue &&
                    !idTransaksi.includes(searchValue) &&
                    !idBarangNamaBarang.includes(searchValue) &&
                    !kuantitas.includes(searchValue) &&
                    !catatan.includes(searchValue)
                ) {
                    showRow = false;
                }

                // Filter berdasarkan jenis transaksi
                if (jenisValue && jenisTransaksi !== jenisValue) {
                    showRow = false;
                }

                // Filter berdasarkan nama pengirim/penerima
                if (namaValue && !namaPengirim.includes(namaValue)) {
                    showRow = false;
                }

                // Filter berdasarkan rentang waktu
                if (filterTanggalMulai.value && waktu < tanggalMulai) {
                    showRow = false;
                }
                if (filterTanggalAkhir.value && waktu > tanggalAkhir) {
                    showRow = false;
                }

                row.style.display = showRow ? '' : 'none';
            });
        }

        searchbox.addEventListener('input', filterTable);
        filterJenisTransaksi.addEventListener('change', filterTable);
        filterNamaPengirim.addEventListener('input', filterTable);
        filterTanggalMulai.addEventListener('change', filterTable);
        filterTanggalAkhir.addEventListener('change', filterTable);
    });
</script>
@endsection
