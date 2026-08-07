<?php
session_start();
require 'config.php';

// Accesul permis doar dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$mesaj = "";
$tip_mesaj = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titlu = trim($_POST['rasa'] ?? '');
    $specie = trim($_POST['rasa'] ?? '');
    $descriere = trim($_POST['desc'] ?? '');
    $pret = $_POST['pret'] ?? '0';
    $user_id = $_SESSION['user_id'];

    // Gestiunea fișierelor: Încărcarea pozei pe server
    if (isset($_FILES['imagine']) && $_FILES['imagine']['error'] === UPLOAD_ERR_OK) {
        $extensie = strtolower(pathinfo($_FILES['imagine']['name'], PATHINFO_EXTENSION));
        $extensii_permise = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($extensie, $extensii_permise)) {
            $mesaj = "Format de imagine nepermis! Folosește: JPG, PNG, GIF sau WEBP.";
            $tip_mesaj = "eroare";
        } else {
            // Generăm un nume unic cu timestamp — sanitizăm numele fișierului
            $nume_original = preg_replace('/[^a-zA-Z0-9._-]/', '_', $_FILES['imagine']['name']);
            $nume_fisier = time() . "_" . $nume_original;
            $upload_dir = __DIR__ . '/uploads';
            $target_abs = $upload_dir . '/' . $nume_fisier;
            $target_rel = 'uploads/' . $nume_fisier;

            // Încercăm să ne asigurăm că directorul există atât absolut cât și relativ
            if (!is_dir($upload_dir)) {
                @mkdir($upload_dir, 0777, true);
            }
            if (!is_dir('uploads')) {
                @mkdir('uploads', 0777, true);
            }

            $success = false;
            // Încercăm salvarea folosind căile absolute și relative
            if (@move_uploaded_file($_FILES['imagine']['tmp_name'], $target_abs)) {
                $success = true;
            } elseif (@move_uploaded_file($_FILES['imagine']['tmp_name'], $target_rel)) {
                $success = true;
            } elseif (@copy($_FILES['imagine']['tmp_name'], $target_abs)) {
                $success = true;
            } elseif (@copy($_FILES['imagine']['tmp_name'], $target_rel)) {
                $success = true;
            }

            if ($success) {
                $sql = "INSERT INTO anunturi (utilizator_id, titlu, specie, descriere, pret, imagine) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$user_id, $titlu, $specie, $descriere, $pret, $target_rel]);
                $mesaj = "Anunț adăugat cu succes!";
                $tip_mesaj = "succes";
            } else {
                $err = error_get_last();
                $detalii = $err ? $err['message'] : 'Permisiuni insuficiente sau restricție open_basedir.';
                $mesaj = "Eroare la salvarea imaginii pe server: " . $detalii . " (Verifică permisiunile folderului uploads/)";
                $tip_mesaj = "eroare";
            }
        }
    } else {
        $mesaj = "Te rugăm să selectezi o imagine!";
        $tip_mesaj = "eroare";
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAWly - Adăugare Anunț</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="announcement.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        /* CSS pentru sfaturile validate */
        .tip-active {
            color: #2e7d32 !important;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .tip-active .tip-icon {
            transform: scale(1.2);
            display: inline-block;
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
        /* Stilizare câmp file upload integrat în design */
        .file-upload-wrapper {
            position: relative;
            border: 2px dashed var(--lila-deschis);
            border-radius: 12px;
            padding: 25px 20px;
            text-align: center;
            background-color: var(--lila-foarte-stins);
            cursor: pointer;
            transition: border-color 0.3s, background 0.3s;
        }
        .file-upload-wrapper:hover {
            border-color: var(--mov-fundal);
            background-color: #ece0ea;
        }
        .file-upload-wrapper input[type="file"] {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        .file-upload-wrapper .file-label { font-size: 0.95rem; color: #888; }
        .file-upload-wrapper .file-icon { font-size: 2rem; display: block; margin-bottom: 8px; }
    </style>
</head>
<body>

    <header>
        <h1>Adăugare Anunț</h1>
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
                <li><a href="add_announcement.php">Adaugă Anunț</a></li>
                <li><a href="logout.php" style="color: #ff4d4d;">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
            </ul>
        </nav>

        <main class="continut">
            <div class="announcement-wrapper">

                <!-- ═══ PANOUL CU FORMULARUL ═══ -->
                <div class="form-card">
                    <h2 class="announcement-header">Publică un anunț nou</h2>

                    <?php if (!empty($mesaj)): ?>
                        <div class="mesaj-<?php echo $tip_mesaj; ?>"><?php echo htmlspecialchars($mesaj); ?></div>
                    <?php endif; ?>

                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="grup-form">
                            <label for="rasa">Rasă animal</label>
                            <input list="rase-animale" id="rasa" name="rasa" placeholder="Caută sau scrie rasa...">
                            <datalist id="rase-animale">
                                <option value="Beagle">
                                <option value="Siameză">
                                <option value="Bichon">
                                <option value="Golden Retriever">
                                <option value="Labrador">
                                <option value="Persan">
                                <option value="Pudel">
                            </datalist>
                        </div>

                        <div class="grup-form">
                            <label for="varsta">Vârstă (luni)</label>
                            <input type="number" id="varsta" name="varsta" step="1" min="0" max="240" placeholder="ex: 3">
                        </div>

                        <div class="grup-form">
                            <label for="judet">Județ</label>
                            <select id="judet" name="judet" required>
                                <option value="">-- Selectează Județul --</option>
                            </select>
                        </div>

                        <div class="grup-form">
                            <label for="oras">Oraș</label>
                            <select id="oras" name="oras" required disabled>
                                <option value="">-- Selectează întâi județul --</option>
                            </select>
                        </div>

                        <div class="grup-form">
                            <label for="ref">ID Referință</label>
                            <input type="text" id="ref" name="ref" value="REF-2026" readonly>
                        </div>

                        <div class="grup-form">
                            <label for="pret">Preț (RON)</label>
                            <input type="text" id="pret" name="pret" placeholder="ex: 500 sau Gratuit">
                        </div>

                        <div class="grup-form">
                            <label>Sex</label>
                            <div class="radio-group">
                                <label class="radio-option">
                                    <input type="radio" name="sex" value="M">
                                    🐾 Mascul
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="sex" value="F">
                                    🌸 Femelă
                                </label>
                            </div>
                        </div>

                        <div class="grup-form">
                            <label>Facilități incluse</label>
                            <div class="checkbox-group">
                                <label class="checkbox-option">
                                    <input type="checkbox" name="vaccin" value="vaccin">
                                    💉 Vaccin
                                </label>
                                <label class="checkbox-option">
                                    <input type="checkbox" name="microcip" value="microcip">
                                    📡 Microcip
                                </label>
                                <label class="checkbox-option">
                                    <input type="checkbox" name="pasaport" value="pasaport">
                                    📋 Pașaport
                                </label>
                            </div>
                        </div>

                        <div class="grup-form">
                            <label for="desc">Descriere anunț</label>
                            <textarea id="desc" name="desc" rows="5" placeholder="Descrie animalul: comportament, istoric medical, detalii suplimentare..." required></textarea>
                        </div>

                        <div class="grup-form">
                            <label>🖼️ Imagine animal</label>
                            <div class="file-upload-wrapper">
                                <span class="file-icon">📷</span>
                                <span class="file-label" id="file-label-text">Click pentru a alege o imagine</span>
                                <input type="file" name="imagine" id="imagine" accept="image/*" required>
                            </div>
                        </div>

                        <div class="butoane-form">
                            <button type="submit">✅ Publică Anunț</button>
                            <button type="reset" class="buton-secundar">🗑 Resetează</button>
                        </div>
                    </form>
                </div>

                <!-- ═══ PANOUL LATERAL CU SFATURI ═══ -->
                <aside class="info-panel">
                    <p class="info-panel-title">💡 Sfaturi pentru un anunț bun</p>
                    <ul class="tips-list">
                        <li id="tip-foto">
                            <span class="tip-icon">📸</span>
                            <span>Adaugă fotografii clare și recente ale animalului</span>
                        </li>
                        <li id="tip-medical">
                            <span class="tip-icon">🩺</span>
                            <span>Menționează istoricul medical și vaccinările efectuate</span>
                        </li>
                        <li id="tip-locatie">
                            <span class="tip-icon">📍</span>
                            <span>Specifică locația pentru a găsi mai ușor un adoptator local</span>
                        </li>
                        <li id="tip-personalitate">
                            <span class="tip-icon">💬</span>
                            <span>Descrie personalitatea și obiceiurile animalului</span>
                        </li>
                        <li id="tip-check">
                            <span class="tip-icon">✅</span>
                            <span>Bifează toate facilitățile disponibile pentru mai multă credibilitate</span>
                        </li>
                    </ul>

                    <p class="info-panel-title" style="margin-top: 20px;">🏷 Categorii detectate</p>
                    <div class="badge-row">
                        <span class="badge" id="badge-caine">🐶 Câini</span>
                        <span class="badge" id="badge-pisica">🐱 Pisici</span>
                        <span class="badge" id="badge-papagal">🦜 Papagali</span>
                        <span class="badge" id="badge-peste">🐠 Pești</span>
                        <span class="badge" id="badge-capra">🐐 Capre</span>
                    </div>
                </aside>

            </div>
        </main>
    </div>

    <footer>
        <p>&copy; 2026 PAWly - Creat pentru Laboratorul de Tehnologii Web</p>
        <div class="sprite-container">
            <span class="social-sprite facebook" title="Facebook"></span>
            <span class="social-sprite instagram" title="Instagram"></span>
        </div>
    </footer>

    <script>
    // ═══ DROPDOWN-URI JUDEȚ → ORAȘ ═══
    const dateLocatii = {
        "București": ["Sector 1", "Sector 2", "Sector 3", "Sector 4", "Sector 5", "Sector 6"],
        "Cluj": ["Cluj-Napoca", "Turda", "Dej", "Câmpia Turzii"],
        "Timiș": ["Timișoara", "Lugoj", "Sânnicolau Mare", "Jimbolia"],
        "Iași": ["Iași", "Pașcani", "Târgu Frumos", "Hârlău"],
        "Brașov": ["Brașov", "Făgăraș", "Săcele", "Zărnești", "Râșnov"]
    };

    const selectJudet = document.getElementById('judet');
    const selectOras = document.getElementById('oras');
    const listaJudete = Object.keys(dateLocatii);

    listaJudete.forEach(function(judet) {
        const optiuneNoua = document.createElement('option');
        optiuneNoua.value = judet;
        optiuneNoua.textContent = judet;
        selectJudet.appendChild(optiuneNoua);
    });

    selectJudet.addEventListener('change', function() {
        const judetSelectat = selectJudet.value;
        selectOras.innerHTML = '<option value="">-- Selectează Orașul --</option>';
        if (judetSelectat === "") {
            selectOras.disabled = true;
        } else {
            selectOras.disabled = false;
            const oraseDinJudet = dateLocatii[judetSelectat];
            oraseDinJudet.forEach(function(oras) {
                const optiuneNoua = document.createElement('option');
                optiuneNoua.value = oras;
                optiuneNoua.textContent = oras;
                selectOras.appendChild(optiuneNoua);
            });
        }
    });

    // ═══ DETECȚIE LIVE CATEGORII ȘI SFATURI (jQuery) ═══
    $(document).ready(function() {

        const categoriiAnimale = {
            '#badge-caine': ["caine", "beagle", "bichon", "retriever", "labrador", "pudel", "catel"],
            '#badge-pisica': ["pisica", "siameza", "persan", "motan"],
            '#badge-papagal': ["papagal", "pasare", "ara", "perus"],
            '#badge-peste': ["peste", "acvariu", "beta", "guppy"],
            '#badge-capra': ["capra", "ied", "belitoare"]
        };

        const reguliSfaturi = {
            '#tip-medical': ["vaccin", "medical", "carnet", "doctor", "sanatate", "sterilizat"],
            '#tip-locatie': ["cluj", "bucuresti", "iasi", "timis", "brasov", "locatie", "oras", "sector"],
            '#tip-personalitate': ["jucaus", "cuminte", "energie", "bland", "personalitate", "iubitor"]
        };

        function verificaValidariLive() {
            const textTotal = ($('#desc').val() + " " + $('#rasa').val()).toLowerCase();

            // Badge-uri animale
            $.each(categoriiAnimale, function(selector, cuvinte) {
                let esteMatch = cuvinte.some(cuv => textTotal.includes(cuv));
                $(selector).toggleClass('badge-active', esteMatch);
            });

            // Sfaturi medicale/locație/personalitate
            $.each(reguliSfaturi, function(selector, cuvinte) {
                let esteMatch = cuvinte.some(cuv => textTotal.includes(cuv));
                $(selector).toggleClass('tip-active', esteMatch);
            });
        }

        // Evenimentele de scriere pe ambele input-uri
        $('#desc, #rasa').on('input change', function() {
            verificaValidariLive();
        });

        // CheckBoxes → sfatul "tip-check"
        $('input[type="checkbox"]').on('change', function() {
            const oricareBifat = $('input[type="checkbox"]:checked').length > 0;
            $('#tip-check').toggleClass('tip-active', oricareBifat);
        });
    });

    // ═══ AFIȘARE NUME FIȘIER SELECTAT ═══
    document.getElementById('imagine').addEventListener('change', function() {
        var label = document.getElementById('file-label-text');
        if (this.files.length > 0) {
            label.textContent = this.files[0].name;
        } else {
            label.textContent = 'Click pentru a alege o imagine';
        }
    });
    </script>

</body>
</html>