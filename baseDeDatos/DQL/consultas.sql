-- USUARIOS

SELECT cedula
FROM USUARIO
WHERE activo = TRUE;

SELECT cedula
FROM USUARIO
WHERE activo = FALSE;

SELECT u.cedula
FROM USUARIO AS u
INNER JOIN ADMINISTRADOR AS a ON u.cedula = a.cedula;

SELECT u.cedula
FROM USUARIO AS u
INNER JOIN TECNICO AS t ON u.cedula = t.cedula;

SELECT u.cedula, s.gmail
FROM USUARIO AS u
INNER JOIN SOLICITANTE AS s ON u.cedula = s.cedula;

SELECT
    u.cedula,
    u.nombre,
    u.apellido,
    u.claveHash,
    u.activo,

    CASE
        WHEN a.cedula IS NOT NULL THEN 1
        ELSE 0
    END AS administrador,

    CASE
        WHEN t.cedula IS NOT NULL THEN 1
        ELSE 0
    END AS tecnico,

    CASE
        WHEN s.cedula IS NOT NULL THEN 1
        ELSE 0
    END AS solicitante,

    s.gmail

FROM USUARIO AS u

    LEFT JOIN ADMINISTRADOR AS a
    ON a.cedula = u.cedula

    LEFT JOIN TECNICO AS t
    ON t.cedula = u.cedula

    LEFT JOIN SOLICITANTE AS s
    ON s.cedula = u.cedula

WHERE u.cedula = '00000000';


-- DIAGNOSTICOS

SELECT
    d.ID_D,
    d.ID_T,
    d.ID_R,
    d.cedula,
    d.fechaD,
    d.estadoD,
    d.descripcionD,

    CASE
        WHEN r.ID_R IS NOT NULL THEN 1
        ELSE 0
    END AS registro_diario,

    CASE
        WHEN t.ID_T IS NOT NULL THEN 1
        ELSE 0
    END AS ticket

FROM DIAGNOSTICO AS d

    LEFT JOIN TICKET AS t
    ON t.ID_R = d.ID_R
    AND t.ID_T = d.ID_T

    LEFT JOIN REGISTRO_DIARIO AS r
    ON r.ID_R = d.ID_R;


-- PRESTAMOS

SELECT
    a.ciAlumno,
    a.nombreAlumno,
    a.apellidoAlumno

FROM ALUMNO_PRESTAMO AS a;

SELECT
    p.ID_P,
    p.cedula,
    p.ciAlumno,
    a.nombreAlumno,
    a.apellidoAlumno,
    p.estadoPrestamo,
    p.fechaInicio,
    p.fechaLimite

FROM PRESTAMO AS p

    LEFT JOIN ALUMNO_PRESTAMO AS a
    ON a.ciAlumno = p.ciAlumno;

SELECT
    e.ID_EP,
    e.ID_P,
    e.modelo,
    p.ciAlumno,
    p.estadoPrestamo

FROM EQUIPO_PRESTAMO AS e

    LEFT JOIN PRESTAMO AS p
    ON p.ID_P = e.ID_P;


-- LABORATORIOS

SELECT
    l.ID_L,
    l.nombreLaboratorio,
    l.numLaboratorio,
    l.nombreAula,
    l.numAula

FROM LABORATORIO AS l;

SELECT
    e.ID_E,
    l.ID_L,
    l.nombreLaboratorio,
    l.numLaboratorio,
    e.cantMonitor,
    e.cantPC,
    e.cantTeclado,
    e.cantMouse

FROM EQUIPO AS e

    LEFT JOIN LABORATORIO AS l
    ON l.ID_L = e.ID_L;

SELECT
    l.ID_L,
    l.nombreLaboratorio,
    l.numLaboratorio,
    u.cedula,
    u.nombre,
    u.apellido

FROM GESTIONA AS g

    LEFT JOIN LABORATORIO AS l
    ON l.ID_L = g.ID_L

    LEFT JOIN USUARIO AS u
    ON u.cedula = g.cedula;


-- SOLICITUDES

SELECT
    l.ID_L,
    l.nombreLaboratorio,
    l.numLaboratorio,
    so.cedula,
    so.descripcionSolicitud,
    so.fecha,
    so.gmail

FROM SOLICITA AS so

    LEFT JOIN SOLICITANTE AS s
    ON s.cedula = so.cedula

    LEFT JOIN LABORATORIO AS l
    ON l.ID_L = so.ID_L;


-- REGISTRO DIARIO Y TICKETS

SELECT
    r.ID_R,
    l.ID_L,
    r.cedula,
    r.fecha,
    r.horaEntrada,
    r.horaFin,
    r.asignatura,
    r.turno,
    r.grupo

FROM REGISTRO_DIARIO AS r

    LEFT JOIN SOLICITANTE AS s
    ON s.cedula = r.cedula

    LEFT JOIN LABORATORIO AS l
    ON l.ID_L = r.ID_L;

SELECT
    t.ID_T,
    t.ID_R,
    r.fecha,
    r.ID_L,
    t.estado,
    t.tipoDispositivo,
    t.nombreEquipo,
    t.nombreAlumno,
    t.descripcionT,
    t.prioridad

FROM TICKET AS t

    LEFT JOIN REGISTRO_DIARIO AS r
    ON r.ID_R = t.ID_R;
