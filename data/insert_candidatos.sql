INSERT INTO candidatos (dni, nombre, apellidos, telefono, email, password_hash, cv, ciudad, provincia)
VALUES
('12345678A', 'Ana', 'Pérez García', '600123456', 'ana.perez@example.com', '$2y$10$hashAna', 'cvs/ana_perez.pdf', 'Madrid', 'Madrid'),
('87654321B', 'Luis', 'Martínez López', '600654321', 'luis.martinez@example.com', '$2y$10$hashLuis', 'cvs/luis_martinez.pdf', 'Barcelona', 'Barcelona'),
('11223344C', 'María', 'Fernández Soto', '600987654', 'maria.fernandez@example.com', '$2y$10$hashMaria', 'cvs/maria_fernandez.pdf', 'Valencia', 'Valencia');
-- Ana se inscribe en la oferta 1
INSERT INTO ofertas_candidatos (idoferta, idcandidato, estado, comentarios)
VALUES (1, 1, 'inscrito', 'Interesada en el puesto, disponibilidad inmediata');

-- Luis se inscribe en la oferta 2
INSERT INTO ofertas_candidatos (idoferta, idcandidato, estado, comentarios)
VALUES (2, 2, 'inscrito', 'Experiencia previa en el sector');

-- María se inscribe en la oferta 1 y 3
INSERT INTO ofertas_candidatos (idoferta, idcandidato, estado, comentarios)
VALUES 
(1, 3, 'inscrito', 'Gran motivación por la empresa'),
(3, 3, 'inscrito', 'Busca cambio de residencia a Valencia');
