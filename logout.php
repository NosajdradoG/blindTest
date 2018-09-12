<?php
// Initialisation de la session
session_start();
 
// On detruit tt les variable de session
$_SESSION = array();
 
// Detruit la session
session_destroy();
 
// Redirection a la page connexion
header("location: connexion.php");
exit;
?>