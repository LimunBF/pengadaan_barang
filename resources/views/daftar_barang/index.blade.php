@extends('layouts.app')

@section('title', 'Daftar Barang')

@section('content')
<div class="container">
    <h1>Daftar Barang</h1>

    <!-- Tombol untuk membuka modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#petugasModal">
        Kelola Petugas
    </button>

    <!-- Modal untuk Kelola Petugas -->
    <div class="modal fade" id="petugasModal" tabindex="-1" aria-labelledby="petugasModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="petugasModalLabel">Kelola Petugas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form Tambah Petugas -->
                    <form id="addPetugasForm" class="mb-4">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="namaPetugas" name="nama" placeholder="Masukkan Nama Petugas" required>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-success w-100">Tambah</button>
                            </div>
                        </div>
                    </form>

                    <!-- Tabel Petugas -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center text-wrap">Nama</th>
                                    <th class="text-center text-wrap" style="width: 20%;">Aksi</th>
                                </tr>                                
                            </thead>
                            <tbody id="petugasList">
                                <!-- Data petugas akan dimuat dengan fetch -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pesan Sukses -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <form method="GET" action="{{ route('barang.index') }}" class="mb-3">
        <label for="filter" class="form-label">Tampilkan Barang Berdasarkan Kondisi:</label>
        <select name="filter" id="filter" class="form-select" onchange="this.form.submit()">
            <option value="ada" {{ $filter == 'ada' ? 'selected' : '' }}>Ada</option>
            <option value="dihapus" {{ $filter == 'dihapus' ? 'selected' : '' }}>Dihapus</option>
        </select>
    </form>

    <div class="table-responsive">
        <!-- Tabel Daftar Barang -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="text-align: center;">ID Barang</th>
                    <th style="text-align: center;">Nama Barang</th>
                    <th style="text-align: center;">Jenis Barang</th>
                    <th style="text-align: center;">Gambar</th>
                    <th style="text-align: center;">QR Code</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if ($barang->isEmpty())
                    <tr>
                        <td colspan="8" style="text-align: center;">Tidak ada data barang</td>
                    </tr>
                @else
                    @foreach ($barang as $item)
                        <tr id="row-{{ $item->id_barang }}" @if($item->kondisi == 'dihapus') class="text-muted" @endif>
                            <td style="text-align: center; vertical-align: middle;">{{ $item->id_barang }}</td>
                            <td style="text-align: center; vertical-align: middle;">
                                @if (session('edit_id') == $item->id_barang)
                                    <form action="{{ route('barang.update', $item->id_barang) }}" method="POST"
                                        enctype="multipart/form-data" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="nama_barang" value="{{ $item->nama_barang }}"
                                            class="form-control form-control-sm text-center" placeholder="Nama Barang" required>
                                @else
                                    <span>{{ $item->nama_barang }}</span>
                                @endif
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                @if (session('edit_id') == $item->id_barang)
                                    <input type="text" name="jenis_barang" value="{{ $item->jenis_barang }}"
                                        class="form-control form-control-sm text-center" placeholder="Jenis Barang" required>
                                @else
                                    <span>{{ $item->jenis_barang }}</span>
                                @endif
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                @if (session('edit_id') == $item->id_barang)
                                    <div>
                                        <img id="preview-{{ $item->id_barang }}"
                                            src="{{ $item->foto_barang ? $item->foto_barang : '#' }}" alt="Gambar Barang"
                                            width="100" class="{{ $item->foto_barang ? 'mb-2' : 'd-none' }}"
                                            style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#photoModal"
                                            data-bs-whatever="{{ asset($item->foto_barang) }}">
                                    </div>
                                    <label for="upload-{{ $item->id_barang }}" class="btn btn-sm btn-primary mt-2">Ganti
                                        Gambar</label>
                                    <input id="upload-{{ $item->id_barang }}" type="file" name="foto_barang" class="d-none"
                                        onchange="previewImage(this, 'preview-{{ $item->id_barang }}')">
                                @else
                                    @if ($item->foto_barang)
                                        <img src="{{ $item->foto_barang }}?{{ time() }}" alt="Gambar Barang" width="140"
                                            style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#photoModal"
                                            data-bs-whatever="{{ asset($item->foto_barang) }}">
                                    @else
                                        <span>Tidak ada gambar</span>
                                    @endif
                                @endif
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                @if ($item->qr_code_path)
                                    <!-- Gambar QR Code -->
                                    <img src="{{ asset($item->qr_code_path) }}?{{ time() }}" alt="QR Code" width="100"
                                        style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#qrModal"
                                        data-bs-id="{{ $item->id_barang }}"
                                        data-bs-image="{{ asset($item->qr_code_path) }}?{{ time() }}">
                                    <!-- Tombol Download QR Code -->
                                    @if ($item->kondisi != 'dihapus')
                                        <br>
                                        <a href="{{ route('barang.downloadQRCode', $item->id_barang) }}"
                                            class="btn btn-primary btn-sm mt-2">Download QR Code</a>
                                    @endif
                                @else
                                    Tidak ada QR Code
                                @endif
                            </td>
                            <td style="align-items: center; vertical-align: middle;" class="text-center">
                                <div class="btn-group-vertical w-100">
                                    @if (session('edit_id') == $item->id_barang)
                                        <button type="submit" class="btn btn-success btn-sm uniform-btn">Simpan</button>
                                        </form>
                                        <form action="{{ route('barang.cancel_edit', $item->id_barang) }}" method="POST"
                                            style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary btn-sm uniform-btn">Batal</button>
                                        </form>
                                    @elseif($item->kondisi == 'dihapus')
                                        <button class="btn btn-light btn-sm uniform-btn" disabled>Barang Dihapus</button>
                                    @else
                                        <form action="{{ route('barang.edit_mode', $item->id_barang) }}" method="POST"
                                            style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm uniform-btn">Edit</button>
                                        </form>
                                        <form action="{{ route('barang.destroy', $item->id_barang) }}" method="POST"
                                            style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm uniform-btn"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?')">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- Modal untuk menampilkan gambar QR Code -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrModalLabel">QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex flex-column align-items-center">
                    <!-- Gambar QR Code -->
                    <img id="qrModalImage" src="" class="img-fluid mb-3" alt="QR Code">
                    <!-- Tombol Download QR Code -->
                    <a id="qrDownloadButton" href="#" class="btn btn-primary">Download QR Code</a>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal untuk menampilkan gambar Barang -->
    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="photoModalLabel">Foto Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex justify-content-center align-items-center">
                    <img id="photoModalImage" src="" class="img-fluid" alt="Foto Barang">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = "block";
                };

                reader.readAsDataURL(input.files[0]); // Konversi file ke Data URL
            } else {
                preview.src = "#";
                preview.style.display = "none";
            }
        }
        // Menangani klik pada gambar QR Code dan foto barang
        const qrModal = document.getElementById('qrModal');
        qrModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Tombol yang memicu modal
            const imageUrl = button.getAttribute('data-bs-image'); // URL gambar QR Code
            const itemId = button.getAttribute('data-bs-id'); // ID barang

            const modalImage = qrModal.querySelector('#qrModalImage');
            const downloadButton = qrModal.querySelector('#qrDownloadButton');

            // Update gambar di modal
            modalImage.src = imageUrl;

            // Update URL tombol download menggunakan route Laravel
            downloadButton.href = `/barang/${itemId}/download-qr`;
        });

        const photoModal = document.getElementById('photoModal');
        photoModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Tombol yang memicu modal
            const imageUrl = button.getAttribute('data-bs-whatever'); // Ambil URL gambar dari atribut data-bs-whatever
            const modalImage = photoModal.querySelector('.modal-body img');
            modalImage.src = imageUrl; // Set gambar ke modal
        });
    </script> 
    <script>
        const petugasList = document.getElementById("petugasList");
        const fetchHeaders = {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json"
        };

        // Fungsi untuk memuat data petugas
        const loadPetugas = () => {
            fetch("{{ route('petugas.index') }}")
                .then(response => response.json())
                .then(data => {
                    petugasList.innerHTML = data.map(petugas => renderRow(petugas)).join('');
                })
                .catch(() => alert("Gagal memuat data petugas."));
        };

        // Fungsi untuk menampilkan baris petugas
        const renderRow = petugas => `
            <tr id="petugas-${petugas.id}">
                <td>
                    <input type="text" value="${petugas.nama}" class="form-control form-control-sm"
                        onchange="updatePetugas(${petugas.id}, this.value)">
                </td>
                <td class="text-center">
                    <button class="btn btn-danger btn-sm" onclick="deletePetugas(${petugas.id})">Hapus</button>
                </td>
            </tr>
        `;

        // Fungsi untuk menambah petugas
        document.getElementById("addPetugasForm").addEventListener("submit", e => {
            e.preventDefault();
            const nama = document.getElementById("namaPetugas").value;
            fetch("{{ route('petugas.store') }}", {
                method: "POST",
                headers: fetchHeaders,
                body: JSON.stringify({ nama })
            })
            .then(response => response.json())
            .then(data => {
                petugasList.innerHTML += renderRow(data.data);
                document.getElementById("namaPetugas").value = "";
                alert(data.success);
            })
            .catch(() => alert("Gagal menambahkan petugas."));
        });

        // Fungsi untuk mengupdate petugas
        const updatePetugas = (id, nama) => {
            fetch(`/petugas/${id}`, {
                method: "PUT",
                headers: fetchHeaders,
                body: JSON.stringify({ nama })
            })
            .then(response => response.json())
            .then(data => alert(data.success))
            .catch(() => alert("Gagal memperbarui petugas."));
        };

        // Fungsi untuk menghapus petugas
        const deletePetugas = id => {
            if (confirm("Apakah Anda yakin ingin menghapus petugas ini?")) {
                fetch(`/petugas/${id}`, {
                    method: "DELETE",
                    headers: fetchHeaders
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById(`petugas-${id}`).remove();
                    alert(data.success);
                })
                .catch(() => alert("Gagal menghapus petugas."));
            }
        };

        // Panggil fungsi loadPetugas saat modal ditampilkan
        document.getElementById("petugasModal").addEventListener("show.bs.modal", loadPetugas);
    </script>
    <style>
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Mencegah tabel merusak tata letak di perangkat kecil */
        .table {
            min-width: 600px;
            /* Atur sesuai dengan kebutuhan */
        }

        /* Membatasi gambar agar tidak terlalu besar */
        img {
            max-width: 100%;
            height: auto;
        }
    </style>
@endpush