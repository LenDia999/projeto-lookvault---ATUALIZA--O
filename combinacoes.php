<?php
session_start();
require_once 'configdatabase.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
$usuario_id = $_SESSION['usuario_id'];

// Buscar todos os looks do usuário
$sql = "SELECT * FROM looks WHERE usuario_id = ? ORDER BY data_criacao DESC";
$result = executarQuery($sql, [$usuario_id]);

$looks = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Buscar peças de cada look
        $sql_pecas = "SELECT p.* FROM pecas p 
                      INNER JOIN look_pecas lp ON p.id = lp.peca_id 
                      WHERE lp.look_id = ?";
        $result_pecas = executarQuery($sql_pecas, [$row['id']]);
        $pecas_look = [];
        if ($result_pecas && $result_pecas->num_rows > 0) {
            while ($peca = $result_pecas->fetch_assoc()) {
                $pecas_look[] = $peca;
            }
        }
        $row['pecas'] = $pecas_look;
        $looks[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LOOKVAULT · Looks & Combinações</title>
    <meta name="description" content="Crie e organize combinações de looks incríveis." />
    
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        .look-card .look-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .look-card .look-actions .btn-small {
            padding: 6px 14px;
            font-size: 0.75rem;
            border-radius: 30px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: 'Poppins', sans-serif;
        }
        
        .btn-edit {
            background: var(--azul-claro);
            color: var(--texto);
        }
        
        .btn-edit:hover {
            background: var(--azul-medio);
            color: #fff;
        }
        
        .btn-delete {
            background: var(--rosa-claro);
            color: var(--texto);
        }
        
        .btn-delete:hover {
            background: var(--rosa-medio);
            color: #fff;
        }
        
        .btn-view {
            background: var(--roxo-claro);
            color: var(--texto);
        }
        
        .btn-view:hover {
            background: var(--roxo-medio);
            color: #fff;
        }
        
        .look-pecas-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin: 8px 0;
        }
        
        .look-pecas-preview .peca-mini {
            background: var(--cinza-claro);
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 0.7rem;
            color: var(--texto-claro);
        }
        
        .mensagem-sucesso {
            background: #d4edda;
            color: #155724;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        
        .mensagem-erro {
            background: #f8d7da;
            color: #721c24;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }
        
        .look-ocasiao-badge {
            display: inline-block;
            padding: 2px 14px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            background: var(--roxo-claro);
            color: var(--roxo-escuro);
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main>
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">Looks & Combinações</h1>
            <p class="page-subtitle">Monte looks completos e planeje produções para qualquer ocasião</p>
        </div>
    </section>

    <?php if (isset($_GET['sucesso'])): ?>
    <div class="container">
        <div class="mensagem-sucesso">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['sucesso']); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['erro'])): ?>
    <div class="container">
        <div class="mensagem-erro">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['erro']); ?>
        </div>
    </div>
    <?php endif; ?>

    <section class="looks-controls">
        <div class="container">
            <div class="controls-wrapper">
                <div class="look-filters">
                    <button class="filter-btn active" data-filter="all">Todos</button>
                    <button class="filter-btn" data-filter="Casual">Casual</button>
                    <button class="filter-btn" data-filter="Social">Social</button>
                    <button class="filter-btn" data-filter="Esporte">Esporte</button>
                    <button class="filter-btn" data-filter="Festa">Festa</button>
                    <button class="filter-btn" data-filter="Trabalho">Trabalho</button>
                </div>
                <a href="criar-look.php" class="btn-primary">
                    <i class="fas fa-plus"></i> Criar Look
                </a>
            </div>
        </div>
    </section>

    <section class="looks-grid">
        <div class="container">
            <?php if (empty($looks)): ?>
            <div style="text-align: center; padding: 80px 20px; background: var(--cinza-claro); border-radius: 20px;">
                <i class="fas fa-magic" style="font-size: 4rem; color: var(--roxo-claro); margin-bottom: 16px;"></i>
                <h3 style="margin-bottom: 8px;">Nenhum look criado ainda</h3>
                <p style="color: var(--texto-claro);">Comece a combinar suas peças e criar looks incríveis!</p>
                <a href="criar-look.php" class="btn-primary" style="margin-top: 16px; display: inline-flex;">
                    <i class="fas fa-plus"></i> Criar Meu Primeiro Look
                </a>
            </div>
            <?php else: ?>
            <div class="grid-items" id="looksGrid">
                <?php foreach ($looks as $look): ?>
                <div class="look-card" data-ocasiao="<?php echo htmlspecialchars($look['ocasiao']); ?>">
                    <div class="look-image">
                        <?php if (!empty($look['pecas']) && !empty($look['pecas'][0]['foto']) && file_exists($look['pecas'][0]['foto'])): ?>
                        <img src="<?php echo htmlspecialchars($look['pecas'][0]['foto']); ?>" alt="<?php echo htmlspecialchars($look['nome']); ?>" />
                        <?php else: ?>
                        <img src="https://via.placeholder.com/400x500/f4f2fd/7c5cbf?text=<?php echo urlencode($look['nome']); ?>" alt="<?php echo htmlspecialchars($look['nome']); ?>" />
                        <?php endif; ?>
                        <span class="look-ocasiao"><?php echo htmlspecialchars($look['ocasiao'] ?: 'Geral'); ?></span>
                    </div>
                    <div class="look-info">
                        <h3><?php echo htmlspecialchars($look['nome']); ?></h3>
                        <div class="look-pecas-preview">
                            <?php 
                            $total_pecas = count($look['pecas']);
                            $mostrar = array_slice($look['pecas'], 0, 4);
                            foreach ($mostrar as $peca): 
                            ?>
                            <span class="peca-mini"><?php echo htmlspecialchars($peca['nome']); ?></span>
                            <?php endforeach; ?>
                            <?php if ($total_pecas > 4): ?>
                            <span class="peca-mini">+<?php echo $total_pecas - 4; ?></span>
                            <?php endif; ?>
                        </div>
                        <div style="display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 12px;">
                            <?php 
                            $cores_unicas = array_unique(array_column($look['pecas'], 'cor'));
                            foreach ($cores_unicas as $cor): 
                            ?>
                            <span class="tag" style="background: <?php echo strtolower(htmlspecialchars($cor)); ?>; color: #fff; border: 1px solid #ddd;">
                                <?php echo htmlspecialchars($cor); ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                        <div class="look-actions">
                            <a href="criar-look.php?id=<?php echo $look['id']; ?>" class="btn-small btn-edit">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button class="btn-small btn-delete" onclick="excluirLook(<?php echo $look['id']; ?>)">
                                <i class="fas fa-trash"></i> Excluir
                            </button>
                            <button class="btn-small btn-view" onclick="visualizarLook(<?php echo $look['id']; ?>)">
                                <i class="fas fa-eye"></i> Ver
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<script>
// Filtros por ocasião
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const filter = this.dataset.filter;
        const items = document.querySelectorAll('.look-card');
        
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        items.forEach(item => {
            const ocasiao = item.dataset.ocasiao || '';
            if (filter === 'all' || ocasiao === filter) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
});

// Função para excluir look
function excluirLook(id) {
    if (confirm('Tem certeza que deseja excluir este look?')) {
        window.location.href = 'excluir-look.php?id=' + id;
    }
}

// Função para visualizar look (modal)
function visualizarLook(id) {
    // Pode implementar um modal ou redirecionar para uma página de visualização
    alert('Funcionalidade em desenvolvimento: Visualizar look #' + id);
}

// Mensagens de sucesso/erro desaparecem após 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    const mensagens = document.querySelectorAll('.mensagem-sucesso, .mensagem-erro');
    mensagens.forEach(msg => {
        setTimeout(() => {
            msg.style.transition = 'opacity 0.5s';
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 500);
        }, 5000);
    });
});
</script>

<?php include 'includes/footer.php'; ?>