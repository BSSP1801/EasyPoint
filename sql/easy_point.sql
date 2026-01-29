-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 28-01-2026 a las 12:11:38
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
-- (Debe ir primero porque otras tablas dependen de ella)
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
  `postal_code` varchar(10) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `is_confirmed`, `token`, `created_at`, `business_name`, `address`, `postal_code`, `phone`, `city`) VALUES
(1, 'admin_test', 'admin@easypoint.com', '$2y$10$vI8.D.Yf/Yv.1yv.1yv.1yv.1yv.1yv.1yv.1yv.1yv.1yv.1yv', 'admin', 1, NULL, '2026-01-19 12:44:49', NULL, NULL, NULL, NULL, NULL),
(2, 'bsalcedo', 'brunosalcedo1801@gmail.com', '$2y$10$deukPQsLWNDQ/KMuDE64LOeeo9yDaCB3L3e63ApoA5Z7DK/IikayO', 'user', 0, NULL, '2026-01-19 13:31:06', NULL, NULL, NULL, NULL, NULL),
(7, 'jciuperca', 'metracam@gmail.com', '$2y$10$TxVa.PD5Fu6dT5s/GU1KlOuJ346qKKYtTI1sWfvxFL9c8aPitccRK', 'user', 0, NULL, '2026-01-19 13:36:07', NULL, NULL, NULL, NULL, NULL),
(8, 'buser1', 'bmail1@gmail.com', '$2y$10$a6dBQ2HaQjdM3C48qQ.kluHNrid4Oo5xBdKQR.QdbZimPSLQ7hdbe', 'store', 0, NULL, '2026-01-20 11:01:23', 'bname1', 'baddress1', '4008', NULL, NULL),
(10, 'user1', 'email1@gmail.com', '$2y$10$seZRXVnGvy3Fsrv4dsxZOuUY2zzJcwkhLSTpIxPOYn7OdjxuOIeH6', 'user', 0, NULL, '2026-01-20 11:04:17', NULL, NULL, NULL, NULL, NULL),
(11, 'yramirez', 'yerai.ramlin@gmail.com', '$2y$10$uVAsO5Y8BG1xqDz7szFxt.39XWef37wrMK5YYLWPfXgPeZoNpdvtW', 'user', 0, NULL, '2026-01-21 11:10:21', NULL, NULL, NULL, NULL, NULL),
(61, 'empresa', 'empresa@gmail.com', '$2y$10$ZK8OcEVZ8fCBt5N5aHbYnefQ/.bHRg7K/4YrllTW7RPYtwnfXx.Yq', 'store', 0, NULL, '2026-01-21 12:14:06', 'EmpresaEjemplo', 'Calle cuenca', '46007', '+555555', 'Valencia'),
(66, 'BarberiaLucas', 'phathompro@gmail.com', '$2y$10$VZ7/DdJo3Glx1B.5vFtAeeTc1mynYLDl2s.AE85ji38I7NXjhZgku', 'store', 0, NULL, '2026-01-28 12:09:28', 'BarberiaLucas', 'Calle cuenca', '46007', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `services`
-- (Actualizada con la nueva estructura que incluye user_id)
--

CREATE TABLE `services` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` int NOT NULL, 
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `appointments`
--

CREATE TABLE `appointments` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `service_id` int NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `business_profiles`
-- (Actualizada con las columnas de redes sociales)
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
  `facebook_link` varchar(255) DEFAULT NULL,
  `twitter_link` varchar(255) DEFAULT NULL,
  `tiktok_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_public` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `business_profiles`
--

INSERT INTO `business_profiles` (`id`, `user_id`, `description`, `logo_url`, `banner_url`, `opening_hours`, `website`, `instagram_link`, `created_at`, `updated_at`, `is_public`) VALUES
(1, 61, 'asduasdaoisd', 'assets/uploads/img_6979cff56df83.jpg', 'assets/uploads/img_6978a245c9128.jpg', '{\"friday\": {\"open\": \"09:00\", \"close\": \"20:00\", \"active\": true}, \"monday\": {\"open\": null, \"close\": null, \"active\": false}, \"sunday\": {\"open\": \"09:00\", \"close\": \"20:00\", \"active\": true}, \"tuesday\": {\"open\": null, \"close\": null, \"active\": false}, \"saturday\": {\"open\": null, \"close\": null, \"active\": false}, \"thursday\": {\"open\": \"09:00\", \"close\": \"20:00\", \"active\": true}, \"wednesday\": {\"open\": null, \"close\": null, \"active\": false}}', NULL, NULL, '2026-01-27 10:56:58', '2026-01-28 08:59:52', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `business_gallery`
--

CREATE TABLE `business_gallery` (
  `id` int NOT NULL,
  `business_profile_id` int NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `is_main` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- --------------------------------------------------------

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
-- Indices de la tabla `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_service_user` (`user_id`);

--
-- Indices de la tabla `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indices de la tabla `business_profiles`
--
ALTER TABLE `business_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user` (`user_id`);

--
-- Indices de la tabla `business_gallery`
--
ALTER TABLE `business_gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_gallery_profile` (`business_profile_id`);


-- --------------------------------------------------------

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT de la tabla `services`
--
ALTER TABLE `services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `business_profiles`
--
ALTER TABLE `business_profiles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `business_gallery`
--
ALTER TABLE `business_gallery`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;


-- --------------------------------------------------------

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `fk_service_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `business_profiles`
--
ALTER TABLE `business_profiles`
  ADD CONSTRAINT `fk_business_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `business_gallery`
--
ALTER TABLE `business_gallery`
  ADD CONSTRAINT `fk_gallery_profile` FOREIGN KEY (`business_profile_id`) REFERENCES `business_profiles` (`id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;