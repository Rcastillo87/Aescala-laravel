--delete campo de proyecto
'val_obra_blanca',
'val_obra_blanca_materiales',
'val_obra_carpinteria',
'val_carpinteria_materiales',
'pres_otros'
'observacion'

-- ademas  se agrega
'cedula_cliente',
'tipo_doc_cliente',
'area_privada' float

-- ademas  se agrega
'fec_inicio',  null
'fec_fin_estimado', null
'id_user' null

ALTER TABLE proyectos
    -- Eliminar columnas que ya no se usan
    DROP COLUMN val_obra_blanca,
    DROP COLUMN val_obra_blanca_materiales,
    DROP COLUMN pres_otros,
    DROP COLUMN observacion,
    DROP COLUMN val_obra_carpinteria,
    DROP COLUMN val_carpinteria_materiales,

    -- Agregar columnas nuevas
    ADD COLUMN cedula_cliente VARCHAR(20) NULL AFTER nombre_cliente;
    ADD COLUMN cedula_cliente varchar(50) NULL AFTER direccion,
    ADD COLUMN tipo_doc_cliente int(11) NULL AFTER cedula_cliente,
    ADD COLUMN id_user_comercial bigint(20) NULL AFTER id_user_carpinteria,
    ADD COLUMN area_privada int(11) NULL AFTER id_user_comercial,
    ADD COLUMN aprov_diseno_por int(11) NULL AFTER area_privada,
    ADD COLUMN ini_carpinteria_por int(11) NULL AFTER aprov_diseno_por,
    ADD COLUMN ini_enchape_por int(11) NULL AFTER ini_carpinteria_por,
    ADD COLUMN ini_griferia_por int(11) NULL AFTER ini_enchape_por,
    ADD COLUMN entrega_obra_por int(11) NULL AFTER ini_griferia_por,
    ADD COLUMN opcion int(11) NULL AFTER entrega_obra_por,
    ADD COLUMN por_inicia int(11) NULL AFTER opcion;

    MODIFY COLUMN fec_inicio DATE NULL;
    MODIFY COLUMN id_user INT NULL;


