-- ============================================================
-- FerreSystem — Script de creación de base de datos
-- J&S Ferretería — Quiparacra, Huachón, Pasco
-- Ejecutar en: phpMyAdmin > ferresystem > Importar
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- TABLA: usuarios
-- Solo un administrador (Edelson). Sin registro público.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,  -- Siempre con password_hash()
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLA: categorias
-- Tipos de productos de la ferretería
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categorias (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255),
    activo      TINYINT(1) DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLA: productos
-- Catálogo completo del inventario
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS productos (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(150) NOT NULL,
    categoria_id    INT NOT NULL,
    descripcion     TEXT,
    precio_compra   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    precio_venta    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock_actual    INT NOT NULL DEFAULT 0,
    stock_minimo    INT NOT NULL DEFAULT 5,
    foto            VARCHAR(255) DEFAULT NULL,
    activo          TINYINT(1) DEFAULT 1,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLA: ventas
-- Cabecera de cada venta registrada en el panel
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS ventas (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    fecha       DATE NOT NULL,
    total       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    observacion TEXT,
    anulada     TINYINT(1) DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLA: venta_detalle
-- Líneas de productos de cada venta
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS venta_detalle (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    venta_id        INT NOT NULL,
    producto_id     INT NOT NULL,
    cantidad        INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal        DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venta_id)    REFERENCES ventas(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLA: caja
-- Movimientos de ingresos y egresos
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS caja (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    fecha       DATE NOT NULL,
    tipo        ENUM('ingreso','egreso') NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    monto       DECIMAL(10,2) NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLA: pedidos
-- Pedidos enviados desde el sitio público
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pedidos (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    cliente_nombre      VARCHAR(150) NOT NULL,
    cliente_telefono    VARCHAR(20)  NOT NULL,
    cliente_direccion   VARCHAR(255) NOT NULL,
    estado              ENUM('pendiente','en_proceso','entregado') DEFAULT 'pendiente',
    total               DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    observacion         TEXT,
    fecha               TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLA: pedido_detalle
-- Productos de cada pedido del cliente
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pedido_detalle (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id       INT NOT NULL,
    producto_id     INT NOT NULL,
    cantidad        INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal        DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id)   REFERENCES pedidos(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLA: cotizaciones
-- Cotizaciones de obra generadas por el maestro albañil
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS cotizaciones (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    cliente_nombre      VARCHAR(150) NOT NULL,
    cliente_telefono    VARCHAR(20),
    descripcion_obra    TEXT,
    mano_obra           DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_materiales    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_general       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    fecha               DATE NOT NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLA: cotizacion_materiales
-- Líneas de materiales de cada cotización de obra
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS cotizacion_materiales (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    cotizacion_id   INT NOT NULL,
    descripcion     VARCHAR(200) NOT NULL,
    cantidad        DECIMAL(10,2) NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal        DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLA: stock_movimientos
-- Historial de entradas y salidas de stock
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS stock_movimientos (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    cantidad    INT NOT NULL,
    tipo        ENUM('entrada','salida') NOT NULL,
    observacion VARCHAR(255),
    fecha       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DATOS INICIALES
-- ============================================================

-- Usuario administrador por defecto
-- Contraseña: ferresystem2025 (hasheada con password_hash)
INSERT INTO usuarios (nombre, email, password) VALUES
('Edelson Orihuela', 'eoj.secu@gmail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uFpxRHDGy');

-- Categorías iniciales de J&S Ferretería
INSERT INTO categorias (nombre, descripcion) VALUES
('Herramientas',      'Martillos, destornilladores, llaves, taladros y más'),
('Construcción',      'Cemento, fierro, ladrillos, arena y materiales de obra'),
('Plomería',          'Tuberías, codos, llaves de paso, pegamentos PVC'),
('Electricidad',      'Cables, interruptores, tomacorrientes, focos'),
('Pintura',           'Pinturas, barnices, thinner, brochas y rodillos'),
('Seguridad',         'Candados, chapas, bisagras, aldabas'),
('Fijación',          'Clavos, tornillos, pernos, tuercas, remaches'),
('Acabados',          'Porcelana, fragua, selladores, masilla'),
('Gasfitería',        'Inodoros, lavatorios, llaves de ducha'),
('Otros',             'Artículos varios de ferretería');

SET FOREIGN_KEY_CHECKS = 1;