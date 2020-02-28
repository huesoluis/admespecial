
DROP DATABASE IF EXISTS HGIR;

CREATE DATABASE IF NOT EXISTS HGIR;


USE HGIR;

CREATE TABLE `alumnos` (
	`id_alumno` INT NOT NULL,
	`id_usuario` INT NOT NULL,
	`apellido1` VARCHAR(500)   NOT NULL,
	`apellido2` VARCHAR(500)  NOT NULL,
	`nombre` VARCHAR(500)  NOT NULL,
	`dni` VARCHAR(500) NOT NULL,
	`nacionalidad` VARCHAR(500) NOT NULL DEFAULT 'española',
	`datos_tutor` VARCHAR(500) NOT NULL,
	`dni_tutor` VARCHAR(500)  NOT NULL,
	`calle_dfamiliar` VARCHAR(500) NOT NULL,
	`num_dfamiliar` INT NOT NULL,
	`piso_dfamiliar` VARCHAR(500) NOT NULL,
	`loc_dfamiliar` VARCHAR(500) NOT NULL,
	`cp_dfamiliar` INT NOT NULL,
	`tel_dfamiliar1` INT(9),
	`tel_dfamiliar2` INT(9),
	`id_centro_estudios_origen` INT,
	`modalidad_origen` VARCHAR(10),
	`modalidad_especial` VARCHAR(10),
	`id_centro_destino` INT NOT NULL,
	`modalidad_destino` INT,
	`id_centro_destino1` INT,
	`id_centro_destino2` INT,
	`id_centro_destino3` INT,
	`id_centro_destino4` INT,
	`id_centro_destino5` INT,
	`id_centro_destino6` INT,
	`fotocopia_resolucion` BOOLEAN NOT NULL DEFAULT '0' COMMENT 'Fotocopia compulsada de la Resolución del Director del Servicio Provincial de Educación en la Modalidad de Educación Especial o Combinada.',
	`reserva_plaza` BOOLEAN NOT NULL DEFAULT '0' COMMENT 'Anexo de reserva de plaza en el Centro de origen de Educación Especial.',
	`prematuridad` BOOLEAN NOT NULL DEFAULT '0' COMMENT 'Resolución del Director del Servicio Provincial de Educación, Cultura y Deporte correspondiente, en caso de prematuridad. (Sólo para el acceso por primera vez a segundo ciclo de Educación Infantil)',
	`acred_dom_familiar` BOOLEAN NOT NULL DEFAULT '0',
	`cer_rec_discapacidad` BOOLEAN NOT NULL DEFAULT '0',
	`fam_numerosa` BOOLEAN NOT NULL DEFAULT '0',
	`acred_dom_laboral` BOOLEAN NOT NULL DEFAULT '0',
	`permiten_renta` BOOLEAN NOT NULL DEFAULT '0',
	`cumplen_resta` BOOLEAN NOT NULL DEFAULT '0',
	UNIQUE KEY `cp_alumnos` (`id_alumno`) USING BTREE,
KEY `un_dni` (`dni`) USING BTREE
) ENGINE=InnoDB;

alter table alumnos add primary key (id_alumno);

CREATE TABLE `usuarios` (
	`id_usuario` INT NOT NULL AUTO_INCREMENT,
	`clave` VARCHAR(500)   NOT NULL,
	`nombre_usuario` VARCHAR(500)   NOT NULL,
	`rol` ENUM ('alumno','centro','sprovincial','admin') NOT NULL,
	UNIQUE KEY `cp_usuarios` (`id_usuario`) USING BTREE,
UNIQUE KEY `un_usuario` (`nombre_usuario`) USING BTREE
) ENGINE=InnoDB;

alter table usuarios add primary key (id_usuario);


alter table alumnos add foreign key (id_usuario) references usuarios(id_usuario);

CREATE TABLE `centros` (
	`id_centro` INT NOT NULL AUTO_INCREMENT,
	`id_usuario` INT NOT NULL,
	`localidad` VARCHAR(20) NOT NULL,
	`provincia` VARCHAR(20) NOT NULL,
	UNIQUE KEY `cp_centros` (`id_centro`) USING BTREE
) ENGINE=InnoDB;

alter table centros add primary key (id_centro);

alter table centros add foreign key (id_usuario) references usuarios(id_usuario);

alter table alumnos add foreign key (id_centro_destino) references centros(id_centro);
alter table alumnos add foreign key (id_centro_destino1) references centros(id_centro);
alter table alumnos add foreign key (id_centro_destino2) references centros(id_centro);
alter table alumnos add foreign key (id_centro_destino3) references centros(id_centro);
alter table alumnos add foreign key (id_centro_destino4) references centros(id_centro);
alter table alumnos add foreign key (id_centro_destino5) references centros(id_centro);
alter table alumnos add foreign key (id_centro_destino6) references centros(id_centro);


