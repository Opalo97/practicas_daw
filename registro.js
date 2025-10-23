document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("formRegistro");
  const erroresDiv = document.getElementById("errores");

  form.addEventListener("submit", function (event) {
    event.preventDefault();
    erroresDiv.innerHTML = "";
    let errores = [];

   
    const usuario = document.getElementById("usuario").value.trim();
    const clave = document.getElementById("clave").value;
    const clave2 = document.getElementById("clave2").value;
    const email = document.getElementById("email").value.trim();
    const sexo = document.querySelector('input[name="sexo"]:checked');
    const fecha = document.getElementById("fecha").value.trim();

    

    // Nombre de usuario
    if (usuario.length < 3 || usuario.length > 15)
      errores.push("El nombre de usuario debe tener entre 3 y 15 caracteres.");
    else {
      const empiezaNumero = usuario.charCodeAt(0) >= 48 && usuario.charCodeAt(0) <= 57;
      if (empiezaNumero)
        errores.push("El nombre de usuario no puede comenzar con un número.");
      else {
        for (let c of usuario) {
          const esLetraMay = c >= "A" && c <= "Z";
          const esLetraMin = c >= "a" && c <= "z";
          const esNumero = c >= "0" && c <= "9";
          if (!esLetraMay && !esLetraMin && !esNumero) {
            errores.push("El nombre de usuario solo puede contener letras y números.");
            break;
          }
        }
      }
    }

    // Contraseña
    if (clave.length < 6 || clave.length > 15)
      errores.push("La contraseña debe tener entre 6 y 15 caracteres.");
    else {
      let mayus = false, minus = false, num = false, simbolo = false;
      for (let c of clave) {
        if (c >= "A" && c <= "Z") mayus = true;
        else if (c >= "a" && c <= "z") minus = true;
        else if (c >= "0" && c <= "9") num = true;
        else if (c === "-" || c === "_") simbolo = true;
        else {
          errores.push("La contraseña solo puede contener letras, números, guion o guion bajo.");
          break;
        }
      }
      if (!mayus || !minus || !num)
        errores.push("La contraseña debe tener al menos una mayúscula, una minúscula y un número.");
    }

    // Repetir contraseña
    if (clave !== clave2)
      errores.push("Las contraseñas no coinciden.");

    // Email (solo lógica básica sin regex)
    if (email === "")
      errores.push("El correo electrónico no puede estar vacío.");
    else {
      if (!email.includes("@") || email.startsWith("@") || email.endsWith("@"))
        errores.push("El correo electrónico debe contener una parte-local y un dominio válidos (con '@').");
      else {
        const [parteLocal, dominio] = email.split("@");
        if (parteLocal.length === 0 || dominio.length === 0)
          errores.push("Formato incorrecto de correo electrónico.");
        else if (email.length > 254)
          errores.push("La longitud máxima del correo electrónico es 254 caracteres.");
      }
    }

    // Sexo
    if (!sexo)
      errores.push("Debe seleccionar un sexo.");

    // Fecha de nacimiento y edad
    if (fecha === "")
      errores.push("Debe introducir una fecha de nacimiento.");
    else {
      const partes = fecha.split("/");
      if (partes.length !== 3)
        errores.push("El formato de fecha debe ser dd/mm/aaaa.");
      else {
        const [dia, mes, anio] = partes.map(Number);
        const fechaNacimiento = new Date(anio, mes - 1, dia);
        if (isNaN(fechaNacimiento))
          errores.push("Fecha de nacimiento no válida.");
        else {
          const hoy = new Date();
          let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
          const m = hoy.getMonth() - fechaNacimiento.getMonth();
          if (m < 0 || (m === 0 && hoy.getDate() < fechaNacimiento.getDate()))
            edad--;
          if (edad < 18)
            errores.push("Debe tener al menos 18 años cumplidos para registrarse.");
        }
      }
    }

    //  errores 
    if (errores.length > 0) {
      erroresDiv.innerHTML = "<ul><li>" + errores.join("</li><li>") + "</li></ul>";
      erroresDiv.style.color = "red";
    } else {
      alert("Registro exitoso");
      form.submit();
    }
  });
});
