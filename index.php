<?php
// Je met le fichier config qui définit la connexion a ma bdd
require_once "config.php";
 
// Je défini les variables et met des values vides
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
 
// Execute le data quand le formulaire est rempli
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validation de l'identifiant (pseudo)
    if(empty(trim($_POST["username"]))){
        $username_err = "Entrez un identifiant.";
    } else{
        // Je prepare ma bdd a recevoir les params
        $sql = "SELECT id FROM validation WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // On prepare la bdd a recevoir l'identifiant
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Défini les params
            $param_username = trim($_POST["username"]);
            
            // Execution de la preparation
            if(mysqli_stmt_execute($stmt)){
                
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "Cet identifiant est déja utilisé !";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! quelquechose ne s'est pas produit comme prévu, réessayez plus tard !";
            }
        }
         
        // Fermer la bdd
        mysqli_stmt_close($stmt);
    }
    
    // Validation password
    if(empty(trim($_POST["password"]))){
        $password_err = "Entrez un mot de passe !";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Le mot de passe doit contenir au moins 6 caractères.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validation de la confirmation du pass
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Confirmez le mot de passe svp !";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Les mots de passe ne correspondent pas.";
        }
    }
    
    // Check les erreur de frappes etc avant d'inserer dans la bdd
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Insertion dans la bdd
        $sql = "INSERT INTO validation (username, password) VALUES (?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // On prepare la bdd a recevoir l'identifiant
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
            
            // defini les params
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Crée un pass hasher
            
            // Execution du truc
            if(mysqli_stmt_execute($stmt)){
                // Redirection a la page connexion
                header("location: connexion.php");
            } else{
                echo "Quelquechose s'est mal passé, réessayer plus tard...";
            }
        }
         
        // Fermer la bdd
        mysqli_stmt_close($stmt);
    }
    
    // Fermer la connexion
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif;}
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Inscription</h2>
        <p>Entrez vos identifiants et mot de passe pour vous inscrire.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Identifiant</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Mot de passe</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirmer le mot de passe</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="S'inscrire">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p>Vous avez déja un compte ? <a href="connexion.php">Se connecter</a>.</p>
        </form>
    </div>    
</body>
</html>