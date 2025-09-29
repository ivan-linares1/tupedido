@extends('layouts.app')

@section('title', 'Dashboard')

@section('contenido')
<h1 class="mb-3 fw-bold">Dashboard</h1>
<p>Bienvenido</p>
<a href="{{ route('NuevaCotizacion') }}" class="btn btn-primary">Nueva Cotización</a>
<a href="{{ route('NuevaPedido') }}" class="btn btn-primary">Nuevo Pedido</a>
@endsection