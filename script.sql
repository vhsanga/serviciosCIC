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
	id INT auto_increment NOT NULL,
	nombre varchar(64) NULL,
	descripcion varchar(100) NULL,
	calificacion INT NULL,
	fecha DATE NULL,
	estado INT NULL,
	CONSTRAINT menu_PK PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_general_ci;




CREATE TABLE `detalleconsumo` (
	id INT auto_increment NOT NULL,
	idmenu INT NOT NULL,
	cantidad INT NOT NULL,
	valor DECIMAL DEFAULT 0 NOT NULL,
	CONSTRAINT detalleconsumo_PK PRIMARY KEY (id),
	CONSTRAINT detalleconsumo_FK FOREIGN KEY (idmenu) REFERENCES cic.menu(id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_general_ci;



CREATE TABLE `detalleconsumo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idmenu` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `valor` decimal(10,0) NOT NULL DEFAULT 0,
  `idconsumo` int(11) NOT NULL,
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

