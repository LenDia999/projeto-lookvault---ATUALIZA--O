<?php
session_start();
require_once 'configdatabase.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    if (empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos!';
    } else {
        $conn = conectarDB();
        $sql = "SELECT id, nome, email, senha FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
            if ($usuario['senha'] === md5($senha) || password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                header('Location: index.php');
                exit;
            } else {
                $erro = 'Senha incorreta!';
            }
        } else {
            $erro = 'E-mail não cadastrado!';
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
    <title>LOOKVAULT · Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        .login-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            background: var(--cinza-claro);
            transition: background 0.3s;
        }
        .login-box {
            background: var(--branco);
            padding: 48px 40px;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(124, 92, 191, 0.10);
            max-width: 420px;
            width: 100%;
            border: 1px solid #f0edff;
            transition: background 0.3s, border-color 0.3s;
        }
        .login-box .logo-login {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-box .logo-login h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            background: linear-gradient(135deg, var(--roxo-escuro), var(--rosa-medio));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .login-box .logo-login p {
            color: var(--texto-claro);
            font-size: 0.9rem;
        }
        .login-box .form-group {
            margin-bottom: 20px;
        }
        .login-box .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 6px;
            color: var(--texto);
            font-size: 0.9rem;
        }
        .login-box .form-group input {
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
        .login-box .form-group input:focus {
            outline: none;
            border-color: var(--roxo-medio);
        }
        .login-box .btn-login {
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
        .login-box .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 36px rgba(167, 139, 250, 0.4);
        }
        .login-box .links {
            text-align: center;
            margin-top: 20px;
        }
        .login-box .links a {
            color: var(--roxo-medio);
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.3s;
        }
        .login-box .links a:hover {
            color: var(--roxo-escuro);
        }
        .login-box .links .divider {
            margin: 0 12px;
            color: var(--texto-claro);
        }
        .login-box .erro {
            background: #f8d7da;
            color: #721c24;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 16px;
            border-left: 4px solid #dc3545;
            font-size: 0.9rem;
        }
        .login-box .sucesso {
            background: #d4edda;
            color: #155724;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 16px;
            border-left: 4px solid #28a745;
            font-size: 0.9rem;
        }
        @media (max-width: 480px) {
            .login-box { padding: 32px 24px; }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <div class="logo-login">
            <h1><i class="fas fa-wardrobe" style="-webkit-text-fill-color: initial; color: var(--roxo-medio);"></i> LOOKVAULT</h1>
            <p>Entre e organize seu guarda-roupa</p>
        </div>
        
        <?php if (!empty($erro)): ?>
        <div class="erro"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['cadastro']) && $_GET['cadastro'] == 'sucesso'): ?>
        <div class="sucesso"><i class="fas fa-check-circle"></i> Cadastro realizado! Faça seu login.</div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="seu@email.com" required />
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="••••••••" required />
            </div>
            <button type="submit" class="btn-login">Entrar</button>
        </form>
        
        <div class="links">
            <a href="cadastro-usuario.php">Criar conta</a>
            <span class="divider">|</span>
            <a href="recuperar-senha.php">Esqueci minha senha</a>
        </div>
    </div>
</div>

<script src="assets/js/theme.js"></script>
</body>
</html>