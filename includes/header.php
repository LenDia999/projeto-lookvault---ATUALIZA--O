<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Buscar foto de perfil se estiver logado
$foto_perfil = '';
if (isset($_SESSION['usuario_id'])) {
    require_once 'configdatabase.php';
    $conn = conectarDB();
    $sql = "SELECT foto_perfil FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $foto_perfil = $row['foto_perfil'] ?? '';
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?php echo $_COOKIE['theme'] ?? 'light'; ?>">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $pageTitle ?? 'LOOKVAULT'; ?></title>
    <meta name="description" content="<?php echo $pageDescription ?? 'Guarda-roupa virtual inteligente.'; ?>" />
    
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
                <a href="minha-conta.php" class="btn-nav" style="background: linear-gradient(135deg, var(--roxo-claro), var(--roxo-medio)); display: flex; align-items: center; gap: 6px;">
                    <?php if (!empty($foto_perfil) && file_exists($foto_perfil)): ?>
                        <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Perfil" class="avatar-nav" />
                    <?php else: ?>
                        <span class="avatar-nav-placeholder"><i class="fas fa-user"></i></span>
                    <?php endif; ?>
                    <?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Conta'); ?>
                </a>
            <?php else: ?>
                <a href="login.php" class="btn-nav">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a href="cadastro-usuario.php" class="btn-nav" style="background: linear-gradient(135deg, var(--roxo-claro), var(--roxo-medio));">
                    <i class="fas fa-user-plus"></i> Criar conta
                </a>
            <?php endif; ?>
        </nav>
    </div>
</header>