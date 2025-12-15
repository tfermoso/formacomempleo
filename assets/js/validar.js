function validarFormulario(form) {
    let email = form.email.value;
    let pass = form.password.value;
    if (!email || !pass) {
        alert("Completa todos los campos");
        return false;
    }
    return true;
}
