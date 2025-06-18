<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SPK - {{ $title ?? 'Dashboard' }}</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --primary: #4e73df;
            --primary-dark: #2e59d9;
            --secondary: #858796;
            --success: #1cc88a;
            --info: #36b9cc;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --light: #f8f9fc;
            --dark: #5a5c69;
            --sidebar-width: 250px;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fc;
            color: #5a5c69;
            line-height: 1.6;
        }
        
        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary) 0%, #224abe 100%);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.5rem;
            margin: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            transition: all 0.3s;
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        
        .sidebar .nav-link:hover, 
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 12px;
            font-size: 1.1rem;
        }
        
        .sidebar-heading {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0 1.5rem;
            margin: 1.5rem 0 0.5rem;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s;
        }
        
        /* Navbar */
        .navbar {
            padding: 0.75rem 1rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            z-index: 999;
            position: sticky;
            top: 0;
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary) !important;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 1.5rem;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 700;
            font-size: 1.1rem;
            padding: 1.25rem 1.5rem;
        }
        
        .container-fluid {
            padding: 0 1rem;
            max-width: 100%;
        }
        
        /* Tables */
        .table th {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            border-top: none;
            color: var(--secondary);
            padding: 1rem 1.5rem;
            background-color: var(--light);
        }
        
        /* Buttons */
        .btn {
            font-weight: 600;
            padding: 0.6rem 1.25rem;
            border-radius: 0.375rem;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
        }
        
        /* Alerts */
        .alert {
            border: none;
            border-radius: 0.5rem;
            padding: 1rem 1.5rem;
            font-weight: 500;
        }
        
        .alert-dismissible .btn-close {
            padding: 1.1rem 1.5rem;
        }
        
        /* Sidebar Styles */
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }

        #sidebar {
            width: 220px;
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary) 0%, #224abe 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            position: fixed;
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        #sidebar.active {
            margin-left: -250px;
        }

        .flex-grow-1 {
            flex: 1;
            margin-left: 220px;
            transition: all 0.3s;
        }

        #content.active {
            margin-left: 0;
        }

        .sidebar-header {
            padding: 20px;
            background: rgba(0, 0, 0, 0.1);
        }

        .sidebar-header h3 {
            color: #fff;
            margin: 0;
            font-weight: 700;
        }

        .sidebar-header strong {
            display: none;
        }

        .sidebar .nav-link {
            padding: 12px 20px;
            font-size: 1em;
            display: block;
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s;
            border-radius: 0;
            margin: 0;
            border-left: 3px solid transparent;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            border-left: 3px solid #fff;
        }

        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            border-left: 3px solid #fff;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1.1em;
            width: 20px;
            text-align: center;
        }

        .sidebar-heading {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75em;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 10px 20px;
            margin: 15px 0 5px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-footer {
            padding: 20px;
            position: absolute;
            bottom: 0;
            width: 100%;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Navbar Styles */
        .navbar {
            padding: 15px 20px;
            background: #fff !important;
            border: none;
            border-radius: 0;
            margin-bottom: 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-btn {
            box-shadow: none;
            outline: none !important;
            border: none;
            background: transparent;
            color: #4e73df;
        }

        .navbar-btn:hover {
            background: rgba(78, 115, 223, 0.1);
        }

        .navbar-btn i {
            font-size: 1.2em;
        }

        /* Content Styles */
        main {
            background: #f8f9fc;
            min-height: calc(100vh - 72px);
            padding: 1.5rem;
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
        }


        /* Alert Styles */
        .alert {
            border: none;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
        }

        .alert i {
            margin-right: 10px;
        }

        /* Table Styles */
        .table {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }

        .table th {
            background-color: #f8f9fc;
            color: #5a5c69;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            padding: 15px 20px;
            border: none;
        }

        .table td {
            padding: 15px 20px;
            vertical-align: middle;
            border-color: #f8f9fc;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
        }

        /* Responsive Styles */
        @media (max-width: 991.98px) {
            #sidebar {
                margin-left: -220px;
            }
            
            #sidebar.active {
                margin-left: 0;
            }
            
            .flex-grow-1 {
                margin-left: 0;
            }
            
            #content.active {
                margin-left: 250px;
                width: calc(100% - 250px);
            }
            
            .navbar-brand span.d-none.d-sm-inline {
                display: none !important;
            }
            
            .sidebar-header strong {
                display: block;
            }
        }
        
        /* Overlay for mobile */
        .overlay {
            display: none;
            position: fixed;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
            top: 0;
            left: 0;
        }
        
        @media (max-width: 767.98px) {
            .overlay {
                display: block;
            }
            
            #content.active {
                position: fixed;
                right: -250px;
            }
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</head>
<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar">
            <div class="sidebar-header p-3">
                <h4 class="text-white mb-0">SPK</h4>
            </div>

            <ul class="nav flex-column px-2">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kriteria.*') ? 'active' : '' }}" href="{{ route('kriteria.index') }}">
                        <i class="fas fa-list me-2"></i>
                        <span>Kriteria</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('alternatif.*') ? 'active' : '' }}" href="{{ route('alternatif.index') }}">
                        <i class="fas fa-users me-2"></i>
                        <span>Alternatif</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('penilaian.*') ? 'active' : '' }}" href="{{ route('penilaian.index') }}">
                        <i class="fas fa-tasks me-2"></i>
                        <span>Penilaian</span>
                    </a>
                </li>
                
                <li class="nav-divider my-2"></li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('saw.*') ? 'active' : '' }}" href="{{ route('saw.index') }}">
                        <i class="fas fa-calculator me-2"></i>
                        <span>Metode SAW</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('wp.*') ? 'active' : '' }}" href="{{ route('wp.index') }}">
                        <i class="fas fa-balance-scale me-2"></i>
                        <span>Metode WP</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('topsis.*') ? 'active' : '' }}" href="{{ route('topsis.index') }}">
                        <i class="fas fa-sitemap me-2"></i>
                        <span>Metode TOPSIS</span>
                    </a>
                </li>
                
                <li class="nav-divider my-2"></li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('perbandingan.*') ? 'active' : '' }}" href="{{ route('perbandingan.index') }}">
                        <i class="fas fa-chart-line me-2"></i>
                        <span>Perbandingan Metode</span>
                    </a>
                </li>
            </ul>
            
            <div class="mt-auto p-3 text-center">
                <small class="text-white-50">  {{ date('Y') }} SPK App</small>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                <div class="container-fluid px-4">
                    <button type="button" id="sidebarCollapse" class="btn btn-link text-white me-3">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="d-flex align-items-center ms-auto">
                        <span class="text-white">
                            <i class="fas fa-user me-1"></i> Admin
                        </span>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="p-4">
                <!-- Page Header -->
                <div class="mb-4">
                    @hasSection('breadcrumb')
                        <nav aria-label="breadcrumb" class="mb-3">
                            @yield('breadcrumb')
                        </nav>
                    @endif
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h1 class="h4 mb-0">@yield('title', 'Dashboard')</h1>
                        @hasSection('header-actions')
                            <div class="header-actions">
                                @yield('header-actions')
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Flash Messages -->
                <!-- Alert messages will be shown by SweetAlert2 -->

                <!-- Page Content -->
                <div class="bg-white rounded-3 p-4 shadow-sm">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>        <!-- Footer -->
            <footer class="footer mt-auto py-3 bg-white border-top">
                <div class="container-fluid text-center">
                    <span class="text-muted">
                         {{ date('Y') }} Sistem Pendukung Keputusan - All Rights Reserved
                    </span>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>
    
    <script>
        // Fungsi untuk menampilkan notifikasi sukses
        function showSuccessAlert(message) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            
            Toast.fire({
                icon: 'success',
                title: message || 'Operasi berhasil dilakukan!'
            });
        }
        
        // Fungsi untuk menampilkan notifikasi error
        function showErrorAlert(message) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            
            Toast.fire({
                icon: 'error',
                title: message || 'Terjadi kesalahan!'
            });
        }
        
        // Cek jika ada flash message dari Laravel
        @if(session('success'))
            showSuccessAlert('{{ session('success') }}');
        @endif
        
        @if($errors->any())
            showErrorAlert('{{ $errors->first() }}');
        @endif
    </script>
    
    <!-- Vite JS -->
    @vite(['resources/js/app.js'])
    
    @stack('scripts')
</body>
</html>
