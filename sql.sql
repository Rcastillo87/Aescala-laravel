
ALTER TABLE proyectos
    -- Eliminar columnas que ya no se usan
    DROP COLUMN val_obra_blanca,
    DROP COLUMN val_obra_blanca_materiales,
    DROP COLUMN pres_otros,
    DROP COLUMN observacion,
    DROP COLUMN val_obra_carpinteria,
    DROP COLUMN val_carpinteria_materiales,

    -- Agregar columnas nuevas
    ADD COLUMN cedula_cliente varchar(50) NULL AFTER direccion,
    ADD COLUMN tipo_doc_cliente int(11) NULL AFTER cedula_cliente,
    ADD COLUMN id_user_comercial bigint(20) NULL AFTER id_user_carpinteria,
    ADD COLUMN area_privada int(11) NULL AFTER id_user_comercial,
    ADD COLUMN termino_1_por int(11) NULL AFTER area_privada,
    ADD COLUMN termino_2_por int(11) NULL AFTER termino_1_por,
    ADD COLUMN termino_3_por int(11) NULL AFTER termino_2_por,
    ADD COLUMN termino_4_por int(11) NULL AFTER termino_3_por,
    ADD COLUMN termino_5_por int(11) NULL AFTER termino_4_por,
    ADD COLUMN termino_6_por int(11) NULL AFTER termino_5_por,
    ADD COLUMN opcion int(11) NULL AFTER termino_6_por,
    ADD COLUMN por_inicia int(11) NULL AFTER opcion,
    ADD COLUMN img_firma TEXT NULL AFTER por_inicia;

ALTER TABLE proyectos
    MODIFY COLUMN fec_inicio DATE NULL;
ALTER TABLE proyectos
    MODIFY COLUMN id_user BIGINT NULL;
ALTER TABLE proyectos 
    MODIFY id_estado INT NULL DEFAULT NULL;
    

CREATE TABLE IF NOT EXISTS `entregables` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_estregable` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla aescala.entregable_proyecto
CREATE TABLE IF NOT EXISTS `entregable_proyecto` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_entregable` bigint(20) NOT NULL,
  `id_proyecto` bigint(20) NOT NULL,
  `cantidad` bigint(20) NOT NULL,
  `valor_total` bigint(20) NOT NULL,
  `tx_entregable` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;