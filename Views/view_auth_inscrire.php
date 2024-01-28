<h2>Inscription</h2>
    <form action="?controller=auth&action=inscrire" method="POST">
        <label for="nom">Nom:</label><br>
        <input type="text" id="nom" name="nom" required><br><br>

        <label for="prenom">Prénom:</label><br>
        <input type="text" id="prenom" name="prenom" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Mot de passe:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="choice">BUT ?:</label><br>
        <select id="choice" name="but">
            <option value="1">But 1</option>
            <option value="2">But 2</option>
            <option value="3">But 3</option>
        </select><br><br>

        <input type="submit" value="inscrire">
    </form>

    <br>
    Déjà Membre : <a href="?controller=auth&ci=connecter"><button id="connection" class="header" role="button" >connection</button></a>
    <br>
    Page d'accueil : <a href="?"><button id="connection" class="header" role="button" >Retour</button></a>