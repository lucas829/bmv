START TRANSACTION;
-- ---
-- Datos iniciales
-- ---

-- Grados
-- -------
INSERT INTO grados (`id`,`descrip`) VALUES
(1,'Aprendices'),
(2,'Compañeros'),
(3,'Maestros');

-- Permisos
-- ---------
INSERT INTO permisos (`id`,`descrip`) VALUES
(1,'Administrador'),
(2,'Usuario');

-- Cargos
-- ------
INSERT INTO `cargos` (`id`,`descrip`) VALUES
(1,'Ninguno'),
(2,'Venerable Maestro'),
(3,'Primer Vigilante'),
(4,'Segundo Vigilante'),
(5,'Orador'),
(6,'Secretario'),
(7,'Experto'),
(8,'Tesorero'),
(9,'Maestro de Ceremonias'),
(10,'Hospitalario'),
(11,'Guardatemplo');

-- Usuarios
-- ---------
INSERT INTO users (`id`,`username`,`password`,`nombre`,`apellido`,`email`,`id_permiso`,`id_grado`,`id_cargo`) VALUES
(1,'admin','d033e22ae348aeb5660fc2140aec35850c4da997','Administrador','Admin','admin@admin.com',1,3,1),
(2,'user','3da541559918a808c2402bba5012f6c60b27661c','Usuario','User','user@user.com',2,1,1),
(3,'compañero','3da541559918a808c2402bba5012f6c60b27661c','Compañero','Comp','comp@comp.com',2,2,1);

-- Carpetas
-- ---------
INSERT INTO carpetas (`id`,`descrip`) VALUES
(1,'Rituales'),
(2,'Planchas'),
(3,'Legislación'),
(4,'Libros');

-- Estado de documento
-- -------------------
INSERT INTO `estados` (`id`,`descrip`) VALUES
(1,'publicado'),
(2,'pendiente'),
(3,'reportado'),
(4,'eliminado');

-- Tipos de archivo
-- ----------------
INSERT INTO `tipo` (`id`,`descrip`,`mime`) VALUES
(1,'Otro','application/octet-stream'),
(2,'Word','application/msword'),
(3,'Excel','application/vnd.ms-excel'),
(4,'Powerpoint','application/vnd.ms-powerpoint'),
(5,'PDF','application/pdf'),
(6,'Texto plano','text/plain'),
(7,'Imagen PNG','image/png'),
(8,'Imagen JPEG','image/jpeg'),
(9,'Imagen GIF','image/gif'),
(10,'Imagen de Mapa de bits BMP','image/BMP'),
(11,'Imagen TIFF','image/tiff'),
(12,'Archivo comprimido ZIP','application/zip'),
(13,'Archivo comprimido RAR','x-rar-compressed'),
(14,'Dibujo vectorial','image/x-coreldraw'),
(15,'Texto Open Document','application/vnd.oasis.opendocument.text'),
(16,'Hoja de cálculo Open Document','application/vnd.oasis.opendocument.spreadsheet'),
(17,'Presentación OpenDocument','application/vnd.oasis.opendocument.presentation'),
(18,'Audio MP3','audio/mpeg'),
(19,'Audio OGG','application/octet-stream'),
(20,'Audio WAV','audio/x-wav'),
(21,'Texto enriquecido','application/rtf');

-- Etiquetas de los anuncios
-- -------------------------
INSERT INTO `etiquetas` (`id`,`descrip`) VALUES
(1,'Urgente'),
(2,'Importante'),
(3,'Informativo');

COMMIT;
