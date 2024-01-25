
<h3>Listes des élèves.</h3>

<form>
    <label for="search">Recherche :</label>
    <input type="text" id="search" name="search" placeholder="Entrez votre recherche">

    <input type="checkbox" id="but1" name="but1">
    <label for="but1">But 1</label>

    <input type="checkbox" id="but2" name="but2">
    <label for="but2">But 2</label>

    <input type="checkbox" id="but3" name="but3">
    <label for="but3">But 3</label>

    <button type="submit">Rechercher</button>
</form>
<ul class="eleves sites">
        
<li class="eleve sites" id="student['id']">
<p class="sites">nom</p>
<p class="sites">prenom</p>
<p class="sites">description</p>
<p class="sites"><a class="sites" href="student['site']">Mon site</a></p>
<img src="$student['photo']" alt="student['nom']">
</li>
</ul>
