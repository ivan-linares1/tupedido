@extends('layouts.app')

@section('title', 'Dashboard')

@section('contenido')
<h1>Dashboard</h1>
<p>Bienvenido al panel de administración.</p>
<a href="{{ route('NuevaCotizacion') }}" class="btn btn-primary">Nueva Cotización</a>
<a href="{{ route('NuevaPedido') }}" class="btn btn-primary">Nueva Pedido</a>
@endsection