-- Empresas
INSERT INTO empresas (cif, nombre, telefono, web, persona_contacto, email_contacto, ciudad, provincia, verificada)
VALUES
('B12345678', 'Tech Solutions SL', '986111222', 'https://techsolutions.es', 'Laura Gómez', 'laura@techsolutions.es', 'Vigo', 'Pontevedra', 1),
('C87654321', 'Marketing Creativo SA', '986333444', 'https://marketingcreativo.com', 'Carlos Pérez', 'carlos@marketingcreativo.com', 'A Coruña', 'A Coruña', 1);

-- Usuarios de empresa (contraseña = 1234)
INSERT INTO usuarios (nombre, apellidos, telefono, email, password_hash, idempresa, is_admin)
VALUES
('Ana', 'Fernández López', '600111222', 'ana@techsolutions.es', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 0),
('Miguel', 'Rodríguez Castro', '600333444', 'miguel@marketingcreativo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 0);

-- Sectores
INSERT INTO sectores (nombre) VALUES
('Tecnología'),
('Marketing'),
('Recursos Humanos');

-- Modalidades
INSERT INTO modalidad (nombre) VALUES
('Presencial'),
('Teletrabajo'),
('Híbrido');

-- Ofertas vinculadas a empresas
INSERT INTO ofertas (idempresa, idsector, idmodalidad, titulo, descripcion, requisitos, funciones, salario_min, salario_max, tipo_contrato, jornada, ubicacion, fecha_publicacion, estado)
VALUES
(1, 1, 2, 'Desarrollador Backend PHP', 'Buscamos programador con experiencia en PHP/MySQL.', 'Experiencia mínima 2 años, conocimientos en Laravel.', 'Desarrollo y mantenimiento de aplicaciones web.', 18000, 24000, 'Indefinido', 'Completa', 'Vigo', CURDATE(), 'publicada'),
(2, 2, 3, 'Especialista en Marketing Digital', 'Se requiere especialista en SEO/SEM.', 'Experiencia en campañas Google Ads y redes sociales.', 'Gestión de campañas digitales y análisis de métricas.', 20000, 28000, 'Temporal', 'Completa', 'A Coruña', CURDATE(), 'publicada');
