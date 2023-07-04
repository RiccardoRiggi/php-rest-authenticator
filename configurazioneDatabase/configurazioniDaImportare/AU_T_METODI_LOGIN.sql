INSERT INTO `au_t_metodi_login` (`idTipoMetodoLogin`, `descrizione`) VALUES('EMAIL_PSW_BACKUP_CODE', 'Inserisci la password e un codice di backup  generato precedentemente # Inserisci un codice di backup per completare l\'autenticazione');
INSERT INTO `au_t_metodi_login` (`idTipoMetodoLogin`, `descrizione`) VALUES('EMAIL_PSW_SIX_APP', 'Inserisci la password e il codice di sei cifre che riceverai sull\'authenticator # Inserisci il codice di 6 cifre che ti è stato mandato sull\'authenticator');
INSERT INTO `au_t_metodi_login` (`idTipoMetodoLogin`, `descrizione`) VALUES('EMAIL_PSW_SIX_EMAIL', 'Inserisci la password e il codice di sei cifre che riceverai via email # Inserisci il codice di 6 cifre che ti è stato mandato via email');
INSERT INTO `au_t_metodi_login` (`idTipoMetodoLogin`, `descrizione`) VALUES('EMAIL_PSW_SI_NO_APP', 'Inserisci la password, poi autorizza l\'accesso dall\'authenticator # Apri l\'authenticator e segui le istruzioni per completare l\'autenticazione');
INSERT INTO `au_t_metodi_login` (`idTipoMetodoLogin`, `descrizione`) VALUES('EMAIL_SIX_APP', 'Inserisci il codice di sei cifre che riceverai sull\'authenticator # Apri l\'authenticator e inserisci il codice di sei cifre che ti è stato mandato');
INSERT INTO `au_t_metodi_login` (`idTipoMetodoLogin`, `descrizione`) VALUES('EMAIL_SI_NO_APP', 'Autorizza l\'accesso dall\'authenticator # Apri l\'authenticator e segui le istruzioni per completare l\'autenticazione');
INSERT INTO `au_t_metodi_login` (`idTipoMetodoLogin`, `descrizione`) VALUES('QR_CODE', 'Inquadra il QrCode con l\'authenticator per effettuare l\'accesso');

-- Versione 1.1.0
INSERT INTO `au_t_metodi_login` (`idTipoMetodoLogin`, `descrizione`) VALUES('EMAIL_SIX_TELEGRAM', 'Inserisci il codice di sei cifre che riceverai su Telegram # Apri Telegram e inserisci il codice di sei cifre che ti è stato mandato');
INSERT INTO `au_t_metodi_login` (`idTipoMetodoLogin`, `descrizione`) VALUES('EMAIL_SI_NO_TELEGRAM', 'Autorizza l\'accesso da Telegram # Apri Telegram e segui le istruzioni per completare l\'autenticazione');
