<?php
session_start();
require 'config.php';

$mesaj = "";
$tip_mesaj = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['username']);
    $email = trim($_POST['email']);
    $pass_raw = $_POST['password'];

    if (strlen($user) < 3) {
        $mesaj = "Numele de utilizator trebuie să aibă minim 3 caractere!";
        $tip_mesaj = "eroare";
    } elseif (strlen($pass_raw) < 5) {
        $mesaj = "Parola trebuie să aibă minim 5 caractere!";
        $tip_mesaj = "eroare";
    } else {
        $pass = password_hash($pass_raw, PASSWORD_DEFAULT);
        $rol = 'utilizator';

        try {
            $sql = "INSERT INTO utilizatori (username, email, parola, rol) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user, $email, $pass, $rol]);
            $mesaj = "Înregistrare reușită! Te poți autentifica acum.";
            $tip_mesaj = "succes";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $mesaj = "Acest nume de utilizator sau email există deja!";
            } else {
                $mesaj = "Eroare la înregistrare: " . $e->getMessage();
            }
            $tip_mesaj = "eroare";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Înregistrare - PAWly</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .form-container {
            max-width: 460px;
            margin: 60px auto;
            background: var(--alb-pur);
            border-radius: var(--raza-colturi);
            box-shadow: var(--umbra-plutire);
            padding: 45px 40px;
            animation: fadeSlideUp 0.6s ease both;
        }
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .form-container h2 {
            text-align: center;
            color: var(--mov-fundal);
            font-size: 1.8rem;
            margin-bottom: 8px;
        }
        .form-container .subtitlu {
            text-align: center;
            color: #999;
            font-size: 0.9rem;
            margin-bottom: 30px;
        }
        .form-container .emoji-header {
            text-align: center;
            font-size: 3rem;
            margin-bottom: 10px;
        }
        .grup-input {
            margin-bottom: 20px;
        }
        .grup-input label {
            display: block;
            font-weight: bold;
            color: #555;
            margin-bottom: 6px;
            font-size: 0.9rem;
        }
        .grup-input input {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid var(--lila-deschis);
            border-radius: 12px;
            font-size: 1rem;
            background-color: var(--lila-foarte-stins);
            transition: border-color 0.3s, box-shadow 0.3s;
            outline: none;
            font-family: inherit;
        }
        .grup-input input:focus {
            border-color: var(--mov-fundal);
            box-shadow: 0 0 0 4px rgba(149, 92, 143, 0.15);
        }
        .btn-submit {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 25px;
            background: linear-gradient(135deg, #4d1647, var(--mov-fundal));
            color: white;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.3s;
            margin-top: 10px;
        }
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(77, 22, 71, 0.35);
            background: linear-gradient(135deg, var(--mov-fundal), var(--portocaliu-accent));
        }
        .mesaj-eroare {
            background-color: #ffe0e0;
            color: #c0392b;
            padding: 12px 18px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 500;
            border-left: 4px solid #e74c3c;
        }
        .mesaj-succes {
            background-color: #e0ffe0;
            color: #27ae60;
            padding: 12px 18px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 500;
            border-left: 4px solid #2ecc71;
        }
        .link-secundar {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9rem;
            color: #777;
        }
        .link-secundar a {
            color: var(--mov-fundal);
            font-weight: bold;
            text-decoration: none;
            transition: color 0.3s;
        }
        .link-secundar a:hover {
            color: var(--portocaliu-accent);
            text-decoration: underline;
        }
        .separator {
            display: flex;
            align-items: center;
            margin: 25px 0;
            color: #ccc;
            font-size: 0.85rem;
        }
        .separator::before,
        .separator::after {
            content: "";
            flex: 1;
            height: 1px;
            background: var(--lila-deschis);
        }
        .separator span { padding: 0 15px; }
        .nota-parola {
            font-size: 0.78rem;
            color: #aaa;
            margin-top: 4px;
        }
    </style>
</head>
<body>

    <header>
        <h1>Bun venit pe PAWly</h1>
        <figure>
            <img src="PAWLY.png" alt="Logo PAWly" id="logo-site">
            <figcaption>Prietenul tău cel mai bun te așteaptă aici</figcaption>
        </figure>
    </header>

    <div class="layout-principal">
        <nav class="meniu-vertical">
            <h2>Navigație</h2>
            <ul>
                <li><a href="index.php">Acasă</a></li>
                <li class="are-submeniu">
                    <a href="#">Categorii Animale</a>
                    <ul class="submeniu">
                        <li><a href="#">Câini</a></li>
                        <li><a href="#">Pisici</a></li>
                        <li><a href="#">Exotice</a></li>
                    </ul>
                </li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="add_announcement.php">Adaugă Anunț</a></li>
                    <li><a href="logout.php" style="color: #ff4d4d;">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                <?php else: ?>
                    <li><a href="login.php">Autentificare</a></li>
                    <li><a href="register.php">Înregistrare</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <main class="continut">
            <div class="form-container">
                <div class="emoji-header">🐾</div>
                <h2>Creează un cont</h2>
                <p class="subtitlu">Alătură-te comunității PAWly</p>

                <?php if (!empty($mesaj)): ?>
                    <div class="mesaj-<?php echo $tip_mesaj; ?>"><?php echo htmlspecialchars($mesaj); ?></div>
                <?php endif; ?>

                <form method="POST" id="form-register">
                    <div class="grup-input">
                        <label for="username">👤 Nume utilizator</label>
                        <input type="text" name="username" id="username" placeholder="Alege un nume de utilizator" required
                               value="<?php echo isset($user) ? htmlspecialchars($user) : ''; ?>">
                    </div>
                    <div class="grup-input">
                        <label for="email">📧 Adresă de email</label>
                        <input type="email" name="email" id="email" placeholder="exemplu@email.com" required
                               value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                    </div>
                    <div class="grup-input">
                        <label for="password">🔑 Parolă</label>
                        <input type="password" name="password" id="password" placeholder="Alege o parolă sigură" required>
                        <p class="nota-parola">Minim 5 caractere</p>
                    </div>
                    <button type="submit" class="btn-submit">Creează contul</button>
                </form>

                <div class="separator"><span>sau</span></div>
                <div class="link-secundar">
                    Ai deja cont? <a href="login.php">Autentifică-te aici 🔐</a>
                </div>
            </div>
        </main>
    </div>

    <footer>
        <p>&copy; 2026 PAWly - Creat pentru Laboratorul de Tehnologii Web</p>
    </footer>

</body>
</html>