CREATE TABLE `cliente` (
	id INT auto_increment NOT NULL,
	nombre varchar(64) NOT NULL,
	apellido varchar(100) NOT NULL,
	direccion varchar(100) NULL,
	telefono varchar(13) NULL,
	correo varchar(64) NOT NULL,
	CONSTRAINT cliente_PK PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_general_ci;


CREATE TABLE `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idcliente` int(11) NOT NULL,
  `usuario` varchar(13) NOT NULL,
  `pass` varchar(100) NOT NULL,
  `estado` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_FK` (`idcliente`),
  CONSTRAINT `usuario_FK` FOREIGN KEY (`idcliente`) REFERENCES `cliente` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8



CREATE TABLE `menu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipo` int DEFAULT NULL,
  `nombre` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `estado` int DEFAULT NULL,
  `cantidadinicial` int DEFAULT NULL,
  `cantidadactual` int DEFAULT NULL,
  `comida` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb3


CREATE TABLE `cic.consumo` (
	id INT auto_increment NOT NULL,
	idcliente INT NULL,
	fecha DATETIME NOT NULL,
	valorsuma DECIMAL DEFAULT 0 NOT NULL,
	valoriva DECIMAL DEFAULT 0 NOT NULL,
	valordescuento DECIMAL DEFAULT 0 NOT NULL,
	valortotal DECIMAL DEFAULT 0 NOT NULL,
	CONSTRAINT consumo_PK PRIMARY KEY (id),
	CONSTRAINT consumo_FK FOREIGN KEY (idcliente) REFERENCES cic.cliente(id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;




CREATE TABLE `detalleconsumo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idmenu` int(11) NOT NULL,
  `idconsumo` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `valor` decimal(10,0) NOT NULL DEFAULT 0,  
  PRIMARY KEY (`id`),
  KEY `detalleconsumo_FK` (`idmenu`),
  KEY `detalleconsumo_FK_1` (`idconsumo`),
  CONSTRAINT `detalleconsumo_FK` FOREIGN KEY (`idmenu`) REFERENCES `menu` (`id`),
  CONSTRAINT `detalleconsumo_FK_1` FOREIGN KEY (`idconsumo`) REFERENCES `consumo` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4





CREATE TABLE `movimiento` (
	id INT auto_increment NOT NULL,
	idcliente INT NOT NULL,
	accion INT NOT NULL,
	fecha DATETIME NOT NULL,
	valor DECIMAL DEFAULT 0 NOT NULL,
	saldo DECIMAL DEFAULT 0 NOT NULL,
	CONSTRAINT movimiento_PK PRIMARY KEY (id),
	CONSTRAINT movimiento_FK FOREIGN KEY (idcliente) REFERENCES cic.cliente(id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_general_ci;



INSERT INTO cic.cliente (nombre,apellido,direccion,telefono,correo)
	VALUES ('Jose Francisco','Lopez Morocho','VA de la prensa y puruha','0979636583','vic_vcp@gmail.com');
INSERT INTO cic.cliente (nombre,apellido,direccion,telefono,correo)
	VALUES ('Melany','Herrera','10de agosto y morona','09149636582','mela@gmail.com');


INSERT INTO cic.usuario (idcliente,usuario,pass,estado)
	VALUES (1,'jose','123456',1);
INSERT INTO cic.usuario (idcliente,usuario,pass,estado)
	VALUES (2,'mela','123456',1);


INSERT INTO cic.menu (tipo,nombre,fecha,estado,cantidadinicial,cantidadactual,comida)
	VALUES (1,'Sopa de alverjas','2021-07-01',1,50,50,2);
INSERT INTO cic.menu (tipo,nombre,fecha,estado,cantidadinicial,cantidadactual,comida)
	VALUES (1,'Arroz con tallarin','2021-07-01',1,50,50,2);
INSERT INTO cic.menu (tipo,nombre,fecha,estado,cantidadinicial,cantidadactual,comida)
	VALUES (1,'Pollo al horno','2021-07-01',1,50,50,2);
INSERT INTO cic.menu (tipo,nombre,fecha,estado,cantidadinicial,cantidadactual,comida)
	VALUES (1,'Sopa de fideo','2021-07-01',1,50,50,2);
INSERT INTO cic.menu (tipo,nombre,fecha,estado,cantidadinicial,cantidadactual,comida)
	VALUES (1,'Avena','2021-07-01',1,50,50,2);
INSERT INTO cic.menu (tipo,nombre,fecha,estado,cantidadinicial,cantidadactual,comida)
	VALUES (1,'Locro de sambo','2021-07-01',1,50,50,3);
INSERT INTO cic.menu (tipo,nombre,fecha,estado,cantidadinicial,cantidadactual,comida)
	VALUES (1,'Sopa de Haba','2021-07-01',1,50,50,3);
INSERT INTO cic.menu (tipo,nombre,fecha,estado,cantidadinicial,cantidadactual,comida)
	VALUES (1,'Arroz relleno','2021-07-01',1,50,50,3);
INSERT INTO cic.menu (tipo,nombre,fecha,estado,cantidadinicial,cantidadactual,comida)
	VALUES (1,'Pollo broster','2021-07-01',1,50,50,3);
INSERT INTO cic.menu (tipo,nombre,fecha,estado,cantidadinicial,cantidadactual,comida)
	VALUES (1,'Agua aromatica','2021-07-01',1,50,50,3);
INSERT INTO cic.menu (tipo,nombre,fecha,estado,cantidadinicial,cantidadactual,comida)
	VALUES (1,'Jugo de tomate','2021-07-01',1,50,50,3);


UPDATE cic.menu
	SET fecha='2021-07-03'
