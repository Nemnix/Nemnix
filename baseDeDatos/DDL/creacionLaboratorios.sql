


CREATE TABLE LABORATORIO (
ID_L INT NOT NULL AUTO_INCREMENT,
nombreLab CHAR(15) NOT NULL,
numeroLab CHAR(8) NOT NULL,

CONSTRAINT pk_laboratorio
    PRIMARY KEY (ID_L)
);

CREATE TABLE EQUIPO (
ID_E CHAR(8) NOT NULL,
ID_L INT NOT NULL,
cantMonitores INT NOT NULL,
cantPC INT NOT NULL,
cantTeclado INT NOT NULL,
cantMouse INT NOT NULL,
CONSTRAINT pk_equipo
    PRIMARY KEY (ID_E, ID_L)
);

alter table EQUIPO
    add constraint fk_equipo
    foreign key (ID_L)
    references LABORATORIO (ID_L);

CREATE TABLE GESTIONA (
ID_L INT NOT NULL,
cedula CHAR(8) NOT NULL,
CONSTRAINT pk_gestiona
    PRIMARY KEY (ID_L, cedula)
);

alter table GESTIONA
    add constraint fk_gestiona
    foreign key (ID_L)
    references LABORATORIO (ID_L),

    add constraint fk_gestiona2
    foreign key (cedula)
    references ADMINISTRADOR (cedula);

CREATE TABLE SOLICITA (
ID_L INT NOT NULL,
cedula CHAR(8) NOT NULL,
descripcionSolicitud CHAR(8) NOT NULL,
fecha DATE NOT NULL,
gmail CHAR(20) NOT NULL,
CONSTRAINT pk_solicita
    PRIMARY KEY (ID_L, cedula)
);