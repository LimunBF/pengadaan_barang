<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .detail-container {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        .detail-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="detail-container">
            <h2 class="detail-title text-center">Detail Barang</h2>
            <table class="table table-bordered mt-3">
                <tr>
                    <th>ID Barang</th>
                    <td>{{ $barang->id_barang }}</td>
                </tr>
                <tr>
                    <th>Nama Barang</th>
                    <td>{{ $barang->nama_barang }}</td>
                </tr>
                <tr>
                    <th>Jenis Barang</th>
                    <td>{{ $barang->jenis_barang }}</td>
                </tr>
                <tr>
                    <th>Deskripsi Barang</th>
                    <td>{{ $barang->deskripsi_barang }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
