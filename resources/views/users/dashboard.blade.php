@extends('layouts.app')

@section('title', 'Dashboard')

@section('contenido')
<h1 class="mb-3 fw-bold">Dashboard</h1>
@if (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
    <p>Bienvenido <b> {{Auth::user()->nombre }} </b> al panel de administración.</p>
@else
    <p>Bienvenido  <b> {{Auth::user()->nombre }} </b> a Tu Pedido.</p>
@endif

@if($configuracionVacia == true && (Auth::user()->rol_id == 3 || Auth::user()->rol_id == 4))
    <div class="d-inline-block position-relative">
        <button class="btn btn-primary" disabled>Nueva Cotización</button>
        <button class="btn btn-primary" disabled>Nuevo Pedido</button>
        <small class="mensaje-cambio text-danger">⚠️ {!! 'Contacte a soporte: <br> Problema de configuracion.' !!}</small>
    </div>
@elseif($configuracionVacia == true && (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2))
    <div class="d-inline-block position-relative">
        <button class="btn btn-primary" onclick="alertConfig()">Nueva Cotización</button>
        <button class="btn btn-primary" onclick="alertConfig()">Nuevo Pedido</button>
    </div>

    <script>
        function alertConfig() {
            Swal.fire({
                title: '⚠️ Configuración incompleta',
                html: `Debes terminar de configurar el sistema.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ir a Configuración',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#05564f',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if(result.isConfirmed){
                    window.location.href = "{{ route('configuracion') }}";
                }
            });
        }
    </script>
@else
    <a href="{{ route('NuevaCotizacion') }}" class="btn btn-primary">Nueva Cotización</a>
    <a href="{{ route('NuevaPedido') }}" class="btn btn-primary">Nuevo Pedido</a>
@endif

@endsection