<?php
session_start();
require_once 'configdatabase.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
$usuario_id = $_SESSION['usuario_id'];

// Buscar peças do banco
$sql = "SELECT * FROM pecas WHERE usuario_id = ? ORDER BY data_cadastro DESC";
$result = executarQuery($sql, [$usuario_id]);

$pecas = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pecas[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LOOKVAULT · Meu Guarda-Roupa</title>
    <meta name="description" content="Gerencie todas as suas peças de roupa em um só lugar." />
    
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>

<?php include 'includes/header.php'; ?>

<main>
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">Meu Guarda-Roupa</h1>
            <p class="page-subtitle">Gerencie todas as suas peças de forma inteligente e organizada</p>
        </div>
    </section>

    <?php if (isset($_GET['sucesso'])): ?>
    <div class="container">
        <div style="background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 12px; margin-bottom: 20px; border-left: 4px solid #28a745;">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['sucesso']); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['erro'])): ?>
    <div class="container">
        <div style="background: #f8d7da; color: #721c24; padding: 15px 20px; border-radius: 12px; margin-bottom: 20px; border-left: 4px solid #dc3545;">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['erro']); ?>
        </div>
    </div>
    <?php endif; ?>

    <section class="wardrobe-controls">
        <div class="container">
            <div class="controls-wrapper">
                <div class="search-filter">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Pesquisar peças..." onkeyup="filtrarPecas()" />
                    </div>
                    <div class="filter-buttons">
                        <button class="filter-btn active" data-filter="all">Todas</button>
                        <button class="filter-btn" data-filter="Camisas">Camisas</button>
                        <button class="filter-btn" data-filter="Calças">Calças</button>
                        <button class="filter-btn" data-filter="Sapatos">Sapatos</button>
                        <button class="filter-btn" data-filter="Acessórios">Acessórios</button>
                    </div>
                </div>
                <a href="cadastro.php" class="btn-primary">
                    <i class="fas fa-plus"></i> Adicionar Peça
                </a>
            </div>
        </div>
    </section>

    <section class="wardrobe-grid">
        <div class="container">
            <div class="grid-items" id="pecasGrid">
                <?php if (empty($pecas)): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px; background: var(--cinza-claro); border-radius: 20px;">
                    <i class="fas fa-tshirt" style="font-size: 4rem; color: var(--roxo-claro); margin-bottom: 16px;"></i>
                    <h3 style="margin-bottom: 8px;">Seu guarda-roupa está vazio</h3>
                    <p style="color: var(--texto-claro);">Comece adicionando suas primeiras peças!</p>
                    <a href="cadastro.php" class="btn-primary" style="margin-top: 16px; display: inline-flex;">
                        <i class="fas fa-plus"></i> Adicionar Peça
                    </a>
                </div>
                <?php else: ?>
                <?php foreach ($pecas as $peca): ?>
                <div class="wardrobe-item" data-categoria="<?php echo htmlspecialchars($peca['categoria']); ?>" data-nome="<?php echo htmlspecialchars($peca['nome']); ?>">
                    <div class="item-image">
                        <?php if (!empty($peca['foto']) && file_exists($peca['foto'])): ?>
                        <img src="<?php echo htmlspecialchars($peca['foto']); ?>" alt="<?php echo htmlspecialchars($peca['nome']); ?>" />
                        <?php else: ?>
                        <img src="https://via.placeholder.com/300x400/f4f2fd/7c5cbf?text=<?php echo urlencode($peca['nome']); ?>" alt="<?php echo htmlspecialchars($peca['nome']); ?>" />
                        <?php endif; ?>
                        <div class="item-actions">
                            <button class="action-btn" onclick="editarPeca(<?php echo $peca['id']; ?>)"><i class="fas fa-edit"></i></button>
                            <button class="action-btn" onclick="excluirPeca(<?php echo $peca['id']; ?>)"><i class="fas fa-trash"></i></button>
                            <button class="action-btn" onclick="registrarUso(<?php echo $peca['id']; ?>)" title="Registrar uso"><i class="fas fa-check"></i></button>
                        </div>
                    </div>
                    <div class="item-info">
                        <h3><?php echo htmlspecialchars($peca['nome']); ?></h3>
                        <p class="item-marca"><?php echo htmlspecialchars($peca['marca'] ?: 'Sem marca'); ?></p>
                        <p class="item-marca" style="font-weight: 600; color: var(--roxo-escuro);">
                            R$ <?php echo number_format($peca['valor'], 2, ',', '.'); ?>
                        </p>
                        <div class="item-tags">
                            <span class="tag"><?php echo htmlspecialchars($peca['categoria']); ?></span>
                            <span class="tag"><?php echo htmlspecialchars($peca['cor']); ?></span>
                            <?php if (!empty($peca['tags'])): ?>
                                <?php foreach (explode(',', $peca['tags']) as $tag): ?>
                                <span class="tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<script>
// Função para filtrar peças
function filtrarPecas() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const items = document.querySelectorAll('.wardrobe-item');
    
    items.forEach(item => {
        const nome = item.dataset.nome.toLowerCase();
        const categoria = item.dataset.categoria.toLowerCase();
        const match = nome.includes(searchTerm) || categoria.includes(searchTerm);
        item.style.display = match ? '' : 'none';
    });
}

// Função para excluir peça
function excluirPeca(id) {
    if (confirm('Tem certeza que deseja excluir esta peça?')) {
        window.location.href = 'excluir-peca.php?id=' + id;
    }
}

// Função para editar peça
function editarPeca(id) {
    window.location.href = 'editar-peca.php?id=' + id;
}

// Função para registrar uso
function registrarUso(id) {
    fetch('registrar-uso.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'peca_id=' + id
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            alert('Uso registrado com sucesso!');
        } else {
            alert('Erro ao registrar uso: ' + data.mensagem);
        }
    })
    .catch(error => {
        alert('Erro ao registrar uso');
    });
}

// Filtros por categoria
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const filter = this.dataset.filter;
        const items = document.querySelectorAll('.wardrobe-item');
        
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        items.forEach(item => {
            if (filter === 'all' || item.dataset.categoria === filter) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>