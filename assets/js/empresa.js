const navToggle = document.querySelector('.nav-toggle-empresa');
const menu = document.querySelector('.menu-principal');
const overlay = document.querySelector('.nav-overlay-empresa');

navToggle.addEventListener('click', () => {
  menu.classList.toggle('show');
  overlay.classList.toggle('show');
});

overlay.addEventListener('click', () => {
  menu.classList.remove('show');
  overlay.classList.remove('show');
});