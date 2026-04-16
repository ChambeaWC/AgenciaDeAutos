<?php
declare(strict_types=1);

class Auth
{
	private const SESSION_USER = 'usuario';

	public static function login(string $email, string $password, PDO $db): bool
	{
		$stmt = $db->prepare('SELECT id, nombre, email, password, rol FROM usuarios WHERE email = :email LIMIT 1');
		$stmt->execute(['email' => trim($email)]);
		$row = $stmt->fetch();

		if (!$row) {
			return false;
		}

		$user = self::mapUser($row);
		if (!$user->verificarPassword($password)) {
			return false;
		}

		$_SESSION[self::SESSION_USER] = $user->toArray();
		session_regenerate_id(true);
		return true;
	}

	public static function logout(): void
	{
		unset($_SESSION[self::SESSION_USER]);
		session_regenerate_id(true);
	}

	public static function user(): ?array
	{
		return $_SESSION[self::SESSION_USER] ?? null;
	}

	public static function check(): bool
	{
		return isset($_SESSION[self::SESSION_USER]);
	}

	public static function isAdmin(): bool
	{
		return self::check() && (($_SESSION[self::SESSION_USER]['rol'] ?? '') === 'administrador');
	}

	public static function requireLogin(): void
	{
		if (!self::check()) {
			set_flash('error', 'Debes iniciar sesion para continuar.');
			redirect('log/login.php');
		}
	}

	public static function requireAdmin(): void
	{
		self::requireLogin();
		if (!self::isAdmin()) {
			set_flash('error', 'No tienes permisos para entrar aqui.');
			redirect('pages/inicio.php');
		}
	}

	private static function mapUser(array $row): Usuario
	{
		$id = (int) $row['id'];
		$nombre = (string) $row['nombre'];
		$email = (string) $row['email'];
		$password = (string) $row['password'];
		$rol = strtolower((string) $row['rol']);

		if ($rol === 'administrador') {
			return new Admin($id, $nombre, $email, $password, $rol);
		}

		return new Empleado($id, $nombre, $email, $password, $rol);
	}
}
