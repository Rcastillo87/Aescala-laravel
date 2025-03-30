update database.users set activo = 2 where activo = 0 

UPDATE database.herramientas set estado = 1 where estado = 'Nuevo';
UPDATE database.herramientas set estado = 2 where estado = 'Bueno';
UPDATE database.herramientas set estado = 3 where estado = 'Regular';
UPDATE database.herramientas set estado = 4 where estado = 'Dado de baja';

update herramienta_prestamos set tipo_prestamo = 1 where tipo_prestamo = 'Prestamo'
update herramienta_prestamos set tipo_prestamo = 2 where tipo_prestamo = 'Devolucion'

update database.inventario_materiales set activo = 2 where activo = 0 
UPDATE database.inventario_materiales set tipo = 1 where tipo = 'Obra Blanca';
UPDATE database.inventario_materiales set tipo = 2 where tipo = 'Carpinteria';

UPDATE database.proyectos set departamento = 29 where departamento = 'Valle del Cauca';

UPDATE database.proyectos set ciudad = 9 where ciudad = 'Cali';
UPDATE database.proyectos set ciudad = 40 where ciudad = 'Yumbo';
UPDATE database.proyectos set ciudad = 21 where ciudad = 'jamundí';

UPDATE database.finanzas set tipo = 1 where tipo = 'Ingreso_abono';
UPDATE database.finanzas set tipo = 2 where tipo = 'Gasto_Carpinteria';
UPDATE database.finanzas set tipo = 3 where tipo = 'Obra Gasto_Obra_Blanca';
UPDATE database.finanzas set tipo = 4 where tipo = 'Gasto_Otros';

se crea en avance fec_avance

UPDATE database.inventario_solicituds set tipo = 1 where tipo = 'Despachado';
UPDATE database.inventario_solicituds set tipo = 2 where tipo = 'Devolucion';

