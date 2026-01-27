-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 27-01-2026 a las 12:04:19
-- Versión del servidor: 8.0.44
-- Versión de PHP: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `easy_point`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `business_profiles`
--

CREATE TABLE `business_profiles` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `description` text,
  `logo_url` varchar(255) DEFAULT NULL,
  `banner_url` varchar(255) DEFAULT NULL,
  `opening_hours` json DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `instagram_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_public` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `business_profiles`
--

INSERT INTO `business_profiles` (`id`, `user_id`, `description`, `logo_url`, `banner_url`, `opening_hours`, `website`, `instagram_link`, `created_at`, `updated_at`, `is_public`) VALUES
(1, 61, 'asduasdaoisd', 'assets/uploads/img_6978a466b1baa.png', 'assets/uploads/img_6978a245c9128.jpg', '{\"friday\": {\"open\": \"09:00\", \"close\": \"20:00\", \"active\": true}, \"monday\": {\"open\": \"09:00\", \"close\": \"20:00\", \"active\": true}, \"sunday\": {\"open\": null, \"close\": null, \"active\": false}, \"tuesday\": {\"open\": \"09:00\", \"close\": \"20:00\", \"active\": true}, \"saturday\": {\"open\": null, \"close\": null, \"active\": false}, \"thursday\": {\"open\": \"09:00\", \"close\": \"20:00\", \"active\": true}, \"wednesday\": {\"open\": \"09:00\", \"close\": \"20:00\", \"active\": true}}', NULL, NULL, '2026-01-27 10:56:58', '2026-01-27 11:41:26', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `business_profiles`
--
ALTER TABLE `business_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user` (`user_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `business_profiles`
--
ALTER TABLE `business_profiles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `business_profiles`
--
ALTER TABLE `business_profiles`
  ADD CONSTRAINT `fk_business_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
