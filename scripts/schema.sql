-- ============================================================
-- SAZ CMS — Esquema de base de datos
-- MySQL 5.7+ / MariaDB 10.3+
-- Charset: utf8mb4 / Collation: utf8mb4_unicode_ci
-- ============================================================
--
-- RELACIONES ENTRE TABLAS
-- ────────────────────────
--   categorias ──< noticias
--     Una categoría agrupa N noticias. Si se elimina una
--     categoría, las noticias quedan sin categoría
--     (ON DELETE SET NULL); no se borran.
--
--   eventos ──< evento_ediciones
--     Un evento (programa) tiene N ediciones anuales. Si se
--     elimina el evento, todas sus ediciones se eliminan
--     también (ON DELETE CASCADE).
--
--   evento_ediciones ──< edicion_imagenes
--     Cada edición puede tener N fotos de galería. Si se
--     elimina la edición, sus fotos se eliminan también
--     (ON DELETE CASCADE).
--
--   miembros ──< miembro_formacion
--     Un miembro tiene N ítems de formación académica. Si se
--     elimina el miembro, su formación se elimina también
--     (ON DELETE CASCADE).
--
--   miembros ──< miembro_divulgacion
--     Un miembro tiene N actividades de divulgación. Si se
--     elimina el miembro, sus actividades se eliminan también
--     (ON DELETE CASCADE).
--
--   actividades ──< actividad_items
--     Una actividad tiene N tarjetas descriptivas. Si se
--     elimina la actividad, sus ítems se eliminan también
--     (ON DELETE CASCADE).
--
--   actividades ──< actividad_imagenes
--     Una actividad tiene N imágenes de carrusel. Si se
--     elimina la actividad, sus imágenes se eliminan también
--     (ON DELETE CASCADE).
--
--   observaciones ──< observacion_items
--     Un tipo de observación tiene N tarjetas de contenido.
--     Si se elimina la observación, sus ítems se eliminan
--     también (ON DELETE CASCADE).
--
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- SOLO DESARROLLO
-- Elimina todas las tablas para empezar desde cero.
-- NUNCA ejecutar en producción.
-- ============================================================
-- DROP TABLE IF EXISTS observacion_items, observaciones,
--     actividad_imagenes, actividad_items, actividades,
--     edicion_imagenes, evento_ediciones, eventos,
--     miembro_formacion, miembro_divulgacion, miembros,
--     noticias, categorias, convocatorias, astrofotografia,
--     colaboradores,
--     admin_usuarios, configuracion;


