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
