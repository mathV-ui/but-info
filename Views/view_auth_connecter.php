<h2>Connecter</h2>
    <form action="?controller=auth&action=connecter" method="POST">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Mot de passe:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="connecter">
    </form>

    <br>
    Pas Encore Membre ? : <a href="?controller=auth&ci=inscrire"><button id="connection" class="header" role="button" >S'inscrire</button></a>
    <br>
    Page d'accueil : <a href="?"><button id="connection" class="header" role="button" >Retour</button></a>