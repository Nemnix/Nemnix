INSERT INTO REGISTRO_DIARIO (cedula, ID_L, grupo, horaEntrada, horaFin, asignatura, fecha, turno) VALUES
('33333333', 1, '3A', '08:00', '10:00', 'Programacion I', '2026-03-10', 'Matutino'),
('33333333', 2, '4B', '14:00', '16:00', 'Base de Datos', '2026-04-05', 'Vespertino'),
('33333333', 3, '5C', '19:00', '21:00', 'Redes', '2026-05-20', 'Nocturno');


INSERT INTO TICKET (ID_R, ID_T, estado, nombreEquipo, nombreAlumno, descripcionT, prioridad) VALUES
(1, 1, 'Abierto', 'Laptop modelo A', 'Pedro Gomez', 'El equipo no enciende', 'Alta'),
(2, 1, 'En proceso', 'Laptop modelo B', 'Sofia Rodriguez', 'Pantalla con fallas', 'Media'),
(3, 1, 'Cerrado', 'Laptop modelo C', 'Martin Alvarez', 'Bateria no carga', 'Baja');
