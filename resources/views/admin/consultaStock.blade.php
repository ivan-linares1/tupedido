@extends('layouts.app') 

@section('title', 'Consulta de Stock')

@section('contenido')


<div class="d-flex justify-content-center align-items-center w-100" style="min-height: 70vh;">
    <div class="card p-4 shadow-modern modern-box">

        <h4 class="mb-4 fw-bold text-center title-modern">
            Consulta de Stock
        </h4>

        <!-- SELECT2 -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Artículo</label>
            <select id="selectArticulo" class="form-control select2"></select>
        </div>

        <!-- CANTIDAD -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Cantidad</label>
            <input type="number" id="inputCantidad" class="form-control">
        </div>

        <!-- Botón -->
        <button id="btnConsultar" class="btn btn-primary w-100 btn-modern">
            Consultar
        </button>
    </div>
</div>

<style>
/* Caja más pequeña */
.modern-box {
    width: 50%;
    border-radius: 16px;
    background: var(--color-fondo);
    font-family: var(--fuente-secundaria);
    color: var(--color-texto-principal);
}

/* Sombra suave */
.shadow-modern {
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}

/* Inputs */
.form-control {
    border-radius: 10px !important;
    padding: 10px;
    transition: 0.2s;
    border: 1px solid #ccc;
    font-family: var(--fuente-secundaria);
}

.form-control:focus {
    border-color: var(--color-secundario) !important;
    box-shadow: 0 0 5px rgba(255, 106, 0, 0.35) !important; 
}

/* Título */
.title-modern {
    letter-spacing: 0.3px;
    font-family: var(--fuente-principal);
    color: var(--color-primario);
}

/* Botón moderno */
.btn-modern {
    border-radius: 12px;
    padding: 10px;
    font-weight: 600;
    border: none;
    transition: 0.2s;
    color: white;
    font-family: var(--fuente-secundaria);

    /* Color sólido primario */
    background: var(--color-primario);
}

/* Hover del botón */
.btn-modern:hover {
    background: var(--color-secundario); /* Cambia sólido al secundario */
    box-shadow: 0 5px 12px rgba(0,0,0,0.2);
}

/* Responsivo */
@media (max-width: 480px) {
    .modern-box {
        width: 100%;
        padding: 1.2rem !important;
    }
}


</style>


{{-- JS --}}
<script>
// ---- ACTIVA EL SELECT CON BUSCADOR Y PAGINACION DE LOS ARTICULOS ----
$('#selectArticulo').select2({
    placeholder: "Selecciona un artículo",
    width: '100%',
    allowClear: true,
    ajax: {
        url: "{{ route('consulta_stock.buscar') }}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                search: params.term || '',
                page: params.page || 1
            };
        },
        processResults: function (data) {
            return {
                results: data.results,
                pagination: {
                    more: data.pagination.more
                }
            };
        },
        cache: true
    }
});

// ---- CONSULTAR STOCK ----
$('#btnConsultar').on('click', function() {

    let itemCode = $('#selectArticulo').val();
    let cantidad = $('#inputCantidad').val();

    if(!itemCode){
        mensajes("Selecciona un artículo primero.");
        return;
    }

    if(!cantidad || cantidad <=0){
        mensajes("Ingresa una cantidad válida.");
        return;
    }

    function mensajes(texto){
        Swal.fire({
            icon: 'error',
            title: texto,
        });
    }

    $.ajax({
        url: "{{ route('consulta_stock.ver') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            itemCode: itemCode,
            cantidad: cantidad
        },
        success: function(response){

            if(response.success){
                Swal.fire({
                    icon: 'success',
                    title: 'Stock disponible',
                });
            }else if(response.error){
                Swal.fire({
                    icon: 'error',
                    title: 'Sin stock suficiente',
                });
            }
        },
        error: function(){
            Swal.fire({
                icon:'error',
                title:'Error',
                text:'No se pudo consultar el stock'
            });
        }
    });

});
</script>

@endsection