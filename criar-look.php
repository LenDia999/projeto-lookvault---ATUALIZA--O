<?php
session_start();
require_once 'configdatabase.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
$usuario_id = $_SESSION['usuario_id'];
$look_id = $_GET['id'] ?? 0;
$editando = false;
$look_atual = null;
$pecas_selecionadas = [];

// Se for edição, buscar dados do look
if ($look_id > 0) {
    $sql = "SELECT * FROM looks WHERE id = ? AND usuario_id = ?";
    $result = executarQuery($sql, [$look_id, $usuario_id]);
    if ($result && $result->num_rows > 0) {
        $look_atual = $result->fetch_assoc();
        $editando = true;
        
        // Buscar peças do look
        $sql_pecas = "SELECT peca_id FROM look_pecas WHERE look_id = ?";
        $result_pecas = executarQuery($sql_pecas, [$look_id]);
        if ($result_pecas) {
            while ($row = $result_pecas->fetch_assoc()) {
                $pecas_selecionadas[] = $row['peca_id'];
            }
        }
    }
}

// Buscar todas as peças do usuário para montar o look
$sql_pecas = "SELECT * FROM pecas WHERE usuario_id = ? ORDER BY nome ASC";
$result_pecas = executarQuery($sql_pecas, [$usuario_id]);
$todas_pecas = [];
if ($result_pecas && $result_pecas->num_rows > 0) {
    while ($row = $result_pecas->fetch_assoc()) {
        $todas_pecas[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LOOKVAULT · <?php echo $editando ? 'Editar' : 'Criar'; ?> Look</title>
    <meta name="description" content="<?php echo $editando ? 'Edite' : 'Crie'; ?> combinações incríveis com suas peças favoritas." />
    
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        /* Estilos específicos para a página de criar look */
        .look-builder {
            padding: 40px 0 60px;
        }
        
        .builder-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }
        
        .pecas-disponiveis {
            background: var(--branco);
            border-radius: 20px;
            padding: 24px;
            border: 2px solid #f0edff;
            max-height: 600px;
            overflow-y: auto;
        }
        
        .pecas-disponiveis h3 {
            font-size: 1.1rem;
            margin-bottom: 16px;
            color: var(--texto);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .pecas-disponiveis h3 span {
            font-size: 0.85rem;
            color: var(--texto-claro);
            font-weight: 400;
        }
        
        .peca-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 12px;
            border: 1px solid #f0edff;
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.3s;
            background: var(--branco);
        }
        
        .peca-item:hover {
            background: var(--cinza-claro);
            border-color: var(--roxo-claro);
        }
        
        .peca-item.selecionada {
            background: var(--roxo-claro);
            border-color: var(--roxo-medio);
            opacity: 0.7;
        }
        
        .peca-item.selecionada .peca-nome {
            color: var(--roxo-escuro);
            font-weight: 600;
        }
        
        .peca-check {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 2px solid var(--roxo-claro);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.3s;
        }
        
        .peca-item.selecionada .peca-check {
            background: var(--roxo-medio);
            border-color: var(--roxo-medio);
            color: #fff;
        }
        
        .peca-check i {
            font-size: 0.7rem;
            opacity: 0;
            transition: all 0.3s;
        }
        
        .peca-item.selecionada .peca-check i {
            opacity: 1;
        }
        
        .peca-info {
            flex: 1;
        }
        
        .peca-nome {
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--texto);
        }
        
        .peca-detalhe {
            font-size: 0.75rem;
            color: var(--texto-claro);
        }
        
        .peca-cor {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 1px solid #ddd;
            flex-shrink: 0;
        }
        
        .area-montagem {
            background: var(--cinza-claro);
            border-radius: 20px;
            padding: 24px;
            border: 2px dashed var(--roxo-claro);
            min-height: 400px;
            position: relative;
        }
        
        .area-montagem h3 {
            font-size: 1.1rem;
            margin-bottom: 16px;
            color: var(--texto);
        }
        
        .look-preview {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .look-preview-item {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--branco);
            padding: 10px 16px;
            border-radius: 12px;
            border: 1px solid #e8e4f0;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .look-preview-item .remove-peca {
            margin-left: auto;
            background: none;
            border: none;
            color: var(--rosa-medio);
            cursor: pointer;
            font-size: 1.1rem;
            transition: transform 0.3s;
        }
        
        .look-preview-item .remove-peca:hover {
            transform: scale(1.2);
        }
        
        .look-empty {
            text-align: center;
            padding: 40px 20px;
            color: var(--texto-claro);
        }
        
        .look-empty i {
            font-size: 3rem;
            color: var(--roxo-claro);
            margin-bottom: 12px;
        }
        
        .search-pecas {
            margin-bottom: 16px;
        }
        
        .search-pecas input {
            width: 100%;
            padding: 10px 16px;
            border: 2px solid #e8e4f0;
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.3s;
        }
        
        .search-pecas input:focus {
            outline: none;
            border-color: var(--roxo-medio);
        }
        
        .form-actions-look {
            display: flex;
            gap: 16px;
            margin-top: 24px;
            flex-wrap: wrap;
        }
        
        .btn-salvar-look {
            background: linear-gradient(135deg, var(--roxo-medio), var(--azul-medio));
            color: #fff;
            padding: 14px 36px;
            border-radius: 60px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: transform 0.25s, box-shadow 0.25s;
            box-shadow: 0 8px 28px rgba(167, 139, 250, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-salvar-look:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 36px rgba(167, 139, 250, 0.4);
        }
        
        .btn-salvar-look:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        @media (max-width: 992px) {
            .builder-layout {
                grid-template-columns: 1fr;
            }
            
            .pecas-disponiveis {
                max-height: 300px;
            }
        }
        
        .peca-item .peca-foto {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
            background: var(--cinza-claro);
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main>
    <section class="page-header">
        <div class="container">
            <h1 class="page-title"><?php echo $editando ? 'Editar Look' : 'Criar Novo Look'; ?></h1>
            <p class="page-subtitle">Monte uma combinação única com suas peças favoritas</p>
        </div>
    </section>

    <?php if (isset($_GET['erro'])): ?>
    <div class="container">
        <div style="background: #f8d7da; color: #721c24; padding: 15px 20px; border-radius: 12px; margin-top: 20px; border-left: 4px solid #dc3545;">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['erro']); ?>
        </div>
    </div>
    <?php endif; ?>

    <section class="look-builder">
        <div class="container">
            <form action="processar-look.php" method="POST" id="formLook">
                <input type="hidden" name="look_id" value="<?php echo $look_id; ?>" />
                <input type="hidden" name="acao" value="<?php echo $editando ? 'editar' : 'criar'; ?>" />
                
                <div class="form-group" style="max-width: 600px; margin-bottom: 30px;">
                    <label for="nome_look">Nome do Look <span style="color: var(--texto-claro); font-weight: 400;">(ex: "Look Casual de Verão")</span></label>
                    <input type="text" id="nome_look" name="nome_look" 
                           placeholder="Dê um nome criativo para seu look" 
                           value="<?php echo $editando ? htmlspecialchars($look_atual['nome']) : ''; ?>" 
                           required style="font-size: 1.1rem; padding: 14px 20px;" />
                </div>
                
                <div class="form-group" style="max-width: 600px; margin-bottom: 30px;">
                    <label for="ocasiao">Ocasião</label>
                    <select id="ocasiao" name="ocasiao" style="font-size: 1rem; padding: 14px 20px;">
                        <option value="">Selecione a ocasião (opcional)</option>
                        <option value="Casual" <?php echo ($editando && $look_atual['ocasiao'] == 'Casual') ? 'selected' : ''; ?>>Casual</option>
                        <option value="Social" <?php echo ($editando && $look_atual['ocasiao'] == 'Social') ? 'selected' : ''; ?>>Social</option>
                        <option value="Festa" <?php echo ($editando && $look_atual['ocasiao'] == 'Festa') ? 'selected' : ''; ?>>Festa</option>
                        <option value="Esporte" <?php echo ($editando && $look_atual['ocasiao'] == 'Esporte') ? 'selected' : ''; ?>>Esporte</option>
                        <option value="Trabalho" <?php echo ($editando && $look_atual['ocasiao'] == 'Trabalho') ? 'selected' : ''; ?>>Trabalho</option>
                        <option value="Praia" <?php echo ($editando && $look_atual['ocasiao'] == 'Praia') ? 'selected' : ''; ?>>Praia</option>
                        <option value="Noite" <?php echo ($editando && $look_atual['ocasiao'] == 'Noite') ? 'selected' : ''; ?>>Noite</option>
                    </select>
                </div>

                <div class="builder-layout">
                    <!-- Painel de Peças Disponíveis -->
                    <div class="pecas-disponiveis">
                        <h3>
                            <span><i class="fas fa-tshirt"></i> Suas Peças</span>
                            <span><?php echo count($todas_pecas); ?> peças disponíveis</span>
                        </h3>
                        
                        <div class="search-pecas">
                            <input type="text" id="searchPecas" placeholder="🔍 Filtrar peças por nome, marca ou categoria..." onkeyup="filtrarPecas()" />
                        </div>
                        
                        <div id="listaPecas">
                            <?php if (empty($todas_pecas)): ?>
                            <div style="text-align: center; padding: 40px 20px; color: var(--texto-claro);">
                                <i class="fas fa-tshirt" style="font-size: 3rem; color: var(--roxo-claro); margin-bottom: 12px;"></i>
                                <p>Você ainda não tem peças cadastradas.</p>
                                <a href="cadastro.php" class="btn-primary" style="margin-top: 12px; display: inline-flex;">
                                    <i class="fas fa-plus"></i> Adicionar Peça
                                </a>
                            </div>
                            <?php else: ?>
                            <?php foreach ($todas_pecas as $peca): 
                                $selecionada = in_array($peca['id'], $pecas_selecionadas);
                            ?>
                            <div class="peca-item <?php echo $selecionada ? 'selecionada' : ''; ?>" 
                                 data-id="<?php echo $peca['id']; ?>"
                                 data-nome="<?php echo strtolower(htmlspecialchars($peca['nome'])); ?>"
                                 data-marca="<?php echo strtolower(htmlspecialchars($peca['marca'])); ?>"
                                 data-categoria="<?php echo strtolower(htmlspecialchars($peca['categoria'])); ?>"
                                 onclick="togglePeca(this, <?php echo $peca['id']; ?>)">
                                
                                <div class="peca-check">
                                    <i class="fas fa-check"></i>
                                </div>
                                
                                <?php if (!empty($peca['foto']) && file_exists($peca['foto'])): ?>
                                <img src="<?php echo htmlspecialchars($peca['foto']); ?>" alt="<?php echo htmlspecialchars($peca['nome']); ?>" class="peca-foto" />
                                <?php else: ?>
                                <div class="peca-foto" style="display: flex; align-items: center; justify-content: center; background: var(--cinza-claro);">
                                    <i class="fas fa-tshirt" style="color: var(--roxo-claro); font-size: 1.2rem;"></i>
                                </div>
                                <?php endif; ?>
                                
                                <div class="peca-info">
                                    <div class="peca-nome"><?php echo htmlspecialchars($peca['nome']); ?></div>
                                    <div class="peca-detalhe">
                                        <?php echo htmlspecialchars($peca['marca'] ?: 'Sem marca'); ?> • 
                                        <?php echo htmlspecialchars($peca['categoria']); ?> • 
                                        R$ <?php echo number_format($peca['valor'], 2, ',', '.'); ?>
                                    </div>
                                </div>
                                
                                <div class="peca-cor" style="background: <?php echo strtolower(htmlspecialchars($peca['cor'])); ?>;"></div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Área de Montagem do Look -->
                    <div class="area-montagem">
                        <h3>
                            <i class="fas fa-magic"></i> Montando seu Look
                            <span style="font-weight: 400; font-size: 0.85rem; color: var(--texto-claro);">
                                (<?php echo count($pecas_selecionadas); ?> peças)
                            </span>
                        </h3>
                        
                        <div id="lookPreview" class="look-preview">
                            <?php if (empty($pecas_selecionadas)): ?>
                            <div class="look-empty" id="lookEmpty">
                                <i class="fas fa-tshirt"></i>
                                <p>Clique nas peças ao lado para montar seu look</p>
                                <p style="font-size: 0.85rem;">Selecione as peças que deseja combinar</p>
                            </div>
                            <?php else: ?>
                            <?php foreach ($todas_pecas as $peca): 
                                if (in_array($peca['id'], $pecas_selecionadas)):
                            ?>
                            <div class="look-preview-item" data-id="<?php echo $peca['id']; ?>">
                                <span style="font-size: 1.2rem;">👔</span>
                                <span><strong><?php echo htmlspecialchars($peca['nome']); ?></strong> 
                                <span style="color: var(--texto-claro); font-size: 0.85rem;">
                                    <?php echo htmlspecialchars($peca['marca']); ?>
                                </span></span>
                                <button type="button" class="remove-peca" onclick="removerPecaDoLook(<?php echo $peca['id']; ?>)" title="Remover do look">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </div>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Campo oculto para enviar as peças selecionadas -->
                        <input type="hidden" name="pecas_selecionadas" id="pecasSelecionadas" 
                               value="<?php echo implode(',', $pecas_selecionadas); ?>" />
                        
                        <div class="form-actions-look">
                            <button type="submit" class="btn-salvar-look" id="btnSalvarLook" <?php echo empty($pecas_selecionadas) ? 'disabled' : ''; ?>>
                                <i class="fas fa-save"></i> <?php echo $editando ? 'Atualizar Look' : 'Salvar Look'; ?>
                            </button>
                            <a href="combinacoes.php" class="btn-secondary">Cancelar</a>
                            
                            <?php if (!empty($pecas_selecionadas)): ?>
                            <button type="button" class="btn-secondary" onclick="limparLook()" style="border-color: var(--rosa-claro); color: var(--rosa-medio);">
                                <i class="fas fa-eraser"></i> Limpar
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</main>

<script>
// Variáveis para controle
let pecasSelecionadas = <?php echo json_encode($pecas_selecionadas); ?>;

// Função para alternar seleção de peça
function togglePeca(element, id) {
    const index = pecasSelecionadas.indexOf(id);
    
    if (index > -1) {
        // Remover da seleção
        pecasSelecionadas.splice(index, 1);
        element.classList.remove('selecionada');
    } else {
        // Adicionar à seleção
        pecasSelecionadas.push(id);
        element.classList.add('selecionada');
    }
    
    atualizarPreview();
    atualizarInput();
}

// Função para remover peça do look
function removerPecaDoLook(id) {
    const index = pecasSelecionadas.indexOf(id);
    if (index > -1) {
        pecasSelecionadas.splice(index, 1);
        
        // Desmarcar visualmente
        const item = document.querySelector(`.peca-item[data-id="${id}"]`);
        if (item) {
            item.classList.remove('selecionada');
        }
        
        atualizarPreview();
        atualizarInput();
    }
}

// Função para limpar todo o look
function limparLook() {
    if (confirm('Tem certeza que deseja limpar todas as peças do look?')) {
        pecasSelecionadas = [];
        
        document.querySelectorAll('.peca-item.selecionada').forEach(item => {
            item.classList.remove('selecionada');
        });
        
        atualizarPreview();
        atualizarInput();
    }
}

// Função para atualizar o preview do look
function atualizarPreview() {
    const preview = document.getElementById('lookPreview');
    const empty = document.getElementById('lookEmpty');
    const btnSalvar = document.getElementById('btnSalvarLook');
    const contador = document.querySelector('.area-montagem h3 span');
    
    // Atualizar contador
    if (contador) {
        contador.textContent = `(${pecasSelecionadas.length} peças)`;
    }
    
    if (pecasSelecionadas.length === 0) {
        // Look vazio
        preview.innerHTML = `
            <div class="look-empty" id="lookEmpty">
                <i class="fas fa-tshirt"></i>
                <p>Clique nas peças ao lado para montar seu look</p>
                <p style="font-size: 0.85rem;">Selecione as peças que deseja combinar</p>
            </div>
        `;
        btnSalvar.disabled = true;
        return;
    }
    
    // Buscar informações das peças selecionadas
    const todasPecas = document.querySelectorAll('.peca-item');
    let html = '';
    
    pecasSelecionadas.forEach(id => {
        const item = document.querySelector(`.peca-item[data-id="${id}"]`);
        if (item) {
            const nome = item.querySelector('.peca-nome').textContent;
            const marca = item.querySelector('.peca-detalhe').textContent.split('•')[0].trim();
            html += `
                <div class="look-preview-item" data-id="${id}">
                    <span style="font-size: 1.2rem;">👔</span>
                    <span><strong>${nome}</strong> 
                    <span style="color: var(--texto-claro); font-size: 0.85rem;">
                        ${marca}
                    </span></span>
                    <button type="button" class="remove-peca" onclick="removerPecaDoLook(${id})" title="Remover do look">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>
            `;
        }
    });
    
    preview.innerHTML = html;
    btnSalvar.disabled = false;
}

// Função para atualizar o input hidden
function atualizarInput() {
    document.getElementById('pecasSelecionadas').value = pecasSelecionadas.join(',');
}

// Função para filtrar peças
function filtrarPecas() {
    const termo = document.getElementById('searchPecas').value.toLowerCase();
    const items = document.querySelectorAll('.peca-item');
    
    items.forEach(item => {
        const nome = item.dataset.nome || '';
        const marca = item.dataset.marca || '';
        const categoria = item.dataset.categoria || '';
        
        const match = nome.includes(termo) || marca.includes(termo) || categoria.includes(termo);
        item.style.display = match ? '' : 'none';
    });
}

// Validação antes de enviar o formulário
document.getElementById('formLook').addEventListener('submit', function(e) {
    const nome = document.getElementById('nome_look').value.trim();
    if (!nome) {
        e.preventDefault();
        alert('Por favor, dê um nome para seu look!');
        return;
    }
    
    if (pecasSelecionadas.length === 0) {
        e.preventDefault();
        alert('Selecione pelo menos uma peça para montar o look!');
        return;
    }
});

// Atualizar preview ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    // Já está tudo configurado
});
</script>

<?php include 'includes/footer.php'; ?>