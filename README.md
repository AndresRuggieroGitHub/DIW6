# Lexi - Plataforma de aprendizaje de inglés

![HTML](https://img.shields.io/badge/HTML-5-orange)
![CSS](https://img.shields.io/badge/CSS-3-blue)
![JavaScript](https://img.shields.io/badge/JS-Vanilla-yellow)
![Bootstrap](https://img.shields.io/badge/UI-Bootstrap%205-purple)
![Status](https://img.shields.io/badge/Project-DIW%20Final-green)

Aplicación web desarrollada para la asignatura DIW (Diseño de Interfaces Web), orientada al aprendizaje de idiomas mediante vocabulario, ejercicios e interacciones simples.

## Descripción

Lexi es un prototipo front-end centrado en la experiencia de usuario, con navegación clara, diseño responsive y persistencia local mediante `localStorage`.

Incluye:
- Página principal
- Biblioteca de vocabulario
- Ejercicios interactivos
- Página de progreso
- Perfil de usuario
- Carrito y página de producto premium
- Páginas de información, contacto e inicio de sesión

## Funcionalidades principales

- Menú responsive para móvil y escritorio
- Biblioteca con guardado en "Mi lista" y colecciones
- Ejercicios por modo y registro de sesiones
- Seguimiento visual del progreso
- Botón de subir con desplazamiento suave
- Notificaciones visuales en acciones clave
- Carrito dinamico con persistencia en localStorage
- Modales y confirmaciones para acciones relevantes
- Atajo de teclado: tecla T para volver al inicio

## Accesibilidad y usabilidad

- Enlace Saltar al contenido para navegacion por teclado
- Focus visible en elementos interactivos
- Estructura semantica en las paginas principales
- Revision con Lighthouse como parte del prototipo

## Tecnologías utilizadas

- HTML5
- CSS3
- JavaScript
- Bootstrap 5
- Bootstrap Icons

## Estructura del proyecto

- index.html - Página principal
- biblioteca.html - Biblioteca de vocabulario
- ejercicios.html - Ejercicios
- progreso.html - Panel de progreso
- perfil.html - Perfil de usuario
- producto.html - Planes premium
- carrito.html - Carrito
- contacto.html - Página de contacto
- info.html - Página informativa
- login.html - Inicio de sesión
- style.css - Estilos principales
- js/script.js - Lógica e interacciones compartidas

## Cómo ejecutar

1. Descargar o clonar el repositorio.
2. Abrir index.html en el navegador.

## Estado actual

El proyecto está preparado como prototipo visual y funcional en cliente. Antes de migrarlo a Laravel conviene centralizar layouts, mover el estado fuera de `localStorage` y consolidar la lógica compartida.

## Publicación

- Repositorio: https://github.com/AndresRuggieroGitHub/DIW6
- GitHub Pages: https://andresruggierogithub.github.io/DIW6/

## Demo

Video de validación en dos navegadores: AQUI_TU_ENLACE_DE_VIDEO

## Autor

Andres Ruggiero
