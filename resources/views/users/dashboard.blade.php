@extends('layouts.app')

@section('title', 'Dashboard')

@section('contenido')
<h1 class="mb-3 fw-bold">Dashboard</h1>
@if (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2) {{--Condicionado para que de la biendenida solo a los roles 1 y 2 que son administradores--}}
    <p>Bienvenido <b> {{Auth::user()->nombre }} </b> al panel de administración.</p>
@else{{--Condicionado para que de la biendenida al resto que seran clientes y vendedores--}}
    <p>Bienvenido  <b> {{Auth::user()->nombre }} </b> a KombiShop.</p>
@endif

<x-loading /> {{--animacion de cargando cuando se de click en un boton--}}

{{--aqui condiciono los botones para que esten habilitados o deshabilitados segun sea el caso el principal es que este configurado el sistema --}}
@if($configuracionVacia == true && (Auth::user()->rol_id == 3 || Auth::user()->rol_id == 4))  {{--si no esta configurado el sistema se deshabilitan y mustra un mensaje de problema de 
configuracion para los clientes y vendedores--}}
    <div class="d-inline-block position-relative">
        <button class="btn btn-primary" disabled>Nueva Cotización</button>
        <button class="btn btn-primary" disabled>Nuevo Pedido</button>
        <small class="mensaje-cambio text-danger">⚠️ {!! 'Contacte a soporte: <br> Problema de configuracion.' !!}</small>
    </div>
@elseif($configuracionVacia == true && (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)){{--si no esta configurado el sistema se deshabilitan y mustra un mensaje de problema de 
configuracion y solicita aceptacion para redireccionar a la configuracion del sistema solo es con administradores rol 1 y 2--}}
    <div class="d-inline-block position-relative">
        <button class="btn btn-primary" onclick="alertConfig()">Nueva Cotización</button>
        <button class="btn btn-primary" onclick="alertConfig()">Nuevo Pedido</button>
    </div>

    <script> //alerta para mostrar el mensaje de redireccioonamiento a los administradores
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
@else {{--todo esta configurado y si estan habilidado los botones para su perfecto funcionamineto--}}
    <a href="{{ route('NuevaCotizacion') }}" class="btn btn-primary" data-loading="true">Nueva Cotización</a>
    <a href="{{ route('NuevaPedido') }}" class="btn btn-primary" data-loading="true">Nuevo Pedido</a>
@endif

@endsection