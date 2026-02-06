-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 04-02-2026 a las 11:08:39
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

--
-- Volcado de datos para la tabla `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `service_id`, `appointment_date`, `appointment_time`, `status`, `notes`, `created_at`) VALUES
(1, 2, 3, '2026-01-30', '10:00:00', 'pending', NULL, '2026-02-02 10:20:01'),
(2, 2, 3, '2026-02-26', '10:00:00', 'cancelled', NULL, '2026-02-02 10:53:54'),
(4, 7, 3, '2026-02-02', '12:00:00', 'pending', NULL, '2026-02-02 10:59:48'),
(5, 2, 5, '2026-02-02', '19:30:00', 'pending', NULL, '2026-02-02 13:33:16');

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

--
-- Volcado de datos para la tabla `business_gallery`
--

INSERT INTO `business_gallery` (`id`, `business_profile_id`, `image_url`, `is_main`) VALUES
(2, 1, 'assets/uploads/gal_697c6c2087d30.jpg', 0),
(3, 1, 'assets/uploads/gal_697c6c208c0ad.jpg', 0),
(4, 1, 'assets/uploads/gal_697c6c208fc2c.png', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `business_profiles`
--

CREATE TABLE `business_profiles` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `description` text,
  `business_type` varchar(50) DEFAULT 'General',
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

INSERT INTO `business_profiles` (`id`, `user_id`, `description`, `business_type`, `logo_url`, `banner_url`, `opening_hours`, `website`, `instagram_link`, `facebook_link`, `twitter_link`, `tiktok_link`, `created_at`, `updated_at`, `is_public`) VALUES
(1, 61, 'Somo una enpreza que colta pelo', 'Barbershop', 'assets/uploads/img_6979cff56df83.jpg', 'assets/uploads/img_6978a245c9128.jpg', '{\"friday\": {\"open\": null, \"close\": null, \"active\": false}, \"monday\": {\"open\": \"09:00\", \"close\": \"20:00\", \"active\": true}, \"sunday\": {\"open\": \"09:00\", \"close\": \"20:00\", \"active\": true}, \"tuesday\": {\"open\": \"09:00\", \"close\": \"20:00\", \"active\": true}, \"saturday\": {\"open\": null, \"close\": null, \"active\": false}, \"thursday\": {\"open\": null, \"close\": null, \"active\": false}, \"wednesday\": {\"open\": null, \"close\": null, \"active\": false}}', 'https://empresaejemplo.com', 'https://www.instagram.com/', 'https://www.facebook.com/', '', '@EmpresaEjemplo', '2026-01-27 10:56:58', '2026-02-03 11:34:14', 1),
(28, 101, 'Expertos en colorimetría y cortes modernos en el corazón de Madrid.', 'Hair Salon', NULL, NULL, '{\"friday\": {\"open\": \"09:00\", \"close\": \"21:00\", \"active\": true}, \"monday\": {\"open\": \"09:00\", \"close\": \"20:00\", \"active\": true}, \"sunday\": {\"active\": false}, \"tuesday\": {\"open\": \"09:00\", \"close\": \"20:00\", \"active\": true}, \"saturday\": {\"open\": \"10:00\", \"close\": \"14:00\", \"active\": true}, \"thursday\": {\"open\": \"09:00\", \"close\": \"20:00\", \"active\": true}, \"wednesday\": {\"open\": \"09:00\", \"close\": \"20:00\", \"active\": true}}', NULL, NULL, NULL, NULL, NULL, '2026-02-04 10:55:01', '2026-02-04 10:55:01', 1),
(29, 102, 'Salón de peluquería vanguardista. Tu imagen es nuestra prioridad.', 'Hair Salon', NULL, NULL, '{\"friday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}, \"monday\": {\"open\": \"10:00\", \"close\": \"19:00\", \"active\": true}, \"sunday\": {\"active\": false}, \"tuesday\": {\"open\": \"10:00\", \"close\": \"19:00\", \"active\": true}, \"saturday\": {\"open\": \"10:00\", \"close\": \"14:00\", \"active\": true}, \"thursday\": {\"open\": \"10:00\", \"close\": \"19:00\", \"active\": true}, \"wednesday\": {\"open\": \"10:00\", \"close\": \"19:00\", \"active\": true}}', NULL, NULL, NULL, NULL, NULL, '2026-02-04 10:55:01', '2026-02-04 10:55:01', 1),
(30, 103, 'Barbería clásica con toques modernos. Afeitado a navaja y degradados.', 'Barbershop', NULL, NULL, '{\"friday\": {\"open\": \"09:30\", \"close\": \"21:00\", \"active\": true}, \"monday\": {\"active\": false}, \"sunday\": {\"active\": false}, \"tuesday\": {\"open\": \"09:30\", \"close\": \"20:30\", \"active\": true}, \"saturday\": {\"open\": \"09:30\", \"close\": \"14:00\", \"active\": true}, \"thursday\": {\"open\": \"09:30\", \"close\": \"20:30\", \"active\": true}, \"wednesday\": {\"open\": \"09:30\", \"close\": \"20:30\", \"active\": true}}', NULL, NULL, NULL, NULL, NULL, '2026-02-04 10:55:01', '2026-02-04 10:55:01', 1),
(31, 104, 'El cuidado que el caballero de hoy necesita.', 'Barbershop', NULL, NULL, '{\"friday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}, \"monday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}, \"sunday\": {\"active\": false}, \"tuesday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}, \"saturday\": {\"open\": \"10:00\", \"close\": \"14:00\", \"active\": true}, \"thursday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}, \"wednesday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}}', NULL, NULL, NULL, NULL, NULL, '2026-02-04 10:55:01', '2026-02-04 10:55:01', 1),
(32, 105, 'Manicura, pedicura y nail art de alta calidad.', 'Nail Salon', NULL, NULL, '{\"friday\": {\"open\": \"09:00\", \"close\": \"20:00\", \"active\": true}, \"monday\": {\"open\": \"09:00\", \"close\": \"20:00\", \"active\": true}, \"sunday\": {\"active\": false}, \"tuesday\": {\"open\": \"09:00\", \"close\": \"20:00\", \"active\": true}, \"saturday\": {\"active\": false}, \"thursday\": {\"open\": \"09:00\", \"close\": \"20:00\", \"active\": true}, \"wednesday\": {\"open\": \"09:00\", \"close\": \"20:00\", \"active\": true}}', NULL, NULL, NULL, NULL, NULL, '2026-02-04 10:55:01', '2026-02-04 10:55:01', 1),
(33, 106, 'Tus uñas son tu mejor accesorio. Déjanos cuidarlas.', 'Nail Salon', NULL, NULL, '{\"friday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}, \"monday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}, \"sunday\": {\"active\": false}, \"tuesday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}, \"saturday\": {\"open\": \"10:00\", \"close\": \"15:00\", \"active\": true}, \"thursday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}, \"wednesday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}}', NULL, NULL, NULL, NULL, NULL, '2026-02-04 10:55:01', '2026-02-04 10:55:01', 1),
(34, 107, 'Depilación láser de diodo de última generación. Indoloro y efectivo.', 'Hair Removal', NULL, NULL, '{\"friday\": {\"open\": \"09:00\", \"close\": \"21:00\", \"active\": true}, \"monday\": {\"open\": \"09:00\", \"close\": \"21:00\", \"active\": true}, \"sunday\": {\"active\": false}, \"tuesday\": {\"open\": \"09:00\", \"close\": \"21:00\", \"active\": true}, \"saturday\": {\"active\": false}, \"thursday\": {\"open\": \"09:00\", \"close\": \"21:00\", \"active\": true}, \"wednesday\": {\"open\": \"09:00\", \"close\": \"21:00\", \"active\": true}}', NULL, NULL, NULL, NULL, NULL, '2026-02-04 10:55:01', '2026-02-04 10:55:01', 1),
(35, 108, 'Especialistas en depilación con cera y láser. Tu piel suave todo el año.', 'Hair Removal', NULL, NULL, '{\"friday\": {\"open\": \"09:00\", \"close\": \"18:00\", \"active\": true}, \"monday\": {\"open\": \"09:00\", \"close\": \"18:00\", \"active\": true}, \"sunday\": {\"active\": false}, \"tuesday\": {\"open\": \"09:00\", \"close\": \"18:00\", \"active\": true}, \"saturday\": {\"open\": \"09:00\", \"close\": \"14:00\", \"active\": true}, \"thursday\": {\"open\": \"09:00\", \"close\": \"18:00\", \"active\": true}, \"wednesday\": {\"open\": \"09:00\", \"close\": \"18:00\", \"active\": true}}', NULL, NULL, NULL, NULL, NULL, '2026-02-04 10:55:01', '2026-02-04 10:55:01', 1),
(36, 109, 'Diseño de miradas, lifting de pestañas y microblading.', 'Eyebrows & Lashes', NULL, NULL, '{\"friday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}, \"monday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}, \"sunday\": {\"active\": false}, \"tuesday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}, \"saturday\": {\"active\": false}, \"thursday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}, \"wednesday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}}', NULL, NULL, NULL, NULL, NULL, '2026-02-04 10:55:01', '2026-02-04 10:55:01', 1),
(37, 110, 'Extensiones de pestañas pelo a pelo y volumen ruso.', 'Eyebrows & Lashes', NULL, NULL, '{\"friday\": {\"open\": \"09:30\", \"close\": \"19:30\", \"active\": true}, \"monday\": {\"open\": \"09:30\", \"close\": \"19:30\", \"active\": true}, \"sunday\": {\"active\": false}, \"tuesday\": {\"open\": \"09:30\", \"close\": \"19:30\", \"active\": true}, \"saturday\": {\"open\": \"10:00\", \"close\": \"14:00\", \"active\": true}, \"thursday\": {\"open\": \"09:30\", \"close\": \"19:30\", \"active\": true}, \"wednesday\": {\"open\": \"09:30\", \"close\": \"19:30\", \"active\": true}}', NULL, NULL, NULL, NULL, NULL, '2026-02-04 10:55:01', '2026-02-04 10:55:01', 1),
(38, 111, 'Tratamientos faciales, limpieza de cutis y cuidado de la piel.', 'Skincare', NULL, NULL, '{\"friday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}, \"monday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}, \"sunday\": {\"active\": false}, \"tuesday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}, \"saturday\": {\"active\": false}, \"thursday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}, \"wednesday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}}', NULL, NULL, NULL, NULL, NULL, '2026-02-04 10:55:01', '2026-02-04 10:55:01', 1),
(39, 112, 'Tu piel radiante con nuestros tratamientos orgánicos.', 'Skincare', NULL, NULL, '{\"friday\": {\"open\": \"11:00\", \"close\": \"20:00\", \"active\": true}, \"monday\": {\"open\": \"11:00\", \"close\": \"20:00\", \"active\": true}, \"sunday\": {\"active\": false}, \"tuesday\": {\"open\": \"11:00\", \"close\": \"20:00\", \"active\": true}, \"saturday\": {\"open\": \"10:00\", \"close\": \"14:00\", \"active\": true}, \"thursday\": {\"open\": \"11:00\", \"close\": \"20:00\", \"active\": true}, \"wednesday\": {\"open\": \"11:00\", \"close\": \"20:00\", \"active\": true}}', NULL, NULL, NULL, NULL, NULL, '2026-02-04 10:55:01', '2026-02-04 10:55:01', 1),
(40, 113, 'Masajes relajantes, descontracturantes y deportivos frente al mar.', 'Massage', NULL, NULL, '{\"friday\": {\"open\": \"09:00\", \"close\": \"21:00\", \"active\": true}, \"monday\": {\"open\": \"09:00\", \"close\": \"21:00\", \"active\": true}, \"sunday\": {\"open\": \"10:00\", \"close\": \"15:00\", \"active\": true}, \"tuesday\": {\"open\": \"09:00\", \"close\": \"21:00\", \"active\": true}, \"saturday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}, \"thursday\": {\"open\": \"09:00\", \"close\": \"21:00\", \"active\": true}, \"wednesday\": {\"open\": \"09:00\", \"close\": \"21:00\", \"active\": true}}', NULL, NULL, NULL, NULL, NULL, '2026-02-04 10:55:01', '2026-02-04 10:55:01', 1),
(41, 114, 'Fisioterapia y masajes para tu bienestar integral.', 'Massage', NULL, NULL, '{\"friday\": {\"open\": \"08:00\", \"close\": \"15:00\", \"active\": true}, \"monday\": {\"open\": \"08:00\", \"close\": \"20:00\", \"active\": true}, \"sunday\": {\"active\": false}, \"tuesday\": {\"open\": \"08:00\", \"close\": \"20:00\", \"active\": true}, \"saturday\": {\"active\": false}, \"thursday\": {\"open\": \"08:00\", \"close\": \"20:00\", \"active\": true}, \"wednesday\": {\"open\": \"08:00\", \"close\": \"20:00\", \"active\": true}}', NULL, NULL, NULL, NULL, NULL, '2026-02-04 10:55:01', '2026-02-04 10:55:01', 1),
(42, 115, 'Maquillaje para eventos, novias y cursos de automaquillaje.', 'Makeup', NULL, NULL, '{\"friday\": {\"open\": \"10:00\", \"close\": \"20:00\", \"active\": true}, \"monday\": {\"active\": false}, \"sunday\": {\"active\": false}, \"tuesday\": {\"open\": \"10:00\", \"close\": \"19:00\", \"active\": true}, \"saturday\": {\"open\": \"09:00\", \"close\": \"21:00\", \"active\": true}, \"thursday\": {\"open\": \"10:00\", \"close\": \"19:00\", \"active\": true}, \"wednesday\": {\"open\": \"10:00\", \"close\": \"19:00\", \"active\": true}}', NULL, NULL, NULL, NULL, NULL, '2026-02-04 10:55:01', '2026-02-04 10:55:01', 1),
(43, 116, 'Artistas del maquillaje profesional para cine, tv y social.', 'Makeup', NULL, NULL, '{\"friday\": {\"open\": \"09:00\", \"close\": \"18:00\", \"active\": true}, \"monday\": {\"open\": \"09:00\", \"close\": \"18:00\", \"active\": true}, \"sunday\": {\"active\": false}, \"tuesday\": {\"open\": \"09:00\", \"close\": \"18:00\", \"active\": true}, \"saturday\": {\"active\": false}, \"thursday\": {\"open\": \"09:00\", \"close\": \"18:00\", \"active\": true}, \"wednesday\": {\"open\": \"09:00\", \"close\": \"18:00\", \"active\": true}}', NULL, NULL, NULL, NULL, NULL, '2026-02-04 10:55:01', '2026-02-04 10:55:01', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `services`
--

CREATE TABLE `services` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `services`
--

INSERT INTO `services` (`id`, `user_id`, `name`, `price`, `duration`, `created_at`) VALUES
(2, 61, 'Cortesito', 100.00, 2, '2026-02-02 07:44:12'),
(3, 61, 'Corte Clásico', 15.00, 30, '2026-02-02 10:20:01'),
(4, 61, 'Rapada', 20.00, 5, '2026-02-02 13:27:51'),
(5, 61, 'Barba', 10.50, 20, '2026-02-02 13:28:19'),
(7, 101, 'Corte Mujer', 25.00, 60, '2026-02-04 10:55:01'),
(8, 101, 'Mechas Balayage', 80.00, 120, '2026-02-04 10:55:01'),
(9, 102, 'Corte Moderno', 30.00, 45, '2026-02-04 10:55:01'),
(10, 102, 'Tratamiento Keratina', 100.00, 90, '2026-02-04 10:55:01'),
(11, 103, 'Corte Caballero', 15.00, 30, '2026-02-04 10:55:01'),
(12, 103, 'Arreglo Barba', 10.00, 20, '2026-02-04 10:55:01'),
(13, 104, 'Corte Premium + Bebida', 25.00, 45, '2026-02-04 10:55:01'),
(14, 105, 'Manicura Semipermanente', 20.00, 45, '2026-02-04 10:55:01'),
(15, 106, 'Uñas Acrílicas', 35.00, 90, '2026-02-04 10:55:01'),
(16, 107, 'Láser Piernas Completas', 60.00, 45, '2026-02-04 10:55:01'),
(17, 108, 'Cera Ingles Brasileñas', 18.00, 20, '2026-02-04 10:55:01'),
(18, 109, 'Diseño de Cejas', 12.00, 20, '2026-02-04 10:55:01'),
(19, 110, 'Extensiones Pestañas Clásicas', 50.00, 90, '2026-02-04 10:55:01'),
(20, 111, 'Limpieza Facial Profunda', 45.00, 60, '2026-02-04 10:55:01'),
(21, 112, 'Peeling Químico', 60.00, 45, '2026-02-04 10:55:01'),
(22, 113, 'Masaje Relajante 1h', 50.00, 60, '2026-02-04 10:55:01'),
(23, 114, 'Masaje Descontracturante', 40.00, 45, '2026-02-04 10:55:01'),
(24, 115, 'Maquillaje Social', 45.00, 60, '2026-02-04 10:55:01'),
(25, 116, 'Maquillaje Novia (Prueba inc.)', 150.00, 120, '2026-02-04 10:55:01');

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
(66, 'BarberiaLucas', 'phathompro@gmail.com', '$2y$10$VZ7/DdJo3Glx1B.5vFtAeeTc1mynYLDl2s.AE85ji38I7NXjhZgku', 'store', 0, NULL, '2026-01-28 12:09:28', 'BarberiaLucas', 'Calle cuenca', '46007', NULL, NULL),
(101, 'estilomadrid', 'contacto@estilomadrid.es', '$2y$10$seZRXVnGvy3Fsrv4dsxZOuUY2zzJcwkhLSTpIxPOYn7OdjxuOIeH6', 'store', 1, NULL, '2026-02-04 10:55:01', 'Estilo Madrid Peluqueros', 'Calle Gran Vía 45', '28013', '910000001', 'Madrid'),
(102, 'bcncreativo', 'info@bcncreativo.cat', '$2y$10$seZRXVnGvy3Fsrv4dsxZOuUY2zzJcwkhLSTpIxPOYn7OdjxuOIeH6', 'store', 1, NULL, '2026-02-04 10:55:01', 'Salón Creativo BCN', 'Carrer de Balmes 120', '08008', '930000002', 'Barcelona'),
(103, 'bigotesevilla', 'hola@barberiabigote.com', '$2y$10$seZRXVnGvy3Fsrv4dsxZOuUY2zzJcwkhLSTpIxPOYn7OdjxuOIeH6', 'store', 1, NULL, '2026-02-04 10:55:01', 'Barbería El Bigote', 'Calle Betis 50', '41010', '954000003', 'Sevilla'),
(104, 'gentlemanvlc', 'citas@gentlemanvlc.com', '$2y$10$seZRXVnGvy3Fsrv4dsxZOuUY2zzJcwkhLSTpIxPOYn7OdjxuOIeH6', 'store', 1, NULL, '2026-02-04 10:55:01', 'The Gentleman Cut', 'Carrer de Colón 15', '46004', '960000004', 'Valencia'),
(105, 'nailsmalaga', 'info@nailsmalaga.es', '$2y$10$seZRXVnGvy3Fsrv4dsxZOuUY2zzJcwkhLSTpIxPOYn7OdjxuOIeH6', 'store', 1, NULL, '2026-02-04 10:55:01', 'Málaga Nails & Spa', 'Calle Marqués de Larios 4', '29005', '952000005', 'Málaga'),
(106, 'artenailszgz', 'contacto@artenails.com', '$2y$10$seZRXVnGvy3Fsrv4dsxZOuUY2zzJcwkhLSTpIxPOYn7OdjxuOIeH6', 'store', 1, NULL, '2026-02-04 10:55:01', 'Arte en Uñas Zaragoza', 'Paseo de la Independencia 22', '50004', '976000006', 'Zaragoza'),
(107, 'bilbaolaser', 'citas@bilbaolaser.eus', '$2y$10$seZRXVnGvy3Fsrv4dsxZOuUY2zzJcwkhLSTpIxPOYn7OdjxuOIeH6', 'store', 1, NULL, '2026-02-04 10:55:01', 'Clínica Láser Norte', 'Gran Vía de Don Diego López de Haro 30', '48009', '944000007', 'Bilbao'),
(108, 'suavepielalicante', 'info@suavepiel.com', '$2y$10$seZRXVnGvy3Fsrv4dsxZOuUY2zzJcwkhLSTpIxPOYn7OdjxuOIeH6', 'store', 1, NULL, '2026-02-04 10:55:01', 'Suave Piel Alicante', 'Avenida de Maisonnave 10', '03003', '965000008', 'Alicante'),
(109, 'miradagranada', 'hola@miradaperfecta.es', '$2y$10$seZRXVnGvy3Fsrv4dsxZOuUY2zzJcwkhLSTpIxPOYn7OdjxuOIeH6', 'store', 1, NULL, '2026-02-04 10:55:01', 'Mirada Perfecta', 'Calle Recogidas 12', '18002', '958000009', 'Granada'),
(110, 'pestanasvigo', 'info@lashesvigo.gal', '$2y$10$seZRXVnGvy3Fsrv4dsxZOuUY2zzJcwkhLSTpIxPOYn7OdjxuOIeH6', 'store', 1, NULL, '2026-02-04 10:55:01', 'Lashes Studio Vigo', 'Rúa do Príncipe 25', '36202', '986000010', 'Vigo'),
(111, 'pielmurcia', 'clinica@pielmurcia.com', '$2y$10$seZRXVnGvy3Fsrv4dsxZOuUY2zzJcwkhLSTpIxPOYn7OdjxuOIeH6', 'store', 1, NULL, '2026-02-04 10:55:01', 'Dermatología y Estética Luz', 'Gran Vía del Escultor Francisco Salzillo 8', '30004', '968000011', 'Murcia'),
(112, 'glowmallorca', 'hello@glowmallorca.com', '$2y$10$seZRXVnGvy3Fsrv4dsxZOuUY2zzJcwkhLSTpIxPOYn7OdjxuOIeH6', 'store', 1, NULL, '2026-02-04 10:55:01', 'Glow Skin Care', 'Passeig del Born 14', '07012', '971000012', 'Palma'),
(113, 'relaxcanarias', 'reservas@relaxcanarias.com', '$2y$10$seZRXVnGvy3Fsrv4dsxZOuUY2zzJcwkhLSTpIxPOYn7OdjxuOIeH6', 'store', 1, NULL, '2026-02-04 10:55:01', 'Masajes Relax Total', 'Paseo de Las Canteras 50', '35010', '928000013', 'Las Palmas'),
(114, 'bienestaroviedo', 'info@bienestaroviedo.es', '$2y$10$seZRXVnGvy3Fsrv4dsxZOuUY2zzJcwkhLSTpIxPOYn7OdjxuOIeH6', 'store', 1, NULL, '2026-02-04 10:55:01', 'Centro Quiromasaje Bienestar', 'Calle Uría 20', '33003', '985000014', 'Oviedo'),
(115, 'makeupmadrid', 'laura@makeupartist.com', '$2y$10$seZRXVnGvy3Fsrv4dsxZOuUY2zzJcwkhLSTpIxPOYn7OdjxuOIeH6', 'store', 1, NULL, '2026-02-04 10:55:01', 'Laura Makeup Studio', 'Calle de Fuencarral 80', '28004', '910000015', 'Madrid'),
(116, 'proartbcn', 'academy@proart.cat', '$2y$10$seZRXVnGvy3Fsrv4dsxZOuUY2zzJcwkhLSTpIxPOYn7OdjxuOIeH6', 'store', 1, NULL, '2026-02-04 10:55:01', 'ProArt Maquilladores', 'Avinguda Diagonal 400', '08037', '930000016', 'Barcelona');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indices de la tabla `business_gallery`
--
ALTER TABLE `business_gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_gallery_profile` (`business_profile_id`);

--
-- Indices de la tabla `business_profiles`
--
ALTER TABLE `business_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user` (`user_id`);

--
-- Indices de la tabla `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_service_user` (`user_id`);

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
-- AUTO_INCREMENT de la tabla `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `business_gallery`
--
ALTER TABLE `business_gallery`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `business_profiles`
--
ALTER TABLE `business_profiles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de la tabla `services`
--
ALTER TABLE `services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `business_gallery`
--
ALTER TABLE `business_gallery`
  ADD CONSTRAINT `fk_gallery_profile` FOREIGN KEY (`business_profile_id`) REFERENCES `business_profiles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `business_profiles`
--
ALTER TABLE `business_profiles`
  ADD CONSTRAINT `fk_business_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `fk_service_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;


CREATE TABLE `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `business_profile_id` int NOT NULL,
  `rating` int NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
  `comment` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `business_profile_id` (`business_profile_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`business_profile_id`) REFERENCES `business_profiles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;




/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
