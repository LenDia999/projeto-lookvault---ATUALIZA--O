<?php
session_start();
require_once 'configdatabase.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    
    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos!';
    } elseif (strlen($nome) < 3) {
        $erro = 'Nome deve ter pelo menos 3 caracteres!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido!';
    } elseif (strlen($senha) < 6) {
        $erro = 'Senha deve ter pelo menos 6 caracteres!';
    } elseif ($senha !== $confirmar_senha) {
        $erro = 'As senhas não coincidem!';
    } else {
        $conn = conectarDB();
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $erro = 'Este e-mail já está cadastrado!';
        } else {
            $senha_hash = md5($senha);
            $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $nome, $email, $senha_hash);
            
            if ($stmt->execute()) {
                header('Location: login.php?cadastro=sucesso');
                exit;
            } else {
                $erro = 'Erro ao cadastrar. Tente novamente.';
            }
        }
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="<?php echo $_COOKIE['theme'] ?? 'light'; ?>">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LOOKVAULT · Criar Conta</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        .register-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            background: var(--cinza-claro);
            transition: background 0.3s;
        }
        .register-box {
            background: var(--branco);
            padding: 48px 40px;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(124, 92, 191, 0.10);
            max-width: 460px;
            width: 100%;
            border: 1px solid #f0edff;
            transition: background 0.3s, border-color 0.3s;
        }
        .register-box .logo-register {
            text-align: center;
            margin-bottom: 32px;
        }
        .register-box .logo-register h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            background: linear-gradient(135deg, var(--roxo-escuro), var(--rosa-medio));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .register-box .logo-register p {
            color: var(--texto-claro);
            font-size: 0.9rem;
        }
        .register-box .form-group {
            margin-bottom: 18px;
        }
        .register-box .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 6px;
            color: var(--texto);
            font-size: 0.9rem;
        }
        .register-box .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e8e4f0;
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.3s;
            font-size: 0.95rem;
            background: var(--branco);
            color: var(--texto);
        }
        .register-box .form-group input:focus {
            outline: none;
            border-color: var(--roxo-medio);
        }
        .register-box .btn-register {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--roxo-medio), var(--azul-medio));
            color: #fff;
            border: none;
            border-radius: 60px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.25s, box-shadow 0.25s;
            box-shadow: 0 8px 28px rgba(167, 139, 250, 0.3);
            font-family: 'Poppins', sans-serif;
        }
        .register-box .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 36px rgba(167, 139, 250, 0.4);
        }
        .register-box .links {
            text-align: center;
            margin-top: 20px;
        }
        .register-box .links a {
            color: var(--roxo-medio);
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.3s;
        }
        .register-box .links a:hover {
            color: var(--roxo-escuro);
        }
        .register-box .erro,
        .register-box .sucesso {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 16px;
            font-size: 0.9rem;
        }
        .register-box .erro {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        .register-box .sucesso {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        @media (max-width: 480px) {
            .register-box { padding: 32px 24px; }
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="register-box">
        <div class="logo-register">
            <h1><i class="fas fa-user-plus" style="-webkit-text-fill-color: initial; color: var(--roxo-medio);"></i> LOOKVAULT</h1>
            <p>Crie sua conta e organize seu guarda-roupa</p>
        </div>

        <?php if (!empty($erro)): ?>
        <div class="erro"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" placeholder="Seu nome completo" required />
            </div>
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="seu@email.com" required />
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="••••••••" required />
            </div>
            <div class="form-group">
                <label for="confirmar_senha">Confirmar senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="••••••••" required />
            </div>
            <button type="submit" class="btn-register">Criar Conta</button>
        </form>

        <div class="links">
            <a href="login.php">Já tenho conta</a>
        </div>
    </div>
</div>

<script src="assets/js/theme.js"></script>
</body>
</html>