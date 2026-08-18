# Normalización

## Usuarios:

**USUARIO**(<u>cedula</u>, nombre, apellido, claveHash, activo)

**ADMINISTRADOR**(<u>cedula</u>)

**TECNICO**(<u>cedula</u>)

**SOLICITANTE**(<u>cedula</u>)

En ADMINISTRADOR, cedula es fk de USUARIO.cedula
En TECNICO, cedula es fk de USUARIO.cedula
En SOLICITANTE, cedula es fk de USUARIO.cedula

* 1FN -> Estan en primera forma normal debido a que todos los atributos son atomicos.
* 
* 2FN -> Esta en segunda forma normal ya que todas son dependencias totales. Debido a que solo cuenta con una clave primaria por lo cual todos los atributos dependen de ella.

*3NF -> Esta en tercera forma normal ya que no hay dependencias transitivas.

## Registro diario, ticket y diagnostico:

**REGISTRO_DIARIO**(<u>ID_R</u>, grupo, horaEntrada, horaFin, docente, asignatura, fecha, nombreAula, turno)

**TICKET**(<u>ID_R</u>, <u>ID_T</u>,  estado, nombreEquipo, nombreAlumno, descripcionT, prioridad) 

**DIAGNOSTICO**(<u>ID_D</u>, ID_R, ID_T, cedula, estadoD, fechaD, descripcionD)

* 1FN -> Estan en primera forma normal debido a que todos los atributos son atomicos.
* 
* 2FN -> Tanto registo diario y diagnostico estan en segunda forma normal ya que sus tablas tienen primary key atomica. Y por el lado de ticket que tiene una primary key compuesta, esta también esta en 2NF ya que, los atributos no dependen de una sola parte de la clave, sino que de toda la llave completa, ya que ticket es una entidad debil de registro diario. 
* 3NF -> Las dos tablas registo diario y diagnostico estan en 3FN ya que no hay dependencias transitivas. Y a su vez ticket tambien esta en tercera forma, ya que ninguno de sus atributos se define por otras claves que no sean la primary key.

## PRESTAMO Y EQUIPO DE PRESTAMO

PRESTAMO(<u>cedula</u>, ciAlumno, nombreAlumno, apellidoAlumno, estadoPrestamo, fechaInicio, fechaLimite)

EQUIPO_PRESTAMO(<u>ID_EP</u>, ID_P, modelo)

1FN -> porque los datos son atómicos y no hay grupos que se repitan.

2FN -> no existen dependencias parciales ya que las claves son simples.

3FN -> debido a que no existen dependencias transitivas, todos los atributos comunes dependen directamente de su clave primaria.

## LABORATORIOS

