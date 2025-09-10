// Navegación a módulos
function navigateToModule(url) {
  window.location.href = url;
}

// Mostrar modal de instructivo
function showInstructivo() {
  const modal = new bootstrap.Modal(document.getElementById('instructivoModal'));
  modal.show();
}

// Carrusel de líneas informativas
let currentInfoLine = 0;
const infoLines = document.querySelectorAll('.info-line');
const carouselDots = document.querySelectorAll('.carousel-dot');

function showInfoLine(index) {
  // Ocultar línea actual
  infoLines[currentInfoLine].classList.remove('active');
  carouselDots[currentInfoLine].classList.remove('active');
  
  // Mostrar nueva línea
  currentInfoLine = index;
  infoLines[currentInfoLine].classList.add('active');
  carouselDots[currentInfoLine].classList.add('active');
}

// Auto-rotación del carrusel
setInterval(() => {
  const nextIndex = (currentInfoLine + 1) % infoLines.length;
  showInfoLine(nextIndex);
}, 5000);

// Animaciones de entrada
document.addEventListener('DOMContentLoaded', function() {
  // Animar indicadores
  const indicators = document.querySelectorAll('.indicator-card');
  indicators.forEach((indicator, index) => {
    setTimeout(() => {
      indicator.style.opacity = '0';
      indicator.style.transform = 'translateY(20px)';
      indicator.style.transition = 'all 0.5s ease';
      
      setTimeout(() => {
        indicator.style.opacity = '1';
        indicator.style.transform = 'translateY(0)';
      }, 100);
    }, index * 100);
  });
  
  // Animar módulos
  const modules = document.querySelectorAll('.module-card');
  modules.forEach((module, index) => {
    setTimeout(() => {
      module.style.opacity = '0';
      module.style.transform = 'translateY(20px)';
      module.style.transition = 'all 0.5s ease';
      
      setTimeout(() => {
        module.style.opacity = '1';
        module.style.transform = 'translateY(0)';
      }, 100);
    }, (index * 100) + 500);
  });
});