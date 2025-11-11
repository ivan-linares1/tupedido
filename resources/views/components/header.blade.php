<header class="header">
    <!-- Botón hamburguesa visible solo en móvil -->
    <button id="sidebarToggle" class="d-md-none btn btn-light">
        <i class="bi bi-list"></i>
    </button>

    <span class="me-3">{{ Auth::user()->nombre }}</span> <!-- nombre del usuario que inicio sesion -->

    <form method="POST" action="{{ route('logout') }}"> <!-- Botón con formulario para cerrar sesion  -->
        @csrf
        <button type="submit" class="btn btn-outline-danger">Cerrar sesión</button>
    </form>
</header>