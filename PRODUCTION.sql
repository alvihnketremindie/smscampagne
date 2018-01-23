CREATE TABLE IF NOT EXISTS `animmessage` (
  `id_pushmessage` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `dateDebut` datetime NOT NULL,
  `dateFin` datetime NOT NULL,
  `service` varchar(20) NOT NULL,
  `type` enum('INFORMATIF','ANIMATION','RELANCE','FINDEPROMO','AUTRE') NOT NULL,
  `libelle` text NOT NULL,
  `statut` enum('EN-ATTENTE','EN-COURS','PAUSE','TERMINER','ECHEC','SUPPRIMER','EXPIRER') NOT NULL DEFAULT 'EN-ATTENTE',
  `dernier_id` int(10) unsigned NOT NULL DEFAULT '0',
  `quantite` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_pushmessage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `blacklist` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `telephone` varchar(20) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `telephone` (`telephone`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `broadcast` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `telephone` varchar(20) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `telephone` (`telephone`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pushmessage` (
  `id_pushmessage` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `datecreation` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datedebut` datetime NOT NULL,
  `datefin` datetime NOT NULL,
  `sender` varchar(15) NOT NULL,
  `operateur` varchar(20) NOT NULL,
  `service` varchar(20) NOT NULL,
  `smstype` enum('sms','flash','voice') NOT NULL DEFAULT 'sms',
  `libelle` text NOT NULL,
  `numbers` varchar(50) NOT NULL,
  `basetype` enum('TABLE','FICHIER') NOT NULL DEFAULT 'TABLE',
  `statut` enum('EN-ATTENTE','EN-COURS','PAUSE','TERMINER','ECHEC','SUPPRIMER','EXPIRER') NOT NULL DEFAULT 'EN-ATTENTE',
  `iddebut` int(10) unsigned NOT NULL DEFAULT '0',
  `idfin` int(10) unsigned NOT NULL DEFAULT '0',
  `idcourant` int(10) unsigned NOT NULL DEFAULT '0',
  `quantite` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_pushmessage`),
  KEY `dateDebut` (`datedebut`),
  KEY `dateCreation` (`datecreation`),
  KEY `dateFin` (`datefin`),
  KEY `statut` (`statut`),
  KEY `iddebut` (`iddebut`),
  KEY `idfin` (`idfin`),
  KEY `idcourant` (`idcourant`),
  KEY `service` (`service`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pushvoice` (
  `id_pushvoice` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `datecreation` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datedebut` datetime NOT NULL,
  `datefin` datetime NOT NULL,
  `sender` varchar(15) NOT NULL,
  `operateur` varchar(20) NOT NULL,
  `service` varchar(20) NOT NULL,
  `soundfile` text NOT NULL,
  `numbers` varchar(50) NOT NULL,
  `serveur` varchar(50) NOT NULL,
  `basetype` enum('TABLE','FICHIER') NOT NULL DEFAULT 'TABLE',
  `statut` enum('EN-ATTENTE','EN-COURS','PAUSE','TERMINER','ECHEC','SUPPRIMER','EXPIRER') NOT NULL DEFAULT 'EN-ATTENTE',
  `iddebut` int(10) unsigned NOT NULL DEFAULT '0',
  `idfin` int(10) unsigned NOT NULL DEFAULT '0',
  `idcourant` int(10) unsigned NOT NULL DEFAULT '0',
  `quantite` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_pushvoice`),
  KEY `dateDebut` (`datedebut`),
  KEY `dateCreation` (`datecreation`),
  KEY `dateFin` (`datefin`),
  KEY `statut` (`statut`),
  KEY `iddebut` (`iddebut`),
  KEY `idfin` (`idfin`),
  KEY `idcourant` (`idcourant`),
  KEY `service` (`service`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
