<?php require "view_begin.php"; ?>
<?php require "view_header_connected.php"?>


<h2>lien github de votre site :</h2>
<p>Pour pouvoir ajouter votre site, il est nécessaire que je le valide. Veuillez fournir le lien GitHub de votre site ci-dessous :</p>
<p>CONDITIONS DU SITE : Je n'accepte que le HTML, le CSS et le JavaScript (donc pas de PHP, SQL, React ou autre).</p>
<p>Délai d'attente avant confirmation et ajout de votre site : environ 2 jours. (Vous pouvez accélérer le processus en me contactant sur Discord.)</p>
<br>
<form action="?controller=github&action=ajouter" method="POST">
    <label for="github">Lien github:</label><br>
    <input type="github" id="github" name="lien_github" required><br><br>
    <input type="submit" value="Ajouter">
</form>
<br>
<br>
Page d'accueil : <a href="?"><button id="connection" class="header" role="button" >Retour</button></a>

<br>

<?php require "view_end.php"; ?>