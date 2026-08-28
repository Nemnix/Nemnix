INSERT INTO ALUMNO_PRESTAMO (ciAlumno, nombreAlumno, apellidoAlumno) VALUES
('20345678', 'Juan', 'Perez'),
('20456789', 'Maria', 'Gomez'),
('20567890', 'Carlos', 'Rodriguez');


INSERT INTO PRESTAMO (cedula, ciAlumno, nombreAlumno, apellidoAlumno, estadoPrestamo, fechaInicio, fechaLimite) VALUES
('22222222', '20345678', 'Juan', 'Perez', 'Disponible', '2026-03-01', '2026-03-15'),
('22222222', '20456789', 'Maria', 'Gomez', 'Prestamo en fecha', '2026-04-02', '2026-04-16'),
('22222222', '20567890', 'Carlos', 'Rodriguez', 'VENCIDO', '2026-05-10', '2026-05-24');
