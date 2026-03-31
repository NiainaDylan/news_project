-- =============================================================
-- SCRIPT D'INSERTION -- Données existantes
-- =============================================================


-- -------------------------------------------------------------
-- 1. categorie_information
-- -------------------------------------------------------------
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
-- id_categorie 1 = Diplomatie
-- id_categorie 2 = Humanitaire
-- id_categorie 3 = Energie
-- id_categorie 4 = International
-- id_categorie 5 = Defense


-- -------------------------------------------------------------
-- 2. source
-- -------------------------------------------------------------
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
-- id_source 1 = Agence Monde
-- id_source 2 = Observatoire Geopolitique
-- id_source 3 = Cellule Energie Europe
-- id_source 4 = Redaction ActuFlash


-- -------------------------------------------------------------
-- 3. article  (tickets sans contenu HTML complet — brèves)
-- -----------------------------------------------------------


-- -------------------------------------------------------------
-- 4. article  (articles complets avec contenu HTML)
-- -------------------------------------------------------------

-- id 1 — URGENCE
INSERT INTO article (id, id_source, id_categorie, valeur, date_, date_cache, record_track, statut, title)
VALUES (
    1, 1, 1,
    E'<h1>URGENCE</h1>\n<h2>Reunion d urgence a l ONU: plusieurs delegations soutiennent une proposition de desescalade immediate en Iran.</h2>\r\n<p><img src="../uploads/articles/img_18cdab0cdd0728ef.jpeg" alt="reunion-deleguation" width="674" height="375" data-local-cache="/var/www/html/uploads/articles/img_18cdab0cdd0728ef.jpeg"></p>\r\n<p>&nbsp;</p>',
    '2026-03-31 07:17:27.975825',
    '2026-04-30 23:59:59',
    0, TRUE,
    'URGENCE'
);

-- id 2 — NEGOCIATION
INSERT INTO article (id, id_source, id_categorie, valeur, date_, date_cache, record_track, statut, title)
VALUES (
    2, 2, 1,
    E'<h1>NEGOCIATION</h1>\n<p><img src="../uploads/articles/img_c69be8ee4a05ea31.webp" alt="pourpalers-negociations" width="404" height="229" data-local-cache="/var/www/html/uploads/articles/img_c69be8ee4a05ea31.webp"></p>\r\n<p>Les negociations se poursuivent a huis clos entre representants regionaux pour obtenir une pause humanitaire durable.</p>',
    '2026-03-31 06:37:27.976993',
    '2026-04-30 23:59:59',
    0, TRUE,
    'NEGOCIATION'
);

-- id 7 — IRAN IN CRISIS: THE OPPOSITION SPEAKS
INSERT INTO article (id, id_source, id_categorie, valeur, date_, date_cache, record_track, statut, title)
VALUES (
    7, 1, 1,
    E'<h1>IRAN IN CRISIS: THE OPPOSITION SPEAKS</h1>\n<p><img src="../uploads/articles/img_bb79779a3ff13d3f.webp" alt="Reza-Phalavi" width="621" height="414" data-local-cache="/var/www/html/uploads/articles/img_bb79779a3ff13d3f.webp"></p>\r\n<h1>"Reza Pahlavi Vows Democratic Future for Iran at Major Exile Conference"<br><br></h1>\r\n<p>In front of a striking red and white backdrop bearing what appears to be opposition insignia, a silver-haired man in a navy suit raises a defiant victory sign to the crowd. A small <strong>Iranian</strong> flag pin on his lapel signals his ties to the <strong>Iranian</strong> opposition movement in exile. The polished stage setup and professional lighting suggest a major conference or rally, likely held in Europe or North America, where the <strong>Iranian</strong> diaspora regularly gathers to call for the end of the Islamic Republic. The crown prince s increasingly visible international profile reflects a broader shift in how Western governments and <strong>Iranian</strong> opposition forces are coordinating their efforts to present a credible alternative to the current regime in Tehran. Such events have grown increasingly prominent as pressure on the <strong>Iranian</strong> regime intensifies both from within and abroad.</p>',
    '2026-03-31 07:45:21.832739',
    '2026-04-30 23:59:59',
    0, TRUE,
    'IRAN IN CRISIS: THE OPPOSITION SPEAKS'
);

