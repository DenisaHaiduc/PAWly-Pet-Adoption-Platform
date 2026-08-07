<?php
require 'config.php';
$id = $_GET['id'];

// Preluăm calea imaginii înainte de ștergere pentru a o putea elimina de pe disc [cite: 15]
$stmt = $pdo->prepare("SELECT imagine FROM anunturi WHERE id = ?");
$stmt->execute([$id]);
$anunt = $stmt->fetch();

if ($anunt) {
    if (file_exists($anunt['imagine'])) {
        unlink($anunt['imagine']); // Ștergerea fișierului de pe server 
    }
    $pdo->prepare("DELETE FROM anunturi WHERE id = ?")->execute([$id]); // Ștergere din DB [cite: 12]
}
header("Location: index.php");