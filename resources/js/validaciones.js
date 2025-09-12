//solo permite que se escriban numeros en los inputs
$(document).on('input', '.cantidad', function() {
    this.value = this.value.replace(/\D/g, '');
});
