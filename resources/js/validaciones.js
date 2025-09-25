//solo permite que se escriban numeros en los inputs
$(document).on('input', '.cantidad', function() {
    this.value = this.value.replace(/[^0-9]/g, ''); 
});

$(document).on('keydown', '.cantidad', function(e) {
    if (["e", "E", "+", "-", "."].includes(e.key)) {
        e.preventDefault();
    }
});
