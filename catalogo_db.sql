-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: catalogo_db
-- ------------------------------------------------------
-- Server version	8.0.41

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `rubro` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,'Taller Electrónica Sur','Av. Córdoba 1050','1145678901','Reparación Técnicas'),(2,'Estudio Diseño Gráfico','Calle 50 Nro 500','2215551234','Servicios Creativos'),(3,'Mayorista Componentes XYZ','Ruta 9 Km 20','3413000500','Distribuidor Mayorista'),(4,'Escuela Técnica N°1','Av. San Martín 800','1133332222','Educación'),(5,'Particular Laura Gómez','Domicilio Particular 1','9999999999','Particular'),(6,'Particular Roberto Díaz','Domicilio Particular 2','9999999998','Particular'),(7,'Tienda de Gamers Pro','Santa Fe 456','3511112222','Minorista'),(8,'Particular Santiago García','Domicilio Particular 3','9999999997','Particular'),(9,'Clara','Local 15, Clinica medica','11259292','Clinica');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compras`
--

DROP TABLE IF EXISTS `compras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compras` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_proveedor` int DEFAULT NULL,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(10,2) DEFAULT NULL,
  `iva` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_proveedor` (`id_proveedor`),
  CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compras`
--

LOCK TABLES `compras` WRITE;
/*!40000 ALTER TABLE `compras` DISABLE KEYS */;
INSERT INTO `compras` VALUES (1,1,'2025-09-10 09:00:00',30000.00,6300.00),(2,2,'2025-09-15 11:30:00',45000.00,9450.00),(3,3,'2025-09-20 15:45:00',60000.00,12600.00),(4,1,'2025-09-25 08:20:00',15000.00,3150.00),(5,2,'2025-10-01 10:30:00',20000.00,4200.00),(6,3,'2025-10-05 14:00:00',35000.00,7350.00),(7,1,'2025-10-10 17:15:00',12000.00,2520.00),(8,2,'2025-10-12 09:40:00',18000.00,3780.00),(9,3,'2025-10-14 13:00:00',8000.00,1680.00),(10,1,'2025-10-15 16:50:00',2500.00,525.00),(11,4,'2025-10-17 15:59:27',484000.00,84000.00),(12,4,'2025-10-18 15:59:27',205700.00,35700.00);
/*!40000 ALTER TABLE `compras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_compras`
--

DROP TABLE IF EXISTS `detalle_compras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_compras` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_compra` int DEFAULT NULL,
  `id_producto` int DEFAULT NULL,
  `cantidad` int DEFAULT NULL,
  `precio_unitario` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_compra` (`id_compra`),
  KEY `id_producto` (`id_producto`),
  CONSTRAINT `detalle_compras_ibfk_1` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id`) ON DELETE CASCADE,
  CONSTRAINT `detalle_compras_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_compras`
--

LOCK TABLES `detalle_compras` WRITE;
/*!40000 ALTER TABLE `detalle_compras` DISABLE KEYS */;
INSERT INTO `detalle_compras` VALUES (1,1,101,500,50.00),(2,1,102,20,800.00),(3,2,201,50,1200.00),(4,2,202,100,750.00),(5,3,301,5,8000.00),(6,3,302,50,1500.00),(7,4,103,30,300.00),(8,4,104,20,400.00),(9,5,203,3,4500.00),(10,5,204,5,3000.00),(11,6,303,10,3500.00),(12,6,301,2,8000.00),(13,7,101,100,50.00),(14,7,102,5,800.00),(15,8,201,10,1200.00),(16,8,203,2,4500.00),(17,9,302,5,1500.00),(18,10,104,5,400.00),(19,11,401,10,30000.00),(20,11,402,50,2000.00),(21,12,403,100,500.00),(22,12,404,20,6000.00);
/*!40000 ALTER TABLE `detalle_compras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_pedido`
--

DROP TABLE IF EXISTS `detalle_pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_pedido` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pedido_id` int NOT NULL,
  `producto_id` int NOT NULL,
  `nombre_producto` varchar(100) NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pedido_id` (`pedido_id`),
  CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_pedido`
--

LOCK TABLES `detalle_pedido` WRITE;
/*!40000 ALTER TABLE `detalle_pedido` DISABLE KEYS */;
INSERT INTO `detalle_pedido` VALUES (13,1,101,'Resistencia 1k Ohm (Pack x100)',4,150.00),(14,1,104,'Protoboard 830 puntos',4,950.00),(15,2,104,'Protoboard 830 puntos',1,950.00),(16,2,103,'Sensor de Temperatura DS18B20',1,650.00),(17,3,406,'Tazas Capibara',2,8500.00),(18,3,405,'Tira led 20mts',1,14500.00),(19,3,102,'Microcontrolador ESP32',6,1800.00),(20,4,406,'Vaso Termico Capibara',10,13500.00);
/*!40000 ALTER TABLE `detalle_pedido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_ventas`
--

DROP TABLE IF EXISTS `detalle_ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_ventas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_venta` int DEFAULT NULL,
  `id_producto` int DEFAULT NULL,
  `cantidad` int DEFAULT NULL,
  `precio_unitario` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_venta` (`id_venta`),
  KEY `id_producto` (`id_producto`),
  CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `detalle_ventas_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_ventas`
--

LOCK TABLES `detalle_ventas` WRITE;
/*!40000 ALTER TABLE `detalle_ventas` DISABLE KEYS */;
INSERT INTO `detalle_ventas` VALUES (100,10,102,1,1800.00),(101,10,103,5,650.00),(102,11,203,2,8999.00),(103,11,201,3,2500.00),(104,12,101,100,150.00),(105,13,104,5,950.00),(106,13,102,2,1800.00),(107,14,303,1,6999.00),(108,15,301,1,14999.00),(109,16,204,1,5999.00),(110,16,203,1,8999.00),(111,17,103,2,650.00),(112,17,101,5,150.00),(113,18,202,3,1599.00),(114,19,302,2,3200.00),(115,19,101,10,150.00),(116,20,201,1,2500.00),(117,21,401,2,45000.00),(118,21,402,5,3500.00),(119,22,404,1,9500.00),(120,22,403,10,1200.00),(121,23,402,2,3500.00),(122,23,101,5,150.00),(124,25,101,15,150.00),(125,26,101,193,150.00),(127,24,201,1,2500.00),(128,26,404,1,9500.00),(129,26,102,1,1800.00),(130,32,202,1,1599.00),(131,27,303,2,6999.00),(132,28,401,1,45000.00),(133,29,403,3,1200.00),(134,30,203,1,8999.00),(135,30,202,1,1500.00),(136,31,101,2,150.00),(137,32,202,1,1599.00),(138,33,407,5,30000.00),(139,34,406,10,13500.00),(140,35,406,2,8500.00),(141,35,405,1,14500.00),(142,35,102,6,1800.00);
/*!40000 ALTER TABLE `detalle_ventas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movimientos_stock`
--

DROP TABLE IF EXISTS `movimientos_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `movimientos_stock` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_producto` int DEFAULT NULL,
  `tipo` enum('Entrada','Salida') DEFAULT NULL,
  `cantidad` int DEFAULT NULL,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_producto` (`id_producto`),
  CONSTRAINT `movimientos_stock_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movimientos_stock`
--

LOCK TABLES `movimientos_stock` WRITE;
/*!40000 ALTER TABLE `movimientos_stock` DISABLE KEYS */;
INSERT INTO `movimientos_stock` VALUES (1,101,'Entrada',500,'2025-09-10 09:00:00','Compra #1: Resistencia'),(2,102,'Entrada',20,'2025-09-10 09:00:00','Compra #1: ESP32'),(3,201,'Entrada',50,'2025-09-15 11:30:00','Compra #2: Mouse'),(4,202,'Entrada',100,'2025-09-15 11:30:00','Compra #2: HDMI'),(5,301,'Entrada',5,'2025-09-20 15:45:00','Compra #3: Smartwatch'),(6,302,'Entrada',50,'2025-09-20 15:45:00','Compra #3: Foco Smart'),(7,103,'Entrada',30,'2025-09-25 08:20:00','Compra #4: Sensor Temp'),(8,104,'Entrada',20,'2025-09-25 08:20:00','Compra #4: Protoboard'),(9,203,'Entrada',3,'2025-10-01 10:30:00','Compra #5: Teclado Mecánico'),(10,204,'Entrada',5,'2025-10-01 10:30:00','Compra #5: Webcam'),(11,303,'Entrada',10,'2025-10-05 14:00:00','Compra #6: Extensor WiFi'),(12,301,'Entrada',2,'2025-10-05 14:00:00','Compra #6: Smartwatch'),(13,101,'Entrada',100,'2025-10-10 17:15:00','Compra #7: Resistencia'),(14,102,'Entrada',5,'2025-10-10 17:15:00','Compra #7: ESP32'),(15,201,'Entrada',10,'2025-10-12 09:40:00','Compra #8: Mouse'),(16,203,'Entrada',2,'2025-10-12 09:40:00','Compra #8: Teclado Mecánico'),(17,302,'Entrada',5,'2025-10-14 13:00:00','Compra #9: Foco Smart'),(18,104,'Entrada',5,'2025-10-15 16:50:00','Compra #10: Protoboard'),(19,102,'Salida',1,'2025-10-02 11:00:00','Venta #10: ESP32'),(20,103,'Salida',5,'2025-10-02 11:00:00','Venta #10: Sensor Temp'),(21,203,'Salida',2,'2025-10-06 16:30:00','Venta #11: Teclado Mecánico'),(22,201,'Salida',3,'2025-10-06 16:30:00','Venta #11: Mouse'),(23,101,'Salida',100,'2025-10-07 09:15:00','Venta #12: Resistencia'),(24,104,'Salida',5,'2025-10-07 14:40:00','Venta #13: Protoboard'),(25,102,'Salida',2,'2025-10-07 14:40:00','Venta #13: ESP32'),(26,303,'Salida',1,'2025-10-08 17:00:00','Venta #14: Extensor WiFi'),(27,301,'Salida',1,'2025-10-09 10:20:00','Venta #15: Smartwatch'),(28,204,'Salida',1,'2025-10-09 15:55:00','Venta #16: Webcam'),(29,203,'Salida',1,'2025-10-09 15:55:00','Venta #16: Teclado Mecánico'),(30,103,'Salida',2,'2025-10-10 11:30:00','Venta #17: Sensor Temp'),(31,101,'Salida',5,'2025-10-10 11:30:00','Venta #17: Resistencia'),(32,202,'Salida',3,'2025-10-11 14:10:00','Venta #18: Cable HDMI'),(33,302,'Salida',2,'2025-10-12 09:30:00','Venta #19: Foco Smart'),(34,101,'Salida',10,'2025-10-12 09:30:00','Venta #19: Resistencia'),(35,201,'Salida',1,'2025-10-12 18:00:00','Venta #20: Mouse Inalámbrico'),(36,401,'Entrada',10,'2025-10-17 15:59:27','Compra #11: Peluche Capibara XL'),(37,402,'Entrada',50,'2025-10-17 15:59:27','Compra #11: Taza Capibara \"Mug\"'),(38,403,'Entrada',100,'2025-10-18 15:59:27','Compra #12: Llavero Capibara PVC'),(39,404,'Entrada',20,'2025-10-18 15:59:27','Compra #12: Agenda 2026 Capibara'),(40,401,'Salida',2,'2025-10-16 15:59:27','Venta #21: Peluche Capibara XL'),(41,402,'Salida',5,'2025-10-16 15:59:27','Venta #21: Taza Capibara \"Mug\"'),(42,404,'Salida',1,'2025-10-14 15:59:27','Venta #22: Agenda 2026 Capibara'),(43,403,'Salida',10,'2025-10-14 15:59:27','Venta #22: Llavero Capibara PVC'),(44,402,'Salida',2,'2025-10-11 15:59:27','Venta #23: Taza Capibara \"Mug\"'),(45,101,'Salida',5,'2025-10-11 15:59:27','Venta #23: Resistencia 1k Ohm');
/*!40000 ALTER TABLE `movimientos_stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedidos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `usuario_nombre` varchar(100) NOT NULL,
  `fecha_pedido` datetime NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(50) NOT NULL,
  `estado` varchar(50) DEFAULT 'Pendiente de Retiro',
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidos`
--

LOCK TABLES `pedidos` WRITE;
/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
INSERT INTO `pedidos` VALUES (1,6,'Ricardo','2025-12-12 23:55:15',4400.00,'Débito/Crédito','Rechazado'),(2,6,'Ricardo','2025-12-17 22:27:46',1600.00,'Débito/Crédito','Entregado'),(3,7,'Carlos','2025-12-17 22:46:28',42300.00,'Efectivo','Entregado'),(4,4,'Santiago','2025-12-18 01:17:22',135000.00,'Transferencia','Entregado');
/*!40000 ALTER TABLE `pedidos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `precio_costo` decimal(10,2) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `cantidad` int DEFAULT '0',
  `id_proveedor` int DEFAULT NULL,
  `id_usuario` int NOT NULL,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `imagen` varchar(255) DEFAULT NULL,
  `categoria` varchar(100) DEFAULT 'General',
  PRIMARY KEY (`id`),
  KEY `id_proveedor` (`id_proveedor`),
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=409 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (101,'Resistencia 1k Ohm (Pack x100)',50.00,150.00,2761,1,0,'2025-12-12 17:50:38','fotos_productos/resistencia1kOhm.png','Electronica y Robotica'),(102,'Microcontrolador ESP32',800.00,1800.00,494,1,0,'2025-12-12 17:50:38','fotos_productos/esp32.webp','Electronica y Robotica'),(103,'Sensor de Temperatura DS18B20',300.00,650.00,800,1,0,'2025-12-12 17:50:38','fotos_productos/SensorTemperatura.webp','Electronica y Robotica'),(104,'Protoboard 830 puntos',400.00,950.00,600,1,0,'2025-12-12 17:50:38','fotos_productos/protoboard.webp','Electronica y Robotica'),(201,'Mouse Óptico Inalámbrico',1200.00,2500.00,800,2,0,'2025-12-12 17:50:38','fotos_productos/mouse.jpg','Perifericos y Accesorios'),(202,'Cable HDMI 2.0 (2 metros)',750.00,1599.00,1200,2,0,'2025-12-12 17:50:38','fotos_productos/hdmi.png','Perifericos y Accesorios'),(203,'Teclado Mecánico RGB',4500.00,8999.00,300,2,0,'2025-12-12 17:50:38','fotos_productos/tecladoMecanico.png','Perifericos y Accesorios'),(204,'Webcam HD 1080p',3000.00,5999.00,400,2,0,'2025-12-12 17:50:38','fotos_productos/webcam.png','Perifericos y Accesorios'),(301,'Reloj Inteligente Básico',8000.00,14999.00,150,3,0,'2025-12-12 17:50:38','fotos_productos/relojbasico.webp','Smart Home y Gadgets'),(302,'Foco LED Smart Wi-Fi',1500.00,3200.00,500,3,0,'2025-12-12 17:50:38','fotos_productos/foco.png','Smart Home y Gadgets'),(303,'Extensor WiFi Doble Banda',3500.00,6999.00,250,3,0,'2025-12-12 17:50:38','fotos_productos/wifi.jpg','Smart Home y Gadgets'),(401,'Peluche Capibara XL',30000.00,45000.00,8,4,0,'2025-12-12 17:50:38','fotos_productos/peluche.avif','Merchandising Capibara'),(402,'Taza Capibara \"Mug\"',2000.00,3500.00,43,4,0,'2025-12-12 17:50:38','fotos_productos/tazaCapibara.png','Merchandising Capibara'),(403,'Llavero Capibara PVC',500.00,1200.00,90,4,0,'2025-12-12 17:50:38','fotos_productos/llavero.webp','Merchandising Capibara'),(404,'Agenda 2026 Capibara',6000.00,9500.00,19,4,0,'2025-12-12 17:50:38','fotos_productos/agenda.webp','Merchandising Capibara'),(405,'Tira led 20mts',12000.00,14500.00,24,3,4,'2025-12-12 18:08:27','fotos_productos/tiraLed.webp','Merchandising Capibara'),(406,'Vaso Termico Capibara',10500.00,13500.00,128,5,4,'2025-12-17 21:39:59','fotos_productos/vasoTermico.png','Merchandising Capibara'),(407,'Mouse oficina',15000.00,30000.00,45,2,4,'2025-12-17 21:40:38','fotos_productos/mouseViejo.png','Perifericos y Accesorios'),(408,'Mouse RGB',15000.00,30000.00,40,2,4,'2025-12-17 23:57:10','fotos_productos/mouseRGB.webp','Perifericos y Accesorios');
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proveedores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `rubro` varchar(50) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedores`
--

LOCK TABLES `proveedores` WRITE;
/*!40000 ALTER TABLE `proveedores` DISABLE KEYS */;
INSERT INTO `proveedores` VALUES (1,'Tech Supplies Inc.','Componentes Electrónicos','Ruta 3 km 50','1123456789'),(2,'Accesorios Global SRL','Periféricos y Cables','Bv. San Juan 100','3514000123'),(3,'Gadgets Innovations','Dispositivos IoT y Wearables','Av. Belgrano 900','1188887777'),(4,'Capibara Merch S.A.','Merchandising/Souvenirs','Ruta 40 Km 10','1133334444'),(5,'Peltier&Co','Cafeteria','Peltier 50','11 6842 0923');
/*!40000 ALTER TABLE `proveedores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `admin` tinyint(1) DEFAULT '0',
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mail` (`mail`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Mauricio','mauricio_arias@live.com.ar','$2y$10$bX2PHgfV2zHnN7ZZSNlFNODrs/2gBZe20ZPA7eyZt9SMExIIQ541m',1,'2025-10-15 21:58:29'),(2,'Juana Perez','Jperez@gmail.com','$2y$10$tM.yF6m71n3gV0J8S.fJLO6w5w6eFfA9hE5.2W5l4H0fL5e9m2fL.',0,'2025-10-15 22:20:36'),(3,'Lucas Lopez','Lucas@hotmail.com','$2y$10$tM.yF6m71n3gV0J8S.fJLO6w5w6eFfA9hE5.2W5l4H0fL5e9m2fL.',0,'2025-10-15 22:20:36'),(4,'Santiago','comessantiago@gmail.com','$2y$10$RrmUL9SNi6d4d1la00QjJek5/PPQJfUjSuQUIPf54s.mOOArktFmW',1,'2025-10-15 22:09:59'),(5,'German','GGiorgis@gmail.com','$2y$10$hryRSUBkHTz3XRc.I0tr6uwlHqkxGvd3NlFD6UrXJIKWyK9vlkDuu',0,'2025-12-12 19:39:39'),(6,'Ricardo','Rick@outlook.com','$2y$10$B2ATlP.s1VA22h95L8YkWOsPh43xekIk62fc14.m9YWdoQYPD.SkO',0,'2025-12-12 19:43:43'),(7,'Carlos','carlito@gmail.com','$2y$10$QXotWh/gBW5V9vRlMb5jdOK0G8I/qroJ8ZdrsPSbarDdXAl23OJx6',0,'2025-12-17 21:44:46');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ventas`
--

DROP TABLE IF EXISTS `ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ventas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int DEFAULT NULL,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(10,2) DEFAULT NULL,
  `iva` decimal(10,2) DEFAULT NULL,
  `metodo_pago` enum('Efectivo','Débito','Crédito','Transferencia') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`),
  CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas`
--

LOCK TABLES `ventas` WRITE;
/*!40000 ALTER TABLE `ventas` DISABLE KEYS */;
INSERT INTO `ventas` VALUES (10,1,'2025-10-02 11:00:00',5000.00,1050.00,'Débito'),(11,2,'2025-10-06 16:30:00',25000.00,5250.00,'Efectivo'),(12,3,'2025-10-07 09:15:00',15000.00,3150.00,'Transferencia'),(13,4,'2025-10-07 14:40:00',9000.00,1890.00,'Crédito'),(14,5,'2025-10-08 17:00:00',6000.00,1260.00,'Efectivo'),(15,6,'2025-10-09 10:20:00',16000.00,3360.00,'Débito'),(16,7,'2025-10-09 15:55:00',9000.00,1890.00,'Transferencia'),(17,1,'2025-10-10 11:30:00',2000.00,420.00,'Efectivo'),(18,2,'2025-10-11 14:10:00',5000.00,1050.00,'Débito'),(19,3,'2025-10-12 09:30:00',8000.00,1680.00,'Transferencia'),(20,5,'2025-10-12 18:00:00',3500.00,735.00,'Efectivo'),(21,7,'2025-10-16 15:59:27',130075.00,22575.00,'Crédito'),(22,5,'2025-10-14 15:59:27',26015.00,4515.00,'Transferencia'),(23,8,'2025-10-11 15:59:27',9377.50,1627.50,'Efectivo'),(24,2,'2025-10-15 10:30:00',2500.00,525.00,'Efectivo'),(25,NULL,'2025-12-12 16:13:01',2250.00,472.50,'Transferencia'),(26,9,'2025-12-12 16:17:46',28950.00,6079.50,'Transferencia'),(27,NULL,'2025-12-01 18:45:00',13998.00,2939.58,'Débito'),(28,8,'2025-10-20 11:00:00',45000.00,9450.00,'Efectivo'),(29,NULL,'2025-11-25 16:30:00',3600.00,756.00,'Transferencia'),(30,3,'2025-12-10 12:00:00',10499.00,2204.79,'Débito'),(31,NULL,'2025-09-28 20:10:00',300.00,63.00,'Efectivo'),(32,NULL,'2025-09-10 14:20:00',1749.00,367.29,'Crédito'),(33,4,'2025-12-17 18:41:34',150000.00,31500.00,'Crédito'),(34,NULL,'2025-12-17 21:20:22',135000.00,28350.00,'Transferencia'),(35,NULL,'2025-12-17 21:20:30',42300.00,8883.00,'Efectivo');
/*!40000 ALTER TABLE `ventas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'catalogo_db'
--

--
-- Dumping routines for database 'catalogo_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-17 23:22:32
