/**SELECT cedula
FROM USUARIO
WHERE activo = TRUE;

SELECT cedula
FROM USUARIO
WHERE activo = FALSE;

SELECT cedula
FROM USUARIO AS u
INNER JOIN ADMINISTRADOR AS a ON u.cedula = a.cedula;

SELECT cedula
FROM USUARIO AS u
INNER JOIN TECNICO AS t ON u.cedula = t.cedula;

SELECT cedula
FROM USUARIO AS u
INNER JOIN SOLICITANTE AS s ON u.cedula = s.cedula;

SELECT ID_D, fechaD, estadoD, descripcionD
FROM DIAGNOSTICO AS d */

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
        WHEN l.cedula IS NOT NULL THEN 1
        ELSE 0
    END AS tecnico,

    CASE
        WHEN s.cedula IS NOT NULL THEN 1
        ELSE 0
    END AS solicitante

FROM USUARIO AS u

    LEFT JOIN ADMINISTRADOR AS a
    ON a.cedula = u.cedula

    LEFT JOIN TECNICO AS t
    ON t.cedula = u.cedula

    LEFT JOIN SOLICITANTE AS s
    ON s.cedula = u.cedula

WHERE u.cedula = '00000000';

SELECT
    d.ID_D,
    d.ID_T,
    d.ID_R,
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
    
SELECT
    a.ciAlumno,
    a.nombreAlumno,
    a.apellidoAlumno,

    FROM ALUMNO_PRESTAMO AS a

SELECT
    p.ID_P,
    p.cedula,
    p.ciAlumno,
    p.estadoPrestamo,
    p.fechaInicio,
    p.fechaLimite,

    FROM PRESTAMO AS p

    LEFT JOIN PRESTAMO AS p
    ON p.ciAlumno = a.ciAlumno

SELECT
    e.ID_EP,
    e.ID_P,
    e.modelo,
    
    FROM EQUIPO_PRESTAMO AS e

    LEFT JOIN EQUIPO_PRESTAMO AS e
    ON e.ID_P = p.ID_P

SELECT
    l.ID_L,
    l.nombreLaboratorio,
    l.numLaboratorio,

    FROM LABORATORIO AS l

SELECT
    l.ID_L,
    s.cedula,
    so.descripcionSolicitud,
    so.fecha,
    so.gmail,

    FROM SOLICITUD AS so

    LEFT JOIN SOLICITANTE AS s
    ON s.cedula = so.cedula

    LEFT JOIN LABORATORIO AS l
    ON l.ID_L = so.ID_L

SELECT
    LABORATORIO.ID_L,
    LABORATORIO.cedulaA,
    LABORATORIO.nombreLaboratorio,
    LABORATORIO.numLaboratorio,
    SOLICITA.ID_L,
    SOLICITA.cedula,
    SOLICITA.descripcionSolicitud,
    SOLICITA.Fecha,
    SOLICITA.Gmail
FROM LABORATORIO
LEFT JOIN SOLICITA ON LABORATORIO.ID_L = SOLICITA.ID_L;


    
    

