<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Aplikasi Pengelolaan Gudang Diskominfo Madiun">
    <meta name="author" content="Your Name">
    <title>@yield('title') - Pengelolaan Gudang</title>
    @vite(['resources/css/tengah.css'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Global Styles */
        html, body {
            background: #f9f9f9;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar Styles */
        .navbar {
            background: #06615E;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.8rem;
            color: #FFFFFF;
        }

        .navbar-brand:hover {
            color: #F9A33E;
        }

        .navbar-nav .nav-link {
            color: #FFFFFF;
            font-weight: 500;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #F9A33E;
        }

        .navbar-toggler-icon {
            background-color: #ffffff;
        }

        .navbar-nav .dropdown-menu {
            background-color: #FFFFFF;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .navbar-nav .dropdown-item {
            font-size: 1rem;
        }

        .navbar-nav .dropdown-item:hover {
            background-color: #06615E;
            color: #FFFFFF;
        }

        /* Content Wrapper */
        .content-wrapper {
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 40px;
            margin-top: 20px;
            flex: 1;
        }
        
        /* Button Styling */
        .btn-large {
            font-size: 1.2rem;
            padding: 12px 30px;
            width: 100%;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #F9A33E;
            color: white;
            border: none;
        }

        .btn-large:hover {
            background-color: #e38e29;
        }

        .btn {
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-primary {
            background-color: #F9A33E;
            border: none;
        }

        .btn-group-vertical {
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: center;
            justify-content: center;
        }

        .btn-primary:hover {
            background-color: #e38e29;
            transform: scale(1.05);
        }

        .btn-primary:focus {
            outline: none;
            box-shadow: 0 0 5px 2px rgba(243, 162, 62, 0.5);
        }

        .btn-success:hover {
            transform: scale(1.05);
            background-color: #045e54;
        }

        .btn.active {
            background: linear-gradient(135deg, #06615E 0%, #3A8E7B 100%); /* Gradasi hijau gelap ke hijau lebih cerah */
            border: none; /* Menghilangkan border default */
            color: white; /* Mengubah teks menjadi putih */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Menambahkan shadow */
            transform: scale(1.05); /* Sedikit membesarkan tombol */
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease; /* Transisi halus */
        }

        .btn.active:hover {
            background: linear-gradient(135deg, #3A8E7B 0%, #06615E 100%);
            transform: scale(1.1);
        }

        .uniform-btn {
            width: 100%; /* Lebar tombol sesuai dengan container */
            min-width: 90px; /* Memberikan lebar minimum untuk memastikan tombol seragam */
            max-width: 90px; /* Memberikan lebar minimum untuk memastikan tombol seragam */
            padding: 8px; /* Menambahkan ruang di dalam tombol */
            font-size: 0.9rem; /* Menyesuaikan ukuran font */
            text-align: center; /* Teks rata tengah */
            display: inline-block; /* Membuat tombol tetap inline tetapi seragam */
            white-space: nowrap; /* Menghindari teks terputus ke baris baru */
            border-radius: 5px; /* Konsistensi dengan tombol lainnya */
            transition: all 0.2s ease-in-out; /* Animasi halus saat hover */
        }

        .uniform-btn:hover {
            transform: scale(1.02);
        }

        .uniform-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Footer Styles */
        footer {
            background: #06615E;
            color: #FFFFFF;
            text-align: center;
            padding: 20px 0;
            margin-top: 40px;
            width: 100%;
        }

        footer p {
            margin: 0;
            font-size: 0.9rem;
        }

        /* Tabel Styles */
        .table {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .table thead {
            background-color: #06615E;
            color: #FFFFFF;
        }

        .table thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            font-weight: bold;
        }

        .table img {
            border-radius: 4px;
            transition: transform 0.3s ease;
        }

        .table img:hover {
            transform: scale(1.1);
        }

        .table tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .table tbody tr:nth-child(even) {
            background-color: #ecf0f1;
        }

        .table tbody tr:hover {
            background-color: #eeeff0;
        }

        .table th, .table td {
            border: 1px solid #ddd;
        }

        /* Pagination Styling */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination .page-item {
            margin: 0 5px;
        }

        .pagination .page-link {
            color: #06615E;
            font-weight: bold;
            border: 1px solid #ddd;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .pagination .page-link:hover {
            background-color: #06615E;
            color: white;
            border-color: #06615E;
        }

        .pagination .page-item.active .page-link {
            background-color: #F9A33E;
            color: white;
            border-color: #F9A33E;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .pagination .page-item.disabled .page-link {
            color: #ccc;
            background-color: #f8f9fa;
            border-color: #ddd;
            cursor: not-allowed;
        }

        /* Responsiveness */
        @media (max-width: 992px) {
            .navbar-brand {
                font-size: 1.5rem;
            }

            .content-wrapper {
                margin: 20px;
                padding: 25px;
            }
        }

        @media (max-width: 768px) {
            .navbar-nav .nav-link {
                font-size: 1rem;
            }

            .content-wrapper {
                margin-top: 20px;
                padding: 20px;
            }

            .pagination .page-link {
                font-size: 0.8rem;
                width: 30px;
                height: 30px;
            }

            .navbar-brand img {
                width: 50px;
                height: 50px;
            }

            .navbar-brand {
                font-size: 1.2rem;
            }

            .btn-large {
                font-size: 1rem;
                padding: 10px 20px;
            }

            .table th, .table td {
                font-size: 0.9rem;
            }

            .table img {
                width: 50px;
                height: 50px;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1rem;
            }

            .navbar-nav .nav-link {
                font-size: 0.9rem;
            }

            .content-wrapper {
                padding: 15px;
                margin: 10px;
            }

            .btn-large {
                font-size: 0.9rem;
                padding: 8px 15px;
            }

            .table th, .table td {
                font-size: 0.8rem;
            }

            .table img {
                width: 40px;
                height: 40px;
            }

            .pagination .page-link {
                width: 25px;
                height: 25px;
                font-size: 0.7rem;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <!-- Logo dan Nama Brand -->
            <a class="navbar-brand d-flex align-items-center" href="{{ route('barcode.index') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" width="70" height="70" class="me-2"> <!-- Logo -->
                Pengelolaan Gudang
            </a>
    
            <!-- Toggler untuk Responsif -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
    
            <!-- Menu Navigasi -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('barcode.index') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('barang.index') }}">Daftar Barang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('gudang.index') }}">Gudang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('transaksi.index')}}">Transaksi</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    

    <!-- Main Content Area -->
    <div class="container content-wrapper">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Diskominfo Madiun. All Rights Reserved.</p>
    </footer>

    <!-- Script Files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>