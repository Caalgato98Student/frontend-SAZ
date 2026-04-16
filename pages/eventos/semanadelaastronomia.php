<?php
/**
 * pages/eventos/semanadelaastronomia.php
 * Página específica para el evento de la Semana de la Astronomía.
 */

$pageTitle       = 'Semana de la Astronomía — SAZ';
$pageDescription = 'Celebramos 15 años de pasión por el cosmos con el evento de divulgación científica más importante de la región. Una semana intensiva de conferencias, talleres y observación.';
$basePath        = '../../';
$eventoSlug      = 'semana-astronomia';

ob_start();

include __DIR__ . '/../../templates/evento.php'; 

?>



    <section class="py-5" aria-labelledby="titulo-programa">
        <div class="container">
            <h2 id="titulo-programa" class="section-title mb-4">Programa de Actividades — XV Aniversario</h2>
            <p class="mb-4">Haz clic en el título de cualquier actividad para ver los detalles completos, semblanza del ponente y contenido de la charla.</p>

            <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-dark">
                <tr>
                    <th scope="col">Actividad (Título)</th>
                    <th scope="col">Ponente</th>
                    <th scope="col">Lugar</th>
                    <th scope="col">Fecha y Horario</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><a href="https://www.facebook.com/share/p/1APMfBFLwp/" class="link-accent">México en la construcción del radiotelescopio más potente del mundo: ngVLA</a></td>
                    <td>Dr. Luis Alberto Zapata González</td>
                    <td>LUMAT</td>
                    <td>Martes 21 | 12:00 - 13:00</td>
                </tr>
                <tr>
                    <td><a href="actividad.php?id=aprendiendo-astronomia" class="link-accent">Aprendiendo astronomía desde México</a></td>
                    <td>Dr. Ary Rodríguez González</td>
                    <td>Estudios Nucleares</td>
                    <td>Martes 21 | 16:00 - 17:00</td>
                </tr>

                <tr>
                    <td><a href="actividad.php?id=monitoreo-asteroides" class="link-accent">Monitoreo de asteroides y Objetos Cercanos a la Tierra con los telescopios del INAOE</a></td>
                    <td>Dr. José Ramón Valdés</td>
                    <td>Estudios del Desarrollo</td>
                    <td>Miércoles 22 | 09:30 - 10:30</td>
                </tr>
                <tr>
                    <td><a href="actividad.php?id=meteoritos-mexicanos" class="link-accent">Semblanza de los meteoritos mexicanos</a></td>
                    <td>Dr. Sergio Huanaco Álvarez</td>
                    <td>Estudios del Desarrollo</td>
                    <td>Miércoles 22 | 11:45 - 12:45</td>
                </tr>

                <tr>
                    <td><a href="actividad.php?id=cabo-tuna" class="link-accent">Cabo TUNA: La aventura espacial mexicana interrumpida</a></td>
                    <td>Dr. Refugio Martínez Mendoza (Flash)</td>
                    <td>Estudios Nucleares</td>
                    <td>Jueves 23 | 09:30 - 10:30</td>
                </tr>

                <tr>
                    <td><a href="actividad.php?id=constelaciones-zodiacales" class="link-accent">Centro en Sagitario, con ascendiente en Virgo: las constelaciones zodiacales...</a></td>
                    <td>Dr. César Caretta</td>
                    <td>MIIMAZ</td>
                    <td>Sábado 25 | 11:45 - 12:45</td>
                </tr>
                </tbody>
            </table>
            </div>
        </div>
    </section>

<?php

$content = ob_get_clean();

include __DIR__ . '/../../base.php';
?>


