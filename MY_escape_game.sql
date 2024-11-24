DROP TABLE objets;
DROP TABLE joueurs;

CREATE TABLE objets (id integer generated always as identity PRIMARY KEY,
nom character varying,
objet_type character varying,
importance character varying,
indice character varying,
img character varying,
taille INT[],
position_ancre INT[],
objet_debloque character varying,
objet_libere character varying,
debut boolean,
minZoom integer,
geometrie geometry

);

INSERT INTO objets (nom, objet_type,importance, indice, img, taille, position_ancre, objet_debloque, objet_libere, debut, minzoom, geometrie) 
VALUES 
('ordinateur', 'bloque_code', NULL, 'Paul ne se rappelle plus de son mot de passe. Va chez lui (A Serris) le récupérer dans son carnet de code.', 'ordinateur', ARRAY[100, 100], ARRAY[50, 50], '2', '7', TRUE, 18, ST_GeomFromText('POINT(2.5878 48.8410)', 4326)),
('carnet', 'objet_code', NULL,'0011', 'carnet', ARRAY[100, 100], ARRAY[50, 50], NULL, NULL, False, 16, ST_GeomFromText('POINT(2.7883 48.8704)', 4326)),
('maison', 'bloque_objet', NULL,'La maison est fermée, Paul ne t'|| '*' ||'a pas remis son badge va le récupérer dans sa voiture au garage pour révision (Au Drive Intermarché proche de la Forêt Régionale des Vaillières).', 'maison', ARRAY[160, 160], ARRAY[80, 80], '4', '2', TRUE, 16, ST_GeomFromText('POINT(2.7883 48.8704)', 4326)),
('badge', 'objet_recuperable', 'moyenne',NULL, 'badge', ARRAY[100, 100], ARRAY[50, 50], NULL, NULL, False, 17, ST_GeomFromText('POINT(2.7168 48.8960)', 4326)),
('voiture', 'bloque_objet', NULL,'Le garagiste est parti en intervention à Orly-Aéroprt avec la clé. Va le retrouver.', 'voiture', ARRAY[160, 160], ARRAY[80, 80], '6', '4', TRUE, 17, ST_GeomFromText('POINT(2.7168 48.8960)', 4326)),
('cle de voiture', 'objet_recuperable', 'faible',NULL, 'cle_de_voiture', ARRAY[100, 100], ARRAY[50, 50], NULL, NULL, TRUE, 18, ST_GeomFromText('POINT(2.3670 48.7278)', 4326)),
('presentation', 'objet_recuperable', 'haute', NULL, 'presentation', ARRAY[160, 160], ARRAY[80, 80], NULL, NULL, FALSE, 18, ST_GeomFromText('POINT(2.5878 48.8410)', 4326));

CREATE TABLE joueurs (id integer generated always as identity PRIMARY KEY,
pseudo character varying,
genre character varying,
score integer
);

INSERT INTO joueurs (pseudo, genre, score) VALUES 
('Kenny','Homme',350),('Bryan','Homme',100),
('Félix','Homme',500),('Pierre','Homme',450),
('Karine','Femme',600),('Julie','Femme',610),
('Marie','Femme',560),('Jonathan','Homme',290),
('Célestin','Homme',100),('Carole','Femme',130);

