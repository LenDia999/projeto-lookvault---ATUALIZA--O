<?php
session_start();
require_once 'configdatabase.php';

// Usar usuário logado ou fallback para 1
$usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 1;

// Total de peças
$sql_total = "SELECT COUNT(*) as total FROM pecas WHERE usuario_id = ?";
$result_total = executarQuery($sql_total, [$usuario_id]);
$total_pecas = $result_total->fetch_assoc()['total'] ?? 0;

// Peças mais usadas (últimos 30 dias)
$sql_mais_usadas = "
    SELECT p.nome, COUNT(u.id) as uso_count 
    FROM pecas p
    LEFT JOIN uso_pecas u ON p.id = u.peca_id 
    WHERE p.usuario_id = ? AND (u.data_uso >= DATE_SUB(NOW(), INTERVAL 30 DAY) OR u.data_uso IS NULL)
    GROUP BY p.id
    ORDER BY uso_count DESC
    LIMIT 5
";
$result_mais_usadas = executarQuery($sql_mais_usadas, [$usuario_id]);

$mais_usadas = [];
if ($result_mais_usadas && $result_mais_usadas->num_rows > 0) {
    while ($row = $result_mais_usadas->fetch_assoc()) {
        $mais_usadas[] = $row;
    }
}

// Custo médio por uso
$sql_custo = "
    SELECT 
        AVG(valor / COALESCE(uso_count, 1)) as custo_medio
    FROM (
        SELECT p.valor, COUNT(u.id) as uso_count
        FROM pecas p
        LEFT JOIN uso_pecas u ON p.id = u.peca_id
        WHERE p.usuario_id = ?
        GROUP BY p.id
    ) as subquery
";
$result_custo = executarQuery($sql_custo, [$usuario_id]);
$custo_medio = $result_custo->fetch_assoc()['custo_medio'] ?? 0;

// Valor total do guarda-roupa
$sql_valor_total = "SELECT SUM(valor) as total FROM pecas WHERE usuario_id = ?";
$result_valor_total = executarQuery($sql_valor_total, [$usuario_id]);
$valor_total = $result_valor_total->fetch_assoc()['total'] ?? 0;

// Estatísticas por categoria
$sql_categorias = "
    SELECT categoria, COUNT(*) as total 
    FROM pecas 
    WHERE usuario_id = ? 
    GROUP BY categoria
    ORDER BY total DESC
";
$result_categorias = executarQuery($sql_categorias, [$usuario_id]);

$categorias = [];
if ($result_categorias && $result_categorias->num_rows > 0) {
    while ($row = $result_categorias->fetch_assoc()) {
        $categorias[] = $row;
    }
}

// Estatísticas por marca
$sql_marcas = "
    SELECT marca, COUNT(*) as total 
    FROM pecas 
    WHERE usuario_id = ? AND marca != ''
    GROUP BY marca
    ORDER BY total DESC
    LIMIT 5
";
$result_marcas = executarQuery($sql_marcas, [$usuario_id]);

$marcas = [];
if ($result_marcas && $result_marcas->num_rows > 0) {
    while ($row = $result_marcas->fetch_assoc()) {
        $marcas[] = $row;
    }
}

// Atividades recentes
$sql_atividades = "
    (SELECT 'Adicionou' as acao, nome as descricao, data_cadastro as data FROM pecas WHERE usuario_id = ?)
    UNION ALL
    (SELECT 'Criou look' as acao, nome as descricao, data_criacao as data FROM looks WHERE usuario_id = ?)
    ORDER BY data DESC
    LIMIT 5
";
$result_atividades = executarQuery($sql_atividades, [$usuario_id, $usuario_id]);

$atividades = [];
if ($result_atividades && $result_atividades->num_rows > 0) {
    while ($row = $result_atividades->fetch_assoc()) {
        $atividades[] = $row;
    }
}

// Retornar todos os dados em JSON
$dados = [
    'total_pecas' => $total_pecas,
    'mais_usadas' => $mais_usadas,
    'custo_medio' => number_format($custo_medio, 2, ',', '.'),
    'valor_total' => number_format($valor_total, 2, ',', '.'),
    'categorias' => $categorias,
    'marcas' => $marcas,
    'atividades' => $atividades
];

header('Content-Type: application/json');
echo json_encode($dados);
?>