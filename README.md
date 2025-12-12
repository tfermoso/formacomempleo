# FormaComEmpleo

Plataforma web de búsqueda de empleo desarrollada como **proyecto colaborativo** por alumnos del ciclo de **Desarrollo de Aplicaciones Web (DAW)**.

El proyecto se desarrolla con **PHP procedural (scripts)**, sin uso de frameworks ni patrones de diseño avanzados, al tratarse de uno de los primeros proyectos del curso.

---

## 📌 Descripción del proyecto

FormaComEmpleo es una aplicación web que conecta **candidatos** y **empresas** mediante ofertas de empleo, incorporando un sistema de validación administrativa y seguimiento de candidaturas.

Incluye:
- Registro y autenticación
- Publicación y validación de ofertas
- Inscripción de candidatos
- Gestión del estado de candidaturas
- Notificaciones por correo electrónico

---

## 👥 Perfiles de usuario

- **Candidato**: busca empleo y se inscribe en ofertas.
- **Empresa**: publica ofertas y gestiona candidatos.
- **Administrador**: valida empresas, ofertas y gestiona todo el sistema.

---

## 🧱 Funcionalidades principales

### Candidatos
- Registro
- Login
- Edición de perfil
- Subida de CV
- Inscripción en ofertas
- Consulta del estado de candidaturas

### Empresas
- Registro de empresa
- Login de empresa
- Gestión de datos de empresa
- Creación de ofertas de empleo
- Gestión de candidatos inscritos y cambio de estado

### Administrador
- Validación de empresas
- Publicación de ofertas
- CRUD completo de todas las entidades

---

## 🗂️ Estructura de carpetas

La aplicación se organiza por **roles**, separando claramente las funcionalidades:

```text
/
├── candidato/
│   ├── dashboard.php
│   ├── perfil.php
│   ├── registro.php
│   └── ...
│
├── empresa/
│   ├── dashboard.php
│   ├── crear_oferta.php
│   ├── candidatos_oferta.php
│   └── ...
│
├── adminFE/
│   ├── dashboard.php
│   ├── empresas.php
│   ├── ofertas.php
│   └── ...
│
├── includes/
│   ├── conexion.php
│   ├── auth.php
│   └── funciones.php
│
├── assets/
│   ├── css/
│   ├── js/
│   └── img/
│
└── index.php
