DROP TABLE IF EXISTS bulletins;
DROP TABLE IF EXISTS historiques;
DROP TABLE IF EXISTS vents;
DROP TABLE IF EXISTS temperatures;
DROP TABLE IF EXISTS precipitations;
DROP TABLE IF EXISTS capteurs;
DROP VIEW IF EXISTS vMassif;
DROP VIEW IF EXISTS vVille;
DROP TABLE IF EXISTS villes;
DROP TABLE IF EXISTS massifs;
DROP TABLE IF EXISTS departements;
DROP TABLE IF EXISTS regions;
DROP TABLE IF EXISTS lieux;




CREATE TABLE lieux (
	nom VARCHAR(50) PRIMARY KEY, 
	seuilTp float,
	seuilVt float,
	seuilPr int CHECK(seuilPr>=1 AND seuilPr<=10)
);

CREATE TABLE regions (
	num VARCHAR(3) PRIMARY KEY,
	nom VARCHAR(100) NOT NULL
);

CREATE TABLE departements(
	departement_id int PRIMARY KEY,
	nom VARCHAR(50) UNIQUE NOT NULL,
	region_id VARCHAR(3) references regions(num) NOT NULL
);

CREATE TABLE massifs (
	nom VARCHAR(50) PRIMARY KEY references lieux(nom),
	d1 int references departements(departement_id) NOT NULL,
	d2 int references departements(departement_id)
);

CREATE TABLE villes(
	nom VARCHAR(50) references lieux(nom),
	dpt int references departements(departement_id),
	PRIMARY KEY(nom,dpt)
);

CREATE TABLE capteurs (
	capteur_id serial PRIMARY KEY,
	lieu_id VARCHAR(50) references lieux(nom)
);


CREATE TABLE precipitations (
	precipitation_id serial PRIMARY KEY,
	type VARCHAR(50) NOT NULL, 
	seuil int CHECK (seuil>=1 AND seuil<=10),
	force int CHECK (force>=1 AND force<=10) NOT NULL,
	info TEXT,
	capteur_id serial references capteurs(capteur_id) NOT NULL,
	CONSTRAINT chk_type_value CHECK(type in ('Pluie', 'Neige', 'Grêle', 'Sable'))
);

CREATE TABLE temperatures (
	temperature_id serial PRIMARY KEY,
	reelle float NOT NULL,
	ressentie float NOT NULL,
	seuil float,
	info TEXT,
	capteur_id serial references capteurs(capteur_id) NOT NULL
);

CREATE TABLE vents(
	vent_id serial PRIMARY KEY,
	force float NOT NULL,
	direction VARCHAR(10) NOT NULL,
	seuil float,
	info TEXT,
	capteur_id serial references capteurs(capteur_id) NOT NULL,
	CONSTRAINT chk_dir_value CHECK(direction in ('Nord', 'Sud', 'Ouest', 'Est'))
);


CREATE TABLE historiques(
	capteur_id serial references capteurs(capteur_id),
	debut timestamp,
	fin timestamp NOT NULL,
	lieu_id VARCHAR(50) references lieux(nom) NOT NULL,
	PRIMARY KEY (capteur_id,debut),
	UNIQUE (capteur_id, fin)
);

CREATE TABLE bulletins(
	bulletin_id serial,
	lieu_id VARCHAR(50) references lieux(nom),
	moment VARCHAR(20) CHECK(moment in('Matin', 'Après-midi', 'Soirée', 'Nuit')) NOT NULL,
	date timestamp NOT NULL,
	precipitation_id serial references precipitations(precipitation_id),
	temperature_id serial references temperatures(temperature_id),
	vent_id serial references vents(vent_id),
	CONSTRAINT chk_pr_te_ve CHECK( precipitation_id is not null or temperature_id is not null or vent_id is not null),
	PRIMARY KEY(bulletin_id,lieu_id),
	UNIQUE(date,moment)
);

create or replace view vMassif as (select l.nom, l.seuilTp, l.seuilVt, l.seuilPr, m.d1, m.d2 from lieux l, massifs m where l.nom = m.nom);

create or replace view vVille as (select l.nom, l.seuilTp, l.seuilVt, l.seuilPr, v.dpt from lieux l, villes v where l.nom = v.nom);






