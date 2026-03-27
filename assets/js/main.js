const yearEl = document.getElementById("currentYear");
const scrollTopBtn = document.getElementById("scrollTopBtn");

if (yearEl) {
  yearEl.textContent = new Date().getFullYear();
}

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
