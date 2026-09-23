<?php
session_start();
$pageTitle = "LOOKVAULT · Blog";
$pageDescription = "Dicas e tendências de moda.";
include 'includes/header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">Blog</h1>
            <p class="page-subtitle">Dicas, tendências e inspirações para seu guarda-roupa</p>
        </div>
    </section>
    <section style="padding: 60px 0;">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
                <div style="background: var(--branco); border-radius: 20px; overflow: hidden; border: 1px solid #f0edff; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
                    <div style="height: 200px; background: linear-gradient(135deg, var(--roxo-claro), var(--rosa-claro)); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 3rem;">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <div style="padding: 20px;">
                        <h3>Como organizar seu guarda-roupa por cores</h3>
                        <p style="color: var(--texto-claro); font-size: 0.9rem;">Aprenda a categorizar suas peças por tons e criar combinações incríveis.</p>
                        <a href="#" style="color: var(--roxo-medio); font-weight: 600; display: inline-block; margin-top: 12px;">Leia mais →</a>
                    </div>
                </div>
                <div style="background: var(--branco); border-radius: 20px; overflow: hidden; border: 1px solid #f0edff; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
                    <div style="height: 200px; background: linear-gradient(135deg, var(--azul-claro), var(--roxo-claro)); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 3rem;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div style="padding: 20px;">
                        <h3>O custo por uso: como economizar com moda</h3>
                        <p style="color: var(--texto-claro); font-size: 0.9rem;">Descubra como calcular o valor real de cada peça e investir com inteligência.</p>
                        <a href="#" style="color: var(--roxo-medio); font-weight: 600; display: inline-block; margin-top: 12px;">Leia mais →</a>
                    </div>
                </div>
                <div style="background: var(--branco); border-radius: 20px; overflow: hidden; border: 1px solid #f0edff; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
                    <div style="height: 200px; background: linear-gradient(135deg, var(--rosa-claro), var(--rosa-medio)); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 3rem;">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div style="padding: 20px;">
                        <h3>Tendências de moda para esta estação</h3>
                        <p style="color: var(--texto-claro); font-size: 0.9rem;">Fique por dentro das cores, tecidos e cortes que estão em alta.</p>
                        <a href="#" style="color: var(--roxo-medio); font-weight: 600; display: inline-block; margin-top: 12px;">Leia mais →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>