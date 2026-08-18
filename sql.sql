CREATE TABLE IF NOT EXISTS valor_area_enchape (
  id bigint unsigned NOT NULL AUTO_INCREMENT,
  descripccion varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  area_min int NOT NULL DEFAULT '0',
  area_max int NOT NULL DEFAULT '0',
  valor_intervalo bigint NOT NULL DEFAULT '0',
  año bigint NOT NULL DEFAULT '0',
  createdAt datetime DEFAULT NULL,
  updatedAt datetime DEFAULT NULL,
  PRIMARY KEY (id)
) 

CREATE TABLE IF NOT EXISTS `planilla_entregables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tipo` int unsigned NOT NULL,
  `unidad` int unsigned NOT NULL DEFAULT '1',
  `cantidad` double unsigned NOT NULL DEFAULT (0),
  `valor_uni` bigint NOT NULL DEFAULT (0),
  `descripccion` text NOT NULL,
  `id_proyecto` bigint NOT NULL,
  `id_user` bigint NOT NULL,
  `createdAt` datetime DEFAULT NULL,
  `updatedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE IF NOT EXISTS `planilla_confi_proyecto` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `valor_config` text,
  `tipo` int unsigned NOT NULL,
  `id_user` bigint NOT NULL,
  `id_proyecto` bigint NOT NULL,
  `createdAt` datetime DEFAULT NULL,
  `updatedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `proyecto_novedades` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `novedades` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `id_proyecto` bigint NOT NULL,
  `id_user` bigint NOT NULL,
  `estado` int NOT NULL DEFAULT '1',
  `comentario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `fecha_respuesta` date DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  `updatedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;