-- id 8 — REGIONAL DIPLOMACY
INSERT INTO article (id, id_source, id_categorie, valeur, date_, date_cache, record_track, statut, title)
VALUES (
    8, 2, 1,
    E'<h1>REGIONAL DIPLOMACY</h1>\n<p><img src="../uploads/articles/img_3566e31349154612.webp" alt="islamabad-Iran-Gulf-Standoff" width="508" height="338" data-local-cache="/var/www/html/uploads/articles/img_3566e31349154612.webp"></p>\r\n<h1>"Islamabad Steps Up as Key Broker in Iran-Gulf Standoff"</h1>\r\n<p>Inside the formal reception hall of what appears to be the Pakistani Prime Minister s office in Islamabad, senior officials sit in a carefully arranged semi-circle for high-stakes bilateral talks. Pakistani and Gulf Arab delegates face each other across an ornate coffee table, with the green Pakistani national flag and an institutional emblem displayed prominently behind the central figure. A portrait of a founding statesman watches over the proceedings. The meeting is believed to be part of broader regional diplomacy surrounding the escalating <strong>Iran</strong> crisis, with Pakistan &mdash; sharing a long and sensitive border with <strong>Iran</strong> &mdash; playing a key mediating role between Gulf states and Tehran. Islamabad finds itself walking a delicate tightrope, balancing its deep economic ties with Gulf monarchies against its historically complex and culturally intertwined relationship with the <strong>Iranian</strong> state.</p>',
    '2026-03-31 07:50:04.072141',
    '2026-04-30 23:59:59',
    0, TRUE,
    'REGIONAL DIPLOMACY'
);

-- id 9 — IRAN IN CRISIS: INFRASTRUCTURE UNDER FIRE
INSERT INTO article (id, id_source, id_categorie, valeur, date_, date_cache, record_track, statut, title)
VALUES (
    9, 2, 2,
    E'<h1>IRAN IN CRISIS: INFRASTRUCTURE UNDER FIRE</h1>\n<p><img src="../uploads/articles/img_d5ccced52d275a57.jpeg" alt="Strikes-on-Tehran" width="522" height="295" data-local-cache="/var/www/html/uploads/articles/img_d5ccced52d275a57.jpeg"></p>\r\n<h1>"Massive Explosion Tears Through Tehran s Skyline in Broad Daylight"</h1>\r\n<p>Captured from a distance, a towering column of jet-black smoke rises from a building consumed by fierce orange flames at the heart of a densely populated urban area. The surrounding skyline, filled with mid-rise and high-rise buildings typical of <strong>Iran</strong>\ s capital Tehran, frames the scale of the destruction. A fire truck appears dwarfed by the blaze below. The image, taken in full daylight, suggests the explosion occurred during peak hours, raising immediate fears of significant civilian casualties. Analysts and officials have pointed to the incident as one of the most dramatic signs yet of deepening instability inside <strong>Iran</strong>, with attribution still fiercely contested between domestic unrest, industrial accident, and deliberate external military or covert action targeting <strong>Iranian</strong> strategic assets.</p>',
    '2026-03-31 07:52:32.817132',
    '2026-04-30 23:59:59',
    0, TRUE,
    'IRAN IN CRISIS: INFRASTRUCTURE UNDER FIRE'
);

-- id 10 — A NATION UNDER ATTACK
INSERT INTO article (id, id_source, id_categorie, valeur, date_, date_cache, record_track, statut, title)
VALUES (
    10, 4, 2,
    E'<h1>A NATION UNDER ATTACK</h1>\n<p><img src="../uploads/articles/img_8d0fc2b7ae881b72.jpeg" alt="fires-accross-capital" width="516" height="343" data-local-cache="/var/www/html/uploads/articles/img_8d0fc2b7ae881b72.jpeg"></p>\r\n<h1>"Strikes on Tehran: Iranian Flags Stand as Fires Rage Across the Capital"</h1>\r\n<p>In one of the most striking and haunting images to emerge from the ongoing <strong>Iran</strong> crisis, three <strong>Iranian</strong> national flags &mdash; green, white, and red, bearing the iconic emblem of the Islamic Republic &mdash; stand tall and floodlit as a catastrophic inferno rages behind them. The entire horizon glows a deep, hellish orange, with massive clouds of black and grey smoke billowing upward over a city silhouette. The contrast between the institutional symbols of the <strong>Iranian</strong> state and the apocalyptic destruction unfolding behind them captures with devastating clarity the gravity of this historical moment. The image is believed to have been taken in or near Tehran during a coordinated wave of strikes targeting <strong>Iranian</strong> military installations, weapons depots, and energy infrastructure, marking one of the most serious direct attacks on <strong>Iranian</strong> soil in the history of the Islamic Republic.</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>',
    '2026-03-31 07:56:58.652832',
    '2026-04-30 23:59:59',
    0, TRUE,
    'A NATION UNDER ATTACK'
);

