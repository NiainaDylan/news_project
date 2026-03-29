CREATE TABLE article_image(
   id_image SERIAL,
   local_cache VARCHAR(255) NOT NULL,
   alt VARCHAR(255) NOT NULL,
   date_cache TIMESTAMP,
   id INT NOT NULL,
   PRIMARY KEY(id_image),
   FOREIGN KEY(id) REFERENCES article(id)
);