CREATE TABLE `hermanos` (
	`id_registro` INT NOT NULL AUTO_INCREMENT,
	`id_alumno` INT NOT NULL,
	`datos` VARCHAR(100) NOT NULL,
	`fnacimiento` DATE NOT NULL,
	`curso_solicitado` VARCHAR(10) NOT NULL,
	`nivel_eduactivo` ENUM ('ESO','BACHILLER') NOT NULL,
	`tipo` ENUM ('admision','baremo') NOT NULL,
	UNIQUE KEY `cp_hermanos` (`id_registro`) USING BTREE
) ENGINE=InnoDB;


alter table hermanos add primary key (id_registro);

alter table hermanos add foreign key (id_alumno) references alumnos(id_alumno);


CREATE TABLE `renta` (
	`id_renta` INT NOT NULL AUTO_INCREMENT,
	`id_alumno` INT NOT NULL,
	`datos` VARCHAR(100) NOT NULL,
	`parentesco` ENUM('padre','madre') NOT NULL,
	`dni` VARCHAR(9) NOT NULL,
	UNIQUE KEY `cp_renta` (`id_renta`) USING BTREE,
UNIQUE KEY `un_renta` (`datos`,`parentesco`,`dni`) USING BTREE
) ENGINE=InnoDB;

CREATE TABLE `matricula` (
	`id_alumno` INT NOT NULL auto_increment,
	`curso` VARCHAR(50)   NOT NULL,
	`provincia` VARCHAR(50)  NOT NULL,
	`ensenanza` VARCHAR(100) NOT NULL,
	`nombre` VARCHAR(100) NOT NULL ,
	`apellidos` VARCHAR(100) NOT NULL,
	`fnac` DATE  NOT NULL,
	`tipo_centro` VARCHAR(100) NOT NULL,
	`nombre_centro` VARCHAR(100) NOT NULL,
	`tipo_alumno_futuro` ENUM('tva','ebo','out') NOT NULL,
	UNIQUE KEY `cp_matricula` (`id_alumno`) USING BTREE
) ENGINE=InnoDB;
alter table matricula add primary key (id_alumno);


DROP DATABASE IF EXISTS HGIR_PRUEBAS;

CREATE DATABASE IF NOT EXISTS HGIR_PRUEBAS;


USE HGIR_PRUEBAS;

CREATE TABLE `alumnos` (
	`id_alumno` INT NOT NULL,
	`id_usuario` INT NOT NULL,
	`apellido1` VARCHAR(500)   NOT NULL,
	`apellido2` VARCHAR(500)  NOT NULL,
	`nombre` VARCHAR(500)  NOT NULL,
	`dni` VARCHAR(500) NOT NULL,
	`nacionalidad` VARCHAR(500) NOT NULL DEFAULT 'española',
	`datos_tutor` VARCHAR(500) NOT NULL,
	`dni_tutor` VARCHAR(500)  NOT NULL,
	`calle_dfamiliar` VARCHAR(500) NOT NULL,
	`num_dfamiliar` INT NOT NULL,
	`piso_dfamiliar` VARCHAR(500) NOT NULL,
	`loc_dfamiliar` VARCHAR(500) NOT NULL,
	`cp_dfamiliar` INT NOT NULL,
	`tel_dfamiliar1` INT(9),
	`tel_dfamiliar2` INT(9),
	`id_centro_estudios_origen` INT,
	`modalidad_origen` VARCHAR(10),
	`modalidad_especial` VARCHAR(10),
	`id_centro_destino` INT NOT NULL,
	`modalidad_destino` INT,
	`id_centro_destino1` INT,
	`id_centro_destino2` INT,
	`id_centro_destino3` INT,
	`id_centro_destino4` INT,
	`id_centro_destino5` INT,
	`id_centro_destino6` INT,
	`fotocopia_resolucion` BOOLEAN NOT NULL DEFAULT '0' COMMENT 'Fotocopia compulsada de la Resolución del Director del Servicio Provincial de Educación en la Modalidad de Educación Especial o Combinada.',
	`reserva_plaza` BOOLEAN NOT NULL DEFAULT '0' COMMENT 'Anexo de reserva de plaza en el Centro de origen de Educación Especial.',
	`prematuridad` BOOLEAN NOT NULL DEFAULT '0' COMMENT 'Resolución del Director del Servicio Provincial de Educación, Cultura y Deporte correspondiente, en caso de prematuridad. (Sólo para el acceso por primera vez a segundo ciclo de Educación Infantil)',
	`acred_dom_familiar` BOOLEAN NOT NULL DEFAULT '0',
	`cer_rec_discapacidad` BOOLEAN NOT NULL DEFAULT '0',
	`fam_numerosa` BOOLEAN NOT NULL DEFAULT '0',
	`acred_dom_laboral` BOOLEAN NOT NULL DEFAULT '0',
	`permiten_renta` BOOLEAN NOT NULL DEFAULT '0',
	`cumplen_resta` BOOLEAN NOT NULL DEFAULT '0',
	UNIQUE KEY `cp_alumnos` (`id_alumno`) USING BTREE,
KEY `un_dni` (`dni`) USING BTREE
) ENGINE=InnoDB;

