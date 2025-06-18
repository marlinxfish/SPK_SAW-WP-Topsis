<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK - {{ $title ?? 'Dashboard' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            flex-shrink: 0;
        }
        .sidebar .nav-link {
            color: #fff;
        }
        .sidebar .nav-link.active {
            background-color: #495057;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column p-3">
        <h4 class="text-white">SPK Dashboard</h4>
        <hr class="text-white">
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a href="/kriteria" class="nav-link {{ request()->is('kriteria') ? 'active' : '' }}">Kriteria</a>
            </li>
            <li class="nav-item">
                <a href="/alternatif" class="nav-link {{ request()->is('alternatif') ? 'active' : '' }}">Alternatif</a>
            </li>
            <li class="nav-item">
                <a href="/penilaian" class="nav-link {{ request()->is('penilaian') ? 'active' : '' }}">Penilaian</a>
            </li>
            <li class="nav-item">
                <a href="#perhitungan" class="nav-link {{ request()->is('saw*') ? 'active' : '' }}" data-bs-toggle="collapse">
                    <i class="fas fa-calculator me-2"></i>Perhitungan
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse {{ request()->is('saw*') || request()->is('wp*') || request()->is('topsis*') ? 'show' : '' }}" id="perhitungan">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a href="{{ route('saw.index') }}" class="nav-link {{ request()->routeIs('saw.index') ? 'active' : '' }}">
                                <i class="fas fa-project-diagram me-2"></i>Metode SAW
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('wp.index') }}" class="nav-link {{ request()->routeIs('wp.index') ? 'active' : '' }}">
                                <i class="fas fa-chart-pie me-2"></i>Metode WP
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('topsis.index') }}" class="nav-link {{ request()->routeIs('topsis.index') ? 'active' : '' }}">
                                <i class="fas fa-sitemap me-2"></i>Metode TOPSIS
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
        <hr class="text-white mt-auto">
        <small class="text-muted">© {{ date('Y') }} Sistem Pendukung Keputusan</small>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">@yield('title', 'Dashboard')</h2>
                @yield('breadcrumb', '')
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Scripts Section -->
    @stack('scripts')
</body>
</html>
