<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

define('ROOT_PATH', dirname(__DIR__, 2));
define('APP_FOLDER', basename(ROOT_PATH));

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'agencia_autos');
define('DB_USER', 'root');
define('DB_PASS', '');

require_once ROOT_PATH . '/classes/autenticable.php';
require_once ROOT_PATH . '/classes/usuario.php';
require_once ROOT_PATH . '/classes/empleado.php';
require_once ROOT_PATH . '/classes/admin.php';
require_once ROOT_PATH . '/classes/auto.php';
require_once ROOT_PATH . '/classes/auth.php';

function db(): PDO
{
	static $pdo = null;

	if ($pdo instanceof PDO) {
		return $pdo;
	}

	$dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
	$pdo = new PDO($dsn, DB_USER, DB_PASS, [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	]);

	return $pdo;
}

function app_url(string $path = ''): string
{
	$base = '/' . APP_FOLDER;
	$path = ltrim($path, '/');
	return $path === '' ? $base : $base . '/' . $path;
}

function redirect(string $path): void
{
	header('Location: ' . app_url($path));
	exit;
}

function set_flash(string $type, string $message): void
{
	$_SESSION['flash'] = [
		'type' => $type,
		'message' => $message,
	];
}

function get_flash(): ?array
{
	if (!isset($_SESSION['flash'])) {
		return null;
	}

	$flash = $_SESSION['flash'];
	unset($_SESSION['flash']);

	return $flash;
}
