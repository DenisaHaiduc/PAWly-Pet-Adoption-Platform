<?php
/**
 * seeder.php — Populează baza de date cu 10 anunțuri dummy
 * Descarcă imagini de pe loremflickr.com și le salvează în uploads/
 * 
 * Rulare: php seeder.php  sau accesează-l în browser o singură dată
 */

require 'config.php';

// Creăm folderul uploads/ dacă nu există
if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
}

// Array cu date realiste pentru 10 animale
$animale = [
    [
        'titlu'     => 'Rex — Ciobănesc German loial',
        'specie'    => 'Caine',
        'descriere' => 'Rex are 3 ani, este vaccin complet și foarte cuminte. Adoră plimbările lungi și este excelent cu copiii. Caută o familie iubitoare cu curte.',
        'pret'      => 0,
        'query'     => 'german+shepherd+dog'
    ],
    [
        'titlu'     => 'Bella — Pisică Persană blândă',
        'specie'    => 'Pisica',
        'descriere' => 'Bella este o pisică Persană de 2 ani, sterilizată și vaccinată. Are blană lungă, albă, și un temperament foarte calm. Ideală pentru apartament.',
        'pret'      => 150,
        'query'     => 'persian+cat'
    ],
    [
        'titlu'     => 'Max — Golden Retriever jucăuș',
        'specie'    => 'Caine',
        'descriere' => 'Max are 1 an și e plin de energie! Are microcip și toate vaccinurile la zi. Caută o familie activă care iubește plimbările în natură.',
        'pret'      => 200,
        'query'     => 'golden+retriever'
    ],
    [
        'titlu'     => 'Coco — Papagal Ara colorat',
        'specie'    => 'Papagal',
        'descriere' => 'Coco este un papagal Ara de 5 ani care știe să vorbească câteva cuvinte. Este social, vesel și adoră muzica. Vine cu cușcă și accesorii.',
        'pret'      => 500,
        'query'     => 'macaw+parrot'
    ],
    [
        'titlu'     => 'Luna — Pisicuță Siameză tânără',
        'specie'    => 'Pisica',
        'descriere' => 'Luna are doar 6 luni, este foarte jucăușă și curioasă. Vaccinată, cu pașaport veterinar. Se înțelege bine cu alte pisici.',
        'pret'      => 100,
        'query'     => 'siamese+cat'
    ],
    [
        'titlu'     => 'Bruno — Labrador negru prietenos',
        'specie'    => 'Caine',
        'descriere' => 'Bruno este un Labrador de 4 ani, dresat, cu experiență în interacțiunea cu copiii. Sterilizat și cu toate vaccinurile. Caută o casă cu curte.',
        'pret'      => 0,
        'query'     => 'black+labrador+dog'
    ],
    [
        'titlu'     => 'Pufi — Iepure pitic alb',
        'specie'    => 'Iepure',
        'descriere' => 'Pufi este un iepuraș pitic de 8 luni. Este blând, nu mușcă și adoră să fie mângâiat. Perfect pentru familii cu copii mici.',
        'pret'      => 80,
        'query'     => 'white+rabbit+bunny'
    ],
    [
        'titlu'     => 'Bianca — Capră de rasă Saanen',
        'specie'    => 'Capra',
        'descriere' => 'Bianca are 2 ani și produce lapte de calitate. Este vaccinată și sănătoasă. Ideală pentru ferme mici sau gospodării la țară.',
        'pret'      => 350,
        'query'     => 'white+goat+farm'
    ],
    [
        'titlu'     => 'Kiwi — Papagal Peruș verde',
        'specie'    => 'Papagal',
        'descriere' => 'Kiwi este un peruș vesel de 1 an care cântă frumos dimineața. Vine cu cușcă, hrană și jucării. Perfect pentru un apartament.',
        'pret'      => 120,
        'query'     => 'green+budgie+parakeet'
    ],
    [
        'titlu'     => 'Mișu — Iepure cu urechi pleoștite',
        'specie'    => 'Iepure',
        'descriere' => 'Mișu este un iepure Holland Lop de 1 an, cu urechi pleoștite adorabile. Este obișnuit cu oamenii și adoră să exploreze casa.',
        'pret'      => 100,
        'query'     => 'lop+ear+rabbit'
    ]
];

