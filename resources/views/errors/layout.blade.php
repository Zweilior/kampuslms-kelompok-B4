<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">

    <title>{{ $statusCode }} — {{ $pageTitle }} | EduSpace</title>
    <style>
        :root {
            color-scheme: light;
            --error-bg: #f7f8f5;
            --error-card: rgba(255, 255, 255, 0.82);
            --error-text: #1a2416;
            --error-muted: #687461;
            --error-border: rgba(61, 107, 31, 0.14);
            --error-primary: #3d6b1f;
            --error-primary-hover: #2a4d15;
            --error-secondary: #e8ede2;
            --error-secondary-hover: #dce5d4;
            --error-shadow: 0 24px 70px rgba(31, 49, 21, 0.12);
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            padding: 2rem;
            display: grid;
            place-items: center;
            overflow: hidden;
            position: relative;
            isolation: isolate;
            color: var(--error-text);
            background:
                radial-gradient(ellipse at 12% 10%, rgba(201, 151, 63, 0.12), transparent 32rem),
                radial-gradient(ellipse at 90% 88%, rgba(139, 174, 102, 0.16), transparent 34rem),
                var(--error-bg);
            font-family: "Poppins", "Segoe UI", system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        body::before,
        body::after {
            content: "";
            position: fixed;
            z-index: -1;
            width: 21rem;
            aspect-ratio: 1;
            border: 1px solid rgba(61, 107, 31, 0.12);
            border-radius: 42% 58% 62% 38% / 44% 42% 58% 56%;
            pointer-events: none;
        }

        body::before {
            top: -11rem;
            right: -7rem;
            box-shadow: 0 0 0 2rem rgba(139, 174, 102, 0.045), 0 0 0 4rem rgba(139, 174, 102, 0.035);
            transform: rotate(24deg);
        }

        body::after {
            bottom: -15rem;
            left: -9rem;
            width: 29rem;
            border-color: rgba(201, 151, 63, 0.15);
            box-shadow: 0 0 0 2rem rgba(201, 151, 63, 0.045), 0 0 0 4rem rgba(201, 151, 63, 0.03);
            transform: rotate(-18deg);
        }

        .error-card {
            width: min(100%, 34rem);
            padding: clamp(2rem, 7vw, 3.5rem);
            text-align: center;
            background: var(--error-card);
            border: 1px solid var(--error-border);
            border-radius: 1.5rem;
            box-shadow: var(--error-shadow);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            margin-bottom: 2rem;
            color: var(--error-primary);
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .brand-mark {
            width: 0.65rem;
            height: 0.65rem;
            border-radius: 0.2rem;
            background: currentColor;
            transform: rotate(45deg);
        }

        .error-code {
            margin: 0 0 1rem;
            font-size: clamp(5rem, 18vw, 7rem);
            font-weight: 800;
            line-height: 0.95;
            letter-spacing: -0.07em;
        }

        .error-code--401 { color: #4b83c4; }
        .error-code--403 { color: #c87832; }
        .error-code--404 { color: #c65050; }

        .error-title {
            margin: 0 0 0.75rem;
            font-size: clamp(1.3rem, 4vw, 1.65rem);
            line-height: 1.3;
        }

        .error-message {
            margin: 0 auto 2rem;
            max-width: 27rem;
            color: var(--error-muted);
            line-height: 1.75;
        }

        .error-actions {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .btn {
            min-height: 2.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.25rem;
            border: 1px solid transparent;
            border-radius: 0.75rem;
            font: inherit;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 160ms ease, transform 160ms ease, border-color 160ms ease;
        }

        .btn:hover { transform: translateY(-1px); }
        .btn:focus-visible { outline: 3px solid rgba(61, 107, 31, 0.35); outline-offset: 3px; }

        .btn-primary {
            color: #fff;
            background: var(--error-primary);
        }

        .btn-primary:hover { background: var(--error-primary-hover); }

        .btn-secondary {
            color: var(--error-text);
            background: var(--error-secondary);
            border-color: var(--error-border);
        }

        .btn-secondary:hover { background: var(--error-secondary-hover); }

        @media (prefers-color-scheme: dark) {
            :root {
                color-scheme: dark;
                --error-bg: #0f150e;
                --error-card: rgba(23, 29, 22, 0.86);
                --error-text: #dee4d9;
                --error-muted: #a6b09d;
                --error-border: rgba(139, 174, 102, 0.17);
                --error-primary: #8bae66;
                --error-primary-hover: #a4c780;
                --error-secondary: #252d24;
                --error-secondary-hover: #303a2e;
                --error-shadow: 0 24px 70px rgba(0, 0, 0, 0.32);
            }

            body {
                background:
                    radial-gradient(ellipse at 12% 10%, rgba(201, 151, 63, 0.09), transparent 32rem),
                    radial-gradient(ellipse at 90% 88%, rgba(139, 174, 102, 0.12), transparent 34rem),
                    var(--error-bg);
            }

            body::before { border-color: rgba(139, 174, 102, 0.16); }
            body::after { border-color: rgba(201, 151, 63, 0.17); }
            .error-code--401 { color: #79a9e2; }
            .error-code--403 { color: #e4a15f; }
            .error-code--404 { color: #e77d7d; }
            .btn-primary { color: #17220f; }
        }

        @media (max-width: 480px) {
            body { padding: 1rem; }
            .error-card { border-radius: 1.25rem; }
            .error-actions { flex-direction: column-reverse; }
            .btn { width: 100%; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { transition-duration: 0.01ms !important; }
        }
    </style>
</head>
<body>
    <main class="error-card">
        <div class="brand"><span class="brand-mark" aria-hidden="true"></span>EduSpace Learning Management</div>
        <p class="error-code error-code--{{ $statusCode }}" aria-label="Error {{ $statusCode }}">{{ $statusCode }}</p>
        <h1 class="error-title">{{ $heading }}</h1>
        <p class="error-message">{{ $message }}</p>

        <nav class="error-actions" aria-label="Navigasi halaman error">
            <a href="/" onclick="history.back(); return false;" class="btn btn-secondary">← Kembali</a>
            <a href="{{ $primaryUrl }}" class="btn btn-primary">{{ $primaryLabel }}</a>
        </nav>
    </main>
</body>
</html>
