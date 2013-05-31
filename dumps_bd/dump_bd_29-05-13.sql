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
ALTER TABLE ONLY public.bulletins DROP CONSTRAINT bulletins_temperature_id_fkey;
ALTER TABLE ONLY public.bulletins DROP CONSTRAINT bulletins_precipitation_id_fkey;
ALTER TABLE ONLY public.bulletins DROP CONSTRAINT bulletins_lieu_id_fkey;
ALTER TABLE ONLY public.villes DROP CONSTRAINT villes_pkey;
ALTER TABLE ONLY public.vents DROP CONSTRAINT vents_pkey;
ALTER TABLE ONLY public.temperatures DROP CONSTRAINT temperatures_pkey;
ALTER TABLE ONLY public.regions DROP CONSTRAINT regions_pkey;
ALTER TABLE ONLY public.precipitations DROP CONSTRAINT precipitations_pkey;
ALTER TABLE ONLY public.massifs DROP CONSTRAINT massifs_pkey;
ALTER TABLE ONLY public.lieux DROP CONSTRAINT lieux_pkey;
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
    precipitation_id integer NOT NULL,
    temperature_id integer NOT NULL,
    vent_id integer NOT NULL,
    CONSTRAINT bulletins_moment_check CHECK (((moment)::text = ANY ((ARRAY['Matin'::character varying, 'Après-midi'::character varying, 'Soirée'::character varying, 'Nuit'::character varying])::text[]))),
    CONSTRAINT chk_pr_te_ve CHECK ((((precipitation_id IS NOT NULL) OR (temperature_id IS NOT NULL)) OR (vent_id IS NOT NULL)))
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
    debut timestamp without time zone,
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
    CONSTRAINT chk_type_value CHECK (((type)::text = ANY ((ARRAY['Pluie'::character varying, 'Neige'::character varying, 'Grêle'::character varying, 'Sable'::character varying])::text[]))),
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
    CONSTRAINT chk_dir_value CHECK (((direction)::text = ANY ((ARRAY['Nord'::character varying, 'Sud'::character varying, 'Ouest'::character varying, 'Est'::character varying])::text[])))
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

COPY bulletins (bulletin_id, lieu_id, moment, date, precipitation_id, temperature_id, vent_id) FROM stdin;
\.


--
-- Name: bulletins_bulletin_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('bulletins_bulletin_id_seq', 1, false);


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

COPY capteurs (capteur_id, lieu_id) FROM stdin;
8	Toulouse
9	Toulouse
10	Toulouse
11	Toulouse
12	Toulouse
17	Bordeaux
18	Bordeaux
19	Bordeaux
20	\N
21	\N
22	\N
23	\N
24	\N
25	\N
26	\N
27	\N
28	\N
29	\N
30	\N
31	\N
32	\N
33	\N
34	\N
35	\N
36	\N
37	\N
38	\N
39	\N
\.


--
-- Name: capteurs_capteur_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('capteurs_capteur_id_seq', 39, true);


