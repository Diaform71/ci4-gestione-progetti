-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Apr 20, 2025 alle 08:27
-- Versione del server: 10.4.24-MariaDB
-- Versione PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ci4_gestione_progetti`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `aliquote_iva`
--

CREATE TABLE `aliquote_iva` (
  `id` int(11) UNSIGNED NOT NULL,
  `codice` varchar(10) NOT NULL,
  `descrizione` varchar(255) NOT NULL,
  `percentuale` decimal(5,2) NOT NULL DEFAULT 0.00,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `anagrafiche`
--

CREATE TABLE `anagrafiche` (
  `id` int(11) UNSIGNED NOT NULL,
  `ragione_sociale` varchar(255) NOT NULL,
  `indirizzo` varchar(255) DEFAULT NULL,
  `citta` varchar(100) DEFAULT NULL,
  `nazione` varchar(100) DEFAULT NULL,
  `cap` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `fax` varchar(50) DEFAULT NULL,
  `partita_iva` varchar(20) DEFAULT NULL,
  `codice_fiscale` varchar(20) DEFAULT NULL,
  `sdi` varchar(7) DEFAULT NULL,
  `id_iva` int(11) UNSIGNED DEFAULT NULL,
  `fornitore` tinyint(1) NOT NULL DEFAULT 0,
  `cliente` tinyint(1) NOT NULL DEFAULT 0,
  `logo` varchar(255) DEFAULT NULL,
  `attivo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `anagrafiche_contatti`
--

CREATE TABLE `anagrafiche_contatti` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_anagrafica` int(11) UNSIGNED NOT NULL,
  `id_contatto` int(11) UNSIGNED NOT NULL,
  `ruolo` varchar(100) DEFAULT NULL,
  `principale` tinyint(1) NOT NULL DEFAULT 0,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `attivita`
--

CREATE TABLE `attivita` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_progetto` int(11) UNSIGNED NOT NULL,
  `id_utente_assegnato` int(11) UNSIGNED DEFAULT NULL,
  `id_utente_creatore` int(11) UNSIGNED DEFAULT NULL,
  `titolo` varchar(255) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `priorita` enum('bassa','media','alta','urgente') NOT NULL DEFAULT 'media',
  `stato` enum('da_iniziare','in_corso','in_pausa','completata','annullata') NOT NULL DEFAULT 'da_iniziare',
  `data_scadenza` date DEFAULT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` datetime DEFAULT NULL,
  `completata` tinyint(1) NOT NULL DEFAULT 0,
  `completata_il` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `contatti`
--

CREATE TABLE `contatti` (
  `id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cognome` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `interno` varchar(20) DEFAULT NULL,
  `cellulare` varchar(50) DEFAULT NULL,
  `immagine` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `attivo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `documenti`
--

CREATE TABLE `documenti` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_progetto` int(11) UNSIGNED NOT NULL,
  `id_utente` int(11) UNSIGNED NOT NULL,
  `nome_file` varchar(255) NOT NULL,
  `nome_originale` varchar(255) NOT NULL,
  `path` varchar(500) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `dimensione` int(20) UNSIGNED NOT NULL,
  `descrizione` text DEFAULT NULL,
  `attivo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `email_logs`
--

