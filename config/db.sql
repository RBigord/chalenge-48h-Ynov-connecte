-- Création de la base de données
CREATE DATABASE IF NOT EXISTS ynov_network;
USE ynov_network;

-- Table USER
CREATE TABLE IF NOT EXISTS USER (
  id_user INT PRIMARY KEY AUTO_INCREMENT,
  nom VARCHAR(100) NOT NULL,
  email VARCHAR(150) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  formation ENUM('Informatique', 'Création & Design', 'Marketing & Communication', 'Audiovisuel', 'Animation 3D & Jeux Vidéo', 'Architecture d''intérieur', 'Business') NOT NULL,
  campus ENUM('Paris', 'Aix-en-Provence', 'Bordeaux', 'Lille', 'Lyon', 'Montpellier', 'Nantes', 'Rennes', 'Sophia Antipolis', 'Toulouse', 'Valence') DEFAULT 'Paris',
  annee_etude ENUM('B1', 'B2', 'B3', 'M1', 'M2') NOT NULL,  
  contact VARCHAR(255),
  bio TEXT,
  avatar VARCHAR(255),
  role VARCHAR(20) DEFAULT 'student', -- Permet de définir des droits spéciaux (ex: 'student', 'admin')
  date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table SKILL (On enlève le niveau d'ici)
CREATE TABLE IF NOT EXISTS SKILL (
  id_skill INT PRIMARY KEY AUTO_INCREMENT,
  nom VARCHAR(100) NOT NULL UNIQUE,
  description TEXT
);

-- Table de liaison USER_SKILL 
CREATE TABLE IF NOT EXISTS USER_SKILL (
  id_user INT,
  id_skill INT,
  niveau VARCHAR(20), -- Niveau (débutant, intermédiaire, avancé) PROPRE à chaque utilisateur
  PRIMARY KEY (id_user, id_skill),
  FOREIGN KEY (id_user) REFERENCES USER(id_user) ON DELETE CASCADE,
  FOREIGN KEY (id_skill) REFERENCES SKILL(id_skill) ON DELETE CASCADE
);

-- Table POST
CREATE TABLE IF NOT EXISTS POST (
  id_post INT PRIMARY KEY AUTO_INCREMENT,
  contenu TEXT NOT NULL,
  image VARCHAR(255),
  date_post DATETIME DEFAULT CURRENT_TIMESTAMP,
  id_user INT,
  FOREIGN KEY (id_user) REFERENCES USER(id_user) ON DELETE CASCADE
);

-- Table COMMENT
CREATE TABLE IF NOT EXISTS COMMENT (
  id_comment INT PRIMARY KEY AUTO_INCREMENT,
  contenu TEXT NOT NULL,
  date_comment DATETIME DEFAULT CURRENT_TIMESTAMP,
  id_user INT,
  id_post INT,
  FOREIGN KEY (id_user) REFERENCES USER(id_user) ON DELETE CASCADE,
  FOREIGN KEY (id_post) REFERENCES POST(id_post) ON DELETE CASCADE
);

-- Table POST_LIKE (Gère uniquement les likes)
CREATE TABLE IF NOT EXISTS POST_LIKE (
  id_like INT PRIMARY KEY AUTO_INCREMENT,
  id_user INT,
  id_post INT,
  UNIQUE (id_user, id_post), -- Empêche un utilisateur de liker plusieurs fois le même post
  FOREIGN KEY (id_user) REFERENCES USER(id_user) ON DELETE CASCADE,
  FOREIGN KEY (id_post) REFERENCES POST(id_post) ON DELETE CASCADE
);

-- Table MESSAGE
CREATE TABLE IF NOT EXISTS MESSAGE (
  id_message INT PRIMARY KEY AUTO_INCREMENT,
  contenu TEXT NOT NULL,
  date_message DATETIME DEFAULT CURRENT_TIMESTAMP,
  fichiers VARCHAR(255), -- Pour les images dans les messages
  id_sender INT,
  id_receiver INT,
  FOREIGN KEY (id_sender) REFERENCES USER(id_user) ON DELETE CASCADE,
  FOREIGN KEY (id_receiver) REFERENCES USER(id_user) ON DELETE CASCADE
);

-- Table NEWS
CREATE TABLE IF NOT EXISTS NEWS (
  id_news INT PRIMARY KEY AUTO_INCREMENT,
  titre VARCHAR(255) NOT NULL,
  contenu TEXT NOT NULL,
  fichiers VARCHAR(255), -- Pour les fichiers des news
  date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
  id_user INT,
  FOREIGN KEY (id_user) REFERENCES USER(id_user) ON DELETE CASCADE
);

-- Table NOTIFICATION
CREATE TABLE IF NOT EXISTS NOTIFICATION (
  id_notification INT PRIMARY KEY AUTO_INCREMENT,
  contenu TEXT NOT NULL,
  is_read BOOLEAN DEFAULT FALSE,
  date DATETIME DEFAULT CURRENT_TIMESTAMP,
  id_user INT,
  FOREIGN KEY (id_user) REFERENCES USER(id_user) ON DELETE CASCADE
);