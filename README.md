# frontend-SAZ

Sitio web de la Sociedad Astronomica de Zacatecas.

## Ejecucion local

```bash
php -S localhost:8000
```

Abre `http://localhost:8000` en el navegador.

---

## Estructura de carpetas

**`content/`**
Archivos JSON con el contenido del sitio. Aqui se publican noticias, eventos, convocatorias y astrofotografias. Cada subcarpeta corresponde a una seccion.

**`assets/`**
Recursos estaticos del sitio: estilos CSS, imagenes e iconos, y archivos JavaScript.

**`pages/`**
Paginas individuales del sitio organizadas por seccion. Cada subcarpeta agrupa las paginas de una seccion (eventos, noticias, quienes-somos, etc.).

**`partials/`**
Fragmentos de interfaz que se reutilizan en multiples paginas, como el header, el footer y las secciones de la pagina de inicio.

**`templates/`**
Estructuras de pagina reutilizables. Definen como se organiza el contenido de una pagina antes de enviarlo al navegador.

---

## Como publicar contenido

Para agregar una noticia, evento, convocatoria o astrofotografia, crea un archivo `.json` nuevo en la subcarpeta correspondiente dentro de `content/`. Usa los archivos existentes como referencia de estructura.
