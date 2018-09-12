<?php
// Initialisation de la session
session_start();
 
// Check si le user est log sinon go page connexion
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: connexion.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>JazzeR</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center;}
    </style>
</head>
<body>
    <header>
    <div class="page-header">
        <h1>Salut, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Bienvenue sur JazzeR.</h1>
    </div>
    </header>
    <p>
        <a href="reset-password.php" class="btn btn-warning">Reset mot de passe</a>
        <a href="logout.php" class="btn btn-danger">Se déconnecter</a>
    </p>
</body>
</html>