echo "<h2>🌱 PAWly Seeder — Populare baza de date</h2>";
echo "<pre>";

$ok = 0;
$erori = 0;

foreach ($animale as $index => $animal) {
    $numar = $index + 1;
    $nume_fisier = "test_animal_{$numar}.jpg";
    $cale_fisier = "uploads/" . $nume_fisier;
    
    // URL imagine de pe loremflickr (redirect automat către o poză relevantă)
    $url_imagine = "https://loremflickr.com/640/480/" . $animal['query'];
    
    echo "[$numar/10] {$animal['titlu']}...\n";
    
    // Descărcăm imaginea
    $imagine_descarcata = false;
    
    // Încercăm cu file_get_contents
    $context = stream_context_create([
        'http' => [
            'timeout' => 15,
            'follow_location' => true,
            'user_agent' => 'Mozilla/5.0 PAWly Seeder'
        ]
    ]);
    
    $continut = @file_get_contents($url_imagine, false, $context);
    
    if ($continut !== false && strlen($continut) > 1000) {
        file_put_contents($cale_fisier, $continut);
        $imagine_descarcata = true;
        echo "  ✅ Imagine descărcată: {$cale_fisier}\n";
    } else {
        // Fallback: încercăm cu copy()
        $imagine_descarcata = @copy($url_imagine, $cale_fisier);
        if ($imagine_descarcata) {
            echo "  ✅ Imagine descărcată (copy): {$cale_fisier}\n";
        } else {
            // Fallback final: folosim o imagine placeholder locală dacă există
            echo "  ⚠️ Nu s-a putut descărca imaginea, folosim placeholder\n";
            // Creăm o imagine placeholder simplă
            if (function_exists('imagecreatetruecolor')) {
                $img = imagecreatetruecolor(640, 480);
                $bg = imagecolorallocate($img, 149, 92, 143); // mov-fundal
                $text_color = imagecolorallocate($img, 255, 255, 255);
                imagefill($img, 0, 0, $bg);
                imagestring($img, 5, 250, 230, $animal['specie'], $text_color);
                imagejpeg($img, $cale_fisier, 85);
                imagedestroy($img);
                $imagine_descarcata = true;
                echo "  ✅ Placeholder generat: {$cale_fisier}\n";
            } else {
                $cale_fisier = "dog_1.jpg"; // Folosim o imagine existentă
                echo "  ⚠️ Folosim imagine existentă: {$cale_fisier}\n";
            }
        }
    }
    
    // Inserăm în baza de date
    try {
        $sql = "INSERT INTO anunturi (utilizator_id, titlu, specie, descriere, pret, imagine, likes) VALUES (?, ?, ?, ?, ?, ?, 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            1, // utilizator_id = 1 (admin)
            $animal['titlu'],
            $animal['specie'],
            $animal['descriere'],
            $animal['pret'],
            $cale_fisier
        ]);
        echo "  ✅ Inserare DB reușită (ID: " . $pdo->lastInsertId() . ")\n";
        $ok++;
    } catch (PDOException $e) {
        echo "  ❌ Eroare DB: " . $e->getMessage() . "\n";
        $erori++;
    }
    
    echo "\n";
    
    // Pauză scurtă între request-uri pentru a nu suprasolicita serviciul
    usleep(500000); // 0.5 secunde
}

echo "═══════════════════════════════════════\n";
echo "✅ Anunțuri inserate cu succes: {$ok}\n";
echo "❌ Erori: {$erori}\n";
echo "═══════════════════════════════════════\n\n";
echo "🎉 Baza de date a fost populată cu succes!\n";
echo "   Mergi la <a href='index.php'>index.php</a> pentru a vedea anunțurile.\n";
echo "</pre>";
?>
