
CREATE TABLE COULEUR (
  id_couleur SERIAL PRIMARY KEY,
  nom        VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE MATERIAU (
  id_materiau SERIAL PRIMARY KEY,
  nom         VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE MARQUE (
  id_marque SERIAL PRIMARY KEY,
  nom       VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE UTILISATEUR (
  id_utilisateur UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  nom            VARCHAR(100) NOT NULL,
  prenom         VARCHAR(100) NOT NULL,
  email          VARCHAR(255) NOT NULL UNIQUE,
  mdp            VARCHAR(255) NOT NULL,
  type_compte    VARCHAR(20) NOT NULL CHECK (type_compte IN ('standard', 'admin')),
  status         VARCHAR(20) NOT NULL DEFAULT 'actif' CHECK (status IN ('actif', 'suspendu', 'bannis')),
  date_creation  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  note_moyenne   DECIMAL(2,1) DEFAULT 0.0 CHECK (note_moyenne >= 0 AND note_moyenne <= 5),
  nombre_avis    INTEGER DEFAULT 0 CHECK (nombre_avis >= 0),
  montant_total_ventes DECIMAL(12,2) DEFAULT 0.00 CHECK (montant_total_ventes >= 0),
  montant_mois_actuel DECIMAL(12,2) DEFAULT 0.00 CHECK (montant_mois_actuel >= 0),
  montant_annee_actuelle DECIMAL(12,2) DEFAULT 0.00 CHECK (montant_annee_actuelle >= 0)
);

CREATE TABLE ADRESSE (
  id_adresse     SERIAL PRIMARY KEY,
  rue1           VARCHAR(255) NOT NULL,
  rue2           VARCHAR(255),
  code_postal    VARCHAR(10) NOT NULL,
  ville          VARCHAR(100) NOT NULL,
  pays           VARCHAR(100) NOT NULL DEFAULT 'France',
  id_utilisateur UUID NOT NULL,
  FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR (id_utilisateur) ON DELETE CASCADE
);

CREATE TABLE IMAGE (
  id_image       SERIAL PRIMARY KEY,
  url            VARCHAR(500) NOT NULL,
  est_principale BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE ANNONCE (
  id_annonce             UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  titre                  VARCHAR(200) NOT NULL,
  description            TEXT NOT NULL,
  prix                   DECIMAL(10,2) NOT NULL CHECK (prix >= 0),
  etat                   VARCHAR(20) NOT NULL CHECK (etat IN ('neuf', 'comme_neuf', 'tres_bon', 'bon', 'correct')),
  taille_systeme         VARCHAR(10) CHECK (taille_systeme IN ('EU', 'US', 'UK')),
  taille                 VARCHAR(10) NOT NULL,
  date_publication       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  disponible             BOOLEAN NOT NULL DEFAULT TRUE,
  embeddings             vector(384),
  id_couleur             INTEGER NOT NULL,
  id_materiau            INTEGER NOT NULL,
  id_marque              INTEGER NOT NULL,
  id_image               INTEGER NOT NULL,
  id_utilisateur_vendeur UUID NOT NULL,
  FOREIGN KEY (id_couleur) REFERENCES COULEUR (id_couleur),
  FOREIGN KEY (id_materiau) REFERENCES MATERIAU (id_materiau),
  FOREIGN KEY (id_marque) REFERENCES MARQUE (id_marque),
  FOREIGN KEY (id_image) REFERENCES IMAGE (id_image),
  FOREIGN KEY (id_utilisateur_vendeur) REFERENCES UTILISATEUR (id_utilisateur) ON DELETE CASCADE
);

CREATE TABLE PAIEMENT (
  id_paiement  SERIAL PRIMARY KEY,
  type         VARCHAR(30) NOT NULL CHECK (type IN ('carte_bancaire', 'paypal', 'google_pay', 'apple_pay', 'bitcoin', 'monero', 'ethereum')),
  date         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  statut       VARCHAR(20) NOT NULL CHECK (statut IN ('en_attente', 'valide', 'refuse', 'rembourse')),
  montant_paye DECIMAL(10,2) NOT NULL CHECK (montant_paye >= 0)
);

CREATE TABLE COMMANDE (
  id_commande    UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  date           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  statut         VARCHAR(30) NOT NULL CHECK (statut IN ('en_preparation', 'expediee', 'livree', 'annulee')),
  id_paiement    INTEGER NOT NULL UNIQUE,
  id_utilisateur UUID NOT NULL,
  FOREIGN KEY (id_paiement) REFERENCES PAIEMENT (id_paiement),
  FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR (id_utilisateur) ON DELETE CASCADE
);

CREATE TABLE LIGNE_COMMANDE (
  id_ligne_commande SERIAL PRIMARY KEY,
  prix              DECIMAL(10,2) NOT NULL CHECK (prix >= 0),
  quantite          INTEGER NOT NULL CHECK (quantite > 0)
);

CREATE TABLE DETAILLER_COMMANDE (
  id_commande       UUID NOT NULL,
  id_annonce        UUID NOT NULL,
  id_ligne_commande INTEGER NOT NULL,
  PRIMARY KEY (id_commande, id_annonce, id_ligne_commande),
  FOREIGN KEY (id_commande) REFERENCES COMMANDE (id_commande) ON DELETE CASCADE,
  FOREIGN KEY (id_annonce) REFERENCES ANNONCE (id_annonce) ON DELETE CASCADE,
  FOREIGN KEY (id_ligne_commande) REFERENCES LIGNE_COMMANDE (id_ligne_commande) ON DELETE CASCADE
);

CREATE TABLE REVIEW (
  id_review              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  note                   INTEGER NOT NULL CHECK (note >= 1 AND note <= 5),
  commentaire            TEXT,
  date                   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  id_utilisateur_auteur  UUID NOT NULL,
  id_utilisateur_vendeur UUID NOT NULL,
  FOREIGN KEY (id_utilisateur_auteur) REFERENCES UTILISATEUR (id_utilisateur) ON DELETE CASCADE,
  FOREIGN KEY (id_utilisateur_vendeur) REFERENCES UTILISATEUR (id_utilisateur) ON DELETE CASCADE,
  CHECK (id_utilisateur_auteur != id_utilisateur_vendeur)
);

CREATE TABLE SIGNALEMENT (
  id_signalement        SERIAL PRIMARY KEY,
  motif                 VARCHAR(100) NOT NULL,
  description           TEXT NOT NULL,
  date                  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  statut                VARCHAR(20) NOT NULL CHECK (statut IN ('en_attente', 'traite', 'rejete')),
  type                  VARCHAR(20) NOT NULL CHECK (type IN ('review', 'user', 'annonce')),
  id_review_cible       UUID,
  id_utilisateur_cible  UUID,
  id_annonce_cible      UUID,
  id_utilisateur_auteur UUID NOT NULL,
  raison_decision       TEXT,
  date_traitement       TIMESTAMP,
  
  FOREIGN KEY (id_utilisateur_auteur) REFERENCES UTILISATEUR (id_utilisateur) ON DELETE CASCADE,
  FOREIGN KEY (id_review_cible) REFERENCES REVIEW (id_review) ON DELETE CASCADE,
  FOREIGN KEY (id_utilisateur_cible) REFERENCES UTILISATEUR (id_utilisateur) ON DELETE CASCADE,
  FOREIGN KEY (id_annonce_cible) REFERENCES ANNONCE (id_annonce) ON DELETE CASCADE,
  
  CHECK (
    (type = 'review' AND id_review_cible IS NOT NULL AND id_utilisateur_cible IS NULL AND id_annonce_cible IS NULL) OR
    (type = 'user' AND id_utilisateur_cible IS NOT NULL AND id_review_cible IS NULL AND id_annonce_cible IS NULL) OR
    (type = 'annonce' AND id_annonce_cible IS NOT NULL AND id_review_cible IS NULL AND id_utilisateur_cible IS NULL)
  )
);

CREATE TABLE ADMIN_LOG (
  id_log         SERIAL PRIMARY KEY,
  date_action    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  action_type    VARCHAR(50) NOT NULL CHECK (action_type IN ('bannir_utilisateur', 'suspendre_utilisateur', 'supprimer_annonce', 'resoudre_signalement', 'rejeter_signalement', 'restaurer_utilisateur', 'modifier_commande')),
  id_cible       UUID,
  raison         TEXT,
  ip_address     INET,
  id_utilisateur UUID NOT NULL,
  FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR (id_utilisateur) ON DELETE CASCADE
);

CREATE TABLE CONSENTEMENT_UTILISATEUR (
  id_consentement   SERIAL PRIMARY KEY,
  type_consentement VARCHAR(50) NOT NULL CHECK (type_consentement IN ('cookies', 'conditions_utilisation', 'traitement_donnees', 'marketing')),
  statut            BOOLEAN DEFAULT TRUE,
  date_consentement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  date_retrait      TIMESTAMP,
  id_utilisateur    UUID NOT NULL,
  FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR (id_utilisateur) ON DELETE CASCADE
);