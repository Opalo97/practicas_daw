"use strict";

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("formLogin");
  const usuario = document.getElementById("usuario");
  const password = document.getElementById("password");

  form.addEventListener("submit", (event) => {
    let mensajesError = [];

    // Quitar espacios y tabulaciones
    const nombreLimpio = usuario.value.replace(/[\s\t]+/g, "");
    const passLimpio = password.value.replace(/[\s\t]+/g, "");

    // Validaciones
    if (nombreLimpio.length === 0) {
      mensajesError.push("- Debes introducir un nombre de usuario válido.");
    }

    if (passLimpio.length === 0) {
      mensajesError.push("- Debes introducir una contraseña válida.");
    }

    // Si hay errores → mostrar en un único alert
    if (mensajesError.length > 0) {
      alert("Por favor, corrige los siguientes errores:\n\n" + mensajesError.join("\n"));
      event.preventDefault(); // Evita el envío
    } else {
      alert("Inicio de sesión correcto. ¡Bienvenido!");
    }
  });
});
