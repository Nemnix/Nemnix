
INSERT INTO LABORATORIO (nombreLab, numeroLab) VALUES
('Laboratorio A', '101'),
('Laboratorio B', '102'),
('Laboratorio C', '103');

INSERT INTO EQUIPO (ID_E, ID_L, cantMonitores, cantPC, cantTeclado, cantMouse) VALUES
('EQ001', 1, 10, 10, 10, 10),
('EQ002', 2, 15, 15, 15, 15),
('EQ003', 3, 8, 8, 8, 8);

INSERT INTO GESTIONA (ID_L, cedula) VALUES
(1, '22222222'),
(2, '22222222'),
(3, '22222222');

INSERT INTO SOLICITA (ID_L, cedula, descripcionSolicitud, fecha, gmail) VALUES
(1, '22222222', 'Falla PC', '2026-08-20', 'carlos@gmail.com'),
(2, '22222222', 'Sin red', '2026-08-21', 'maria@gmail.com');