# AgenciaDeAutos

Trabajo Practico Parcial - Sistema de Gestion para Agencia de Autos (Produccion Web).

## Integrantes

- Completar con nombre y apellido 1
- Completar con nombre y apellido 2

## Tecnologias

- PHP orientado a objetos
- MySQL
- HTML5
- CSS3 (sin Bootstrap)

## Requisitos cumplidos de la consigna

- Login con email y contrasena contra base MySQL.
- Roles diferenciados: empleado y administrador.
- CRUD de vehiculos: alta, listado, modificacion y eliminacion.
- Gestion de usuarios solo para administrador: alta y listado.
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

## Instalacion rapida

1. Crear base y tablas importando db/agencia_autos.sql.
2. Configurar credenciales MySQL en components/conf/conf.php.
3. Colocar el proyecto en el servidor local (ej: htdocs o www).
4. Abrir /AgenciaDeAutos en el navegador.

## Usuarios de prueba

- Administrador
	- email: admin@agencia.com
	- password: password
- Empleado
	- email: empleado@agencia.com
	- password: password

## Demo y documentacion solicitada

En la carpeta docs deben adjuntar la demo final:

- Opcion 1: video recorrido (2 a 4 minutos).
- Opcion 2: galeria de imagenes con captions.

Se recomienda incluir:

- Login correcto e incorrecto.
- Alta/edicion/baja de vehiculos.
- Alta de usuarios desde administrador.
- Restriccion de acceso a usuarios no admin.

## Extras recomendados (por ser equipo de 2)

1. Buscador y filtros de vehiculos por marca, modelo y rango de precio.
2. Paginacion del listado de vehiculos.
3. Ordenamiento asc/desc por precio y anio.
4. Historial simple de acciones (quien creo, edito o elimino).
5. Exportar listado de vehiculos a CSV.
6. Subida de imagen por vehiculo.
7. Dashboard con graficos de cantidad por marca.
8. Auditoria de accesos (ultimo login por usuario).

## Notas

- El proyecto esta hecho sin frameworks CSS para permitir edicion visual libre.
- Se aplico un enfoque responsive para celular, tablet, laptop y desktop.
