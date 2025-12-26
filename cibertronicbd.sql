-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-06-2025 a las 05:50:48
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cibertronicbd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrador`
--

CREATE TABLE `administrador` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `dni` varchar(8) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `administrador`
--

INSERT INTO `administrador` (`id`, `usuario_id`, `nombre`, `apellidos`, `dni`, `telefono`, `foto_perfil`) VALUES
(1, 1, 'Juniors Santos', 'Murrugarra Vallenas ', '44701633', '902396616', 'ff'),
(69, 119, 'Damian', 'Herrrera', '45021832', '987365145', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `dni` varchar(8) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`id`, `usuario_id`, `nombre`, `apellidos`, `dni`, `telefono`, `foto_perfil`) VALUES
(11, 112, 'Jhonny', 'Jimenez', '47588280', '966587825', NULL),
(12, 113, 'Cesar', 'Yontop', '46666485', '965478357', NULL),
(13, 114, 'Paul', 'Rios', '48251585', '985412365', NULL),
(14, 111, 'Jose', 'Vidaurre', '48482468', '963257486', NULL),
(15, 115, 'Raul', 'Francis', '46973585', '987589632', NULL),
(16, 116, 'Santos', 'Yupanqui', '47104522', '987745632', NULL),
(17, 117, 'Carlos', 'Ramirez ', '48101857', '968589874', NULL),
(18, 110, 'Jadir ', 'Guzman', '48511716', '958006245', NULL),
(19, 106, 'Cristobal', 'Ramirez ', '47569009', '914782370', NULL),
(20, 107, 'Sebastian', 'Salazar', '47901896', '969584321', NULL),
(21, 120, 'Fabiola', 'Oritz', '46598736', '902396618', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `areas`
--

CREATE TABLE `areas` (
  `id` int(11) NOT NULL,
  `nombre_area` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `areas`
--

INSERT INTO `areas` (`id`, `nombre_area`, `descripcion`, `estado`) VALUES
(1, 'AREA EMPRESARIAL', 'Formación en administración, contabilidad y ofimática para entornos corporativos.', 1),
(2, 'AREA DE EMPRENDEDORES', 'Capacitación práctica en oficios innovadores orientados a negocios propios.', 1),
(3, 'AREA DE INGENIERÍA', 'Desarrollo técnico en construcción, diseño CAD y topografía.', 1),
(4, 'AREA DE SALUD', 'Preparación para el apoyo clínico y cuidado del paciente en diversas especialidades.', 1),
(5, 'AREA DE EDUCACIÓN', 'Enseñanza auxiliar con enfoque en idiomas y educación inicial.', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaciones`
--

