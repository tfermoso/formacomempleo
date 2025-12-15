-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema formacomempleo
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `formacomempleo` ;

-- -----------------------------------------------------
-- Schema formacomempleo
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `formacomempleo` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ;
USE `formacomempleo` ;

-- -----------------------------------------------------
-- Table `formacomempleo`.`candidatos`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `formacomempleo`.`candidatos` ;

CREATE TABLE IF NOT EXISTS `formacomempleo`.`candidatos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `dni` VARCHAR(20) NULL DEFAULT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `apellidos` VARCHAR(150) NOT NULL,
  `telefono` VARCHAR(20) NULL DEFAULT NULL,
  `email` VARCHAR(150) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `linkedin` VARCHAR(255) NULL DEFAULT NULL,
  `web` VARCHAR(255) NULL DEFAULT NULL,
  `cv` VARCHAR(255) NULL DEFAULT NULL,
  `foto` VARCHAR(255) NULL DEFAULT NULL,
  `direccion` VARCHAR(200) NULL DEFAULT NULL,
  `cp` VARCHAR(10) NULL DEFAULT NULL,
  `ciudad` VARCHAR(100) NULL DEFAULT NULL,
  `provincia` VARCHAR(100) NULL DEFAULT NULL,
  `fecha_nacimiento` DATE NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email` (`email` ASC) ,
  UNIQUE INDEX `dni` (`dni` ASC) ,
  INDEX `idx_candidatos_ciudad` (`ciudad` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `formacomempleo`.`empresas`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `formacomempleo`.`empresas` ;

CREATE TABLE IF NOT EXISTS `formacomempleo`.`empresas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `cif` VARCHAR(20) NOT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `telefono` VARCHAR(20) NULL DEFAULT NULL,
  `web` VARCHAR(200) NULL DEFAULT NULL,
  `persona_contacto` VARCHAR(150) NULL DEFAULT NULL,
  `email_contacto` VARCHAR(150) NULL DEFAULT NULL,
  `direccion` VARCHAR(200) NULL DEFAULT NULL,
  `cp` VARCHAR(10) NULL DEFAULT NULL,
  `ciudad` VARCHAR(100) NULL DEFAULT NULL,
  `provincia` VARCHAR(100) NULL DEFAULT NULL,
  `logo` VARCHAR(255) NULL DEFAULT NULL,
  `verificada` TINYINT(1) NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `cif` (`cif` ASC) ,
  INDEX `idx_empresas_ciudad` (`ciudad` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `formacomempleo`.`sectores`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `formacomempleo`.`sectores` ;

CREATE TABLE IF NOT EXISTS `formacomempleo`.`sectores` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `nombre` (`nombre` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `formacomempleo`.`empresa_sector`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `formacomempleo`.`empresa_sector` ;

CREATE TABLE IF NOT EXISTS `formacomempleo`.`empresa_sector` (
  `idempresa` INT(11) NOT NULL,
  `idsector` INT(11) NOT NULL,
  PRIMARY KEY (`idempresa`, `idsector`),
  INDEX `fk_es_sector` (`idsector` ASC) ,
  CONSTRAINT `fk_es_empresa`
    FOREIGN KEY (`idempresa`)
    REFERENCES `formacomempleo`.`empresas` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_es_sector`
    FOREIGN KEY (`idsector`)
    REFERENCES `formacomempleo`.`sectores` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `formacomempleo`.`modalidad`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `formacomempleo`.`modalidad` ;

CREATE TABLE IF NOT EXISTS `formacomempleo`.`modalidad` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `nombre` (`nombre` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `formacomempleo`.`ofertas`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `formacomempleo`.`ofertas` ;

CREATE TABLE IF NOT EXISTS `formacomempleo`.`ofertas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idempresa` INT(11) NOT NULL,
  `idsector` INT(11) NOT NULL,
  `idmodalidad` INT(11) NOT NULL,
  `titulo` VARCHAR(200) NOT NULL,
  `descripcion` TEXT NOT NULL,
  `requisitos` TEXT NULL DEFAULT NULL,
  `funciones` TEXT NULL DEFAULT NULL,
  `salario_min` DECIMAL(10,2) NULL DEFAULT NULL,
  `salario_max` DECIMAL(10,2) NULL DEFAULT NULL,
  `tipo_contrato` VARCHAR(100) NULL DEFAULT NULL,
  `jornada` VARCHAR(100) NULL DEFAULT NULL,
  `ubicacion` VARCHAR(150) NULL DEFAULT NULL,
  `fecha_publicacion` DATE NULL DEFAULT NULL,
  `publicar_hasta` DATE NULL DEFAULT NULL,
  `fecha_incorporacion` DATE NULL DEFAULT NULL,
  `estado` ENUM('borrador', 'publicada', 'pausada', 'cerrada', 'vencida') NULL DEFAULT 'borrador',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_oferta_modalidad` (`idmodalidad` ASC) ,
  INDEX `idx_ofertas_empresa` (`idempresa` ASC) ,
  INDEX `idx_ofertas_sector` (`idsector` ASC) ,
  INDEX `idx_ofertas_estado` (`estado` ASC) ,
  CONSTRAINT `fk_oferta_empresa`
    FOREIGN KEY (`idempresa`)
    REFERENCES `formacomempleo`.`empresas` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_oferta_modalidad`
    FOREIGN KEY (`idmodalidad`)
    REFERENCES `formacomempleo`.`modalidad` (`id`),
  CONSTRAINT `fk_oferta_sector`
    FOREIGN KEY (`idsector`)
    REFERENCES `formacomempleo`.`sectores` (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `formacomempleo`.`ofertas_candidatos`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `formacomempleo`.`ofertas_candidatos` ;

CREATE TABLE IF NOT EXISTS `formacomempleo`.`ofertas_candidatos` (
  `idoferta` INT(11) NOT NULL,
  `idcandidato` INT(11) NOT NULL,
  `fecha_inscripcion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `estado` ENUM('inscrito', 'revisado', 'preseleccionado', 'entrevista', 'descartado', 'finalista', 'contratado') NULL DEFAULT 'inscrito',
  `comentarios` TEXT NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`idoferta`, `idcandidato`),
  INDEX `fk_oc_candidato` (`idcandidato` ASC) ,
  CONSTRAINT `fk_oc_candidato`
    FOREIGN KEY (`idcandidato`)
    REFERENCES `formacomempleo`.`candidatos` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_oc_oferta`
    FOREIGN KEY (`idoferta`)
    REFERENCES `formacomempleo`.`ofertas` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `formacomempleo`.`usuarios`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `formacomempleo`.`usuarios` ;

CREATE TABLE IF NOT EXISTS `formacomempleo`.`usuarios` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `apellidos` VARCHAR(150) NOT NULL,
  `telefono` VARCHAR(20) NULL DEFAULT NULL,
  `email` VARCHAR(150) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `idempresa` INT(11) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `is_admin` TINYINT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email` (`email` ASC) ,
  INDEX `fk_usuario_empresa` (`idempresa` ASC) ,
  CONSTRAINT `fk_usuario_empresa`
    FOREIGN KEY (`idempresa`)
    REFERENCES `formacomempleo`.`empresas` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
