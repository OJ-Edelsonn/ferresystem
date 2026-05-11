# FerreSystem 🔨

Plataforma web completa para **J&S Ferretería** — Quiparacra, Huachón, Pasco, Perú.

## 🌐 Demo en vivo
[jsferreteria.infinityfreeapp.com](http://jsferreteria.infinityfreeapp.com/public/index.php)

## 📋 Descripción
Sistema web full-stack desarrollado para digitalizar completamente las operaciones 
de una ferretería familiar. Incluye sitio web público orientado a clientes locales 
y panel de administración interno para gestión del negocio.

## ✨ Funcionalidades

### Sitio público (mobile-first)
- Catálogo de productos por categoría con buscador
- Cotizador de materiales con formulario de pedido
- Sección de servicios de obra (maestro albañil)
- Contacto con integración a WhatsApp
- Diseño responsive optimizado para celular

### Panel de administración
- Dashboard con KPIs y gráficos en tiempo real (Chart.js)
- Módulo de inventario con alertas de stock crítico
- Módulo de ventas con descuento automático de stock
- Módulo de caja (ingresos, egresos, saldo diario)
- Módulo de pedidos recibidos desde el sitio público
- Módulo de cotizaciones de obra con cálculo automático

## 🛠️ Stack tecnológico
- **Frontend:** HTML5, CSS3, JavaScript (vanilla), Bootstrap 5, Chart.js
- **Backend:** PHP 8
- **Base de datos:** MySQL (PDO)
- **Arquitectura:** MVC simplificado
- **Hosting:** InfinityFree
- **Control de versiones:** Git + GitHub

## 🚀 Instalación local
1. Clona el repositorio: `git clone https://github.com/OJ-Edelsonn/ferresystem.git`
2. Copia la carpeta a `C:\xampp\htdocs\`
3. Inicia Apache y MySQL en XAMPP
4. Crea la BD `ferresystem` en phpMyAdmin
5. Importa `database/ferresystem.sql`
6. Configura `config/database.php` con tus credenciales locales
7. Accede a `http://localhost/ferresystem/public/index.php`

## 👤 Desarrollador
**Edelson Anghuelo Orihuela Jara**  
Ingeniería Empresarial y Sistemas — Universidad Científica del Sur  
[LinkedIn](https://www.linkedin.com/in/edelson-anghuelo-orihuela-jara-07b299329)