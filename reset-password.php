<?php
// Initialisation de la session
session_start();
 
// Check si le user est log sinon go page connexion
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: connexion.php");
    exit;
}
 
// Fichier config OKLM
require_once "config.php";
 
// Variable vide pour le debut comdab
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
 
// Data quand le form est complet
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validattion nouveau mdp
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Entrez le nouveau mot de passe.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Votre mot de passe doit contenir au moins 6 caractères.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validation de la confirmation
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Confirmez le mot de passe";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Les mots de passe ne correspondent pas.";
        }
    }
        
    // Check les erreur avant de mettre dans la bdd
    if(empty($new_password_err) && empty($confirm_password_err)){
        
        $sql = "UPDATE validation SET password = ? WHERE id = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            
            mysqli_stmt_bind_param($stmt, "si", $param_password, $param_id);
            
            // defini params
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];
            
            // exec
            if(mysqli_stmt_execute($stmt)){
                // Pass reset go close session et go connexion
                session_destroy();
                header("location: connexion.php");
                exit();
            } else{
                echo "Oops! Une erreur est survenue, réessayer plus tard.";
            }
        }
        
        
        mysqli_stmt_close($stmt);
    }
    
    
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset mot de passe</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Reset mot de passe</h2>
        <p>Remplissez le formulaire pour reset le mot de passe</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
            <div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
                <label>Nouveau mot de passe</label>
                <input type="password" name="new_password" class="form-control" value="<?php echo $new_password; ?>">
                <span class="help-block"><?php echo $new_password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirmation du mot de passe</label>
                <input type="password" name="confirm_password" class="form-control">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Envoyer">
                <a class="btn btn-link" href="bienvenu.php">Annuler</a>
            </div>
        </form>
    </div>    
</body>
</html>