CREATE TABLE `asignaciones` (
  `id` int(11) NOT NULL,
  `codigo_asignacion` varchar(10) DEFAULT NULL,
  `profesor_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `horario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `asignaciones`
--

INSERT INTO `asignaciones` (`id`, `codigo_asignacion`, `profesor_id`, `curso_id`, `horario_id`) VALUES
(18, 'ASG-001', 18, 14, 41),
(19, 'ASG-002', 18, 6, 18),
(20, 'ASG-020', 18, 9, 26),
(21, 'ASG-021', 22, 19, 56),
(22, 'ASG-022', 18, 5, 13),
(23, 'ASG-023', 22, 15, 43),
(24, 'ASG-024', 22, 9, 25);

--
-- Disparadores `asignaciones`
--
DELIMITER $$
CREATE TRIGGER `generar_codigo_asignacion` BEFORE INSERT ON `asignaciones` FOR EACH ROW BEGIN
  DECLARE ultimo INT;

  SELECT IFNULL(MAX(id), 0) + 1 INTO ultimo FROM asignaciones;
  SET NEW.codigo_asignacion = CONCAT('ASG-', LPAD(ultimo, 3, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia`
--

CREATE TABLE `asistencia` (
  `id` int(11) NOT NULL,
  `alumno_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `horario_id` int(11) NOT NULL,
  `profesor_id` int(11) DEFAULT NULL,
  `fecha` date NOT NULL,
  `estado` enum('Presente','Ausente','Justificado') NOT NULL,
  `sesion_registro` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `asistencia`
--

INSERT INTO `asistencia` (`id`, `alumno_id`, `curso_id`, `horario_id`, `profesor_id`, `fecha`, `estado`, `sesion_registro`) VALUES
(42, 11, 0, 0, 18, '2025-06-19', 'Justificado', 1),
(43, 12, 0, 0, 18, '2025-06-19', 'Ausente', 1),
(44, 14, 0, 0, 18, '2025-06-19', 'Ausente', 1),
(45, 13, 0, 0, 18, '2025-06-19', 'Ausente', 1),
(46, 11, 0, 0, 18, '2025-06-19', 'Presente', 2),
(47, 12, 0, 0, 18, '2025-06-19', 'Ausente', 2),
(48, 13, 0, 0, 18, '2025-06-19', 'Presente', 2),
(49, 14, 0, 0, 18, '2025-06-19', 'Presente', 2),
(50, 15, 0, 0, 18, '2025-06-19', 'Ausente', 2),
(51, 11, 6, 18, 18, '2025-06-19', 'Presente', 3),
(52, 12, 6, 18, 18, '2025-06-19', 'Ausente', 3),
(53, 13, 6, 18, 18, '2025-06-19', 'Ausente', 3),
(54, 14, 6, 18, 18, '2025-06-19', 'Ausente', 3),
(55, 15, 6, 18, 18, '2025-06-19', 'Justificado', 3),
(56, 11, 14, 41, 18, '2025-06-19', 'Ausente', 4),
(57, 12, 14, 41, 18, '2025-06-19', 'Ausente', 4),
(58, 14, 14, 41, 18, '2025-06-19', 'Presente', 4),
(59, 13, 14, 41, 18, '2025-06-19', 'Presente', 4),
(60, 11, 14, 41, 18, '2025-06-19', 'Presente', 5),
(61, 12, 14, 41, 18, '2025-06-19', 'Ausente', 5),
(62, 14, 14, 41, 18, '2025-06-19', 'Justificado', 5),
(63, 13, 14, 41, 18, '2025-06-19', 'Ausente', 5),
(65, 12, 14, 41, 18, '2025-06-19', 'Presente', 6),
(66, 14, 14, 41, 18, '2025-06-19', 'Presente', 6),
(67, 13, 14, 41, 18, '2025-06-19', 'Presente', 6),
(102, 11, 14, 41, 18, '2025-06-19', 'Presente', 7),
(103, 12, 14, 41, 18, '2025-06-19', 'Ausente', 7),
(104, 14, 14, 41, 18, '2025-06-19', 'Ausente', 7),
(105, 13, 14, 41, 18, '2025-06-19', 'Ausente', 7),
(106, 11, 14, 41, 18, '2025-06-20', 'Presente', 8),
(107, 12, 14, 41, 18, '2025-06-20', 'Presente', 8),
(108, 14, 14, 41, 18, '2025-06-20', 'Presente', 8),
(109, 13, 14, 41, 18, '2025-06-20', 'Presente', 8),
(110, 11, 14, 41, 18, '2025-06-22', 'Presente', 9),
(111, 12, 14, 41, 18, '2025-06-22', 'Presente', 9),
(112, 14, 14, 41, 18, '2025-06-22', 'Ausente', 9),
(113, 13, 14, 41, 18, '2025-06-22', 'Justificado', 9),
(114, 11, 6, 18, 18, '2025-06-24', 'Presente', 10),
(115, 12, 6, 18, 18, '2025-06-24', 'Presente', 10),
(116, 13, 6, 18, 18, '2025-06-24', 'Presente', 10),
(117, 14, 6, 18, 18, '2025-06-24', 'Presente', 10),
(118, 15, 6, 18, 18, '2025-06-24', 'Presente', 10),
(119, 16, 6, 18, 18, '2025-06-24', 'Presente', 10),
(120, 17, 6, 18, 18, '2025-06-24', 'Presente', 10),
(121, 18, 6, 18, 18, '2025-06-24', 'Presente', 10),
(122, 19, 6, 18, 18, '2025-06-24', 'Presente', 10),
(123, 20, 6, 18, 18, '2025-06-24', 'Presente', 10),
(124, 11, 6, 18, 18, '2025-06-24', 'Presente', 11),
(125, 12, 6, 18, 18, '2025-06-24', 'Presente', 11),
(126, 13, 6, 18, 18, '2025-06-24', 'Presente', 11),
(127, 14, 6, 18, 18, '2025-06-24', 'Presente', 11),
(128, 15, 6, 18, 18, '2025-06-24', 'Presente', 11),
(129, 16, 6, 18, 18, '2025-06-24', 'Justificado', 11),
(130, 17, 6, 18, 18, '2025-06-24', 'Presente', 11),
(131, 18, 6, 18, 18, '2025-06-24', 'Presente', 11),
(132, 19, 6, 18, 18, '2025-06-24', 'Presente', 11),
(133, 20, 6, 18, 18, '2025-06-24', 'Presente', 11),
(134, 11, 14, 41, 18, '2025-06-23', 'Presente', 12),
(135, 12, 14, 41, 18, '2025-06-23', 'Ausente', 12),
(136, 14, 14, 41, 18, '2025-06-23', 'Ausente', 12),
(137, 13, 14, 41, 18, '2025-06-23', 'Presente', 12),
(138, 11, 14, 41, 18, '2025-06-24', 'Presente', 13),
(139, 12, 14, 41, 18, '2025-06-24', 'Ausente', 13),
(140, 14, 14, 41, 18, '2025-06-24', 'Presente', 13),
(141, 13, 14, 41, 18, '2025-06-24', 'Presente', 13),
(142, 11, 14, 41, 18, '2025-06-23', 'Presente', 14),
(143, 12, 14, 41, 18, '2025-06-23', 'Presente', 14),
(144, 14, 14, 41, 18, '2025-06-23', 'Ausente', 14),
(145, 13, 14, 41, 18, '2025-06-23', 'Presente', 14),
(146, 11, 14, 41, 18, '2025-06-24', 'Presente', 15),
(147, 12, 14, 41, 18, '2025-06-24', 'Presente', 15),
(148, 14, 14, 41, 18, '2025-06-24', 'Presente', 15),
(149, 13, 14, 41, 18, '2025-06-24', 'Presente', 15),
(150, 11, 14, 41, 18, '2025-06-23', 'Presente', 16),
(151, 12, 14, 41, 18, '2025-06-23', 'Presente', 16),
(152, 14, 14, 41, 18, '2025-06-23', 'Presente', 16),
(153, 13, 14, 41, 18, '2025-06-23', 'Presente', 16);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL,
  `nombre_curso` varchar(100) NOT NULL,
  `id_area` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id`, `nombre_curso`, `id_area`) VALUES
(1, 'Administración', 1),
(2, 'Secretariado', 1),
(3, 'Contabilidad', 1),
(4, 'Computación', 1),
(5, 'Auxiliar de Educación', 5),
(6, 'Inglés', 5),
(7, 'Quechua', 5),
(8, 'Gastronomía', 2),
(9, 'Cosmetología', 2),
(10, 'Barbería', 2),
(11, 'Maquillaje', 2),
(12, 'Topografía', 3),
(13, 'Maestro de obra', 3),
(14, 'Especialista en CAD', 3),
(15, 'Diseño de Interiores', 3),
(16, 'Auxiliar de Enfermería', 4),
(17, 'Auxiliar de Farmacia', 4),
(18, 'Auxiliar de Geriatría', 4),
(19, 'Auxiliar de Fisioterapia', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `dia` varchar(20) NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `horarios`
--

INSERT INTO `horarios` (`id`, `curso_id`, `dia`, `hora_inicio`, `hora_fin`) VALUES
(1, 1, 'Lunes', '08:00:00', '10:00:00'),
(2, 1, 'Miércoles', '08:00:00', '10:00:00'),
(3, 1, 'Viernes', '08:00:00', '10:00:00'),
(4, 2, 'Sábado', '08:00:00', '14:00:00'),
(5, 2, 'Domingo', '08:00:00', '14:00:00'),
(6, 2, 'Lunes', '18:00:00', '20:00:00'),
(7, 3, 'Martes', '09:00:00', '11:00:00'),
(8, 3, 'Jueves', '09:00:00', '11:00:00'),
(9, 3, 'Viernes', '09:00:00', '11:00:00'),
(10, 4, 'Sábado', '08:00:00', '14:00:00'),
(11, 4, 'Domingo', '08:00:00', '14:00:00'),
(12, 4, 'Martes', '18:00:00', '20:00:00'),
(13, 5, 'Lunes', '15:00:00', '17:00:00'),
(14, 5, 'Miércoles', '15:00:00', '17:00:00'),
(15, 5, 'Viernes', '15:00:00', '17:00:00'),
(16, 6, 'Martes', '08:00:00', '10:00:00'),
(17, 6, 'Jueves', '08:00:00', '10:00:00'),
(18, 6, 'Sábado', '10:00:00', '12:00:00'),
(19, 7, 'Domingo', '14:00:00', '20:00:00'),
(20, 7, 'Miércoles', '10:00:00', '12:00:00'),
(21, 7, 'Viernes', '10:00:00', '12:00:00'),
(22, 8, 'Lunes', '14:00:00', '16:00:00'),
(23, 8, 'Miércoles', '14:00:00', '16:00:00'),
(24, 8, 'Viernes', '14:00:00', '16:00:00'),
(25, 9, 'Sábado', '08:00:00', '14:00:00'),
(26, 9, 'Domingo', '08:00:00', '14:00:00'),
(27, 9, 'Jueves', '18:00:00', '20:00:00'),
(28, 10, 'Martes', '10:00:00', '12:00:00'),
(29, 10, 'Jueves', '10:00:00', '12:00:00'),
(30, 10, 'Viernes', '10:00:00', '12:00:00'),
(31, 11, 'Sábado', '14:00:00', '20:00:00'),
(32, 11, 'Domingo', '14:00:00', '20:00:00'),
(33, 11, 'Lunes', '18:00:00', '20:00:00'),
(34, 12, 'Martes', '08:00:00', '10:00:00'),
(35, 12, 'Jueves', '08:00:00', '10:00:00'),
(36, 12, 'Viernes', '08:00:00', '10:00:00'),
(37, 13, 'Sábado', '08:00:00', '14:00:00'),
(38, 13, 'Domingo', '08:00:00', '14:00:00'),
(39, 13, 'Miércoles', '18:00:00', '20:00:00'),
(40, 14, 'Lunes', '10:00:00', '12:00:00'),
(41, 14, 'Miércoles', '10:00:00', '12:00:00'),
(42, 14, 'Viernes', '10:00:00', '12:00:00'),
(43, 15, 'Sábado', '08:00:00', '14:00:00'),
(44, 15, 'Domingo', '08:00:00', '14:00:00'),
(45, 15, 'Martes', '18:00:00', '20:00:00'),
(46, 16, 'Lunes', '13:00:00', '15:00:00'),
(47, 16, 'Miércoles', '13:00:00', '15:00:00'),
(48, 16, 'Viernes', '13:00:00', '15:00:00'),
(49, 17, 'Martes', '14:00:00', '16:00:00'),
(50, 17, 'Jueves', '14:00:00', '16:00:00'),
(51, 17, 'Sábado', '10:00:00', '12:00:00'),
(52, 18, 'Domingo', '14:00:00', '20:00:00'),
(53, 18, 'Miércoles', '10:00:00', '12:00:00'),
(54, 18, 'Viernes', '10:00:00', '12:00:00'),
(55, 19, 'Sábado', '08:00:00', '14:00:00'),
(56, 19, 'Domingo', '08:00:00', '14:00:00'),
(57, 19, 'Lunes', '18:00:00', '20:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenes`
--

CREATE TABLE `imagenes` (
  `id` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matriculas`
--

CREATE TABLE `matriculas` (
  `id` int(11) NOT NULL,
  `alumno_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `horario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `matriculas`
--

INSERT INTO `matriculas` (`id`, `alumno_id`, `curso_id`, `horario_id`) VALUES
(30, 11, 6, 18),
(31, 12, 6, 18),
(32, 13, 6, 18),
(33, 14, 6, 18),
(34, 11, 14, 41),
(35, 12, 14, 41),
(36, 14, 14, 41),
(37, 13, 14, 41),
(38, 15, 6, 18),
(39, 16, 6, 18),
(40, 17, 6, 18),
(41, 18, 6, 18),
(42, 19, 6, 18),
(43, 20, 6, 18),
(44, 13, 9, 25),
(45, 12, 9, 27),
(46, 21, 2, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas`
--

CREATE TABLE `notas` (
  `id` int(11) NOT NULL,
  `alumno_id` int(11) NOT NULL,
  `asignatura` varchar(100) NOT NULL,
  `nota_01` decimal(5,2) NOT NULL,
  `nota_02` decimal(5,2) NOT NULL,
  `nota_03` decimal(5,2) NOT NULL,
  `ciclo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `notas`
--

INSERT INTO `notas` (`id`, `alumno_id`, `asignatura`, `nota_01`, `nota_02`, `nota_03`, `ciclo`) VALUES
(1, 11, 'Especialista en CAD', 12.50, 11.00, 14.00, NULL),
(2, 12, 'Especialista en CAD', 7.80, 12.60, 15.00, NULL),
(3, 14, 'Especialista en CAD', 10.00, 0.00, 0.00, NULL),
(4, 13, 'Especialista en CAD', 15.00, 16.00, 11.00, NULL),
(5, 11, 'Inglés', 19.50, 0.00, 0.00, NULL),
(6, 12, 'Inglés', 0.00, 0.00, 0.00, NULL),
(7, 13, 'Inglés', 0.00, 0.00, 0.00, NULL),
(8, 14, 'Inglés', 0.00, 0.00, 0.00, NULL),
(9, 15, 'Inglés', 0.00, 0.00, 0.00, NULL),
(10, 16, 'Inglés', 0.00, 0.00, 0.00, NULL),
(11, 17, 'Inglés', 0.00, 0.00, 0.00, NULL),
(12, 18, 'Inglés', 0.00, 0.00, 0.00, NULL),
(13, 19, 'Inglés', 0.00, 0.00, 0.00, NULL),
(14, 20, 'Inglés', 0.00, 0.00, 0.00, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores`
--

CREATE TABLE `profesores` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `dni` varchar(8) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `horario` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `profesores`
--

INSERT INTO `profesores` (`id`, `usuario_id`, `nombre`, `apellidos`, `dni`, `telefono`, `horario`) VALUES
(18, 109, 'Guillermo ', 'Damariz', '45904717', '985698112', NULL),
(22, 118, 'Fabio ', 'Ortiz', '44948557', '902875616', NULL),
(23, 121, 'Dante', 'Romel', '4868957', '965324123', NULL),
(24, 122, 'cg', 'g', '14563668', '965324123', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('administrador','profesor','alumno') NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `correo`, `password`, `rol`, `estado`) VALUES
(1, 'usuario@admin', '$2y$10$T1gIat6d2PYnNdmrZyS2ku/fwm4ABk.8I7EsEcyUUFtVgwwAjgnF6', 'administrador', 'activo'),
(106, 'cristobal.ramirez@cibertronic.com', '$2y$10$pKoFINx2WpPm6DUj8YGcDOkdnaMt0Ipsi48G2zAx2C9pKTZBIklLG', 'alumno', 'activo'),
(107, 'sebastian.salazar@cibertronic.com', '$2y$10$jEfqFQSI35vXbG52BYdzGOlipmgm5LXUAtgPYFdMaXglr.iVonerq', 'alumno', 'activo'),
(109, 'Guillermo.damariz@cibertronic.com', '$2y$10$0aqWrpUs2ThoRjWBk6kRtOY5XD30h4WKVL/JzzgdMEk20kJLbstLK', 'profesor', 'activo'),
(110, 'Jadir.guzman@cibertronic.com', '$2y$10$6q/NRYkFZAC6e8iZ07VDH..PW5oVRf6DLr6C8jVvzLjEHiENSbfY2', 'alumno', 'activo'),
(111, 'Jose.vidaurre@cibertronic.com', '$2y$10$j9wHHzGao2gpi.WfVJsaAesiRM4xEtjFDcmGZeXC/ZT6RP/EwwXeK', 'alumno', 'activo'),
(112, 'Jhonny.jimenez@cibertronic.com', '$2y$10$Aa85ppq/JM2aExGqjoShce8L3Kc2puqrmI.8FQrvM9xWUwo7RjLP6', 'alumno', 'activo'),
(113, 'Cesar.Yontop@cibertronic.com', '$2y$10$Z4YBVfAOv/cO1Co1VV1yk.gqgfyERjy//KyBoaGVtktGT53dNfrJK', 'alumno', 'activo'),
(114, 'Paul.Rios@cibertronic.com', '$2y$10$3/tS/B1ThLoptlOEiPoYlOv8Py5Dfg8zow9QQ7Byeg3mGT/NYKNbK', 'alumno', 'activo'),
(115, 'Raul.Francis@cibertronic.com', '$2y$10$moRo/iz5B8i8NfoTOZrs3.n6MYBDAroRWfJJ8qDngwPyOgYCatg.6', 'alumno', 'activo'),
(116, 'Santos.Yupanqui@cibertronic.com', '$2y$10$UhbGjyu3ZLmKQrIwa6z34OcgFrjZYF7CmnLgU3w9YTHOuJnDvP/HO', 'alumno', 'activo'),
(117, 'Carlos.Ramirez@cibertronic.com', '$2y$10$AmamYmK3ogG3xLhpHQfYFe4T4GzmMwRXKfIr8z2FJOKXjaNGFWhnW', 'alumno', 'activo'),
(118, 'Fabio.Ortiz@cibertronic.com', '$2y$10$KVSV4DRdBPmsbfd5CrIcoephbqWJi9oZ472iZ.n9FD/y9oSA.EAsu', 'profesor', 'activo'),
(119, 'Damian.Herrera@gmail.com', '$2y$10$U5NDuOeZnfqteU05WVEqO.72BT7d2nrZfGvBg.zXXycRCbpj5GGe2', 'administrador', 'activo'),
(120, 'Fabiola.Ortiz@cibertronic.com', '$2y$10$YxRT0uvrHW19nFzhulEHIuZBtuJ3D4BgVwHEcTgRczV1kv9/IA/bO', 'alumno', 'activo'),
(121, 'Dante.Romel@cibertronic.com', '$2y$10$HKEKi.KpF84nnzoMMPq42.Vl3uKnrxYnEl0xXUkpVO9RItqxJ6ure', 'profesor', 'activo'),
(122, 'c@g.com', '$2y$10$W93IsBY211YO2.9QXlWqW.sz7u/uZ1u5m81.Ky3PQE4GK0lCZdDS.', 'profesor', 'activo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD KEY `fk_admin_usuario` (`usuario_id`);

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `asignaciones`
--
ALTER TABLE `asignaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_asignacion` (`codigo_asignacion`),
  ADD KEY `profesor_id` (`profesor_id`),
  ADD KEY `curso_id` (`curso_id`),
  ADD KEY `fk_asignaciones_horario` (`horario_id`);

--
-- Indices de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alumno_id` (`alumno_id`),
  ADD KEY `fk_profesor` (`profesor_id`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_area` (`id_area`);

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `curso_id` (`curso_id`);

--
-- Indices de la tabla `imagenes`
--
ALTER TABLE `imagenes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `matriculas`
--
ALTER TABLE `matriculas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alumno_id` (`alumno_id`),
  ADD KEY `curso_id` (`curso_id`),
  ADD KEY `fk_matriculas_horario` (`horario_id`);

--
-- Indices de la tabla `notas`
--
ALTER TABLE `notas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_alumno_asignatura` (`alumno_id`,`asignatura`);

--
-- Indices de la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administrador`
--
ALTER TABLE `administrador`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `areas`
--
ALTER TABLE `areas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `asignaciones`
--
ALTER TABLE `asignaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de la tabla `imagenes`
--
ALTER TABLE `imagenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `matriculas`
--
ALTER TABLE `matriculas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de la tabla `notas`
--
ALTER TABLE `notas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT de la tabla `profesores`
--
ALTER TABLE `profesores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD CONSTRAINT `fk_admin_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD CONSTRAINT `alumnos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `asignaciones`
--
ALTER TABLE `asignaciones`
  ADD CONSTRAINT `asignaciones_ibfk_1` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asignaciones_ibfk_2` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_asignaciones_horario` FOREIGN KEY (`horario_id`) REFERENCES `horarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD CONSTRAINT `asistencia_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_profesor` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id`);

--
-- Filtros para la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD CONSTRAINT `fk_cursos_areas` FOREIGN KEY (`id_area`) REFERENCES `areas` (`id`);

--
-- Filtros para la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`);

--
-- Filtros para la tabla `matriculas`
--
ALTER TABLE `matriculas`
  ADD CONSTRAINT `fk_matriculas_horario` FOREIGN KEY (`horario_id`) REFERENCES `horarios` (`id`),
  ADD CONSTRAINT `matriculas_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matriculas_ibfk_2` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notas`
--
ALTER TABLE `notas`
  ADD CONSTRAINT `notas_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD CONSTRAINT `profesores_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
