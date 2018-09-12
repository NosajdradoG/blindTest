<?php
// Initialisation de la session
session_start();
 
// Check si l'identifiant est deja log go page de bienvenu
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: bienvenu.php");
    exit;
}
 
// Fichier config narmol
require_once "config.php";
 
// Defini les variable vides au debut lol
$username = $password = "";
$username_err = $password_err = "";
 
// Execute dans la bdd lorsque le form est completer
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check si pseudo est vide
    if(empty(trim($_POST["username"]))){
        $username_err = "Entrez un pseudo.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check si pass est vide
    if(empty(trim($_POST["password"]))){
        $password_err = "Entrez un mot de passe.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validation
    if(empty($username_err) && empty($password_err)){
        // On selectione dans la bdd
        $sql = "SELECT id, username, password FROM validation WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // defini les params
            $param_username = $username;
            
            // Execution
            if(mysqli_stmt_execute($stmt)){
                
                mysqli_stmt_store_result($stmt);
                
                // Check si le log existe et si oui check le pass correspond
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Le pass est correct donc demarage de la session
                            session_start();
                            
                            // Met les donné en variable de session
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirection a la page de bienvenu
                            header("location: bienvenu.php");
                        } else{
                            // Si pass pas valide
                            $password_err = "Le mot de passe n'est pas valide.";
                        }
                    }
                } else{
                    // Si le nom n'existe pas
                    $username_err = "Nous n'avons pas trouvé le compte correspondant.";
                }
            } else{
                echo "Oops! Une erreur est survenue, réessayez plus tard !";
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
    <title>Connexion</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Connexion</h2>
        <p>Rentrez vos identifiants pour vous connecter.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Pseudo</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Mot de passe</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Connexion">
            </div>
            <p>Vous n'avez pas de compte ? <a href="index.php">S'inscrire</a>.</p>
        </form>
    </div>    
</body>
</html>