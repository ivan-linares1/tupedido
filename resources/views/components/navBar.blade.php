{{-- resources/views/components/navbar-horizontal.blade.php para una proxima version donde quieran el menu de navegacion arriba--}}
<nav class="navbar-horizontal shadow-sm">
    <div class="container">
        <!-- Logo izquierdo -->
        <div class="logo-section">
            <img src="{{ asset('storage/logos/logocarrito.jpg') }}" alt="Logo" class="logo-img">
            <a href="{{ route('dashboard') }}" class="brand-text">KOMBITEC</a>
        </div>

        <!-- Menú principal -->
        <ul class="nav-links">
            <li><a href="{{ route('dashboard') }}">Dashboard</a></li>

            <!-- Ventas -->
            <li class="dropdown">
                <a href="#" class="dropbtn">Ventas <i class="bi bi-chevron-down"></i></a>
                <ul class="dropdown-menu">
                    @if (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                        <li><a href="{{ route('clientes') }}">Clientes</a></li>
                    @endif
                    <li><a href="{{ route('cotizaciones') }}">Cotizaciones</a></li>
                    <li><a href="{{ route('Pedidos') }}">Pedidos</a></li>
                    @if (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                        <li class="dropdown-submenu">
                            <a href="#">Configuración <i class="bi bi-chevron-right"></i></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Cartera de clientes</a></li>
                                <li><a href="{{ route('admin.catalogo.vendedores') }}">Catálogo de vendedores</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </li>

            <!-- Productos -->
            <li class="dropdown">
                <a href="#" class="dropbtn">Productos/Servicios <i class="bi bi-chevron-down"></i></a>
                <ul class="dropdown-menu">
                    <li><a href="{{ route('articulos', ['estatus' => 'Activos']) }}">Productos</a></li>
                    @if (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                        <li class="dropdown-submenu">
                            <a href="#">Configuración <i class="bi bi-chevron-right"></i></a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('admin.marcas.index') }}">Grupos de Productos</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </li>

            <!-- Configuración General -->
            @if (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                <li class="dropdown">
                    <a href="#" class="dropbtn">Configuración General <i class="bi bi-chevron-down"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('configuracion') }}">Configurar Sistema</a></li>
                        <li><a href="{{ route('usuarios') }}">Usuarios</a></li>
                    </ul>
                </li>
            @endif

            @if (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                <li><a href="{{ route('insertar.monedas') }}">Insertar monedas</a></li>
            @endif
        </ul>

        <!-- Usuario -->
        <div class="user-dropdown">
            <button class="user-button">
                <svg class="user-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 10a4 4 0 100-8 4 4 0 000 8zm-7 8a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
                <span class="user-name">{{ Auth::user()->nombre }}</span>
                <i class="bi bi-chevron-down"></i>
            </button>
            <div class="dropdown-menu">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" ><i class="bi bi-box-arrow-right"></i> Cerrar sesión</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        :root {
            --color-primario: #05564f;
            --color-secundario: #ff6a00;
            --color-fondo: #ffffff;
            --color-texto: #ffffff;
            --fuente-principal: 'Lato', sans-serif;
            --fuente-secundaria: 'Catamaran', sans-serif;
        }

        /* === NAVBAR BASE === */
        .navbar-horizontal {
            background-color: var(--color-primario);
            font-family: var(--fuente-principal);
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-horizontal .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0.6rem 1.5rem;
        }

        /* === LOGO === */
        .logo-section {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .logo-img {
            height: 45px;
            width: auto;
            border-radius: 6px;
        }

        .brand-text {
            font-family: var(--fuente-secundaria);
            font-weight: 900;
            font-size: 1.6rem;
            color: var(--color-texto);
            text-decoration: none;
            letter-spacing: 1px;
        }

        /* === LINKS PRINCIPALES === */
        .nav-links {
            display: flex;
            gap: 1.5rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-links li {
            position: relative;
        }

        .nav-links li a {
            color: var(--color-texto);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.5rem 0.8rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .nav-links li a:hover {
            background-color: var(--color-secundario);
            color: white;
        }

        /* === DROPDOWNS === */
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: var(--color-primario);
            border-radius: 0.4rem;
            min-width: 200px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 999;
            padding: 0.3rem 0;
        }

        .dropdown:hover > .dropdown-menu {
            display: block;
        }

        .dropdown-menu li {
            width: 100%;
        }

        .dropdown-menu a {
            color: #e0e0e0;
            display: block;
            padding: 0.5rem 1rem;
            font-weight: 500;
            text-decoration: none;
        }

        .dropdown-menu a:hover {
            background-color: var(--color-secundario);
            color: white;
        }

        /* Submenú lateral */
        .dropdown-submenu {
            position: relative;
        }

        .dropdown-submenu .dropdown-menu {
            top: 0;
            left: 100%;
            margin-left: 0.5rem;
        }

        .dropdown-submenu:hover > .dropdown-menu {
            display: block;
        }

        /* === USUARIO === */
        .user-dropdown {
            position: relative;
        }

        .user-button {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            background-color: transparent;
            border: 1px solid rgba(255,255,255,0.6);
            border-radius: 9999px;
            padding: 0.3rem 0.7rem;
            cursor: pointer;
            color: var(--color-texto);
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .user-button:hover {
            background-color: var(--color-secundario);
            border-color: var(--color-secundario);
        }

        .user-icon {
            width: 20px;
            height: 20px;
        }

        .user-dropdown .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            background-color: var(--color-primario);
            border-radius: 0.4rem;
            min-width: 160px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .user-dropdown .dropdown-menu button {
            width: 100%;
            background: rgb(255, 255, 255); 
            border: none;
            color: rgb(0, 0, 0);
            text-align: left;
            padding: 0.6rem 1rem;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.9rem;
            border-radius: 0.25rem;
        }

        .user-dropdown .dropdown-menu button:hover {
            background-color: #fa0606; 
             color: rgb(255, 255, 255);
        }

        .user-dropdown.active .dropdown-menu {
            display: block;
        }

        /* === RESPONSIVE === */
        @media (max-width: 992px) {
            .nav-links {
                display: none;
            }
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const userDropdown = document.querySelector(".user-dropdown");
            const toggleButton = userDropdown.querySelector(".user-button");
            const menu = userDropdown.querySelector(".dropdown-menu");

            toggleButton.addEventListener("click", function(e) {
                e.stopPropagation();
                menu.style.display = menu.style.display === "block" ? "none" : "block";
            });

            document.addEventListener("click", function(e) {
                if (!userDropdown.contains(e.target)) {
                    menu.style.display = "none";
                }
            });
        });
    </script>
</nav>
