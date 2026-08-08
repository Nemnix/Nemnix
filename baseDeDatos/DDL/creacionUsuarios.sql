CREATE TABLE USUARIO (
    cedula CHAR(8) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR (100) NOT NULL,
    claveHash VARCHAR(255) NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,

    CONSTRAINT pk_usuario
        PRIMARY KEY (cedula)
)

CREATE TABLE ADMINISTRADOR (
    cedula CHAR(8) NOT NULL,

    CONSTRAINT pk_administrador
        PRIMARY KEY (cedula)
)

CREATE TABLE TECNICO(
    cedula CHAR(8) NOT NULL,

    CONSTRAINT pk_tecnico
        PRIMARY KEY (cedula)
)

CREATE TABLE SOLICITANTE(
    cedula CHAR(8) NOT NULL,

    CONSTRAINT pk_solicitante
        PRIMARY KEY (cedula)
)

ALTER TABLE ADMINISTRADOR
    ADD CONSTRAINT fk_administrador_usuario
    FOREIGN KEY (cedula)
    REFERENCES USUARIO (cedula);

ALTER TABLE TECNICO
    ADD CONSTRAINT fk_tecnico_usuario
    FOREIGN KEY (cedula)
    REFERENCES USUARIO (cedula);

ALTER TABLE SOLICITANTE
    ADD CONSTRAINT fk_solicitante_usuario
    FOREIGN KEY (cedula)
    REFERENCES USUARIO (cedula);

    CREATE TABLE TICKET (
    ID_T INT NOT NULL AUTO_INCREMENT,
    estado VARCHAR(50) NOT NULL,
    nombreEquipo VARCHAR(50) NOT NULL,
    nombreAlumno VARCHAR(255) NOT NULL,
    descripcionT VARCHAR(255) NOT NULL,
    prioridad VARCHAR(255) NOT NULL,
    

    CONSTRAINT pk_ticket
        PRIMARY KEY (ID_T)
);

CREATE TABLE DIAGNOSTICO (
     ID_T  INT NOT NULL,
     ID_D INT NOT NULL,
     estadoD VARCHAR(50) NOT NULL,
     fechaD DATE NOT NULL,
     descripcionD VARCHAR(255) NOT NULL,
    

    CONSTRAINT pk_diagnostico
        PRIMARY KEY (ID_T, ID_D)
);



ALTER TABLE DIAGNOSTICO
    ADD CONSTRAINT fk_diagnostico
    FOREIGN KEY (ID_T)
    REFERENCES TICKET (ID_T);

