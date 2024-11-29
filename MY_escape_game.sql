--
-- PostgreSQL database dump
--

-- Dumped from database version 16.4
-- Dumped by pg_dump version 16.4

-- Started on 2024-11-19 19:52:17

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 2 (class 3079 OID 16398)
-- Name: postgis; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS postgis WITH SCHEMA public;


--
-- TOC entry 5727 (class 0 OID 0)
-- Dependencies: 2
-- Name: EXTENSION postgis; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION postgis IS 'PostGIS geometry and geography spatial types and functions';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 224 (class 1259 OID 58621)
-- Name: joueurs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.joueurs (
    id integer NOT NULL,
    pseudo character varying,
    genre character varying,
    score integer
);


ALTER TABLE public.joueurs OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 58620)
-- Name: joueurs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.joueurs ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.joueurs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- TOC entry 222 (class 1259 OID 58613)
-- Name: objets; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.objets (
    id integer NOT NULL,
    nom character varying,
    objet_type character varying,
    importance character varying,
    indice character varying,
    img character varying,
    taille integer[],
    position_ancre integer[],
    objet_debloque character varying,
    objet_libere character varying,
    debut boolean,
    minzoom integer,
    geometrie public.geometry
);


ALTER TABLE public.objets OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 58612)
-- Name: objets_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.objets ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.objets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- TOC entry 5721 (class 0 OID 58621)
-- Dependencies: 224
-- Data for Name: joueurs; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.joueurs OVERRIDING SYSTEM VALUE VALUES (1, 'Kenny', 'Homme', 350);
INSERT INTO public.joueurs OVERRIDING SYSTEM VALUE VALUES (2, 'Bryan', 'Homme', 100);
INSERT INTO public.joueurs OVERRIDING SYSTEM VALUE VALUES (3, 'Félix', 'Homme', 500);
INSERT INTO public.joueurs OVERRIDING SYSTEM VALUE VALUES (4, 'Pierre', 'Homme', 450);
INSERT INTO public.joueurs OVERRIDING SYSTEM VALUE VALUES (5, 'Karine', 'Femme', 600);
INSERT INTO public.joueurs OVERRIDING SYSTEM VALUE VALUES (6, 'Julie', 'Femme', 610);
INSERT INTO public.joueurs OVERRIDING SYSTEM VALUE VALUES (7, 'Marie', 'Femme', 560);
INSERT INTO public.joueurs OVERRIDING SYSTEM VALUE VALUES (8, 'Jonathan', 'Homme', 290);
INSERT INTO public.joueurs OVERRIDING SYSTEM VALUE VALUES (9, 'Célestin', 'Homme', 100);
INSERT INTO public.joueurs OVERRIDING SYSTEM VALUE VALUES (10, 'Carole', 'Femme', 130);


--
-- TOC entry 5719 (class 0 OID 58613)
-- Dependencies: 222
-- Data for Name: objets; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.objets OVERRIDING SYSTEM VALUE VALUES (1, 'ordinateur', 'bloque_code', NULL, 'Paul ne se rappelle plus de son mot de passe. Va chez lui (A Serris) le récupérer dans son carnet de code.', 'ordinateur', '{100,100}', '{50,50}', '0011', '7', true, 18, '0101000020E6100000DDB5847CD0B30440CFF753E3A56B4840');
INSERT INTO public.objets OVERRIDING SYSTEM VALUE VALUES (2, 'carnet', 'objet_code', NULL, '0011', 'carnet', '{100,100}', '{50,50}', NULL, NULL, false, 16, '0101000020E6100000917EFB3A704E06401D386744696F4840');
INSERT INTO public.objets OVERRIDING SYSTEM VALUE VALUES (3, 'maison', 'bloque_objet', NULL, 'La maison est fermée, Paul ne t*a pas remis son badge va le récupérer dans sa voiture au garage pour révision (Au Drive Intermarché proche de la Forêt Régionale des Vaillières).', 'maison', '{160,160}', '{80,80}', '4', '2', true, 16, '0101000020E6100000917EFB3A704E06401D386744696F4840');
INSERT INTO public.objets OVERRIDING SYSTEM VALUE VALUES (4, 'badge', 'objet_recuperable', 'moyenne', NULL, 'badge', '{100,100}', '{50,50}', NULL, NULL, false, 17, '0101000020E6100000B22E6EA301BC0540A69BC420B0724840');
INSERT INTO public.objets OVERRIDING SYSTEM VALUE VALUES (5, 'voiture', 'bloque_objet', NULL, 'Le garagiste est parti en intervention à Orly-Aéroprt avec la clé. Va le retrouver.', 'voiture', '{160,160}', '{80,80}', '6', '4', true, 17, '0101000020E6100000B22E6EA301BC0540A69BC420B0724840');
INSERT INTO public.objets OVERRIDING SYSTEM VALUE VALUES (6, 'cle de voiture', 'objet_recuperable', 'faible', NULL, 'cle_de_voiture', '{100,100}', '{50,50}', NULL, NULL, true, 18, '0101000020E6100000560E2DB29DEF0240B003E78C285D4840');
INSERT INTO public.objets OVERRIDING SYSTEM VALUE VALUES (7, 'presentation', 'objet_recuperable', 'haute', NULL, 'presentation', '{160,160}', '{80,80}', NULL, NULL, false, 18, '0101000020E6100000DDB5847CD0B30440CFF753E3A56B4840');


--
-- TOC entry 5562 (class 0 OID 16716)
-- Dependencies: 217
-- Data for Name: spatial_ref_sys; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 5728 (class 0 OID 0)
-- Dependencies: 223
-- Name: joueurs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.joueurs_id_seq', 10, true);


--
-- TOC entry 5729 (class 0 OID 0)
-- Dependencies: 221
-- Name: objets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.objets_id_seq', 7, true);


--
-- TOC entry 5569 (class 2606 OID 58627)
-- Name: joueurs joueurs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.joueurs
    ADD CONSTRAINT joueurs_pkey PRIMARY KEY (id);


--
-- TOC entry 5567 (class 2606 OID 58619)
-- Name: objets objets_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.objets
    ADD CONSTRAINT objets_pkey PRIMARY KEY (id);


-- Completed on 2024-11-19 19:52:21

--
-- PostgreSQL database dump complete
--

