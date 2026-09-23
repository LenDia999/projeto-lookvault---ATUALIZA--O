<?php
require_once 'configdatabase.php';

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    $sql = "DELETE FROM pecas WHERE id = ?";
    $result = executarQuery($sql, [$id]);
    
    if ($result) {
        header('Location: guarda-roupa.php?sucesso=Peça excluída com sucesso!');
    } else {
        header('Location: guarda-roupa.php?erro=Erro ao excluir peça');
    }
} else {
    header('Location: guarda-roupa.php');
}
?>