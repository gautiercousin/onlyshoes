INSERT INTO COULEUR (nom) VALUES
('Noir'),
('Blanc'),
('Rouge'),
('Bleu'),
('Vert'),
('Jaune'),
('Gris'),
('Rose'),
('Marron'),
('Beige');

INSERT INTO MATERIAU (nom) VALUES
('Cuir'),
('Cuir synthétique'),
('Toile'),
('Daim'),
('Mesh'),
('Caoutchouc'),
('Textile'),
('Nylon');

INSERT INTO MARQUE (nom) VALUES
('Nike'),
('Adidas'),
('Puma'),
('New Balance'),
('Converse'),
('Vans'),
('Reebok'),
('Asics'),
('Saucony'),
('Dr. Martens');

-- Tous les mots de passe = 'password' (bcrypt)
INSERT INTO UTILISATEUR (id_utilisateur, nom, prenom, email, mdp, type_compte, status) VALUES
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'Dupont', 'Marie', 'marie.dupont@email.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'standard', 'actif'),
('b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22', 'Martin', 'Thomas', 'thomas.martin@email.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'standard', 'actif'),
('c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33', 'Bernard', 'Sophie', 'sophie.bernard@email.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'standard', 'actif'),
('d0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44', 'Dubois', 'Lucas', 'lucas.dubois@email.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'standard', 'actif'),
('e0eebc99-9c0b-4ef8-bb6d-6bb9bd380a55', 'Admin', 'System', 'admin@sae.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'actif');
INSERT INTO ADRESSE (rue1, rue2, code_postal, ville, pays, id_utilisateur) VALUES
('12 rue de la Paix', NULL, '75002', 'Paris', 'France', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('45 avenue des Champs', 'Appartement 3B', '44000', 'Nantes', 'France', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
('78 boulevard Victor Hugo', NULL, '69003', 'Lyon', 'France', 'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33'),
('23 rue du Commerce', NULL, '33000', 'Bordeaux', 'France', 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44');

INSERT INTO IMAGE (url, est_principale) VALUES
('/images/annonces/nike-air-max-1.png', TRUE),
('/images/annonces/nike-air-max-2.png', FALSE),
('/images/annonces/adidas-stan-smith-1.png', TRUE),
('/images/annonces/converse-chuck-taylor-1.png', TRUE),
('/images/annonces/vans-old-skool-1.png', TRUE),
('/images/annonces/newbalance-574-1.png', TRUE),
('/images/annonces/puma-suede-1.png', TRUE),
('/images/annonces/dr-martens-1460-1.png', TRUE);

INSERT INTO ANNONCE (id_annonce, titre, description, prix, etat, taille_systeme, taille, disponible, embeddings, id_couleur, id_materiau, id_marque, id_image, id_utilisateur_vendeur) VALUES
('10000000-0000-0000-0000-000000000001', 'Nike Air Max 90 Noir', 'Baskets Nike Air Max 90 en excellent état, portées 3 fois seulement. Très confortables, idéales pour le quotidien.', 89.99, 'comme_neuf', 'EU', '42', TRUE, NULL, 1, 1, 1, 1, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000002', 'Adidas Stan Smith Blanc', 'Classiques Stan Smith blanches avec détails verts. Portées régulièrement mais bien entretenues.', 65.00, 'bon', 'EU', '39', TRUE, NULL, 2, 1, 2, 3, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000003', 'Converse Chuck Taylor All Star', 'Converse montantes noires iconiques. Jamais portées, neuves avec boîte d''origine.', 55.00, 'neuf', 'EU', '41', TRUE, NULL, 1, 3, 5, 4, 'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33'),
('10000000-0000-0000-0000-000000000004', 'Vans Old Skool Noir et Blanc', 'Vans Old Skool classiques, en très bon état. Semelle peu usée.', 45.00, 'tres_bon', 'EU', '43', TRUE, NULL, 1, 3, 6, 5, 'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33'),
('10000000-0000-0000-0000-000000000005', 'New Balance 574 Gris', 'New Balance 574 grises, confortables et stylées. Quelques signes d''usure mais encore beaucoup de vie.', 55.00, 'bon', 'EU', '44', TRUE, NULL, 7, 5, 4, 6, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000006', 'Puma Suede Classic Rouge', 'Puma Suede rouges en daim, état correct. Parfaites pour un look vintage.', 35.00, 'correct', 'EU', '40', TRUE, NULL, 3, 4, 3, 7, 'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33'),
('10000000-0000-0000-0000-000000000007', 'Dr. Martens 1460 Noir', 'Légendaires Dr. Martens 1460 en cuir noir. Bien rodées, très confortables. Petites éraflures.', 95.00, 'bon', 'EU', '41', FALSE, NULL, 1, 1, 10, 8, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),

-- Lot de produits pour tester la pagination (tous pour Marie Dupont - a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11)
('10000000-0000-0000-0000-000000000008', 'Nike Air Force 1 Blanc', 'Air Force 1 blanches classiques, portées quelques fois.', 79.99, 'tres_bon', 'EU', '42', TRUE, NULL, 2, 1, 1, 1, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000009', 'Adidas Superstar Noir', 'Superstar noires iconiques avec les 3 bandes.', 69.99, 'bon', 'EU', '41', TRUE, NULL, 1, 1, 2, 3, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000010', 'Nike Dunk Low Panda', 'Dunk Low coloris Panda très recherché.', 129.99, 'neuf', 'EU', '43', TRUE, NULL, 2, 1, 1, 1, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000011', 'Adidas Gazelle Bleu', 'Gazelle bleues en daim, style rétro.', 75.00, 'comme_neuf', 'EU', '40', TRUE, NULL, 4, 4, 2, 3, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000012', 'Puma RS-X Rouge', 'RS-X rouges ultra confortables.', 85.00, 'tres_bon', 'EU', '44', TRUE, NULL, 3, 5, 3, 7, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000013', 'Nike Air Max 97 Silver', 'Air Max 97 argentées, design futuriste.', 149.99, 'bon', 'EU', '42', TRUE, NULL, 8, 5, 1, 1, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000014', 'New Balance 990v5 Gris', '990v5 grises, le confort ultime.', 159.99, 'comme_neuf', 'EU', '43', TRUE, NULL, 7, 5, 4, 6, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000015', 'Vans Sk8-Hi Noir', 'Sk8-Hi montantes noires classiques.', 59.99, 'bon', 'EU', '41', TRUE, NULL, 1, 3, 6, 5, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000016', 'Converse One Star Vert', 'One Star vertes en daim vintage.', 49.99, 'tres_bon', 'EU', '40', TRUE, NULL, 5, 4, 5, 4, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000017', 'Nike Blazer Mid Blanc', 'Blazer Mid blanches rétro style.', 89.99, 'neuf', 'EU', '42', TRUE, NULL, 2, 1, 1, 1, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000018', 'Adidas Samba OG Noir', 'Samba OG noires, modèle iconique.', 79.99, 'comme_neuf', 'EU', '43', TRUE, NULL, 1, 1, 2, 3, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000019', 'Puma Clyde Court Rouge', 'Clyde Court rouges pour le basket.', 95.00, 'bon', 'EU', '44', TRUE, NULL, 3, 5, 3, 7, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000020', 'New Balance 574 Beige', '574 beiges, style décontracté.', 69.99, 'tres_bon', 'EU', '41', TRUE, NULL, 6, 5, 4, 6, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000021', 'Nike Jordan 1 Low Bred', 'Jordan 1 Low coloris Bred.', 119.99, 'neuf', 'EU', '42', TRUE, NULL, 3, 1, 1, 1, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000022', 'Adidas Yung-1 Blanc', 'Yung-1 blanches chunky style.', 89.99, 'comme_neuf', 'EU', '43', TRUE, NULL, 2, 5, 2, 3, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000023', 'Vans Authentic Rouge', 'Authentic rouges ultra légères.', 45.00, 'bon', 'EU', '40', TRUE, NULL, 3, 3, 6, 5, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000024', 'Converse 70s High Noir', 'Chuck 70s montantes noires premium.', 79.99, 'tres_bon', 'EU', '41', TRUE, NULL, 1, 3, 5, 4, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000025', 'Nike Air Presto Gris', 'Air Presto grises ultra confortables.', 99.99, 'neuf', 'EU', '42', TRUE, NULL, 7, 5, 1, 1, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000026', 'Puma Suede Platform Noir', 'Suede Platform noires à semelle épaisse.', 75.00, 'comme_neuf', 'EU', '39', TRUE, NULL, 1, 4, 3, 7, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000027', 'New Balance 997 Bleu', '997 bleues made in USA premium.', 179.99, 'bon', 'EU', '43', TRUE, NULL, 4, 5, 4, 6, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000028', 'Nike Cortez Blanc Rouge', 'Cortez blanches bande rouge vintage.', 69.99, 'tres_bon', 'EU', '42', TRUE, NULL, 2, 1, 1, 1, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000029', 'Adidas Continental 80 Beige', 'Continental 80 beiges style tennis.', 79.99, 'neuf', 'EU', '41', TRUE, NULL, 6, 1, 2, 3, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000030', 'Vans Era Bleu Marine', 'Era bleu marine classiques.', 49.99, 'comme_neuf', 'EU', '40', TRUE, NULL, 4, 3, 6, 5, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000031', 'Nike React Element 87', 'React Element 87 transparentes.', 129.99, 'bon', 'EU', '43', TRUE, NULL, 2, 5, 1, 1, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000032', 'Puma Thunder Spectra', 'Thunder Spectra multicolores.', 89.99, 'tres_bon', 'EU', '42', TRUE, NULL, 3, 5, 3, 7, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000033', 'Converse Run Star Hike Blanc', 'Run Star Hike blanches plateforme.', 99.99, 'neuf', 'EU', '40', TRUE, NULL, 2, 5, 5, 4, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000034', 'New Balance 327 Gris Orange', '327 grises détails orange.', 89.99, 'comme_neuf', 'EU', '41', TRUE, NULL, 7, 5, 4, 6, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000035', 'Nike Waffle One Blanc', 'Waffle One blanches rétro.', 79.99, 'bon', 'EU', '42', TRUE, NULL, 2, 5, 1, 1, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000036', 'Adidas Forum Low Blanc Bleu', 'Forum Low blanches bandes bleues.', 89.99, 'tres_bon', 'EU', '43', TRUE, NULL, 2, 1, 2, 3, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000037', 'Vans Half Cab Noir', 'Half Cab noires pour le skate.', 69.99, 'neuf', 'EU', '41', TRUE, NULL, 1, 4, 6, 5, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000038', 'Nike SB Dunk Low Pro', 'SB Dunk Low Pro pour skateboard.', 109.99, 'comme_neuf', 'EU', '42', TRUE, NULL, 1, 4, 1, 1, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000039', 'Puma Rider Multicolore', 'Rider multicolores style années 80.', 79.99, 'bon', 'EU', '40', TRUE, NULL, 3, 5, 3, 7, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000040', 'Converse Pro Leather High', 'Pro Leather montantes vintage.', 89.99, 'tres_bon', 'EU', '41', TRUE, NULL, 2, 1, 5, 4, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000041', 'New Balance 1500 Gris', '1500 grises made in England.', 199.99, 'neuf', 'EU', '43', TRUE, NULL, 7, 4, 4, 6, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000042', 'Nike Tailwind 79 Bleu', 'Tailwind 79 bleues rétro running.', 99.99, 'comme_neuf', 'EU', '42', TRUE, NULL, 4, 5, 1, 1, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000043', 'Adidas ZX 500 Vert', 'ZX 500 vertes style street.', 79.99, 'bon', 'EU', '41', TRUE, NULL, 5, 5, 2, 3, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000044', 'Vans Style 36 Jaune', 'Style 36 jaunes eye-catching.', 59.99, 'tres_bon', 'EU', '40', TRUE, NULL, 9, 3, 6, 5, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000045', 'Nike M2K Tekno Blanc', 'M2K Tekno blanches dad shoes.', 99.99, 'neuf', 'EU', '42', TRUE, NULL, 2, 1, 1, 1, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000046', 'Puma Cell Venom Noir', 'Cell Venom noires futuristes.', 89.99, 'comme_neuf', 'EU', '43', TRUE, NULL, 1, 5, 3, 7, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000047', 'New Balance 991 Gris Bleu', '991 grises détails bleus.', 189.99, 'bon', 'EU', '41', TRUE, NULL, 7, 4, 4, 6, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000048', 'Converse Weapon Mid', 'Weapon Mid basket vintage.', 79.99, 'tres_bon', 'EU', '42', TRUE, NULL, 2, 1, 5, 4, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000049', 'Nike Air Huarache Gris', 'Air Huarache grises iconiques.', 109.99, 'neuf', 'EU', '43', TRUE, NULL, 7, 5, 1, 1, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000050', 'Adidas NMD R1 Noir Rouge', 'NMD R1 noires détails rouges.', 119.99, 'comme_neuf', 'EU', '42', TRUE, NULL, 1, 5, 2, 3, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000051', 'Vans Slip-On Checkerboard', 'Slip-On damier noir blanc.', 49.99, 'bon', 'EU', '40', TRUE, NULL, 1, 3, 6, 5, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000052', 'Nike Zoom Vomero 5 Gris', 'Zoom Vomero 5 grises running.', 129.99, 'tres_bon', 'EU', '41', TRUE, NULL, 7, 5, 1, 1, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000053', 'Puma Speedcat OG Blanc', 'Speedcat OG blanches racing.', 99.99, 'neuf', 'EU', '42', TRUE, NULL, 2, 1, 3, 7, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000054', 'New Balance 2002R Gris', '2002R grises protection pack.', 139.99, 'comme_neuf', 'EU', '43', TRUE, NULL, 7, 5, 4, 6, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000055', 'Converse Chuck 70 Vert', 'Chuck 70 vertes coloris unique.', 69.99, 'bon', 'EU', '41', TRUE, NULL, 5, 3, 5, 4, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000056', 'Nike Pegasus Trail 3', 'Pegasus Trail 3 trail running.', 119.99, 'tres_bon', 'EU', '42', TRUE, NULL, 7, 5, 1, 1, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('10000000-0000-0000-0000-000000000057', 'Adidas Handball Spezial Bleu', 'Handball Spezial bleues vintage.', 89.99, 'neuf', 'EU', '40', TRUE, NULL, 4, 4, 2, 3, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11');

INSERT INTO PAIEMENT (type, date, statut, montant_paye) VALUES
('carte_bancaire', CURRENT_TIMESTAMP - INTERVAL '5 days', 'valide', 80.99),
('paypal', CURRENT_TIMESTAMP - INTERVAL '3 days', 'valide', 65.00),
('carte_bancaire', CURRENT_TIMESTAMP - INTERVAL '6 days', 'valide', 55.00);

INSERT INTO COMMANDE (id_commande, date, statut, id_paiement, id_utilisateur) VALUES
('20000000-0000-0000-0000-000000000001', CURRENT_TIMESTAMP - INTERVAL '5 days', 'livree', 1, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
('20000000-0000-0000-0000-000000000002', CURRENT_TIMESTAMP - INTERVAL '3 days', 'livree', 2, 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44'),
('20000000-0000-0000-0000-000000000003', CURRENT_TIMESTAMP - INTERVAL '6 days', 'livree', 3, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22');

INSERT INTO LIGNE_COMMANDE (prix, quantite) VALUES
(80.99, 1),
(65.00, 1),
(55.00, 1);

INSERT INTO DETAILLER_COMMANDE (id_commande, id_annonce, id_ligne_commande) VALUES
('20000000-0000-0000-0000-000000000001', '10000000-0000-0000-0000-000000000001', 1),
('20000000-0000-0000-0000-000000000002', '10000000-0000-0000-0000-000000000002', 2),
('20000000-0000-0000-0000-000000000003', '10000000-0000-0000-0000-000000000003', 3);

INSERT INTO REVIEW (id_review, note, commentaire, date, id_utilisateur_auteur, id_utilisateur_vendeur) VALUES
('90000000-0000-0000-0000-000000000001', 5, 'Excellente transaction ! Chaussures conformes à la description, envoi rapide. Vendeur de confiance.', CURRENT_TIMESTAMP - INTERVAL '4 days', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('90000000-0000-0000-0000-000000000002', 4, 'Bonnes chaussures mais un peu plus usées que prévu. Livraison correcte.', CURRENT_TIMESTAMP - INTERVAL '2 days', 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('90000000-0000-0000-0000-000000000003', 5, 'Parfait ! Communication excellente, emballage soigné.', CURRENT_TIMESTAMP - INTERVAL '1 day', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22', 'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33');


INSERT INTO SIGNALEMENT (motif, description, date, statut, type, id_annonce_cible, id_utilisateur_cible, id_review_cible, id_utilisateur_auteur) VALUES
('Contenu inapproprié', 'Description de l''annonce contient des informations trompeuses sur l''état réel du produit.', CURRENT_TIMESTAMP - INTERVAL '2 days', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000001', NULL, NULL, 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44'),
('Prix abusif', 'Le vendeur demande un prix excessif pour des chaussures usagées. Probablement une arnaque.', CURRENT_TIMESTAMP - INTERVAL '5 days', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000006', NULL, NULL, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
('Produit contrefait', 'Ces chaussures semblent être des contrefaçons. Les détails ne correspondent pas aux modèles authentiques de la marque.', CURRENT_TIMESTAMP - INTERVAL '1 day', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000003', NULL, NULL, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('Comportement inapproprié', 'Cet utilisateur envoie des messages déplacés et harcèle les acheteurs potentiels.', CURRENT_TIMESTAMP - INTERVAL '3 days', 'en_attente', 'user', NULL, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22', NULL, 'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33'),
('Arnaque', 'Ce vendeur a reçu le paiement mais n''a jamais envoyé l''article. Impossible de le contacter depuis.', CURRENT_TIMESTAMP - INTERVAL '4 days', 'en_attente', 'user', NULL, 'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33', NULL, 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44'),
('Faux profil', 'Ce compte utilise des photos volées et se fait passer pour quelqu''un d''autre. Probablement un bot ou un scammer.', CURRENT_TIMESTAMP - INTERVAL '6 hours', 'en_attente', 'user', NULL, 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44', NULL, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11');

INSERT INTO CONSENTEMENT_UTILISATEUR (type_consentement, statut, date_consentement, date_retrait, id_utilisateur) VALUES
('cookies', TRUE, CURRENT_TIMESTAMP - INTERVAL '30 days', NULL, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('conditions_utilisation', TRUE, CURRENT_TIMESTAMP - INTERVAL '30 days', NULL, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('traitement_donnees', TRUE, CURRENT_TIMESTAMP - INTERVAL '30 days', NULL, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('cookies', TRUE, CURRENT_TIMESTAMP - INTERVAL '20 days', NULL, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
('conditions_utilisation', TRUE, CURRENT_TIMESTAMP - INTERVAL '20 days', NULL, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
('traitement_donnees', TRUE, CURRENT_TIMESTAMP - INTERVAL '20 days', NULL, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
('marketing', FALSE, CURRENT_TIMESTAMP - INTERVAL '20 days', CURRENT_TIMESTAMP - INTERVAL '15 days', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
('cookies', TRUE, CURRENT_TIMESTAMP - INTERVAL '15 days', NULL, 'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33'),
('conditions_utilisation', TRUE, CURRENT_TIMESTAMP - INTERVAL '15 days', NULL, 'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33'),
('traitement_donnees', TRUE, CURRENT_TIMESTAMP - INTERVAL '15 days', NULL, 'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33'),
('cookies', TRUE, CURRENT_TIMESTAMP - INTERVAL '10 days', NULL, 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44'),
('conditions_utilisation', TRUE, CURRENT_TIMESTAMP - INTERVAL '10 days', NULL, 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44'),
('traitement_donnees', TRUE, CURRENT_TIMESTAMP - INTERVAL '10 days', NULL, 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44'),
('cookies', TRUE, CURRENT_TIMESTAMP - INTERVAL '365 days', NULL, 'e0eebc99-9c0b-4ef8-bb6d-6bb9bd380a55'),
('conditions_utilisation', TRUE, CURRENT_TIMESTAMP - INTERVAL '365 days', NULL, 'e0eebc99-9c0b-4ef8-bb6d-6bb9bd380a55'),
('traitement_donnees', TRUE, CURRENT_TIMESTAMP - INTERVAL '365 days', NULL, 'e0eebc99-9c0b-4ef8-bb6d-6bb9bd380a55');

INSERT INTO ADMIN_LOG (date_action, action_type, id_cible, raison, ip_address, id_utilisateur) VALUES
(CURRENT_TIMESTAMP - INTERVAL '2 days', 'resoudre_signalement', 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44', 'Signalement vérifié - annonce mise à jour', '192.168.1.100', 'e0eebc99-9c0b-4ef8-bb6d-6bb9bd380a55'),
(CURRENT_TIMESTAMP - INTERVAL '5 days', 'supprimer_annonce', NULL, 'Annonce non conforme - contenu inapproprié', '192.168.1.100', 'e0eebc99-9c0b-4ef8-bb6d-6bb9bd380a55'),
(CURRENT_TIMESTAMP - INTERVAL '10 days', 'suspendre_utilisateur', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22', 'Suspension temporaire - multiples infractions', '192.168.1.100', 'e0eebc99-9c0b-4ef8-bb6d-6bb9bd380a55'),
(CURRENT_TIMESTAMP - INTERVAL '8 days', 'restaurer_utilisateur', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a55', 'Compte restauré après examen', '192.168.1.100', 'e0eebc99-9c0b-4ef8-bb6d-6bb9bd380a55');

-- =============================================================================
-- DONNÉES DE TEST COMPLÉMENTAIRES POUR TESTER TOUTES LES FONCTIONNALITÉS
-- =============================================================================

-- Nouveaux utilisateurs pour tester différents scénarios
INSERT INTO UTILISATEUR (id_utilisateur, nom, prenom, email, mdp, type_compte, status) VALUES
('f0eebc99-9c0b-4ef8-bb6d-6bb9bd380a66', 'Leroy', 'Emma', 'emma.leroy@email.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'standard', 'actif'),
('f1eebc99-9c0b-4ef8-bb6d-6bb9bd380a77', 'Moreau', 'Jules', 'jules.moreau@email.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'standard', 'actif'),
('f2eebc99-9c0b-4ef8-bb6d-6bb9bd380a88', 'Laurent', 'Camille', 'camille.laurent@email.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'standard', 'actif'),
('f3eebc99-9c0b-4ef8-bb6d-6bb9bd380a99', 'Simon', 'Noah', 'noah.simon@email.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'standard', 'actif'),
('f4eebc99-9c0b-4ef8-bb6d-6bb9bd380aaa', 'Michel', 'Lea', 'lea.michel@email.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'standard', 'actif');

INSERT INTO ADRESSE (rue1, rue2, code_postal, ville, pays, id_utilisateur) VALUES
('56 rue Gambetta', NULL, '59000', 'Lille', 'France', 'f0eebc99-9c0b-4ef8-bb6d-6bb9bd380a66'),
('89 avenue de Toulouse', 'Bat C', '31000', 'Toulouse', 'France', 'f1eebc99-9c0b-4ef8-bb6d-6bb9bd380a77'),
('12 place Bellecour', NULL, '69002', 'Lyon', 'France', 'f2eebc99-9c0b-4ef8-bb6d-6bb9bd380a88'),
('34 rue de la République', NULL, '13001', 'Marseille', 'France', 'f3eebc99-9c0b-4ef8-bb6d-6bb9bd380a99'),
('67 boulevard Haussmann', NULL, '75008', 'Paris', 'France', 'f4eebc99-9c0b-4ef8-bb6d-6bb9bd380aaa');

-- Annonces supplémentaires pour différents vendeurs
INSERT INTO ANNONCE (id_annonce, titre, description, prix, etat, taille_systeme, taille, disponible, embeddings, id_couleur, id_materiau, id_marque, id_image, id_utilisateur_vendeur) VALUES
-- Thomas Martin (b0eebc99...)
('10000000-0000-0000-0000-000000000100', 'Nike Air Jordan 1 Chicago', 'Jordan 1 Chicago colorway légendaire. État quasi neuf, portées 2 fois.', 189.99, 'comme_neuf', 'EU', '43', TRUE, NULL, 3, 1, 1, 1, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
('10000000-0000-0000-0000-000000000101', 'Adidas Yeezy Boost 350 V2', 'Yeezy 350 V2 noires authentiques. Très recherchées.', 249.99, 'neuf', 'EU', '42', TRUE, NULL, 1, 5, 2, 3, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
('10000000-0000-0000-0000-000000000102', 'New Balance 990v4 Gris', '990v4 grises made in USA, ultra confortables.', 149.99, 'tres_bon', 'EU', '41', TRUE, NULL, 7, 5, 4, 6, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
('10000000-0000-0000-0000-000000000103', 'Converse All Star 70s', 'Chuck 70s vintage blanches premium.', 79.99, 'bon', 'EU', '40', TRUE, NULL, 2, 3, 5, 4, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
('10000000-0000-0000-0000-000000000104', 'Vans Old Skool Pro', 'Old Skool Pro pour le skate, semelle renforcée.', 69.99, 'tres_bon', 'EU', '42', TRUE, NULL, 1, 4, 6, 5, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
-- Lucas Dubois (d0eebc99...)
('10000000-0000-0000-0000-000000000200', 'Nike SB Dunk Low Travis Scott', 'Dunk Low collaboration Travis Scott, très rares.', 399.99, 'neuf', 'EU', '43', TRUE, NULL, 9, 4, 1, 1, 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44'),
('10000000-0000-0000-0000-000000000201', 'Adidas Ultra Boost 1.0 Blanc', 'Ultra Boost 1.0 blanches OG, confort maximal.', 159.99, 'comme_neuf', 'EU', '42', TRUE, NULL, 2, 5, 2, 3, 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44'),
('10000000-0000-0000-0000-000000000202', 'Asics Gel-Lyte III Gris', 'Gel-Lyte III grises vintage style.', 89.99, 'bon', 'EU', '41', TRUE, NULL, 7, 5, 8, 6, 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44'),
('10000000-0000-0000-0000-000000000203', 'Reebok Club C 85 Blanc', 'Club C 85 blanches classiques tennis.', 59.99, 'tres_bon', 'EU', '40', TRUE, NULL, 2, 1, 7, 6, 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44'),
-- Emma Leroy (f0eebc99...)
('10000000-0000-0000-0000-000000000300', 'Nike Air Max 1 Anniversary', 'Air Max 1 Anniversary édition limitée.', 139.99, 'neuf', 'EU', '38', TRUE, NULL, 2, 1, 1, 1, 'f0eebc99-9c0b-4ef8-bb6d-6bb9bd380a66'),
('10000000-0000-0000-0000-000000000301', 'Adidas Samba Rose Blanc Rose', 'Samba Rose blanches détails roses, plateforme.', 89.99, 'comme_neuf', 'EU', '37', TRUE, NULL, 8, 1, 2, 3, 'f0eebc99-9c0b-4ef8-bb6d-6bb9bd380a66'),
('10000000-0000-0000-0000-000000000302', 'Vans Platform Old Skool', 'Old Skool plateforme noires.', 69.99, 'bon', 'EU', '38', TRUE, NULL, 1, 3, 6, 5, 'f0eebc99-9c0b-4ef8-bb6d-6bb9bd380a66');

-- =============================================================================
-- PAIEMENTS ET COMMANDES POUR TESTER LES STATISTIQUES DE VENTE
-- =============================================================================

-- Ventes récentes CE MOIS-CI (pour Marie Dupont - a0eebc99... vendeur)
INSERT INTO PAIEMENT (type, date, statut, montant_paye) VALUES
-- Cette semaine
('carte_bancaire', CURRENT_TIMESTAMP - INTERVAL '2 days', 'valide', 89.99),
('paypal', CURRENT_TIMESTAMP - INTERVAL '4 days', 'valide', 129.99),
('carte_bancaire', CURRENT_TIMESTAMP - INTERVAL '6 days', 'valide', 79.99),
-- Plus tôt ce mois
('google_pay', CURRENT_TIMESTAMP - INTERVAL '10 days', 'valide', 119.99),
('carte_bancaire', CURRENT_TIMESTAMP - INTERVAL '15 days', 'valide', 99.99),
('paypal', CURRENT_TIMESTAMP - INTERVAL '20 days', 'valide', 149.99);

-- Ventes CETTE ANNÉE mais pas ce mois (pour Marie)
INSERT INTO PAIEMENT (type, date, statut, montant_paye) VALUES
('carte_bancaire', CURRENT_TIMESTAMP - INTERVAL '35 days', 'valide', 159.99),
('paypal', CURRENT_TIMESTAMP - INTERVAL '50 days', 'valide', 89.99),
('carte_bancaire', CURRENT_TIMESTAMP - INTERVAL '75 days', 'valide', 139.99),
('apple_pay', CURRENT_TIMESTAMP - INTERVAL '100 days', 'valide', 179.99);

-- Ventes ANNÉES PRÉCÉDENTES (pour Marie)
INSERT INTO PAIEMENT (type, date, statut, montant_paye) VALUES
('carte_bancaire', CURRENT_TIMESTAMP - INTERVAL '400 days', 'valide', 99.99),
('paypal', CURRENT_TIMESTAMP - INTERVAL '500 days', 'valide', 119.99);

-- Paiements REMBOURSÉS (pour tester le trigger backwards)
INSERT INTO PAIEMENT (type, date, statut, montant_paye) VALUES
('carte_bancaire', CURRENT_TIMESTAMP - INTERVAL '3 days', 'rembourse', 89.99),
('paypal', CURRENT_TIMESTAMP - INTERVAL '8 days', 'refuse', 69.99);

-- Paiements EN ATTENTE (ne devraient pas compter)
INSERT INTO PAIEMENT (type, date, statut, montant_paye) VALUES
('bitcoin', CURRENT_TIMESTAMP - INTERVAL '1 day', 'en_attente', 199.99),
('ethereum', CURRENT_TIMESTAMP - INTERVAL '2 hours', 'en_attente', 249.99);

-- Ventes pour Thomas Martin (b0eebc99... vendeur)
INSERT INTO PAIEMENT (type, date, statut, montant_paye) VALUES
('carte_bancaire', CURRENT_TIMESTAMP - INTERVAL '5 days', 'valide', 189.99),
('paypal', CURRENT_TIMESTAMP - INTERVAL '12 days', 'valide', 249.99),
('carte_bancaire', CURRENT_TIMESTAMP - INTERVAL '40 days', 'valide', 149.99);

-- Ventes pour Lucas Dubois (d0eebc99... vendeur)
INSERT INTO PAIEMENT (type, date, statut, montant_paye) VALUES
('carte_bancaire', CURRENT_TIMESTAMP - INTERVAL '7 days', 'valide', 399.99),
('paypal', CURRENT_TIMESTAMP - INTERVAL '14 days', 'valide', 159.99);

-- Ventes pour Sophie Bernard (c0eebc99... vendeur) - déjà 1 vente existante
INSERT INTO PAIEMENT (type, date, statut, montant_paye) VALUES
('carte_bancaire', CURRENT_TIMESTAMP - INTERVAL '9 days', 'valide', 45.00),
('google_pay', CURRENT_TIMESTAMP - INTERVAL '18 days', 'valide', 35.00);

-- Ventes pour Emma Leroy (f0eebc99... vendeur)
INSERT INTO PAIEMENT (type, date, statut, montant_paye) VALUES
('paypal', CURRENT_TIMESTAMP - INTERVAL '11 days', 'valide', 139.99),
('carte_bancaire', CURRENT_TIMESTAMP - INTERVAL '16 days', 'valide', 89.99);

-- Désactiver temporairement le trigger de vérification de paiement pour l'import de données
ALTER TABLE COMMANDE DISABLE TRIGGER trg_verif_paiement_valide;

-- COMMANDES correspondantes
INSERT INTO COMMANDE (id_commande, date, statut, id_paiement, id_utilisateur) VALUES
-- Achats récents par différents acheteurs (produits de Marie)
('20000000-0000-0000-0000-000000000010', CURRENT_TIMESTAMP - INTERVAL '2 days', 'livree', 4, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
('20000000-0000-0000-0000-000000000011', CURRENT_TIMESTAMP - INTERVAL '4 days', 'livree', 5, 'f1eebc99-9c0b-4ef8-bb6d-6bb9bd380a77'),
('20000000-0000-0000-0000-000000000012', CURRENT_TIMESTAMP - INTERVAL '6 days', 'expediee', 6, 'f2eebc99-9c0b-4ef8-bb6d-6bb9bd380a88'),
('20000000-0000-0000-0000-000000000013', CURRENT_TIMESTAMP - INTERVAL '10 days', 'livree', 7, 'f3eebc99-9c0b-4ef8-bb6d-6bb9bd380a99'),
('20000000-0000-0000-0000-000000000014', CURRENT_TIMESTAMP - INTERVAL '15 days', 'livree', 8, 'f4eebc99-9c0b-4ef8-bb6d-6bb9bd380aaa'),
('20000000-0000-0000-0000-000000000015', CURRENT_TIMESTAMP - INTERVAL '20 days', 'livree', 9, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
-- Cette année (Marie)
('20000000-0000-0000-0000-000000000016', CURRENT_TIMESTAMP - INTERVAL '35 days', 'livree', 10, 'f1eebc99-9c0b-4ef8-bb6d-6bb9bd380a77'),
('20000000-0000-0000-0000-000000000017', CURRENT_TIMESTAMP - INTERVAL '50 days', 'livree', 11, 'f2eebc99-9c0b-4ef8-bb6d-6bb9bd380a88'),
('20000000-0000-0000-0000-000000000018', CURRENT_TIMESTAMP - INTERVAL '75 days', 'livree', 12, 'f3eebc99-9c0b-4ef8-bb6d-6bb9bd380a99'),
('20000000-0000-0000-0000-000000000019', CURRENT_TIMESTAMP - INTERVAL '100 days', 'livree', 13, 'f4eebc99-9c0b-4ef8-bb6d-6bb9bd380aaa'),
-- Années précédentes (Marie)
('20000000-0000-0000-0000-000000000020', CURRENT_TIMESTAMP - INTERVAL '400 days', 'livree', 14, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
('20000000-0000-0000-0000-000000000021', CURRENT_TIMESTAMP - INTERVAL '500 days', 'livree', 15, 'f1eebc99-9c0b-4ef8-bb6d-6bb9bd380a77'),
-- Remboursés (Marie)
('20000000-0000-0000-0000-000000000022', CURRENT_TIMESTAMP - INTERVAL '3 days', 'annulee', 16, 'f2eebc99-9c0b-4ef8-bb6d-6bb9bd380a88'),
('20000000-0000-0000-0000-000000000023', CURRENT_TIMESTAMP - INTERVAL '8 days', 'annulee', 17, 'f3eebc99-9c0b-4ef8-bb6d-6bb9bd380a99'),
-- En attente (Marie)
('20000000-0000-0000-0000-000000000024', CURRENT_TIMESTAMP - INTERVAL '1 day', 'en_preparation', 18, 'f4eebc99-9c0b-4ef8-bb6d-6bb9bd380aaa'),
('20000000-0000-0000-0000-000000000025', CURRENT_TIMESTAMP - INTERVAL '2 hours', 'en_preparation', 19, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
-- Thomas Martin (vendeur)
('20000000-0000-0000-0000-000000000030', CURRENT_TIMESTAMP - INTERVAL '5 days', 'livree', 20, 'f0eebc99-9c0b-4ef8-bb6d-6bb9bd380a66'),
('20000000-0000-0000-0000-000000000031', CURRENT_TIMESTAMP - INTERVAL '12 days', 'livree', 21, 'f2eebc99-9c0b-4ef8-bb6d-6bb9bd380a88'),
('20000000-0000-0000-0000-000000000032', CURRENT_TIMESTAMP - INTERVAL '40 days', 'livree', 22, 'f3eebc99-9c0b-4ef8-bb6d-6bb9bd380a99'),
-- Lucas Dubois (vendeur)
('20000000-0000-0000-0000-000000000040', CURRENT_TIMESTAMP - INTERVAL '7 days', 'livree', 23, 'f1eebc99-9c0b-4ef8-bb6d-6bb9bd380a77'),
('20000000-0000-0000-0000-000000000041', CURRENT_TIMESTAMP - INTERVAL '14 days', 'livree', 24, 'f4eebc99-9c0b-4ef8-bb6d-6bb9bd380aaa'),
-- Sophie Bernard (vendeur)
('20000000-0000-0000-0000-000000000050', CURRENT_TIMESTAMP - INTERVAL '9 days', 'livree', 25, 'f0eebc99-9c0b-4ef8-bb6d-6bb9bd380a66'),
('20000000-0000-0000-0000-000000000051', CURRENT_TIMESTAMP - INTERVAL '18 days', 'livree', 26, 'f1eebc99-9c0b-4ef8-bb6d-6bb9bd380a77'),
-- Emma Leroy (vendeur)
('20000000-0000-0000-0000-000000000060', CURRENT_TIMESTAMP - INTERVAL '11 days', 'livree', 27, 'f3eebc99-9c0b-4ef8-bb6d-6bb9bd380a99'),
('20000000-0000-0000-0000-000000000061', CURRENT_TIMESTAMP - INTERVAL '16 days', 'livree', 28, 'f4eebc99-9c0b-4ef8-bb6d-6bb9bd380aaa');

-- LIGNES DE COMMANDE
INSERT INTO LIGNE_COMMANDE (prix, quantite) VALUES
-- Marie (vendeur)
(89.99, 1), (129.99, 1), (79.99, 1), (119.99, 1), (99.99, 1), (149.99, 1),
(159.99, 1), (89.99, 1), (139.99, 1), (179.99, 1),
(99.99, 1), (119.99, 1),
(89.99, 1), (69.99, 1),
(199.99, 1), (249.99, 1),
-- Thomas
(189.99, 1), (249.99, 1), (149.99, 1),
-- Lucas
(399.99, 1), (159.99, 1),
-- Sophie
(45.00, 1), (35.00, 1),
-- Emma
(139.99, 1), (89.99, 1);

-- DÉTAILLER_COMMANDE (lien commandes -> annonces)
INSERT INTO DETAILLER_COMMANDE (id_commande, id_annonce, id_ligne_commande) VALUES
-- Marie vendeur (id_ligne 4-19)
('20000000-0000-0000-0000-000000000010', '10000000-0000-0000-0000-000000000008', 4),
('20000000-0000-0000-0000-000000000011', '10000000-0000-0000-0000-000000000010', 5),
('20000000-0000-0000-0000-000000000012', '10000000-0000-0000-0000-000000000017', 6),
('20000000-0000-0000-0000-000000000013', '10000000-0000-0000-0000-000000000021', 7),
('20000000-0000-0000-0000-000000000014', '10000000-0000-0000-0000-000000000025', 8),
('20000000-0000-0000-0000-000000000015', '10000000-0000-0000-0000-000000000013', 9),
('20000000-0000-0000-0000-000000000016', '10000000-0000-0000-0000-000000000014', 10),
('20000000-0000-0000-0000-000000000017', '10000000-0000-0000-0000-000000000009', 11),
('20000000-0000-0000-0000-000000000018', '10000000-0000-0000-0000-000000000011', 12),
('20000000-0000-0000-0000-000000000019', '10000000-0000-0000-0000-000000000027', 13),
('20000000-0000-0000-0000-000000000020', '10000000-0000-0000-0000-000000000020', 14),
('20000000-0000-0000-0000-000000000021', '10000000-0000-0000-0000-000000000024', 15),
('20000000-0000-0000-0000-000000000022', '10000000-0000-0000-0000-000000000012', 16),
('20000000-0000-0000-0000-000000000023', '10000000-0000-0000-0000-000000000016', 17),
('20000000-0000-0000-0000-000000000024', '10000000-0000-0000-0000-000000000030', 18),
('20000000-0000-0000-0000-000000000025', '10000000-0000-0000-0000-000000000022', 19),
-- Thomas vendeur (id_ligne 20-22)
('20000000-0000-0000-0000-000000000030', '10000000-0000-0000-0000-000000000100', 20),
('20000000-0000-0000-0000-000000000031', '10000000-0000-0000-0000-000000000101', 21),
('20000000-0000-0000-0000-000000000032', '10000000-0000-0000-0000-000000000102', 22),
-- Lucas vendeur (id_ligne 23-24)
('20000000-0000-0000-0000-000000000040', '10000000-0000-0000-0000-000000000200', 23),
('20000000-0000-0000-0000-000000000041', '10000000-0000-0000-0000-000000000201', 24),
-- Sophie vendeur (id_ligne 25-26)
('20000000-0000-0000-0000-000000000050', '10000000-0000-0000-0000-000000000004', 25),
('20000000-0000-0000-0000-000000000051', '10000000-0000-0000-0000-000000000006', 26),
-- Emma vendeur (id_ligne 27-28)
('20000000-0000-0000-0000-000000000060', '10000000-0000-0000-0000-000000000300', 27),
('20000000-0000-0000-0000-000000000061', '10000000-0000-0000-0000-000000000301', 28);

-- =============================================================================
-- REVIEWS SUPPLÉMENTAIRES POUR TESTER LE SYSTÈME D'AVIS ET LA PAGINATION
-- =============================================================================

-- Reviews pour Marie Dupont (a0eebc99... vendeur) - déjà 2 existantes
INSERT INTO REVIEW (note, commentaire, date, id_utilisateur_auteur, id_utilisateur_vendeur) VALUES
(5, 'Livraison ultra rapide, emballage parfait. Chaussures exactement comme décrites!', CURRENT_TIMESTAMP - INTERVAL '2 days', 'f1eebc99-9c0b-4ef8-bb6d-6bb9bd380a77', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
(4, 'Très bien, quelques petites traces d''usure mais acceptable pour le prix.', CURRENT_TIMESTAMP - INTERVAL '6 days', 'f2eebc99-9c0b-4ef8-bb6d-6bb9bd380a88', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
(5, 'Vendeuse au top! Communication excellente, je recommande vivement.', CURRENT_TIMESTAMP - INTERVAL '10 days', 'f3eebc99-9c0b-4ef8-bb6d-6bb9bd380a99', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
(5, 'Transaction parfaite du début à la fin. Merci!', CURRENT_TIMESTAMP - INTERVAL '15 days', 'f4eebc99-9c0b-4ef8-bb6d-6bb9bd380aaa', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
(3, 'Produit conforme mais délai de livraison un peu long.', CURRENT_TIMESTAMP - INTERVAL '20 days', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
(5, 'Super vendeur, réponse rapide à toutes mes questions!', CURRENT_TIMESTAMP - INTERVAL '35 days', 'f1eebc99-9c0b-4ef8-bb6d-6bb9bd380a77', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
(4, 'Bien emballé, livraison soignée. Petit défaut non mentionné mais rien de grave.', CURRENT_TIMESTAMP - INTERVAL '50 days', 'f2eebc99-9c0b-4ef8-bb6d-6bb9bd380a88', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
(5, 'Chaussures magnifiques! État impeccable, exactement ce que je cherchais.', CURRENT_TIMESTAMP - INTERVAL '75 days', 'f3eebc99-9c0b-4ef8-bb6d-6bb9bd380a99', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
(5, 'Excellent rapport qualité/prix. Vendeur fiable et professionnel.', CURRENT_TIMESTAMP - INTERVAL '100 days', 'f4eebc99-9c0b-4ef8-bb6d-6bb9bd380aaa', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11');

-- Reviews pour Thomas Martin (b0eebc99... vendeur)
INSERT INTO REVIEW (note, commentaire, date, id_utilisateur_auteur, id_utilisateur_vendeur) VALUES
(5, 'Sneakers authentiques et en parfait état. Très satisfait!', CURRENT_TIMESTAMP - INTERVAL '5 days', 'f0eebc99-9c0b-4ef8-bb6d-6bb9bd380a66', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
(5, 'Vendeur sérieux, transaction rapide et sans problème.', CURRENT_TIMESTAMP - INTERVAL '12 days', 'f2eebc99-9c0b-4ef8-bb6d-6bb9bd380a88', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
(4, 'Bonnes chaussures, livraison correcte. RAS.', CURRENT_TIMESTAMP - INTERVAL '40 days', 'f3eebc99-9c0b-4ef8-bb6d-6bb9bd380a99', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22');

-- Reviews pour Lucas Dubois (d0eebc99... vendeur)
INSERT INTO REVIEW (note, commentaire, date, id_utilisateur_auteur, id_utilisateur_vendeur) VALUES
(5, 'Paire de rêve! Envoi ultra rapide, emballage de qualité premium.', CURRENT_TIMESTAMP - INTERVAL '7 days', 'f1eebc99-9c0b-4ef8-bb6d-6bb9bd380a77', 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44'),
(5, 'Impeccable! Le vendeur a même ajouté un petit cadeau, top classe.', CURRENT_TIMESTAMP - INTERVAL '14 days', 'f4eebc99-9c0b-4ef8-bb6d-6bb9bd380aaa', 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44');

-- Reviews pour Sophie Bernard (c0eebc99... vendeur) - déjà 1 existante
INSERT INTO REVIEW (note, commentaire, date, id_utilisateur_auteur, id_utilisateur_vendeur) VALUES
(4, 'Bien, conforme à la description. Livraison standard.', CURRENT_TIMESTAMP - INTERVAL '9 days', 'f0eebc99-9c0b-4ef8-bb6d-6bb9bd380a66', 'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33'),
(5, 'Super vendeuse, très arrangeante sur les frais de port!', CURRENT_TIMESTAMP - INTERVAL '18 days', 'f1eebc99-9c0b-4ef8-bb6d-6bb9bd380a77', 'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33');

-- Reviews pour Emma Leroy (f0eebc99... vendeur)
INSERT INTO REVIEW (note, commentaire, date, id_utilisateur_auteur, id_utilisateur_vendeur) VALUES
(5, 'Chaussures magnifiques et en parfait état! Merci beaucoup.', CURRENT_TIMESTAMP - INTERVAL '11 days', 'f3eebc99-9c0b-4ef8-bb6d-6bb9bd380a99', 'f0eebc99-9c0b-4ef8-bb6d-6bb9bd380a66'),
(4, 'Très bien, juste un peu plus de traces que prévu mais acceptable.', CURRENT_TIMESTAMP - INTERVAL '16 days', 'f4eebc99-9c0b-4ef8-bb6d-6bb9bd380aaa', 'f0eebc99-9c0b-4ef8-bb6d-6bb9bd380a66');

-- =============================================================================
-- SIGNALEMENTS SUPPLÉMENTAIRES
-- =============================================================================

INSERT INTO SIGNALEMENT (motif, description, date, statut, type, id_annonce_cible, id_utilisateur_cible, id_review_cible, id_utilisateur_auteur) VALUES
('Spam', 'Ce vendeur envoie des messages publicitaires non sollicités.', CURRENT_TIMESTAMP - INTERVAL '12 hours', 'en_attente', 'user', NULL, 'f1eebc99-9c0b-4ef8-bb6d-6bb9bd380a77', NULL, 'f2eebc99-9c0b-4ef8-bb6d-6bb9bd380a88'),
('Avis frauduleux', 'Cet avis semble faux, probablement posté par le vendeur lui-même.', CURRENT_TIMESTAMP - INTERVAL '8 hours', 'en_attente', 'review', NULL, NULL, '90000000-0000-0000-0000-000000000001', 'f3eebc99-9c0b-4ef8-bb6d-6bb9bd380a99'),
('Prix trompeur', 'Le prix affiché ne correspond pas au prix final demandé.', CURRENT_TIMESTAMP - INTERVAL '18 hours', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000100', NULL, NULL, 'f4eebc99-9c0b-4ef8-bb6d-6bb9bd380aaa'),
('Langage inapproprié', 'Commentaire contenant des insultes et du langage vulgaire.', CURRENT_TIMESTAMP - INTERVAL '3 hours', 'en_attente', 'review', NULL, NULL, '90000000-0000-0000-0000-000000000002', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('Spam', 'Envoi de messages publicitaires non sollicités.', CURRENT_TIMESTAMP - INTERVAL '1 hour', 'en_attente', 'user', NULL, 'f1eebc99-9c0b-4ef8-bb6d-6bb9bd380a77', NULL, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('Avis frauduleux', 'Cet avis semble écrit par le vendeur lui-même.', CURRENT_TIMESTAMP - INTERVAL '2 hours', 'en_attente', 'review', NULL, NULL, '90000000-0000-0000-0000-000000000001', 'f2eebc99-9c0b-4ef8-bb6d-6bb9bd380a88'),
('Prix trompeur', 'Le prix affiché diffère du prix demandé après contact.', CURRENT_TIMESTAMP - INTERVAL '3 hours', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000100', NULL, NULL, 'f3eebc99-9c0b-4ef8-bb6d-6bb9bd380a99'),
('Langage inapproprié', 'Commentaire contenant des insultes.', CURRENT_TIMESTAMP - INTERVAL '4 hours', 'en_attente', 'review', NULL, NULL, '90000000-0000-0000-0000-000000000002', 'f4eebc99-9c0b-4ef8-bb6d-6bb9bd380aaa'),
('Image volée', 'Les photos semblent provenir d''un autre site, possible contrefaçon.', CURRENT_TIMESTAMP - INTERVAL '6 hours', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000101', NULL, NULL, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
('Annonce trompeuse', 'La description omet des défauts importants.', CURRENT_TIMESTAMP - INTERVAL '8 hours', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000102', NULL, NULL, 'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33'),
('Comportement agressif', 'Messages agressifs envers un acheteur potentiel.', CURRENT_TIMESTAMP - INTERVAL '10 hours', 'en_attente', 'user', NULL, 'f0eebc99-9c0b-4ef8-bb6d-6bb9bd380a66', NULL, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
('Annonce fausse', 'L''article vendu n''existe pas, probable arnaque.', CURRENT_TIMESTAMP - INTERVAL '12 hours', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000200', NULL, NULL, 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44'),
('Tentative d''escroquerie', 'Demande de virement avant expédition.', CURRENT_TIMESTAMP - INTERVAL '1 day', 'en_attente', 'user', NULL, 'f2eebc99-9c0b-4ef8-bb6d-6bb9bd380a88', NULL, 'f1eebc99-9c0b-4ef8-bb6d-6bb9bd380a77'),
('Contenu inapproprié', 'Texte à caractère sexuel dans la description.', CURRENT_TIMESTAMP - INTERVAL '1 day', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000103', NULL, NULL, 'f3eebc99-9c0b-4ef8-bb6d-6bb9bd380a99'),
('Faux profil', 'Compte utilisant une identité volée.', CURRENT_TIMESTAMP - INTERVAL '2 days', 'en_attente', 'user', NULL, 'f3eebc99-9c0b-4ef8-bb6d-6bb9bd380a99', NULL, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('Multiples annonces identiques', 'Le même article est publié plusieurs fois, spam.', CURRENT_TIMESTAMP - INTERVAL '2 days', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000104', NULL, NULL, 'f4eebc99-9c0b-4ef8-bb6d-6bb9bd380aaa'),
('Réponse tardive', 'Aucun suivi après la vente, acheteur bloqué.', CURRENT_TIMESTAMP - INTERVAL '3 days', 'en_attente', 'user', NULL, 'f4eebc99-9c0b-4ef8-bb6d-6bb9bd380aaa', NULL, 'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33'),
('Annonce illégale', 'Article prohibé mis en vente.', CURRENT_TIMESTAMP - INTERVAL '3 days', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000300', NULL, NULL, 'f0eebc99-9c0b-4ef8-bb6d-6bb9bd380a66'),
('Usurpation', 'Compte usurpe une marque connue.', CURRENT_TIMESTAMP - INTERVAL '4 days', 'en_attente', 'user', NULL, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22', NULL, 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44'),
('Mauvaise foi', 'Vendeur refuse le retour sans raison valable.', CURRENT_TIMESTAMP - INTERVAL '4 days', 'en_attente', 'user', NULL, 'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33', NULL, 'f1eebc99-9c0b-4ef8-bb6d-6bb9bd380a77'),
('Photo offensante', 'Image contenant symbole offensant.', CURRENT_TIMESTAMP - INTERVAL '5 days', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000201', NULL, NULL, 'f2eebc99-9c0b-4ef8-bb6d-6bb9bd380a88'),
('Contact hors plateforme', 'Vendeur demande de poursuivre en dehors du site.', CURRENT_TIMESTAMP - INTERVAL '5 days', 'en_attente', 'user', NULL, 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44', NULL, 'f3eebc99-9c0b-4ef8-bb6d-6bb9bd380a99'),
('Escroquerie signalée', 'Plusieurs acheteurs n''ont pas reçu l''article.', CURRENT_TIMESTAMP - INTERVAL '6 days', 'en_attente', 'user', NULL, 'f0eebc99-9c0b-4ef8-bb6d-6bb9bd380a66', NULL, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
('Annonce mensongère', 'Etat indiqué comme ''neuf'' alors que usé.', CURRENT_TIMESTAMP - INTERVAL '6 days', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000200', NULL, NULL, 'f4eebc99-9c0b-4ef8-bb6d-6bb9bd380aaa'),
('Contenu haineux', 'Message à caractère haineux dans le commentaire.', CURRENT_TIMESTAMP - INTERVAL '7 days', 'en_attente', 'review', NULL, NULL, '90000000-0000-0000-0000-000000000003', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('Annonce périmée', 'Annonce encore en ligne après vente.', CURRENT_TIMESTAMP - INTERVAL '8 days', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000202', NULL, NULL, 'f1eebc99-9c0b-4ef8-bb6d-6bb9bd380a77'),
('Profils multiples', 'Même personne possède plusieurs comptes fraude.', CURRENT_TIMESTAMP - INTERVAL '8 days', 'en_attente', 'user', NULL, 'f2eebc99-9c0b-4ef8-bb6d-6bb9bd380a88', NULL, 'f0eebc99-9c0b-4ef8-bb6d-6bb9bd380a66'),
('Message indésirable', 'Demande répétée pour acheter sans paiement sécurisé.', CURRENT_TIMESTAMP - INTERVAL '9 days', 'en_attente', 'user', NULL, 'f3eebc99-9c0b-4ef8-bb6d-6bb9bd380a99', NULL, 'f4eebc99-9c0b-4ef8-bb6d-6bb9bd380aaa'),
('Produit dangereux', 'Objet potentiellement dangereux mis en vente.', CURRENT_TIMESTAMP - INTERVAL '10 days', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000203', NULL, NULL, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
('Mauvaise description', 'Taille incorrecte indiquée dans l''annonce.', CURRENT_TIMESTAMP - INTERVAL '11 days', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000300', NULL, NULL, 'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33'),
('Offre douteuse', 'Prix trop bas pour être vrai, suspicion d''arnaque.', CURRENT_TIMESTAMP - INTERVAL '12 days', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000301', NULL, NULL, 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44'),
('Harcelement', 'Messages répétés après refus d''achat.', CURRENT_TIMESTAMP - INTERVAL '13 days', 'en_attente', 'user', NULL, 'f4eebc99-9c0b-4ef8-bb6d-6bb9bd380aaa', NULL, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
('Non conformite', 'Article reçu très différent de la photo.', CURRENT_TIMESTAMP - INTERVAL '14 days', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000301', NULL, NULL, 'f1eebc99-9c0b-4ef8-bb6d-6bb9bd380a77'),
('Contact abusif', 'Demandes d''informations personnelles non nécessaires.', CURRENT_TIMESTAMP - INTERVAL '15 days', 'en_attente', 'user', NULL, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22', NULL, 'f2eebc99-9c0b-4ef8-bb6d-6bb9bd380a88'),
('Arnaque au remboursement', 'Vendeur propose de rembourser mais demande des frais.', CURRENT_TIMESTAMP - INTERVAL '16 days', 'en_attente', 'user', NULL, 'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33', NULL, 'f3eebc99-9c0b-4ef8-bb6d-6bb9bd380a99'),
('Photos trompeuses', 'Images retouchées pour cacher défauts.', CURRENT_TIMESTAMP - INTERVAL '17 days', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000102', NULL, NULL, 'f4eebc99-9c0b-4ef8-bb6d-6bb9bd380aaa'),
('Offre hors-politique', 'Vendeur propose services interdits.', CURRENT_TIMESTAMP - INTERVAL '18 days', 'en_attente', 'user', NULL, 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44', NULL, 'f0eebc99-9c0b-4ef8-bb6d-6bb9bd380a66'),
('Faux commentaire', 'Commentaire suspect publié pour améliorer note.', CURRENT_TIMESTAMP - INTERVAL '19 days', 'en_attente', 'review', NULL, NULL, '90000000-0000-0000-0000-000000000001', 'f1eebc99-9c0b-4ef8-bb6d-6bb9bd380a77'),
('Mise en danger', 'Objet potentiellement frauduleux demandé en échange.', CURRENT_TIMESTAMP - INTERVAL '20 days', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000202', NULL, NULL, 'f2eebc99-9c0b-4ef8-bb6d-6bb9bd380a88'),
('Profil suspect', 'Comportement automatisé observé.', CURRENT_TIMESTAMP - INTERVAL '21 days', 'en_attente', 'user', NULL, 'f3eebc99-9c0b-4ef8-bb6d-6bb9bd380a99', NULL, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'),
('Annonce hors catégorie', 'Article mal classé, trompe les recherches.', CURRENT_TIMESTAMP - INTERVAL '22 days', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000104', NULL, NULL, 'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33'),
('Publicité déguisée', 'Annonce qui renvoie vers un site externe commercial.', CURRENT_TIMESTAMP - INTERVAL '23 days', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000103', NULL, NULL, 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44'),
('Injonction abusive', 'Vendeur menace d''évaluer négativement si pas de paiement immédiat.', CURRENT_TIMESTAMP - INTERVAL '24 days', 'en_attente', 'user', NULL, 'f4eebc99-9c0b-4ef8-bb6d-6bb9bd380aaa', NULL, 'f0eebc99-9c0b-4ef8-bb6d-6bb9bd380a66'),
('Multiples tentatives', 'Tentatives de contact répétées pour pousser à l''achat.', CURRENT_TIMESTAMP - INTERVAL '25 days', 'en_attente', 'user', NULL, 'f1eebc99-9c0b-4ef8-bb6d-6bb9bd380a77', NULL, 'f2eebc99-9c0b-4ef8-bb6d-6bb9bd380a88'),
('Annonce expirée', 'Annonce toujours affichée après suppression demandée.', CURRENT_TIMESTAMP - INTERVAL '26 days', 'en_attente', 'annonce', '10000000-0000-0000-0000-000000000300', NULL, NULL, 'f3eebc99-9c0b-4ef8-bb6d-6bb9bd380a99'),
('Harcèlement ciblé', 'Commentaires répétés visant une personne spécifique.', CURRENT_TIMESTAMP - INTERVAL '27 days', 'en_attente', 'review', NULL, NULL, '90000000-0000-0000-0000-000000000002', 'f4eebc99-9c0b-4ef8-bb6d-6bb9bd380aaa');


-- Réactiver le trigger de vérification de paiement après l'import
ALTER TABLE COMMANDE ENABLE TRIGGER trg_verif_paiement_valide;

-- =============================================================================
-- INITIALISATION DES STATISTIQUES DE VENTE
-- =============================================================================
-- Les triggers ne se déclenchent que sur UPDATE, pas sur INSERT.
-- On doit donc calculer manuellement les stats initiales pour tous les vendeurs.

SELECT update_user_sales_stats('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'); -- Marie Dupont
SELECT update_user_sales_stats('b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22'); -- Thomas Martin
SELECT update_user_sales_stats('c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33'); -- Sophie Bernard
SELECT update_user_sales_stats('d0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44'); -- Lucas Dubois
SELECT update_user_sales_stats('f0eebc99-9c0b-4ef8-bb6d-6bb9bd380a66'); -- Emma Leroy
