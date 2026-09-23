<?php
session_start();
require_once 'configdatabase.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $erro = 'Por favor, informe seu e-mail.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Por favor, informe um e-mail válido.';
    } else {
        $conn = conectarDB();
        $sql = 'SELECT id FROM usuarios WHERE email = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        $sucesso = 'Se o e-mail estiver cadastrado, você receberá instruções para redefinir sua senha.';

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
    <title>LOOKVAULT · Recuperar Senha</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        .recover-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            background: var(--cinza-claro);
            transition: background 0.3s;
        }
        .recover-box {
            background: var(--branco);
            padding: 48px 40px;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(124, 92, 191, 0.10);
            max-width: 420px;
            width: 100%;
            border: 1px solid #f0edff;
            transition: background 0.3s, border-color 0.3s;
        }
        .recover-box .logo-recover {
            text-align: center;
            margin-bottom: 32px;
        }
        .recover-box .logo-recover h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            background: linear-gradient(135deg, var(--roxo-escuro), var(--rosa-medio));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .recover-box .logo-recover p {
            color: var(--texto-claro);
            font-size: 0.9rem;
        }
        .recover-box .form-group {
            margin-bottom: 20px;
        }
        .recover-box .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 6px;
            color: var(--texto);
            font-size: 0.9rem;
        }
        .recover-box .form-group input {
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
        .recover-box .form-group input:focus {
            outline: none;
            border-color: var(--roxo-medio);
        }
        .recover-box .btn-recover {
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
        .recover-box .btn-recover:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 36px rgba(167, 139, 250, 0.4);
        }
        .recover-box .links {
            text-align: center;
            margin-top: 20px;
        }
        .recover-box .links a {
            color: var(--roxo-medio);
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.3s;
        }
        .recover-box .links a:hover {
            color: var(--roxo-escuro);
        }
        .recover-box .links .divider {
            margin: 0 12px;
            color: var(--texto-claro);
        }
        .recover-box .erro {
            background: #f8d7da;
            color: #721c24;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 16px;
            border-left: 4px solid #dc3545;
            font-size: 0.9rem;
        }
        .recover-box .sucesso {
            background: #d4edda;
            color: #155724;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 16px;
            border-left: 4px solid #28a745;
            font-size: 0.9rem;
        }
        @media (max-width: 480px) {
            .recover-box { padding: 32px 24px; }
        }
    </style>
</head>
<body>

<div class="recover-container">
    <div class="recover-box">
        <div class="logo-recover">
            <h1><i class="fas fa-unlock-alt" style="-webkit-text-fill-color: initial; color: var(--roxo-medio);"></i> Recuperar Senha</h1>
            <p>Digite seu e-mail para receber instruções de redefinição de senha.</p>
        </div>

        <?php if (!empty($erro)): ?>
        <div class="erro"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <?php if (!empty($sucesso)): ?>
        <div class="sucesso"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($sucesso); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="seu@email.com" required />
            </div>
            <button type="submit" class="btn-recover">Enviar instruções</button>
        </form>

        <div class="links">
            <a href="login.php">Voltar ao login</a>
            <span class="divider">|</span>
            <a href="cadastro-usuario.php">Criar conta</a>
        </div>
    </div>
</div>

<script src="assets/js/theme.js"></script>
</body>
</html>