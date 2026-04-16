<?php
declare(strict_types=1);

class Admin extends Usuario
{
	public function puedeGestionarUsuarios(): bool
	{
		return true;
	}
}
