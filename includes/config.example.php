<?php
// ─── Copy this file to config.php and fill in your values ───

define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // your MySQL username
define('DB_PASS', '');            // your MySQL password
define('DB_NAME', 'travelnest');
define('SITE_NAME', 'TravelNest');
define('TAX_RATE', 0.12);
define('PER_PAGE', 9);

if (session_status() === PHP_SESSION_NONE) session_start();
date_default_timezone_set('Asia/Kolkata');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// ─── Email / SMTP Settings ───
// For Gmail: use an App Password (not your main password)
// https://myaccount.google.com/apppasswords
define('SMTP_HOST',     'smtp.gmail.com');
define('SMTP_USER',     'your_email@gmail.com');
define('SMTP_PASS',     'your_app_password_here');
define('SMTP_PORT',     587);
define('SMTP_SECURE',   'tls');
define('MAIL_FROM',     'your_email@gmail.com');
define('MAIL_FROM_NAME','TravelNest');
