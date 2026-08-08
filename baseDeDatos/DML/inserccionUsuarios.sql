INSERT INTO USUARIO (cedula, nombre, apellido, claveHash) 
VALUES (11111111, "pepe", "gonzalez", "$2y$12$wWh92L3SicKSfe3H/JRKtuj96.gGf/5LPgvASMNJ1FYdcP/CsrSdS");

INSERT INTO USUARIO (cedula, nombre, apellido, claveHash) 
VALUES (22222222, "jose", "gutierrez", "$2y$12$QSlsDogvjW2drBnGudz1uOanUTLSNSRfphl2W1oxG2uPpJIT45Bt.");

INSERT INTO USUARIO (cedula, nombre, apellido, claveHash) 
VALUES (33333333, "pedro", "martinez", "$2y$12$prRAPEz7uSEohHfBpX3g.eNCYYWHIbvce03P7ADedu/Z1HH8n9FgG");

INSERT INTO ADMINISTRADOR (cedula) VALUES ('11111111');
INSERT INTO TECNICO (cedula) VALUES ('22222222');
INSERT INTO ADMINISTRADOR (cedula) VALUES ('22222222');
INSERT INTO SOLICITANTE (cedula) VALUES ('33333333');

INSERT INTO TICKET (estado, nombreEquipo, nombreAlumno, descripcionT, prioridad) VALUES
('Abierto', 'PC01', 'Nahuel Olazarria', 'No enciende la PC del laboratorio 3', 'Alta'),
('En proceso', 'PC02', 'María Gómez', 'Problema de conexión a internet', 'Media'),
('Cerrado', 'PC03', 'Carlos Ruiz', 'Impresora no responde', 'Baja');

INSERT INTO DIAGNOSTICO (ID_T, ID_D, estadoD, fechaD, descripcionD) VALUES
(1, 1, 'Revisado', '2026-08-01', 'Se detectó fuente de poder quemada'),
(1, 2, 'Solucionado', '2026-08-02', 'Se reemplazó la fuente de poder'),
(2, 1, 'Pendiente', '2026-08-01', 'Se solicitó revisión del router'),
(3, 1, 'Solucionado', '2026-07-30', 'Se reinstaló el driver de la impresora');