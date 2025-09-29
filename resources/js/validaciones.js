//solo permite que se escriban numeros en los inputs
$(document).on('input', '.cantidad, #telefono', function() {
    this.value = this.value.replace(/[^0-9]/g, ''); 
});

$(document).on('keydown', '.cantidad, #telefono', function(e) {
    if (["e", "E", "+", "-", "."].includes(e.key)) {
        e.preventDefault();
    }
});
