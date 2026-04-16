<?php
declare(strict_types=1);

require_once __DIR__ . '/../components/conf/conf.php';

Auth::logout();
set_flash('success', 'Sesion cerrada correctamente.');
redirect('log/login.php');
