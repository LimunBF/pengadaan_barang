@extends('layouts.app')

@section('title', 'Daftar Transaksi')

@section('content')
<div class="container">
    <h2>Daftar Transaksi</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-md-3">
            <label for="searchbox">Search</label>
            <input type="text" id="searchbox" class="form-control" placeholder="Search...">
        </div>
        <div class="col-md-3">
            <label for="filterJenisTransaksi">Jenis Transaksi</label>
            <select id="filterJenisTransaksi" class="form-control">
                <option value="">Semua</option>
                <option value="masuk">Masuk</option>
                <option value="keluar">Keluar</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="filterNamaPengirim">Nama Pengirim/Penerima</label>
            <input type="text" id="filterNamaPengirim" class="form-control" placeholder="Cari nama pengirim/penerima">
        </div>
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
    </div>

    <!-- Table Section -->
    <table class="table table-bordered" id="transaksiTable">
        <thead>
            <tr>
                <th>ID Transaksi</th>
                <th>Waktu</th>
                <th>Id Barang | Nama Barang</th>
                <th>Jenis Transaksi</th>
                <th>Kuantitas</th>
                <th>Nama Pengirim / Penerima</th>
                <th>Catatan</th>
                <th>Photo</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksi as $t)
                <tr>
                    <td>{{ $t->id_transaksi }}</td>
                    <td>{{ $t->waktu }}</td>
                    <td>{{ $t->id_barang }} | {{ $t->barang->nama_barang ?? 'Tidak Ditemukan' }}</td>
                    <td>{{ $t->tipe_transaksi }}</td>
                    <td>{{ $t->kuantitas }}</td>
                    <td>{{ $t->nama_pengirim_penerima }}</td>
                    <td>{{ $t->catatan }}</td>
                    <td>{{ $t->photo }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
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
                const jenisTransaksi = row.cells[3].textContent.toLowerCase();
                const namaPengirim = row.cells[5].textContent.toLowerCase();

                let showRow = true;

                // Filter berdasarkan searchbox
                if (searchValue && !idTransaksi.includes(searchValue) && !namaPengirim.includes(searchValue)) {
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