-- ============================================================
-- TABLA: categorias
-- ============================================================
-- Vocabulario controlado de categorías para las noticias.
-- Existe como tabla separada para garantizar consistencia:
-- renombrar "Actividad" actualiza todas las noticias
-- automáticamente sin tocar múltiples filas.
--
-- CAMPOS
--   id        Clave primaria numérica.
--   nombre    Texto visible en el sitio ("Actividad", "Ciencia").
--             UNIQUE: no pueden existir dos categorías con el
--             mismo nombre.
--   slug      Versión URL del nombre ("actividad"). UNIQUE.
--   creado_en Fecha de creación del registro. No cambia.
-- ============================================================
CREATE TABLE categorias (
    id        INT          AUTO_INCREMENT PRIMARY KEY,
    nombre    VARCHAR(100) NOT NULL,
    slug      VARCHAR(100) NOT NULL,
    creado_en TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_categorias_nombre UNIQUE (nombre),
    CONSTRAINT uq_categorias_slug   UNIQUE (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- TABLA: noticias
-- ============================================================
--
-- Ciclo de vida del campo estado:
--   borrador → publicado → archivado
--   Un borrador no aparece en ninguna URL pública.
--   Un archivado solo lo ve el admin en el panel.
--
-- CAMPOS
--   id                   Clave primaria numérica.
--   slug                 Identificador URL de la noticia
--                        ("expo-tec-calera-2026"). UNIQUE.
--   titulo               Título visible al lector.
--   resumen              Párrafo corto para tarjetas y previews.
--                        Texto plano, no HTML.
--   contenido            Cuerpo completo del artículo.
--                        HTML generado por TinyMCE. LONGTEXT
--                        porque artículos con imágenes embebidas
--                        pueden superar los 64 KB de TEXT normal.
--   imagen               Ruta relativa a assets/img/noticias/.
--                        Nullable: una noticia puede publicarse
--                        sin imagen de portada.
--   autor                Nombre del autor como texto libre.
--   categoria_id         FK a categorias. NULL = sin categoría.
--   fecha                Fecha de publicación visible. Puede ser
--                        histórica (distinta de creado_en).
--   estado               Ciclo de vida de la noticia.
--   visible_en_principal Controla si aparece en el home. Permite
--                        quitarla del home sin archivarla: sigue
--                        visible en el archivo de noticias.
--   fijado               1 = anclada al inicio del home sin
--                        importar la fecha de publicación.
--   creado_en            Fecha de inserción en la DB. No cambia.
--   actualizado_en       Se actualiza automáticamente cada vez
--                        que se modifica el registro.
-- ============================================================
CREATE TABLE noticias (
    id                   INT          AUTO_INCREMENT PRIMARY KEY,
    slug                 VARCHAR(200) NOT NULL,
    titulo               VARCHAR(255) NOT NULL,
    resumen              TEXT,
    contenido            LONGTEXT,
    imagen               VARCHAR(500),
    autor                VARCHAR(150),
    categoria_id         INT          DEFAULT NULL,
    fecha                DATE         NOT NULL,
    estado               ENUM('borrador','publicado','archivado') NOT NULL DEFAULT 'borrador',
    visible_en_principal TINYINT(1)   NOT NULL DEFAULT 1,
    fijado               TINYINT(1)   NOT NULL DEFAULT 0,
    creado_en            TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    actualizado_en       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT uq_noticias_slug UNIQUE (slug),
    CONSTRAINT fk_noticias_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- WHERE estado='publicado' AND visible_en_principal=1 ORDER BY fijado DESC, fecha DESC
CREATE INDEX idx_noticias_home    ON noticias (estado, visible_en_principal, fijado, fecha);
-- WHERE estado='publicado' ORDER BY fecha DESC
CREATE INDEX idx_noticias_archivo ON noticias (estado, fecha);


-- ============================================================
-- TABLA: eventos
-- ============================================================
-- Representa un programa recurrente de la SAZ (Semana de
-- Astronomía, Olimpiadas, Maratón Messier…). Es el programa
-- en abstracto; sus realizaciones concretas año a año viven
-- en evento_ediciones.
--
-- CAMPOS
--   id               Clave primaria numérica.
--   slug             URL del programa ("semana-astronomia"). UNIQUE.
--   titulo           Nombre oficial del programa.
--   descripcion      Presentación general del programa.
--                    HTML de TinyMCE.
--   imagen_principal Foto de portada del programa. Es distinta
--                    a la imagen de cada edición concreta.
--                    Nullable: puede agregarse después.
--   activo           0 = oculto del sitio. Útil si un programa
--                    se discontinúa sin querer borrarlo.
--   orden            Posición en la página de eventos.
--                    El admin lo controla desde el panel.
--   creado_en        Fecha de inserción en la DB. No cambia.
--   actualizado_en   Se actualiza automáticamente con cada cambio.
-- ============================================================
CREATE TABLE eventos (
    id               INT          AUTO_INCREMENT PRIMARY KEY,
    slug             VARCHAR(100) NOT NULL,
    titulo           VARCHAR(255) NOT NULL,
    descripcion      LONGTEXT,
    imagen_principal VARCHAR(500),
    activo           TINYINT(1)   NOT NULL DEFAULT 1,
    orden            SMALLINT     NOT NULL DEFAULT 0,
    creado_en        TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    actualizado_en   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT uq_eventos_slug UNIQUE (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- TABLA: evento_ediciones
-- ============================================================
-- Cada fila es una realización concreta de un evento en un
-- año: "Semana de Astronomía 2024", "Semana de Astronomía 2025".
--
-- El estado de cada edición se deriva en PHP a partir de las
-- fechas, sin necesidad de un campo adicional:
--   próxima  → fecha_inicio > hoy
--   en curso → hoy BETWEEN fecha_inicio AND fecha_fin
--   pasada   → fecha_fin < hoy
--
-- CAMPOS
--   id             Clave primaria numérica.
--   evento_id      FK al evento padre.
--   anio           Año de la edición. Tipo YEAR de MySQL:
--                  más eficiente que INT para este dato.
--   fecha_inicio   Nullable: puede publicarse la edición antes
--                  de confirmar las fechas exactas.
--   fecha_fin      Nullable por la misma razón que fecha_inicio.
--   lugar          Ciudad o sede ese año. Nullable para eventos
--                  virtuales o sin sede definida.
--   resumen        Descripción de esa edición específica.
--                  HTML de TinyMCE.
--   imagen         Foto representativa de esa edición.
--   pdf            Ruta al PDF de memorias o programa. Nullable.
--   creado_en      Fecha de inserción en la DB. No cambia.
--   actualizado_en Se actualiza automáticamente con cada cambio.
-- ============================================================
CREATE TABLE evento_ediciones (
    id             INT          AUTO_INCREMENT PRIMARY KEY,
    evento_id      INT          NOT NULL,
    anio           YEAR         NOT NULL,
    fecha_inicio   DATE         NULL,
    fecha_fin      DATE         NULL,
    lugar          VARCHAR(255),
    resumen        LONGTEXT,
    imagen         VARCHAR(500),
    pdf            VARCHAR(500),
    creado_en      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_edicion_evento
        FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE,
    -- Un evento no puede tener dos ediciones en el mismo año.
    -- Este UNIQUE ya genera un índice implícito sobre (evento_id, anio);
    -- no se necesita un CREATE INDEX adicional.
    CONSTRAINT uq_edicion_evento_anio UNIQUE (evento_id, anio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- TABLA: edicion_imagenes
-- ============================================================
-- Galería de fotos de una edición de evento (0 a N imágenes).
-- Es una tabla separada para que el CMS pueda agregar, quitar
-- y reordenar fotos individualmente. Si las rutas estuvieran
-- serializadas en un campo de evento_ediciones, eso no sería
-- posible sin reescribir el valor completo en cada operación.
--
-- CAMPOS
--   id             Clave primaria numérica.
--   edicion_id     FK a evento_ediciones.
--   ruta           Ruta relativa a assets/img/eventos/.
--   alt_texto      Texto alternativo para accesibilidad y SEO.
--                  Nullable, pero el admin debe llenarlo siempre.
--   orden          Posición de la foto en el carrusel o galería.
--   creado_en      Fecha de inserción en la DB. No cambia.
--   actualizado_en Se actualiza automáticamente con cada cambio.
-- ============================================================
CREATE TABLE edicion_imagenes (
    id             INT          AUTO_INCREMENT PRIMARY KEY,
    edicion_id     INT          NOT NULL,
    ruta           VARCHAR(500) NOT NULL,
    alt_texto      VARCHAR(255),
    orden          SMALLINT     NOT NULL DEFAULT 0,
    creado_en      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_imagen_edicion
        FOREIGN KEY (edicion_id) REFERENCES evento_ediciones(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- WHERE edicion_id = X ORDER BY orden
CREATE INDEX idx_imagenes_edicion ON edicion_imagenes (edicion_id, orden);


-- ============================================================
-- TABLA: convocatorias
-- ============================================================
-- Convocatorias, becas y llamados a participación que la SAZ
-- publica para sus miembros o el público general.
--
-- Ciclo de vida del campo estado:
--   borrador → publicada → cerrada → archivada
--   'cerrada' es distinta de 'archivada': una convocatoria
--   cerrada sigue visible al público para consultar las bases;
--   una archivada solo la ve el admin en el panel.
--
-- CAMPOS
--   id                Clave primaria numérica.
--   slug              URL amigable. UNIQUE.
--   titulo            Nombre de la convocatoria.
--   resumen           Descripción corta para tarjetas. Texto plano.
--   contenido         Bases y requisitos completos.
--                     HTML de TinyMCE.
--   imagen            Imagen de portada. Nullable.
--   pdf               Ruta al PDF descargable de bases. Nullable.
--   url_externa       Enlace a formulario externo (Google Forms,etc.). Nullable.
--                     VARCHAR(1000) porque URLs pueden ser muy largas.
--   fecha_publicacion Cuándo se anunció la convocatoria en el sitio.
--   fecha_apertura    Cuándo se abre el periodo de postulación.
--                     Nullable: puede publicarse el anuncio antes
--                     de confirmar la fecha de apertura.
--   fecha_cierre      Fecha límite de postulación. Nullable porque
--                     algunas convocatorias no tienen fecha límite
--                     definida ("hasta agotar cupo").
--   estado            Ciclo de vida de la convocatoria. El admin
--                     puede forzar el estado manualmente sin
--                     depender de la lógica de fechas en PHP.
--   creado_en         Fecha de inserción en la DB. No cambia.
--   actualizado_en    Se actualiza automáticamente con cada cambio.
-- ============================================================
CREATE TABLE convocatorias (
    id                INT           AUTO_INCREMENT PRIMARY KEY,
    slug              VARCHAR(200)  NOT NULL,
    titulo            VARCHAR(255)  NOT NULL,
    resumen           TEXT,
    contenido         LONGTEXT,
    imagen            VARCHAR(500),
    pdf               VARCHAR(500),
    url_externa       VARCHAR(1000),
    fecha_publicacion DATE          NOT NULL,
    fecha_apertura    DATE,
    fecha_cierre      DATE,
    estado            ENUM('borrador','publicada','cerrada','archivada') NOT NULL DEFAULT 'borrador',
    creado_en         TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    actualizado_en    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT uq_convocatorias_slug UNIQUE (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- WHERE estado IN ('publicada','cerrada') ORDER BY fecha_cierre
CREATE INDEX idx_convocatorias_estado ON convocatorias (estado, fecha_cierre);


-- ============================================================
-- TABLA: astrofotografia
-- ============================================================
-- Galería de fotografías astronómicas tomadas por miembros
-- de la SAZ. Cada registro es una foto con su ficha técnica.
--
-- Los campos de equipo (telescopio, montura, cámara) y captura
-- (integración, ISO, filtros) son columnas independientes en
-- lugar de un objeto JSON anidado. Esto permite editar cada
-- campo por separado en el panel y filtrar por equipo sin
-- tener que parsear JSON en PHP.
--
-- CAMPOS
--   id                 Clave primaria numérica.
--   slug               Identificador URL ("luna-20240315"). UNIQUE.
--   titulo             Nombre descriptivo de la foto. Nullable:
--                      si está vacío, el frontend usa descripcion
--                      como fallback.
--   fotografo          Nombre del autor. NOT NULL.
--   lugar              Lugar de observación. Nullable.
--   fecha              Fecha de captura. NOT NULL para poder ordenar
--                      la galería cronológicamente.
--   descripcion        Nota del fotógrafo sobre el objeto capturado.
--   fuente             Coordenadas celestes RA/Dec. Campo heredado
--                      del formato anterior; puede quedar vacío en
--                      registros nuevos.
--   imagen             Ruta a assets/img/astrofotografia/. Nullable:
--                      puede subirse después de crear el registro.
--   categoria          Tipo de fotografía. Reemplaza el sistema
--                      anterior de categorización por prefijo de
--                      nombre de archivo (sol-*, luna-*, profundo-*).
--                      NOT NULL sin DEFAULT: el backend debe enviar
--                      siempre un valor explícito; la DB lo rechaza
--                      si no se proporciona.
--   telescopio         Equipo óptico utilizado. Nullable.
--   montura            Montura utilizada. Nullable.
--   camara             Cámara utilizada. Nullable.
--   integracion        Tiempo total de exposición. Nullable.
--   iso_gain           ISO (DSLR) o ganancia (cámara astronómica).
--                      Nullable.
--   filtros            Filtros utilizados. Nullable.
--   post_procesamiento Software y técnicas de procesamiento
--                      (AutoStakkert, PixInsight, etc.). Nullable.
--   visible            0 = retirada de la galería sin borrar el
--                      registro.
--   destacada          1 = Para controlar si puede aparecer en el home.
--   creado_en          Fecha de inserción en la DB. No cambia.
--   actualizado_en     Se actualiza automáticamente con cada cambio.
-- ============================================================
CREATE TABLE astrofotografia (
    id                 INT          AUTO_INCREMENT PRIMARY KEY,
    slug               VARCHAR(100) NOT NULL,
    titulo             VARCHAR(255),
    fotografo          VARCHAR(150) NOT NULL,
    lugar              VARCHAR(255),
    fecha              DATE         NOT NULL,
    descripcion        TEXT,
    fuente             VARCHAR(255),
    imagen             VARCHAR(500),
    categoria          ENUM('sol','luna','espacio_profundo') NOT NULL,
    telescopio         VARCHAR(255),
    montura            VARCHAR(255),
    camara             VARCHAR(255),
    integracion        VARCHAR(255),
    iso_gain           VARCHAR(100),
    filtros            VARCHAR(255),
    post_procesamiento VARCHAR(500),
    visible            TINYINT(1)   NOT NULL DEFAULT 1,
    destacada          TINYINT(1)   NOT NULL DEFAULT 0,
    creado_en          TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    actualizado_en     TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT uq_astro_slug UNIQUE (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- WHERE visible=1 AND categoria='luna' ORDER BY fecha DESC
CREATE INDEX idx_astro_galeria ON astrofotografia (visible, categoria, fecha);
-- WHERE visible=1 AND destacada=1
CREATE INDEX idx_astro_home    ON astrofotografia (visible, destacada);


-- ============================================================
-- TABLA: miembros
-- ============================================================
-- Directorio de miembros de la SAZ. Cada registro es el
-- perfil completo de un miembro con su información profesional.
--
-- Los ítems de formación y divulgación viven en tablas hijas
-- separadas (miembro_formacion, miembro_divulgacion) porque
-- el panel necesita agregar, quitar y reordenar cada ítem
-- individualmente. Si estuvieran serializados en un campo TEXT
-- aquí, eso no sería posible.
--
-- CAMPOS
--   id                Clave primaria numérica.
--   slug              URL del perfil. UNIQUE.
--   nombre            Nombre completo del miembro.
--   especialidad      Área de conocimiento principal. Nullable.
--   cargo             Rol dentro de la organización ("Presidente",
--                     "Secretario", "Tesorero", "Consejo Consultivo",
--                     "Consejo de Vigilancia"). Nullable: un miembro
--                     puede estar activo sin cargo directivo.
--                     Lo usa pages/quienes-somos/directorio.php.
--   en_mesa_directiva 1 = aparece en pages/quienes-somos/mesa-directiva.php.
--                     Separado de cargo porque no todo cargo directivo
--                     pertenece a la mesa ejecutiva.
--   correo            Email visible en el perfil público. Nullable.
--                     Sin UNIQUE porque dos miembros podrían
--                     compartir un correo institucional.
--   ubicacion         Ciudad o estado de residencia. Nullable.
--   distincion        Reconocimiento especial ("Miembro Honorario").
--                     Nullable. Distinto de cargo: un miembro puede
--                     tener distinción sin cargo directivo.
--   imagen            Foto de perfil. Nullable.
--   cv                Ruta al PDF del CV en assets/pdf/. Nullable.
--   generalidades     Presentación y líneas de investigación.
--                     HTML de TinyMCE.
--   activo            0 = miembro retirado; oculto del directorio
--                     sin borrar el registro.
--   orden             Posición en el listado del directorio.
--                     Permite al admin controlar quién aparece
--                     primero (ej. el presidente).
--   creado_en         Fecha de inserción en la DB. No cambia.
--   actualizado_en    Se actualiza automáticamente con cada cambio.
-- ============================================================
CREATE TABLE miembros (
    id                INT          AUTO_INCREMENT PRIMARY KEY,
    slug              VARCHAR(100) NOT NULL,
    nombre            VARCHAR(255) NOT NULL,
    especialidad      VARCHAR(255),
    cargo             VARCHAR(255),
    en_mesa_directiva TINYINT(1)   NOT NULL DEFAULT 0,
    correo            VARCHAR(255),
    ubicacion         VARCHAR(255),
    distincion        VARCHAR(255),
    imagen            VARCHAR(500),
    cv                VARCHAR(500),
    generalidades     LONGTEXT,
    activo            TINYINT(1)   NOT NULL DEFAULT 1,
    orden             SMALLINT     NOT NULL DEFAULT 0,
    creado_en         TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    actualizado_en    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT uq_miembros_slug UNIQUE (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- WHERE activo=1 ORDER BY orden
CREATE INDEX idx_miembros_activo         ON miembros (activo, orden);
-- WHERE activo=1 AND en_mesa_directiva=1 ORDER BY orden
CREATE INDEX idx_miembros_mesa_directiva ON miembros (activo, en_mesa_directiva, orden);


-- ============================================================
-- TABLA: miembro_formacion
-- ============================================================
-- Lista de títulos académicos y formación profesional de un
-- miembro. Tabla hija de miembros (1 miembro : N ítems).
--
-- CAMPOS
--   id          Clave primaria numérica.
--   miembro_id  FK a miembros. Al borrar el miembro, sus ítems
--               de formación se borran también (ON DELETE CASCADE).
--   descripcion Texto del ítem ("Licenciatura en Física, UAZ, 2018").
--               NOT NULL porque el ítem no tiene sentido vacío.
-- ============================================================
CREATE TABLE miembro_formacion (
    id          INT  AUTO_INCREMENT PRIMARY KEY,
    miembro_id  INT  NOT NULL,
    descripcion TEXT NOT NULL,
    CONSTRAINT fk_formacion_miembro
        FOREIGN KEY (miembro_id) REFERENCES miembros(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- WHERE miembro_id = X
CREATE INDEX idx_formacion_miembro ON miembro_formacion (miembro_id);


-- ============================================================
-- TABLA: miembro_divulgacion
-- ============================================================
-- Lista de actividades de divulgación científica de un miembro
-- (conferencias, talleres, publicaciones). Misma estructura
-- que miembro_formacion por la misma razón.
--
-- CAMPOS
--   id          Clave primaria numérica.
--   miembro_id  FK a miembros. Al borrar el miembro, sus
--               actividades se borran también (ON DELETE CASCADE).
--   descripcion Texto del ítem ("Conferencia en Feria de Ciencias
--               UAZ, 2023"). NOT NULL.
-- ============================================================
CREATE TABLE miembro_divulgacion (
    id          INT  AUTO_INCREMENT PRIMARY KEY,
    miembro_id  INT  NOT NULL,
    descripcion TEXT NOT NULL,
    CONSTRAINT fk_divulgacion_miembro
        FOREIGN KEY (miembro_id) REFERENCES miembros(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- WHERE miembro_id = X
CREATE INDEX idx_divulgacion_miembro ON miembro_divulgacion (miembro_id);


-- ============================================================
-- TABLA: admin_usuarios
-- ============================================================
-- Cuentas de acceso al panel CMS. Completamente separada del
-- contenido del sitio: un admin no es necesariamente un miembro
-- de la SAZ, y un miembro no necesariamente tiene acceso al panel.
--
-- CAMPOS
--   id             Clave primaria numérica.
--   nombre         Nombre para mostrar dentro del panel.
--   usuario        Nombre de usuario para el login. UNIQUE.
--   email          Correo del administrador. UNIQUE.
--   hash           Resultado de password_hash($pw, PASSWORD_BCRYPT).
--                  NUNCA se almacena la contraseña en texto claro.
--                  Para verificar: password_verify($input, $hash).
--   activo         0 = cuenta deshabilitada. Permite bloquear el
--                  acceso sin borrar el registro ni perder historial.
--   ultimo_login   Fecha y hora del último login exitoso.
--                  El código PHP lo actualiza en cada login.
--   creado_en      Fecha de creación de la cuenta. No cambia.
--   actualizado_en Se actualiza automáticamente con cada cambio.
-- ============================================================
CREATE TABLE admin_usuarios (
    id             INT          AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(150) NOT NULL,
    usuario        VARCHAR(100) NOT NULL,
    email          VARCHAR(255) NOT NULL,
    hash           VARCHAR(255) NOT NULL,
    activo         TINYINT(1)   NOT NULL DEFAULT 1,
    ultimo_login   TIMESTAMP    NULL DEFAULT NULL,
    creado_en      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT uq_admin_usuario UNIQUE (usuario),
    CONSTRAINT uq_admin_email   UNIQUE (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- TABLA: configuracion
-- ============================================================
-- Parámetros del sitio que el admin puede cambiar desde el
-- panel sin tocar código. Patrón clave-valor estándar en CMS
-- (equivalente a wp_options en WordPress).
--
-- Por qué tabla y no constantes en config.php: una constante
-- PHP requiere acceso al servidor para modificarse; con esta
-- tabla el admin lo hace desde el navegador.
--
-- CAMPOS
--   clave       Identificador único del parámetro. Es la PK.
--               VARCHAR en lugar de INT porque la clave es el
--               único acceso que se usa y ya es un identificador
--               natural y estable.
--   valor       Valor del parámetro como texto. La conversión
--               al tipo correcto (int, bool) se hace en PHP:
--               $limite = (int) get_config('noticias_home_limite');
--   descripcion Texto que ve el admin en el panel para entender
--               qué controla cada parámetro. Nullable.
-- ============================================================
CREATE TABLE configuracion (
    clave       VARCHAR(100) PRIMARY KEY,
    valor       TEXT         NOT NULL,
    descripcion VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Valores por defecto. El admin los puede modificar desde el panel.
INSERT INTO configuracion (clave, valor, descripcion) VALUES
    ('noticias_home_limite', '5',                             'Cantidad de noticias que aparecen en la página principal. También define el máximo de noticias que se pueden fijar.'),
    ('contacto_email',       'sazac2010@gmail.com',           'Correo de contacto visible en la página de contacto.'),
    ('contacto_telefono',    '492 123 16 39',                 'Teléfono de contacto visible en la página de contacto.'),
    ('contacto_direccion',   'Zacatecas, Zacatecas, México',  'Dirección visible en la página de contacto.'),
    ('social_facebook',      'https://www.facebook.com/SAZacatecas', 'URL del perfil de Facebook de la SAZ.'),
    ('social_instagram',     'https://www.instagram.com/sazacatecas/', 'URL del perfil de Instagram de la SAZ.'),
    ('social_x',             'https://x.com/ndezacmx',        'URL del perfil de X (Twitter) de la SAZ.');


-- ============================================================
-- TABLA: colaboradores
-- ============================================================
-- Personas externas que colaboran con la SAZ. Sección propia
-- en el sitio (pages/colaboradores/index.php).
--
-- Distinto de miembros: un colaborador contribuye a actividades
-- de la SAZ pero no es miembro formal de la sociedad.
--
-- CAMPOS
--   id             Clave primaria numérica.
--   nombre         Nombre completo con grado académico.
--   profesion      Descripción de su área ("Astrónomo observacional").
--   red_nombre     Nombre de la red social o plataforma
--                  ("ResearchGate", "LinkedIn", "Instagram", etc.).
--                  Nullable: puede no tener perfil público enlazable.
--   url_red        URL de su perfil en la red social. Nullable.
--                  VARCHAR(500) porque URLs de perfiles pueden ser largas.
--   imagen         Foto del colaborador. Nullable.
--   activo         0 = oculto del sitio sin borrar el registro.
--   orden          Posición en la cuadrícula de colaboradores.
--   creado_en      Fecha de inserción en la DB. No cambia.
--   actualizado_en Se actualiza automáticamente con cada cambio.
-- ============================================================
CREATE TABLE colaboradores (
    id             INT          AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(255) NOT NULL,
    profesion      VARCHAR(255),
    red_nombre     VARCHAR(100),
    url_red        VARCHAR(500),
    imagen         VARCHAR(500),
    activo         TINYINT(1)   NOT NULL DEFAULT 1,
    orden          SMALLINT     NOT NULL DEFAULT 0,
    creado_en      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- WHERE activo=1 ORDER BY orden
CREATE INDEX idx_colaboradores_activo ON colaboradores (activo, orden);


-- ============================================================
-- TABLA: actividades
-- ============================================================
-- Catálogo de tipos de actividad de la SAZ (charlas,
-- conferencias, cursos, diplomados, talleres). Cada registro
-- corresponde a una sección del sitio con su propia URL.
--
-- Los ítems descriptivos viven en actividad_items y las
-- imágenes del carrusel en actividad_imagenes (tablas hijas),
-- por las mismas razones que edicion_imagenes en eventos.
--
-- CAMPOS
--   id             Clave primaria numérica.
--   slug           URL del tipo de actividad ("charlas"). UNIQUE.
--   titulo         Nombre visible ("Charlas").
--   icono          Clase de Bootstrap Icons ("bi bi-chat-left-text").
--   descripcion    Párrafo introductorio de la sección. Texto plano.
--   activo         0 = sección oculta del sitio sin borrar el registro.
--   orden          Posición en el menú de actividades.
--   creado_en      Fecha de inserción en la DB. No cambia.
--   actualizado_en Se actualiza automáticamente con cada cambio.
-- ============================================================
CREATE TABLE actividades (
    id             INT          AUTO_INCREMENT PRIMARY KEY,
    slug           VARCHAR(100) NOT NULL,
    titulo         VARCHAR(255) NOT NULL,
    icono          VARCHAR(100) NOT NULL,
    descripcion    TEXT,
    activo         TINYINT(1)   NOT NULL DEFAULT 1,
    orden          SMALLINT     NOT NULL DEFAULT 0,
    creado_en      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT uq_actividades_slug UNIQUE (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- WHERE activo=1 ORDER BY orden
CREATE INDEX idx_actividades_activo ON actividades (activo, orden);


-- ============================================================
-- TABLA: actividad_items
-- ============================================================
-- Tarjetas descriptivas de cada tipo de actividad. Tabla hija
-- de actividades (1 actividad : N ítems). Misma razón de
-- existir que miembro_formacion: el panel necesita agregar,
-- quitar y reordenar cada ítem individualmente.
--
-- CAMPOS
--   id           Clave primaria numérica.
--   actividad_id FK a actividades. Al borrar la actividad, sus
--                ítems se borran también (ON DELETE CASCADE).
--   titulo       Nombre del ítem ("Charlas de café astronómico").
--   descripcion  Texto descriptivo del ítem.
--   orden        Posición del ítem dentro de la cuadrícula.
-- ============================================================
CREATE TABLE actividad_items (
    id           INT          AUTO_INCREMENT PRIMARY KEY,
    actividad_id INT          NOT NULL,
    titulo       VARCHAR(255) NOT NULL,
    descripcion  TEXT,
    orden        SMALLINT     NOT NULL DEFAULT 0,
    CONSTRAINT fk_item_actividad
        FOREIGN KEY (actividad_id) REFERENCES actividades(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- WHERE actividad_id = X ORDER BY orden
CREATE INDEX idx_actividad_items ON actividad_items (actividad_id, orden);


-- ============================================================
-- TABLA: actividad_imagenes
-- ============================================================
-- Imágenes del carrusel de cada tipo de actividad. Tabla hija
-- de actividades (1 actividad : N imágenes). Separada para
-- poder agregar, quitar y reordenar fotos individualmente,
-- igual que edicion_imagenes para los eventos.
--
-- CAMPOS
--   id             Clave primaria numérica.
--   actividad_id   FK a actividades.
--   ruta           Ruta relativa a assets/img/actividades/{slug}/.
--   alt_texto      Texto alternativo para accesibilidad y SEO.
--                  Nullable, pero el admin debe llenarlo siempre.
--   orden          Posición de la foto en el carrusel.
--   creado_en      Fecha de inserción en la DB. No cambia.
--   actualizado_en Se actualiza automáticamente con cada cambio.
-- ============================================================
CREATE TABLE actividad_imagenes (
    id             INT          AUTO_INCREMENT PRIMARY KEY,
    actividad_id   INT          NOT NULL,
    ruta           VARCHAR(500) NOT NULL,
    alt_texto      VARCHAR(255),
    orden          SMALLINT     NOT NULL DEFAULT 0,
    creado_en      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_imagen_actividad
        FOREIGN KEY (actividad_id) REFERENCES actividades(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- WHERE actividad_id = X ORDER BY orden
CREATE INDEX idx_actividad_imagenes ON actividad_imagenes (actividad_id, orden);


-- ============================================================
-- TABLA: observaciones
-- ============================================================
-- Catálogo de tipos de sesión de observación de la SAZ
-- (diurna, nocturna, solar). Cada registro corresponde a
-- una sección del sitio con su propia URL.
--
-- Los ítems descriptivos viven en observacion_items para
-- poder administrarlos por separado desde el panel.
--
-- CAMPOS
--   id                Clave primaria numérica.
--   slug              URL del tipo ("nocturna"). UNIQUE.
--   titulo            Nombre visible ("Observación Nocturna").
--   icono             Clase de Bootstrap Icons ("bi bi-moon-stars-fill").
--   descripcion_intro Párrafo introductorio de la sección.
--   recomendaciones   Lista de recomendaciones o notas de seguridad
--                     al pie de la página. HTML de TinyMCE. Nullable:
--                     no todos los tipos necesitan esta sección
--                     (ej. diurna no la tiene actualmente).
--   activo            0 = sección oculta sin borrar el registro.
--   orden             Posición en el menú de observaciones.
--   creado_en         Fecha de inserción en la DB. No cambia.
--   actualizado_en    Se actualiza automáticamente con cada cambio.
-- ============================================================
CREATE TABLE observaciones (
    id                INT          AUTO_INCREMENT PRIMARY KEY,
    slug              VARCHAR(100) NOT NULL,
    titulo            VARCHAR(255) NOT NULL,
    icono             VARCHAR(100) NOT NULL,
    descripcion_intro TEXT,
    recomendaciones   LONGTEXT,
    activo            TINYINT(1)   NOT NULL DEFAULT 1,
    orden             SMALLINT     NOT NULL DEFAULT 0,
    creado_en         TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    actualizado_en    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT uq_observaciones_slug UNIQUE (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- WHERE activo=1 ORDER BY orden
CREATE INDEX idx_observaciones_activo ON observaciones (activo, orden);


-- ============================================================
-- TABLA: observacion_items
-- ============================================================
-- Tarjetas de contenido de cada tipo de observación. Tabla
-- hija de observaciones (1 observación : N ítems).
--
-- A diferencia de actividad_items, incluye el campo icono
-- porque cada tarjeta de observación muestra su propio ícono
-- con clase de color Bootstrap (ej. "bi bi-globe2 me-2
-- text-warning"). El frontend usa este valor directamente
-- en el atributo class del <i>.
--
-- CAMPOS
--   id             Clave primaria numérica.
--   observacion_id FK a observaciones. Al borrar el tipo de
--                  observación, sus ítems se borran también
--                  (ON DELETE CASCADE).
--   titulo         Nombre del ítem ("Planetas").
--   icono          Clase Bootstrap Icons con color opcional
--                  ("bi bi-globe2 me-2 text-warning"). Nullable.
--   descripcion    Texto descriptivo del ítem.
--   orden          Posición del ítem en la cuadrícula.
-- ============================================================
CREATE TABLE observacion_items (
    id             INT          AUTO_INCREMENT PRIMARY KEY,
    observacion_id INT          NOT NULL,
    titulo         VARCHAR(255) NOT NULL,
    icono          VARCHAR(150),
    descripcion    TEXT,
    orden          SMALLINT     NOT NULL DEFAULT 0,
    CONSTRAINT fk_item_observacion
        FOREIGN KEY (observacion_id) REFERENCES observaciones(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- WHERE observacion_id = X ORDER BY orden
CREATE INDEX idx_observacion_items ON observacion_items (observacion_id, orden);


-- ============================================================
-- TABLA: suscriptores
-- ============================================================
-- Personas que envían el formulario de pages/suscribirse/.
-- Separada del directorio de miembros porque un suscriptor
-- es un interesado externo, no un miembro formal de la SAZ.
--
-- CAMPOS
--   id        Clave primaria numérica.
--   nombre    Nombre completo del interesado.
--   correo    Email de contacto. UNIQUE: evita duplicados.
--             INSERT IGNORE en PHP para manejar el caso
--             "ya estoy suscrito" sin mostrar error al usuario.
--   telefono  Teléfono opcional.
--   interes   Área de interés seleccionada en el formulario.
--             Nullable: el campo es opcional en el form.
--   mensaje   Texto libre opcional del formulario.
--   activo    1 = activo, 0 = baja de la lista (GDPR/LFPDPPP).
--             No se borran registros; se desactivan.
--   creado_en Fecha de registro. No cambia.
-- ============================================================
CREATE TABLE suscriptores (
    id        INT          AUTO_INCREMENT PRIMARY KEY,
    nombre    VARCHAR(100) NOT NULL,
    correo    VARCHAR(254) NOT NULL,
    telefono  VARCHAR(20),
    interes   ENUM('Divulgacion','Astrofotografia','Observacion','Investigacion','Educacion'),
    mensaje   TEXT,
    activo    TINYINT(1)   NOT NULL DEFAULT 1,
    creado_en TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_suscriptor_correo UNIQUE (correo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- WHERE activo=1 ORDER BY creado_en DESC
CREATE INDEX idx_suscriptores_activo ON suscriptores (activo, creado_en);


-- ============================================================
-- TABLA: mensajes_contacto
-- ============================================================
-- Mensajes recibidos desde pages/contacto/. Se guardan en DB
-- como respaldo permanente, independientemente de si el email
-- se entregó o no. El admin puede marcar cada mensaje como
-- leído desde el panel.
--
-- CAMPOS
--   id        Clave primaria numérica.
--   nombre    Nombre del remitente.
--   correo    Email del remitente para poder responder.
--   asunto    Asunto del mensaje. Nullable (campo opcional).
--   mensaje   Cuerpo del mensaje. NOT NULL.
--   leido     0 = sin leer, 1 = leído. El admin lo actualiza.
--             Permite mostrar un contador de no leídos en el panel.
--   creado_en Fecha de recepción. No cambia.
-- ============================================================
CREATE TABLE mensajes_contacto (
    id        INT          AUTO_INCREMENT PRIMARY KEY,
    nombre    VARCHAR(100) NOT NULL,
    correo    VARCHAR(254) NOT NULL,
    asunto    VARCHAR(150),
    mensaje   TEXT         NOT NULL,
    leido     TINYINT(1)   NOT NULL DEFAULT 0,
    creado_en TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- WHERE leido=0 ORDER BY creado_en DESC  (bandeja del panel)
CREATE INDEX idx_mensajes_leido ON mensajes_contacto (leido, creado_en);


SET FOREIGN_KEY_CHECKS = 1;