CREATE TABLE `email_logs` (
  `id` int(11) UNSIGNED NOT NULL,
  `destinatario` varchar(255) NOT NULL,
  `cc` text DEFAULT NULL,
  `ccn` text DEFAULT NULL,
  `oggetto` varchar(255) NOT NULL,
  `corpo` text NOT NULL,
  `id_riferimento` int(11) UNSIGNED DEFAULT NULL,
  `tipo_riferimento` varchar(50) NOT NULL DEFAULT 'RDO' COMMENT 'RDO, ORDINE, ecc.',
  `data_invio` datetime NOT NULL,
  `stato` varchar(20) NOT NULL DEFAULT 'inviato' COMMENT 'inviato, errore',
  `error_message` text DEFAULT NULL,
  `allegati` text DEFAULT NULL COMMENT 'JSON array con informazioni sugli allegati',
  `id_utente` int(11) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `email_templates`
--

CREATE TABLE `email_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `oggetto` varchar(255) NOT NULL,
  `corpo` text NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `impostazioni`
--

CREATE TABLE `impostazioni` (
  `id` int(11) UNSIGNED NOT NULL,
  `chiave` varchar(50) NOT NULL,
  `valore` text DEFAULT NULL,
  `id_utente` int(11) UNSIGNED DEFAULT NULL,
  `tipo` varchar(20) NOT NULL DEFAULT 'stringa',
  `descrizione` varchar(255) DEFAULT NULL,
  `gruppo` varchar(50) NOT NULL DEFAULT 'sistema',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `materiali`
--

CREATE TABLE `materiali` (
  `id` int(11) UNSIGNED NOT NULL,
  `codice` varchar(50) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `materiale` varchar(255) DEFAULT NULL,
  `produttore` varchar(255) DEFAULT NULL,
  `immagine` varchar(255) DEFAULT NULL,
  `commerciale` tinyint(1) NOT NULL DEFAULT 0,
  `meccanica` tinyint(1) NOT NULL DEFAULT 0,
  `elettrica` tinyint(1) NOT NULL DEFAULT 0,
  `pneumatica` tinyint(1) NOT NULL DEFAULT 0,
  `in_produzione` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `offerte_fornitore`
--

CREATE TABLE `offerte_fornitore` (
  `id` int(11) UNSIGNED NOT NULL,
  `numero` varchar(50) NOT NULL,
  `data` date NOT NULL,
  `oggetto` varchar(255) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `id_anagrafica` int(11) UNSIGNED NOT NULL,
  `id_referente` int(11) UNSIGNED DEFAULT NULL,
  `id_richiesta_offerta` int(11) UNSIGNED DEFAULT NULL,
  `id_progetto` int(11) UNSIGNED DEFAULT NULL,
  `stato` varchar(20) NOT NULL DEFAULT 'ricevuta',
  `id_utente_creatore` int(11) UNSIGNED NOT NULL,
  `data_ricezione` datetime DEFAULT NULL,
  `data_approvazione` datetime DEFAULT NULL,
  `importo_totale` decimal(10,2) DEFAULT NULL,
  `valuta` varchar(10) DEFAULT 'EUR',
  `note` text DEFAULT NULL,
  `sconto_totale` decimal(10,2) DEFAULT NULL,
  `sconto_fisso` decimal(10,2) DEFAULT NULL,
  `costo_trasporto` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `offerte_fornitore_allegati`
--

CREATE TABLE `offerte_fornitore_allegati` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_offerta_fornitore` int(11) UNSIGNED NOT NULL,
  `nome_file` varchar(255) NOT NULL,
  `file_originale` varchar(255) NOT NULL,
  `dimensione` int(11) UNSIGNED DEFAULT NULL,
  `tipo_mime` varchar(100) DEFAULT NULL,
  `descrizione` text DEFAULT NULL,
  `data_caricamento` datetime NOT NULL DEFAULT '2025-04-09 04:03:16',
  `id_utente` int(11) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `offerte_fornitore_voci`
--

CREATE TABLE `offerte_fornitore_voci` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_offerta_fornitore` int(11) UNSIGNED NOT NULL,
  `id_materiale` int(11) UNSIGNED DEFAULT NULL,
  `codice` varchar(50) DEFAULT NULL,
  `descrizione` text NOT NULL,
  `quantita` decimal(10,2) NOT NULL DEFAULT 1.00,
  `prezzo_unitario` decimal(10,2) DEFAULT NULL,
  `importo` decimal(10,2) DEFAULT NULL,
  `unita_misura` varchar(20) DEFAULT 'pz',
  `sconto` decimal(5,2) DEFAULT 0.00,
  `note` text DEFAULT NULL,
  `id_progetto` int(11) UNSIGNED DEFAULT NULL,
  `id_richiesta_materiale` int(11) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `ordini_materiale`
--

CREATE TABLE `ordini_materiale` (
  `id` int(11) UNSIGNED NOT NULL,
  `numero` varchar(50) NOT NULL,
  `data` date NOT NULL,
  `oggetto` varchar(255) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `id_anagrafica` int(11) UNSIGNED NOT NULL,
  `id_referente` int(11) UNSIGNED DEFAULT NULL,
  `id_progetto` int(11) UNSIGNED DEFAULT NULL,
  `stato` enum('bozza','inviato','confermato','in_consegna','consegnato','annullato','in_attesa','completato') NOT NULL DEFAULT 'bozza',
  `id_utente_creatore` int(11) UNSIGNED NOT NULL,
  `data_invio` datetime DEFAULT NULL,
  `data_accettazione` datetime DEFAULT NULL,
  `data_consegna_prevista` date DEFAULT NULL,
  `data_consegna_effettiva` date DEFAULT NULL,
  `data_completamento` datetime DEFAULT NULL,
  `data_annullamento` datetime DEFAULT NULL,
  `note` text DEFAULT NULL,
  `importo_totale` decimal(10,2) DEFAULT NULL,
  `sconto_totale` decimal(10,2) DEFAULT NULL,
  `sconto_fisso` decimal(10,2) DEFAULT NULL,
  `costo_trasporto` decimal(10,2) DEFAULT NULL,
  `condizioni_pagamento` varchar(255) DEFAULT NULL,
  `condizioni_consegna` varchar(255) DEFAULT NULL,
  `id_offerta_fornitore` int(11) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `ordini_materiale_allegati`
--

CREATE TABLE `ordini_materiale_allegati` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_ordine_materiale` int(11) UNSIGNED NOT NULL,
  `nome_file` varchar(255) NOT NULL,
  `file_originale` varchar(255) NOT NULL,
  `dimensione` int(11) UNSIGNED DEFAULT NULL,
  `tipo_mime` varchar(100) DEFAULT NULL,
  `descrizione` text DEFAULT NULL,
  `data_caricamento` datetime DEFAULT NULL,
  `id_utente` int(11) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `ordini_materiale_voci`
--

CREATE TABLE `ordini_materiale_voci` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_ordine` int(11) UNSIGNED NOT NULL,
  `id_materiale` int(11) UNSIGNED DEFAULT NULL,
  `codice` varchar(50) DEFAULT NULL,
  `descrizione` text NOT NULL,
  `quantita` decimal(10,2) NOT NULL DEFAULT 1.00,
  `prezzo_unitario` decimal(10,2) DEFAULT 0.00,
  `importo` decimal(10,2) DEFAULT 0.00,
  `unita_misura` varchar(20) DEFAULT 'pz',
  `sconto` decimal(5,2) DEFAULT 0.00,
  `id_progetto` int(11) UNSIGNED DEFAULT NULL,
  `id_offerta_voce` int(11) UNSIGNED DEFAULT NULL,
  `note` text DEFAULT NULL,
  `data_consegna_prevista` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `progetti`
--

CREATE TABLE `progetti` (
  `id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `fase_kanban` varchar(100) NOT NULL DEFAULT 'backlog',
  `id_anagrafica` int(11) UNSIGNED DEFAULT NULL,
  `data_inizio` datetime DEFAULT NULL,
  `data_scadenza` datetime DEFAULT NULL,
  `data_fine` datetime DEFAULT NULL,
  `id_creato_da` int(11) UNSIGNED NOT NULL,
  `id_responsabile` int(11) UNSIGNED DEFAULT NULL,
  `priorita` enum('bassa','media','alta','critica') NOT NULL DEFAULT 'media',
  `stato` enum('in_corso','completato','sospeso','annullato') NOT NULL DEFAULT 'in_corso',
  `budget` decimal(10,2) DEFAULT NULL,
  `attivo` tinyint(1) NOT NULL DEFAULT 1,
  `id_progetto_padre` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `richieste_materiali`
--

CREATE TABLE `richieste_materiali` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_richiesta` int(11) UNSIGNED NOT NULL,
  `id_materiale` int(11) UNSIGNED NOT NULL,
  `quantita` decimal(10,2) NOT NULL DEFAULT 1.00,
  `id_progetto` int(11) UNSIGNED DEFAULT NULL,
  `unita_misura` varchar(20) DEFAULT 'pz',
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `richieste_offerta`
--

CREATE TABLE `richieste_offerta` (
  `id` int(11) UNSIGNED NOT NULL,
  `numero` varchar(50) NOT NULL,
  `data` date NOT NULL,
  `oggetto` varchar(255) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `id_anagrafica` int(11) UNSIGNED NOT NULL,
  `id_referente` int(11) UNSIGNED DEFAULT NULL,
  `id_progetto` int(11) UNSIGNED DEFAULT NULL,
  `stato` enum('bozza','inviata','accettata','rifiutata','annullata') NOT NULL DEFAULT 'bozza',
  `id_utente_creatore` int(11) UNSIGNED NOT NULL,
  `data_invio` datetime DEFAULT NULL,
  `data_accettazione` datetime DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `scadenze`
--

CREATE TABLE `scadenze` (
  `id` int(11) UNSIGNED NOT NULL,
  `titolo` varchar(255) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `data_scadenza` datetime NOT NULL,
  `data_promemoria` datetime DEFAULT NULL,
  `promemoria_inviato_il` datetime DEFAULT NULL,
  `id_progetto` int(11) UNSIGNED DEFAULT NULL,
  `id_attivita` int(11) UNSIGNED DEFAULT NULL,
  `id_utente_assegnato` int(11) UNSIGNED NOT NULL,
  `id_utente_creatore` int(11) UNSIGNED NOT NULL,
  `completata` tinyint(1) NOT NULL DEFAULT 0,
  `completata_il` datetime DEFAULT NULL,
  `priorita` enum('bassa','media','alta','urgente') NOT NULL DEFAULT 'media',
  `stato` enum('da_iniziare','in_corso','completata','annullata') NOT NULL DEFAULT 'da_iniziare',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `sotto_attivita`
--

CREATE TABLE `sotto_attivita` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_attivita` int(11) UNSIGNED NOT NULL,
  `id_utente_assegnato` int(11) UNSIGNED DEFAULT NULL,
  `titolo` varchar(255) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `priorita` enum('bassa','media','alta','urgente') NOT NULL DEFAULT 'media',
  `stato` enum('da_iniziare','in_corso','in_pausa','completata','annullata') NOT NULL DEFAULT 'da_iniziare',
  `data_scadenza` date DEFAULT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` datetime DEFAULT NULL,
  `completata` tinyint(1) NOT NULL DEFAULT 0,
  `completata_il` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `cognome` varchar(100) DEFAULT NULL,
  `ultimo_accesso` datetime DEFAULT NULL,
  `attivo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `ruolo` enum('admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `aliquote_iva`
--
ALTER TABLE `aliquote_iva`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codice` (`codice`);

--
-- Indici per le tabelle `anagrafiche`
--
ALTER TABLE `anagrafiche`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `anagrafiche_contatti`
--
ALTER TABLE `anagrafiche_contatti`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_anagrafica_id_contatto` (`id_anagrafica`,`id_contatto`),
  ADD KEY `anagrafiche_contatti_id_contatto_foreign` (`id_contatto`);

--
-- Indici per le tabelle `attivita`
--
ALTER TABLE `attivita`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attivita_id_progetto_foreign` (`id_progetto`),
  ADD KEY `attivita_id_utente_assegnato_foreign` (`id_utente_assegnato`),
  ADD KEY `attivita_id_utente_creatore_foreign` (`id_utente_creatore`);

--
-- Indici per le tabelle `contatti`
--
ALTER TABLE `contatti`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `documenti`
--
ALTER TABLE `documenti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `documenti_id_progetto_foreign` (`id_progetto`);

--
-- Indici per le tabelle `email_logs`
--
ALTER TABLE `email_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_riferimento_tipo_riferimento` (`id_riferimento`,`tipo_riferimento`),
  ADD KEY `id_utente` (`id_utente`),
  ADD KEY `data_invio` (`data_invio`),
  ADD KEY `stato` (`stato`);

--
-- Indici per le tabelle `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `impostazioni`
--
ALTER TABLE `impostazioni`
  ADD PRIMARY KEY (`id`),
  ADD KEY `impostazioni_id_utente_foreign` (`id_utente`),
  ADD KEY `chiave_id_utente` (`chiave`,`id_utente`);

--
-- Indici per le tabelle `materiali`
--
ALTER TABLE `materiali`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codice` (`codice`);

--
-- Indici per le tabelle `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `offerte_fornitore`
--
ALTER TABLE `offerte_fornitore`
  ADD PRIMARY KEY (`id`),
  ADD KEY `offerte_fornitore_id_anagrafica_foreign` (`id_anagrafica`),
  ADD KEY `offerte_fornitore_id_referente_foreign` (`id_referente`),
  ADD KEY `offerte_fornitore_id_richiesta_offerta_foreign` (`id_richiesta_offerta`),
  ADD KEY `offerte_fornitore_id_progetto_foreign` (`id_progetto`),
  ADD KEY `offerte_fornitore_id_utente_creatore_foreign` (`id_utente_creatore`);

--
-- Indici per le tabelle `offerte_fornitore_allegati`
--
ALTER TABLE `offerte_fornitore_allegati`
  ADD PRIMARY KEY (`id`),
  ADD KEY `offerte_fornitore_allegati_id_offerta_fornitore_foreign` (`id_offerta_fornitore`),
  ADD KEY `offerte_fornitore_allegati_id_utente_foreign` (`id_utente`);

--
-- Indici per le tabelle `offerte_fornitore_voci`
--
ALTER TABLE `offerte_fornitore_voci`
  ADD PRIMARY KEY (`id`),
  ADD KEY `offerte_fornitore_voci_id_offerta_fornitore_foreign` (`id_offerta_fornitore`),
  ADD KEY `offerte_fornitore_voci_id_materiale_foreign` (`id_materiale`),
  ADD KEY `offerte_fornitore_voci_id_progetto_foreign` (`id_progetto`),
  ADD KEY `offerte_fornitore_voci_id_richiesta_materiale_foreign` (`id_richiesta_materiale`);

--
-- Indici per le tabelle `ordini_materiale`
--
ALTER TABLE `ordini_materiale`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_anagrafica_id_progetto_stato` (`id_anagrafica`,`id_progetto`,`stato`),
  ADD KEY `ordini_materiale_id_progetto_fk` (`id_progetto`),
  ADD KEY `ordini_materiale_id_referente_fk` (`id_referente`),
  ADD KEY `ordini_materiale_id_utente_creatore_fk` (`id_utente_creatore`),
  ADD KEY `ordini_materiale_id_offerta_fornitore_fk` (`id_offerta_fornitore`);

--
-- Indici per le tabelle `ordini_materiale_allegati`
--
ALTER TABLE `ordini_materiale_allegati`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ordini_materiale_allegati_id_ordine_materiale_foreign` (`id_ordine_materiale`),
  ADD KEY `ordini_materiale_allegati_id_utente_foreign` (`id_utente`);

--
-- Indici per le tabelle `ordini_materiale_voci`
--
ALTER TABLE `ordini_materiale_voci`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ordine_id_materiale` (`id_ordine`,`id_materiale`),
  ADD KEY `ordini_materiale_voci_id_materiale_fk` (`id_materiale`),
  ADD KEY `ordini_materiale_voci_id_progetto_fk` (`id_progetto`),
  ADD KEY `ordini_materiale_voci_id_offerta_voce_fk` (`id_offerta_voce`);

--
-- Indici per le tabelle `progetti`
--
ALTER TABLE `progetti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `progetti_id_anagrafica_foreign` (`id_anagrafica`),
  ADD KEY `progetti_id_creato_da_foreign` (`id_creato_da`),
  ADD KEY `progetti_id_responsabile_foreign` (`id_responsabile`);

--
-- Indici per le tabelle `richieste_materiali`
--
ALTER TABLE `richieste_materiali`
  ADD PRIMARY KEY (`id`),
  ADD KEY `richieste_materiali_id_richiesta_foreign` (`id_richiesta`),
  ADD KEY `richieste_materiali_id_materiale_foreign` (`id_materiale`),
  ADD KEY `richieste_materiali_id_progetto_foreign` (`id_progetto`);

--
-- Indici per le tabelle `richieste_offerta`
--
ALTER TABLE `richieste_offerta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `richieste_offerta_id_anagrafica_foreign` (`id_anagrafica`),
  ADD KEY `richieste_offerta_id_progetto_foreign` (`id_progetto`),
  ADD KEY `richieste_offerta_id_utente_creatore_foreign` (`id_utente_creatore`);

--
-- Indici per le tabelle `scadenze`
--
ALTER TABLE `scadenze`
  ADD PRIMARY KEY (`id`),
  ADD KEY `scadenze_id_progetto_foreign` (`id_progetto`),
  ADD KEY `scadenze_id_attivita_foreign` (`id_attivita`),
  ADD KEY `scadenze_id_utente_assegnato_foreign` (`id_utente_assegnato`),
  ADD KEY `scadenze_id_utente_creatore_foreign` (`id_utente_creatore`);

--
-- Indici per le tabelle `sotto_attivita`
--
ALTER TABLE `sotto_attivita`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sotto_attivita_id_attivita_foreign` (`id_attivita`),
  ADD KEY `sotto_attivita_id_utente_assegnato_foreign` (`id_utente_assegnato`);

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `aliquote_iva`
--
ALTER TABLE `aliquote_iva`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `anagrafiche`
--
ALTER TABLE `anagrafiche`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `anagrafiche_contatti`
--
ALTER TABLE `anagrafiche_contatti`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `attivita`
--
ALTER TABLE `attivita`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `contatti`
--
ALTER TABLE `contatti`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `documenti`
--
ALTER TABLE `documenti`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `email_logs`
--
ALTER TABLE `email_logs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `impostazioni`
--
ALTER TABLE `impostazioni`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `materiali`
--
ALTER TABLE `materiali`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `offerte_fornitore`
--
ALTER TABLE `offerte_fornitore`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `offerte_fornitore_allegati`
--
ALTER TABLE `offerte_fornitore_allegati`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `offerte_fornitore_voci`
--
ALTER TABLE `offerte_fornitore_voci`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `ordini_materiale`
--
ALTER TABLE `ordini_materiale`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `ordini_materiale_allegati`
--
ALTER TABLE `ordini_materiale_allegati`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `ordini_materiale_voci`
--
ALTER TABLE `ordini_materiale_voci`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `progetti`
--
ALTER TABLE `progetti`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `richieste_materiali`
--
ALTER TABLE `richieste_materiali`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `richieste_offerta`
--
ALTER TABLE `richieste_offerta`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `scadenze`
--
ALTER TABLE `scadenze`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `sotto_attivita`
--
ALTER TABLE `sotto_attivita`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `utenti`
--
ALTER TABLE `utenti`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `anagrafiche_contatti`
--
ALTER TABLE `anagrafiche_contatti`
  ADD CONSTRAINT `anagrafiche_contatti_id_anagrafica_foreign` FOREIGN KEY (`id_anagrafica`) REFERENCES `anagrafiche` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `anagrafiche_contatti_id_contatto_foreign` FOREIGN KEY (`id_contatto`) REFERENCES `contatti` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `attivita`
--
ALTER TABLE `attivita`
  ADD CONSTRAINT `attivita_id_progetto_foreign` FOREIGN KEY (`id_progetto`) REFERENCES `progetti` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `attivita_id_utente_assegnato_foreign` FOREIGN KEY (`id_utente_assegnato`) REFERENCES `utenti` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `attivita_id_utente_creatore_foreign` FOREIGN KEY (`id_utente_creatore`) REFERENCES `utenti` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limiti per la tabella `documenti`
--
ALTER TABLE `documenti`
  ADD CONSTRAINT `documenti_id_progetto_foreign` FOREIGN KEY (`id_progetto`) REFERENCES `progetti` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `impostazioni`
--
ALTER TABLE `impostazioni`
  ADD CONSTRAINT `impostazioni_id_utente_foreign` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limiti per la tabella `offerte_fornitore`
--
ALTER TABLE `offerte_fornitore`
  ADD CONSTRAINT `offerte_fornitore_id_anagrafica_foreign` FOREIGN KEY (`id_anagrafica`) REFERENCES `anagrafiche` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `offerte_fornitore_id_progetto_foreign` FOREIGN KEY (`id_progetto`) REFERENCES `progetti` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `offerte_fornitore_id_referente_foreign` FOREIGN KEY (`id_referente`) REFERENCES `contatti` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `offerte_fornitore_id_richiesta_offerta_foreign` FOREIGN KEY (`id_richiesta_offerta`) REFERENCES `richieste_offerta` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `offerte_fornitore_id_utente_creatore_foreign` FOREIGN KEY (`id_utente_creatore`) REFERENCES `utenti` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `offerte_fornitore_allegati`
--
ALTER TABLE `offerte_fornitore_allegati`
  ADD CONSTRAINT `offerte_fornitore_allegati_id_offerta_fornitore_foreign` FOREIGN KEY (`id_offerta_fornitore`) REFERENCES `offerte_fornitore` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `offerte_fornitore_allegati_id_utente_foreign` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Limiti per la tabella `offerte_fornitore_voci`
--
ALTER TABLE `offerte_fornitore_voci`
  ADD CONSTRAINT `offerte_fornitore_voci_id_materiale_foreign` FOREIGN KEY (`id_materiale`) REFERENCES `materiali` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `offerte_fornitore_voci_id_offerta_fornitore_foreign` FOREIGN KEY (`id_offerta_fornitore`) REFERENCES `offerte_fornitore` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `offerte_fornitore_voci_id_progetto_foreign` FOREIGN KEY (`id_progetto`) REFERENCES `progetti` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `offerte_fornitore_voci_id_richiesta_materiale_foreign` FOREIGN KEY (`id_richiesta_materiale`) REFERENCES `richieste_materiali` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Limiti per la tabella `ordini_materiale`
--
ALTER TABLE `ordini_materiale`
  ADD CONSTRAINT `ordini_materiale_id_anagrafica_fk` FOREIGN KEY (`id_anagrafica`) REFERENCES `anagrafiche` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ordini_materiale_id_offerta_fornitore_fk` FOREIGN KEY (`id_offerta_fornitore`) REFERENCES `offerte_fornitore` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ordini_materiale_id_progetto_fk` FOREIGN KEY (`id_progetto`) REFERENCES `progetti` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ordini_materiale_id_referente_fk` FOREIGN KEY (`id_referente`) REFERENCES `contatti` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ordini_materiale_id_utente_creatore_fk` FOREIGN KEY (`id_utente_creatore`) REFERENCES `utenti` (`id`) ON UPDATE CASCADE;

--
-- Limiti per la tabella `ordini_materiale_allegati`
--
ALTER TABLE `ordini_materiale_allegati`
  ADD CONSTRAINT `ordini_materiale_allegati_id_ordine_materiale_foreign` FOREIGN KEY (`id_ordine_materiale`) REFERENCES `ordini_materiale` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ordini_materiale_allegati_id_utente_foreign` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Limiti per la tabella `ordini_materiale_voci`
--
ALTER TABLE `ordini_materiale_voci`
  ADD CONSTRAINT `ordini_materiale_voci_id_materiale_fk` FOREIGN KEY (`id_materiale`) REFERENCES `materiali` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ordini_materiale_voci_id_offerta_voce_fk` FOREIGN KEY (`id_offerta_voce`) REFERENCES `offerte_fornitore_voci` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `ordini_materiale_voci_id_ordine_fk` FOREIGN KEY (`id_ordine`) REFERENCES `ordini_materiale` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ordini_materiale_voci_id_progetto_fk` FOREIGN KEY (`id_progetto`) REFERENCES `progetti` (`id`) ON UPDATE CASCADE;

--
-- Limiti per la tabella `progetti`
--
ALTER TABLE `progetti`
  ADD CONSTRAINT `progetti_id_anagrafica_foreign` FOREIGN KEY (`id_anagrafica`) REFERENCES `anagrafiche` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `progetti_id_creato_da_foreign` FOREIGN KEY (`id_creato_da`) REFERENCES `utenti` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `progetti_id_responsabile_foreign` FOREIGN KEY (`id_responsabile`) REFERENCES `utenti` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limiti per la tabella `richieste_materiali`
--
ALTER TABLE `richieste_materiali`
  ADD CONSTRAINT `richieste_materiali_id_materiale_foreign` FOREIGN KEY (`id_materiale`) REFERENCES `materiali` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `richieste_materiali_id_progetto_foreign` FOREIGN KEY (`id_progetto`) REFERENCES `progetti` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `richieste_materiali_id_richiesta_foreign` FOREIGN KEY (`id_richiesta`) REFERENCES `richieste_offerta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `richieste_offerta`
--
ALTER TABLE `richieste_offerta`
  ADD CONSTRAINT `richieste_offerta_id_anagrafica_foreign` FOREIGN KEY (`id_anagrafica`) REFERENCES `anagrafiche` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `richieste_offerta_id_progetto_foreign` FOREIGN KEY (`id_progetto`) REFERENCES `progetti` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `richieste_offerta_id_utente_creatore_foreign` FOREIGN KEY (`id_utente_creatore`) REFERENCES `utenti` (`id`) ON UPDATE CASCADE;

--
-- Limiti per la tabella `scadenze`
--
ALTER TABLE `scadenze`
  ADD CONSTRAINT `scadenze_id_attivita_foreign` FOREIGN KEY (`id_attivita`) REFERENCES `attivita` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `scadenze_id_progetto_foreign` FOREIGN KEY (`id_progetto`) REFERENCES `progetti` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `scadenze_id_utente_assegnato_foreign` FOREIGN KEY (`id_utente_assegnato`) REFERENCES `utenti` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `scadenze_id_utente_creatore_foreign` FOREIGN KEY (`id_utente_creatore`) REFERENCES `utenti` (`id`) ON UPDATE CASCADE;

--
-- Limiti per la tabella `sotto_attivita`
--
ALTER TABLE `sotto_attivita`
  ADD CONSTRAINT `sotto_attivita_id_attivita_foreign` FOREIGN KEY (`id_attivita`) REFERENCES `attivita` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sotto_attivita_id_utente_assegnato_foreign` FOREIGN KEY (`id_utente_assegnato`) REFERENCES `utenti` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
