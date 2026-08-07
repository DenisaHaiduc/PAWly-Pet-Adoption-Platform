<?php
session_start();

// Distrugem toate datele sesiunii
$_SESSION = [];

// Ștergem cookie-ul de sesiune
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Distrugem sesiunea
session_destroy();

// Redirecționăm la pagina principală
header("Location: index.php");
exit();
?>
