<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Aplikasi Pengelolaan Gudang Diskominfo Madiun">
    <meta name="author" content="Your Name">
    <title>@yield('title') - Pengelolaan Gudang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
       html, body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: #ffffff;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh; /* Memastikan body memiliki tinggi minimal layar penuh */ /* Pastikan body dan html memiliki tinggi 100% */
            display: flex; /* Gunakan Flexbox */
            flex-direction: column; 
        }
        .content-wrapper {
            background: #ffffff;
            color: #000000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            padding: 30px;
            margin-top: 30px;
            margin-bottom: 30px;
            min-height: calc(110vh - 230px);
        }
        .navbar {
            background: rgba(0, 0, 0, 0.8);
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.7rem;
            color: #ffffff;
        }
        .navbar-brand:hover {
            color: #2673FB;
        }
        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8);
        }
        .navbar-nav .nav-link:hover {
            color: #ffffff;
        }
        .content-wrapper {
            background: #ffffff;
            color: #cf1515;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            padding: 30px;
            margin-top: 30px;
            margin-bottom: 30px;
            flex: 1;
        }
        footer {
    background: rgba(0, 0, 0, 0.8);
    color: #ffffff;
    text-align: center;
    padding: 15px 0;
    width: 100%;
}
        footer p {
            margin: 0;
        }
        .table-secondary {
            background-color: #f0f0f0 !important;
            color: #6c757d;
        }
        .table-secondary .btn {
            pointer-events: none;
            opacity: 0.5;
        }
        .btn-large {
            font-size: 1.5rem;
            padding: 20px 40px;
            width: 100%;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">Pengelolaan Gudang</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('barang.index') }}">Daftar Barang</a>
                    </li>                    
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('gudang.index') }}">Gudang</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Transaksi
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('transaksi.index') }}">Masuk</a>
                            <a class="dropdown-item" href="{{ route('transaksi.keluar') }}">Keluar</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container content-wrapper">
        @yield('content')
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2025 Diskominfo Madiun. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
    @stack('scripts')
</body>
</html>
    