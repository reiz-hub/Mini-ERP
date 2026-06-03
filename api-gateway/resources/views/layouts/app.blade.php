<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FitLife ERP')</title>
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
            background-color: #f8fafc;
            color: #1e293b;
        }
        
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, #0f172a 0%, #1e1b4b 100%);
            color: #f8fafc;
            z-index: 1000;
            padding-top: 1.5rem;
            box-shadow: 4px 0 25px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 0 1.5rem 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            margin-bottom: 1.5rem;
        }

        .sidebar-brand h4 {
            font-weight: 700;
            color: #6366f1;
            letter-spacing: -0.5px;
            background: linear-gradient(to right, #818cf8, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            padding: 0.85rem 1.5rem;
            color: #94a3b8;
            text-decoration: none;
            font-weight: 500;
            border-left: 4px solid transparent;
            margin: 0.2rem 0;
            transition: all 0.2s ease-in-out;
        }

        .nav-link-custom i {
            font-size: 1.25rem;
            margin-right: 0.85rem;
        }

        .nav-link-custom:hover {
            color: #f8fafc;
            background-color: rgba(255, 255, 255, 0.05);
            transform: translateX(4px);
        }

        .nav-link-custom.active {
            color: #ffffff;
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.15) 0%, rgba(99, 102, 241, 0.03) 100%);
            border-left-color: #6366f1;
            font-weight: 600;
        }

        .main-content {
            margin-left: 260px;
            padding: 2.5rem;
            min-height: 100vh;
            animation: fadeIn 0.4s ease-out;
        }

        .top-navbar {
            background-color: #ffffff;
            border: 1px solid #f1f5f9;
            padding: 1.2rem 2rem;
            margin-bottom: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03);
        }

        .card-premium {
            border: 1px solid #f1f5f9;
            border-radius: 20px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.03);
            background-color: #ffffff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-premium:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -10px rgba(99, 102, 241, 0.08);
            border-color: rgba(99, 102, 241, 0.15);
        }

        .btn-indigo {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #ffffff;
            font-weight: 600;
            border-radius: 12px;
            padding: 0.7rem 1.4rem;
            border: none;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
            transition: all 0.2s ease;
        }

        .btn-indigo:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.3);
        }

        .form-control, .form-select {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
        }

        .table {
            --bs-table-bg: transparent;
            --bs-table-hover-bg: #f8fafc;
        }

        .table thead th {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #64748b;
            padding: 1rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .table tbody td {
            padding: 1.2rem 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .badge-active {
            background-color: rgba(34, 197, 94, 0.1);
            color: #16a34a;
            font-weight: 600;
        }

        .badge-inactive, .badge-expired, .badge-cancelled {
            background-color: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            font-weight: 600;
        }

        .badge-completed {
            background-color: rgba(59, 130, 246, 0.1);
            color: #2563eb;
            font-weight: 600;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand d-flex align-items-center justify-content-between">
            <h4 class="mb-0"><i class="bi bi-lightning-charge-fill me-2"></i>FitLife ERP</h4>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link-custom {{ Route::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-grid-fill"></i>Dashboard
            </a>
            <a class="nav-link-custom {{ Route::is('members') ? 'active' : '' }}" href="{{ route('members') }}">
                <i class="bi bi-people-fill"></i>Members
            </a>
            <a class="nav-link-custom {{ Route::is('memberships') ? 'active' : '' }}" href="{{ route('memberships') }}">
                <i class="bi bi-credit-card-2-front-fill"></i>Memberships
            </a>
            <a class="nav-link-custom {{ Route::is('employees') ? 'active' : '' }}" href="{{ route('employees') }}">
                <i class="bi bi-person-badge-fill"></i>Employees
            </a>
            <a class="nav-link-custom {{ Route::is('reports') ? 'active' : '' }}" href="{{ route('reports') }}">
                <i class="bi bi-bar-chart-line-fill"></i>Reports
            </a>
        </nav>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-0 fw-semibold">@yield('page-title', 'Overview')</h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                @if(Session::has('user'))
                    <div class="text-end">
                        <div class="fw-semibold text-dark">{{ Session::get('user')['name'] }}</div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">{{ Session::get('user')['role'] ?? 'Staff' }}</small>
                    </div>
                    <div class="vr"></div>
                    <form action="{{ route('logout') }}" method="POST" class="mb-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Alert messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Dynamic Content -->
        @yield('content')
    </div>

    <!-- Bootstrap 5 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
