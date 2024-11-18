<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=cadastrar_pessoas', 'root', '123123');
    echo "Conexão com o banco de dados bem-sucedida!";
} catch (PDOException $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
}
