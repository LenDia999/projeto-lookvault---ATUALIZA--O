// ===== CONTROLE DO MODO ESCURO GLOBAL =====

function toggleTheme() {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    html.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    
    atualizarIconesTema(newTheme);
}

function carregarTema() {
    const savedTheme = localStorage.getItem('theme');
    const html = document.documentElement;
    
    if (savedTheme) {
        html.setAttribute('data-theme', savedTheme);
        atualizarIconesTema(savedTheme);
    } else {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            html.setAttribute('data-theme', 'dark');
            atualizarIconesTema('dark');
        } else {
            html.setAttribute('data-theme', 'light');
            atualizarIconesTema('light');
        }
    }
}

function atualizarIconesTema(theme) {
    const icons = document.querySelectorAll('.theme-icon');
    icons.forEach(icon => {
        if (theme === 'dark') {
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
        } else {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        }
    });
}

document.addEventListener('DOMContentLoaded', carregarTema);

if (window.matchMedia) {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
        if (!localStorage.getItem('theme')) {
            const html = document.documentElement;
            const newTheme = e.matches ? 'dark' : 'light';
            html.setAttribute('data-theme', newTheme);
            atualizarIconesTema(newTheme);
        }
    });
}