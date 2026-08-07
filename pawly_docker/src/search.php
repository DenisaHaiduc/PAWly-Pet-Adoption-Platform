<?php
require 'config.php';
$search = $_GET['q'] ?? '';

$sql = "SELECT * FROM anunturi WHERE titlu LIKE ? OR specie LIKE ?";
$stmt = $pdo->prepare($sql);
$stmt->execute(["%$search%", "%$search%"]);
$rezultate = $stmt->fetchAll();

if (!$rezultate) {
    echo "<p>Nu am găsit niciun animal cu acest nume.</p>";
}

foreach ($rezultate as $anunt) {
    echo "
    <div class='card-anunt'>
        <img src='{$anunt['imagine']}' width='150'>
        <h3>{$anunt['titlu']}</h3>
        <button class='like-btn' data-id='{$anunt['id']}'>❤️ <span class='count'>{$anunt['likes']}</span></button>
    </div>";
}
?>