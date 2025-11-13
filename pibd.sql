-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-11-2025 a las 10:07:02
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
-- Base de datos: `pibd`
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposanuncios`
--

CREATE TABLE `tiposanuncios` (
  `IdTAnuncio` tinyint(3) UNSIGNED NOT NULL,
  `NomTAnuncio` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposmensajes`
--

CREATE TABLE `tiposmensajes` (
  `IdTMensaje` tinyint(3) UNSIGNED NOT NULL,
  `NomTMensaje` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposviviendas`
--

CREATE TABLE `tiposviviendas` (
  `IdTVivienda` tinyint(3) UNSIGNED NOT NULL,
  `NomTVivienda` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  MODIFY `IdAnuncio` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estilos`
--
ALTER TABLE `estilos`
  MODIFY `IdEstilo` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `fotos`
--
ALTER TABLE `fotos`
  MODIFY `IdFoto` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `IdMensaje` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `paises`
--
ALTER TABLE `paises`
  MODIFY `IdPais` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `IdSolicitud` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tiposanuncios`
--
ALTER TABLE `tiposanuncios`
  MODIFY `IdTAnuncio` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tiposmensajes`
--
ALTER TABLE `tiposmensajes`
  MODIFY `IdTMensaje` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tiposviviendas`
--
ALTER TABLE `tiposviviendas`
  MODIFY `IdTVivienda` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `IdUsuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

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
