@extends('layouts.app')

@section('title', 'Daftar Transaksi')

@section('content')

<div class="container">
    <h1>Daftar Transaksi</h1>
    <a id="exportExcel" class="btn btn-success mb-3">Export to Excel</a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row mb-4">
        <div class="col-md-4">
            <label for="filterTanggalMulai">Rentang Waktu</label>
            <div class="d-flex">
                <input 
                    type="date" 
                    id="filterTanggalMulai" 
                    class="form-control" 
                    placeholder="Mulai"
                    value="{{ $tanggalPertama }}"
                    min="{{ $tanggalPertama }}" 
                    max="{{ $tanggalTerakhir }}">
                <input 
                    type="date" 
                    id="filterTanggalAkhir" 
                    class="form-control" 
                    placeholder="Akhir"
                    value="{{ $tanggalTerakhir }}"
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
        <div class="col-md-2">
            <label for="filterJenisTransaksi">Jenis Transaksi</label>
            <select id="filterJenisTransaksi" class="form-control">
                <option value="">Semua</option>
                <option value="masuk">Masuk</option>
                <option value="keluar">Keluar</option>
            </select>
        </div>    
    </div>
    
    <div class="table-responsive">
        <table class="table table-bordered" id="transaksiTable">
            <thead>
                <tr>
                    <th class="d-none">ID</th> 
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
                        <td class="d-none">{{ $t->id_transaksi }}</td>
                        <td class="tengah-kolom">{{ date('Y-m-d H:i:s', strtotime($t->waktu)) }}</td>
                        <td class="tengah-kolom">{{ $t->id_barang }} | {{ $t->barang->nama_barang ?? 'Tidak Ditemukan' }}</td>
                        <td class="tengah-kolom">{{ $t->tipe_transaksi }}</td>
                        <td class="tengah-kolom">{{ $t->kuantitas }}</td>
                        <td class="tengah-kolom">{{ $t->nama_pengirim_penerima }}</td>
                        <td class="tengah-kolom">{{ $t->catatan }}</td>
                        <td class="tengah-kolom">
                            @if ($t->photo)
                                {{-- PERBAIKAN LOGIKA GAMBAR DISINI --}}
                                @php
                                    // Cek apakah ini path baru (relative) atau URL lama
                                    $imgSrc = \Illuminate\Support\Str::startsWith($t->photo, 'http') ? $t->photo : asset('storage/' . $t->photo);
                                @endphp
                                <img src="{{ $imgSrc }}" alt="Foto Bukti" style="max-width: 100px; max-height: 100px; cursor: pointer;"
                                    data-bs-toggle="modal" data-bs-target="#imageModal" onclick="showImageModal('{{ $imgSrc }}')">
                            @else
                                <img src="{{ asset('images/placeholder.png') }}" alt="Tidak ada foto" style="max-width: 100px; max-height: 100px;">
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

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

@push('scripts')
<script>
    function showImageModal(imageUrl) {
        document.getElementById('modalImage').src = imageUrl;
    }

    document.getElementById('exportExcel').addEventListener('click', function () {
        const rows = Array.from(document.querySelectorAll('#transaksiTable tbody tr'));
        const filteredData = [];
        let filterApplied = false;

        // Cek input filter
        const searchValue = document.getElementById('searchbox').value.trim();
        const jenisValue = document.getElementById('filterJenisTransaksi').value.trim();
        const namaValue = document.getElementById('filterNamaPengirim').value.trim();
        const tanggalMulai = document.getElementById('filterTanggalMulai').value.trim();
        const tanggalAkhir = document.getElementById('filterTanggalAkhir').value.trim();

        if (searchValue || jenisValue || namaValue || tanggalMulai || tanggalAkhir) {
            filterApplied = true;
        }

        // Logic pengambilan data (sesuai index kolom tabel yang baru)
        // Index: 0=HiddenID, 1=Waktu, 2=Barang, 3=Jenis, 4=Jml, 5=Petugas, 6=Catatan, 7=Foto
        if (filterApplied) {
            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    filteredData.push({
                        id_transaksi: row.cells[0].textContent.trim(),
                        waktu: row.cells[1].textContent.trim(),
                        id_barang: row.cells[2].textContent.split('|')[0].trim(),
                        nama_barang: row.cells[2].textContent.split('|')[1]?.trim() || '',
                        tipe_transaksi: row.cells[3].textContent.trim(),
                        kuantitas: row.cells[4].textContent.trim(),
                        nama_pengirim_penerima: row.cells[5].textContent.trim(),
                        catatan: row.cells[6].textContent.trim(),
                        photo: row.cells[7].querySelector('img')?.src || 'Tidak Ada Foto'
                    });
                }
            });
        }

        // Kirim ke server
        fetch('{{ route('transaksi.export') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                filteredData: filterApplied ? filteredData : [],
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

    // Logic Filter Tabel Client-Side
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
            const jenisValue = filterJenisTransaksi.value.toLowerCase(); // Penting: lowercase agar cocok
            const namaValue = filterNamaPengirim.value.toLowerCase();
            
            // Konversi tanggal (set jam ke 00:00:00 agar perbandingan akurat)
            const tanggalMulai = filterTanggalMulai.value ? new Date(filterTanggalMulai.value) : null;
            if(tanggalMulai) tanggalMulai.setHours(0,0,0,0);

            const tanggalAkhir = filterTanggalAkhir.value ? new Date(filterTanggalAkhir.value) : null;
            if(tanggalAkhir) tanggalAkhir.setHours(23,59,59,999);

            rows.forEach(row => {
                const idTransaksi = row.cells[0].textContent.toLowerCase();
                const waktuText = row.cells[1].textContent; 
                const waktu = new Date(waktuText); // Javascript auto parse YYYY-MM-DD HH:MM:SS

                const idBarangNamaBarang = row.cells[2].textContent.toLowerCase();
                const jenisTransaksi = row.cells[3].textContent.toLowerCase();
                const kuantitas = row.cells[4].textContent.toLowerCase();
                const namaPengirim = row.cells[5].textContent.toLowerCase();
                const catatan = row.cells[6].textContent.toLowerCase();

                let showRow = true;

                if (searchValue && 
                    !idTransaksi.includes(searchValue) && 
                    !idBarangNamaBarang.includes(searchValue) && 
                    !kuantitas.includes(searchValue) && 
                    !catatan.includes(searchValue)) {
                    showRow = false;
                }

                if (jenisValue && jenisTransaksi !== jenisValue) {
                    showRow = false;
                }

                if (namaValue && !namaPengirim.includes(namaValue)) {
                    showRow = false;
                }

                if (tanggalMulai && waktu < tanggalMulai) {
                    showRow = false;
                }
                if (tanggalAkhir && waktu > tanggalAkhir) {
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
@endpush
@endsection