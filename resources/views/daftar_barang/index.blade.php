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
                    <th>Deskripsi Barang</th>
                    <th>Gambar</th>
                    <th>QR Code</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($barang as $item)
                    <tr id="row-{{ $item->id_barang }}">
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
                                <input type="text" name="deskripsi_barang" value="{{ $item->deskripsi_barang }}" class="form-control">
                            @else
                                <span>{{ $item->deskripsi_barang }}</span>
                            @endif
                        </td>
                        <td>
                            @if (session('edit_id') == $item->id_barang)
                                <div>
                                    @if ($item->foto_barang)
                                        <img id="preview-{{ $item->id_barang }}" src="{{ $item->foto_barang }}" alt="Gambar Barang" width="100" class="mb-2">
                                    @else
                                        <img id="preview-{{ $item->id_barang }}" src="#" alt="Gambar Barang" width="100" class="mb-2" style="display: none;">
                                    @endif
                                </div>
                                <input type="file" name="foto_barang" class="form-control" onchange="previewImage(this, 'preview-{{ $item->id_barang }}')">
                            @else
                                @if ($item->foto_barang)
                                <img src="{{ $item->foto_barang }}?{{ time() }}" alt="Gambar Barang" width="100">
                                @else
                                    Tidak ada gambar
                                @endif
                            @endif
                        </td>
                        <td>
                            @if ($item->qr_code_path)
                                <img src="{{ asset($item->qr_code_path) }}" alt="QR Code" width="100">
                                <br>
                                <a href="{{ route('barang.downloadQRCode', $item->id_barang) }}" class="btn btn-primary btn-sm mt-2">Download QR Code</a>
                            @else
                                Tidak ada QR Code
                            @endif
                        </td>
                        <td>
                            @if (session('edit_id') == $item->id_barang)
                                <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                                </form>
                                <form action="{{ route('barang.cancel_edit', $item->id_barang) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary btn-sm">Batal</button>
                                </form>
                            @else
                                <form action="{{ route('barang.edit_mode', $item->id_barang) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm">Edit</button>
                                </form>
                                <form action="{{ route('barang.destroy', $item->id_barang) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?')">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>        
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
</script>
@endpush
