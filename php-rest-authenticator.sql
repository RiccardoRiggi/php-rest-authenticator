-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Feb 17, 2023 alle 20:57
-- Versione del server: 10.4.20-MariaDB
-- Versione PHP: 8.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `php-rest-authenticator`
--
CREATE DATABASE IF NOT EXISTS `php-rest-authenticator` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `php-rest-authenticator`;

-- --------------------------------------------------------

--
-- Struttura della tabella `au_codici_backup`
--

CREATE TABLE `au_codici_backup` (
  `idUtente` int(10) NOT NULL,
  `codice` varchar(255) NOT NULL,
  `dataGenerazione` datetime NOT NULL DEFAULT current_timestamp(),
  `dataUtilizzo` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `au_dispositivi_fisici`
--

CREATE TABLE `au_dispositivi_fisici` (
  `idDispositivoFisico` varchar(512) NOT NULL,
  `idUtente` int(10) NOT NULL,
  `nomeDispositivo` varchar(512) NOT NULL,
  `dataAbilitazione` datetime NOT NULL DEFAULT current_timestamp(),
  `dataDisabilitazione` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `au_indirizzi_ip`
--

CREATE TABLE `au_indirizzi_ip` (
  `indirizzoIp` varchar(512) NOT NULL,
  `tentativiRimanenti` int(10) NOT NULL,
  `dataBlocco` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `au_login`
--

CREATE TABLE `au_login` (
  `idLogin` varchar(512) NOT NULL,
  `idUtente` int(10) NOT NULL,
  `idTipoLogin` varchar(32) NOT NULL,
  `idSessione` varchar(512) DEFAULT NULL,
  `dataCreazione` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `au_log_accessi`
--

CREATE TABLE `au_log_accessi` (
  `idLogAccesso` int(10) NOT NULL,
  `indirizzoIp` varchar(255) NOT NULL,
  `dataEvento` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `au_log_chiamate`
--

CREATE TABLE `au_log_chiamate` (
  `idLogChiamata` int(10) NOT NULL,
  `idSessione` varchar(255) NOT NULL,
  `indirizzoIp` varchar(255) NOT NULL,
  `dataEvento` datetime NOT NULL DEFAULT current_timestamp(),
  `pathChiamato` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `au_log_operazioni`
--

CREATE TABLE `au_log_operazioni` (
  `idLogOperazione` int(10) NOT NULL,
  `idSessione` varchar(255) NOT NULL,
  `indirizzoIp` varchar(255) NOT NULL,
  `operazione` varchar(255) NOT NULL,
  `dataEvento` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `au_metodi_login`
--

CREATE TABLE `au_metodi_login` (
  `idTipoMetodoLogin` varchar(32) NOT NULL,
  `idUtente` int(10) NOT NULL,
  `dataInizioValidita` datetime NOT NULL DEFAULT current_timestamp(),
  `dataFineValidita` datetime DEFAULT NULL,
  `isPredefinito` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `au_metodi_rec_psw`
--

CREATE TABLE `au_metodi_rec_psw` (
  `idTipoMetodoLogin` varchar(32) NOT NULL,
  `idUtente` int(10) NOT NULL,
  `dataInizioValidita` datetime NOT NULL DEFAULT current_timestamp(),
  `dataFineValidita` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `au_qr_code`
--

CREATE TABLE `au_qr_code` (
  `idQrCode` varchar(512) NOT NULL,
  `dataInizioValidita` datetime NOT NULL DEFAULT current_timestamp(),
  `dataFineValidita` datetime DEFAULT NULL,
  `idUtente` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `au_rec_psw`
--

CREATE TABLE `au_rec_psw` (
  `idRecPsw` varchar(512) NOT NULL,
  `idTipoRecPsw` varchar(512) NOT NULL,
  `idUtente` int(10) NOT NULL,
  `dataCreazione` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `au_risorse`
--

CREATE TABLE `au_risorse` (
  `idRisorsa` int(10) NOT NULL,
  `nomeMetodo` varchar(255) NOT NULL,
  `descrizione` varchar(1024) NOT NULL,
  `dataCreazione` datetime NOT NULL DEFAULT current_timestamp(),
  `dataEliminazione` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `au_ruoli`
--

CREATE TABLE `au_ruoli` (
  `idTipoRuolo` varchar(32) NOT NULL,
  `idUtente` int(10) NOT NULL,
  `dataCreazione` datetime NOT NULL DEFAULT current_timestamp(),
  `dataEliminazione` datetime NOT NULL,
  `idRuolo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `au_ruoli_risorse`
--

CREATE TABLE `au_ruoli_risorse` (
  `idRuolo` int(10) NOT NULL,
  `idRisorsa` int(10) NOT NULL,
  `dataInizioValidita` datetime NOT NULL DEFAULT current_timestamp(),
  `dataFineValidita` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `au_sessioni`
--

CREATE TABLE `au_sessioni` (
  `idSessione` varchar(512) NOT NULL,
  `idLogin` varchar(512) NOT NULL,
  `idUtente` int(10) NOT NULL,
  `dataGenerazione` datetime NOT NULL DEFAULT current_timestamp(),
  `dataInizioValidita` datetime DEFAULT NULL,
  `dataFineValidita` datetime DEFAULT NULL,
  `indirizzoIp` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `au_sessioni_impronte`
--

CREATE TABLE `au_sessioni_impronte` (
  `idSessione` varchar(256) NOT NULL,
  `idImpronta` varchar(256) NOT NULL,
  `dataGenerazione` datetime NOT NULL DEFAULT current_timestamp(),
  `dataUtilizzo` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `au_two_fact`
--

CREATE TABLE `au_two_fact` (
  `idTwoFact` varchar(512) NOT NULL,
  `idLogin` varchar(512) NOT NULL,
  `idRecPsw` varchar(512) NOT NULL,
  `codice` varchar(32) NOT NULL,
  `dataCreazione` datetime NOT NULL DEFAULT current_timestamp(),
  `dataUtilizzo` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `au_t_metodi_login`
--

CREATE TABLE `au_t_metodi_login` (
  `idTipoMetodoLogin` varchar(32) NOT NULL,
  `descrizione` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `au_t_metodi_rec_psw`
--

CREATE TABLE `au_t_metodi_rec_psw` (
  `idTipoMetodoRecPsw` varchar(32) NOT NULL,
  `descrizione` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `au_t_ruoli`
--

CREATE TABLE `au_t_ruoli` (
  `idTipoRuolo` varchar(32) NOT NULL,
  `descrizione` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `au_utenti`
--

CREATE TABLE `au_utenti` (
  `idUtente` int(10) NOT NULL,
  `nome` varchar(1024) NOT NULL,
  `cognome` varchar(1024) NOT NULL,
  `email` varchar(1024) NOT NULL,
  `password` varchar(1024) NOT NULL,
  `dataCreazione` datetime NOT NULL DEFAULT current_timestamp(),
  `dataUltimaModifica` datetime DEFAULT NULL,
  `dataEliminazione` datetime DEFAULT NULL,
  `dataBlocco` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `au_codici_backup`
--
ALTER TABLE `au_codici_backup`
  ADD PRIMARY KEY (`idUtente`,`codice`);

--
-- Indici per le tabelle `au_dispositivi_fisici`
--
ALTER TABLE `au_dispositivi_fisici`
  ADD PRIMARY KEY (`idDispositivoFisico`);

--
-- Indici per le tabelle `au_indirizzi_ip`
--
ALTER TABLE `au_indirizzi_ip`
  ADD PRIMARY KEY (`indirizzoIp`);

--
-- Indici per le tabelle `au_login`
--
ALTER TABLE `au_login`
  ADD PRIMARY KEY (`idLogin`);

--
-- Indici per le tabelle `au_log_accessi`
--
ALTER TABLE `au_log_accessi`
  ADD PRIMARY KEY (`idLogAccesso`);

--
-- Indici per le tabelle `au_log_chiamate`
--
ALTER TABLE `au_log_chiamate`
  ADD PRIMARY KEY (`idLogChiamata`);

--
-- Indici per le tabelle `au_log_operazioni`
--
ALTER TABLE `au_log_operazioni`
  ADD PRIMARY KEY (`idLogOperazione`);

--
-- Indici per le tabelle `au_metodi_login`
--
ALTER TABLE `au_metodi_login`
  ADD PRIMARY KEY (`idTipoMetodoLogin`,`idUtente`,`dataInizioValidita`);

--
-- Indici per le tabelle `au_metodi_rec_psw`
--
ALTER TABLE `au_metodi_rec_psw`
  ADD PRIMARY KEY (`idTipoMetodoLogin`,`idUtente`,`dataInizioValidita`);

--
-- Indici per le tabelle `au_qr_code`
--
ALTER TABLE `au_qr_code`
  ADD PRIMARY KEY (`idQrCode`);

--
-- Indici per le tabelle `au_rec_psw`
--
ALTER TABLE `au_rec_psw`
  ADD PRIMARY KEY (`idRecPsw`);

--
-- Indici per le tabelle `au_risorse`
--
ALTER TABLE `au_risorse`
  ADD PRIMARY KEY (`idRisorsa`);

--
-- Indici per le tabelle `au_ruoli`
--
ALTER TABLE `au_ruoli`
  ADD PRIMARY KEY (`idRuolo`);

--
-- Indici per le tabelle `au_ruoli_risorse`
--
ALTER TABLE `au_ruoli_risorse`
  ADD PRIMARY KEY (`idRuolo`,`idRisorsa`,`dataInizioValidita`);

--
-- Indici per le tabelle `au_sessioni`
--
ALTER TABLE `au_sessioni`
  ADD PRIMARY KEY (`idSessione`);

--
-- Indici per le tabelle `au_sessioni_impronte`
--
ALTER TABLE `au_sessioni_impronte`
  ADD PRIMARY KEY (`idSessione`,`idImpronta`);

--
-- Indici per le tabelle `au_two_fact`
--
ALTER TABLE `au_two_fact`
  ADD PRIMARY KEY (`idTwoFact`);

--
-- Indici per le tabelle `au_t_metodi_login`
--
ALTER TABLE `au_t_metodi_login`
  ADD PRIMARY KEY (`idTipoMetodoLogin`);

--
-- Indici per le tabelle `au_t_metodi_rec_psw`
--
ALTER TABLE `au_t_metodi_rec_psw`
  ADD PRIMARY KEY (`idTipoMetodoRecPsw`);

--
-- Indici per le tabelle `au_t_ruoli`
--
ALTER TABLE `au_t_ruoli`
  ADD PRIMARY KEY (`idTipoRuolo`);

--
-- Indici per le tabelle `au_utenti`
--
ALTER TABLE `au_utenti`
  ADD PRIMARY KEY (`idUtente`),
  ADD UNIQUE KEY `email` (`email`) USING HASH;

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `au_log_accessi`
--
ALTER TABLE `au_log_accessi`
  MODIFY `idLogAccesso` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `au_log_chiamate`
--
ALTER TABLE `au_log_chiamate`
  MODIFY `idLogChiamata` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `au_log_operazioni`
--
ALTER TABLE `au_log_operazioni`
  MODIFY `idLogOperazione` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `au_risorse`
--
ALTER TABLE `au_risorse`
  MODIFY `idRisorsa` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `au_utenti`
--
ALTER TABLE `au_utenti`
  MODIFY `idUtente` int(10) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;