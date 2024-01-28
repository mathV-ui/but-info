<header>

    <span class='profil header'>
    <p id='nom'><?=e($user['nom'])?></p>
    <p id='prenom'><?=e($user['prenom'])?></p>
    </span>

    <h1 class='header' id="titre">But Info Villetaneuse</h1>
    <img class='header' id="logo" src="content/img/logoUSPN.png" alt="Logo USPN">
    <div class="checkbox-wrapper-54 header">
      <label class="switch">
        <input type="checkbox">
        <span class="slider"></span>
      </label>
      <p>dark-mode</p>
    </div>

</header>
<?php require "view_menu.php";?>