-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-12-2025 a las 13:39:55
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `pibd2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anuncios`
--

CREATE TABLE `anuncios` (
  `IdAnuncio` int(10) UNSIGNED NOT NULL,
  `TAnuncio` tinyint(3) UNSIGNED NOT NULL,
  `TVivienda` tinyint(3) UNSIGNED NOT NULL,
  `FPrincipal` varchar(255) DEFAULT NULL,
  `Alternativo` varchar(255) NOT NULL,
  `Titulo` varchar(255) NOT NULL,
  `Precio` decimal(10,2) NOT NULL,
  `Texto` text DEFAULT NULL,
  `Ciudad` varchar(100) DEFAULT NULL,
  `Pais` int(10) UNSIGNED DEFAULT NULL,
  `Superficie` decimal(10,2) DEFAULT NULL,
  `NHabitaciones` int(10) UNSIGNED DEFAULT NULL,
  `NBanyos` int(10) UNSIGNED DEFAULT NULL,
  `Planta` int(11) DEFAULT NULL,
  `Anyo` int(11) DEFAULT NULL,
  `FRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  `Usuario` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `anuncios`
--

INSERT INTO `anuncios` (`IdAnuncio`, `TAnuncio`, `TVivienda`, `FPrincipal`, `Alternativo`, `Titulo`, `Precio`, `Texto`, `Ciudad`, `Pais`, `Superficie`, `NHabitaciones`, `NBanyos`, `Planta`, `Anyo`, `FRegistro`, `Usuario`) VALUES
(1, 2, 1, 'img/piso.jpg', 'Piso reformado en Alicante', 'Piso en Alicante centro', 235000.00, 'Vivienda reformada con 3 habitaciones y 2 baños...', 'Alicante', 1, 95.00, 3, 2, 4, 2008, '2025-05-10 10:00:00', 1),
(2, 1, 2, 'img/foto_piso5.jpeg', 'Ático con terraza en París', 'Ático con terraza y vistas al río', 450000.00, 'Ático con terraza y vistas al río, ideal para parejas...', 'París', 2, 120.00, 2, 1, 5, 2015, '2025-07-02 18:30:00', 2),
(3, 1, 1, 'img/foto_piso.jpg\n', 'Piso en Madrid centro', 'Piso en Madrid centro', 320000.00, 'Piso luminoso en el centro de Madrid con balcón.', 'Madrid', 1, 90.00, 3, 2, 3, 2010, '2025-08-01 09:00:00', 1),
(4, 2, 1, 'img/casa1.jpg', 'Casa en Roma', 'Casa en Roma', 280000.00, 'Casa familiar con jardín en Roma con jardín y sótano.', 'Roma', 3, 110.00, 4, 2, 2, 2005, '2025-08-15 16:00:00', 2),
(5, 1, 2, 'img/piso3.jpg', 'Ático en Lisboa', 'Ático en Lisboa', 310000.00, 'Ático con vistas al río Tajo en Lisboa.', 'Lisboa', 4, 85.00, 2, 1, 6, 2012, '2025-09-01 11:00:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estilos`
--

CREATE TABLE `estilos` (
  `IdEstilo` int(10) UNSIGNED NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Descripcion` text DEFAULT NULL,
  `Fichero` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `estilos`
--

INSERT INTO `estilos` (`IdEstilo`, `Nombre`, `Descripcion`, `Fichero`) VALUES
(1, 'Modo oscuro', 'Hoja de estilo modo oscuro', 'modo-oscuro.css'),
(2, 'Básico', 'Hoja de estilo básica', 'basic.css'),
(3, 'Accesible', 'Hoja de estilo accesible', 'accesible.css'),
(4, 'Alto contraste', 'Hoja de estilo en alto contraste', 'alto-contraste.css'),
(5, 'Letra grande', 'Hoja de estilo con letra más grande', 'letra_grande.css');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fotos`
--

CREATE TABLE `fotos` (
  `IdFoto` int(10) UNSIGNED NOT NULL,
  `Titulo` varchar(255) DEFAULT NULL,
  `Foto` varchar(255) NOT NULL,
  `Alternativo` varchar(255) NOT NULL,
  `Anuncio` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `fotos`
--

INSERT INTO `fotos` (`IdFoto`, `Titulo`, `Foto`, `Alternativo`, `Anuncio`) VALUES
(1, NULL, 'img/foto_piso.jpg', 'Foto principal del piso', 1),
(2, NULL, 'img/foto_piso2.jpg', 'Salón del piso', 1),
(5, NULL, 'img/foto_piso5.jpeg', 'Baño principal', 1),
(6, NULL, 'img/foto_piso6.jpeg', 'Balcón del piso', 1),
(7, NULL, 'img/piso.jpg', 'Foto principal del ático', 2),
(9, NULL, 'img/piso3.jpeg', 'Salón del ático', 2),
(13, NULL, 'img/foto_piso.jpg', 'Foto principal', 3),
(25, NULL, 'img/piso3.jpeg', 'Foto principal del ático', 5),
(31, 'Salon', 'img/foto_piso1.jpg', 'Curiosidades Fascinantes', 1),
(32, 'Salom', 'img/foto_piso2.jpg', 'Curiosidades Fas', 1),
(33, 'Salon', 'img/foto_piso2.jpg', 'Curiosidades Fas', 3),
(43, 'Salon', 'img/anuncios/anun_1_5_1765365845_0b3a4b33.jpg', 'Curiosidades Fascinantes sobre la Música', 5),
(44, 'Salon', 'img/anuncios/anun_1_1_1765366080_00566a83.jpg', 'Curiosidades Fas', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `IdMensaje` int(10) UNSIGNED NOT NULL,
  `TMensaje` tinyint(3) UNSIGNED NOT NULL,
  `Texto` text DEFAULT NULL,
  `Anuncio` int(10) UNSIGNED NOT NULL,
  `UsuOrigen` int(10) UNSIGNED NOT NULL,
  `UsuDestino` int(10) UNSIGNED NOT NULL,
  `FRegistro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`IdMensaje`, `TMensaje`, `Texto`, `Anuncio`, `UsuOrigen`, `UsuDestino`, `FRegistro`) VALUES
(1, 2, 'Hola, me interesa tu anuncio', 1, 1, 1, '2025-11-13 23:12:21'),
(2, 1, 'Gracias por tu interés', 1, 2, 2, '2025-11-13 23:12:21'),
(3, 1, 'Hola que tal', 5, 1, 1, '2025-11-26 13:41:22'),
(4, 2, 'holi', 5, 1, 1, '2025-11-26 13:57:38'),
(5, 3, 'hola hola', 2, 1, 2, '2025-11-26 13:59:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paises`
--

CREATE TABLE `paises` (
  `IdPais` int(10) UNSIGNED NOT NULL,
  `NomPais` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `paises`
--

INSERT INTO `paises` (`IdPais`, `NomPais`) VALUES
(1, 'España'),
(2, 'Francia'),
(3, 'Italia'),
(4, 'Portugal');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `IdSolicitud` int(10) UNSIGNED NOT NULL,
  `Anuncio` int(10) UNSIGNED NOT NULL,
  `Texto` text DEFAULT NULL,
  `Nombre` varchar(200) DEFAULT NULL,
  `Email` varchar(254) DEFAULT NULL,
  `Direccion` text DEFAULT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `Color` varchar(50) DEFAULT NULL,
  `Copias` int(11) DEFAULT NULL,
  `Resolucion` int(11) DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  `IColor` tinyint(1) DEFAULT NULL,
  `IPrecio` tinyint(1) DEFAULT NULL,
  `FRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  `Coste` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `solicitudes`
--

INSERT INTO `solicitudes` (`IdSolicitud`, `Anuncio`, `Texto`, `Nombre`, `Email`, `Direccion`, `Telefono`, `Color`, `Copias`, `Resolucion`, `Fecha`, `IColor`, `IPrecio`, `FRegistro`, `Coste`) VALUES
(1, 3, '', 'AgregarProductoAlCarritoCP', 'juan@example.com', 'asdf 234, 43534 ghfd, fgfgfgfgfg', '', '#cf1717', 4, 150, '2025-11-28', 1, 1, '2025-11-25 23:04:52', 54.00),
(2, 5, '', 'AgregarProductoAlCarritoCP', 'juan@example.com', 'asdf 234, 43534 ghfd, df', '', '#000000', 1, 150, '0000-00-00', 0, 1, '2025-11-26 13:39:38', 12.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposanuncios`
--

CREATE TABLE `tiposanuncios` (
  `IdTAnuncio` tinyint(3) UNSIGNED NOT NULL,
  `NomTAnuncio` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tiposanuncios`
--

INSERT INTO `tiposanuncios` (`IdTAnuncio`, `NomTAnuncio`) VALUES
(1, 'Venta'),
(2, 'Alquiler');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposmensajes`
--

CREATE TABLE `tiposmensajes` (
  `IdTMensaje` tinyint(3) UNSIGNED NOT NULL,
  `NomTMensaje` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tiposmensajes`
--

INSERT INTO `tiposmensajes` (`IdTMensaje`, `NomTMensaje`) VALUES
(1, 'Más información'),
(2, 'Solicitar una cita'),
(3, 'Comunicar una oferta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposviviendas`
--

CREATE TABLE `tiposviviendas` (
  `IdTVivienda` tinyint(3) UNSIGNED NOT NULL,
  `NomTVivienda` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tiposviviendas`
--

INSERT INTO `tiposviviendas` (`IdTVivienda`, `NomTVivienda`) VALUES
(1, 'Vivienda'),
(2, 'Ático');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `IdUsuario` int(10) UNSIGNED NOT NULL,
  `NomUsuario` varchar(15) NOT NULL,
  `Clave` varchar(255) NOT NULL,
  `Email` varchar(254) NOT NULL,
  `Sexo` tinyint(3) UNSIGNED DEFAULT NULL,
  `FNacimiento` date DEFAULT NULL,
  `Ciudad` varchar(100) DEFAULT NULL,
  `Pais` int(10) UNSIGNED DEFAULT NULL,
  `Foto` varchar(255) DEFAULT NULL,
  `FRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  `Estilo` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`IdUsuario`, `NomUsuario`, `Clave`, `Email`, `Sexo`, `FNacimiento`, `Ciudad`, `Pais`, `Foto`, `FRegistro`, `Estilo`) VALUES
(1, 'juan', '$2y$10$crGvxa2uPy0brEdXKvfjN.wdMK6TENIH5TrAec.Xpyp62IMP9S/uS', 'juan@example.com', 1, NULL, NULL, 4, 'img/usuarios/usr_69395d58d1ce8.jpg', '2025-11-13 10:40:00', 1),
(2, 'maria', '$2y$10$fBRFjAH/asaGCbC4bk.RtOLi.Lps5ry4dVZIbJOTEiSI3FTodcFzm', 'maria@example.com', NULL, NULL, NULL, NULL, 'img/maria.jpg', '2025-11-13 10:40:00', 2),
(8, 'lolita', '$2y$10$JkxUP4V/Zoriwu95D.mjN.nkbUr15BvKZKQS9FKWucClSpuj7DhdG', 'lolita@gmail.com', 2, '1999-10-10', NULL, 1, 'img/usuarios/usr_693962bfacb12.jpg', '2025-11-27 22:09:16', 2),
(9, 'pere', '$2y$10$zYns3kuTJjv/hgN9Jynzd.hs61Kc1lWiumKgXMQYc0gM03oxA367q', 'pere@gmail.com', 1, '1999-03-02', '0', 1, NULL, '2025-11-27 22:12:03', 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `anuncios`
--
ALTER TABLE `anuncios`
  ADD PRIMARY KEY (`IdAnuncio`),
  ADD KEY `fk_anuncios_tipoanuncio` (`TAnuncio`),
  ADD KEY `fk_anuncios_tipovivienda` (`TVivienda`),
  ADD KEY `fk_anuncios_pais` (`Pais`),
  ADD KEY `fk_anuncios_usuario` (`Usuario`);

--
-- Indices de la tabla `estilos`
--
ALTER TABLE `estilos`
  ADD PRIMARY KEY (`IdEstilo`);

--
-- Indices de la tabla `fotos`
--
ALTER TABLE `fotos`
  ADD PRIMARY KEY (`IdFoto`),
  ADD KEY `fk_fotos_anuncio` (`Anuncio`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`IdMensaje`),
  ADD KEY `fk_mensajes_tipomensaje` (`TMensaje`),
  ADD KEY `fk_mensajes_anuncio` (`Anuncio`),
  ADD KEY `fk_mensajes_origen` (`UsuOrigen`),
  ADD KEY `fk_mensajes_destino` (`UsuDestino`);

--
-- Indices de la tabla `paises`
--
ALTER TABLE `paises`
  ADD PRIMARY KEY (`IdPais`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`IdSolicitud`),
  ADD KEY `fk_solicitudes_anuncio` (`Anuncio`);

--
-- Indices de la tabla `tiposanuncios`
--
ALTER TABLE `tiposanuncios`
  ADD PRIMARY KEY (`IdTAnuncio`);

--
-- Indices de la tabla `tiposmensajes`
--
ALTER TABLE `tiposmensajes`
  ADD PRIMARY KEY (`IdTMensaje`);

--
-- Indices de la tabla `tiposviviendas`
--
ALTER TABLE `tiposviviendas`
  ADD PRIMARY KEY (`IdTVivienda`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`IdUsuario`),
  ADD UNIQUE KEY `NomUsuario` (`NomUsuario`),
  ADD KEY `fk_usuarios_pais` (`Pais`),
  ADD KEY `fk_usuarios_estilo` (`Estilo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `anuncios`
--
ALTER TABLE `anuncios`
  MODIFY `IdAnuncio` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `estilos`
--
ALTER TABLE `estilos`
  MODIFY `IdEstilo` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `fotos`
--
ALTER TABLE `fotos`
  MODIFY `IdFoto` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `IdMensaje` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `paises`
--
ALTER TABLE `paises`
  MODIFY `IdPais` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `IdSolicitud` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tiposanuncios`
--
ALTER TABLE `tiposanuncios`
  MODIFY `IdTAnuncio` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tiposmensajes`
--
ALTER TABLE `tiposmensajes`
  MODIFY `IdTMensaje` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tiposviviendas`
--
ALTER TABLE `tiposviviendas`
  MODIFY `IdTVivienda` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `IdUsuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `anuncios`
--
ALTER TABLE `anuncios`
  ADD CONSTRAINT `fk_anuncios_pais` FOREIGN KEY (`Pais`) REFERENCES `paises` (`IdPais`),
  ADD CONSTRAINT `fk_anuncios_tipoanuncio` FOREIGN KEY (`TAnuncio`) REFERENCES `tiposanuncios` (`IdTAnuncio`),
  ADD CONSTRAINT `fk_anuncios_tipovivienda` FOREIGN KEY (`TVivienda`) REFERENCES `tiposviviendas` (`IdTVivienda`),
  ADD CONSTRAINT `fk_anuncios_usuario` FOREIGN KEY (`Usuario`) REFERENCES `usuarios` (`IdUsuario`);

--
-- Filtros para la tabla `fotos`
--
ALTER TABLE `fotos`
  ADD CONSTRAINT `fk_fotos_anuncio` FOREIGN KEY (`Anuncio`) REFERENCES `anuncios` (`IdAnuncio`);

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `fk_mensajes_anuncio` FOREIGN KEY (`Anuncio`) REFERENCES `anuncios` (`IdAnuncio`),
  ADD CONSTRAINT `fk_mensajes_destino` FOREIGN KEY (`UsuDestino`) REFERENCES `usuarios` (`IdUsuario`),
  ADD CONSTRAINT `fk_mensajes_origen` FOREIGN KEY (`UsuOrigen`) REFERENCES `usuarios` (`IdUsuario`),
  ADD CONSTRAINT `fk_mensajes_tipomensaje` FOREIGN KEY (`TMensaje`) REFERENCES `tiposmensajes` (`IdTMensaje`);

--
-- Filtros para la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `fk_solicitudes_anuncio` FOREIGN KEY (`Anuncio`) REFERENCES `anuncios` (`IdAnuncio`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_estilo` FOREIGN KEY (`Estilo`) REFERENCES `estilos` (`IdEstilo`),
  ADD CONSTRAINT `fk_usuarios_pais` FOREIGN KEY (`Pais`) REFERENCES `paises` (`IdPais`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