--
-- Data for Name: departements; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY departements (departement_id, nom, region_id) FROM stdin;
1	Ain	22
2	Aisne	19
3	Allier	2
5	Alpes (Hautes)	21
6	Alpes Maritimes	21
7	Ardéche	22
8	Ardennes	6
9	 Ariége	14
10	Aube	6
11	Aude	11
12	Aveyron	14
13	Bouches du Rhône	21
15	Cantal	2
16	Charente	20
17	Charente Maritime	20
18	Cher	5
19	Corréze	12
23	Creuse 	12
24	Dordogne	1
25	Doubs	9
26	Drôme	22
27	Eure	17
28	Eure et Loir	5
29	Finistére	4
30	Gard	11
31	Garonne (Haute)	14
32	Gers	14
33	Gironde	1
34	Hérault	11
35	Ile et Vilaine	4
36	Indre	5
37	Indre et Loire	5
38	Isére	22
39	Jura	9
40	Landes	1
41	Loir et Cher	5
42	Loire	22
43	Loire (Haute)	2
44	Loire Atlantique	18
45	Loiret	5
46	Lot	14
47	Lot et Garonne	1
48	Lozére	11
49	Maine et Loire	18
51	Marne	6
52	Marne (Haute)	6
53	Mayenne	18
54	Meurthe et Moselle	13
55	Meuse	13
56	Morbihan	4
57	Moselle	13
58	Niévre	3
59	Nord	15
60	Oise	19
62	Pas de Calais	15
63	Puy de Dôme	2
64	Pyrénées Atlantiques	1
65	Pyrénées (Hautes)	14
66	Pyrénées Orientales	11
69	Rhône	22
70	Saône (Haute)	9
71	Saône et Loire	3
72	Sarthe	18
73	Savoie	22
74	Savoie (Haute)	22
75	Paris	10
76	Seine Maritime	17
77	Seine et Marne	10
78	Yvelines	10
79	Sèvres (Deux)	20
80	Somme	19
81	Tarn	14
82	Tarn et Garonne	14
83	Var	21
84	Vaucluse	21
85	Vendée	18
86	Vienne	20
87	Vienne (Haute)	12
88	Vosges	13
89	Yonne	3
90	Belfort (Territoire de)	9
91	Essonne	10
92	Hauts de Seine	10
93	Seine Saint Denis	10
94	Val de Marne	10
976	Mayotte	8
971	Guadeloupe	8
973	Guyane	8
972	Martinique	8
974	Réunion	8
21	Côte d'or	3
22	Côtes d'armor	4
95	Val d'oise	10
\.


--
-- Data for Name: historiques; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY historiques (capteur_id, debut, lieu_id, fin) FROM stdin;
\.


--
-- Name: historiques_capteur_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('historiques_capteur_id_seq', 1, false);


--
-- Data for Name: lieux; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY lieux (nom, seuiltp, seuilvt, seuilpr) FROM stdin;
Toulouse	45	6	8
Bordeaux	40	10	10
\.


--
-- Data for Name: massifs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY massifs (nom, d1, d2) FROM stdin;
Bordeaux	1	9
\.


--
-- Data for Name: precipitations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY precipitations (precipitation_id, type, seuil, force, info, capteur_id) FROM stdin;
\.


--
-- Name: precipitations_capteur_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('precipitations_capteur_id_seq', 1, false);


--
-- Name: precipitations_precipitation_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('precipitations_precipitation_id_seq', 1, false);


--
-- Data for Name: regions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY regions (num, nom) FROM stdin;
1	Alsace
10	Franche Comte
11	Haute Normandie
12	Ile de France
13	Languedoc Roussillon
14	Limousin
15	Lorraine
16	Midi-Pyrénées
17	Nord Pas de Calais
18	Provence Alpes Côte d'Azur
19	Pays de la Loire
2	Aquitaine
20	Picardie
21	Poitou Charente
22	Rhone Alpes
3	Auvergne
4	Basse Normandie
5	Bourgogne
6	Bretagne
7	Centre
8	Champagne Ardenne
9	Corse
\.


--
-- Data for Name: temperatures; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY temperatures (temperature_id, reelle, ressentie, seuil, info, capteur_id) FROM stdin;
\.


--
-- Name: temperatures_capteur_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('temperatures_capteur_id_seq', 1, false);


--
-- Name: temperatures_temperature_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('temperatures_temperature_id_seq', 1, false);


--
-- Data for Name: vents; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY vents (vent_id, force, direction, seuil, info, capteur_id) FROM stdin;
\.


--
-- Name: vents_capteur_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('vents_capteur_id_seq', 1, false);


--
-- Name: vents_vent_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('vents_vent_id_seq', 1, false);


--
-- Data for Name: villes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY villes (nom, dpt) FROM stdin;
Toulouse	31
\.


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
-- Name: bulletins_temperature_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY bulletins
    ADD CONSTRAINT bulletins_temperature_id_fkey FOREIGN KEY (temperature_id) REFERENCES temperatures(temperature_id);


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

