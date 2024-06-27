-- Création de la base de données
CREATE DATABASE IF NOT EXISTS GleanDB;

-- Sélection de la base de données
USE GleanDB;

-- Création de la table Utilisateur
CREATE TABLE IF NOT EXISTS Utilisateur (
    ID_Utilisateur INTEGER PRIMARY KEY AUTOINCREMENT,
    Nom VARCHAR(100),
    Email VARCHAR(100) UNIQUE,
    Mot_de_passe VARCHAR(255)
);

-- Création de la table Createur
CREATE TABLE IF NOT EXISTS Createur (
    ID_Createur INTEGER PRIMARY KEY AUTOINCREMENT,
    Nom_Createur VARCHAR(100) UNIQUE, -- Auteur du contenu
    About TEXT
);

-- Création de la table Contenu
CREATE TABLE IF NOT EXISTS Contenu (
    ID_Contenu INTEGER PRIMARY KEY AUTOINCREMENT,
    ID_Createur INTEGER,
    Type_de_contenu TEXT CHECK(Type_de_contenu IN ('article', 'video', 'livre')),
    Titre_Contenu VARCHAR(255),
    Description_contenu TEXT,
    URL VARCHAR(1000),
    Etat_d_achevement BOOLEAN,
    Date_ajout DATETIME,
    FOREIGN KEY (ID_Createur) REFERENCES Createur(ID_Createur)
);

-- Création de la table Note
CREATE TABLE IF NOT EXISTS Note (
    ID_Note INTEGER PRIMARY KEY AUTOINCREMENT,
    ID_Utilisateur INTEGER,
    ID_Contenu INTEGER,
    Titre_Note VARCHAR(255),
    Texte_de_Note TEXT,
    Date_creation DATETIME,
    Derniere_modification DATETIME,
    FOREIGN KEY (ID_Utilisateur) REFERENCES Utilisateur(ID_Utilisateur),
    FOREIGN KEY (ID_Contenu) REFERENCES Contenu(ID_Contenu)
);