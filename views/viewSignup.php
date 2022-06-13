<?php
$Last_name = "Nom de famille";
$First_name = "Prénom";
$Email = "Email";
$Username = "Nom d'utilisateur";
$Password = "Mot de passe";
$Password_Confirm = "Confirmer le mot de passe";
$Register = "Inscription";
$Project = "R&T Agenda";
$Signup = "S'inscrire";
$Login = "Connexion";
$Already_registered = "Déjà inscrit ?";

$this->_t = $Project . " - " . $Register;

?>


<div class="container">
    <h1><?= $Project ?></h1>
    <h2><?= $Register ?></h2>
    <form action="confirm" , method="POST">
        <div class="form-group d-flex">
            <div class="form-group p-2">
                <label for="last_name"><?= $Last_name ?></label>
                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="<?= $Last_name ?>">
                <small id="last_nameHelp" class="text-danger"></small>
            </div>
            <div class="form-group p-2">
                <label for="first_name"><?= $First_name ?></label>
                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="<?= $First_name ?>">
                <small id="first_nameHelp" class="text-danger"></small>
            </div>
        </div>
        <div class="form-group d-flex">
            <div class="form-group p-2">
                <label for="email"><?= $Email ?></label>
                <input type="text" class="form-control" id="email" name="email" placeholder="<?= $Email ?>">
                <small id="emailHelp" class="text-danger"></small>
            </div>
            <div class="form-group p-2">
                <label for="username"><?= $Username ?></label>
                <input type="text" class="form-control" id="username" name="username" placeholder="<?= $Username ?>">
                <small id="usernameHelp" class="text-danger"></small>
            </div>
        </div>
        <div class="form-group w-100 px-2">
            <div class="form-group">
                <label for="password"><?= $Password ?></label>
                <input type="password" class="form-control" id="password" name="password" placeholder="<?= $Password ?>">
                <small id="passwordHelp" class="text-danger"></small>
            </div>
            <div class="form-group">
                <label for="confirm"><?= $Password_Confirm ?></label>
                <input type="password" class="form-control" id="confirm" name="confirm" placeholder="<?= $Password ?>">
                <small id="confirmHelp" class="text-danger"></small>
            </div>
        </div>
        <button type="submit" class="btn btn-primary my-2" id="register"><?= $Signup ?></button>
    </form>
    <p><?= $Already_registered ?> <a href="./login"><?= $Login ?></a></p>
</div>