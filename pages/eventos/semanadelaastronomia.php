<?php
/**
 * pages/eventos/semanadelaastronomia.php
 * Página específica para el evento de la Semana de la Astronomía.
 */

$pageTitle = 'Semana de la Astronomía — SAZ';
$pageDescription = 'Celebramos 15 años de pasión por el cosmos con el evento de divulgación científica más importante de la región. Una semana intensiva de conferencias, talleres y observación.';
$basePath = '../../';
$eventoSlug = 'semana-astronomia';

ob_start();

include __DIR__ . '/../../templates/evento.php';


?>



    <section class="py-5" aria-labelledby="titulo-programa">
        <div class="container">
            <h2 id="titulo-programa" class="section-title mb-2">Programa de Actividades — XV Aniversario</h2>
            <p class="text-muted mb-4">Despliega cada día para consultar el horario completo. Haz clic en el título de cualquier actividad para ver más detalles.</p>

            <div class="accordion accordion-programa" id="acordeonPrograma">

            <!-- ═══════════════════════════════════════════════════════
                 MARTES 21 DE ABRIL
                 ═══════════════════════════════════════════════════════ -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                <button class="accordion-button" type="button"
                        data-bs-toggle="collapse" data-bs-target="#dia-martes"
                        aria-expanded="true" aria-controls="dia-martes">
                    <span class="programa-dia-label">
                        <i class="bi bi-calendar-week me-2"></i>Martes 21 de abril
                        <span class="badge bg-accent ms-2">10 actividades</span>
                    </span>
                </button>
                </h2>
                <div id="dia-martes" class="accordion-collapse collapse show" data-bs-parent="#acordeonPrograma">
                <div class="accordion-body p-0">
                <div class="table-responsive">
                <table class="table table-programa align-middle mb-0">
                    <thead>
                    <tr>
                        <th><i class="bi bi-mortarboard me-1"></i>Actividad</th>
                        <th><i class="bi bi-person me-1"></i>Ponente / Responsable</th>
                        <th><i class="bi bi-geo-alt me-1"></i>Lugar</th>
                        <th><i class="bi bi-clock me-1"></i>Horario</th>
                    </tr>
                    </thead>
                    <tbody>

                    <tr>
                        <td><a href="link (Habilitación de la Exposición de Astrofotografía)" class="link-accent">Habilitación de la Exposición de Astrofotografía</a></td>
                        <td>—</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>LUMAT</span></td>
                        <td><span class="text-nowrap">09:00</span></td>
                    </tr>
                    <tr>
                        <td><a href="link (Inauguración)" class="link-accent">Inauguración</a></td>
                        <td>—</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>LUMAT</span></td>
                        <td><span class="text-nowrap">09:30 – 10:00</span></td>
                    </tr>
                    <tr>
                        <td><a href="link (Semblanza de la SAZ)" class="link-accent">Semblanza de la SAZ</a></td>
                        <td>—</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>LUMAT</span></td>
                        <td><span class="text-nowrap">10:00 – 10:30</span></td>
                    </tr>
                    <tr class="table-programa-break">
                        <td colspan="4" class="text-center"><i class="bi bi-cup-hot me-1"></i>Coffee break — 10:30 – 11:00</td>
                    </tr>
                    <tr>
                        <td><a href="link (Presentación oficial y recorrido Exposición Astrofotográfica)" class="link-accent">Presentación oficial y recorrido Exposición Astrofotográfica</a></td>
                        <td>Berenice Gómez · Daniel Coronado</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>LUMAT</span></td>
                        <td><span class="text-nowrap">11:00 – 12:00</span></td>
                    </tr>
                    <tr>
                        <td><a href="link (Conferencia Magistral ngVLA)" class="link-accent">Conferencia Magistral: México en la construcción del radiotelescopio más potente del mundo: ngVLA</a></td>
                        <td>Dr. Luis Alberto Zapata González</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>LUMAT</span></td>
                        <td><span class="text-nowrap">12:00 – 13:00</span></td>
                    </tr>

                    <tr>
                        <td><a href="link (Conferencia Magistral Aprendiendo astronomía desde México)" class="link-accent">Conferencia Magistral: Aprendiendo astronomía desde México</a></td>
                        <td>Dr. Ary Rodríguez González</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>Estudios Nucleares</span></td>
                        <td><span class="text-nowrap">16:00 – 17:00</span></td>
                    </tr>
                    <tr>
                        <td><a href="link (Mesa Redonda 1)" class="link-accent">Mesa Redonda 1: La astronomía mexicana en el contexto internacional</a></td>
                        <td>Rocío Ortiz</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>Estudios Nucleares</span></td>
                        <td><span class="text-nowrap">17:00 – 19:00</span></td>
                    </tr>

                    <tr>
                        <td><a href="link (Observación Astronómica Martes)" class="link-accent"><i class="bi bi-telescope me-1"></i>Observación Astronómica</a></td>
                        <td>Asesor: Jonathan Lechuga Bonilla</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>Plaza Bicentenario</span></td>
                        <td><span class="text-nowrap">19:00 – 21:30</span></td>
                    </tr>
                    </tbody>
                </table>
                </div>
                </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════
                 MIÉRCOLES 22 DE ABRIL
                 ═══════════════════════════════════════════════════════ -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button"
                        data-bs-toggle="collapse" data-bs-target="#dia-miercoles"
                        aria-expanded="false" aria-controls="dia-miercoles">
                    <span class="programa-dia-label">
                        <i class="bi bi-calendar-week me-2"></i>Miércoles 22 de abril
                        <span class="badge bg-accent ms-2">8 actividades</span>
                    </span>
                </button>
                </h2>
                <div id="dia-miercoles" class="accordion-collapse collapse" data-bs-parent="#acordeonPrograma">
                <div class="accordion-body p-0">
                <div class="table-responsive">
                <table class="table table-programa align-middle mb-0">
                    <thead>
                    <tr>
                        <th><i class="bi bi-mortarboard me-1"></i>Actividad</th>
                        <th><i class="bi bi-person me-1"></i>Ponente / Responsable</th>
                        <th><i class="bi bi-geo-alt me-1"></i>Lugar</th>
                        <th><i class="bi bi-clock me-1"></i>Horario</th>
                    </tr>
                    </thead>
                    <tbody>

                    <tr>
                        <td><a href="link (Habilitación de Exposición de Meteoritos)" class="link-accent">Habilitación de Exposición de Meteoritos</a></td>
                        <td>—</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>Estudios del Desarrollo</span></td>
                        <td><span class="text-nowrap">09:00</span></td>
                    </tr>
                    <tr>
                        <td><a href="link (Conferencia Magistral Monitoreo de asteroides)" class="link-accent">Conferencia Magistral: Monitoreo de asteroides y Objetos Cercanos a la Tierra con los telescopios del INAOE</a></td>
                        <td>Dr. José Ramón Valdés</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>Estudios del Desarrollo</span></td>
                        <td><span class="text-nowrap">09:30 – 10:30</span></td>
                    </tr>
                    <tr class="table-programa-break">
                        <td colspan="4" class="text-center"><i class="bi bi-cup-hot me-1"></i>Coffee break — 10:30 – 11:00</td>
                    </tr>
                    <tr>
                        <td><a href="link (Presentación oficial y recorrido de la Exposición de Meteoritos)" class="link-accent">Presentación oficial y recorrido de la Exposición de Meteoritos</a></td>
                        <td>Dr. Sergio Huanado Álvarez</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>Estudios del Desarrollo</span></td>
                        <td><span class="text-nowrap">11:00 – 11:45</span></td>
                    </tr>
                    <tr>
                        <td><a href="link (Conferencia Magistral Semblanza de los meteoritos mexicanos)" class="link-accent">Conferencia Magistral: Semblanza de los meteoritos mexicanos</a></td>
                        <td>Dr. Sergio Huanaco Álvarez</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>Estudios del Desarrollo</span></td>
                        <td><span class="text-nowrap">11:45 – 12:45</span></td>
                    </tr>
                    <tr>
                        <td><a href="link (Plática corta Fragmento del cometa Biela de 1885)" class="link-accent">Plática corta: Fragmento del cometa Biela de 1885</a></td>
                        <td>M.C. Ciro Robles Berumen</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>Estudios del Desarrollo</span></td>
                        <td><span class="text-nowrap">12:45 – 13:00</span></td>
                    </tr>

                    <tr>
                        <td><a href="link (Conferencia Magistral Dra. Rosa Becerra Godínez)" class="link-accent">Conferencia Magistral</a></td>
                        <td>Dra. Rosa Becerra Godínez</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>Estudios del Desarrollo</span></td>
                        <td><span class="text-nowrap">16:00 – 17:00</span></td>
                    </tr>
                    <tr>
                        <td><a href="link (Mesa Redonda 2)" class="link-accent">Mesa Redonda 2: El estado de la divulgación científica en Zacatecas</a></td>
                        <td>Abigail Flores Márquez · Nancy Sánchez Aguilar</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>Estudios del Desarrollo</span></td>
                        <td><span class="text-nowrap">17:00 – 19:00</span></td>
                    </tr>
                    </tbody>
                </table>
                </div>
                </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════
                 JUEVES 23 DE ABRIL
                 ═══════════════════════════════════════════════════════ -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button"
                        data-bs-toggle="collapse" data-bs-target="#dia-jueves"
                        aria-expanded="false" aria-controls="dia-jueves">
                    <span class="programa-dia-label">
                        <i class="bi bi-calendar-week me-2"></i>Jueves 23 de abril
                        <span class="badge bg-accent ms-2">7 actividades</span>
                    </span>
                </button>
                </h2>
                <div id="dia-jueves" class="accordion-collapse collapse" data-bs-parent="#acordeonPrograma">
                <div class="accordion-body p-0">
                <div class="table-responsive">
                <table class="table table-programa align-middle mb-0">
                    <thead>
                    <tr>
                        <th><i class="bi bi-mortarboard me-1"></i>Actividad</th>
                        <th><i class="bi bi-person me-1"></i>Ponente / Responsable</th>
                        <th><i class="bi bi-geo-alt me-1"></i>Lugar</th>
                        <th><i class="bi bi-clock me-1"></i>Horario</th>
                    </tr>
                    </thead>
                    <tbody>

                    <tr>
                        <td><a href="link (Conferencia Magistral Cabo TUNA)" class="link-accent">Conferencia Magistral: Cabo TUNA — La aventura espacial mexicana interrumpida</a></td>
                        <td>Dr. Refugio Martínez Mendoza (Flash)</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>Estudios Nucleares</span></td>
                        <td><span class="text-nowrap">09:30 – 10:30</span></td>
                    </tr>
                    <tr class="table-programa-break">
                        <td colspan="4" class="text-center"><i class="bi bi-cup-hot me-1"></i>Coffee break — 10:30 – 11:00</td>
                    </tr>
                    <tr>
                        <td><a href="link (Conferencia Magistral Dr. Omar López Cruz)" class="link-accent">Conferencia Magistral</a></td>
                        <td>Dr. Omar López Cruz</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>Estudios Nucleares</span></td>
                        <td><span class="text-nowrap">11:00 – 12:00</span></td>
                    </tr>
                    <tr>
                        <td><a href="link (Presentación del libro de la SAZ)" class="link-accent">Presentación del libro de la SAZ y Guía de Olimpiadas de Astronomía</a></td>
                        <td>—</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>Estudios Nucleares</span></td>
                        <td><span class="text-nowrap">12:00 – 13:15</span></td>
                    </tr>
                    <tr class="table-programa-break">
                        <td colspan="4" class="text-center"><i class="bi bi-pause-circle me-1"></i>Receso</td>
                    </tr>

                    <tr>
                        <td><a href="link (Talleres Dr. Juan Martínez Ortiz)" class="link-accent">Talleres</a></td>
                        <td>Dr. Juan Martínez Ortiz</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>U.A. de Matemáticas</span></td>
                        <td><span class="text-nowrap">16:00 – 18:00</span></td>
                    </tr>

                    <tr>
                        <td><a href="link (Conferencia Magistral Dr. Miguel Jaquez Domínguez)" class="link-accent">Conferencia Magistral</a></td>
                        <td>Dr. Miguel Jaquez Domínguez</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>Patio Rectoría UAZ</span></td>
                        <td><span class="text-nowrap">16:00 – 17:00</span></td>
                    </tr>
                    <tr>
                        <td><a href="link (Presentación del artículo Origen e Impulso de la Cosmología Física en México)" class="link-accent">Presentación del artículo: Origen e Impulso de la Cosmología Física en México</a></td>
                        <td>Dr. Alejandro González Sánchez</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>Patio Rectoría UAZ</span></td>
                        <td><span class="text-nowrap">17:00 – 18:00</span></td>
                    </tr>
                    </tbody>
                </table>
                </div>
                </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════
                 VIERNES 24 DE ABRIL
                 ═══════════════════════════════════════════════════════ -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button"
                        data-bs-toggle="collapse" data-bs-target="#dia-viernes"
                        aria-expanded="false" aria-controls="dia-viernes">
                    <span class="programa-dia-label">
                        <i class="bi bi-calendar-week me-2"></i>Viernes 24 de abril
                        <span class="badge bg-accent ms-2">6 actividades</span>
                    </span>
                </button>
                </h2>
                <div id="dia-viernes" class="accordion-collapse collapse" data-bs-parent="#acordeonPrograma">
                <div class="accordion-body p-0">
                <div class="table-responsive">
                <table class="table table-programa align-middle mb-0">
                    <thead>
                    <tr>
                        <th><i class="bi bi-mortarboard me-1"></i>Actividad</th>
                        <th><i class="bi bi-person me-1"></i>Ponente / Responsable</th>
                        <th><i class="bi bi-geo-alt me-1"></i>Lugar</th>
                        <th><i class="bi bi-clock me-1"></i>Horario</th>
                    </tr>
                    </thead>
                    <tbody>

                    <tr>
                        <td><a href="link (Conferencia Magistral Dr. Pedro Rivera Ortiz)" class="link-accent">Conferencia Magistral</a></td>
                        <td>Dr. Pedro Rivera Ortiz</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>Preparatoria Plantel 4</span></td>
                        <td><span class="text-nowrap">09:30 – 10:30</span></td>
                    </tr>
                    <tr class="table-programa-break">
                        <td colspan="4" class="text-center"><i class="bi bi-cup-hot me-1"></i>Coffee break — 10:30 – 11:00</td>
                    </tr>
                    <tr>
                        <td><a href="link (Conferencia Magistral Dra. Liliana Hernández Martínez)" class="link-accent">Conferencia Magistral</a></td>
                        <td>Dra. Liliana Hernández Martínez</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>Preparatoria Plantel 4</span></td>
                        <td><span class="text-nowrap">11:00 – 12:00</span></td>
                    </tr>
                    <tr>
                        <td><a href="link (Mesa Redonda 3)" class="link-accent">Mesa Redonda 3: Los astrónomos de Zacatecas</a></td>
                        <td>Berenice Gómez</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>Preparatoria Plantel 4</span></td>
                        <td><span class="text-nowrap">12:00 – 14:00</span></td>
                    </tr>

                    <tr>
                        <td><a href="link (Conferencia Magistral Dr. Erik Aquino Ortiz)" class="link-accent">Conferencia Magistral</a></td>
                        <td>Dr. Erik Aquino Ortiz</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>Patio Rectoría UAZ</span></td>
                        <td><span class="text-nowrap">16:00 – 17:00</span></td>
                    </tr>

                    <tr>
                        <td><a href="link (Talleres y Observación Astronómica Viernes)" class="link-accent"><i class="bi bi-telescope me-1"></i>Talleres y Observación Astronómica</a></td>
                        <td>Dr. Juan Martínez Ortiz · M.C. Iván Santamaría Najar</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>U.A. de Matemáticas</span></td>
                        <td><span class="text-nowrap">17:30 – 20:30</span></td>
                    </tr>
                    </tbody>
                </table>
                </div>
                </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════
                 SÁBADO 25 DE ABRIL
                 ═══════════════════════════════════════════════════════ -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button"
                        data-bs-toggle="collapse" data-bs-target="#dia-sabado"
                        aria-expanded="false" aria-controls="dia-sabado">
                    <span class="programa-dia-label">
                        <i class="bi bi-calendar-week me-2"></i>Sábado 25 de abril
                        <span class="badge bg-accent ms-2">6 actividades</span>
                    </span>
                </button>
                </h2>
                <div id="dia-sabado" class="accordion-collapse collapse" data-bs-parent="#acordeonPrograma">
                <div class="accordion-body p-0">
                <div class="table-responsive">
                <table class="table table-programa align-middle mb-0">
                    <thead>
                    <tr>
                        <th><i class="bi bi-mortarboard me-1"></i>Actividad</th>
                        <th><i class="bi bi-person me-1"></i>Ponente / Responsable</th>
                        <th><i class="bi bi-geo-alt me-1"></i>Lugar</th>
                        <th><i class="bi bi-clock me-1"></i>Horario</th>
                    </tr>
                    </thead>
                    <tbody>

                    <tr>
                        <td><a href="link (Habilitación de Exposición y Taller de Fractales)" class="link-accent">Habilitación de Exposición y Taller de Fractales</a></td>
                        <td>Dr. Plácido Hernández Sánchez</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>MIIMAZ</span></td>
                        <td><span class="text-nowrap">09:30</span></td>
                    </tr>
                    <tr>
                        <td><a href="link (Conferencia Magistral Dra. Adriana Gazol Patiño)" class="link-accent">Conferencia Magistral</a></td>
                        <td>Dra. Adriana Gazol Patiño</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>MIIMAZ</span></td>
                        <td><span class="text-nowrap">10:00 – 11:00</span></td>
                    </tr>
                    <tr class="table-programa-break">
                        <td colspan="4" class="text-center"><i class="bi bi-cup-hot me-1"></i>Coffee break y corte de pastel — 11:00 – 11:45</td>
                    </tr>
                    <tr>
                        <td><a href="link (Conferencia Magistral Constelaciones zodiacales)" class="link-accent">Conferencia Magistral: Centro en Sagitario, con ascendiente en Virgo — Las constelaciones zodiacales...</a></td>
                        <td>Dr. César Caretta</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>MIIMAZ</span></td>
                        <td><span class="text-nowrap">11:45 – 12:45</span></td>
                    </tr>
                    <tr>
                        <td><a href="link (Mesa Redonda 4)" class="link-accent">Mesa Redonda 4: Futuro y perspectivas de la SAZ</a></td>
                        <td>Rocío Ortiz</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>MIIMAZ</span></td>
                        <td><span class="text-nowrap">12:45 – 13:45</span></td>
                    </tr>
                    <tr>
                        <td><a href="link (Clausura)" class="link-accent"><i class="bi bi-camera me-1"></i>Clausura, fotografía de participantes y regalo sorpresa </a></td>
                        <td>—</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building me-1"></i>MIIMAZ</span></td>
                        <td><span class="text-nowrap">13:45 – 14:00</span></td>
                    </tr>
                    </tbody>
                </table>
                </div>
                </div>
                </div>
            </div>

            </div><!-- /.accordion -->
        </div>
    </section>


<?php

$content = ob_get_clean();

include __DIR__ . '/../../base.php';
?>


