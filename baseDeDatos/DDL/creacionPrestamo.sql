CREATE TABLE PRESTAMO (
    ID_P INT NOT NULL AUTO_INCREMENT,
    cedula CHAR(8) NOT NULL,
    ciAlumno CHAR(8) NOT NULL,
    nombreAlumno VARCHAR(600) NOT NULL,
    apellidoAlumno VARCHAR(600) NOT NULL,
    estadoPrestamo VARCHAR(600) NOT NULL,
    fechaInicio DATE NOT NULL,
    fechaLimite DATE NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,

    CONSTRAINT pk_prestamo
        PRIMARY KEY (ID_P)

);

ALTER TABLE PRESTAMO
    ADD CONSTRAINT fk_alumno_prestamo
    FOREIGN KEY (ciAlumno)
    REFERENCES ALUMNO_PRESTAMO(ciAlumno);

ALTER TABLE PRESTAMO
    ADD CONSTRAINT fk_prestamo
    FOREIGN KEY (cedula)
    REFERENCES TECNICO(cedula);
