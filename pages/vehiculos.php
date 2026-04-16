<?php
declare(strict_types=1);

require_once __DIR__ . '/../components/conf/conf.php';
Auth::requireLogin();

$db = db();

try {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$accion = (string) ($_POST['accion'] ?? '');

		if ($accion === 'crear' || $accion === 'actualizar') {
			$id = (int) ($_POST['id'] ?? 0);
			$marca = (string) ($_POST['marca'] ?? '');
			$modelo = (string) ($_POST['modelo'] ?? '');
			$anio = (int) ($_POST['anio'] ?? 0);
			$precio = (float) ($_POST['precio'] ?? 0);

			$auto = new Auto($id, $marca, $modelo, $anio, $precio);

			if ($accion === 'crear') {
				$stmt = $db->prepare('INSERT INTO vehiculos (marca, modelo, anio, precio) VALUES (:marca, :modelo, :anio, :precio)');
				$stmt->execute([
					'marca' => $auto->getMarca(),
					'modelo' => $auto->getModelo(),
					'anio' => $auto->getAnio(),
					'precio' => $auto->getPrecio(),
				]);
				set_flash('success', 'Vehiculo creado correctamente.');
			} else {
				if ($id <= 0) {
					throw new InvalidArgumentException('ID invalido para actualizar.');
				}

				$stmt = $db->prepare('UPDATE vehiculos SET marca = :marca, modelo = :modelo, anio = :anio, precio = :precio WHERE id = :id');
				$stmt->execute([
					'id' => $id,
					'marca' => $auto->getMarca(),
					'modelo' => $auto->getModelo(),
					'anio' => $auto->getAnio(),
					'precio' => $auto->getPrecio(),
				]);
				set_flash('success', 'Vehiculo actualizado correctamente.');
			}
		}

		if ($accion === 'eliminar') {
			$id = (int) ($_POST['id'] ?? 0);
			if ($id <= 0) {
				throw new InvalidArgumentException('ID invalido para eliminar.');
			}

			$stmt = $db->prepare('DELETE FROM vehiculos WHERE id = :id');
			$stmt->execute(['id' => $id]);
			set_flash('success', 'Vehiculo eliminado correctamente.');
		}

		redirect('pages/vehiculos.php');
	}
} catch (Throwable $e) {
	set_flash('error', $e->getMessage());
	redirect('pages/vehiculos.php');
}

$autoEditar = null;
if (isset($_GET['edit'])) {
	$idEditar = (int) $_GET['edit'];
	if ($idEditar > 0) {
		$stmt = $db->prepare('SELECT id, marca, modelo, anio, precio FROM vehiculos WHERE id = :id LIMIT 1');
		$stmt->execute(['id' => $idEditar]);
		$row = $stmt->fetch();
		if ($row) {
			$autoEditar = Auto::fromRow($row);
		}
	}
}

$stmt = $db->query('SELECT id, marca, modelo, anio, precio FROM vehiculos ORDER BY id DESC');
$autos = [];
while ($row = $stmt->fetch()) {
	$autos[] = Auto::fromRow($row);
}

require_once __DIR__ . '/../components/header.php';
?>

<section class="panel">
	<h2><?= $autoEditar ? 'Editar vehiculo' : 'Alta de vehiculo' ?></h2>
	<form method="post" class="form-grid">
		<input type="hidden" name="accion" value="<?= $autoEditar ? 'actualizar' : 'crear' ?>">
		<input type="hidden" name="id" value="<?= $autoEditar ? $autoEditar->getId() : 0 ?>">

		<label for="marca">Marca</label>
		<input id="marca" name="marca" type="text" required value="<?= htmlspecialchars($autoEditar ? $autoEditar->getMarca() : '') ?>">

		<label for="modelo">Modelo</label>
		<input id="modelo" name="modelo" type="text" required value="<?= htmlspecialchars($autoEditar ? $autoEditar->getModelo() : '') ?>">

		<label for="anio">Anio</label>
		<input id="anio" name="anio" type="number" min="1900" max="2100" required value="<?= htmlspecialchars((string) ($autoEditar ? $autoEditar->getAnio() : date('Y'))) ?>">

		<label for="precio">Precio</label>
		<input id="precio" name="precio" type="number" min="1" step="0.01" required value="<?= htmlspecialchars((string) ($autoEditar ? $autoEditar->getPrecio() : '')) ?>">

		<button type="submit" class="btn"><?= $autoEditar ? 'Guardar cambios' : 'Crear vehiculo' ?></button>
		<?php if ($autoEditar): ?>
			<a href="<?= htmlspecialchars(app_url('pages/vehiculos.php')) ?>" class="btn btn-secondary">Cancelar</a>
		<?php endif; ?>
	</form>
</section>

<section class="panel">
	<h2>Listado de vehiculos</h2>
	<div class="table-wrap">
		<table>
			<thead>
				<tr>
					<th>ID</th>
					<th>Marca</th>
					<th>Modelo</th>
					<th>Anio</th>
					<th>Precio</th>
					<th>Acciones</th>
				</tr>
			</thead>
			<tbody>
				<?php if (count($autos) === 0): ?>
					<tr>
						<td colspan="6">No hay vehiculos cargados.</td>
					</tr>
				<?php endif; ?>
				<?php foreach ($autos as $auto): ?>
					<tr>
						<td><?= $auto->getId() ?></td>
						<td><?= htmlspecialchars($auto->getMarca()) ?></td>
						<td><?= htmlspecialchars($auto->getModelo()) ?></td>
						<td><?= $auto->getAnio() ?></td>
						<td><?= htmlspecialchars(Auto::formatearPrecio($auto->getPrecio())) ?></td>
						<td class="actions">
							<a href="<?= htmlspecialchars(app_url('pages/vehiculos.php?edit=' . $auto->getId())) ?>" class="btn btn-small">Editar</a>
							<form method="post" onsubmit="return confirm('Seguro que deseas eliminar este vehiculo?');">
								<input type="hidden" name="accion" value="eliminar">
								<input type="hidden" name="id" value="<?= $auto->getId() ?>">
								<button type="submit" class="btn btn-small btn-danger">Eliminar</button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</section>

<?php require_once __DIR__ . '/../components/footer.php'; ?>
