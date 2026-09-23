<?php
session_start();
require_once 'configdatabase.php';

// Verificar se está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$conn = conectarDB();

// Buscar dados do usuário
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

// Buscar estatísticas do usuário
$sql = "SELECT COUNT(*) as total_pecas FROM pecas WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$total_pecas = $result->fetch_assoc()['total_pecas'] ?? 0;
$stmt->close();

$sql = "SELECT COUNT(*) as total_looks FROM looks WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$total_looks = $result->fetch_assoc()['total_looks'] ?? 0;
$stmt->close();

$conn->close();

// Processar atualizações
$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Atualizar perfil (nome e email)
    if (isset($_POST['atualizar_perfil'])) {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        if (empty($nome) || empty($email)) {
            $erro = 'Preencha todos os campos.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro = 'E-mail inválido.';
        } else {
            $conn = conectarDB();
            
            // Verificar se email já existe para outro usuário
            $sql = "SELECT id FROM usuarios WHERE email = ? AND id != ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $email, $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $erro = 'Este e-mail já está em uso.';
            } else {
                $sql = "UPDATE usuarios SET nome = ?, email = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssi", $nome, $email, $usuario_id);
                
                if ($stmt->execute()) {
                    $_SESSION['usuario_nome'] = $nome;
                    $_SESSION['usuario_email'] = $email;
                    $usuario['nome'] = $nome;
                    $usuario['email'] = $email;
                    $mensagem = 'Perfil atualizado com sucesso!';
                } else {
                    $erro = 'Erro ao atualizar perfil.';
                }
            }
            $stmt->close();
            $conn->close();
        }
    }
    
    // Atualizar foto de perfil
