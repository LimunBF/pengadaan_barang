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
            background: linear-gradient(to bottom right, #6a11cb, #2575fc);
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
        }
        .form-control {
            border-radius: 10px;
        }
        .btn-primary {
            background: #6a11cb;
            border: none;
            border-radius: 10px;
            transition: background 0.3s ease;
        }
        .btn-primary:hover {
            background: #2575fc;
        }
        .alert-danger {
            border-radius: 10px;
        }
        .form-text {
            font-size: 0.9rem;
            color: #6a11cb;
        }
        footer {
            background: rgba(0, 0, 0, 0.8);
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
        <form method="POST" action="{{ route('login') }}">
            @csrf
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
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
</body>
</html>
