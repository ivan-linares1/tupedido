<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tu Pedido</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Importación de CSS -->
    @vite(['resources/css/variables.css', 'resources/css/style.css'])
</head>
<body>

    {{-- Sidebar --}}
    @include('components.sidebar')

    {{-- Header --}}
    @include('components.header')

    {{-- Contenido principal --}}
    <main class="content content-body">
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
