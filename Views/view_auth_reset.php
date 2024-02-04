<?php require "view_begin.php"; ?>

<h2>Reset</h2>
    <form action="?controller=auth&action=reset_mdp" method="POST">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <input type="submit" value="Envoyer">
    </form>
    <br>
    Pas Encore Membre ? : <a href="?controller=auth&ci=inscrire"><button id="connection" class="header" role="button" >S'inscrire</button></a>
    <br>
    Page d'accueil : <a href="?"><button id="connection" class="header" role="button" >Retour</button></a>

<?php require "view_end.php"; ?>