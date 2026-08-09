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

INSERT INTO REGISTRO_DIARIO (grupo, horaEntrada, horaFin, docente, asignatura, fecha, nombreAula, turno)
VALUES 
('3A', '08:00', '10:00', 'Juan Pérez', 'Programación', '2026-08-08', 'Lab 1', 'Matutino'),
('2B', '10:00', '12:00', 'Ana Gómez', 'Redes', '2026-08-08', 'Lab 2', 'Matutino'),
('4C', '14:00', '16:00', 'Carlos Ruiz', 'Base de Datos', '2026-08-08', 'Lab 1', 'Vespertino');


INSERT INTO TICKET (ID_R, estado, nombreEquipo, nombreAlumno, descripcionT, prioridad)
VALUES 
(1, 'Pendiente', 'PC-05', 'María López', 'La PC no enciende', 'Alta'),
(2, 'En proceso', 'PC-12', 'Pedro Fernández', 'No conecta a la red', 'Media'),
(3, 'Resuelto', 'PC-08', 'Lucía Martínez', 'Pantalla parpadea', 'Baja');

INSERT INTO DIAGNOSTICO (ID_R, ID_T, estadoD, fechaD, descripcionD)
VALUES 
(1, 1, 'En proceso', '2026-08-08', 'Se revisó la fuente de poder, posible falla eléctrica'),
(2, 2, 'Finalizado', '2026-08-08', 'Cable de red dañado, se reemplazó'),
(3, 3, 'Finalizado', '2026-08-08', 'Se cambió el cable flex de la pantalla');