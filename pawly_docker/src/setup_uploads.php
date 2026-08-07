<?php
/**
 * Rulează acest fișier O SINGURĂ DATĂ pentru a configura directorul uploads.
 * Accesează: http://server/pawly_docker/src/setup_uploads.php
 * Apoi șterge-l din server!
 */

$upload_dir = __DIR__ . '/uploads';

echo "<h2>🔧 Setup director uploads</h2><pre>";

// 1. Verificăm dacă directorul există
if (!is_dir($upload_dir)) {
    if (mkdir($upload_dir, 0755, true)) {
        echo "✅ Director creat: $upload_dir\n";
    } else {
        echo "❌ Nu s-a putut crea directorul!\n";
        echo "   Creează manual directorul 'uploads' prin FTP/SSH.\n";
    }
} else {
    echo "ℹ️ Directorul există deja: $upload_dir\n";
}

// 2. Încercăm diverse permisiuni
$perms = [0755, 0775, 0777];
foreach ($perms as $p) {
    if (@chmod($upload_dir, $p)) {
        echo "✅ chmod(" . decoct($p) . ") reușit!\n";
        break;
    } else {
        echo "⚠️ chmod(" . decoct($p) . ") eșuat.\n";
    }
}

// 3. Test de scriere
$test_file = $upload_dir . '/test_write.tmp';
if (file_put_contents($test_file, 'test') !== false) {
    echo "✅ Testul de scriere a reușit! Upload-ul va funcționa.\n";
    unlink($test_file);
} else {
    echo "\n❌ DIRECTORUL NU ESTE WRITABLE!\n";
    echo "\n💡 EXPLICAȚIE TEHNICĂ (Server Facultate / Hosting Partajat):\n";
    echo "   Pe multe servere partajate cu măsuri de securitate stricte (suPHP, CloudLinux),\n";
    echo "   setarea permisiunilor la 0777 blochează automat accesul de scriere/execuție\n";
    echo "   din motive de securitate. Directorul trebuie să aibă permisiunile 0755.\n";
    echo "\n📋 Rezolvare recomandată (dacă 0777 nu funcționează):\n";
    echo "   Schimbă permisiunile folderului 'uploads' în 0755 sau 0775.\n";
    echo "\n   Prin SSH:\n";
    echo "   chmod 755 " . $upload_dir . "\n";
    echo "\n   Prin FileZilla/FTP:\n";
    echo "   Click dreapta pe folderul 'uploads' → File Permissions → 755 → OK\n";
}

// 4. Info curent
echo "\n═══ Info Director ═══\n";
echo "Cale: $upload_dir\n";
echo "Există: " . (is_dir($upload_dir) ? 'Da' : 'Nu') . "\n";
echo "Writable: " . (is_writable($upload_dir) ? 'Da' : 'Nu') . "\n";
$perms = file_exists($upload_dir) ? substr(sprintf('%o', fileperms($upload_dir)), -4) : 'N/A';
echo "Permisiuni: " . $perms . "\n";

$owner = 'Necunoscut';
if (file_exists($upload_dir) && function_exists('posix_getpwuid')) {
    $stat = @stat($upload_dir);
    if ($stat && isset($stat['uid'])) {
        $pwuid = @posix_getpwuid($stat['uid']);
        if ($pwuid && isset($pwuid['name'])) {
            $owner = $pwuid['name'];
        }
    }
}
echo "Owner: " . $owner . "\n";
echo "PHP User: " . get_current_user() . "\n";
echo "</pre>";
?>
