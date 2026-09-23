<?php
session_start();
require_once 'configdatabase.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $marca = $_POST['marca'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $cor = $_POST['cor'] ?? '';
    $valor = str_replace(',', '.', $_POST['valor'] ?? 0);
    $tags = $_POST['tags'] ?? '';
    
    // Validação básica
    if (empty($nome) || empty($categoria)) {
        header('Location: cadastro.php?erro=Preencha todos os campos obrigatórios');
        exit;
    }
    
    // Upload da foto
    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/pecas/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nome_arquivo = time() . '_' . uniqid() . '.' . $ext;
        $caminho_arquivo = $upload_dir . $nome_arquivo;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_arquivo)) {
            $foto = $caminho_arquivo;
        }
    }
    
    // Inserir no banco
    $sql = "INSERT INTO pecas (usuario_id, nome, marca, categoria, cor, valor, tags, foto) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $id = inserirRetornarId($sql, [$usuario_id, $nome, $marca, $categoria, $cor, $valor, $tags, $foto]);
    
    if ($id) {
        header('Location: guarda-roupa.php?sucesso=Peça cadastrada com sucesso!');
    } else {
        header('Location: cadastro.php?erro=Erro ao cadastrar peça');
    }
} else {
    header('Location: cadastro.php');
}
?>