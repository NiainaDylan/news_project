CREATE TABLE categorie_inforamtion(
   id_categorie COUNTER,
   valeur VARCHAR(50),
   PRIMARY KEY(id_categorie)
);

CREATE TABLE information(
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
