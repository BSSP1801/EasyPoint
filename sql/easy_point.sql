-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 21-01-2026 a las 11:13:28
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
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user','store') DEFAULT 'user',
  `is_confirmed` tinyint(1) DEFAULT '0',
  `token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `business_name` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `is_confirmed`, `token`, `created_at`, `business_name`, `address`, `postal_code`) VALUES
(1, 'admin_test', 'admin@easypoint.com', '$2y$10$vI8.D.Yf/Yv.1yv.1yv.1yv.1yv.1yv.1yv.1yv.1yv.1yv.1yv', 'admin', 1, NULL, '2026-01-19 12:44:49', NULL, NULL, NULL),
(2, 'bsalcedo', 'brunosalcedo1801@gmail.com', '$2y$10$deukPQsLWNDQ/KMuDE64LOeeo9yDaCB3L3e63ApoA5Z7DK/IikayO', 'user', 0, NULL, '2026-01-19 13:31:06', NULL, NULL, NULL),
(7, 'jciuperca', 'metracam@gmail.com', '$2y$10$TxVa.PD5Fu6dT5s/GU1KlOuJ346qKKYtTI1sWfvxFL9c8aPitccRK', 'user', 0, NULL, '2026-01-19 13:36:07', NULL, NULL, NULL),
(8, 'buser1', 'bmail1@gmail.com', '$2y$10$a6dBQ2HaQjdM3C48qQ.kluHNrid4Oo5xBdKQR.QdbZimPSLQ7hdbe', 'store', 0, NULL, '2026-01-20 11:01:23', 'bname1', 'baddress1', '4008'),
(10, 'user1', 'email1@gmail.com', '$2y$10$seZRXVnGvy3Fsrv4dsxZOuUY2zzJcwkhLSTpIxPOYn7OdjxuOIeH6', 'user', 0, NULL, '2026-01-20 11:04:17', NULL, NULL, NULL),
(11, 'yramirez', 'yerai.ramlin@gmail.com', '$2y$10$uVAsO5Y8BG1xqDz7szFxt.39XWef37wrMK5YYLWPfXgPeZoNpdvtW', 'user', 0, NULL, '2026-01-21 11:10:21', NULL, NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
