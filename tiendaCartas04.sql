-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-12-2021 a las 19:07:58
-- Versión del servidor: 10.4.21-MariaDB
-- Versión de PHP: 8.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tienda`
--
CREATE DATABASE IF NOT EXISTS `tienda` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `tienda`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `idUsuario` int(11) DEFAULT NULL,
  `idCarta` int(11) DEFAULT NULL,
  `fechaCompra` date NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carta`
--

CREATE TABLE `carta` (
  `id` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `formato` varchar(50) NOT NULL,
  `clan` varchar(50) NOT NULL,
  `grado` int(11) NOT NULL,
  `precio` float NOT NULL,
  `imagen` varchar(50) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 40,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `carta`
--

INSERT INTO `carta` (`id`, `nombre`, `formato`, `clan`, `grado`, `precio`, `imagen`, `stock`, `estado`) VALUES
(1, 'Super Dimensional Robo, Daikaiser', 'V Standard', 'Dimension Police', 3, 20, 'daikaiser', 26, 'activo'),
(2, 'Bluish Flame Liberator, Prominence Core', 'V Standard', 'Gold Paladin', 3, 17, 'prominencecore', 50, 'activo'),
(3, 'Oath Liberator, Aglovale', 'V Standard', 'Gold Paladin', 2, 20, 'aglovale', 19, 'activo'),
(4, 'Blue Wave Dragon, Tetra-drive Dragon', 'V Standard', 'Aqua Force', 3, 7, 'tetra-drivedragon', 43, 'activo'),
(5, 'One Who Surpasses the Storm, Thavas', 'V Standard', 'Aqua Force', 3, 13, 'thavas', 22, 'activo'),
(7, 'Supreme Heavenly Emperor Dragon, Dragonic Overlord &quot;The Purge&quot;', 'Premium', 'Kagero', 4, 45, 'dothepurge', 5, 'activo'),
(8, 'Emperor Dragon, Gaia Emperor', 'V Standard', 'Tachikaze', 3, 4, 'gaiaemperor', 6, 'activo'),
(9, 'Stern Blaukluger', 'V Standard', 'Nova Grappler', 3, 15, 'sternblaukluger', 42, 'activo'),
(10, 'Dragheart, Luard', 'V Standard', 'Shadow Paladin', 3, 20, 'luard', 19, 'activo'),
(11, 'Dragonic Overlord &quot;The Destiny&quot;', '', 'Kagero', 3, 34, 'dothedestiny', 5, 'activo'),
(12, 'Star-vader, Blaster Joker', '', 'Link Joker', 3, 33.45, 'starvaderblasterjoker', 12, 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `nombreUsuario` varchar(50) NOT NULL,
  `contrasenia` varchar(255) NOT NULL,
  `fecha` date NOT NULL,
  `administrador` enum('si','no') NOT NULL DEFAULT 'no',
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `nombre`, `apellidos`, `nombreUsuario`, `contrasenia`, `fecha`, `administrador`, `estado`) VALUES
(2, 'Jose', 'Ejem', 'hola@ejemplo.ej', '$2y$10$yxIYL1ZWNdPA0BhFO.13OOFkhZhqJ8do3XG.zyKKB8zLJy7YQ2YSS', '2021-11-10', 'no', 'activo'),
(3, 'Jesus', 'Jurado', 'jjur@ejemplo.ej', '$2y$10$svptnXuoAd8.80gX1XG6X.GTOHFyj1rIdWWKGk217rL03A5DhxWUK', '2021-11-06', 'si', 'activo'),
(4, 'George', 'Khan', 'george@ejemplo.ej', '$2y$10$95UZf7aIdJm4yEGDmFaZL.isMYZiNe6/xVOahk8bLOP3Ek4DzZ2iq', '2021-11-19', 'no', 'inactivo'),
(5, 'Carmen', 'SanDiego', 'carmensan@ejemplo.ej', '$2y$10$Z7CdN98tiP6IlgqspgO6wutPWKqtnFJNOly814NDSyb/lPTI503Ge', '2021-10-14', 'no', 'activo'),
(6, 'Admin', 'Administrador', 'admin01@ejemplo.ej', '$2y$10$yFZuL.ycXg11cc5u3Kruc.QFNJ.wHzwTEuG5vxUbWewd8Hc6v9fZ.', '2021-10-06', 'si', 'activo'),
(7, 'rober', 'Jordan', 'roberj@ejem.ej', '$2y$10$F17h48njvqogQWCRN9.LeOcLEOQ5oRN52oV6Luitv1RENVTmTSG82', '2021-11-30', 'no', 'activo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carta`
--
ALTER TABLE `carta`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carta`
--
ALTER TABLE `carta`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
