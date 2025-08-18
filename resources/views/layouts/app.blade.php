<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tu Pedido</title>

    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!--importacion de css-->
    @vite(['resources/css/variables.css', 'resources/css/style.css'])


</head>

<body>

    {{-- Sidebar --}}
    @include('components.sidebar')

    {{-- Header --}}
    @include('components.header')

    {{-- Contenido --}}
    <main class="content content-body">
        @yield('contenido')
    </main>
    @stack('scripts')

    

    <!-- Bootstrap JS Bundle CDN (Popper + Bootstrap JS) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>