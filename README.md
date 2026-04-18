# AgenciaDeAutos

Trabajo Practico Parcial - Sistema de Gestion para Agencia de Autos (Produccion Web).

## Integrantes

- Benicio Mercante
- Galo Rosselli

## Tecnologias

- PHP orientado a objetos
- MySQL
- HTML5
- CSS3 (sin Bootstrap)

## Requisitos cumplidos de la consigna

- Login con email y contraseña contra base MySQL.
- Roles diferenciados: empleado y administrador.
- CRUD de vehiculos: alta, listado, modificacion y eliminacion.
- Gestion de usuarios solo para administrador: alta, listado, modificacion y eliminacion.
- POO aplicada con clases, objetos, constructores, encapsulamiento y getters/setters.
- Herencia: Usuario -> Empleado/Admin.
- Interface: Autenticable.
- Metodos/propiedades estaticas en clase Auto.
- Arrays indexados y asociativos (usados en dashboard y listados).
- Formularios por POST.

## Estructura del proyecto

- classes: clases de dominio y autenticacion
- components: layout y configuracion
- log: login/logout
- pages: inicio y gestion de vehiculos
- admin: gestion de usuarios
- css: estilos responsive
- db: script SQL de base de datos

## Usuarios de prueba

- Administrador
	- email: admin@agencia.com
	- password: password
- Empleado
	- email: empleado@agencia.com
	- password: password