if (isset($_POST['atualizar_foto'])) {
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/perfil/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $ext = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
        $nome_arquivo = 'perfil_' . $usuario_id . '_' . time() . '.' . $ext;  // <--- CORRIGIDO: $nome_arquivo (sem "n" no final)
        $caminho_arquivo = $upload_dir . $nome_arquivo;  // <--- CORRIGIDO: usa $nome_arquivo
        
        if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $caminho_arquivo)) {
            $conn = conectarDB();
            $sql = "UPDATE usuarios SET foto_perfil = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $caminho_arquivo, $usuario_id);
            
            if ($stmt->execute()) {
                $usuario['foto_perfil'] = $caminho_arquivo;
                $mensagem = 'Foto de perfil atualizada!';
            } else {
                $erro = 'Erro ao salvar foto.';
            }
            $stmt->close();
            $conn->close();
        } else {
            $erro = 'Erro ao fazer upload da foto.';
        }
    } else {
        $erro = 'Selecione uma imagem.';
    }
}
    
    // Alterar senha
    if (isset($_POST['alterar_senha'])) {
        $senha_atual = $_POST['senha_atual'] ?? '';
        $nova_senha = $_POST['nova_senha'] ?? '';
        $confirmar_senha = $_POST['confirmar_senha'] ?? '';
        
        if (empty($senha_atual) || empty($nova_senha) || empty($confirmar_senha)) {
            $erro = 'Preencha todos os campos de senha.';
        } elseif ($nova_senha !== $confirmar_senha) {
            $erro = 'As novas senhas não coincidem.';
        } elseif (strlen($nova_senha) < 6) {
            $erro = 'A nova senha deve ter pelo menos 6 caracteres.';
        } else {
            $conn = conectarDB();
            $sql = "SELECT senha FROM usuarios WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            $senha_valida = false;
            if (password_verify($senha_atual, $row['senha'])) {
                $senha_valida = true;
            } elseif ($row['senha'] === md5($senha_atual)) {
                $senha_valida = true;
            }
            
            if (!$senha_valida) {
                $erro = 'Senha atual incorreta.';
            } else {
                $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET senha = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $nova_senha_hash, $usuario_id);
                
                if ($stmt->execute()) {
                    $mensagem = 'Senha alterada com sucesso!';
                } else {
                    $erro = 'Erro ao alterar senha.';
                }
                $stmt->close();
            }
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LOOKVAULT · Minha Conta</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        /* ===== MODO ESCURO ===== */
        [data-theme="dark"] {
            --roxo-claro: #6b5b8a;
            --roxo-medio: #8b7ab8;
            --roxo-escuro: #a894d4;
            --azul-claro: #5a7a9a;
            --azul-medio: #7a9aba;
            --rosa-claro: #8a5a6a;
            --rosa-medio: #aa7a8a;
            --branco: #1a1a2e;
            --cinza-claro: #2a2a3e;
            --texto: #e8e8f0;
            --texto-claro: #b0b0c0;
        }
        
        /* ===== CONFIGURAÇÕES DA CONTA ===== */
        .account-container {
            padding: 40px 0 60px;
        }
        
        .account-header {
            display: flex;
            align-items: center;
            gap: 24px;
            margin-bottom: 40px;
            padding: 30px;
            background: var(--cinza-claro);
            border-radius: 24px;
            border: 1px solid #f0edff;
        }
        
        .account-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--roxo-claro), var(--roxo-medio));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: #fff;
            flex-shrink: 0;
            overflow: hidden;
            position: relative;
        }
        
        .account-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .account-avatar .avatar-placeholder {
            font-size: 3rem;
            color: #fff;
        }
        
        .account-avatar .avatar-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.6);
            color: #fff;
            font-size: 0.6rem;
            padding: 4px;
            text-align: center;
            opacity: 0;
            transition: opacity 0.3s;
            cursor: pointer;
        }
        
        .account-avatar:hover .avatar-overlay {
            opacity: 1;
        }
        
        .account-info h2 {
            font-size: 1.5rem;
            margin-bottom: 4px;
        }
        
        .account-info p {
            color: var(--texto-claro);
            font-size: 0.95rem;
        }
        
        .account-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 16px;
            margin-bottom: 40px;
        }
        
        .account-stat-card {
            background: var(--branco);
            padding: 20px;
            border-radius: 16px;
            text-align: center;
            border: 1px solid #f0edff;
            transition: transform 0.3s;
        }
        
        .account-stat-card:hover {
            transform: translateY(-4px);
        }
        
        .account-stat-card .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--roxo-escuro);
        }
        
        .account-stat-card .stat-label {
            color: var(--texto-claro);
            font-size: 0.85rem;
        }
        
        .account-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .account-card {
            background: var(--branco);
            padding: 28px 32px;
            border-radius: 20px;
            border: 1px solid #f0edff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }
        
        .account-card h3 {
            font-size: 1.1rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--texto);
        }
        
        .account-card h3 i {
            color: var(--roxo-medio);
        }
        
        .account-card .form-group {
            margin-bottom: 16px;
        }
        
        .account-card .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 4px;
            font-size: 0.85rem;
            color: var(--texto);
        }
        
        .account-card .form-group input {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e8e4f0;
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.3s;
            font-size: 0.95rem;
            background: var(--branco);
            color: var(--texto);
        }
        
        .account-card .form-group input:focus {
            outline: none;
            border-color: var(--roxo-medio);
        }
        
        .account-card .form-group input:disabled {
            background: var(--cinza-claro);
            cursor: not-allowed;
        }
        
        .btn-account {
            padding: 10px 28px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Poppins', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-account-primary {
            background: linear-gradient(135deg, var(--roxo-medio), var(--azul-medio));
            color: #fff;
            box-shadow: 0 4px 16px rgba(167, 139, 250, 0.25);
        }
        
        .btn-account-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(167, 139, 250, 0.35);
        }
        
        .btn-account-danger {
            background: var(--rosa-claro);
            color: var(--roxo-escuro);
        }
        
        .btn-account-danger:hover {
            background: var(--rosa-medio);
            color: #fff;
        }
        
        .mensagem-sucesso {
            background: #d4edda;
            color: #155724;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 16px;
            border-left: 4px solid #28a745;
            font-size: 0.9rem;
        }
        
        .mensagem-erro {
            background: #f8d7da;
            color: #721c24;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 16px;
            border-left: 4px solid #dc3545;
            font-size: 0.9rem;
        }
        
        .account-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #f0edff;
        }
        
        /* ===== TOGGLE MODO ESCURO ===== */
        .theme-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            background: var(--cinza-claro);
            border-radius: 40px;
            cursor: pointer;
            transition: all 0.3s;
            border: 1px solid #f0edff;
        }
        
        .theme-toggle:hover {
            transform: scale(1.02);
        }
        
        .theme-toggle .toggle-track {
            width: 50px;
            height: 26px;
            background: var(--roxo-claro);
            border-radius: 40px;
            position: relative;
            transition: background 0.3s;
        }
        
        .theme-toggle .toggle-thumb {
            width: 20px;
            height: 20px;
            background: #fff;
            border-radius: 50%;
            position: absolute;
            top: 3px;
            left: 3px;
            transition: transform 0.3s;
        }
        
        [data-theme="dark"] .theme-toggle .toggle-track {
            background: var(--roxo-medio);
        }
        
        [data-theme="dark"] .theme-toggle .toggle-thumb {
            transform: translateX(24px);
        }
        
        @media (max-width: 768px) {
            .account-grid {
                grid-template-columns: 1fr;
            }
            
            .account-header {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main>
    <section class="page-header">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    <h1 class="page-title">Minha Conta</h1>
                    <p class="page-subtitle">Gerencie suas informações e preferências</p>
                </div>
                <!-- Toggle Modo Escuro -->
                <!-- Toggle Modo Escuro - VERSÃO MAIS BONITA -->
<div class="theme-toggle" onclick="toggleTheme()" title="Alternar tema">
    <i class="fas fa-sun" id="themeIcon" style="color: var(--texto-claro); font-size: 1.1rem;"></i>
    <div class="toggle-track">
        <div class="toggle-thumb">
            <i class="fas fa-moon" style="font-size: 0.6rem; color: #fff; display: flex; align-items: center; justify-content: center; width: 100%; height: 100%;"></i>
        </div>
    </div>
    <i class="fas fa-moon" id="themeIconDark" style="color: var(--texto-claro); font-size: 1.1rem;"></i>
    <span style="font-size: 0.8rem; color: var(--texto-claro); font-weight: 500; margin-left: 4px;">Tema</span>
</div>
    </section>

    <section class="account-container">
        <div class="container">
            <?php if (!empty($mensagem)): ?>
            <div class="mensagem-sucesso"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($mensagem); ?></div>
            <?php endif; ?>
            
            <?php if (!empty($erro)): ?>
            <div class="mensagem-erro"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>

            <!-- HEADER DA CONTA -->
            <div class="account-header">
                <div class="account-avatar" id="avatarContainer">
                    <?php if (!empty($usuario['foto_perfil']) && file_exists($usuario['foto_perfil'])): ?>
                    <img src="<?php echo htmlspecialchars($usuario['foto_perfil']); ?>" alt="Foto de perfil" />
                    <?php else: ?>
                    <span class="avatar-placeholder"><i class="fas fa-user"></i></span>
                    <?php endif; ?>
                    <div class="avatar-overlay" onclick="document.getElementById('fotoInput').click();">
                        <i class="fas fa-camera"></i> Trocar
                    </div>
                </div>
                <div class="account-info">
                    <h2><?php echo htmlspecialchars($usuario['nome']); ?></h2>
                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($usuario['email']); ?></p>
                    <p style="font-size: 0.8rem; color: var(--texto-claro);">
                        <i class="fas fa-calendar-alt"></i> Membro desde <?php echo date('d/m/Y', strtotime($usuario['data_cadastro'])); ?>
                    </p>
                </div>
            </div>

            <!-- ESTATÍSTICAS RÁPIDAS -->
            <div class="account-stats">
                <div class="account-stat-card">
                    <div class="stat-number"><?php echo $total_pecas; ?></div>
                    <div class="stat-label">Peças no guarda-roupa</div>
                </div>
                <div class="account-stat-card">
                    <div class="stat-number"><?php echo $total_looks; ?></div>
                    <div class="stat-label">Looks criados</div>
                </div>
                <div class="account-stat-card">
                    <div class="stat-number"><?php echo date('d/m/Y'); ?></div>
                    <div class="stat-label">Último acesso</div>
                </div>
            </div>

            <!-- GRID DE CONFIGURAÇÕES -->
            <div class="account-grid">
                <!-- EDITAR PERFIL -->
                <div class="account-card">
                    <h3><i class="fas fa-user-edit"></i> Editar Perfil</h3>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="nome">Nome completo</label>
                            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required />
                        </div>
                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required />
                        </div>
                        <button type="submit" name="atualizar_perfil" class="btn-account btn-account-primary">
                            <i class="fas fa-save"></i> Atualizar Perfil
                        </button>
                    </form>
                    
                    <!-- Upload Foto Perfil -->
                    <form method="POST" action="" enctype="multipart/form-data" style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #f0edff;">
                        <div class="form-group">
                            <label for="foto_perfil">Foto de perfil</label>
                            <input type="file" id="fotoInput" name="foto_perfil" accept="image/*" style="display: none;" onchange="this.form.submit()" />
                            <button type="button" class="btn-account btn-account-primary" onclick="document.getElementById('fotoInput').click();" style="font-size: 0.85rem;">
                                <i class="fas fa-camera"></i> Escolher foto
                            </button>
                        </div>
                        <input type="hidden" name="atualizar_foto" value="1" />
                    </form>
                </div>

                <!-- ALTERAR SENHA -->
                <div class="account-card">
                    <h3><i class="fas fa-lock"></i> Alterar Senha</h3>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="senha_atual">Senha atual</label>
                            <input type="password" id="senha_atual" name="senha_atual" placeholder="Digite sua senha atual" required />
                        </div>
                        <div class="form-group">
                            <label for="nova_senha">Nova senha</label>
                            <input type="password" id="nova_senha" name="nova_senha" placeholder="Mínimo 6 caracteres" required />
                        </div>
                        <div class="form-group">
                            <label for="confirmar_senha">Confirmar nova senha</label>
                            <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Digite novamente" required />
                        </div>
                        <button type="submit" name="alterar_senha" class="btn-account btn-account-primary">
                            <i class="fas fa-key"></i> Alterar Senha
                        </button>
                    </form>
                </div>
            </div>

            <!-- AÇÕES DA CONTA -->
            <div style="margin-top: 30px; padding: 24px; background: var(--cinza-claro); border-radius: 20px; border: 1px solid #f0edff;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                    <div>
                        <h4 style="margin-bottom: 4px; color: var(--texto);">Sair da sua conta</h4>
                        <p style="color: var(--texto-claro); font-size: 0.85rem;">Você será redirecionado para a página de login.</p>
                    </div>
                    <a href="logout.php" class="btn-account btn-account-danger">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
// ===== MODO ESCURO =====
function toggleTheme() {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
}

// Carregar tema salvo
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        document.documentElement.setAttribute('data-theme', savedTheme);
    }
});
</script>

<?php include 'includes/footer.php'; ?>