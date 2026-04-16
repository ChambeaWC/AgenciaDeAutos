<?php
declare(strict_types=1);

interface Autenticable
{
    public function verificarPassword(string $password): bool;
    public function puedeGestionarUsuarios(): bool;
}
