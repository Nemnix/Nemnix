# Normalización

## Relaciones USUARIO, ADMINISTRADOR, TECNICO Y SOLICITANTE:

**USUARIO**(<u>cedula</u>, nombre, apellido, claveHash, activo)

**ADMINISTRADOR**(<u>cedula</u>)

**TECNICO**(<u>cedula</u>)

**SOLICITANTE**(<u>cedula</u>)

En ADMINISTRADOR, cedula es fk de USUARIO.cedula
En TECNICO, cedula es fk de USUARIO.cedula
En SOLICITANTE, cedula es fk de USUARIO.cedula

Clave primaria {cedula}

### Primera forma normal 
 Estan en primera forma normal debido a que todos los atributos son atomicos.


### Segunda forma normal 
Se analizan los atributos no primos con respecto a la clave primaria:

{cedula} -> {nombre} D.F total
{cedula} -> {apellido} D.F total
{cedula} -> {claveHash} D.F total
{cedula} -> {activo} D.F total


Esta en segunda forma normal ya que todas son dependencias totales. Debido a que solo cuenta con una clave primaria por lo cual todos los atributos dependen de ella.

### Tercera forma normal 
 Esta en tercera forma normal ya que no hay dependencias transitivas.

## Relaciones REGISTRO_DIARIO, TICKET y DIAGNOSTICO:

**REGISTRO_DIARIO**(<u>ID_R</u>, grupo, horaEntrada, horaFin, docente, asignatura, fecha, nombreAula, numLaboratorio, turno)

**TICKET**(<u>ID_R</u>, <u>ID_T</u>,  estado, nombreEquipo, nombreAlumnoT, descripcionT, prioridad) 

**DIAGNOSTICO**(<u>ID_D</u>, ID_R, ID_T, cedula, estadoD, fechaD, descripcionD)

### Primera forma normal

Estan en primera forma normal debido a que todos los atributos son atomicos.

### Segunda forma normal 
Analisis de los atributos no primos con respecto a la clave primaria:

REGISTRO_DIARIO:

Tanto registo diario y diagnostico estan en segunda forma normal ya que sus tablas tienen primary key atomica. Y por el lado de ticket que tiene una primary key compuesta, esta también esta en 2NF ya que, los atributos no dependen de una sola parte de la clave, sino que de toda la llave completa, ya que ticket es una entidad debil de registro diario. 
* 3NF -> Las dos tablas registo diario y diagnostico estan en 3FN ya que no hay dependencias transitivas. Y a su vez ticket tambien esta en tercera forma, ya que ninguno de sus atributos se define por otras claves que no sean la primary key.

## PRESTAMO Y EQUIPO DE PRESTAMO

PRESTAMO(<u>ID_P</u>,cedulaTecnico, ciAlumno, nombreAlumno, apellidoAlumno, estadoPrestamo, fechaInicio, fechaLimite)

EQUIPO_PRESTAMO(<u>ID_EP</u>, ID_P, modelo)

1FN -> porque los datos son atómicos y no hay grupos que se repitan.

2FN -> no existen dependencias parciales ya que las claves son simples.

3NF -> si existen dependencias transitivas, debido a que ciAlumno puede determinar a nombreAlumno y apellidoAlumno, por ende se debe de hacer una nueva tabla llamada ALUMNO_PRESTAMO.

Solucion:

ALUMNO_PRESTAMO(<u>ciAlumno</u>, nombreAlumno, apellidoAlumno)

PRESTAMO(<u>ID_P</u>,cedula,ciAlumno, estadoPrestamo, fechaInicio, fechaLimite)
En PESTAMO, es fk ciAlumno de ALUMNO_PRESTAMO ciAlumno
En PREESTAMO, es fk cedulaTecnico de TECNICO cedula

EQUIPO_PRESTAMO(<u>ID_EP</u>, ID_P, modelo)
En EQUIPO_PRESTAMO, es fk ID_P de PRESTAMO ID_P

## LABORATORIOS

LABORATORIO(<u>ID_L</u>, nombreLaboratorio)
EQUIPO(<u>ID_E</u>, ID_L, CantMonitor, CantPC, CantTeclado, CantMouse)
En EQUIPO, es fk EQUIPO ID_L de LABORATORIO ID_L
SOLICITA(<u>ID_L</u>,<u>cedula</u>, descripcionSolicitud, gmail, fecha)
    

* 1FN -> Estan en primera forma normal debido a que todos los atributos son atomicos.

* 2FN -> Esta en segunda forma normal ya que todas son dependencias totales. Debido a que solo cuenta con una clave primaria por lo cual todos los atributos dependen de ella.

* 3NF -> Esta en tercera forma normal ya que no hay dependencias transitivas.