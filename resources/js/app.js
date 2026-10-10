document.addEventListener('click', (e) => {
    const menu = document.getElementById('user-menu');
    if (menu && !menu.contains(e.target)) menu.classList.remove('open');
});