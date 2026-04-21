<?php
declare(strict_types=1);

require_once __DIR__ . '/../components/conf/conf.php';
Auth::requireLogin();

$usuario = Auth::user();

$stmtAutos = db()->query('SELECT id, marca, modelo, anio, precio FROM vehiculos ORDER BY id DESC');
$autos = [];
while ($row = $stmtAutos->fetch()) {
	$autos[] = Auto::fromRow($row);
}

$stmtUsuarios = db()->query('SELECT COUNT(*) AS total FROM usuarios');
$totalUsuarios = (int) $stmtUsuarios->fetch()['total'];

$valorTotal = 0.0;
foreach ($autos as $auto) {
	$valorTotal += $auto->getPrecio();
}

$resumen = [
	'cantidad_autos' => count($autos),
	'cantidad_usuarios' => $totalUsuarios,
	'valor_total' => $valorTotal,
	'autos_premium' => Auto::contarAutosPremium($autos),
];

require_once __DIR__ . '/../components/header.php';
?>

<section class="hero">
	<h2>Panel de control</h2>
	<p>
		Bienvenido, <strong><?= htmlspecialchars($usuario['nombre']) ?></strong>.
		Rol actual: <strong><?= htmlspecialchars($usuario['rol']) ?></strong>
	</p>
</section>

<section class="cards-grid">
	<article class="card">
		<h3>Autos cargados</h3>
		<p class="metric"><?= $resumen['cantidad_autos'] ?></p>
	</article>
	<article class="card">
		<h3>Usuarios cargados</h3>
		<p class="metric"><?= $resumen['cantidad_usuarios'] ?></p>
	</article>
	<article class="card">
		<h3>Valor de inventario</h3>
		<p class="metric"><?= htmlspecialchars(Auto::formatearPrecio($resumen['valor_total'])) ?></p>
	</article>
	<article class="card">
		<h3>Autos premium</h3>
		<p class="metric"><?= $resumen['autos_premium'] ?></p>
	</article>
</section>

<section class="actions-row">
	<a class="btn" href="<?= htmlspecialchars(app_url('pages/vehiculos.php')) ?>">Gestionar vehiculos</a>
	<?php if (Auth::isAdmin()): ?>
		<a class="btn btn-secondary" href="<?= htmlspecialchars(app_url('admin/usuarios.php')) ?>">Gestionar usuarios</a>
	<?php endif; ?>
</section>

<?php require_once __DIR__ . '/../components/footer.php'; ?>
