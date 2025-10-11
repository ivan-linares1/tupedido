@extends('layouts.app')

@section('title', 'Panel de Sincronizadores')

@section('contenido')
<style>
/* Encapsular todos los estilos del panel */
.sincronizadores-panel {
    max-width: 1200px;
    padding: 40px 20px;
    background-color: #f0f2f5;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.sincronizadores-panel h1 {
    color: #212529;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 40px;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
}

.sincronizadores-panel .card {
    border-radius: 15px;
    transition: transform 0.3s, box-shadow 0.3s;
    cursor: pointer;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    background: #ffffff;
}

.sincronizadores-panel .card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 12px 25px rgba(0,0,0,0.25);
}

.sincronizadores-panel .card i {
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px auto;
    border-radius: 50%;
    font-size: 2rem;
    background: linear-gradient(135deg, #6f42c1, #6610f2);
    color: #fff;
    transition: transform 0.3s, box-shadow 0.3s;
}

.sincronizadores-panel .card:hover i {
    transform: scale(1.2);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.sincronizadores-panel .card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #212529;
    margin-bottom: 5px;
}

.sincronizadores-panel .card-text {
    font-size: 0.95rem;
    color: #495057;
    margin-bottom: 15px;
}

.sincronizadores-panel .btn {
    font-weight: 600;
    padding: 10px 0;
    border-radius: 10px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    transition: all 0.3s;
}

.sincronizadores-panel .btn-primary { background: #0d6efd; border: none; }
.sincronizadores-panel .btn-primary:hover { background: #0b5ed7; transform: scale(1.05); box-shadow: 0 6px 15px rgba(13,110,253,0.4); }

.sincronizadores-panel .btn-success { background: #198754; border: none; }
.sincronizadores-panel .btn-success:hover { background: #157347; transform: scale(1.05); box-shadow: 0 6px 15px rgba(25,135,84,0.4); }

@media (max-width: 768px) {
    .sincronizadores-panel .card i { width: 60px; height: 60px; font-size: 1.7rem; }
    .sincronizadores-panel .card-title { font-size: 1.1rem; }
    .sincronizadores-panel .card-text { font-size: 0.85rem; }
}
</style>

<div class="container mt-5 sincronizadores-panel">
    <h1 class="text-center">Panel de Control de Sincronizadores Manuales</h1>

    <div class="row g-4 justify-content-center">
        <!-- Botón Insertar Monedas -->
        <div class="col-md-3">
            <form action="{{ route('monedas') }}" method="POST">
                @csrf
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-currency-dollar"></i>
                        <h5 class="card-title">Insertar Monedas</h5>
                        <p class="card-text">Agregar monedas manualmente al sistema.</p>
                        <button type="submit" class="btn btn-success w-100">Ejecutar</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Botón WS - Traer Artículos -->
        <div class="col-md-3">
            <form action="{{ route('articulosWeb') }}" method="POST">
                @csrf
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-cloud-arrow-down"></i>
                        <h5 class="card-title">Traer Artículos</h5>
                        <p class="card-text">Sincroniza y trae artículos del WS manualmente.</p>
                        <button type="submit" class="btn btn-success w-100">Ejecutar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
