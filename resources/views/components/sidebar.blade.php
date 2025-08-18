<nav class="sidebar d-flex flex-column">
    <h1>Tu pedido</h1>
    <a href="https://www.kombitec.com.mx/" target="_blank" class="fs-4 fw-bold mb-4 px-3">KOMBITEC</a>
    <ul class="nav flex-column px-2">

        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
        </li>

        {{-- Ventas con submenu --}}
        <li class="nav-item">
            <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#submenuVentas" role="button" aria-expanded="false" aria-controls="submenuVentas">
                Ventas
                <i class="bi bi-chevron-down"></i>
            </a>
            <div class="collapse" id="submenuVentas">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ps-3">
                    <li><a href="#" class="nav-link">Seguimiento</a></li>
                    <li><a href="#" class="nav-link">Clientes</a></li>
                </ul>
            </div>
        </li>

        {{-- Productos/Servicios con submenu --}}
        <li class="nav-item">
            <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#submenuProductos" role="button" aria-expanded="false" aria-controls="submenuProductos"> Productos/Servicios <i class="bi bi-chevron-down"></i>
            </a>
            <div class="collapse" id="submenuProductos">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ps-3">
                    <li><a href="#" class="nav-link">Productos</a></li>
                    <li><a href="#" class="nav-link">Lista de Precios</a></li>
                </ul>
            </div>
        </li>

        <li class="nav-item"><a href="#" class="nav-link">Configuración General</a></li>

    </ul>
</nav>