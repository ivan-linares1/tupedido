<header class="header">
    <!-- Botón hamburguesa visible solo en móvil -->
    <button id="sidebarToggle" class="d-md-none btn btn-light">
        <i class="bi bi-list"></i>
    </button>

    <span class="me-3">{{ Auth::user()->nombre }}</span>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-outline-danger">Cerrar sesión</button>
    </form>
</header>