\c news_db;

INSERT INTO categorie_information (valeur)
SELECT v.valeur
FROM (VALUES
	('Diplomatie'),
	('Humanitaire'),
	('Energie'),
	('International'),
	('Defense')
) AS v(valeur)
WHERE NOT EXISTS (
	SELECT 1 FROM categorie_information c WHERE c.valeur = v.valeur
);

INSERT INTO source (valeur)
SELECT v.valeur
FROM (VALUES
	('Agence Monde'),
	('Observatoire Geopolitique'),
	('Cellule Energie Europe'),
	('Redaction ActuFlash')
) AS v(valeur)
WHERE NOT EXISTS (
	SELECT 1 FROM source s WHERE s.valeur = v.valeur
);

INSERT INTO article (id_source, id_categorie, valeur, date_, statut)
SELECT s.id_source, c.id_categorie,
	   'Reunion d urgence a l ONU: plusieurs delegations soutiennent une proposition de desescalade immediate en Iran.',
	   NOW() - INTERVAL '20 minutes',
	   TRUE
FROM source s, categorie_information c
WHERE s.valeur = 'Agence Monde' AND c.valeur = 'Diplomatie';

INSERT INTO article (id_source, id_categorie, valeur, date_, statut)
SELECT s.id_source, c.id_categorie,
	   'Les negociations se poursuivent a huis clos entre representants regionaux pour obtenir une pause humanitaire durable.',
	   NOW() - INTERVAL '1 hour',
	   TRUE
FROM source s, categorie_information c
WHERE s.valeur = 'Observatoire Geopolitique' AND c.valeur = 'Diplomatie';

INSERT INTO article (id_source, id_categorie, valeur, date_, statut)
SELECT s.id_source, c.id_categorie,
	   'Des convois medicaux attendent des garanties de securite pour rejoindre les zones les plus touchees autour de Teheran.',
	   NOW() - INTERVAL '2 hours',
	   TRUE
FROM source s, categorie_information c
WHERE s.valeur = 'Redaction ActuFlash' AND c.valeur = 'Humanitaire';

INSERT INTO article (id_source, id_categorie, valeur, date_, statut)
SELECT s.id_source, c.id_categorie,
	   'Les marches petroliers reagissent a la hausse, les analystes prevoient une volatilite prolongee cette semaine.',
	   NOW() - INTERVAL '3 hours',
	   TRUE
FROM source s, categorie_information c
WHERE s.valeur = 'Cellule Energie Europe' AND c.valeur = 'Energie';

INSERT INTO article (id_source, id_categorie, valeur, date_, statut)
SELECT s.id_source, c.id_categorie,
	   'Plusieurs capitales europeennes annoncent un nouveau cycle de consultations diplomatiques sur le conflit iranien.',
	   NOW() - INTERVAL '5 hours',
	   TRUE
FROM source s, categorie_information c
WHERE s.valeur = 'Agence Monde' AND c.valeur = 'International';

INSERT INTO article (id_source, id_categorie, valeur, date_, statut)
SELECT s.id_source, c.id_categorie,
	   'Des experts militaires observent un repositionnement strategique dans la region, sans confirmation officielle.',
	   NOW() - INTERVAL '8 hours',
	   TRUE
FROM source s, categorie_information c
WHERE s.valeur = 'Observatoire Geopolitique' AND c.valeur = 'Defense';
