@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Daftar Barang</h1>

        <!-- Pesan Sukses -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Barang</th>
                    <th>Nama Barang</th>
                    <th>Jenis Barang</th>
                    <th>Deskripsi Barang</th>
                    <th>QR Code</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($barang as $item)
                    <tr>
                        <td>{{ $item->id_barang }}</td>
                        <td>
                            <span class="nama_barang_display">{{ $item->nama_barang }}</span>
                            <form action="{{ route('barang.update', $item->id_barang) }}" method="POST" class="d-inline nama_barang_edit_form" style="display: none;">
                                @csrf
                                @method('PUT')
                                <input type="text" name="nama_barang" value="{{ $item->nama_barang }}" class="form-control" required>
                            </form>
                        </td>
                        <td>
                            <span class="jenis_barang_display">{{ $item->jenis_barang }}</span>
                            <input type="text" name="jenis_barang" value="{{ $item->jenis_barang }}" class="form-control jenis_barang_edit_form" style="display: none;" required>
                        </td>
                        <td>
                            <span class="deskripsi_barang_display">{{ $item->deskripsi_barang }}</span>
                            <input type="text" name="deskripsi_barang" value="{{ $item->deskripsi_barang }}" class="form-control deskripsi_barang_edit_form" style="display: none;">
                        </td>
                        <td>
                            @if ($item->qr_code_path)
                                <img src="{{ asset($item->qr_code_path) }}" alt="QR Code" width="100">
                                <br>
                                <a href="{{ route('barang.downloadQRCode', $item->id_barang) }}" class="btn btn-primary btn-sm mt-2">
                                    Download QR Code
                                </a>
                            @else
                                Tidak ada QR Code
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm btn-edit" onclick="enableEdit(this)">Edit</button>
                            <button type="submit" class="btn btn-success btn-sm nama_barang_edit_form" style="display: none;">Simpan</button>
                            </form>
                            <form action="{{ route('barang.destroy', $item->id_barang) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        function enableEdit(button) {
            const row = button.closest('tr');
            // Sembunyikan tampilan statis
            row.querySelectorAll('.nama_barang_display, .jenis_barang_display, .deskripsi_barang_display').forEach(el => {
                el.style.display = 'none';
            });
            // Tampilkan form edit
            row.querySelectorAll('.nama_barang_edit_form, .jenis_barang_edit_form, .deskripsi_barang_edit_form').forEach(el => {
                el.style.display = 'block';
            });
        }
    </script>
@endsection
