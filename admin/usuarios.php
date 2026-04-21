<?php
declare(strict_types=1);

require_once __DIR__ . '/../components/conf/conf.php';
Auth::requireAdmin();

$db = db();

$usuarioEditar = null;

if (isset($_GET['editar'])) {
    $id = (int) $_GET['editar'];

    $stmt = $db->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt->execute(['id' => $id]);

    $usuarioEditar = $stmt->fetch();
}

try {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$accion = (string) ($_POST['accion'] ?? 'guardar');

		if ($accion === 'eliminar') {
			$id = (int) ($_POST['id'] ?? 0);
			if ($id <= 0) {
				throw new InvalidArgumentException('ID invalido para eliminar usuario.');
			}

			$stmt = $db->prepare('DELETE FROM usuarios WHERE id = :id');
			$stmt->execute(['id' => $id]);

			set_flash('success', 'Usuario eliminado correctamente.');
			redirect('admin/usuarios.php');
		}

        $id = $_POST['id'] ?? null;
		$isEdicion = !empty($id);

		$nombre = trim((string) ($_POST['nombre'] ?? ''));
		$email = trim((string) ($_POST['email'] ?? ''));
		$password = (string) ($_POST['password'] ?? '');
		$rol = trim((string) ($_POST['rol'] ?? 'empleado'));

		if ($nombre === '' || $email === '') {
			throw new InvalidArgumentException('Nombre y email son obligatorios.');
		}

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			throw new InvalidArgumentException('Email invalido.');
		}

		if (!in_array($rol, ['empleado', 'administrador'], true)) {
			throw new InvalidArgumentException('Rol invalido.');
		}

		if ($password !== '' && strlen($password) < 6) {
			throw new InvalidArgumentException('La contraseña debe tener al menos 6 caracteres.');
		}

		if (!$isEdicion && $password === '') {
			throw new InvalidArgumentException('La contraseña es obligatoria.');
		}

		if ($isEdicion) {
			$params = [
				'id' => $id,
				'nombre' => $nombre,
				'email' => $email,
				'rol' => $rol,
			];

			if ($password !== '') {
				$params['password'] = password_hash($password, PASSWORD_DEFAULT);
				$sql = "
					UPDATE usuarios 
					SET nombre = :nombre, email = :email, password = :password, rol = :rol
					WHERE id = :id
				";
			} else {
				$sql = "
					UPDATE usuarios 
					SET nombre = :nombre, email = :email, rol = :rol
					WHERE id = :id
				";
			}

			$stmt = $db->prepare($sql);
			$stmt->execute($params);
			set_flash('success', 'Usuario actualizado.');
		} else {
			$stmt = $db->prepare('INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, :rol)');
			$stmt->execute([
				'nombre' => $nombre,
				'email' => $email,
				'password' => password_hash($password, PASSWORD_DEFAULT),
				'rol' => $rol,
			]);

			set_flash('success', 'Usuario creado correctamente.');
		}

		redirect('admin/usuarios.php');
	}
} catch (Throwable $e) {
	set_flash('error', $e->getMessage());
	redirect('admin/usuarios.php');
}

$filtroBuscar = trim((string) ($_GET['buscar'] ?? ''));
$filtroRol = trim((string) ($_GET['rol_filtro'] ?? ''));

$where = [];
$params = [];

if ($filtroBuscar !== '') {
	$where[] = '(nombre LIKE :buscar OR email LIKE :buscar)';
	$params['buscar'] = '%' . $filtroBuscar . '%';
}

if (in_array($filtroRol, ['empleado', 'administrador'], true)) {
	$where[] = 'rol = :rol_filtro';
	$params['rol_filtro'] = $filtroRol;
}

