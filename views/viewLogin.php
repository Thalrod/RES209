<?php
$Project_Name = "R&T Agenda";
$Login = "Connexion";
$Not_Registered_Yet = "Pas encore inscrit ?";
$Register = "S'inscrire";
$Username = "Nom d'utilisateur";
$Password = "Mot de passe";
$Logout = "Deconnexion";
$Signup = "Inscription";


?>

<div class="container">
    <h1><?= $Project_Name ?></h1>
    <h2><?= $Login ?></h2>
    <form action="confirm" method="post" id="form">
    <span id="formHelp" class="text-danger"></span>
        <div class="form-group p-2">
            <label for="username"><?= $Username ?></label>
            <input type="text" class="form-control" id="username" name="username" placeholder="<?= $Username ?>">
            <span id="usernameHelp" class="text-danger"></span>
        </div>
        <div class="form-group p-2">
            <label for="password"><?= $Password ?></label>
            <input type="password" class="form-control" id="password" name="password" placeholder="<?= $Password ?>">
            <span id="passwordHelp" class="text-danger"></span>
        </div>

        <button type="submit" class="btn btn-primary my-4" id="login"><?= $Login ?></button>
    </form>
    <p><?= $Not_Registered_Yet ?> <a href="./signup"><?= $Register ?></a></p>
</div>