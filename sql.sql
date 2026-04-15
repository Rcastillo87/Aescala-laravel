INSERT INTO areas_empresa
(id, nombre_area)
VALUES
(1, 'Diseño'),
(2, 'Contabilidad'),
(3, 'Comercial'),
(4, 'Carpinteria'),
(5, 'Bodega'),
(6, 'Operaciones');

uso laravel 12, flowbite, atom select y js

ALTER TABLE aescala.inventario_proveedores ADD tipo SMALLINT DEFAULT 1 NULL COMMENT '1 materiales, 2 insumos';
ALTER TABLE aescala.inventario_pedidos DROP FOREIGN KEY FK2_id_material;
ALTER TABLE inventario_pedidos ADD tipo SMALLINT DEFAULT 1 NULL COMMENT '1 materiales, 2 insumos';
