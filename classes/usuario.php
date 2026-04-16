<?php
declare(strict_types=1);

abstract class Usuario implements Autenticable
{
	private int $id;
	private string $nombre;
	private string $email;
	private string $passwordHash;
	private string $rol;

	public function __construct(int $id, string $nombre, string $email, string $passwordHash, string $rol)
	{
		$this->id = $id;
		$this->setNombre($nombre);
		$this->setEmail($email);
		$this->passwordHash = $passwordHash;
		$this->rol = $rol;
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getNombre(): string
	{
		return $this->nombre;
	}

	public function setNombre(string $nombre): void
	{
		$nombre = trim($nombre);
		if ($nombre === '') {
			throw new InvalidArgumentException('El nombre no puede estar vacio.');
		}
		$this->nombre = $nombre;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function setEmail(string $email): void
	{
		$email = trim($email);
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			throw new InvalidArgumentException('Email invalido.');
		}
		$this->email = $email;
	}

	public function getRol(): string
	{
		return $this->rol;
	}

	public function getPasswordHash(): string
	{
		return $this->passwordHash;
	}

	public function verificarPassword(string $password): bool
	{
		return password_verify($password, $this->passwordHash);
	}

	public function toArray(): array
	{
		return [
			'id' => $this->id,
			'nombre' => $this->nombre,
			'email' => $this->email,
			'rol' => $this->rol,
		];
	}

	abstract public function puedeGestionarUsuarios(): bool;
}
