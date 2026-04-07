CREATE TABLE dispositivo (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    device_serial VARCHAR(120) NOT NULL,
    manufacturer VARCHAR(80) NULL,
    model VARCHAR(80) NULL,
    brand VARCHAR(80) NULL,
    device VARCHAR(80) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY dispositivo_device_serial_unique (device_serial)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE georreferencias (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    device_id BIGINT UNSIGNED NOT NULL,
    lat DECIMAL(10,8) NOT NULL,
    lng DECIMAL(11,8) NOT NULL,
    accuracy DECIMAL(8,2) NULL,
    speed DECIMAL(8,2) NULL,
    battery TINYINT UNSIGNED NULL,
    request_at DATETIME NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_georreferencias_device
        FOREIGN KEY (device_id)
        REFERENCES dispositivo(id)
        ON DELETE CASCADE,

    INDEX idx_device_id (device_id),
    INDEX idx_request_at (request_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE aescala.proyectos ADD ubicacion INT NULL;
ALTER TABLE aescala.dias_festivos ADD comentario TEXT NULL;

CREATE TABLE aescala.dias_no_laboradosxproy (
	id_proyecto BIGINT NOT NULL,
	dia DATE NOT NULL,
	detalle TEXT NOT NULL
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE `almacenes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre_almacen` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `id_user` bigint NOT NULL,
  `tipo` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE aescala.notas_proyecto (
	id_proyecto BIGINT NOT NULL,
	nota TEXT NOT NULL
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;



CREATE TABLE `bitacoras` (
    `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_user`      BIGINT NULL,
    `servicio`     VARCHAR(255) NOT NULL,
    `metodo`       VARCHAR(10)  NOT NULL,
    `url`          VARCHAR(255) NOT NULL,
    `ip_address`   VARCHAR(45)  NOT NULL,
    `user_agent`   TEXT         NULL,
    `payload`      JSON         NOT NULL,
    `error`        JSON         NULL,
    `tipo`         VARCHAR(20)  NOT NULL COMMENT 'exito | error_validacion | error_inesperado',
    `status_code`  SMALLINT     NOT NULL,
    `duracion_ms`  INT UNSIGNED NULL,
    `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_id_user`    (`id_user`),
    INDEX `idx_servicio`   (`servicio`),
    INDEX `idx_tipo`       (`tipo`),
    INDEX `idx_created_at` (`created_at`),
    CONSTRAINT `fk_bitacoras_user`
        FOREIGN KEY (`id_user`) REFERENCES `users` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `insumos` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nombre_insumo` varchar(100) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `estado` smallint NOT NULL DEFAULT '1',
  `cantidad` bigint NOT NULL,
  `cantidad_min` bigint NOT NULL,
  `descripccion` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE aescala.areas_empresa (
	id BIGINT auto_increment NOT NULL,
	nombre_area varchar(100) NOT NULL,
	CONSTRAINT areas_empresa_pk PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE `insumos_entregados` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `id_area_empresa` bigint NOT NULL,
  `id_user` bigint DEFAULT NULL,
  `id_insumo` bigint NOT NULL,
  `cantidad` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


INSERT INTO areas_empresa
(id, nombre_area)
VALUES(1, 'Administracion'),
(2, 'Comercial'),
(3, 'Arquitectos');

ALTER TABLE dispositivo
ADD COLUMN nombre_equipo VARCHAR(120),
ADD COLUMN imei_1 VARCHAR(20),
ADD COLUMN imei_2 VARCHAR(20);
