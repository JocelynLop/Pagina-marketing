# Autenticacion Las Tablas

Relacionado con: [[Avances Las Tablas]], [[Bitacora 2026-09-29 Las Tablas]], [[Administracion Las Tablas]], [[Blog Las Tablas]], [[Datos Las Tablas]], [[Frontend Las Tablas]]

Estado: login/registro construido y pulido visualmente.

## Archivos principales

- `src/Controller/SecurityController.php`
- `src/Security/AppUser.php`
- `src/Security/UserStore.php`
- `src/Security/JsonUserProvider.php`
- `templates/security/login.html.twig`
- `templates/security/register.html.twig`
- `templates/security/layout.html.twig`
- `templates/security/_password_field.html.twig`
- `config/packages/security.yaml`

## Avances

- Login con provider JSON.
- Logout POST con CSRF.
- Registro publico en `/registro`.
- Registro pide fecha de nacimiento en formato `DD/MM/AAAA`.
- Validaciones: requerido, formato real, no futuro, contrasena minima y confirmacion.
- Usuarios guardados en JSON mediante `UserStore`.
- Rutas `/usuario` protegidas con `ROLE_ADMIN`.
- Layout compartido para login y registro.
- Fondo y panel visual alineados a la identidad de Las Tablas.
- Campos de contrasena con boton para mostrar/ocultar.
- Revision visual en escritorio y movil realizada.

## Pendiente

- Probar login de admin configurado desde `.env`.
- Confirmar compatibilidad con usuarios antiguos sin fecha de nacimiento.
- Revisar textos finales con el cliente antes de cierre.

