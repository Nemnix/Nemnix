CREATE TABLE ALUMNO_PRESTAMO (
    ciAlumno CHAR(8) NOT NULL,
    nombreAlumno VARCHAR(600) NOT NULL,
    apellidoAlumno VARCHAR(600) NOT NULL,

    CONSTRAINT pk_alumno_prestamo
        PRIMARY KEY (ciAlumno)

);
