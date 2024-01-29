DROP TABLE IF EXISTS utilisateur CASCADE;
DROP TABLE IF EXISTS eleve CASCADE;
DROP TABLE IF EXISTS moderateur CASCADE;
DROP TABLE IF EXISTS prof CASCADE;
DROP TABLE IF EXISTS discussion CASCADE;
DROP TABLE IF EXISTS github CASCADE;
DROP TABLE IF EXISTS message CASCADE;
DROP TABLE IF EXISTS site CASCADE;
DROP TABLE IF EXISTS logs CASCADE;
CREATE TABLE utilisateur (
   id_utilisateur SERIAL PRIMARY KEY,
   nom VARCHAR(64) NOT NULL,
   prenom VARCHAR(64) NOT NULL,
   password VARCHAR(1024) NOT NULL,
   mail VARCHAR(256) UNIQUE NOT NULL,
   token VARCHAR(1024) UNIQUE NOT NULL,
   photo_de_profil VARCHAR(64) NOT NULL,
   date_de_creation DATE NOT NULL,
   mail_verifier BOOLEAN NOT NULL
);

CREATE TABLE eleve (
   id_utilisateur INT PRIMARY KEY,
   annee INT NOT NULL,
   FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE
);

CREATE TABLE moderateur (
   id_moderateur SERIAL PRIMARY KEY,
   id_utilisateur INT UNIQUE NOT NULL,
   FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE
);

CREATE TABLE prof (
   id_utilisateur INT PRIMARY KEY,
   FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE
);

CREATE TABLE discussion (
   id_discussion SERIAL PRIMARY KEY,
   id_utilisateur INT NOT NULL,
   id_utilisateur_1 INT NOT NULL,
   FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE,
   FOREIGN KEY (id_utilisateur_1) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE
);

CREATE TABLE github (
   id_github SERIAL PRIMARY KEY,
   lien VARCHAR(256),
   verifier BOOLEAN NOT NULL,
   date_du_github TIMESTAMP NOT NULL,
   id_utilisateur INT NOT NULL,
   FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE
);

CREATE TABLE message (
   id_discussion INT,
   id_message SERIAL PRIMARY KEY,
   id_utilisateur INT NOT NULL,
   FOREIGN KEY (id_discussion) REFERENCES discussion(id_discussion) ON DELETE CASCADE,
   FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE
);

CREATE TABLE site (
   id_site SERIAL PRIMARY KEY,
   date_d_ajout TIMESTAMP NOT NULL,
   commentaire VARCHAR(256),
   nom_dossier VARCHAR(128) NOT NULL,
   id_github INT NOT NULL,
   id_utilisateur INT NOT NULL,
   FOREIGN KEY (id_github) REFERENCES github(id_github) ON DELETE CASCADE,
   FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE
);

CREATE TABLE logs(
   id_logs SERIAL PRIMARY KEY,
   commentaire VARCHAR(256),
   date_du_log TIMESTAMP NOT NULL,
   id_utilisateur INT NOT NULL,
   FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE
);