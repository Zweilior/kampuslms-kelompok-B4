
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>404 — {{ $exception->getMessage() ?: 'Halaman Tidak Ditemukan' }} | KampusLMS</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f5f7fa;
            color: #1a202c;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .error-container {
            text-align: center;
            max-width: 480px;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 800;
            color: #e53e3e;
            line-height: 1;
            margin-bottom: 1rem;
        }
        .error-title {
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
            color: #2d3748;
        }
        .error-message {
            color: #718096;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s;
        }
        .btn-primary {
            background: #3182ce;
            color: white;
        }
        .btn-primary:hover {
            background: #2c5282;
        }
        .btn-secondary {
            background: #e2e8f0;
            color: #2d3748;
        }
        .btn-secondary:hover {
            background: #cbd5e0;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <h1 class="error-title">Halaman yang Anda Cari Tidak Ada</h1>
        <p class="error-message">
            Maaf, halaman yang Anda tuju tidak dapat ditemukan.
            Mungkin URL-nya salah ketik, atau halaman tersebut sudah dipindahkan.
        </p>

        <div class="error-actions">
            <a href="/" onclick="history.back(); return false;" class="btn btn-secondary">
                ← Kembali
            </a>

            <a href="{{ route('courses.index') }}" class="btn btn-primary">
                Ke Daftar Mata Kuliah
            </a>
        </div>
    </div>
</body>
</html>