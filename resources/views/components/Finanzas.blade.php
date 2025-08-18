<div class="finanzas-section">
    <div class="col-md-4 d-flex align-items-center">
        <label for="moneda" class="form-label mb-0 me-2">Entrada en el diario</label>
        <input type="text" id="moneda" class="form-control" placeholder="Ofertas de Ventas - C05974" readonly style="width:240px;">
    </div>
    <br><br><br>
    <div class="col-md-4">
        <div class="row mb-3">
            <label for="condicionesPago" class="col-sm-5 col-form-label">Condiciones de pago:</label>
            <div class="col-sm-7">
                <select id="condicionesPago" class="form-select">
                    <option value="30">30 DÍAS</option>
                    <option value="x">x DÍAS</option>
                    <option value="x">x DÍAS</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <label for="formaPago" class="col-sm-5 col-form-label">Forma de pago:</label>
            <div class="col-sm-7">
                <select id="formaPago" class="form-select">
                    <option value="99">99</option>
                    <option value="x">x forma</option>
                    <option value="x">x forma</option>
                </select>
            </div>
        </div>

        <div class="row mb-3 align-items-start">
            <label for="periodoFechasDesc" class="col-sm-5 col-form-label">Período fechas de descuento:</label>
            <div class="col-sm-7">
                <input type="text" id="periodoFechasDesc" class="form-control w-100" placeholder="(Campo vacío)">
            </div>
        </div>

    </div>

    <div class="row mb-3">
        <div class="col-sm-12">
            <label class="form-label">Volver a calcular manualmente fecha de vencimiento:</label>
            <div class="input-group" >
                <input type="number" id="pendiente" class="form-control"  min="0" style="max-width: 200px;" readonly>
                <input type="number" id="meses" class="form-control" value="0" min="0" style="max-width: 50px;">
                <span class="input-group-text" style="max-width: 80px;">Meses +</span>
                <input type="number" id="dias" class="form-control" value="30" min="0" style="max-width: 50px;">
                <span class="input-group-text" style="max-width: 60px;">Días</span>
            </div>
        </div>
    </div>


</div>