<?php
session_start();
$pageTitle = "LOOKVAULT · Demonstração";
$pageDescription = "Veja como funciona o guarda-roupa virtual LOOKVAULT.";
include 'includes/header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">Demonstração do LOOKVAULT</h1>
            <p class="page-subtitle">Veja como é fácil organizar seu guarda-roupa digital</p>
        </div>
    </section>

    <section class="demo-section">
        <div class="container">
            <div class="demo-video">
                <div class="video-placeholder">
                    <i class="fas fa-play-circle"></i>
                    <p>Assista ao vídeo de demonstração</p>
                </div>
            </div>

            <div class="demo-features">
                <h3>Funcionalidades em Destaque</h3>
                <div class="demo-grid">
                    <div class="demo-card">
                        <i class="fas fa-plus-circle"></i>
                        <h4>Cadastro Rápido</h4>
                        <p>Adicione peças em segundos com informações completas</p>
                    </div>
                    <div class="demo-card">
                        <i class="fas fa-search"></i>
                        <h4>Busca Inteligente</h4>
                        <p>Encontre qualquer peça por nome, cor, marca ou categoria</p>
                    </div>
                    <div class="demo-card">
                        <i class="fas fa-random"></i>
                        <h4>Combinações Criativas</h4>
                        <p>Crie looks incríveis com recomendações inteligentes</p>
                    </div>
                    <div class="demo-card">
                        <i class="fas fa-chart-line"></i>
                        <h4>Análise de Uso</h4>
                        <p>Descubra quais peças você mais usa e otimize seu guarda-roupa</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>