$sql = 'SELECT id, nombre, email, rol, creado_en FROM usuarios';
if (count($where) > 0) {
	$sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY id DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$usuarios = $stmt->fetchAll();

require_once __DIR__ . '/../components/header.php';
?>

<section class="panel">
	<h2><?= $usuarioEditar ? 'Editar usuario' : 'Alta de usuario' ?></h2>

	<form method="post" class="form-grid">
		<input type="hidden" name="accion" value="guardar">

        <input type="hidden" name="id" value="<?= $usuarioEditar['id'] ?? '' ?>">

		<label for="nombre">Nombre</label>
		<input id="nombre" name="nombre" type="text" required
        value="<?= htmlspecialchars($usuarioEditar['nombre'] ?? '') ?>">

		<label for="email">Email</label>
		<input id="email" name="email" type="email" required
        value="<?= htmlspecialchars($usuarioEditar['email'] ?? '') ?>">

		<label for="password">Contraseña</label>
		<input id="password" name="password" type="password" minlength="6"
        <?= $usuarioEditar ? '' : 'required' ?>
        placeholder="<?= $usuarioEditar ? 'Dejar vacio para no cambiar' : '' ?>">

		<label for="rol">Rol</label>
		<select id="rol" name="rol" required>
			<option value="empleado" <?= (($usuarioEditar['rol'] ?? '') === 'empleado') ? 'selected' : '' ?>>Empleado</option>
			<option value="administrador" <?= (($usuarioEditar['rol'] ?? '') === 'administrador') ? 'selected' : '' ?>>Administrador</option>
		</select>

		<button type="submit" class="btn">
            <?= $usuarioEditar ? 'Actualizar usuario' : 'Crear usuario' ?>
        </button>
	</form>
</section>

<section class="panel">
	<h2>Listado de usuarios</h2>
	<form method="get" class="form-grid filtros-grid">
		<div class="filtro-campo">
			<label for="buscar_usuario">Buscar por nombre o email</label>
			<input id="buscar_usuario" name="buscar" type="text"
				value="<?= htmlspecialchars($filtroBuscar) ?>"
				placeholder="Ej: Juan o correo@dominio.com">
		</div>

		<div class="filtro-campo">
			<label for="rol_filtro">Rol</label>
			<select id="rol_filtro" name="rol_filtro">
				<option value="">Todos</option>
				<option value="empleado" <?= $filtroRol === 'empleado' ? 'selected' : '' ?>>Empleado</option>
				<option value="administrador" <?= $filtroRol === 'administrador' ? 'selected' : '' ?>>Administrador</option>
			</select>
		</div>

		<div class="filtros-actions">
			<button type="submit" class="btn btn-small">Filtrar</button>
			<a href="<?= htmlspecialchars(app_url('admin/usuarios.php')) ?>" class="btn btn-secondary btn-small">Limpiar</a>
		</div>
	</form>
	<div class="table-wrap">
		<table>
			<thead>
				<tr>
					<th>ID</th>
					<th>Nombre</th>
					<th>Email</th>
					<th>Rol</th>
					<th>Fecha alta</th>
                    <th>Acciones</th>
				</tr>
			</thead>
			<tbody>
				<?php if (count($usuarios) === 0): ?>
					<tr>
						<td colspan="6">No hay usuarios registrados.</td>
					</tr>
				<?php endif; ?>
				<?php foreach ($usuarios as $item): ?>
					<tr>
						<td><?= (int) $item['id'] ?></td>
						<td><?= htmlspecialchars((string) $item['nombre']) ?></td>
						<td><?= htmlspecialchars((string) $item['email']) ?></td>
						<td><?= htmlspecialchars((string) $item['rol']) ?></td>
						<td><?= htmlspecialchars((string) $item['creado_en']) ?></td>
						<td>
							<div class="actions">
								<a href="usuarios.php?editar=<?= $item['id'] ?>" class="btn btn-small">
									Editar
								</a>
								<form method="post" onsubmit="return confirm('¿Seguro que deseás eliminar este usuario?');">
									<input type="hidden" name="accion" value="eliminar">
									<input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
									<button type="submit" class="btn btn-danger btn-small">Eliminar</button>
								</form>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</section>

<?php require_once __DIR__ . '/../components/footer.php'; ?>