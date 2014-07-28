
-- ---
-- Globals
-- ---

-- SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
-- SET FOREIGN_KEY_CHECKS=0;

-- ---
-- Table 'users'
-- 
-- ---

DROP TABLE IF EXISTS `users`;
		
CREATE TABLE `users` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(20) NOT NULL,
  `password` VARCHAR(40) NOT NULL,
  `nombre` VARCHAR(40) NULL DEFAULT NULL,
  `apellido` VARCHAR(40) NULL DEFAULT NULL,
  `email` VARCHAR(50) NOT NULL,
  `telcel` VARCHAR(40) NULL DEFAULT NULL,
  `id_permiso` BIGINT NOT NULL,
  `id_grado` BIGINT NOT NULL,
  `id_cargo` BIGINT NOT NULL,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'docs'
-- 
-- ---

DROP TABLE IF EXISTS `docs`;
		
CREATE TABLE `docs` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(255) NOT NULL,
  `filename` VARCHAR(255) NOT NULL,
  `descrip` VARCHAR(255) NULL DEFAULT NULL,
  `hash` VARCHAR(40) NOT NULL,
  `id_carpeta` BIGINT NOT NULL,
  `id_grado` BIGINT NOT NULL,
  `fecha` DATE NULL DEFAULT NULL,
  `size` BIGINT NOT NULL,
  `id_tipo` BIGINT NOT NULL,
  `id_estado` BIGINT NOT NULL,
  `subido_por` BIGINT NOT NULL,
  `descargas` BIGINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'grados'
-- 
-- ---

DROP TABLE IF EXISTS `grados`;
		
CREATE TABLE `grados` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `descrip` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'permisos'
-- 
-- ---

DROP TABLE IF EXISTS `permisos`;
		
CREATE TABLE `permisos` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `descrip` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'carpetas'
-- 
-- ---

DROP TABLE IF EXISTS `carpetas`;
		
CREATE TABLE `carpetas` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `descrip` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'estados'
-- 
-- ---

DROP TABLE IF EXISTS `estados`;
		
CREATE TABLE `estados` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `descrip` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'tipo'
-- 
-- ---

DROP TABLE IF EXISTS `tipo`;
		
CREATE TABLE `tipo` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `descrip` VARCHAR(255) NOT NULL,
  `mime` VARCHAR(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'resets'
-- 
-- ---

DROP TABLE IF EXISTS `resets`;
		
CREATE TABLE `resets` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `id_user` BIGINT NOT NULL,
  `code` VARCHAR(40) NOT NULL,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'reportes'
-- 
-- ---

DROP TABLE IF EXISTS `reportes`;
		
CREATE TABLE `reportes` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `id_doc` BIGINT NOT NULL,
  `id_user` BIGINT NOT NULL,
  `descrip` VARCHAR(255) NOT NULL,
  `visible` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'cargos'
-- 
-- ---

DROP TABLE IF EXISTS `cargos`;
		
CREATE TABLE `cargos` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `descrip` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'anuncios'
-- 
-- ---

DROP TABLE IF EXISTS `anuncios`;
		
CREATE TABLE `anuncios` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `id_grado` BIGINT NOT NULL,
  `titulo` VARCHAR(255) NOT NULL,
  `texto` MEDIUMTEXT NOT NULL,
  `id_user` BIGINT NOT NULL,
  `fecha_inicio` DATE NULL DEFAULT NULL,
  `fecha_fin` DATE NULL DEFAULT NULL,
  `id_etiqueta` BIGINT NOT NULL,
  `visible` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'etiquetas'
-- 
-- ---

DROP TABLE IF EXISTS `etiquetas`;
		
CREATE TABLE `etiquetas` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `descrip` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`)
);

-- ---
-- Foreign Keys 
-- ---

ALTER TABLE `users` ADD FOREIGN KEY (id_permiso) REFERENCES `permisos` (`id`);
ALTER TABLE `users` ADD FOREIGN KEY (id_grado) REFERENCES `grados` (`id`);
ALTER TABLE `users` ADD FOREIGN KEY (id_cargo) REFERENCES `cargos` (`id`);
ALTER TABLE `docs` ADD FOREIGN KEY (id_carpeta) REFERENCES `carpetas` (`id`);
ALTER TABLE `docs` ADD FOREIGN KEY (id_grado) REFERENCES `grados` (`id`);
ALTER TABLE `docs` ADD FOREIGN KEY (id_tipo) REFERENCES `tipo` (`id`);
ALTER TABLE `docs` ADD FOREIGN KEY (id_estado) REFERENCES `estados` (`id`);
ALTER TABLE `docs` ADD FOREIGN KEY (subido_por) REFERENCES `users` (`id`);
ALTER TABLE `resets` ADD FOREIGN KEY (id_user) REFERENCES `users` (`id`);
ALTER TABLE `reportes` ADD FOREIGN KEY (id_doc) REFERENCES `docs` (`id`);
ALTER TABLE `reportes` ADD FOREIGN KEY (id_user) REFERENCES `users` (`id`);
ALTER TABLE `anuncios` ADD FOREIGN KEY (id_grado) REFERENCES `grados` (`id`);
ALTER TABLE `anuncios` ADD FOREIGN KEY (id_user) REFERENCES `users` (`id`);
ALTER TABLE `anuncios` ADD FOREIGN KEY (id_etiqueta) REFERENCES `etiquetas` (`id`);

-- ---
-- Table Properties
-- ---

ALTER TABLE `users` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `docs` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `grados` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `permisos` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `carpetas` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `estados` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `tipo` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `resets` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `reportes` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `cargos` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `anuncios` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `etiquetas` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ---
-- Test Data
-- ---

-- INSERT INTO `users` (`id`,`username`,`password`,`nombre`,`apellido`,`email`,`telcel`,`id_permiso`,`id_grado`,`id_cargo`) VALUES
-- ('','','','','','','','','','');
-- INSERT INTO `docs` (`id`,`nombre`,`filename`,`descrip`,`hash`,`id_carpeta`,`id_grado`,`fecha`,`size`,`id_tipo`,`id_estado`,`subido_por`,`descargas`) VALUES
-- ('','','','','','','','','','','','','');
-- INSERT INTO `grados` (`id`,`descrip`) VALUES
-- ('','');
-- INSERT INTO `permisos` (`id`,`descrip`) VALUES
-- ('','');
-- INSERT INTO `carpetas` (`id`,`descrip`) VALUES
-- ('','');
-- INSERT INTO `estados` (`id`,`descrip`) VALUES
-- ('','');
-- INSERT INTO `tipo` (`id`,`descrip`,`mime`) VALUES
-- ('','','');
-- INSERT INTO `resets` (`id`,`id_user`,`code`) VALUES
-- ('','','');
-- INSERT INTO `reportes` (`id`,`id_doc`,`id_user`,`descrip`,`visible`) VALUES
-- ('','','','','');
-- INSERT INTO `cargos` (`id`,`descrip`) VALUES
-- ('','');
-- INSERT INTO `anuncios` (`id`,`id_grado`,`titulo`,`texto`,`id_user`,`fecha_inicio`,`fecha_fin`,`id_etiqueta`,`visible`) VALUES
-- ('','','','','','','','','');
-- INSERT INTO `etiquetas` (`id`,`descrip`) VALUES
-- ('','');

