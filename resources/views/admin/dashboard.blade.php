@extends('layouts.app')

@section('title', 'Dashboard')

@section('contenido')
<h1>Dashboard</h1>
<p>Bienvenido al panel de administración.</p>
<a href="{{ route('cotizaciones') }}" class="btn btn-primary">Nueva Cotización</a>
@endsection