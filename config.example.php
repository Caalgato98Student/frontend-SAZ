<?php
/**
 * config.example.php — Plantilla de configuración.
 *
 * INSTRUCCIONES:
 *   1. Copiar este archivo como "config.php" en la misma ubicación.
 *   2. Rellenar los valores reales según tu entorno.
 *   3. NUNCA commitear config.php — ya está en .gitignore.
 *
 * DESARROLLO LOCAL (XAMPP):
 *   Los valores por defecto de abajo funcionan con XAMPP sin cambios.
 *   root sin contraseña es aceptable solo en local.
 *
 * PRODUCCIÓN (CPanel):
 *   Nunca usar root en producción. Crear un usuario dedicado con solo
 *   los permisos que necesita la app. Ejecutar esto una sola vez en
 *   phpMyAdmin como root antes de rellenar este archivo:
 *
 *   CREATE USER 'saz_app'@'localhost' IDENTIFIED BY 'contraseña_fuerte';
 *   GRANT SELECT, INSERT, UPDATE, DELETE ON saz_cms.* TO 'saz_app'@'localhost';
 *   FLUSH PRIVILEGES;
 *
 *   Luego usar 'saz_app' y su contraseña en DB_USER y DB_PASS.
 */

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'saz_cms');
define('DB_USER', 'root');       // En producción: usar el usuario dedicado, no root
define('DB_PASS', '');           // En producción: contraseña del usuario dedicado
define('DB_PORT', 3306);
