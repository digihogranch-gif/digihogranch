<?php

session_start();
if (!isset($_SESSION['usuario_id'])) { 
    header('Location: registro.php'); 
    exit(); 
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=digihog_ranch;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    require_once 'controllers/AcercaController.php';
    $controller = new AcercaController($pdo);
    $controller->procesar();

} catch (PDOException $e) {
    die("Error en el sistema: " . $e->getMessage());
}