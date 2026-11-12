<?php

session_start();

/* Clear all session variables */
$_SESSION = [];


/* Remove session cookie */
if (ini_get('session.use_cookies')) {

    $p = session_get_cookie_params();

    setcookie(
        session_name(),
        ' ',
        time() - 42000,
        $p['path'],
        $p['domain'],
        $p['secure'],
        $p['httponly']
    );
}


/* Destroy session */
session_destroy();


/* Redirect to welcome page */
header('Location: ./welcome.php');
exit;

?>
