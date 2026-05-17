<?php
// Single bootstrap — include this at top of every page
$root = dirname(__DIR__); // /path/to/tn
define('ROOT', $root);

// Detect base URL automatically
$script  = str_replace('\\','/',$_SERVER['SCRIPT_NAME']);
$dir     = rtrim(dirname($script),'/');
// If we're inside /admin/, go one level up
if(str_ends_with($dir,'/admin'))$dir=dirname($dir);
define('BASE', $dir); // e.g. /travelnest  or  /tn  or ''

require_once ROOT.'/includes/config.php';
require_once ROOT.'/includes/db.php';
require_once ROOT.'/includes/functions.php';
require_once ROOT.'/includes/mailer.php';

// Auto-convert dd/mm/yyyy to YYYY-MM-DD for SQL compatibility seamlessly
$dfmt = function(&$v){ if(is_string($v) && preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', trim($v), $m)) $v = $m[3].'-'.$m[2].'-'.$m[1]; };
array_walk_recursive($_GET, $dfmt); array_walk_recursive($_POST, $dfmt);
