<?php
session_start();
$pageTitle = "LOOKVAULT · Desenvolvimento";
$pageDescription = "Conheça a equipe por trás do LOOKVAULT.";
include 'includes/header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">Desenvolvimento</h1>
            <p class="page-subtitle">Conheça a equipe por trás do LOOKVAULT</p>
        </div>
    </section>
    <section style="padding: 60px 0;">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; max-width: 900px; margin: 0 auto;">
                <div style="text-align: center; background: var(--cinza-claro); padding: 30px; border-radius: 20px; border: 1px solid #f0edff;">
                    <i class="fas fa-code" style="font-size: 3rem; color: var(--roxo-medio); margin-bottom: 12px;"></i>
                    <h3>Front-end</h3>
                    <p style="color: var(--texto-claro);">Design responsivo e experiência do usuário</p>
                </div>
                <div style="text-align: center; background: var(--cinza-claro); padding: 30px; border-radius: 20px; border: 1px solid #f0edff;">
                    <i class="fas fa-server" style="font-size: 3rem; color: var(--azul-medio); margin-bottom: 12px;"></i>
                    <h3>Back-end</h3>
                    <p style="color: var(--texto-claro);">Sistemas e banco de dados</p>
                </div>
                <div style="text-align: center; background: var(--cinza-claro); padding: 30px; border-radius: 20px; border: 1px solid #f0edff;">
                    <i class="fas fa-paint-brush" style="font-size: 3rem; color: var(--rosa-medio); margin-bottom: 12px;"></i>
                    <h3>UX/UI Design</h3>
                    <p style="color: var(--texto-claro);">Interface e identidade visual</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>