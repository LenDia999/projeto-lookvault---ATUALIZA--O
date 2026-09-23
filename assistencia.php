<?php
session_start();
$pageTitle = "LOOKVAULT · Assistência";
$pageDescription = "Suporte e ajuda para o LOOKVAULT.";
include 'includes/header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">Assistência</h1>
            <p class="page-subtitle">Estamos aqui para ajudar você</p>
        </div>
    </section>
    <section class="form-section">
        <div class="container">
            <div class="form-wrapper">
                <div style="text-align: center; padding: 40px 0;">
                    <i class="fas fa-headset" style="font-size: 4rem; color: var(--roxo-medio); margin-bottom: 20px;"></i>
                    <h3 style="margin-bottom: 12px;">Central de Suporte</h3>
                    <p style="color: var(--texto-claro); max-width: 500px; margin: 0 auto 30px;">
                        Entre em contato conosco através do e-mail ou pelo formulário abaixo.
                    </p>
                    <a href="mailto:suporte@lookvault.com" class="btn-primary">
                        <i class="fas fa-envelope"></i> suporte@lookvault.com
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>