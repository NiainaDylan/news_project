
CREATE TABLE IF NOT EXISTS categorie_information (
    id_categorie SERIAL,
    valeur       VARCHAR(50) NOT NULL,
    PRIMARY KEY (id_categorie)
);

CREATE TABLE IF NOT EXISTS source (
    id_source SERIAL,
    valeur    VARCHAR(50) NOT NULL,
    PRIMARY KEY (id_source)
);

CREATE TABLE IF NOT EXISTS article (
    id           SERIAL,
    id_source    INT,
    id_categorie INT,
    valeur       TEXT,
    date_        TIMESTAMP DEFAULT NOW(),
    record_track SMALLINT  DEFAULT 0,
    statut       BOOLEAN   DEFAULT TRUE,
    PRIMARY KEY (id),
    FOREIGN KEY (id_source)    REFERENCES source(id_source),
    FOREIGN KEY (id_categorie) REFERENCES categorie_information(id_categorie)
);