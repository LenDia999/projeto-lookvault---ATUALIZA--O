<?php
require_once 'configdatabase.php';

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    // Primeiro excluir as relações
    $sql_rel = "DELETE FROM look_pecas WHERE look_id = ?";
    executarQuery($sql_rel, [$id]);
    
    // Depois excluir o look
    $sql = "DELETE FROM looks WHERE id = ?";
    $result = executarQuery($sql, [$id]);
    
    if ($result) {
        header('Location: combinacoes.php?sucesso=Look excluído com sucesso!');
    } else {
        header('Location: combinacoes.php?erro=Erro ao excluir look');
    }
} else {
    header('Location: combinacoes.php');
}
?>