/**
 * assets/js/main.js
 * JavaScript principal — Sociedad Astronómica de Zacatecas
 *
 * Funcionalidades:
 *   1. Año dinámico en el footer
 *   2. Botón scroll-to-top
 *   3. Toggle modo claro/oscuro con persistencia en localStorage
 */

// ── 1. Año dinámico ──
const yearEl = document.getElementById("currentYear");
if (yearEl) {
  yearEl.textContent = new Date().getFullYear();
}

// ── 2. Botón scroll-to-top ──
const scrollTopBtn = document.getElementById("scrollTopBtn");

function toggleScrollButton() {
  if (!scrollTopBtn) return;
  if (window.scrollY > 360) {
    scrollTopBtn.classList.add("show");
  } else {
    scrollTopBtn.classList.remove("show");
  }
}

window.addEventListener("scroll", toggleScrollButton);
toggleScrollButton();

if (scrollTopBtn) {
  scrollTopBtn.addEventListener("click", () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  });
}

// ── 3. Modo claro / oscuro ──
const themeToggle = document.getElementById("themeToggle");
const themeIcon   = document.getElementById("themeIcon");
const htmlEl      = document.documentElement;

/**
 * Aplica el tema indicado ('light' o 'dark') y actualiza el ícono.
 * @param {string} tema - 'light' o 'dark'
 */
function aplicarTema(tema) {
  htmlEl.setAttribute("data-bs-theme", tema);
  if (themeIcon) {
    if (tema === "dark") {
      themeIcon.classList.remove("bi-sun-fill");
      themeIcon.classList.add("bi-moon-fill");
    } else {
      themeIcon.classList.remove("bi-moon-fill");
      themeIcon.classList.add("bi-sun-fill");
    }
  }
}

// Cargar preferencia guardada o usar 'light' por defecto
const temaGuardado = localStorage.getItem("saz-tema") || "light";
aplicarTema(temaGuardado);

// Listener del toggle
if (themeToggle) {
  themeToggle.addEventListener("click", () => {
    const temaActual = htmlEl.getAttribute("data-bs-theme");
    const nuevoTema  = temaActual === "dark" ? "light" : "dark";
    aplicarTema(nuevoTema);
    localStorage.setItem("saz-tema", nuevoTema);
  });
}
