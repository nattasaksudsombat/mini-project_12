<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ระบบจัดการร้านค้า')</title>    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts - Kanit (Thai font) -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- AOS Animation Library -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --dark-bg: #0a0a0a;
            --dark-secondary: #121212;
            --dark-tertiary: #1e1e1e;
            --gold: #FFD700;
            --gold-dark: #e6c300;
            --neon-gold: #ffe566;
            --neon-blue: #5fedff;
            --neon-pink: #ff36c4;
            --text-primary: #f8f8f8;
            --text-secondary: #cccccc;
        }
        
        * {
            transition: all 0.3s ease;
        }
        
        body {
            background-color: var(--dark-bg);
            color: var(--text-primary);
            font-family: 'Kanit', sans-serif;
            background-image: 
                radial-gradient(circle at 10% 10%, rgba(255, 215, 0, 0.02) 0%, transparent 30%),
                radial-gradient(circle at 90% 90%, rgba(255, 215, 0, 0.02) 0%, transparent 30%);
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            background: 
                linear-gradient(45deg, transparent 96%, var(--gold) 97%),
                linear-gradient(135deg, transparent 96%, var(--gold) 97%);
            background-size: 300px 300px;
            opacity: 0.05;
            z-index: -1;
        }
        
        /* Navbar Styling */
        .navbar {
            background: linear-gradient(90deg, var(--dark-secondary), var(--dark-tertiary));
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.5);
            border-bottom: 1px solid rgba(255, 215, 0, 0.2);
            padding: 0.7rem 1rem;
        }
        
        .navbar-brand {
            font-weight: 600;
            color: var(--gold) !important;
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
            letter-spacing: 1px;
            position: relative;
            padding-bottom: 2px;
        }
        
        .navbar-brand::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--gold), transparent);
            transition: width 0.5s ease;
        }
        
        .navbar-brand:hover::after {
            width: 100%;
        }
        
        .navbar-toggler {
            border: 1px solid var(--gold);
            padding: 0.2rem 0.5rem;
        }
        
        .navbar-toggler:focus {
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.25);
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 215, 0, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        .navbar .nav-link {
            color: var(--text-secondary) !important;
            font-weight: 400;
            position: relative;
            margin: 0 0.3rem;
            padding: 0.5rem 0.7rem !important;
            border-radius: 4px;
            z-index: 1;
        }
        
        .navbar .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 215, 0, 0.1);
            border-radius: 4px;
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.3s ease;
            z-index: -1;
        }
        
        .navbar .nav-link:hover {
            color: var(--gold) !important;
        }
        
        .navbar .nav-link:hover::before {
            transform: scaleX(1);
            transform-origin: left;
        }
        
        .navbar .nav-link.active {
            color: var(--gold) !important;
            background-color: rgba(255, 215, 0, 0.1);
            text-shadow: 0 0 5px rgba(255, 215, 0, 0.3);
        }
        
        .navbar .nav-link i {
            transition: transform 0.3s ease;
        }
        
        .navbar .nav-link:hover i {
            transform: translateY(-2px);
        }
        
        /* Sidebar Styling */
        .sidebar {
            min-height: calc(100vh - 56px);
            background: linear-gradient(180deg, var(--dark-secondary), var(--dark-tertiary));
            padding-top: 20px;
            border-right: 1px solid rgba(255, 215, 0, 0.1);
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.2);
            z-index: 100;
        }
        
        .sidebar .nav-link {
            color: var(--text-secondary);
            padding: 0.7rem 1rem;
            margin-bottom: 8px;
            border-radius: 0 30px 30px 0;
            position: relative;
            overflow: hidden;
        }
        
        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -5px;
            width: 5px;
            height: 100%;
            background: linear-gradient(to bottom, var(--gold), var(--neon-gold));
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            color: var(--text-primary);
            background-color: rgba(255, 215, 0, 0.05);
            padding-left: 1.3rem;
        }
        
        .sidebar .nav-link:hover::before {
            left: 0;
        }
        
        .sidebar .nav-link.active {
            background: linear-gradient(90deg, rgba(255, 215, 0, 0.15), transparent);
            color: var(--gold);
            font-weight: 500;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar .nav-link.active::before {
            left: 0;
        }
        
        .sidebar .nav-link i {
            margin-right: 0.5rem;
            width: 20px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .sidebar .nav-link:hover i {
            transform: translateX(3px);
        }
        
        /* Main Content Area */
        .main-content {
            padding: 30px;
            position: relative;
            min-height: calc(100vh - 56px - 60px); /* Navbar height & footer height */
        }
        
        /* Alert Styling */
        .alert {
            border: none;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            animation: slideDown 0.5s ease forwards;
        }
        
        @keyframes slideDown {
            0% { transform: translateY(-50px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        
        .alert::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
        }
        
        .alert-success {
            background-color: rgba(25, 135, 84, 0.1);
            color: #75b798;
        }
        
        .alert-success::before {
            background: linear-gradient(to right, transparent, #75b798, transparent);
            animation: loading 3s infinite ease;
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #ea868f;
        }
        
        .alert-danger::before {
            background: linear-gradient(to right, transparent, #ea868f, transparent);
            animation: loading 3s infinite ease;
        }
        
        @keyframes loading {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .btn-close {
            color: var(--text-secondary);
        }
        
        /* Card Styling */
        .card {
            background-color: var(--dark-secondary);
            border: 1px solid rgba(255, 215, 0, 0.1);
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            position: relative;
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 50% 0%, rgba(255, 215, 0, 0.05), transparent 70%);
            pointer-events: none;
        }
        
       
        
        .card-header {
            background-color: var(--dark-tertiary);
            border-bottom: 1px solid rgba(255, 215, 0, 0.1);
            padding: 1rem 1.5rem;
            font-weight: 500;
            color: var(--gold);
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Footer Styling */
        .footer {
            padding: 20px 0;
            text-align: center;
            background-color: var(--dark-secondary);
            border-top: 1px solid rgba(255, 215, 0, 0.1);
            position: relative;
            z-index: 10;
        }
        
        .footer .text-muted {
            color: var(--text-secondary) !important;
            position: relative;
            display: inline-block;
        }
        
        .footer .text-muted::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -5px;
            width: 0;
            height: 1px;
            background: var(--gold);
            transition: width 0.5s ease;
        }
        
        .footer:hover .text-muted::after {
            width: 100%;
        }
        
        /* Button & Form Controls */
        .btn {
            border-radius: 6px;
            padding: 0.5rem 1.2rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .btn::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            animation: shine 3s infinite linear;
            z-index: -1;
        }
        
        @keyframes shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .btn-primary {
            background: linear-gradient(45deg, var(--dark-tertiary), var(--dark-secondary));
            border: 1px solid var(--gold);
            color: var(--gold);
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background: linear-gradient(45deg, var(--gold-dark), var(--gold));
            border-color: var(--gold-dark);
            color: var(--dark-bg);
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.5);
        }
        
        .btn-success {
            background: linear-gradient(45deg, #198754, #20c997);
            border: 1px solid #198754;
        }
        
        .btn-success:hover, .btn-success:focus {
            background: linear-gradient(45deg, #157347, #198754);
            border-color: #157347;
            box-shadow: 0 0 15px rgba(32, 201, 151, 0.5);
        }
        
        .btn-danger {
            background: linear-gradient(45deg, #dc3545, #e35d6a);
        
        background:
            linear-gradient(45deg, #dc3545, #e35d6a);
            border: 1px solid #dc3545;
        } 
        
        
        .btn-danger:hover, .btn-danger:focus {
            background: linear-gradient(45deg, #bb2d3b, #dc3545);
            border-color: #bb2d3b;
            box-shadow: 0 0 15px rgba(220, 53, 69, 0.5);
        }
        
        .form-control, .form-select {
            background-color: var(--dark-tertiary);
            border: 1px solid rgba(255, 215, 0, 0.2);
            color: var(--text-primary);
            border-radius: 6px;
            padding: 0.6rem 1rem;
        }
        
        .form-control:focus, .form-select:focus {
            background-color: var(--dark-tertiary);
            border-color: var(--gold);
            color: var(--text-primary);
            box-shadow: 0 0 0 0.25rem rgba(255, 215, 0, 0.2);
        }
        
        .form-label {
            color: var(--gold);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        /* Table Styling */
        .table {
            color: var(--text-primary);
            border-collapse: separate;
            border-spacing: 0 5px;
        }
        
        .table thead th {
            background-color: var(--dark-tertiary);
            color: var(--gold);
            font-weight: 500;
            border: none;
            padding: 0.75rem 1rem;
        }
        
        .table tbody tr {
            background-color: rgba(255, 255, 255, 0.02);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 225, 0, 0.91);
            background-color: rgba(205, 183, 54, 0.05);
        }
        
        .table td {
            border: none;
            padding: 0.75rem 1rem;
            vertical-align: middle;
        }
        
        /* Badge Styling */
        .badge {
            padding: 0.4rem 0.6rem;
            border-radius: 4px;
            font-weight: 500;
        }
        
        .bg-primary {
            background-color: rgba(95, 237, 255, 0.2) !important;
            color: var(--neon-blue) !important;
            border: 1px solid rgba(95, 237, 255, 0.3);
        }
        
        .bg-success {
            background-color: rgba(25, 135, 84, 0.2) !important;
            color: #75b798 !important;
            border: 1px solid rgba(25, 135, 84, 0.3);
        }
        
        .bg-danger {
            background-color: rgba(255, 54, 196, 0.2) !important;
            color: var(--neon-pink) !important;
            border: 1px solid rgba(255, 54, 196, 0.3);
        }
        
        .bg-warning {
            background-color: rgba(255, 215, 0, 0.2) !important;
            color: var(--gold) !important;
            border: 1px solid rgba(255, 215, 0, 0.3);
        }
        
        /* Animations */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        @keyframes glow {
            0% { box-shadow: 0 0 5px rgba(255, 215, 0, 0.3); }
            50% { box-shadow: 0 0 20px rgba(255, 215, 0, 0.5); }
            100% { box-shadow: 0 0 5px rgba(255, 215, 0, 0.3); }
        }
        
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        
        .glow-effect {
            animation: glow 3s infinite;
        }
        
        .pulse-effect {
            animation: pulse 2s infinite;
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }
        .delay-5 { animation-delay: 0.5s; }
        
        /* Neon text effects */
        .neon-text {
            color: var(--gold);
            text-shadow: 0 0 5px rgba(255, 215, 0, 0.5),
                         0 0 10px rgba(255, 215, 0, 0.3),
                         0 0 15px rgba(255, 215, 0, 0.2);
        }
        
        .neon-text-blue {
            color: var(--neon-blue);
            text-shadow: 0 0 5px rgba(95, 237, 255, 0.5),
                         0 0 10px rgba(95, 237, 255, 0.3),
                         0 0 15px rgba(95, 237, 255, 0.2);
        }
        
        .neon-text-pink {
            color: var(--neon-pink);
            text-shadow: 0 0 5px rgba(255, 54, 196, 0.5),
                         0 0 10px rgba(255, 54, 196, 0.3),
                         0 0 15px rgba(255, 54, 196, 0.2);
        }
        
        /* Particle background */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }
        
        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            background-color: var(--gold);
            border-radius: 50%;
            opacity: 0.7;
            animation: float 30s infinite linear;
        }
        
        @keyframes float {
            0% { transform: translateY(0) translateX(0); }
            25% { transform: translateY(-20px) translateX(10px); }
            50% { transform: translateY(0) translateX(20px); }
            75% { transform: translateY(20px) translateX(10px); }
            100% { transform: translateY(0) translateX(0); }
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
            background-color: var(--dark-bg);
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, var(--gold), var(--gold-dark));
            border-radius: 5px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, var(--gold-dark), var(--gold));
        }

        /* Custom Loader */
        .loader {
            width: 100%;
            height: 3px;
            position: relative;
            overflow: hidden;
            background-color: var(--dark-tertiary);
            margin: 10px 0;
            border-radius: 20px;
        }
        
        .loader:before {
            content: "";
            position: absolute;
            left: -50%;
            height: 3px;
            width: 40%;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            animation: loading 1.5s infinite ease;
            border-radius: 20px;
        }
        
        @keyframes loading {
            0% { left: -50%; }
            100% { left: 110%; }
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 56px;
                left: -100%;
                width: 250px;
                height: calc(100% - 56px);
                z-index: 1000;
                transition: all 0.3s ease;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .main-content {
                padding: 20px 15px;
            }
        }
    </style>


<body>

    {{-- Navbar --}}
    

    <div class="container-fluid">
        <div class="row">
            {{-- Sidebar --}}
            @include('layouts.sidebar')

            {{-- Main Content --}}
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                @yield('content')
            </main>
        </div>
    </div>



    <footer class="footer mt-auto">
        <div class="container">
            <span class="text-muted">
                <i class="fas fa-wallet me-2"></i>
                ระบบบันทึกข้อมูลรายรับ-รายจ่าย &copy; {{ date('Y') }}
            </span>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0"></script>
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@yield('scripts') <!-- Add this line to include script sections -->
@stack('scripts')
</body>
</html>