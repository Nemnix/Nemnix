# Patron de Diseño MVC (Modelo Vista Controlador)
## ¿Qué es?
MVC es un patrón de diseño que separa una aplicación en tres componentes con responsabilidades distintas, para organizar el código y facilitar su mantenimiento:

- Modelo: maneja los datos y la lógica de negocio.
- Vista: muestra la información al usuario.
- Controlador: recibe las peticiones del usuario y coordina entre el Modelo y la Vista.

## Vista
### Debe tener:
- Código HTML/CSS para mostrar la información.
- Variables recibidas desde el controlador (ya procesadas).
- Estructuras simples para mostrar datos (control de flujo mínimo).
### No debe tener:
- Consultas SQL ni acceso directo a la base de datos.
- Lógica de negocio (cálculos complejos, validaciones, reglas del sistema).
- Conexiones o instancias de clases del modelo.

## Modelo
### Debe tener:
- Conexión y consultas a la base de datos (SELECT, INSERT, UPDATE, DELETE).
- Lógica de negocio y validaciones de datos.
- Métodos que devuelvan datos (arrays, objetos) listos para usar.
### No debe tener:
- Código HTML.
- Referencias a $_GET, $_POST directamente (eso lo recibe del controlador).
- Lógica de presentación (cómo se ve algo en pantalla).
## Controlador
### Debe tener:
- Recepción de peticiones del usuario ($_GET, $_POST).
- Llamadas a los métodos del Modelo.
- Envío de los datos obtenidos a la Vista correspondiente.
### No debe tener:
- Consultas SQL directas (eso es tarea del Modelo).
- Código HTML (eso es tarea de la Vista).
- Lógica de negocio compleja (solo coordina, no procesa reglas del sistema).