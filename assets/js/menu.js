document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('.nav-toggle');
  const navMenu = document.querySelector('.main-nav ul');
  const overlay = document.querySelector('.nav-overlay');
  const navLinks = document.querySelectorAll('.main-nav a');

  const toggleMenu = () => {
    navMenu.classList.toggle('show');
    overlay.classList.toggle('show');
  };

  const closeMenu = () => {
    navMenu.classList.remove('show');
    overlay.classList.remove('show');
  };

  toggle.addEventListener('click', toggleMenu);
  overlay.addEventListener('click', closeMenu);
  navLinks.forEach(link => link.addEventListener('click', closeMenu));
});

window.addEventListener('resize', () => {
  const navMenu = document.querySelector('.main-nav ul');
  const overlay = document.querySelector('.nav-overlay');

  if (window.innerWidth > 768) {
    navMenu.classList.remove('show');
    overlay.classList.remove('show');
  }
});