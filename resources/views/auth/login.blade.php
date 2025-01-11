<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: linear-gradient(to bottom right, #06615E, #20B2AA);
            color: #ffffff;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 400px;
            background: #ffffff;
            color: #000000;
            border-radius: 15px;
            padding: 40px 30px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
            margin: auto;
        }
        h2 {
            margin-bottom: 30px;
            text-align: center;
            font-weight: bold;
            color: #06615E;
        }
        .form-control {
            border-radius: 10px;
            border: 1px solid #06615E;
        }
        .form-control:focus {
            border-color: #20B2AA;
            box-shadow: 0 0 5px rgba(32, 178, 170, 0.5);
        }
        .btn-primary {
            background: #F9A33E;
            border: none;
            border-radius: 10px;
            transition: background 0.3s ease;
        }
        .btn-primary:hover {
            background: #e38e29;
        }
        footer {
            background: #06615E;
            color: #ffffff;
            text-align: center;
            padding: 15px 0;
            margin-top: auto;
        }
        footer p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2025 Diskominfo Madiun. All Rights Reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- SweetAlert2 Notifications -->
    <script>
        // Check for validation errors
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                html: `
                    <ul style="list-style: none; padding: 0; text-align: center;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                `,
                confirmButtonText: 'OK',
                confirmButtonColor: '#F9A33E'
            });
        @endif



        // Example: Session success message (if any)
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK',
                confirmButtonColor: '#06615E'
            });
        @endif
    </script>
</body>
</html>
