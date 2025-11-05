CREATE TABLE IF NOT EXISTS `areas` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nombre_area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla aescala.area_entregables
CREATE TABLE IF NOT EXISTS `area_entregables` (
  `id_otro_si` bigint NOT NULL,
  `id_area` bigint NOT NULL,
  `descripccion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cantidad` bigint NOT NULL,
  `valor` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla aescala.otro_si
CREATE TABLE IF NOT EXISTS `otro_si` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `id_proyecto` bigint NOT NULL,
  `id_user_encargado` bigint NOT NULL,
  `numero` bigint NOT NULL,
  `fecha_creacion` timestamp NOT NULL,
  `plantilla` text COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_firma` timestamp NULL DEFAULT NULL,
  `estado` int NOT NULL DEFAULT '0',
  `sugerencia_cliente` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `img_firma` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `areas` (`id`, `nombre_area`) VALUES
	(1, 'ROPAS'),
	(2, 'COCINA'),
	(3, 'SALA'),
	(4, 'ZONA DE ESTAR'),
	(5, 'BAÑO SOCIAL'),
	(6, 'BAÑO PRIVADO'),
	(7, 'HABITACION PRINCIPAL'),
	(8, 'HABITACION AUXILIAR'),
	(9, 'APTO GENERAL');