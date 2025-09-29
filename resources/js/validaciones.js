//solo permite que se escriban numeros en los inputs
$(document).on('input', '.cantidad, #telefono', function() {
    this.value = this.value.replace(/[^0-9]/g, ''); 
});

//No permite que se inserten las las letras e y algunos simbolos de operacion y el punto
$(document).on('keydown', '.cantidad, #telefono', function(e) {
    if (["e", "E", "+", "-", "."].includes(e.key)) {
        e.preventDefault();
    }
});


document.addEventListener("DOMContentLoaded", function () {
    const flashContainer = document.getElementById("flash-messages");

    // Función para mostrar mensajes tipo Bootstrap
    function showFlash(message, type = "success") {
        const div = document.createElement("div");
        div.className = `alert alert-${type} alert-dismissible fade show mt-2`;
        div.setAttribute("role", "alert");
        div.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        flashContainer.appendChild(div);

        // Quitar después de 3s
        setTimeout(() => {
            div.classList.remove("show");
            div.classList.add("fade");
            setTimeout(() => div.remove(), 300);
        }, 3000);
    }


    document.querySelectorAll(".toggle-estado").forEach(input => {
        input.addEventListener("change", function () {
            const id = this.dataset.id;           // id del registro
            const url = this.dataset.url;         // ruta del controlador
            const field = this.dataset.field;     // campo a actualizar
            const value = this.checked ? 'Y' : 'N'; // valor Y/N

            // Confirmación antes de enviar
            if (!confirm("¿Seguro que deseas cambiar el estado?")) {
                this.checked = !this.checked; // revertir si cancela
                return;
            }

            fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: new URLSearchParams({
                    id: id,
                    field: field,
                    value: value
                })
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    this.checked = !this.checked;
                    showFlash("❌ Error al actualizar", "danger");
                } else {
                    showFlash("✅ Estado actualizado correctamente", "success");
                }
            })
            .catch(() => {
                this.checked = !this.checked;
                showFlash("⚠️ Error de conexión", "danger");
            });
        });
    });
});
