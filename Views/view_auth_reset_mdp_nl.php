<?php require "view_begin.php"; ?>

<h2>Reset</h2>
    <form action="?controller=auth&action=reset_mdp_nk&token=<?= e($token)?>" method="POST">
        <label for="mdp">Nouveau Mot de Passe:</label><br>
        <input type="mdp" id="mdp" name="mdp" required><br><br>

        <input type="submit" value="Reset">
    </form>
    <br>
    Page d'accueil : <a href="?"><button id="connection" class="header" role="button" >Retour</button></a>

<?php require "view_end.php"; ?>