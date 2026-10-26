-- ============================================================
-- Rediseño módulo Planilla
-- Empezar de 0: se eliminan las tablas actuales y se recrean.
-- (No se migran datos. proyectos y users se mantienen intactas.)
-- ============================================================

DROP TABLE IF EXISTS `planilla_confi_proyecto`;
DROP TABLE IF EXISTS `planilla_entregables`;


-- ============================================================
-- 1) planilla_valores_proyecto
--    Reemplaza a planilla_confi_proyecto.
--    Un registro por (proyecto, tipo). Solo valores numéricos.
--
--    Tipos válidos:
--      1 = Valor área Proyecto
--      2 = Valor área Enchape
--      4 = Obra Blanca
--      5 = Carpintería
--      6 = Excedente Enchape
--
--    El tipo 3 (Porcentajes del Proyecto) ya NO vive en esta tabla:
--    se movió a planilla_entregables.tipo = 5.
-- ============================================================
CREATE TABLE `planilla_valores_proyecto` (
  `id`          bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_proyecto` bigint NOT NULL,
  `tipo`        int unsigned NOT NULL,
  `valor`       bigint NOT NULL DEFAULT 0,
  `id_user`     bigint NOT NULL,
  `createdAt`   datetime DEFAULT NULL,
  `updatedAt`   datetime DEFAULT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_valores_proyecto_tipo` (`id_proyecto`, `tipo`),
  KEY `idx_valores_proyecto` (`id_proyecto`),
  KEY `idx_valores_tipo` (`tipo`),

  CONSTRAINT `fk_valores_proyecto_proyecto`
    FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_valores_proyecto_user`
    FOREIGN KEY (`id_user`) REFERENCES `users` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ============================================================
-- 2) planilla_entregables
--    Mismo nombre, estructura nueva.
--    Un registro por (proyecto, tipo). El array completo de ítems
--    vive dentro de `items` como JSON.
--
--    Tipos válidos:
--      1 = Adicionales contrato
--      2 = Adicionales obra
--      3 = Adicionales de carpintería
--      4 = Adicional mesones
--      5 = Porcentajes del Proyecto   (migrado del viejo tipo 3)
--
--    Forma del JSON según tipo:
--      tipos 1..4:
--        [{"unidad":int,"cantidad":float,"valor_uni":int,
--          "descripccion":string,"porcentage":int}, ...]
--
--      tipo 5:
--        [{"concepto":string,"porcentage":int,"en_pesos":0|1,
--          "valor_cobrar":int|null,"valor_cobrar_por":int|null,
--          "aprobacion":0|50|100,"aprobacion_por":int|null,
--          "aprobacion_at":datetime|null,
--          "fecha_pago":date|null,"fecha_pago_por":int|null}, ...]
-- ============================================================
CREATE TABLE `planilla_entregables` (
  `id`          bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_proyecto` bigint NOT NULL,
  `tipo`        int unsigned NOT NULL,
  `items`       longtext NOT NULL,
  `id_user`     bigint NOT NULL,
  `createdAt`   datetime DEFAULT NULL,
  `updatedAt`   datetime DEFAULT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_entregables_proyecto_tipo` (`id_proyecto`, `tipo`),
  KEY `idx_entregables_proyecto` (`id_proyecto`),
  KEY `idx_entregables_tipo` (`tipo`),

  CONSTRAINT `fk_entregables_proyecto`
    FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_entregables_user`
    FOREIGN KEY (`id_user`) REFERENCES `users` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


