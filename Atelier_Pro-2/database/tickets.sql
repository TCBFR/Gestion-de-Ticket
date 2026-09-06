
CREATE DATABASE IF NOT EXISTS Projet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE Projet;

CREATE TABLE utilisateurs (
    id           INT          PRIMARY KEY AUTO_INCREMENT,
    nom          VARCHAR(255) NOT NULL,
    email        VARCHAR(255) NOT NULL UNIQUE,
    -- Le mot de passe est TOUJOURS stocké haché avec password_hash() en PHP
    -- JAMAIS en clair en production
    mot_de_passe VARCHAR(255) NOT NULL,
    role         ENUM('admin', 'secretaire', 'technicien', 'agent municipal', 'user') NOT NULL DEFAULT 'technicien',
    created_at   DATETIME     DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id  INT          PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL
);


CREATE TABLE tickets (
    id            INT          PRIMARY KEY AUTO_INCREMENT,
    titre         VARCHAR(255) NOT NULL,
    corps         TEXT         NOT NULL,
    adresse_lieu  VARCHAR(255) NOT NULL,
    priorite      ENUM('low', 'medium', 'hard')                         NOT NULL DEFAULT 'medium',
    statut        ENUM('en cours', 'en attente', 'termine', 'cloture')  NOT NULL DEFAULT 'en cours',
    assign_to     INT          NULL,
    categorie_id  INT          NULL,
    date_creation DATETIME     DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_ticket_utilisateur FOREIGN KEY (assign_to)
        REFERENCES utilisateurs(id) ON DELETE SET NULL,

    CONSTRAINT fk_ticket_categorie FOREIGN KEY (categorie_id)
        REFERENCES categories(id) ON DELETE SET NULL
);

INSERT INTO categories (nom) VALUES
    ('Voirie'),
    ('Eclairage public'),
    ('Espaces verts'),
    ('Signalisation'),
    ('Batiments communaux'),
    ('Autre');

INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES
    ('Administrateur',  'admin@example.com',      '$2y$12$qY4onRz1smO0uLM/3Xd1H.66PlLV0Z9LdgJJX2OeMVeZCjQV4v206', 'admin'),
    ('Jean Technicien', 'technicien@example.com', '$2y$12$qY4onRz1smO0uLM/3Xd1H.66PlLV0Z9LdgJJX2OeMVeZCjQV4v206',  'technicien'),
    ('Marie Agent',     'agent@municipal.local',  '$2y$12$qY4onRz1smO0uLM/3Xd1H.66PlLV0Z9LdgJJX2OeMVeZCjQV4v206', 'agent municipal'),
    ('Secrétaire',       'secretaire@example.com', '$2y$12$qY4onRz1smO0uLM/3Xd1H.66PlLV0Z9LdgJJX2OeMVeZCjQV4v206', 'secretaire'),
    ('User',             'user@example.com',       '$2y$12$qY4onRz1smO0uLM/3Xd1H.66PlLV0Z9LdgJJX2OeMVeZCjQV4v206',  'user');


INSERT INTO tickets (titre, corps, adresse_lieu, priorite, statut, assign_to, categorie_id) VALUES
    ('Nid de poule rue Principale',
     'Un nid de poule important est apparu sur la chaussee, dangereux pour les vehicules.',
     '12 rue Principale',
     'hard', 'en cours', 2, 1),
    ('Lampadaire en panne',
     'Le lampadaire situe devant la mairie est eteint depuis 3 jours.',
     'Place de la Mairie',
     'medium', 'en attente', 3, 2),
    ('Arbres a tailler square Pasteur',
     'Les arbres du square empiettent sur la voie publique.',
     'Square Pasteur',
     'low', 'en cours', NULL, 3);