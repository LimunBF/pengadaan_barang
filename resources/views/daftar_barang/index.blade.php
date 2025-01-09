@extends('layouts.app')

@section('title', 'Daftar Barang')

@section('content')
    <div class="container">
        <h1>Daftar Barang</h1>

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

        <!-- Tabel Daftar Barang -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Barang</th>
                    <th>Nama Barang</th>
                    <th>Jenis Barang</th>
                    <th>Gambar</th>
                    <th>QR Code</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($barang as $item)
                    <tr id="row-{{ $item->id_barang }}" @if($item->kondisi == 'dihapus') class="text-muted" @endif>
                        <td>{{ $item->id_barang }}</td>
                        <td>
                            @if (session('edit_id') == $item->id_barang)
                                <form action="{{ route('barang.update', $item->id_barang) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="nama_barang" value="{{ $item->nama_barang }}" class="form-control" required>
                            @else
                                <span>{{ $item->nama_barang }}</span>
                            @endif
                        </td>
                        <td>
                            @if (session('edit_id') == $item->id_barang)
                                <input type="text" name="jenis_barang" value="{{ $item->jenis_barang }}" class="form-control" required>
                            @else
                                <span>{{ $item->jenis_barang }}</span>
                            @endif
                        </td>
                        <td>
                            @if (session('edit_id') == $item->id_barang)
                                <div>
                                    @if ($item->foto_barang)
                                        <img id="preview-{{ $item->id_barang }}" src="{{ $item->foto_barang }}" alt="Gambar Barang" width="100" class="mb-2" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#photoModal" data-bs-whatever="{{ asset($item->foto_barang) }}">
                                    @else
                                        <img id="preview-{{ $item->id_barang }}" src="#" alt="Gambar Barang" width="100" class="mb-2" style="display: none;">
                                    @endif
                                </div>
                                <input type="file" name="foto_barang" class="form-control" onchange="previewImage(this, 'preview-{{ $item->id_barang }}')">
                            @else
                                @if ($item->foto_barang)
                                    <img src="{{ $item->foto_barang }}?{{ time() }}" alt="Gambar Barang" width="100" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#photoModal" data-bs-whatever="{{ asset($item->foto_barang) }}">
                                @else
                                    Tidak ada gambar
                                @endif
                            @endif
                        </td>
                        <td>
                            @if ($item->qr_code_path)
                                <!-- Gambar QR Code -->
                                <img src="{{ asset($item->qr_code_path) }}" alt="QR Code" width="100" style="cursor: pointer;" 
                                    data-bs-toggle="modal" data-bs-target="#qrModal" 
                                    data-bs-id="{{ $item->id_barang }}" 
                                    data-bs-image="{{ asset($item->qr_code_path) }}">
                                
                                <!-- Tombol Download QR Code -->
                                @if ($item->kondisi != 'dihapus')
                                    <br>
                                    <a href="{{ route('barang.downloadQRCode', $item->id_barang) }}" class="btn btn-primary btn-sm mt-2">Download QR Code</a>
                                @endif
                            @else
                                Tidak ada QR Code
                            @endif
                        </td>                        
                        <td class="text-center">
                            <div class="btn-group-vertical w-100">
                                @if (session('edit_id') == $item->id_barang)
                                    <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                                    </form>
                                    <form action="{{ route('barang.cancel_edit', $item->id_barang) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm">Batal</button>
                                    </form>
                                @elseif($item->kondisi == 'dihapus')
                                    <button class="btn btn-light btn-sm w-100" disabled>Barang Dihapus</button>
                                @else
                                    <form action="{{ route('barang.edit_mode', $item->id_barang) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm w-100">Edit</button>
                                    </form>
                                    <form action="{{ route('barang.destroy', $item->id_barang) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?')">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
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
@endpush
