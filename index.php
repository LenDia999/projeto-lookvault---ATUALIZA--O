<?php
session_start();
require_once 'configdatabase.php';
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?php echo $_COOKIE['theme'] ?? 'light'; ?>">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LOOKVAULT · Guarda-Roupa Virtual</title>
    <meta name="description" content="Seu guarda-roupa digital com estilo, organização e inteligência." />
    
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>

<header class="header" id="header">
    <div class="container">
        <a href="index.php" class="logo">
            <i class="fas fa-wardrobe"></i> LOOKVAULT
        </a>
        <button class="menu-toggle" id="menuToggle" aria-label="Menu">
            <i class="fas fa-bars"></i>
        </button>
        <nav class="nav" id="nav">
            <a href="index.php">Início</a>
            <a href="guarda-roupa.php">Guarda-Roupa</a>
            <a href="combinacoes.php">Combinações</a>
            <a href="estatisticas.php">Estatísticas</a>
            
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="minha-conta.php" class="btn-nav" style="background: linear-gradient(135deg, var(--roxo-claro), var(--roxo-medio));">
                    <i class="fas fa-user-circle"></i> 
                    <?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Conta'); ?>
                </a>
            <?php else: ?>
                <a href="login.php" class="btn-nav">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main>
    <section class="hero">
        <div class="container">
            <div class="hero-text">
                <h1>Seu estilo,<br />em um só lugar.</h1>
                <div class="sub">✦ Deu guarda-roupa na virtual</div>
                <p>
                    Cadastre suas peças, organize por cor, marca ou tags personalizadas,
                    e tenha tudo ao alcance de um toque. Crie looks, planeje produções
                    e acompanhe o custo por uso de cada item.
                </p>
                <div class="hero-buttons">
                    <a href="cadastro.php" class="btn-primary">
                        <i class="fas fa-plus-circle"></i> Criar meu guarda-roupa
                    </a>
                    <a href="demo.php" class="btn-secondary">
                        <i class="fas fa-eye"></i> Ver demonstração
                    </a>
                </div>
            </div>
            <div class="hero-image">
                <div class="mockup">
                    <div class="mockup-inner">
                        <div class="mockup-row">
                            <span class="mockup-tag"><i class="fas fa-tag"></i> 12 peças</span>
                            <div class="mockup-color-dots">
                                <span class="dot purple"></span>
                                <span class="dot pink"></span>
                                <span class="dot blue"></span>
                                <span class="dot beige"></span>
                            </div>
                        </div>
                        <div class="mockup-items">
                            <div class="mockup-item">
                                <i class="fas fa-tshirt"></i> Camisas
                            </div>
                            <div class="mockup-item">
                                <i class="fas fa-shoe-prints"></i> Sapatos
                            </div>
                            <div class="mockup-item">
                                <i class="fas fa-hat-cowboy"></i> Acessórios
                            </div>
                            <div class="mockup-item">
                                <i class="fas fa-user-tie"></i> Blazers
                            </div>
                            <div class="mockup-item">
                                <i class="fas fa-ring"></i> Joias
                            </div>
                            <div class="mockup-item">
                                <i class="fas fa-shopping-bag"></i> Bolsas
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="brands">
        <div class="container">
            <span>MARCAS EM DESTAQUE</span>
            <div class="brand-carousel">
                <div class="brand-track">
                    <span class="brand">DIOR</span>
                    <span class="brand">GUCCI</span>
                    <span class="brand">CHANEL</span>
                    <span class="brand">PRADA</span>
                    <span class="brand">FENDI</span>
                    <span class="brand">LOUIS VUITTON</span>
                    <span class="brand">BALENCIAGA</span>
                    <span class="brand">VERSACE</span>
                    <span class="brand">ARMANI</span>
                    <span class="brand">DOLCE &amp; GABBANA</span>
                    <span class="brand">BURBERRY</span>
                    <span class="brand">RALPH LAUREN</span>
                    <span class="brand">HERMÈS</span>
                    <span class="brand">CELINE</span>
                    <span class="brand">OFF-WHITE</span>
                    <span class="brand">SAINT LAURENT</span>
                    <span class="brand">GIVENCHY</span>
                    <span class="brand">DIOR</span>
                    <span class="brand">GUCCI</span>
                    <span class="brand">CHANEL</span>
                    <span class="brand">PRADA</span>
                    <span class="brand">FENDI</span>
                    <span class="brand">LOUIS VUITTON</span>
                    <span class="brand">BALENCIAGA</span>
                    <span class="brand">VERSACE</span>
                    <span class="brand">ARMANI</span>
                    <span class="brand">DOLCE &amp; GABBANA</span>
                    <span class="brand">BURBERRY</span>
                    <span class="brand">RALPH LAUREN</span>
                    <span class="brand">HERMÈS</span>
                    <span class="brand">CELINE</span>
                    <span class="brand">OFF-WHITE</span>
                    <span class="brand">SAINT LAURENT</span>
                    <span class="brand">GIVENCHY</span>
                </div>
            </div>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <h2 class="section-title">Crie seu próprio estilo</h2>
            <p class="section-sub">
                Organize, combine e valorize cada peça do seu guarda-roupa com ferramentas
                inteligentes e uma interface feita para inspirar.
            </p>
            <div class="grid-features">
                <div class="card-feature">
                    <i class="fas fa-layer-group"></i>
                    <h3>Organização total</h3>
                    <p>Cadastre peças por categoria, cor, marca e tags personalizadas. Tudo ao seu alcance.</p>
                </div>
                <div class="card-feature">
                    <i class="fas fa-people-arrows"></i>
                    <h3>Looks & combinações</h3>
                    <p>Crie combinações, monte looks completos e planeje produções para qualquer ocasião.</p>
                </div>
                <div class="card-feature">
                    <i class="fas fa-chart-line"></i>
                    <h3>Estatísticas inteligentes</h3>
                    <p>Acompanhe a frequência de uso, custo por uso e descubra o valor real de cada peça.</p>
                </div>
                <div class="card-feature">
                    <i class="fas fa-search"></i>
                    <h3>Inspiração & filtros</h3>
                    <p>Pesquise, filtre, encontre inspirações e revisite looks que você já criou com facilidade.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <h4>1.2k+</h4>
                    <p>Peças cadastradas</p>
                </div>
                <div class="stat-item">
                    <h4>340+</h4>
                    <p>Looks criados</p>
                </div>
                <div class="stat-item">
                    <h4>87%</h4>
                    <p>Redução de compras por impulso</p>
                </div>
                <div class="stat-item">
                    <h4>4.8★</h4>
                    <p>Avaliação dos usuários</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>