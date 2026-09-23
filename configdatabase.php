<?php
// Configuração do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'lookvault');

// Conexão com o banco
function conectarDB() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            throw new Exception("Erro na conexão: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8");
        return $conn;
    } catch (Exception $e) {
        die("Erro ao conectar ao banco de dados: " . $e->getMessage());
    }
}

// Função para executar queries com segurança
function executarQuery($sql, $params = []) {
    $conn = conectarDB();
    
    if (empty($params)) {
        $result = $conn->query($sql);
        $conn->close();
        return $result;
    }
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        $conn->close();
        return false;
    }
    
    if (!empty($params)) {
        $types = '';
        $values = [];
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
            $values[] = $param;
        }
        $stmt->bind_param($types, ...$values);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $conn->close();
    
    return $result;
}

// Função para inserir e retornar o ID
function inserirRetornarId($sql, $params = []) {
    $conn = conectarDB();
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        $conn->close();
        return false;
    }
    
    if (!empty($params)) {
        $types = '';
        $values = [];
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
            $values[] = $param;
        }
        $stmt->bind_param($types, ...$values);
    }
    
    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();
    $conn->close();
    
    return $id;
}
?>