-- id 11 — THE SUPREME LEADER RESPONDS
INSERT INTO article (id, id_source, id_categorie, valeur, date_, date_cache, record_track, statut, title)
VALUES (
    11, 1, 4,
    E'<h1>THE SUPREME LEADER RESPONDS</h1>\n<p><img src="../uploads/articles/img_317a0162171c4275.jpeg" alt="Khamanei-Iran-Supreme-Leader" width="522" height="347" data-local-cache="/var/www/html/uploads/articles/img_317a0162171c4275.jpeg"></p>\r\n<h1>"Khamenei Breaks Silence: Iran\ s Supreme Leader Issues Stark Warning to Enemies"</h1>\r\n<p class="font-claude-response-body break-words whitespace-normal leading-[1.7]">Seated before a deep blue curtain and flanked by the <strong>Iranian</strong> flag, an elderly cleric in full black robes and turban grips a sheet of notes as he speaks into a bank of microphones. His expression is stern, measured, and resolute, conveying undiminished authority despite the extraordinary military, political, and economic pressures bearing down on the <strong>Iranian</strong> theocratic state. As the Islamic Republic\ s highest religious and political authority, his rare and carefully choreographed public appearances carry enormous symbolic weight both domestically and internationally. This address &mdash; delivered at a moment of acute national crisis &mdash; is being forensically analyzed by world leaders, intelligence agencies, opposition groups, and the <strong>Iranian</strong> public alike. His words are expected to define <strong>Iran</strong> s official narrative and set the tone for the regime s next strategic and military moves in the weeks ahead.</p>',
    '2026-03-31 07:59:47.826362',
    '2026-04-30 23:59:59',
    0, TRUE,
    'THE SUPREME LEADER RESPONDS'
);

-- id 12 — THE STREETS EXPLODE
INSERT INTO article (id, id_source, id_categorie, valeur, date_, date_cache, record_track, statut, title)
VALUES (
    12, 2, 2,
    E'<h1>THE STREETS EXPLODE</h1>\n<p><img src="../uploads/articles/img_8d7b8828bd862489.jpeg" alt="Tehran-streets-fire" width="1116" height="744" data-local-cache="/var/www/html/uploads/articles/img_8d7b8828bd862489.jpeg"></p>\r\n<h1>"Tehran on the Brink: Mass Protests and Street Fires Signal Regime s Gravest Hour"</h1>\r\n<p>A sweeping elevated view of a major Tehran boulevard captures the raw, uncontainable energy of a city pushed to its breaking point. Hundreds of motorcyclists, pedestrians, and vehicles fill the wide road in chaotic, ungovernable fashion, while on the right side of the frame, a vehicle burns fiercely, sending a thick and ominous column of black smoke into the already haze-filled afternoon sky. A fire engine attempts desperately to reach the scene as swelling crowds press in from all sides. Persian-language shop signs lining the buildings confirm the location as the heart of the <strong>Iranian</strong> capital. The image powerfully echoes the waves of mass protest that have periodically shaken and challenged the <strong>Iranian</strong> regime &mdash; from the 2009 Green Movement to the 2019 fuel protests, to the seismic 2022 "Woman, Life, Freedom" uprising &mdash; and now signals what many observers are calling the most dangerous and potentially regime-threatening eruption of popular anger in the entire history of the Islamic Republic.</p>\r\n<p>&nbsp;</p>',
    '2026-03-31 08:01:35.289556',
    '2026-04-30 23:59:59',
    0, TRUE,
    'THE STREETS EXPLODE'
);


-- -------------------------------------------------------------
-- 5. article_image  (images extraites des champs data-local-cache)
-- -------------------------------------------------------------
INSERT INTO article_image (local_cache, alt, date_cache, id)
VALUES
('/var/www/html/uploads/articles/img_18cdab0cdd0728ef.jpeg',  'reunion-deleguation',          '2026-04-30 23:59:59', 1),
('/var/www/html/uploads/articles/img_c69be8ee4a05ea31.webp',  'pourpalers-negociations',       '2026-04-30 23:59:59', 2),
('/var/www/html/uploads/articles/img_bb79779a3ff13d3f.webp',  'Reza-Phalavi',                  '2026-04-30 23:59:59', 7),
('/var/www/html/uploads/articles/img_3566e31349154612.webp',  'islamabad-Iran-Gulf-Standoff',  '2026-04-30 23:59:59', 8),
('/var/www/html/uploads/articles/img_d5ccced52d275a57.jpeg',  'Strikes-on-Tehran',             '2026-04-30 23:59:59', 9),
('/var/www/html/uploads/articles/img_8d0fc2b7ae881b72.jpeg',  'fires-accross-capital',         '2026-04-30 23:59:59', 10),
('/var/www/html/uploads/articles/img_317a0162171c4275.jpeg',  'Khamanei-Iran-Supreme-Leader',  '2026-04-30 23:59:59', 11),
('/var/www/html/uploads/articles/img_8d7b8828bd862489.jpeg',  'Tehran-streets-fire',           '2026-04-30 23:59:59', 12);


-- -------------------------------------------------------------
-- Réinitialiser les séquences SERIAL pour éviter les conflits
-- (à exécuter après les insertions si les id sont forcés)
-- -------------------------------------------------------------
SELECT setval('article_id_seq',            (SELECT MAX(id)           FROM article));
SELECT setval('article_image_id_image_seq',(SELECT MAX(id_image)     FROM article_image));
SELECT setval('categorie_information_id_categorie_seq', (SELECT MAX(id_categorie) FROM categorie_information));
SELECT setval('source_id_source_seq',      (SELECT MAX(id_source)    FROM source));