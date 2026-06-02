SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- DROP TABLE IF EXISTS observacion_items, observaciones,
--     actividad_imagenes, actividad_items, actividades,
--     evento_edicion_imagenes, evento_ediciones, eventos,
--     miembro_formacion, miembro_divulgacion, miembros,
--     noticias, categorias_noticias, convocatorias,
--     astrofotografia, astrofoto_categorias,
--     colaborador_redes, colaboradores, instituciones, cargos,
--     intereses_suscripcion, suscriptores, mensajes_contacto,
--     admin_usuarios, configuracion;


CREATE TABLE categorias_noticias (
    id        INT          AUTO_INCREMENT PRIMARY KEY,
    nombre    VARCHAR(100) NOT NULL,
    slug      VARCHAR(100) NOT NULL,
    creado_en TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_cat_noticias_nombre UNIQUE (nombre),
    CONSTRAINT uq_cat_noticias_slug   UNIQUE (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


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
        FOREIGN KEY (categoria_id) REFERENCES categorias_noticias(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_noticias_home    ON noticias (estado, visible_en_principal, fijado, fecha);
CREATE INDEX idx_noticias_archivo ON noticias (estado, fecha);


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
    CONSTRAINT uq_edicion_evento_anio UNIQUE (evento_id, anio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE evento_edicion_imagenes (
    id             INT          AUTO_INCREMENT PRIMARY KEY,
    edicion_id     INT          NOT NULL,
    ruta           VARCHAR(500) NOT NULL,
    alt_texto      VARCHAR(255),
    orden          SMALLINT     NOT NULL DEFAULT 0,
    creado_en      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_ev_edicion_imagen
        FOREIGN KEY (edicion_id) REFERENCES evento_ediciones(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_ev_edicion_imagenes ON evento_edicion_imagenes (edicion_id, orden);


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

CREATE INDEX idx_convocatorias_estado ON convocatorias (estado, fecha_cierre);


CREATE TABLE astrofoto_categorias (
    id          INT          AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL,
    slug        VARCHAR(100) NOT NULL,
    icono       VARCHAR(100) NOT NULL DEFAULT 'bi-camera',
    color       VARCHAR(20)  NOT NULL DEFAULT '#818cf8',
    descripcion VARCHAR(255),
    creado_en   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_astrofoto_cat_nombre UNIQUE (nombre),
    CONSTRAINT uq_astrofoto_cat_slug   UNIQUE (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO astrofoto_categorias (nombre, slug, icono, color, descripcion) VALUES
    ('Sol',              'sol',      'bi-sun-fill',        '#f59e0b', 'Fotografía solar, manchas, fáculas y prominencias'),
    ('Luna',             'luna',     'bi-moon-stars-fill', '#7dd3fc', 'Fases lunares, cráteres, mares y formaciones'),
    ('Espacio Profundo',  'profundo', 'bi-stars',           '#a78bfa', 'Nebulosas, galaxias, cúmulos estelares y objetos del catálogo Messier');


CREATE TABLE astrofotografia (
    id                 INT          AUTO_INCREMENT PRIMARY KEY,
    slug               VARCHAR(100) NOT NULL,
    titulo             VARCHAR(255),
    fotografo          VARCHAR(150) NOT NULL,
    lugar              VARCHAR(255),
    fecha              DATE         NOT NULL,
    descripcion        TEXT,
    coordenadas        VARCHAR(255),
    imagen             VARCHAR(500),
    categoria_id       INT          NOT NULL,
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
    CONSTRAINT uq_astro_slug UNIQUE (slug),
    CONSTRAINT fk_astro_categoria
        FOREIGN KEY (categoria_id) REFERENCES astrofoto_categorias(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_astro_galeria ON astrofotografia (visible, categoria_id, fecha);
CREATE INDEX idx_astro_home    ON astrofotografia (visible, destacada);


CREATE TABLE cargos (
    id     INT          AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    CONSTRAINT uq_cargo_nombre UNIQUE (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO cargos (nombre) VALUES
    ('Presidente'),
    ('Vicepresidente'),
    ('Secretario'),
    ('Tesorero'),
    ('Consejo Consultivo'),
    ('Consejo de Vigilancia');


CREATE TABLE miembros (
    id                INT          AUTO_INCREMENT PRIMARY KEY,
    slug              VARCHAR(100) NOT NULL,
    nombre            VARCHAR(255) NOT NULL,
    especialidad      VARCHAR(255),
    cargo_id          INT          NULL,
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
    CONSTRAINT uq_miembros_slug UNIQUE (slug),
    CONSTRAINT fk_miembro_cargo
        FOREIGN KEY (cargo_id) REFERENCES cargos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_miembros_activo         ON miembros (activo, orden);
CREATE INDEX idx_miembros_mesa_directiva ON miembros (activo, en_mesa_directiva, orden);


CREATE TABLE miembro_formacion (
    id          INT      AUTO_INCREMENT PRIMARY KEY,
    miembro_id  INT      NOT NULL,
    descripcion TEXT     NOT NULL,
    orden       SMALLINT NOT NULL DEFAULT 0,
    CONSTRAINT fk_formacion_miembro
        FOREIGN KEY (miembro_id) REFERENCES miembros(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_formacion_miembro ON miembro_formacion (miembro_id, orden);


CREATE TABLE miembro_divulgacion (
    id          INT      AUTO_INCREMENT PRIMARY KEY,
    miembro_id  INT      NOT NULL,
    descripcion TEXT     NOT NULL,
    orden       SMALLINT NOT NULL DEFAULT 0,
    CONSTRAINT fk_divulgacion_miembro
        FOREIGN KEY (miembro_id) REFERENCES miembros(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_divulgacion_miembro ON miembro_divulgacion (miembro_id, orden);


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


CREATE TABLE configuracion (
    clave       VARCHAR(100) PRIMARY KEY,
    valor       LONGTEXT     NOT NULL,
    tipo        VARCHAR(50)  NOT NULL DEFAULT 'text',
    grupo       VARCHAR(50)  NOT NULL DEFAULT 'General',
    descripcion VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO configuracion (clave, valor, tipo, grupo, descripcion) VALUES
    ('sitio_nombre',         'Sociedad Astronómica de Zacatecas', 'text',   'General', 'Nombre oficial del sitio. Se usa en el brand del header, el hero, el footer y el título fallback del sitio.'),
    ('noticias_home_limite', '5',                                 'number', 'General', 'Cantidad de noticias que aparecen en la página principal. También define el máximo de noticias que se pueden fijar.'),
    ('suscribirse_descripcion', 'Únete a la SAZ y recibe información sobre eventos, actividades y oportunidades de colaboración.', 'textarea', 'General', 'Texto invitacional que aparece debajo del título en la página de suscripción.'),
    ('instituciones_titulo',       'Instituciones con las que colaboramos', 'text',     'General', 'Título de la sección de instituciones colaboradoras en la página principal.'),
    ('instituciones_descripcion',  'Colaboramos con universidades, centros de investigación y organizaciones de divulgación científica a nivel regional, nacional e internacional.', 'textarea', 'General', 'Descripción de la sección de instituciones colaboradoras.'),
    ('actividades_info_nota', 'Esta sección será actualizada conforme se programen nuevas sesiones. Para más detalles, contacta a la SAZ a través de la página de contacto.', 'textarea', 'General', 'Nota informativa al pie de cada página de actividad.'),
    ('hero_titulo',
     'Sociedad Astronómica de Zacatecas',
     'text', 'Hero', 'Título principal (h1) del hero en la página de inicio.'),
    ('hero_subtitulo',
     'Comunidad dedicada a la divulgación científica, observación astronómica e impulso de proyectos académicos en Zacatecas.',
     'textarea', 'Hero', 'Texto descriptivo debajo del título en el hero de la página principal.'),
    ('hero_imagen',
     'assets/img/aniversarioXV.png',
     'text', 'Hero', 'Ruta relativa de la imagen del hero (desde la raíz del sitio). Ej: assets/img/foto.jpg'),
    ('hero_imagen_alt',
     'XV Aniversario de la Sociedad Astronómica de Zacatecas',
     'text', 'Hero', 'Texto alternativo (alt) de la imagen del hero. Importante para accesibilidad y SEO.'),
    ('footer_copyright',           'Hecho en México. Sociedad Astronómica de Zacatecas, todos los derechos reservados', 'text', 'Footer', 'Texto de copyright visible en el pie de página.'),
    ('footer_lavnet_url',          'http://gipimo.ddns.net:8000/lavnet-zac/', 'url',  'Footer', 'URL del enlace LavNet-Zac-Mx en el topbar y el footer.'),
    ('footer_lavnet_nombre',       'LavNet-Zac-Mx',                           'text', 'Footer', 'Texto visible del enlace LavNet en el topbar y el footer.'),
    ('footer_transparencia_url',   '#',                                        'url',  'Footer', 'URL de la página de Transparencia. Usar "#" si aún no está disponible.'),
    ('footer_aviso_privacidad_url','#',                                        'url',  'Footer', 'URL del Aviso de Privacidad. Usar "#" si aún no está disponible.'),
    ('contacto_email',       'sazac2010@gmail.com',           'email', 'Contacto', 'Correo de contacto visible en la página de contacto.'),
    ('contacto_telefono',    '492 123 16 39',                 'text',  'Contacto', 'Teléfono de contacto visible en la página de contacto.'),
    ('contacto_direccion',   'Zacatecas, Zacatecas, México',  'text',  'Contacto', 'Dirección visible en la página de contacto.'),
    ('contacto_mapa_embed',
     'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d29068.03782367076!2d-102.58324!3d22.7711!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x86824ebbf47eaaa5%3A0x2c96536bfa1fe2ec!2sZacatecas%2C%20Zac.%2C%20Mexico!5e0!3m2!1ses!2smx!4v1680000000000!5m2!1ses!2smx',
     'url', 'Contacto', 'URL del iframe de Google Maps para la página de contacto.'),
    ('social_facebook',  'https://www.facebook.com/SAZacatecas',    'url', 'Social', 'URL del perfil de Facebook de la SAZ.'),
    ('social_instagram', 'https://www.instagram.com/sazacatecas/',  'url', 'Social', 'URL del perfil de Instagram de la SAZ.'),
    ('social_x',         'https://x.com/ndezacmx',                  'url', 'Social', 'URL del perfil de X (Twitter) de la SAZ.'),
    ('quienes_somos_historia',
     'El 23 de junio de 2010, se fundó la Sociedad Astronómica de Zacatecas A.C., con el objetivo de reunir entusiastas, estudiantes y profesionales interesados en la astronomía, para promover el conocimiento del cielo y las ciencias del espacio en el estado. Surgió como una iniciativa ciudadana impulsada por un pequeño grupo de aficionados, divulgadores y científicos que buscaban dar un carácter formal y organizado a las actividades astronómicas que ya se realizaban en el estado, creando así un espacio permanente para la formación, la observación y la divulgación científica.',
     'html', 'Quienes Somos', 'Párrafo de historia/presentación en la página Quiénes somos. Acepta HTML.'),
    ('quienes_somos_mision',
     '"Promover la investigación especializada así como la afición por la astronomía para elevar el conocimiento del Universo y el interés por su estudio y comprensión." Lema: "Compartiendo miradas al Universo profundo"',
     'html', 'Quienes Somos', 'Texto de Misión en la página Quiénes somos. Acepta HTML.'),
    ('quienes_somos_vision',
     '"Ser en 2030 la sociedad astronómica líder en el centro-norte de México, reconocida por su excelencia en la promoción de la cultura científica, la formación de aficionados y profesionales en astronomía, y por consolidar a Zacatecas como un referente nacional en la divulgación, observación e investigación del Universo, actuando siempre con un espíritu de fraternidad, humanismo y compromiso social."',
     'html', 'Quienes Somos', 'Texto de Visión en la página Quiénes somos. Acepta HTML.'),
    ('quienes_somos_objetivo_general',
     '"El fomento y la promoción de una mayor cultura científica, principalmente astronómica y ciencias y ramas afines tales como astrobiología, arqueoastronomía, ciencias planetarias, matemáticas, física, geofísica, biología, instrumentación, éstos últimos principalmente en sus aspectos relativos a la astronomía, entre otras; pero principalmente fomentar y promover en todos sus aspectos la astronomía, como especialidad científica o de afición."',
     'html', 'Quienes Somos', 'Texto de Objetivo General. Acepta HTML.'),
    ('quienes_somos_objetivos_particulares',
     '<ul><li>Enseñanza, difusión, promoción y divulgación de la astronomía en el Estado de Zacatecas mediante cursos, talleres, conferencias, clases, videos, observaciones astronómicas y otras actividades afines.</li><li>Investigación y estudio de la astronomía y ciencias afines.</li><li>Observación astronómica y realización de trabajos de campo y de investigación.</li><li>Popularización de una cultura de observación cotidiana del cielo (fomento a la astronomía de afición).</li><li>Difusión a través de todos los medios de comunicación masiva de sus objetivos, actividades y eventos astronómicos.</li><li>Fomentar el interés y gusto por la astronomía en la población del Estado de Zacatecas, de todas las edades.</li><li>Llevar a cabo observaciones astronómicas periódicas en todo el estado de Zacatecas, principalmente en lugares propicios.</li><li>Utilizar la capacidad profesional de los integrantes para desarrollar y explotar medios de difusión, publicando obras encaminadas a la educación y el entretenimiento astronómico.</li><li>Prestar servicios de asesoría en diferentes áreas relacionadas con su objeto de estudio.</li><li>Fomentar y establecer relaciones y convenios de colaboración con sociedades afines e instituciones de educación e investigación científica, nacionales o internacionales.</li><li>Obtener y otorgar derechos de autor, concesiones y derechos reales y personales para todo tipo de actividades.</li><li>Organizar continuamente eventos de observación astronómica, pequeños y/o masivos, en colaboración con otros organismos.</li><li>Ofrecer programas educativos de formación en astronomía (afición o disciplina científica) desde plataformas de educación informal, no formal o formal.</li></ul>',
     'html', 'Quienes Somos', 'Lista HTML de Objetivos Particulares. Usar TinyMCE para editar.'),
    ('mesa_directiva_periodo', '2024–2026',
     'text', 'Quienes Somos', 'Periodo de la mesa directiva actual. Ej: "2024–2026". Visible en la página Mesa Directiva.');


CREATE TABLE colaboradores (
    id             INT          AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(255) NOT NULL,
    profesion      VARCHAR(255),
    imagen         VARCHAR(500),
    activo         TINYINT(1)   NOT NULL DEFAULT 1,
    orden          SMALLINT     NOT NULL DEFAULT 0,
    creado_en      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_colaboradores_activo ON colaboradores (activo, orden);


CREATE TABLE colaborador_redes (
    id             INT          AUTO_INCREMENT PRIMARY KEY,
    colaborador_id INT          NOT NULL,
    nombre         VARCHAR(100) NOT NULL,
    url            VARCHAR(500) NOT NULL,
    orden          SMALLINT     NOT NULL DEFAULT 0,
    CONSTRAINT fk_red_colaborador
        FOREIGN KEY (colaborador_id) REFERENCES colaboradores(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_colaborador_redes ON colaborador_redes (colaborador_id, orden);


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

CREATE INDEX idx_actividades_activo ON actividades (activo, orden);


CREATE TABLE actividad_items (
    id           INT          AUTO_INCREMENT PRIMARY KEY,
    actividad_id INT          NOT NULL,
    titulo       VARCHAR(255) NOT NULL,
    descripcion  TEXT,
    orden        SMALLINT     NOT NULL DEFAULT 0,
    CONSTRAINT fk_item_actividad
        FOREIGN KEY (actividad_id) REFERENCES actividades(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_actividad_items ON actividad_items (actividad_id, orden);


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

CREATE INDEX idx_actividad_imagenes ON actividad_imagenes (actividad_id, orden);


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

CREATE INDEX idx_observaciones_activo ON observaciones (activo, orden);


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

CREATE INDEX idx_observacion_items ON observacion_items (observacion_id, orden);


CREATE TABLE intereses_suscripcion (
    id     INT          AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    slug   VARCHAR(100) NOT NULL,
    CONSTRAINT uq_interes_nombre UNIQUE (nombre),
    CONSTRAINT uq_interes_slug   UNIQUE (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO intereses_suscripcion (nombre, slug) VALUES
    ('Divulgación',    'divulgacion'),
    ('Astrofotografía','astrofotografia'),
    ('Observación',    'observacion'),
    ('Investigación',  'investigacion'),
    ('Educación',      'educacion');


CREATE TABLE suscriptores (
    id         INT          AUTO_INCREMENT PRIMARY KEY,
    nombre     VARCHAR(100) NOT NULL,
    correo     VARCHAR(254) NOT NULL,
    telefono   VARCHAR(20),
    interes_id INT          NULL,
    mensaje    TEXT,
    activo     TINYINT(1)   NOT NULL DEFAULT 1,
    creado_en  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_suscriptor_correo UNIQUE (correo),
    CONSTRAINT fk_suscriptor_interes
        FOREIGN KEY (interes_id) REFERENCES intereses_suscripcion(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_suscriptores_activo ON suscriptores (activo, creado_en);


CREATE TABLE mensajes_contacto (
    id             INT          AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(100) NOT NULL,
    correo         VARCHAR(254) NOT NULL,
    asunto         VARCHAR(150),
    mensaje        TEXT         NOT NULL,
    leido          TINYINT(1)   NOT NULL DEFAULT 0,
    creado_en      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_mensajes_leido ON mensajes_contacto (leido, creado_en);


CREATE TABLE instituciones (
    id             INT          AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(255) NOT NULL,
    imagen         VARCHAR(500),
    url            VARCHAR(500),
    activo         TINYINT(1)   NOT NULL DEFAULT 1,
    orden          SMALLINT     NOT NULL DEFAULT 0,
    creado_en      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_instituciones_activo ON instituciones (activo, orden);

INSERT INTO instituciones (nombre, imagen, url, orden) VALUES
    ('Universidad Autónoma de Zacatecas',           NULL, 'https://www.uaz.edu.mx/',    1),
    ('Instituto Politécnico Nacional',              NULL, 'https://www.ipn.mx/',        2),
    ('Consejo Zacatecano de Ciencia y Tecnología',  NULL, 'https://cozcyt.gob.mx/',    3);


SET FOREIGN_KEY_CHECKS = 1;
