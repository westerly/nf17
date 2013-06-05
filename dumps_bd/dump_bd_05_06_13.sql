--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

ALTER TABLE ONLY public.villes DROP CONSTRAINT villes_nom_fkey;
ALTER TABLE ONLY public.villes DROP CONSTRAINT villes_dpt_fkey;
ALTER TABLE ONLY public.vents DROP CONSTRAINT vents_capteur_id_fkey;
ALTER TABLE ONLY public.temperatures DROP CONSTRAINT temperatures_capteur_id_fkey;
ALTER TABLE ONLY public.precipitations DROP CONSTRAINT precipitations_capteur_id_fkey;
ALTER TABLE ONLY public.massifs DROP CONSTRAINT massifs_nom_fkey;
ALTER TABLE ONLY public.massifs DROP CONSTRAINT massifs_d2_fkey;
ALTER TABLE ONLY public.massifs DROP CONSTRAINT massifs_d1_fkey;
ALTER TABLE ONLY public.historiques DROP CONSTRAINT historiques_lieu_id_fkey;
ALTER TABLE ONLY public.historiques DROP CONSTRAINT historiques_capteur_id_fkey;
ALTER TABLE ONLY public.departements DROP CONSTRAINT departements_region_id_fkey;
ALTER TABLE ONLY public.capteurs DROP CONSTRAINT capteurs_lieu_id_fkey;
ALTER TABLE ONLY public.bulletins DROP CONSTRAINT bulletins_vent_id_fkey;
ALTER TABLE ONLY public.bulletins DROP CONSTRAINT bulletins_precipitation_id_fkey1;
ALTER TABLE ONLY public.bulletins DROP CONSTRAINT bulletins_precipitation_id_fkey;
ALTER TABLE ONLY public.bulletins DROP CONSTRAINT bulletins_lieu_id_fkey;
DROP TRIGGER handle_update_capteurs ON public.capteurs;
DROP TRIGGER handle_create_capteurs ON public.capteurs;
ALTER TABLE ONLY public.villes DROP CONSTRAINT villes_pkey;
ALTER TABLE ONLY public.vents DROP CONSTRAINT vents_pkey;
ALTER TABLE ONLY public.temperatures DROP CONSTRAINT temperatures_pkey;
ALTER TABLE ONLY public.regions DROP CONSTRAINT regions_pkey;
ALTER TABLE ONLY public.precipitations DROP CONSTRAINT precipitations_pkey;
ALTER TABLE ONLY public.massifs DROP CONSTRAINT massifs_pkey;
ALTER TABLE ONLY public.lieux DROP CONSTRAINT lieux_pkey;
ALTER TABLE ONLY public.historiques DROP CONSTRAINT historiques_pkey;
ALTER TABLE ONLY public.historiques DROP CONSTRAINT historiques_capteur_id_fin_key;
ALTER TABLE ONLY public.departements DROP CONSTRAINT departements_pkey;
ALTER TABLE ONLY public.departements DROP CONSTRAINT departements_nom_key;
ALTER TABLE ONLY public.capteurs DROP CONSTRAINT capteurs_pkey;
ALTER TABLE ONLY public.bulletins DROP CONSTRAINT bulletins_pkey;
ALTER TABLE ONLY public.bulletins DROP CONSTRAINT bulletins_date_moment_key;
ALTER TABLE public.vents ALTER COLUMN capteur_id DROP DEFAULT;
ALTER TABLE public.vents ALTER COLUMN vent_id DROP DEFAULT;
ALTER TABLE public.temperatures ALTER COLUMN capteur_id DROP DEFAULT;
ALTER TABLE public.temperatures ALTER COLUMN temperature_id DROP DEFAULT;
ALTER TABLE public.precipitations ALTER COLUMN capteur_id DROP DEFAULT;
ALTER TABLE public.precipitations ALTER COLUMN precipitation_id DROP DEFAULT;
ALTER TABLE public.historiques ALTER COLUMN capteur_id DROP DEFAULT;
ALTER TABLE public.capteurs ALTER COLUMN capteur_id DROP DEFAULT;
ALTER TABLE public.bulletins ALTER COLUMN vent_id DROP DEFAULT;
ALTER TABLE public.bulletins ALTER COLUMN temperature_id DROP DEFAULT;
ALTER TABLE public.bulletins ALTER COLUMN precipitation_id DROP DEFAULT;
ALTER TABLE public.bulletins ALTER COLUMN bulletin_id DROP DEFAULT;
DROP VIEW public.vville;
DROP VIEW public.vmassif;
DROP VIEW public.vlieudeptreg;
DROP TABLE public.villes;
DROP SEQUENCE public.vents_vent_id_seq;
DROP SEQUENCE public.vents_capteur_id_seq;
DROP TABLE public.vents;
DROP SEQUENCE public.temperatures_temperature_id_seq;
DROP SEQUENCE public.temperatures_capteur_id_seq;
DROP TABLE public.temperatures;
DROP TABLE public.regions;
DROP SEQUENCE public.precipitations_precipitation_id_seq;
DROP SEQUENCE public.precipitations_capteur_id_seq;
DROP TABLE public.precipitations;
DROP TABLE public.massifs;
DROP TABLE public.lieux;
DROP SEQUENCE public.historiques_capteur_id_seq;
DROP TABLE public.historiques;
DROP TABLE public.departements;
DROP SEQUENCE public.capteurs_capteur_id_seq;
DROP TABLE public.capteurs;
DROP SEQUENCE public.bulletins_vent_id_seq;
DROP SEQUENCE public.bulletins_temperature_id_seq;
DROP SEQUENCE public.bulletins_precipitation_id_seq;
DROP SEQUENCE public.bulletins_bulletin_id_seq;
DROP TABLE public.bulletins;
DROP FUNCTION public.process_update_capteur();
DROP FUNCTION public.process_insert_capteur();
DROP EXTENSION plpgsql;
DROP SCHEMA public;
--
-- Name: nf17; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON DATABASE nf17 IS 'Base de données pour le projet NF17 sur les bulletins météo.';


