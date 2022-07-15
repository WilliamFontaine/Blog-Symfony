const toggler = document.querySelector('.hamburger');
const navLinksContainer = document.querySelector('.navlinks-container');

const toggleNav = () => {
  toggler.classList.toggle('open');

  navLinksContainer.classList.toggle('open');
}

toggler.addEventListener('click', toggleNav);

new ResizeObserver(entries => {
  if (entries[0].contentRect.width <=900) {
    navLinksContainer.style.transition = "transform 0.3s ease-out";
  }
  else {
    navLinksContainer.style.transition = "none";
  }
}).observe(document.body);