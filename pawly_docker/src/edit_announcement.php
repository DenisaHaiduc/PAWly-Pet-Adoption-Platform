<?php
session_start();
require 'config.php';

// 1. Verificăm dacă utilizatorul este logat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id']; // Luăm ID-ul anunțului din URL

// 2. PRECOMPLETARE: Preluăm datele actuale din baza de date pentru a le pune în formular
$stmt = $pdo->prepare("SELECT * FROM anunturi WHERE id = ?");
$stmt->execute([$id]);
$anunt = $stmt->fetch();

if (!$anunt) {
    die("Anunțul nu a fost găsit!");
}

// 3. ACTUALIZARE: Dacă formularul a fost trimis, salvăm noile date
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titlu = $_POST['titlu'];
    $specie = $_POST['specie'];
    $descriere = $_POST['descriere'];
    $pret = $_POST['pret'];
    $imagine_noua = $anunt['imagine']; // Păstrăm imaginea veche implicit

    // Dacă utilizatorul a ales o imagine nouă
    if (!empty($_FILES['imagine']['name'])) {
        // Ștergem imaginea veche de pe server pentru a nu ocupa spațiu degeaba
        if (file_exists($anunt['imagine'])) {
            unlink($anunt['imagine']);
        }
        
        // Salvăm imaginea nouă
        $imagine_noua = "uploads/" . time() . "_" . $_FILES['imagine']['name'];
        move_uploaded_file($_FILES['imagine']['tmp_name'], $imagine_noua);
    }

    // Executăm comanda UPDATE în SQL
    $sql = "UPDATE anunturi SET titlu = ?, specie = ?, descriere = ?, pret = ?, imagine = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$titlu, $specie, $descriere, $pret, $imagine_noua, $id]);

    header("Location: index.php"); // Ne întoarcem la pagina principală după salvare
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editare Anunț - PAWly</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Editează Anunțul</h2>
    
    <form method="POST" enctype="multipart/form-data">
        <label>Titlu:</label><br>
        <input type="text" name="titlu" value="<?php echo htmlspecialchars($anunt['titlu']); ?>" required><br>

        <label>Specie:</label><br>
        <input type="text" name="specie" value="<?php echo htmlspecialchars($anunt['specie']); ?>" required><br>

        <label>Descriere:</label><br>
        <textarea name="descriere" required><?php echo htmlspecialchars($anunt['descriere']); ?></textarea><br>

        <label>Preț:</label><br>
        <input type="text" name="pret" value="<?php echo htmlspecialchars($anunt['pret']); ?>"><br>

        <label>Imagine curentă:</label><br>
        <img src="<?php echo $anunt['imagine']; ?>" width="100"><br>
        
        <label>Schimbă imaginea (opțional):</label><br>
        <input type="file" name="imagine"><br><br>

        <button type="submit">Salvează Modificările</button>
        <a href="index.php">Anulează</a>
    </form>
</body>
</html>