--
-- Name: public; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA public;


ALTER SCHEMA public OWNER TO postgres;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'standard public schema';


--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- Name: process_insert_capteur(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION process_insert_capteur() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

    BEGIN

		-- Nouveau capteur dans un lieu => insertion d'un nouvel historique pour ce capteur

	  IF NEW.lieu_id is not null THEN

		INSERT INTO historiques 

		VALUES(NEW.capteur_id,NOW(),NEW.lieu_id,null);

	  END IF;

      RETURN NULL; -- result is ignored since this is an AFTER trigger

    END;

$$;


ALTER FUNCTION public.process_insert_capteur() OWNER TO postgres;

--
-- Name: process_update_capteur(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION process_update_capteur() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

    BEGIN

		IF OLD.lieu_id is not null OR NEW.lieu_id is not null OR OLD.lieu_id <> NEW.lieu_id THEN

			-- Affectation d'un capteur dans un autre lieu à partir du lieu courant

		  IF NEW.lieu_id is not null THEN

			IF OLD.lieu_id is not null THEN 

				-- MAJ de la date de fin de l'historique pour le lieu courant

				UPDATE historiques SET fin = NOW() WHERE capteur_id = OLD.capteur_id AND fin is null;

			END IF;

			-- insertion d'un nouvel historique pour l'affectation du capteur au nouveau lieu

			INSERT INTO historiques

			VALUES(OLD.capteur_id,NOW(),NEW.lieu_id,null);

		  ELSE -- Cas ou on met le capteur en réparation

			UPDATE historiques SET fin = NOW() WHERE capteur_id = OLD.capteur_id AND fin is null;

		  END IF;

		END IF;

      RETURN NULL; -- result is ignored since this is an AFTER trigger

    END;

$$;


ALTER FUNCTION public.process_update_capteur() OWNER TO postgres;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: bulletins; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE bulletins (
    bulletin_id integer NOT NULL,
    lieu_id character varying(50) NOT NULL,
    moment character varying(20) NOT NULL,
    date timestamp without time zone NOT NULL,
    precipitation_id integer,
    temperature_id integer,
    vent_id integer,
    CONSTRAINT bulletins_moment_check CHECK (((moment)::text = ANY (ARRAY[('Matin'::character varying)::text, ('Après-midi'::character varying)::text, ('Soirée'::character varying)::text, ('Nuit'::character varying)::text]))),
    CONSTRAINT chk_or_not_null CHECK ((((temperature_id IS NOT NULL) OR (precipitation_id IS NOT NULL)) OR (vent_id IS NOT NULL)))
);


ALTER TABLE public.bulletins OWNER TO postgres;

--
-- Name: bulletins_bulletin_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE bulletins_bulletin_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.bulletins_bulletin_id_seq OWNER TO postgres;

--
-- Name: bulletins_bulletin_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE bulletins_bulletin_id_seq OWNED BY bulletins.bulletin_id;


--
-- Name: bulletins_precipitation_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE bulletins_precipitation_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.bulletins_precipitation_id_seq OWNER TO postgres;

--
-- Name: bulletins_precipitation_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE bulletins_precipitation_id_seq OWNED BY bulletins.precipitation_id;


--
-- Name: bulletins_temperature_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE bulletins_temperature_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.bulletins_temperature_id_seq OWNER TO postgres;

--
-- Name: bulletins_temperature_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE bulletins_temperature_id_seq OWNED BY bulletins.temperature_id;


--
-- Name: bulletins_vent_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE bulletins_vent_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.bulletins_vent_id_seq OWNER TO postgres;

--
-- Name: bulletins_vent_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE bulletins_vent_id_seq OWNED BY bulletins.vent_id;


--
-- Name: capteurs; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE capteurs (
    capteur_id integer NOT NULL,
    lieu_id character varying(50)
);


ALTER TABLE public.capteurs OWNER TO postgres;

--
-- Name: capteurs_capteur_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE capteurs_capteur_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.capteurs_capteur_id_seq OWNER TO postgres;

--
-- Name: capteurs_capteur_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE capteurs_capteur_id_seq OWNED BY capteurs.capteur_id;


--
-- Name: departements; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE departements (
    departement_id integer NOT NULL,
    nom character varying(50) NOT NULL,
    region_id character varying(3) NOT NULL
);


ALTER TABLE public.departements OWNER TO postgres;

--
-- Name: historiques; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE historiques (
    capteur_id integer NOT NULL,
    debut timestamp without time zone NOT NULL,
    lieu_id character varying(50) NOT NULL,
    fin timestamp without time zone
);


ALTER TABLE public.historiques OWNER TO postgres;

--
-- Name: historiques_capteur_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE historiques_capteur_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.historiques_capteur_id_seq OWNER TO postgres;

--
-- Name: historiques_capteur_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE historiques_capteur_id_seq OWNED BY historiques.capteur_id;


--
-- Name: lieux; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE lieux (
    nom character varying(50) NOT NULL,
    seuiltp double precision,
    seuilvt double precision,
    seuilpr integer,
    CONSTRAINT lieux_seuilpr_check CHECK (((seuilpr >= 1) AND (seuilpr <= 10)))
);


ALTER TABLE public.lieux OWNER TO postgres;

--
-- Name: massifs; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE massifs (
    nom character varying(50) NOT NULL,
    d1 integer NOT NULL,
    d2 integer
);


ALTER TABLE public.massifs OWNER TO postgres;

--
-- Name: precipitations; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE precipitations (
    precipitation_id integer NOT NULL,
    type character varying(50) NOT NULL,
    seuil integer,
    force integer NOT NULL,
    info text,
    capteur_id integer NOT NULL,
    CONSTRAINT chk_type_value CHECK (((type)::text = ANY (ARRAY[('Pluie'::character varying)::text, ('Neige'::character varying)::text, ('Grêle'::character varying)::text, ('Sable'::character varying)::text]))),
    CONSTRAINT precipitations_force_check CHECK (((force >= 1) AND (force <= 10))),
    CONSTRAINT precipitations_seuil_check CHECK (((seuil >= 1) AND (seuil <= 10)))
);


ALTER TABLE public.precipitations OWNER TO postgres;

--
-- Name: precipitations_capteur_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE precipitations_capteur_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.precipitations_capteur_id_seq OWNER TO postgres;

--
-- Name: precipitations_capteur_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE precipitations_capteur_id_seq OWNED BY precipitations.capteur_id;


--
-- Name: precipitations_precipitation_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE precipitations_precipitation_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.precipitations_precipitation_id_seq OWNER TO postgres;

--
-- Name: precipitations_precipitation_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE precipitations_precipitation_id_seq OWNED BY precipitations.precipitation_id;


--
-- Name: regions; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE regions (
    num character varying(3) NOT NULL,
    nom character varying(100) NOT NULL
);


ALTER TABLE public.regions OWNER TO postgres;

--
-- Name: temperatures; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE temperatures (
    temperature_id integer NOT NULL,
    reelle double precision NOT NULL,
    ressentie double precision NOT NULL,
    seuil double precision,
    info text,
    capteur_id integer NOT NULL
);


ALTER TABLE public.temperatures OWNER TO postgres;

--
-- Name: temperatures_capteur_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE temperatures_capteur_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.temperatures_capteur_id_seq OWNER TO postgres;

--
-- Name: temperatures_capteur_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE temperatures_capteur_id_seq OWNED BY temperatures.capteur_id;


--
-- Name: temperatures_temperature_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE temperatures_temperature_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.temperatures_temperature_id_seq OWNER TO postgres;

--
-- Name: temperatures_temperature_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE temperatures_temperature_id_seq OWNED BY temperatures.temperature_id;


--
-- Name: vents; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE vents (
    vent_id integer NOT NULL,
    force double precision NOT NULL,
    direction character varying(10) NOT NULL,
    seuil double precision,
    info text,
    capteur_id integer NOT NULL,
    CONSTRAINT chk_dir_value CHECK (((direction)::text = ANY (ARRAY[('Nord'::character varying)::text, ('Sud'::character varying)::text, ('Ouest'::character varying)::text, ('Est'::character varying)::text])))
);


ALTER TABLE public.vents OWNER TO postgres;

--
-- Name: vents_capteur_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE vents_capteur_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.vents_capteur_id_seq OWNER TO postgres;

--
-- Name: vents_capteur_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE vents_capteur_id_seq OWNED BY vents.capteur_id;


--
-- Name: vents_vent_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE vents_vent_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.vents_vent_id_seq OWNER TO postgres;

--
-- Name: vents_vent_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE vents_vent_id_seq OWNED BY vents.vent_id;


--
-- Name: villes; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE villes (
    nom character varying(50) NOT NULL,
    dpt integer NOT NULL
);


ALTER TABLE public.villes OWNER TO postgres;

--
-- Name: vlieudeptreg; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW vlieudeptreg AS
    (SELECT v.nom AS lieu, v.dpt AS dept, d.region_id AS region FROM (villes v JOIN departements d ON ((d.departement_id = v.dpt))) UNION SELECT m.nom AS lieu, m.d1 AS dept, d.region_id AS region FROM (massifs m JOIN departements d ON ((d.departement_id = m.d1)))) UNION SELECT m.nom AS lieu, m.d2 AS dept, d.region_id AS region FROM (massifs m JOIN departements d ON ((d.departement_id = m.d1))) WHERE (m.d2 IS NOT NULL);


ALTER TABLE public.vlieudeptreg OWNER TO postgres;

--
-- Name: VIEW vlieudeptreg; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON VIEW vlieudeptreg IS 'Cette vue regroupe toutes les associations lieu-departement des tables villes et massifs Ainsi que les associations departement-region de la table departement';


--
-- Name: vmassif; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW vmassif AS
    SELECT l.nom, l.seuiltp, l.seuilvt, l.seuilpr, m.d1, m.d2 FROM lieux l, massifs m WHERE ((l.nom)::text = (m.nom)::text);


ALTER TABLE public.vmassif OWNER TO postgres;

--
-- Name: vville; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW vville AS
    SELECT l.nom, l.seuiltp, l.seuilvt, l.seuilpr, v.dpt FROM lieux l, villes v WHERE ((l.nom)::text = (v.nom)::text);


ALTER TABLE public.vville OWNER TO postgres;

--
-- Name: bulletin_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY bulletins ALTER COLUMN bulletin_id SET DEFAULT nextval('bulletins_bulletin_id_seq'::regclass);


--
-- Name: precipitation_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY bulletins ALTER COLUMN precipitation_id SET DEFAULT nextval('bulletins_precipitation_id_seq'::regclass);


--
-- Name: temperature_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY bulletins ALTER COLUMN temperature_id SET DEFAULT nextval('bulletins_temperature_id_seq'::regclass);


--
-- Name: vent_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY bulletins ALTER COLUMN vent_id SET DEFAULT nextval('bulletins_vent_id_seq'::regclass);


--
-- Name: capteur_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY capteurs ALTER COLUMN capteur_id SET DEFAULT nextval('capteurs_capteur_id_seq'::regclass);


--
-- Name: capteur_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY historiques ALTER COLUMN capteur_id SET DEFAULT nextval('historiques_capteur_id_seq'::regclass);


--
-- Name: precipitation_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY precipitations ALTER COLUMN precipitation_id SET DEFAULT nextval('precipitations_precipitation_id_seq'::regclass);


--
-- Name: capteur_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY precipitations ALTER COLUMN capteur_id SET DEFAULT nextval('precipitations_capteur_id_seq'::regclass);


--
-- Name: temperature_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY temperatures ALTER COLUMN temperature_id SET DEFAULT nextval('temperatures_temperature_id_seq'::regclass);


--
-- Name: capteur_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY temperatures ALTER COLUMN capteur_id SET DEFAULT nextval('temperatures_capteur_id_seq'::regclass);


--
-- Name: vent_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY vents ALTER COLUMN vent_id SET DEFAULT nextval('vents_vent_id_seq'::regclass);


--
-- Name: capteur_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY vents ALTER COLUMN capteur_id SET DEFAULT nextval('vents_capteur_id_seq'::regclass);


--
-- Data for Name: bulletins; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO bulletins VALUES (16, 'Toulouse', 'Matin', '2012-09-30 00:00:00', 56, 22, NULL);
INSERT INTO bulletins VALUES (18, 'Bordeaux', 'Après-midi', '2012-09-30 00:00:00', 58, 24, 34);
INSERT INTO bulletins VALUES (19, 'Bordeaux', 'Soirée', '2012-09-30 00:00:00', NULL, 25, 35);
INSERT INTO bulletins VALUES (20, 'Bordeaux', 'Nuit', '2012-09-30 00:00:00', NULL, 26, NULL);


--
-- Name: bulletins_bulletin_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('bulletins_bulletin_id_seq', 20, true);


--
-- Name: bulletins_precipitation_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('bulletins_precipitation_id_seq', 1, false);


--
-- Name: bulletins_temperature_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('bulletins_temperature_id_seq', 1, false);


--
-- Name: bulletins_vent_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('bulletins_vent_id_seq', 1, false);


--
-- Data for Name: capteurs; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO capteurs VALUES (58, 'Toulouse');
INSERT INTO capteurs VALUES (57, 'Toulouse');
INSERT INTO capteurs VALUES (56, 'Bordeaux');


--
-- Name: capteurs_capteur_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('capteurs_capteur_id_seq', 58, true);


--
-- Data for Name: departements; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO departements VALUES (1, 'Ain', '22');
INSERT INTO departements VALUES (2, 'Aisne', '19');
INSERT INTO departements VALUES (3, 'Allier', '2');
INSERT INTO departements VALUES (5, 'Alpes (Hautes)', '21');
INSERT INTO departements VALUES (6, 'Alpes Maritimes', '21');
INSERT INTO departements VALUES (7, 'Ardéche', '22');
INSERT INTO departements VALUES (8, 'Ardennes', '6');
INSERT INTO departements VALUES (9, ' Ariége', '14');
INSERT INTO departements VALUES (10, 'Aube', '6');
INSERT INTO departements VALUES (11, 'Aude', '11');
INSERT INTO departements VALUES (12, 'Aveyron', '14');
INSERT INTO departements VALUES (13, 'Bouches du Rhône', '21');
INSERT INTO departements VALUES (15, 'Cantal', '2');
INSERT INTO departements VALUES (16, 'Charente', '20');
INSERT INTO departements VALUES (17, 'Charente Maritime', '20');
INSERT INTO departements VALUES (18, 'Cher', '5');
INSERT INTO departements VALUES (19, 'Corréze', '12');
INSERT INTO departements VALUES (23, 'Creuse ', '12');
INSERT INTO departements VALUES (24, 'Dordogne', '1');
INSERT INTO departements VALUES (25, 'Doubs', '9');
INSERT INTO departements VALUES (26, 'Drôme', '22');
INSERT INTO departements VALUES (27, 'Eure', '17');
INSERT INTO departements VALUES (28, 'Eure et Loir', '5');
INSERT INTO departements VALUES (29, 'Finistére', '4');
INSERT INTO departements VALUES (30, 'Gard', '11');
INSERT INTO departements VALUES (31, 'Garonne (Haute)', '14');
INSERT INTO departements VALUES (32, 'Gers', '14');
INSERT INTO departements VALUES (33, 'Gironde', '1');
INSERT INTO departements VALUES (34, 'Hérault', '11');
INSERT INTO departements VALUES (35, 'Ile et Vilaine', '4');
INSERT INTO departements VALUES (36, 'Indre', '5');
INSERT INTO departements VALUES (37, 'Indre et Loire', '5');
INSERT INTO departements VALUES (38, 'Isére', '22');
INSERT INTO departements VALUES (39, 'Jura', '9');
INSERT INTO departements VALUES (40, 'Landes', '1');
INSERT INTO departements VALUES (41, 'Loir et Cher', '5');
INSERT INTO departements VALUES (42, 'Loire', '22');
INSERT INTO departements VALUES (43, 'Loire (Haute)', '2');
INSERT INTO departements VALUES (44, 'Loire Atlantique', '18');
INSERT INTO departements VALUES (45, 'Loiret', '5');
INSERT INTO departements VALUES (46, 'Lot', '14');
INSERT INTO departements VALUES (47, 'Lot et Garonne', '1');
INSERT INTO departements VALUES (48, 'Lozére', '11');
INSERT INTO departements VALUES (49, 'Maine et Loire', '18');
INSERT INTO departements VALUES (51, 'Marne', '6');
INSERT INTO departements VALUES (52, 'Marne (Haute)', '6');
INSERT INTO departements VALUES (53, 'Mayenne', '18');
INSERT INTO departements VALUES (54, 'Meurthe et Moselle', '13');
INSERT INTO departements VALUES (55, 'Meuse', '13');
INSERT INTO departements VALUES (56, 'Morbihan', '4');
INSERT INTO departements VALUES (57, 'Moselle', '13');
INSERT INTO departements VALUES (58, 'Niévre', '3');
INSERT INTO departements VALUES (59, 'Nord', '15');
INSERT INTO departements VALUES (60, 'Oise', '19');
INSERT INTO departements VALUES (62, 'Pas de Calais', '15');
INSERT INTO departements VALUES (63, 'Puy de Dôme', '2');
INSERT INTO departements VALUES (64, 'Pyrénées Atlantiques', '1');
INSERT INTO departements VALUES (65, 'Pyrénées (Hautes)', '14');
INSERT INTO departements VALUES (66, 'Pyrénées Orientales', '11');
INSERT INTO departements VALUES (69, 'Rhône', '22');
INSERT INTO departements VALUES (70, 'Saône (Haute)', '9');
INSERT INTO departements VALUES (71, 'Saône et Loire', '3');
INSERT INTO departements VALUES (72, 'Sarthe', '18');
INSERT INTO departements VALUES (73, 'Savoie', '22');
INSERT INTO departements VALUES (74, 'Savoie (Haute)', '22');
INSERT INTO departements VALUES (75, 'Paris', '10');
INSERT INTO departements VALUES (76, 'Seine Maritime', '17');
INSERT INTO departements VALUES (77, 'Seine et Marne', '10');
INSERT INTO departements VALUES (78, 'Yvelines', '10');
INSERT INTO departements VALUES (79, 'Sèvres (Deux)', '20');
INSERT INTO departements VALUES (80, 'Somme', '19');
INSERT INTO departements VALUES (81, 'Tarn', '14');
INSERT INTO departements VALUES (82, 'Tarn et Garonne', '14');
INSERT INTO departements VALUES (83, 'Var', '21');
INSERT INTO departements VALUES (84, 'Vaucluse', '21');
INSERT INTO departements VALUES (85, 'Vendée', '18');
INSERT INTO departements VALUES (86, 'Vienne', '20');
INSERT INTO departements VALUES (87, 'Vienne (Haute)', '12');
INSERT INTO departements VALUES (88, 'Vosges', '13');
INSERT INTO departements VALUES (89, 'Yonne', '3');
INSERT INTO departements VALUES (90, 'Belfort (Territoire de)', '9');
INSERT INTO departements VALUES (91, 'Essonne', '10');
INSERT INTO departements VALUES (92, 'Hauts de Seine', '10');
INSERT INTO departements VALUES (93, 'Seine Saint Denis', '10');
INSERT INTO departements VALUES (94, 'Val de Marne', '10');
INSERT INTO departements VALUES (976, 'Mayotte', '8');
INSERT INTO departements VALUES (971, 'Guadeloupe', '8');
INSERT INTO departements VALUES (973, 'Guyane', '8');
INSERT INTO departements VALUES (972, 'Martinique', '8');
INSERT INTO departements VALUES (974, 'Réunion', '8');
INSERT INTO departements VALUES (21, 'Côte d''or', '3');
INSERT INTO departements VALUES (22, 'Côtes d''armor', '4');
INSERT INTO departements VALUES (95, 'Val d''oise', '10');


--
-- Data for Name: historiques; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO historiques VALUES (58, '2013-05-31 14:38:13.772', 'Toulouse', '2013-05-31 15:55:06.141');
INSERT INTO historiques VALUES (58, '2013-05-31 15:55:06.141', 'Bordeaux', '2013-05-31 15:57:03.939');
INSERT INTO historiques VALUES (58, '2013-05-31 15:57:03.939', 'Toulouse', NULL);
INSERT INTO historiques VALUES (57, '2013-05-31 16:07:26.547', 'Bordeaux', '2013-05-31 16:08:54.817');
INSERT INTO historiques VALUES (57, '2013-05-31 16:08:54.817', 'Toulouse', '2013-05-31 16:09:10.439');
INSERT INTO historiques VALUES (57, '2013-06-03 15:22:42.348', 'Toulouse', NULL);
INSERT INTO historiques VALUES (56, '2013-06-03 22:59:07.965', 'Bordeaux', '2013-06-03 23:02:26.866');
INSERT INTO historiques VALUES (56, '2013-05-31 15:59:11.272', 'Toulouse', '2013-06-03 23:04:23.974');
INSERT INTO historiques VALUES (56, '2013-06-03 23:27:08.628', 'Toulouse', '2013-06-03 23:30:45.44');
INSERT INTO historiques VALUES (56, '2013-06-03 23:30:45.44', 'Bordeaux', '2013-06-03 23:31:01.63');
INSERT INTO historiques VALUES (56, '2013-06-03 23:31:32.096', 'Toulouse', '2013-06-03 23:31:46.711');
INSERT INTO historiques VALUES (56, '2013-06-03 23:31:46.711', 'Bordeaux', NULL);


--
-- Name: historiques_capteur_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('historiques_capteur_id_seq', 1, false);


--
-- Data for Name: lieux; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO lieux VALUES ('Toulouse', 45, 6, 8);
INSERT INTO lieux VALUES ('Bordeaux', 40, 10, 10);


--
-- Data for Name: massifs; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO massifs VALUES ('Bordeaux', 1, 9);


--
-- Data for Name: precipitations; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO precipitations VALUES (55, 'Pluie', 10, 10, 'zeffijzefefhefhzefezifhef ', 58);
INSERT INTO precipitations VALUES (56, 'Pluie', 10, 10, 'zeffijzefefhefhzefezifhef				

						 ', 58);
INSERT INTO precipitations VALUES (58, 'Pluie', 10, 10, 'zeffijzefefhefhzefezifhef				

						 ', 56);


--
-- Name: precipitations_capteur_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('precipitations_capteur_id_seq', 1, false);


--
-- Name: precipitations_precipitation_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('precipitations_precipitation_id_seq', 58, true);


--
-- Data for Name: regions; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO regions VALUES ('1', 'Alsace');
INSERT INTO regions VALUES ('10', 'Franche Comte');
INSERT INTO regions VALUES ('11', 'Haute Normandie');
INSERT INTO regions VALUES ('12', 'Ile de France');
INSERT INTO regions VALUES ('13', 'Languedoc Roussillon');
INSERT INTO regions VALUES ('14', 'Limousin');
INSERT INTO regions VALUES ('15', 'Lorraine');
INSERT INTO regions VALUES ('16', 'Midi-Pyrénées');
INSERT INTO regions VALUES ('17', 'Nord Pas de Calais');
INSERT INTO regions VALUES ('18', 'Provence Alpes Côte d''Azur');
INSERT INTO regions VALUES ('19', 'Pays de la Loire');
INSERT INTO regions VALUES ('2', 'Aquitaine');
INSERT INTO regions VALUES ('20', 'Picardie');
INSERT INTO regions VALUES ('21', 'Poitou Charente');
INSERT INTO regions VALUES ('22', 'Rhone Alpes');
INSERT INTO regions VALUES ('3', 'Auvergne');
INSERT INTO regions VALUES ('4', 'Basse Normandie');
INSERT INTO regions VALUES ('5', 'Bourgogne');
INSERT INTO regions VALUES ('6', 'Bretagne');
INSERT INTO regions VALUES ('7', 'Centre');
INSERT INTO regions VALUES ('8', 'Champagne Ardenne');
INSERT INTO regions VALUES ('9', 'Corse');


--
-- Data for Name: temperatures; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO temperatures VALUES (21, 15, 20, 25, 'frgrgeergregregregregregregrgrgerg ', 58);
INSERT INTO temperatures VALUES (22, 15, 20, 25, 'frgrgeergregregregregregregrgrgerg				 ', 58);
INSERT INTO temperatures VALUES (24, 15, 20, 25, 'frgrgeergregregregregregregrgrgerg				 ', 56);
INSERT INTO temperatures VALUES (25, 15, 20, 25, 'frgrgeergregregregregregregrgrgerg				 ', 56);
INSERT INTO temperatures VALUES (26, 15, 20, 25, 'frgrgeergregregregregregregrgrgerg				 ', 56);


--
-- Name: temperatures_capteur_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('temperatures_capteur_id_seq', 1, false);


--
-- Name: temperatures_temperature_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('temperatures_temperature_id_seq', 26, true);


--
-- Data for Name: vents; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO vents VALUES (34, 4, 'Nord', 8, 'rezdfbzefgezufgzeufgezfuigefyugezfg				 ', 56);
INSERT INTO vents VALUES (35, 4, 'Nord', 8, 'rezdfbzefgezufgzeufgezfuigefyugezfg				 ', 56);


--
-- Name: vents_capteur_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('vents_capteur_id_seq', 1, false);


--
-- Name: vents_vent_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('vents_vent_id_seq', 35, true);


--
-- Data for Name: villes; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO villes VALUES ('Toulouse', 31);


--
-- Name: bulletins_date_moment_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY bulletins
    ADD CONSTRAINT bulletins_date_moment_key UNIQUE (date, moment);


--
-- Name: bulletins_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY bulletins
    ADD CONSTRAINT bulletins_pkey PRIMARY KEY (bulletin_id, lieu_id);


--
-- Name: capteurs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY capteurs
    ADD CONSTRAINT capteurs_pkey PRIMARY KEY (capteur_id);


--
-- Name: departements_nom_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY departements
    ADD CONSTRAINT departements_nom_key UNIQUE (nom);


--
-- Name: departements_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY departements
    ADD CONSTRAINT departements_pkey PRIMARY KEY (departement_id);


--
-- Name: historiques_capteur_id_fin_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY historiques
    ADD CONSTRAINT historiques_capteur_id_fin_key UNIQUE (capteur_id, fin);


--
-- Name: historiques_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY historiques
    ADD CONSTRAINT historiques_pkey PRIMARY KEY (capteur_id, debut);


--
-- Name: lieux_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY lieux
    ADD CONSTRAINT lieux_pkey PRIMARY KEY (nom);


--
-- Name: massifs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY massifs
    ADD CONSTRAINT massifs_pkey PRIMARY KEY (nom);


--
-- Name: precipitations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY precipitations
    ADD CONSTRAINT precipitations_pkey PRIMARY KEY (precipitation_id);


--
-- Name: regions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY regions
    ADD CONSTRAINT regions_pkey PRIMARY KEY (num);


--
-- Name: temperatures_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY temperatures
    ADD CONSTRAINT temperatures_pkey PRIMARY KEY (temperature_id);


--
-- Name: vents_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY vents
    ADD CONSTRAINT vents_pkey PRIMARY KEY (vent_id);


--
-- Name: villes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY villes
    ADD CONSTRAINT villes_pkey PRIMARY KEY (nom, dpt);


--
-- Name: handle_create_capteurs; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER handle_create_capteurs AFTER INSERT ON capteurs FOR EACH ROW EXECUTE PROCEDURE process_insert_capteur();


--
-- Name: handle_update_capteurs; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER handle_update_capteurs AFTER UPDATE ON capteurs FOR EACH ROW EXECUTE PROCEDURE process_update_capteur();


--
-- Name: bulletins_lieu_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY bulletins
    ADD CONSTRAINT bulletins_lieu_id_fkey FOREIGN KEY (lieu_id) REFERENCES lieux(nom);


--
-- Name: bulletins_precipitation_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY bulletins
    ADD CONSTRAINT bulletins_precipitation_id_fkey FOREIGN KEY (precipitation_id) REFERENCES precipitations(precipitation_id);


--
-- Name: bulletins_precipitation_id_fkey1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY bulletins
    ADD CONSTRAINT bulletins_precipitation_id_fkey1 FOREIGN KEY (precipitation_id) REFERENCES precipitations(precipitation_id);


--
-- Name: bulletins_vent_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY bulletins
    ADD CONSTRAINT bulletins_vent_id_fkey FOREIGN KEY (vent_id) REFERENCES vents(vent_id);


--
-- Name: capteurs_lieu_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY capteurs
    ADD CONSTRAINT capteurs_lieu_id_fkey FOREIGN KEY (lieu_id) REFERENCES lieux(nom);


--
-- Name: departements_region_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY departements
    ADD CONSTRAINT departements_region_id_fkey FOREIGN KEY (region_id) REFERENCES regions(num);


--
-- Name: historiques_capteur_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY historiques
    ADD CONSTRAINT historiques_capteur_id_fkey FOREIGN KEY (capteur_id) REFERENCES capteurs(capteur_id);


--
-- Name: historiques_lieu_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY historiques
    ADD CONSTRAINT historiques_lieu_id_fkey FOREIGN KEY (lieu_id) REFERENCES lieux(nom);


--
-- Name: massifs_d1_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY massifs
    ADD CONSTRAINT massifs_d1_fkey FOREIGN KEY (d1) REFERENCES departements(departement_id);


--
-- Name: massifs_d2_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY massifs
    ADD CONSTRAINT massifs_d2_fkey FOREIGN KEY (d2) REFERENCES departements(departement_id);


--
-- Name: massifs_nom_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY massifs
    ADD CONSTRAINT massifs_nom_fkey FOREIGN KEY (nom) REFERENCES lieux(nom);


--
-- Name: precipitations_capteur_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY precipitations
    ADD CONSTRAINT precipitations_capteur_id_fkey FOREIGN KEY (capteur_id) REFERENCES capteurs(capteur_id);


--
-- Name: temperatures_capteur_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY temperatures
    ADD CONSTRAINT temperatures_capteur_id_fkey FOREIGN KEY (capteur_id) REFERENCES capteurs(capteur_id);


--
-- Name: vents_capteur_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY vents
    ADD CONSTRAINT vents_capteur_id_fkey FOREIGN KEY (capteur_id) REFERENCES capteurs(capteur_id);


--
-- Name: villes_dpt_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY villes
    ADD CONSTRAINT villes_dpt_fkey FOREIGN KEY (dpt) REFERENCES departements(departement_id);


--
-- Name: villes_nom_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY villes
    ADD CONSTRAINT villes_nom_fkey FOREIGN KEY (nom) REFERENCES lieux(nom);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

