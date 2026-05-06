# frontend-SAZ

Sitio web institucional de la Sociedad Astronomica de Zacatecas. PHP procedural con datos en JSON.

## Requisitos

- PHP 8.x instalado.

## Ejecucion local

```bash
php -S localhost:8000
```

Abre `http://localhost:8000` en el navegador.

---

## Estructura de carpetas

**`content/`**
Archivos JSON con el contenido del sitio (noticias, eventos, convocatorias, astrofotografia, miembros).

**`assets/`**
Recursos estaticos del sitio: CSS, JS, imagenes y PDF.

**`pages/`**
Paginas PHP organizadas por seccion (eventos, noticias, quienes-somos, etc.).

**`partials/`**
Fragmentos reutilizables (header, footer, portada).

**`templates/`**
Plantillas reutilizables para eventos y actividades.

---

## Como publicar contenido

Para agregar una noticia, evento, convocatoria, astrofotografia o miembro, crea/edita un archivo `.json` en `content/` y agrega los recursos asociados en `assets/`.
