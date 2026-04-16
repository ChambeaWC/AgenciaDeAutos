<?php
declare(strict_types=1);

class Empleado extends Usuario
{
	public function puedeGestionarUsuarios(): bool
	{
		return false;
	}
}
