<?php
session_start();
require 'config.php';

$mesaj = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['parola'];

    $sql = "SELECT * FROM utilizatori WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user]);
    $utilizator = $stmt->fetch();

    // Verificăm dacă utilizatorul există și dacă parola (criptată anterior) este corectă
    if ($utilizator && password_verify($pass, $utilizator['parola'])) {
        // Salvăm datele esențiale în sesiune
        $_SESSION['user_id'] = $utilizator['id'];
        $_SESSION['username'] = $utilizator['username'];
        $_SESSION['rol'] = $utilizator['rol'];

        header("Location: index.php");
        exit();
    } else {
        $mesaj = "Utilizator sau parolă incorectă!";
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAWly - Autentificare</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="login.css">
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
        .grup-input input,
        .grup-input select {
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
        .grup-input input:focus,
        .grup-input select:focus {
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
            animation: shake 0.4s ease;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            75% { transform: translateX(8px); }
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

        /* ═══ LAYOUT CU WIDGETS LATERAL ═══ */
        .login-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 30px;
            align-items: start;
        }
        .login-layout .form-container {
            margin: 0;
        }
        @media (max-width: 900px) {
            .login-layout {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <header>
        <h1>Autentificare PAWly</h1>
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
            <div class="login-layout">

                <!-- ═══ FORMULARUL (stil identic cu register.php) ═══ -->
                <div class="form-container">
                    <div class="emoji-header">🔐</div>
                    <h2>Intră în contul tău</h2>
                    <p class="subtitlu">Bine ai revenit pe PAWly</p>

                    <?php if (!empty($mesaj)): ?>
                        <div class="mesaj-eroare"><?php echo htmlspecialchars($mesaj); ?></div>
                    <?php endif; ?>

                    <form id="form-autentificare" method="POST">
                        <div class="grup-input">
                            <label for="username">👤 Nume utilizator</label>
                            <input type="text" id="username" name="username" placeholder="Introdu numele de utilizator" required>
                        </div>

                        <div class="grup-input">
                            <label for="parola">🔑 Parolă</label>
                            <input type="password" id="parola" name="parola" placeholder="Introdu parola" required>
                        </div>

                        <div class="grup-input">
                            <label for="rol">🎭 Te autentifici ca</label>
                            <select id="rol" name="rol">
                                <option value="proprietar">🐾 Proprietar de animal</option>
                                <option value="adoptator">🏠 Căutător (Adopție)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-submit">Autentificare</button>
                    </form>

                    <div class="separator"><span>sau</span></div>

                    <div class="link-secundar">
                        Nu ai cont? <a href="register.php">Înregistrează-te aici 🐾</a>
                    </div>
                </div>

                <!-- ═══ PANOUL CU WIDGET-URI (SFATURI/STATISTICI) ═══ -->
                <div class="widgets-panel">
                    <div class="widget">
                        <p class="widget-title">📊 Statistici Platformă</p>
                        <div class="stats-row">
                            <div class="stat-card">
                                <div class="stat-number">1.240</div>
                                <div class="stat-label">Anunțuri active</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number">387</div>
                                <div class="stat-label">Adopții reușite</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number">56</div>
                                <div class="stat-label">Medici parteneri</div>
                            </div>
                        </div>
                        <div class="progress-bar-wrap">
                            <div class="progress-label">
                                <span>🎯 Obiectiv lunar adopții</span>
                                <span>387 / 500</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill"></div>
                            </div>
                        </div>
                    </div>

                    <div class="widget">
                        <p class="widget-title">👤 Ce poți face pe PAWly</p>
                        <div class="role-cards">
                            <div class="role-card">
                                <span class="role-emoji">🐾</span>
                                <div class="role-info">
                                    <strong>Proprietar</strong>
                                    <span>Publică anunțuri, gestionează animalele tale</span>
                                </div>
                            </div>
                            <div class="role-card">
                                <span class="role-emoji">🏠</span>
                                <div class="role-info">
                                    <strong>Adoptator</strong>
                                    <span>Caută animale, contactează proprietari</span>
                                </div>
                            </div>
                            <div class="role-card">
                                <span class="role-emoji">🩺</span>
                                <div class="role-info">
                                    <strong>Medic Veterinar</strong>
                                    <span>Verifică sănătatea, oferă consultații</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <footer>
        <p>&copy; 2026 PAWly - Creat pentru Laboratorul de Tehnologii Web</p>
    </footer>

</body>
</html>