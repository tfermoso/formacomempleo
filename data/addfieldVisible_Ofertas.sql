ALTER TABLE `formacomempleo`.`ofertas` 
ADD COLUMN `visible` TINYINT NULL DEFAULT 0 AFTER `deleted_at`;
