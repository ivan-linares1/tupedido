@extends('layouts.app')

@section('title', 'Dashboard')

@section('contenido')
<h1 class="mb-3 fw-bold">Dashboard</h1>
@if (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
    <p>Bienvenido <b> {{Auth::user()->nombre }} </b> al panel de administración.</p>
@else
    <p>Bienvenido  <b> {{Auth::user()->nombre }} </b> a Tu Pedido.</p>
@endif

<a href="{{ route('NuevaCotizacion') }}" class="btn btn-primary">Nueva Cotización</a>
<a href="{{ route('NuevaPedido') }}" class="btn btn-primary">Nuevo Pedido</a>
@endsection