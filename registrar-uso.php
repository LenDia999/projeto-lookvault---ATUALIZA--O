<?php
require_once 'configdatabase.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $peca_id = $_POST['peca_id'] ?? 0;
    $data_uso = $_POST['data_uso'] ?? date('Y-m-d');
    
    if ($peca_id > 0) {
        $sql = "INSERT INTO uso_pecas (peca_id, data_uso) VALUES (?, ?)";
        $result = executarQuery($sql, [$peca_id, $data_uso]);
        
        if ($result) {
            echo json_encode(['sucesso' => true, 'mensagem' => 'Uso registrado com sucesso!']);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao registrar uso']);
        }
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'ID da peça inválido']);
    }
}
?>