<header class="header">
    <button type="button" class="btn btn-outline-secondary position-relative me-3" aria-label="Notificaciones">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bell"
            viewBox="0 0 16 16">
            <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2z" />
            <path
                d="M8 1a5 5 0 0 0-5 5v2.086l-.707.707A1 1 0 0 0 2 10h12a1 1 0 0 0 .707-1.707L13 8.086V6a5 5 0 0 0-5-5z" />
        </svg>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            3
            <span class="visually-hidden">notificaciones</span>
        </span>
    </button>

    <span class="me-3">{{ Auth::user()->nombre }}</span>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-link text-danger p-0">Cerrar sesión</button>
    </form>
</header>