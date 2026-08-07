<?php
session_start();
require 'config.php';

// Preluăm anunțurile inițiale pentru prima afișare
$stmt = $pdo->query("SELECT * FROM anunturi ORDER BY id DESC");
$anunturi = $stmt->fetchAll();

// Preluăm like-urile utilizatorului logat (pentru toggle vizual)
$liked_ids = [];
if (isset($_SESSION['user_id'])) {
    $stmt_likes = $pdo->prepare("SELECT anunt_id FROM likes_utilizatori WHERE utilizator_id = ?");
    $stmt_likes->execute([$_SESSION['user_id']]);
    $liked_ids = $stmt_likes->fetchAll(PDO::FETCH_COLUMN);
}

$username_display = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Vizitator';
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAWly - Platforma ta de Adopții</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="script-jquery.js"></script>
    <style>
        /* ═══ RESET LAYOUT — suprascrie style.css ═══ */
        body {
            margin: 0; padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--mov-fundal);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ═══ STICKY TOP BAR ═══ */
        .top-bar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: linear-gradient(135deg, #4d1647 0%, var(--mov-fundal) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 35px;
            min-height: 80px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        .top-bar-left {
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .top-bar-left img {
            width: 60px;
            height: 60px;
            border: 3px solid var(--portocaliu-accent);
            border-radius: 50%;
            object-fit: contain;
            background: rgba(50, 38, 51, 0.9);
            padding: 3px;
            cursor: pointer;
            transition: transform 0.4s;
        }
        .top-bar-left img:hover {
            transform: rotate(360deg) scale(1.15);
        }
        .top-bar-left .salut {
            font-size: 1.2rem;
            font-weight: 500;
            line-height: 1.4;
        }
        .top-bar-left .salut strong {
            color: var(--portocaliu-accent);
            font-size: 1.3rem;
        }

        /* Mini galerie în top bar */
        .top-bar-gallery {
            width: 150px;
            height: 50px;
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid rgba(255,255,255,0.3);
            position: relative;
        }
        .top-bar-gallery .mini-slider {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .top-bar-gallery .mini-slider li {
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .top-bar-gallery .mini-slider img,
        .top-bar-gallery .mini-slider video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* ═══ PAGINA: SIDEBAR + MAIN ═══ */
        .page-layout {
            display: flex;
            flex: 1;
            gap: 25px;
            padding: 25px;
            align-items: flex-start;
        }

        /* ═══ SIDEBAR — Card plutitor ═══ */
        .page-layout .meniu-vertical {
            width: 240px;
            min-height: auto;
            height: auto;
            align-self: flex-start;
            border-radius: var(--raza-colturi, 16px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            padding: 20px 0;
            position: sticky;
            top: 95px;
        }

        /* ═══ MAIN CONTENT ═══ */
        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        /* Carduri anunțuri */
        .actions { margin-top: 15px; padding: 10px; border-top: 1px solid #7e128fff; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .like-btn { cursor: pointer; border: none; font-size: 1.2rem; transition: transform 0.2s, background-color 0.3s; display: inline-flex; align-items: center; gap: 5px; padding: 6px 14px; border-radius: 20px; background-color: var(--lila-foarte-stins); }
        .like-btn:hover { transform: scale(1.1); background-color: var(--lila-deschis); }
        .like-btn .heart { transition: transform 0.3s; }
        .like-btn.liked { background-color: #fde8e8; }
        .like-btn.liked .heart { transform: scale(1.2); }
        .like-btn .count { color: var(--mov-fundal); font-weight: bold; font-size: 1rem; }
        .btn-edit { color: #4CAF50; text-decoration: none; margin-left: 10px; font-weight: bold; }
        .btn-delete { color: #f44336; text-decoration: none; margin-left: 10px; font-weight: bold; }

        /* ═══ QUIZ OVERLAY ═══ */
        .creative-quiz {
            position: fixed;
            inset: 0;
            z-index: 2000;
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* ═══ QUIZ CARD DESIGN ═══ */
        .quiz-card {
            background: var(--alb-pur, #fff);
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(77, 22, 71, 0.3);
            padding: 40px;
            max-width: 650px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            animation: quizSlideIn 0.5s ease both;
        }
        @keyframes quizSlideIn {
            from { opacity: 0; transform: translateY(30px) scale(0.95); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .quiz-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .quiz-header h2 {
            color: var(--mov-fundal);
            font-size: 1.6rem;
            margin: 0 0 6px;
        }
        .quiz-subtitle {
            color: #999;
            font-size: 0.9rem;
            margin: 0 0 20px;
        }

        /* Bară de progres */
        .quiz-progress {
            width: 100%;
            height: 8px;
            background: var(--lila-deschis, #d9b8d4);
            border-radius: 99px;
            overflow: hidden;
        }
        .quiz-progress-bar {
            height: 100%;
            width: 20%;
            background: linear-gradient(90deg, var(--mov-fundal), var(--portocaliu-accent, #f0883f));
            border-radius: 99px;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .quiz-progress-text {
            display: inline-block;
            margin-top: 8px;
            font-size: 0.8rem;
            color: #aaa;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* Întrebări */
        .quiz-question-icon {
            font-size: 3rem;
            text-align: center;
            margin-bottom: 10px;
            animation: bounceIcon 0.6s ease;
        }
        @keyframes bounceIcon {
            0%   { transform: scale(0.3); opacity: 0; }
            50%  { transform: scale(1.15); }
            100% { transform: scale(1); opacity: 1; }
        }
        .quiz-question {
            text-align: center;
            font-size: 1.2rem;
            color: #444;
            font-weight: 600;
            margin-bottom: 25px;
        }

        /* Butoane quiz */
        .quiz-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .quiz-card .quiz-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 25px 20px;
            border: 2px solid var(--lila-deschis, #d9b8d4);
            border-radius: 16px;
            background: var(--lila-foarte-stins, #f5edf4);
            cursor: pointer;
            font-family: inherit;
            transition: all 0.3s ease;
        }
        .quiz-card .quiz-btn:hover {
            border-color: var(--mov-fundal);
            background: white;
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(149, 92, 143, 0.25);
        }
        .quiz-card .quiz-btn:active {
            transform: scale(0.97);
        }
        .qb-icon {
            font-size: 2rem;
        }
        .qb-text {
            font-size: 0.95rem;
            font-weight: 600;
            color: #555;
        }

        /* Panou rezultat */
        .result-box {
            text-align: center;
            position: relative;
            overflow: hidden;
            padding: 20px 0;
        }
        .result-emoji {
            font-size: 5rem;
            animation: resultPulse 1.5s ease infinite alternate;
        }
        @keyframes resultPulse {
            from { transform: scale(1); }
            to   { transform: scale(1.1); }
        }
        .result-box h3 {
            color: var(--mov-fundal);
            font-size: 1.1rem;
            margin: 15px 0 5px;
        }
        .result-animal {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--mov-fundal);
            margin-bottom: 10px;
            background: linear-gradient(135deg, #4d1647, var(--portocaliu-accent, #f0883f));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .result-mesaj {
            color: #777;
            font-size: 0.95rem;
            max-width: 450px;
            margin: 0 auto 25px;
            line-height: 1.6;
        }
        .restart-btn {
            flex-direction: row !important;
            padding: 14px 30px !important;
            max-width: 220px;
            margin: 0 auto;
        }

        /* Sclipici / confetti */
        .result-sparkles {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .sparkle {
            position: absolute;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            animation: sparkleFloat 2s ease forwards;
            opacity: 0;
        }
        @keyframes sparkleFloat {
            0%   { opacity: 1; transform: translateY(0) scale(1); }
            100% { opacity: 0; transform: translateY(-120px) scale(0); }
        }

        /* Ascundem header-ul vechi din style.css */
        body > header { display: none; }

        /* Footer */
        footer {
            background-color: rgba(0, 0, 0, 0.4);
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 0.85rem;
        }

        /* ═══ RESPONSIVE ═══ */
        @media (max-width: 768px) {
            .top-bar { padding: 15px; min-height: auto; }
            .top-bar-gallery { display: none; }
            .page-layout { flex-direction: column; }
            .page-layout .meniu-vertical { width: 100%; border-radius: 10px; }
            .main-content { padding: 15px; }
        }
    </style>
</head>
<body>

    <!-- ═══ STICKY TOP BAR ═══ -->
    <div class="top-bar">
        <div class="top-bar-left">
            <img src="PAWLY.png" alt="Logo PAWly" id="logo-site">
            <span class="salut">👋 Salut, <strong><?php echo $username_display; ?></strong></span>
        </div>
        <div class="top-bar-gallery">
            <ul class="mini-slider" id="mini-slider">
                <li><img src="dog_1.jpg" alt="Câine"></li>
                <li><img src="cat_1.jpg" alt="Pisică"></li>
                <li>
                    <video muted loop playsinline autoplay>
                        <source src="vecteezy_portrait-of-a-dog-golden-retriever-play-outdoors-on-a-sunny_9542081.mp4" type="video/mp4">
                    </video>
                </li>
            </ul>
        </div>
    </div>

    <!-- ═══ PAGE LAYOUT: SIDEBAR + MAIN ═══ -->
    <div class="page-layout">

        <!-- SIDEBAR — Stilul original -->
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

            <div class="searchable-select-container">
                <h3>🔍 Căutare Live (AJAX)</h3>
                <input type="text" id="live-search-input" placeholder="Caută (ex: Max)..." autocomplete="off">
                <ul id="live-search-list">
                    <li>Toate</li>
                    <li>Caine</li>
                    <li>Pisica</li>
                    <li>Papagal</li>
                    <li>Peste</li>
                    <li>Capra</li>
                </ul>
            </div>
        </nav>

        <!-- MAIN CONTENT -->
        <main class="main-content">

            <section id="vizualizare">
                <h2>Anunțuri Recente</h2>

                <div id="resultsContainer">
                    <?php if (empty($anunturi)): ?>
                        <p>Nu există anunțuri în baza de date.</p>
                    <?php else: ?>
                        <?php foreach ($anunturi as $anunt): ?>
                            <article class="card-anunt">
                                <header><h3><?php echo htmlspecialchars($anunt['titlu']); ?></h3></header>
                                <div class="slider-container">
                                    <img src="<?php echo $anunt['imagine']; ?>" class="imagine-mare" alt="Animal">
                                </div>
                                <table class="tabel-stilizat">
                                    <thead><tr><th>Caracteristică</th><th>Specificații</th></tr></thead>
                                    <tbody>
                                        <tr><td>Specie</td><td><?php echo htmlspecialchars($anunt['specie']); ?></td></tr>
                                        <tr><td>Preț</td><td><?php echo htmlspecialchars($anunt['pret']); ?> RON</td></tr>
                                        <tr><td>Descriere</td><td><?php echo htmlspecialchars($anunt['descriere']); ?></td></tr>
                                    </tbody>
                                </table>

                                <div class="actions">
                                    <button class="like-btn<?php echo in_array($anunt['id'], $liked_ids) ? ' liked' : ''; ?>" data-id="<?php echo $anunt['id']; ?>">
                                        <span class="heart"><?php echo in_array($anunt['id'], $liked_ids) ? '❤️' : '🤍'; ?></span> <span class="count"><?php echo $anunt['likes']; ?></span>
                                    </button>

                                    <?php if(isset($_SESSION['user_id'])): ?>
                                        <?php if($_SESSION['rol'] == 'admin' || $_SESSION['user_id'] == $anunt['utilizator_id']): ?>
                                            <a href="edit_announcement.php?id=<?php echo $anunt['id']; ?>" class="btn-edit">📝 Editează</a>
                                            <a href="delete_announcement.php?id=<?php echo $anunt['id']; ?>" class="btn-delete" onclick="return confirm('Ștergi anunțul?')">🗑️ Șterge</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <footer>
        <p>&copy; 2026 PAWly - Creat pentru Laboratorul de Tehnologii Web</p>
    </footer>

    <!-- ═══ QUIZ OVERLAY — outside page-layout for proper position:fixed ═══ -->
    <section class="creative-quiz" id="creative-quiz" style="display: none;">
        <div class="quiz-card">
            <!-- Header cu progress -->
            <div class="quiz-header">
                <h2>🐾 Găsește partenerul perfect</h2>
                <p class="quiz-subtitle">Răspunde la 5 întrebări și îți vom recomanda animalul ideal!</p>
                <div class="quiz-progress">
                    <div class="quiz-progress-bar" id="quiz-progress-bar"></div>
                </div>
                <span class="quiz-progress-text" id="quiz-progress-text">Întrebarea 1 din 5</span>
            </div>

            <!-- Întrebarea 1 -->
            <div class="quiz-step active" data-step="1">
                <div class="quiz-question-icon">🏠</div>
                <p class="quiz-question">Unde va locui viitorul tău prieten?</p>
                <div class="quiz-options">
                    <button class="quiz-btn" data-val="apartament">
                        <span class="qb-icon">🏢</span>
                        <span class="qb-text">Apartament</span>
                    </button>
                    <button class="quiz-btn" data-val="curte">
                        <span class="qb-icon">🏡</span>
                        <span class="qb-text">Casă cu curte</span>
                    </button>
                </div>
            </div>

            <!-- Întrebarea 2 -->
            <div class="quiz-step" data-step="2" style="display:none;">
                <div class="quiz-question-icon">⏰</div>
                <p class="quiz-question">Cât timp poți dedica zilnic animalului?</p>
                <div class="quiz-options">
                    <button class="quiz-btn" data-val="putin">
                        <span class="qb-icon">⚡</span>
                        <span class="qb-text">Sub 30 minute</span>
                    </button>
                    <button class="quiz-btn" data-val="mult">
                        <span class="qb-icon">☀️</span>
                        <span class="qb-text">Peste 1 oră</span>
                    </button>
                </div>
            </div>

            <!-- Întrebarea 3 -->
            <div class="quiz-step" data-step="3" style="display:none;">
                <div class="quiz-question-icon">🎓</div>
                <p class="quiz-question">Ai mai avut animale de companie?</p>
                <div class="quiz-options">
                    <button class="quiz-btn" data-val="primul">
                        <span class="qb-icon">🌱</span>
                        <span class="qb-text">E primul meu animal</span>
                    </button>
                    <button class="quiz-btn" data-val="experimentat">
                        <span class="qb-icon">🏆</span>
                        <span class="qb-text">Am experiență</span>
                    </button>
                </div>
            </div>

            <!-- Întrebarea 4 -->
            <div class="quiz-step" data-step="4" style="display:none;">
                <div class="quiz-question-icon">🔋</div>
                <p class="quiz-question">Ce nivel de energie preferi?</p>
                <div class="quiz-options">
                    <button class="quiz-btn" data-val="lenes">
                        <span class="qb-icon">😴</span>
                        <span class="qb-text">Leneș și calm</span>
                    </button>
                    <button class="quiz-btn" data-val="activ">
                        <span class="qb-icon">🏃</span>
                        <span class="qb-text">Foarte activ</span>
                    </button>
                </div>
            </div>

            <!-- Întrebarea 5 -->
            <div class="quiz-step" data-step="5" style="display:none;">
                <div class="quiz-question-icon">👨‍👩‍👧</div>
                <p class="quiz-question">Ai copii mici sau alte animale în casă?</p>
                <div class="quiz-options">
                    <button class="quiz-btn" data-val="da_copii">
                        <span class="qb-icon">👶</span>
                        <span class="qb-text">Da, am</span>
                    </button>
                    <button class="quiz-btn" data-val="nu_copii">
                        <span class="qb-icon">🧑</span>
                        <span class="qb-text">Nu, doar eu</span>
                    </button>
                </div>
            </div>

            <!-- Panoul de rezultat -->
            <div id="quiz-result-panel" style="display:none;">
                <div class="result-box">
                    <div class="result-sparkles" id="sparkle-container"></div>
                    <div class="result-emoji" id="result-emoji">🐕</div>
                    <h3>✨ Recomandarea noastră:</h3>
                    <div class="result-animal" id="animal-recomandat">Golden Retriever</div>
                    <p class="result-mesaj" id="mesaj-personalizat">Descriere</p>
                    <button id="restart-quiz" class="quiz-btn restart-btn">
                        <span class="qb-icon">🔄</span>
                        <span class="qb-text">Reia Quiz-ul</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <script>
    $(document).ready(function() {

        // 1. SEARCH LIVE (AJAX)
        $('#live-search-input').on('keyup', function() {
            let query = $(this).val();
            $.ajax({
                url: 'search.php',
                method: 'GET',
                data: { q: query },
                success: function(data) {
                    $('#resultsContainer').html(data);
                }
            });
        });

        // 2. LIKE TOGGLE (AJAX)
        $(document).on('click', '.like-btn', function() {
            let btn = $(this);
            let idAnunt = btn.data('id');

            $.ajax({
                url: 'like.php',
                method: 'POST',
                data: { id: idAnunt },
                dataType: 'json',
                success: function(raspuns) {
                    if (raspuns.error) {
                        alert('Trebuie să fii autentificat pentru a da like!');
                        return;
                    }
                    btn.find('.count').text(raspuns.likes);
                    if (raspuns.liked) {
                        btn.addClass('liked');
                        btn.find('.heart').text('❤️');
                    } else {
                        btn.removeClass('liked');
                        btn.find('.heart').text('🤍');
                    }
                }
            });
        });

        // 3. MINI GALERIE TOP-BAR — auto-slide fixat la 3 secunde
        const $miniSlider = $('#mini-slider');
        const hMini = 50;

        function autoMiniSlide() {
            $miniSlider.animate({ marginTop: -hMini }, 600, function() {
                $miniSlider.append($miniSlider.find('li:first')).css('marginTop', 0);
                // Gestionare video
                $('.top-bar-gallery video').each(function() { this.pause(); });
                let $vid = $miniSlider.find('li:first video');
                if ($vid.length) $vid[0].play();
            });
        }

        setInterval(autoMiniSlide, 3000);
    });
    </script>

</body>
</html>