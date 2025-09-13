
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla aescala.entregables: ~8 rows (aproximadamente)
INSERT INTO `entregables` (`id`, `nombre_estregable`) VALUES
	(1, 'Obra blanca'),
	(2, 'Cocina integral'),
	(3, 'Closet principal'),
	(4, 'Closet auxiliar'),
	(5, 'Puertas'),
	(6, 'Mueble de Lavamanos'),
	(7, 'División de Baño en Vidrio'),
	(8, 'Sanitario');

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla aescala.entregable_proyecto
CREATE TABLE IF NOT EXISTS `entregable_proyecto` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_entregable` bigint(20) NOT NULL,
  `id_proyecto` bigint(20) NOT NULL,
  `cantidad` bigint(20) NOT NULL,
  `valor_total` bigint(20) NOT NULL,
  `tx_entregable` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK2_id_estregable` (`id_entregable`),
  KEY `FK5_id_proyecto` (`id_proyecto`),
  CONSTRAINT `FK2_id_estregable` FOREIGN KEY (`id_entregable`) REFERENCES `entregables` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK5_id_proyecto` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS `entregable_default` (
  `id_estregable` bigint(20) NOT NULL,
  `descripccion` text DEFAULT NULL,
  KEY `FK1_id_estregable` (`id_estregable`),
  CONSTRAINT `FK1_id_estregable` FOREIGN KEY (`id_estregable`) REFERENCES `entregables` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO entregable_default (id_estregable, descripccion) VALUES
(1, 'Pegante y fragua para todo el enchape'),
(1, 'Estuco relleno, estuco listo y pintura en todos los muros'),
(1, 'Cielo falso en panel yeso para cubrir tubería en zonas húmedas'),
(1, 'Estuco de loza en techo'),
(1, 'Mortero de nivelación de piso y/o poyos de closet'),
(1, 'Filos perforados para cada esquina del apto instalado en el interior del estuco'),
(1, 'Iluminación con balas led de sobreponer y de incrustar referencias seleccionadas'),
(1, 'Instalación de rejillas en acero inoxidable referencia seleccionada'),
(1, 'Transporte de materiales comprado por A.ESCALA'),
(1, 'Remoción de escombros'),
(1, 'Aseo completo del apartamento'),
(1, 'Enchapes de piso, 1 baño completo con accesorios y zona de oficios media altura de pared (opcional referencia seleccionada por A.escala, si el cliente la escoge a su preferencia bono de descuento para la compra de cerámica)'),

(2, 'Cocina integral mesón en quarstone blanco polar o Silestone de 1.60m'),
(2, 'Barra en quarstone blanco polar 1.60m'),
(2, 'Mueblería en RH inferior y superior'),
(2, 'Grifería de lujo en acero inoxidable'),
(2, 'Poceta en acero inoxidable'),
(2, 'Sistema de desagüe'),
(2, 'Condimentero'),
(2, 'Cubiertero'),
(2, 'Escurridor de platos en acero inoxidable'),
(2, 'Campana 3 velocidades, negra marca Haceb'),
(2, 'Estufa 4 boquillas, acero inoxidable marca Haceb'),
(2, 'Color de carpintería gusto del cliente'),

(3, 'Closet para alcoba principal con cajones, perchero y entrepaños'),

(4, 'Closet para alcoba auxiliar con cajones, perchero y entrepaños'),

(5, 'Puerta en RH con marco en RH, chapa y tope de puerta (opcional puerta corrediza para zona de lavado)'),

(6, 'Mueble en RH color y diseño al gusto del cliente'),
(6, 'Lavamanos de sobre poner o empotrar (referencia seleccionada)'),
(6, 'Grifería en acero'),
(6, 'Desagüe sistema push'),
(6, 'Llave de regulación'),

(7, 'División de baños de 1.20 x 1.90 corrediza en vidrio templado de 8mm con accesorios en acero inoxidable'),

(8, 'Sanitario dual doble descarga marca Modermicas');
