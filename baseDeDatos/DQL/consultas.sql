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