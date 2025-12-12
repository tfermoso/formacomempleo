//Validación frontend

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("formRegistroEmpresa");

  if (!form) return;

  form.addEventListener("submit", function (e) {
    let errores = [];

    const cif = form.cif.value.trim().toUpperCase();
    const emailEmpresa = form.email_empresa.value.trim();
    const emailUsuario = form.email_usuario.value.trim();
    const password = form.password.value;
    const password2 = form.password2.value;
    const telefonoUsuario = form.telefono_usuario.value.trim();

    // CIF: patrón básico + longitud
    const regexCIF = /^[ABCDEFGHJNPQRSUVW]\d{7}[0-9A-J]$/;
    if (!regexCIF.test(cif)) {
      errores.push("El CIF no tiene un formato válido.");
    }

    // Email empresa
    if (!validarEmail(emailEmpresa)) {
      errores.push("El email de la empresa no es válido.");
    }

    // Email usuario
    if (!validarEmail(emailUsuario)) {
      errores.push("El email del usuario no es válido.");
    }

    // Teléfono usuario obligatorio (puedes mejorar la regex)
    if (telefonoUsuario === "") {
      errores.push("El teléfono del usuario es obligatorio.");
    }

    // Passwords
    if (password.length < 8) {
      errores.push("La contraseña debe tener al menos 8 caracteres.");
    }

    if (password !== password2) {
      errores.push("Las contraseñas no coinciden.");
    }

    if (errores.length > 0) {
      e.preventDefault();
      alert(errores.join("\n"));
    }
  });
});

function validarEmail(email) {
  // Validación sencilla
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}