alter table alumnos add primary key (id_alumno);

CREATE TABLE `usuarios` (
	`id_usuario` INT NOT NULL AUTO_INCREMENT,
	`clave` VARCHAR(500)   NOT NULL,
	`nombre_usuario` VARCHAR(500)   NOT NULL,
	`rol` ENUM ('alumno','centro','sprovincial','admin') NOT NULL,
	UNIQUE KEY `cp_usuarios` (`id_usuario`) USING BTREE,
UNIQUE KEY `un_usuario` (`nombre_usuario`) USING BTREE
) ENGINE=InnoDB;

alter table usuarios add primary key (id_usuario);


alter table alumnos add foreign key (id_usuario) references usuarios(id_usuario);

CREATE TABLE `centros` (
	`id_centro` INT NOT NULL AUTO_INCREMENT,
	`id_usuario` INT NOT NULL,
	`localidad` VARCHAR(20) NOT NULL,
	`provincia` VARCHAR(20) NOT NULL,
	UNIQUE KEY `cp_centros` (`id_centro`) USING BTREE
) ENGINE=InnoDB;

alter table centros add primary key (id_centro);

alter table centros add foreign key (id_usuario) references usuarios(id_usuario);

alter table alumnos add foreign key (id_centro_destino) references centros(id_centro);
alter table alumnos add foreign key (id_centro_destino1) references centros(id_centro);
alter table alumnos add foreign key (id_centro_destino2) references centros(id_centro);
alter table alumnos add foreign key (id_centro_destino3) references centros(id_centro);
alter table alumnos add foreign key (id_centro_destino4) references centros(id_centro);
alter table alumnos add foreign key (id_centro_destino5) references centros(id_centro);
alter table alumnos add foreign key (id_centro_destino6) references centros(id_centro);


CREATE TABLE `hermanos` (
	`id_registro` INT NOT NULL AUTO_INCREMENT,
	`id_alumno` INT NOT NULL,
	`datos` VARCHAR(100) NOT NULL,
	`fnacimiento` DATE NOT NULL,
	`curso_solicitado` VARCHAR(10) NOT NULL,
	`nivel_eduactivo` ENUM ('ESO','BACHILLER') NOT NULL,
	`tipo` ENUM ('admision','baremo') NOT NULL,
	UNIQUE KEY `cp_hermanos` (`id_registro`) USING BTREE
) ENGINE=InnoDB;


alter table hermanos add primary key (id_registro);


alter table hermanos add foreign key (id_alumno) references alumnos(id_alumno);


CREATE TABLE `renta` (
	`id_renta` INT NOT NULL AUTO_INCREMENT,
	`id_alumno` INT NOT NULL,
	`datos` VARCHAR(100) NOT NULL,
	`parentesco` ENUM('padre','madre') NOT NULL,
	`dni` VARCHAR(9) NOT NULL,
	UNIQUE KEY `cp_renta` (`id_renta`) USING BTREE,
UNIQUE KEY `un_renta` (`datos`,`parentesco`,`dni`) USING BTREE
) ENGINE=InnoDB;

CREATE TABLE `matricula` (
	`id_alumno` INT NOT NULL auto_increment,
	`curso` VARCHAR(50)   NOT NULL,
	`provincia` VARCHAR(50)  NOT NULL,
	`ensenanza` VARCHAR(100) NOT NULL,
	`nombre` VARCHAR(100) NOT NULL ,
	`apellidos` VARCHAR(100) NOT NULL,
	`fnac` DATE  NOT NULL,
	`tipo_centro` VARCHAR(100) NOT NULL,
	`nombre_centro` VARCHAR(100) NOT NULL,
	`tipo_alumno_futuro` ENUM('tva','ebo','out') NOT NULL,
	UNIQUE KEY `cp_matricula` (`id_alumno`) USING BTREE
) ENGINE=InnoDB;
alter table matricula add primary key (id_alumno);
