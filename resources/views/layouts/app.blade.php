<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.title') }}</title>

    <!-- jQuery (obligatorio para Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!--  Swal.fire alertas con estilos -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    

    <!-- Importación de CSS -->
    @vite(['resources/css/variables.css', 'resources/css/style.css', 'resources/js/validaciones.js', 'resources/css/catalogos.css'])
</head>
<body>

    {{-- Sidebar --}}
    @include('components.sidebar')

    {{-- Header --}}
    @include('components.header')
    {{--@include('components.navBar')--}}


    {{-- Contenido principal --}}
    <main class="content content-body">
        {{--muestra los mensajes de errrores en todas las vistas--}}
        @if(session('success'))
            <div class="panel-alert success"> {{ session('success') }} </div>
        @endif

        @if(session('warning'))
            <div class="panel-alert warning"> {{ session('warning') }} </div>
        @endif

        @if(session('error')) 
            <div class="panel-alert error"> {{ session('error') }} </div>
        @endif

        @yield('contenido')
    </main>

    {{-- Overlay --}}
    <div class="overlay" id="overlay"></div>

    @stack('scripts')

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    
    <!-- Script toggle sidebar -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        const closeBtn = document.getElementById('sidebarClose');
        const overlay = document.getElementById('overlay');

        const openSidebar = () => {
            sidebar.classList.add('sidebar-show');
            overlay.classList.add('active');
        };

        const closeSidebar = () => {
            sidebar.classList.remove('sidebar-show');
            overlay.classList.remove('active');
        };

        if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (overlay) overlay.addEventListener('click', closeSidebar);
    });
    </script>

</body>
</html>
