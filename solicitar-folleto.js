"use strict";

document.addEventListener("DOMContentLoaded", () => {
  const boton = document.getElementById("mostrarTabla");
  const contenedor = document.getElementById("contenedorTabla");
  let visible = false;

  boton.addEventListener("click", () => {
    visible = !visible;
    contenedor.style.display = visible ? "block" : "none";
    boton.textContent = visible ? "Ocultar tabla de costes" : "Mostrar tabla de costes";

    if (visible && contenedor.childElementCount === 0) {
      crearTablaCostes(contenedor);
    }
  });
});

function crearTablaCostes(contenedor) {
  // Tarifas base (idénticas a las de la tabla de tarifas)
  const COSTE_ENVIO = 10;
  const TARIFAS_PAGINAS = [
    { bloque: "< 5 páginas", max: 4, precio: 2.0 },
    { bloque: "5–10 páginas", max: 10, precio: 1.8 },
    { bloque: "> 10 páginas", max: Infinity, precio: 1.6 }
  ];
  const PRECIO_COLOR = 0.5;
  const PRECIO_RES_ALTA = 0.2;

  const fotosList = [1, 3, 5];

  // Crear tabla sin estilos inline
  const tabla = document.createElement("table");

  // Cabecera
  const thead = document.createElement("thead");
  const filaCab = document.createElement("tr");
  const cabeceras = [
    "Bloque de páginas",
    "Nº fotos",
    "B/N 150–300 dpi",
    "B/N >300 dpi",
    "Color 150–300 dpi",
    "Color >300 dpi"
  ];
  cabeceras.forEach(texto => {
    const th = document.createElement("th");
    th.textContent = texto;
    filaCab.appendChild(th);
  });
  thead.appendChild(filaCab);
  tabla.appendChild(thead);

  // Cuerpo
  const tbody = document.createElement("tbody");

  TARIFAS_PAGINAS.forEach(tramo => {
    fotosList.forEach(fotos => {
      const fila = document.createElement("tr");

      const celdaBloque = document.createElement("td");
      celdaBloque.textContent = tramo.bloque;
      fila.appendChild(celdaBloque);

      const celdaFotos = document.createElement("td");
      celdaFotos.textContent = fotos;
      fila.appendChild(celdaFotos);

      // Combinaciones de color y resolución
      const combinaciones = [
        { color: false, altaRes: false },
        { color: false, altaRes: true },
        { color: true, altaRes: false },
        { color: true, altaRes: true }
      ];

      combinaciones.forEach(comb => {
        const td = document.createElement("td");
        const coste = calcularCoste(
          tramo.max === Infinity ? 12 : tramo.max,
          fotos,
          comb.color,
          comb.altaRes,
          COSTE_ENVIO,
          tramo.precio,
          PRECIO_COLOR,
          PRECIO_RES_ALTA
        );
        td.textContent = coste.toFixed(2) + " €";
        fila.appendChild(td);
      });

      tbody.appendChild(fila);
    });
  });

  tabla.appendChild(tbody);
  contenedor.appendChild(tabla);
}

function calcularCoste(paginas, fotos, esColor, altaRes, envio, precioPagina, extraColor, extraRes) {
  let coste = envio + paginas * precioPagina;
  if (esColor) coste += fotos * extraColor;
  if (altaRes) coste += fotos * extraRes;
  return coste;
}
