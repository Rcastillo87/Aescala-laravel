INSERT INTO areas_empresa
(id, nombre_area)
VALUES
(1, 'Diseño'),
(2, 'Contabilidad'),
(3, 'Comercial'),(4, 'Carpinteria'),
(5, 'Bodega'),
(6, 'Operaciones');

uso laravel 12, flowbite, atom select y js

ALTER TABLE aescala.inventario_proveedores ADD tipo SMALLINT DEFAULT 1 NULL COMMENT '1 materiales, 2 insumos';
ALTER TABLE aescala.inventario_pedidos DROP FOREIGN KEY FK2_id_material;
ALTER TABLE inventario_pedidos ADD tipo SMALLINT DEFAULT 1 NULL COMMENT '1 materiales, 2 insumos';


ALTER TABLE aescala.almacenes ADD editar SMALLINT DEFAULT 0 NULL;

ALTER TABLE aescala.inventario_materiales ADD fase INT NULL;

update inventario_materiales set fase = 1
	where id in(185,6,103,170,65,177,228,179,229,122,120,279,125,412,97,100,99,101,191,169,174,189,250,251,241,128,190,127,323,80,109,110,111,359,46,43,344,194,197,200,105,195,262,201,299,216,314,196,199,205,334,337,338,339,317,315,204,316,499,301,217,221,220,218,219,121,210,264,214,259,198,206,207,208,213,350,348,303,340,349,304,341,107,322,357,236,212,211,401,409,193)


update inventario_materiales set fase = 2
	where id in(272,273,275,32,39,289,509,309,310,473,474,475,342,332,490,343,333,232,256,66,108);


update inventario_materiales set fase = 3
	where id in(139,148,11,137,67,8,79,48,242,30,7,76,75,318,321,58,60,327,82,84,142,141,287,288,352,504,293);


update inventario_materiales set fase = 4
	where id in(461,462,592,457,458,583,584,488,377,707,192,639,638);


ALTER TABLE pagos ADD COLUMN id_pago BIGINT UNSIGNED NULL AFTER tipo_pago;


CREATE TABLE documentos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    documentable_type VARCHAR(255) NOT NULL, -- El nombre del modelo o tabla (id_tabla)
    documentable_id BIGINT UNSIGNED NOT NULL,   -- El ID del registro relacionado (id_registro)
    nombre VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    contenido LONGBLOB NOT NULL,             -- Almacena el binario comprimido
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (documentable_type, documentable_id)
);


ALTER TABLE proyectos ADD COLUMN id_user_diseno BIGINT NULL AFTER id_user_comercial;

-- aescala.pagos definition

CREATE TABLE `pagos` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `id_proyecto` bigint NOT NULL,
  `id_user` bigint DEFAULT NULL,
  `valor` bigint unsigned DEFAULT '0',
  `fecha_pago` date NOT NULL,
  `comentario` text,
  `rc` varchar(20) NULL,
  `createdAt` datetime DEFAULT NULL,
  `updatedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pagos_id_proyecto` (`id_proyecto`),
  KEY `pagos_id_user` (`id_user`),
  CONSTRAINT `pagos_id_proyecto` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id`),
  CONSTRAINT `pagos_id_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`)
);

CREATE TABLE otro_si_refe_pago (
	id BIGINT auto_increment NOT NULL,
	id_otro_si BIGINT NOT NULL,
    id_user bigint DEFAULT NULL,
	referencia INT NOT NULL,
	valor BIGINT UNSIGNED DEFAULT 0 NOT NULL,
	CONSTRAINT otro_si_refe_pago_pk PRIMARY KEY (id)
);

ALTER TABLE proyectos ADD fecha_comision date NULL;
ALTER TABLE otro_si DROP COLUMN paz_salvo;

ALTER TABLE tarea_tipos DROP COLUMN createdAt;
ALTER TABLE tarea_tipos DROP COLUMN updatedAt;
ALTER TABLE tarea_tipos ADD porcentage INT UNSIGNED DEFAULT 0 NULL;


ALTER TABLE aescala.proyectos DROP FOREIGN KEY FK1_id_user_carpinteria;
ALTER TABLE aescala.proyectos DROP COLUMN id_user_carpinteria;
ALTER TABLE aescala.proyectos ADD user_carpinteria varchar(100) NULL;


ALTER TABLE area_entregables MODIFY COLUMN cantidad DOUBLE UNSIGNED NOT NULL;
ALTER TABLE area_entregables ADD unidad DOUBLE UNSIGNED DEFAULT 1 NULL;
ALTER TABLE area_entregables CHANGE unidad unidad INT UNSIGNED DEFAULT 1 NULL AFTER descripccion;


INSERT INTO areas
(id, nombre_area)
VALUES(33, 'Descuentos');

INSERT INTO entregables
(id, nombre_estregable)
VALUES(58, 'Puerta Principal');

ALTER TABLE tarea_tipos ADD dias_default INT UNSIGNED DEFAULT 0 NULL;


CREATE TABLE `cobro_refe` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_proyecto` bigint NOT NULL,
  `id_user` bigint NOT NULL,
  `referencia` int unsigned NOT NULL,
  `valor_pendiente` bigint unsigned NOT NULL,
  `fecha_notificacion` date DEFAULT NULL,
  `fecha_acuerdo_pago` date DEFAULT NULL,
  `fecha_pago_cli` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE cobro_refe ADD COLUMN estado INT DEFAULT 1;

CREATE TABLE `proyec_tx_refe` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `tx_descripcion` TEXT NULL,
    `referencia` VARCHAR(255) NULL,
    `id_proyecto` BIGINT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
