<?php

// Informations de connexion à la base de données PostgreSQL
define('DB_HOST', 'localhost');      // L'hôte de la base de données (généralement localhost)
define('DB_NAME', 'but-info'); // Le nom de votre base de données PostgreSQL
define('DB_USER', 'postgres'); // Votre nom d'utilisateur PostgreSQL
define('DB_PASSWORD', '1234'); // Votre mot de passe PostgreSQL

$dsn = "pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME;
$login = DB_USER;
$mdp = DB_PASSWORD;

?>