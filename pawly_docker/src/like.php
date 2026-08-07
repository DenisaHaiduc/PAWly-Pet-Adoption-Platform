<?php
session_start();
require 'config.php';

// Returnăm JSON pentru AJAX
header('Content-Type: application/json');

if (!isset($_POST['id']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Neautorizat']);
    exit();
}

$anunt_id = (int)$_POST['id'];
$user_id = (int)$_SESSION['user_id'];

// Verificăm dacă utilizatorul a dat deja like
$stmt = $pdo->prepare("SELECT id FROM likes_utilizatori WHERE utilizator_id = ? AND anunt_id = ?");
$stmt->execute([$user_id, $anunt_id]);
$like_existent = $stmt->fetch();

if ($like_existent) {
    // Există deja like → îl retragem (UNLIKE)
    $pdo->prepare("DELETE FROM likes_utilizatori WHERE utilizator_id = ? AND anunt_id = ?")->execute([$user_id, $anunt_id]);
    $pdo->prepare("UPDATE anunturi SET likes = likes - 1 WHERE id = ? AND likes > 0")->execute([$anunt_id]);
    $liked = false;
} else {
    // Nu există like → îl adăugăm (LIKE)
    $pdo->prepare("INSERT INTO likes_utilizatori (utilizator_id, anunt_id) VALUES (?, ?)")->execute([$user_id, $anunt_id]);
    $pdo->prepare("UPDATE anunturi SET likes = likes + 1 WHERE id = ?")->execute([$anunt_id]);
    $liked = true;
}

// Returnăm noul număr de like-uri și starea
$stmt = $pdo->prepare("SELECT likes FROM anunturi WHERE id = ?");
$stmt->execute([$anunt_id]);
$numar_likes = $stmt->fetchColumn();

echo json_encode([
    'likes' => (int)$numar_likes,
    'liked' => $liked
]);
?>