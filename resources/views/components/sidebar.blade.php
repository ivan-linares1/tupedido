@if(session('success'))
    <script>alert("{{ session('success') }}");</script>
@endif
@if(session('error'))
    <script>alert("{{ session('error') }}");</script>
@endif

<nav class="sidebar d-flex flex-column">
    <!-- Botón cerrar sidebar (icono flecha) -->
    <button id="sidebarClose" class="d-md-none">
        <i class="bi bi-arrow-left"></i>
    </button>

    <h1>Tu pedido</h1>
    <a href="https://www.kombitec.com.mx/" target="_blank" class="fs-4">KOMBITEC</a>

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
            <div class="collapse" id="submenuVentas" data-bs-parent=".sidebar">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ps-3">
                    <li><a href="{{ url('/work') }}" class="nav-link">Clientes</a></li>
                    <li><a href="#" class="nav-link">Cotizaciones</a></li>
                    <li><a href="#" class="nav-link">Pedidos</a></li>
                    <li>
                        <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#submenuConfiguracionVentas" role="button" aria-expanded="false" aria-controls="submenuConfiguracionVentas">
                        Configuración
                        <i class="bi bi-chevron-down"></i>
                        </a>
                        <div class="collapse" id="submenuConfiguracionVentas" data-bs-parent="#submenuVentas">
                            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ps-3">
                                <li><a href="#" class="nav-link">Zonas de venta</a></li>
                                <li><a href="#" class="nav-link">Catalogo de vendedores</a></li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </li>

        {{-- Productos/Servicios con submenu --}}
        <li class="nav-item">
            <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#submenuProductos" role="button" aria-expanded="false" aria-controls="submenuProductos">
            Productos/Servicios
            <i class="bi bi-chevron-down"></i>
            </a>
            <div class="collapse" id="submenuProductos" data-bs-parent=".sidebar">
            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ps-3">
                <li><a href="{{ route('articulos') }}" class="nav-link">Productos</a></li>
                <li><a href="#" class="nav-link">Lista de Precios</a></li>
                <li>
                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#submenuConfiguracionProductos" role="button" aria-expanded="false" aria-controls="submenuConfiguracionProductos">
                    Configuración
                    <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="collapse" id="submenuConfiguracionProductos" data-bs-parent="#submenuProductos">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ps-3">
                            <li><a href="#" class="nav-link">Grupos de Productos</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
            </div>
        </li>

        {{-- Configuracion general --}}
        <li class="nav-item">
            <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#submenuConfiguracionGeneral" role="button" aria-expanded="false" aria-controls="submenuConfiguracionGeneral">
            Configuración General   
            <i class="bi bi-chevron-down"></i>
            </a>
            <div class="collapse" id="submenuConfiguracionGeneral" data-bs-parent=".sidebar">
            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ps-3">
                <li><a href="#" class="nav-link">Configurar Sistema</a></li>
                <li><a href="#" class="nav-link">Configurar Multimoneda</a></li>
                <li><a href="{{ route('usuarios') }}" class="nav-link">Usuarios</a></li>
                <li><a href="#" class="nav-link">Perfiles</a></li>
            </ul>
            </div>
        </li>

        {{--Boton auxiliar para mandar las monedas a la base del dia de hoy //borrar cuando este en produccion*****************--}}
        <li>
            <a href="{{ route ('insertar.monedas') }}"  class="nav-link">Insertar monedas</a>
        </li>
    </ul>
</nav>