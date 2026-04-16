<?php
declare(strict_types=1);

require_once __DIR__ . '/../components/conf/conf.php';
Auth::requireAdmin();

$db = db();

try {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$nombre = trim((string) ($_POST['nombre'] ?? ''));
		$email = trim((string) ($_POST['email'] ?? ''));
		$password = (string) ($_POST['password'] ?? '');
		$rol = trim((string) ($_POST['rol'] ?? 'empleado'));

		if ($nombre === '' || $email === '' || $password === '') {
			throw new InvalidArgumentException('Todos los campos son obligatorios.');
		}

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			throw new InvalidArgumentException('Email invalido.');
		}

		if (strlen($password) < 6) {
			throw new InvalidArgumentException('La contrasena debe tener al menos 6 caracteres.');
		}

		if (!in_array($rol, ['empleado', 'administrador'], true)) {
			throw new InvalidArgumentException('Rol invalido.');
		}

		$hash = password_hash($password, PASSWORD_DEFAULT);

		$stmt = $db->prepare('INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, :rol)');
		$stmt->execute([
			'nombre' => $nombre,
			'email' => $email,
			'password' => $hash,
			'rol' => $rol,
		]);

		set_flash('success', 'Usuario creado correctamente.');
		redirect('admin/usuarios.php');
	}
} catch (Throwable $e) {
	set_flash('error', $e->getMessage());
	redirect('admin/usuarios.php');
}

$stmt = $db->query('SELECT id, nombre, email, rol, creado_en FROM usuarios ORDER BY id DESC');
$usuarios = $stmt->fetchAll();

require_once __DIR__ . '/../components/header.php';
?>

<section class="panel">
	<h2>Alta de usuario</h2>
	<form method="post" class="form-grid">
		<label for="nombre">Nombre</label>
		<input id="nombre" name="nombre" type="text" required>

		<label for="email">Email</label>
		<input id="email" name="email" type="email" required>

		<label for="password">Contrasena</label>
		<input id="password" name="password" type="password" minlength="6" required>

		<label for="rol">Rol</label>
		<select id="rol" name="rol" required>
			<option value="empleado">Empleado</option>
			<option value="administrador">Administrador</option>
		</select>

		<button type="submit" class="btn">Crear usuario</button>
	</form>
</section>

<section class="panel">
	<h2>Listado de usuarios</h2>
	<div class="table-wrap">
		<table>
			<thead>
				<tr>
					<th>ID</th>
					<th>Nombre</th>
					<th>Email</th>
					<th>Rol</th>
					<th>Fecha alta</th>
				</tr>
			</thead>
			<tbody>
				<?php if (count($usuarios) === 0): ?>
					<tr>
						<td colspan="5">No hay usuarios registrados.</td>
					</tr>
				<?php endif; ?>
				<?php foreach ($usuarios as $item): ?>
					<tr>
						<td><?= (int) $item['id'] ?></td>
						<td><?= htmlspecialchars((string) $item['nombre']) ?></td>
						<td><?= htmlspecialchars((string) $item['email']) ?></td>
						<td><?= htmlspecialchars((string) $item['rol']) ?></td>
						<td><?= htmlspecialchars((string) $item['creado_en']) ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</section>

<?php require_once __DIR__ . '/../components/footer.php'; ?>
