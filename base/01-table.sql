CREATE TABLE categorie_information(
   id_categorie COUNTER,
   valeur VARCHAR(50),
   PRIMARY KEY(id_categorie)
);

CREATE TABLE article(
   id COUNTER,
   id_source INT,
   id_categorie INT,
   valeur TEXT,
   date_ DATETIME,
   record_track SMALLINT,
   statut LOGICAL,
   PRIMARY KEY(id)
);

CREATE TABLE source(
   id_source COUNTER,
   valeur VARCHAR(50),
   PRIMARY KEY(id_source)
);
