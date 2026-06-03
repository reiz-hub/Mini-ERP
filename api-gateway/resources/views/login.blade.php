<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FitLife ERP</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a, #1e1b4b);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f8fafc;
        }

        .login-card {
            border: none;
            border-radius: 24px;
            background-color: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
            padding: 2.5rem;
        }

        .form-control-custom {
            background-color: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus {
            background-color: rgba(15, 23, 42, 0.8);
            border-color: #6366f1;
            color: #ffffff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
        }

        .btn-indigo {
            background-color: #6366f1;
            color: #ffffff;
            font-weight: 600;
            border-radius: 12px;
            padding: 0.75rem;
            border: none;
            transition: background-color 0.2s ease;
        }

        .btn-indigo:hover {
            background-color: #4f46e5;
            color: #ffffff;
        }

        .logo-icon {
            font-size: 3rem;
            color: #6366f1;
            margin-bottom: 1rem;
        }

        .invalid-feedback {
            color: #ffffff !important;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <div class="logo-icon"><i class="bi bi-lightning-charge-fill"></i></div>
            <h3 class="fw-bold mb-1">Welcome back</h3>
            <p class="text-muted small">Sign in to your FitLife ERP account</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-3 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label small text-muted fw-bold text-uppercase">Email address</label>
                <input type="email" name="email" id="email" class="form-control form-control-custom @error('email') is-invalid @enderror" placeholder="name@example.com" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="form-label small text-muted fw-bold text-uppercase">Password</label>
                <input type="password" name="password" id="password" class="form-control form-control-custom" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-indigo w-100 mb-3">Sign In</button>
            
            <div class="text-center">
                <span class="text-muted small">Default credentials: test@example.com / password</span>
            </div>
        </form>
    </div>

    <!-- Bootstrap 5 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
