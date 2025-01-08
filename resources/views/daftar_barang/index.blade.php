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

        <!-- Tabel Daftar Barang -->
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
                    <tr id="row-{{ $item->id_barang }}">
                        <td>{{ $item->id_barang }}</td>
                        <td>
                            @if (session('edit_id') == $item->id_barang)
                                <form action="{{ route('barang.update', $item->id_barang) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="nama_barang" value="{{ $item->nama_barang }}"
                                        class="form-control" required>
                                @else
                                    <span>{{ $item->nama_barang }}</span>
                            @endif
                        </td>
                        <td>
                            @if (session('edit_id') == $item->id_barang)
                                <input type="text" name="jenis_barang" value="{{ $item->jenis_barang }}"
                                    class="form-control" required>
                            @else
                                <span>{{ $item->jenis_barang }}</span>
                            @endif
                        </td>
                        <td>
                            @if (session('edit_id') == $item->id_barang)
                                <input type="text" name="deskripsi_barang" value="{{ $item->deskripsi_barang }}"
                                    class="form-control">
                            @else
                                <span>{{ $item->deskripsi_barang }}</span>
                            @endif
                        </td>
                        <td>
                            @if ($item->qr_code_path)
                                <img src="{{ asset($item->qr_code_path) }}" alt="QR Code" width="100">
                                <br>
                                <a href="{{ route('barang.downloadQRCode', $item->id_barang) }}"
                                    class="btn btn-primary btn-sm mt-2">Download QR Code</a>
                            @else
                                Tidak ada QR Code
                            @endif
                        </td>
                        <td>
                            @if (session('edit_id') == $item->id_barang)
                                <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                                </form>
                                <form action="{{ route('barang.cancel_edit', $item->id_barang) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary btn-sm">Batal</button>
                                </form>
                            @else
                                <form action="{{ route('barang.edit_mode', $item->id_barang) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm">Edit</button>
                                </form>
                                <form action="{{ route('barang.destroy', $item->id_barang) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?')">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
