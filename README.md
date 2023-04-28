# Otter Guardian BE (ex php-rest-authenticator)

Questo repository contiene la componente di backend di Otter Guardian sviluppata in PHP nativo, la struttura del database, gli script di configurazione e altre informazioni utili. 

Attenzione! Il progetto non è ancora totalmente ultimato, è in corso la revisione della documentazione, del codice e l'ottimizzazione del database

## Struttura del progetto

## Database

All'interno della cartella configurazioneDatabase è presente il file php-rest-authenticator.sql che contiene la struttura del database. 

## API Rest

Il progetto è suddiviso in macro aree / moduli. Per ogni modulo è presente un file che contiene tutti i servizi esposti. Per richiamare ciascun metodo è necessario invocare il servizio utilizzando la seguente struttura:

```markdown
rest/{nomeModulo}.php?nomeMetodo={nomeMetodo}
```

I seguenti metodi sono richiamabili senza alcun token passato negli headers http

### Autenticazione.php

**getMetodoAutenticazionePredefinito | POST**

Dato un indirizzo email di un utente registrato restituisce il metodo di autenticazione predefinito

Query param aggiuntivi: no

Headers: no

Body:

```json
{
"email": "info@riccardoriggi.it"
}
```

Response

```json
[
	{
		"codice": "EMAIL_PSW_SIX_APP",
		"descrizione": "Inserisci la password e il codice di sei cifre che riceverai sull'authenticator",
	}
]
```

---

**getMetodiAutenticazioneSupportati | POST**

Descrizione: Dato un indirizzo email di un utente registrato restituisce l’elenco delle modalità di autenticazione che sono state configurate

Query param aggiuntivi: no

Headers: no

Body:

```json
{
"email": "info@riccardoriggi.it"
}
```

Response

```json
[
	{
		"codice": "EMAIL_PSW_SIX_APP",
		"descrizione": "Inserisci la password e il codice di sei cifre che riceverai sull'authenticator",
	}
]
```

---

**effettuaAutenticazione | POST**

Descrizione: Data l’email, la password e il tipo di autenticazione effettua la pre autorizzazione all’autenticazione

Query param aggiuntivi: no

Headers: no

Body:

```json
{
	"email": "info@riccardoriggi.it",
	"password":"1234567",
	"tipoAutenticazione":"EMAIL_PSW_SI_NO_APP"
}
```

Response

```json
{
	"idLogin": "642f1c93965f...",
	"descrizione": "Apri l'authenticator e segui le istruzioni per completare l'autenticazione"
}
```

---

**confermaAutenticazione | POST**

Descrizione: Dato l’idLogin restituito precedentemente e un codice di verifica generato e inoltrato al dispositivo fisico oppure all’indirizzo email, si può completare il processo di autenticazione. Verrà restituito un TOKEN negli headers http. 

Query param aggiuntivi: no

Headers: no

Body:

```json
{
	"email": "info@riccardoriggi.it",
	"password":"1234567",
	"tipoAutenticazione":"EMAIL_PSW_SI_NO_APP"
}
```

Response (Headers)

```json
TOKEN	63f...
```

---

**recuperaTokenDaLogin | GET**

Descrizione: Dato l’idLogin restituito precedentemente, se il processo di autenticazione si è concluso con successo, verrà restituito negli headers http un TOKEN. 

Query param aggiuntivi: 

| Nome | Valore |
| --- | --- |
| idLogin | 63f8ed8a86ff15……. |

Headers: no

Body: no

Response (Headers)

```json
TOKEN	63f907....
```

---

**generaQrCode | GET**

Descrizione: Viene generato un idQrCode da scansionare con il dispositivo fisico per entrare all’interno dell’applicazione.

Query param aggiuntivi: no

Headers: no

Body: no

Response

```json
{
	"idQrCode": "642f1ee7..."
}
```

---

**recuperaTokenDaQrCode | GET**

Descrizione: Dato l’idQrCode restituito precedentemente, se il processo di autenticazione si è concluso con successo, verrà restituito negli headers http un TOKEN. 

Query param aggiuntivi: 

| Nome | Valore |
| --- | --- |
| idQrCode | 63f8ed8a86ff15……. |

Headers: no

Body: no

Response (Headers)

```json
TOKEN	63f907cf1a77f...
```

---

### DispositivoFisico.php

**isDispositivoAbilitato** | **GET**

Descrizione: Dato un idDispositivoFisico indica se il dispositivo è stato correttamente abilitato

Query param aggiuntivi: 

| Nome | Valore |
| --- | --- |
| idDispositivoFisico | 63f0aacf536ac2…. |

Headers: no

Body: no

Response

```json
true
```

---

**autorizzaQrCode** | **POST**

Descrizione: Dato un idDispositivoFisico correttamente configurato e un idQrCode precedentemente generato, abilita quest’ultimo all’accesso

Query param aggiuntivi: no

Headers: no

Body:

```json
{
	"idDispositivoFisico": "63f0aaf45f4...",
	"idQrCode": "63f8e6e49928..."
}
```

Response: no body

---

**getRichiesteDiAccessoPendenti** | **POST**

Descrizione: Dato un idDispositivoFisico correttamente configurato viene restituito un eventuale accesso pre autorizzato da confermare.

Query param aggiuntivi: no

Headers: no

Body:

```json
{
	"idDispositivoFisico": "63f0aaf45f4..."
}
```

Response

```json
[
	{
		"idTwoFact": "63f8ed8a8...",
		"idTipoLogin": "EMAIL_SI_NO_APP",
		"codice": "628859",
		"dataCreazione": "2000-06-12 00:00:00",
		"tempoPassato": "0",
		"indirizzoIp": "127.0.0.1",
	}
]
```

---

**autorizzaAccesso** | **POST**

Descrizione: Dato un idDispositivoFisico correttamente configurato e un idTwoFact restituito dal metodo precedente, si autorizza quest’ultimo. L’idTwoFact è collegato all’idLogin. 

Query param aggiuntivi: no

Headers: no

Body:

```json
{
	"idDispositivoFisico": "63f0aaf45f...",
	"idTwoFact": "63f8ed8a87f..."
}
```

Response: no body

---

### RecuperoPassword.php

**getMetodiRecuperoPasswordSupportati | POST**

Descrizione: Dato un indirizzo email di un utente registrato restituisce l’elenco delle modalità di recupero password che sono state configurate

Query param aggiuntivi: no

Headers: no

Body:

```json
{
"email": "info@riccardoriggi.it"
}
```

Response

```json
[
	{
		"codice": "REC_PSW_EMAIL_SIX_APP",
		"descrizione": "Ricevi il codice di verifica sull'authenticator",
	}
]
```

---

**effettuaRichiestaRecuperoPassword | POST**

Descrizione: Dato un indirizzo email di un utente registrato e un tipo di recupero password viene pre autorizzato il cambio password

Query param aggiuntivi: no

Headers: no

Body:

```json
{
	"email": "info@riccardoriggi.it",	
	"tipoRecuperoPassword":"REC_PSW_EMAIL_SIX_APP"
}
```

Response

```json
{
	"idRecPsw": "642f23e39...",
	"descrizione": "Inserisci il codice di verifica e la nuova password"
}
```

---

**confermaRecuperoPassword | POST**

Descrizione: Dato l’idRecPsw generato precedentemente, il codice di verifica e la nuova password, si procederà al cambio password effettivo

Query param aggiuntivi: no

Headers: no

Body:

```json
{
	"idRecPsw": "63fb713.....7",
	"codice": "803280",
	"nuovaPassowrd":"1234567",
	"confermaNuovaPassowrd":"1234567"
}
```

Response: no body

---

I metodi seguenti dovranno essere chiamati passando negli headers http un TOKEN:

```json
TOKEN	63f907cf1a77f9.37723667-63f907cf1a7aa9.47241054-63f907cf1a7c77.48547232-63f907cf1a7cb9.68996169-63f907cf1a7ce1.39915789-63f907cf1a7d35.59532876
```

### DispositivoFisico.php

**generaIdentificativoDispositivoFisico** | **GET**

Descrizione: Viene generato un idDispositivoFisico associato all'utente da assegnare.

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
{
	"idDispositivoFisico": "6438471..."
}
```

---

**abilitaDispositivoFisico** | **PUT**

Descrizione: Viene abilitato l'idDispositivoFisico in oggetto.

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json
{
	"idDispositivoFisico":"63fc82856239d...",
	"nomeDispositivo":"Smartphone"
}

```

Response

```json

```

---

**disabilitaDispositivoFisico** | **PUT**

Descrizione: Viene disabilitato l'idDispositivoFisico in oggetto

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json
{
	"idDispositivoFisico":"63fc828562..."
}

```

Response

```json

```

---

**rimuoviDispositivoFisico** | **PUT**

Descrizione: Viene rimosso l'idDispositivoFisico in oggetto

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json
{
	"idDispositivoFisico":"63fc82856239d5..."
}

```

Response

```json

```

---

**getDispositiviFisici** | **GET**

Descrizione: Viene restituita la lista dei dispositivi dell'utente loggato

Query param aggiuntivi: 

| Nome | Valore |
| --- | --- |
| pagina | 1 |

Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
[
	{
		"nomeDispositivo": "Smartphone",
		"dataAbilitazione": "2000-06-12 00:00:00",
		"dataDisabilitazione": null
	}
]
```

---

**getListaDispositiviFisici** | **GET**

Descrizione: Viene restituita la lista di tutti i dispositivi degli utenti registrati nel sistema

Query param aggiuntivi: 

| Nome | Valore |
| --- | --- |
| pagina | 1 |

Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
[
	{
		"nomeDispositivo": "Smartphone",
		"dataAbilitazione": "2000-06-12 00:00:00",
		"dataDisabilitazione": null,
		"nome": "Riccardo ",
		"cognome": "Ing Riggi",
		"idDispositivoFisico": "6414ebbc198699..."
	}
]
```

---

### Notifiche.php

**getListaNotifiche** | **GET**

Descrizione: Viene mostrata la lista di tutte le notifiche generate

Query param aggiuntivi: 

| Nome | Valore |
| --- | --- |
| pagina | 1 |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
[
	{
		"idNotifica": "1",
		"titolo": "Titolo della notifica",
		"testo": "Questo è il testo della notifica",
		"dataCreazione": "2000-06-12 00:00:00",
	}
]
```

---

**inserisciNotifica** | **POST**

Descrizione: Metodo per inserire una nuova notifica

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json
{
	"titolo": "Titolo della notifica",
	"testo": "Questo è il testo della notifica",
}
```

Response

```json

```

---

**modificaNotifica** | **PUT**

Descrizione: Metodo per modificare una notifica

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idNotifica | 1 |


Headers aggiuntivi: nessuno

Body:

```json
{
	"titolo": "Nuovo titolo della notifica",
	"testo": "Questo è il nuovo testo della notifica",
}
```

Response

```json

```

---

**eliminaNotifica** | **DELETE**

Descrizione: Metodo per eliminare una notifica

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idNotifica | 1 |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

**getNotifica** | **GET**

Descrizione: Metodo per ottenere una notifica dato l'identificativo

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idNotifica | 1 |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
{
	"idNotifica": "1",
	"titolo": "Titolo della notifica",
	"testo": "Testo della notifica",
	"dataCreazione": "2000-06-12 00:00:00",
}
```

---

**getDestinatariNotifica** | **GET**

Descrizione: Metodo per ottenere la lista dei destinatari di una determinata notifica con lo stato lettura e invio

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idNotifica | 1 |
| pagina | 1 |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
[
	{
		"idUtente": "1",
		"nome": "Riccardo",
		"cognome": "Riggi",
		"email": "info@riccardoriggi.it",
		"idNotifica": "1",
		"dataInvio": "2000-06-12 00:00:00",
		"dataLettura": null,
	}
]
```

---

**leggiNotificheLatoUtente** | **GET**

Descrizione: Metodo per far marcare all'utente loggato lette tutte le notifiche

Query param aggiuntivi: nessuno
Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```
---

**eliminaNotificaLatoUtente** | **DELETE**

Descrizione: Metodo per eliminare una notifica lato utente

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idNotifica | 1 |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

**getNotificheLatoUtente** | **GET**

Descrizione: Metodo per ottenere la lista delle notifiche dato l'utente loggato

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| pagina | 1 |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
[
	{
		"idNotifica": "2",
		"titolo": "TITOLO NOTIFICA",
		"testo": "TESTO NOTIFICA",
		"dataCreazione": "2023-04-02 10:32:54",
	}
]
```

---

**getNotificaLatoUtente** | **GET**

Descrizione: Metodo per ottenere una notifica dato l'identificativo e l'utente loggato

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idNotifica | 1 |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
{
	"idNotifica": "1",
	"titolo": "Titolo della notifica",
	"testo": "Testo della notifica",
	"dataCreazione": "2000-06-12 00:00:00",
}
```

---

**inviaNotificaTutti** | **POST**

Descrizione: Metodo per inviare una notifica a tutti gli utenti

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idNotifica | 1 |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

**inviaNotificaRuolo** | **POST**

Descrizione: Metodo per inviare una notifica a tutti gli utenti che hanno il ruolo associato

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idNotifica | 1 |
| idTipoRuolo | AMM |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

**inviaNotificaUtente** | **POST**

Descrizione: Metodo per inviare una notifica ad un determinato utente

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idNotifica | 1 |
| idUtente | 1 |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

### GestioneAccessi.php

**getListaAccessi** | **GET**

Descrizione: Metodo per ottenere la lista di tutti gli accessi effettuati dagli utenti

Query param aggiuntivi: 

| Nome | Valore |
| --- | --- |
| pagina | 1 |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
[
	{
		"token": "6427d313aa82...",
		"dataInizioValidita": "2000-06-12 00:00:00",
		"dataUltimoUtilizzo": "2000-06-12 00:00:01",
		"nome": "Riccardo",
		"cognome": "Riggi",
		"indirizzoIp": "127.0.0.1",
		"userAgent": "Mozilla\/5.0 ...",
	}
]
```

---

**terminaAccesso** | **GET**

Descrizione: Metodo per disconnettere forzatamente un utente

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json
{
	"token":"6444fdfsf...."
} 
```

Response

```json

```

---

### Statistiche.php

**getStatisticheMetodi** | **GET**

Descrizione: Metodo che restituisce l'elenco delle risorse con il numero di invocazioni

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
[
	{
		"chiamate": "143",
		"nomeMetodo": "verificaAutenticazione",
	}
]
```

---

**getNumeroVociMenu** | **GET**

Descrizione: Metodo che restituisce il numero di voci di menu configurate

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
[
	{
		"numero": "24"
	}
]
```

Esistono anche i seguenti servizi: getNumeroRuoli, getNumeroUtenti, getNumeroAccessiAttivi, getNumeroRisorse, getNumeroLogin, getNumeroIndirizziIp, getNumeroDispositiviFisiciAttivi

---

### Utenti.php

**getListaUtenti** | **GET**

Descrizione: Viene mostrata la lista di tutti gli utenti registrati

Query param aggiuntivi: 

| Nome | Valore |
| --- | --- |
| pagina | 1 |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
[
	{
		"idUtente": "1",
		"nome": "Riccardo",
		"cognome": "Riggi",
		"email": "info@riccardoriggi.it",
		"dataBlocco": null,
	}
]
```

---

**inserisciUtente** | **POST**

Descrizione: Metodo per inserire un nuovo utente

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json
{
	"nome":"Mario",
	"cognome":"Rossi",
	"email":"info@ghiroinformatico.net",
	"password":"123456",
	"confermaPassword":"123456"
}
```

Response

```json

```

---

**modificaUtente** | **PUT**

Descrizione: Metodo per modificare un utente

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idUtente | 1 |


Headers aggiuntivi: nessuno

Body:

```json
{
	"nome":"Riccardo",
	"cognome":"Riggi"
}
```

Response

```json

```

---

**eliminaUtente** | **DELETE**

Descrizione: Metodo per eliminare un utente

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idUtente | 1 |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

**getUtente** | **GET**

Descrizione: Metodo per ottenere un utente

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idUtente | 1 |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
{
	"idUtente": "1",
	"nome": "Riccardo",
	"cognome": "Riggi",
	"email": "info@riccardoriggi.it",
	"dataBlocco": null,
}
```

---

**bloccaUtente** | **PUT**

Descrizione: Metodo per bloccare un utente

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idUtente | 1 |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

**sbloccaUtente** | **PUT**

Descrizione: Metodo per sbloccare un utente

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idUtente | 1 |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

### Ruoli.php

**getRuoli** | **GET**

Descrizione: Viene mostrata la lista di tutti i ruoli configurati

Query param aggiuntivi: 

| Nome | Valore |
| --- | --- |
| pagina | 1 |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
[
	{
		"idTipoRuolo": "AMM",
		"descrizione": "Amministratore",
	}
]
```

---

**inserisciRuolo** | **POST**

Descrizione: Metodo per inserire un nuovo ruolo

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json
{
	"idTipoRuolo":"GUEST",
	"descrizione":"Ospite"
}
```

Response

```json

```

---

**modificaRuolo** | **PUT**

Descrizione: Metodo per modificare un ruolo

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idTipoRuolo | GUEST |


Headers aggiuntivi: nessuno

Body:

```json
{
	"descrizione":"Ospite"
}
```

Response

```json

```

---

**eliminaRuolo** | **DELETE**

Descrizione: Metodo per eliminare un ruolo

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idTipoRuolo | GUEST |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

**getRuolo** | **GET**

Descrizione: Metodo per ottenere un ruolo

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idTipoRuolo | GUEST |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
{
	"idTipoRuolo": "AMM",
	"descrizione": "Amministratore",
}
```

---

**associaRuoloUtente** | **PUT**

Descrizione: Metodo per associare un ruolo ad un utente

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idUtente | 1 |
| idTipoRuolo | GUEST |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

**dissociaRuoloUtente** | **PUT**

Descrizione: Metodo per dissociare un ruolo ad un utente

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idUtente | 1 |
| idTipoRuolo | GUEST |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

**getUtentiPerRuolo** | **GET**

Descrizione: Metodo per ottenere la lista degli utenti dato un ruolo e l'eventuale associazione

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| pagina | 1 |
| idTipoRuolo | GUEST |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
[
	{
		"idUtente": "1",
		"nome": "Riccardo",
		"cognome": "Riggi",
		"email": "info@riccardoriggi.it",
		"dataCreazione": null,
		"idTipoRuolo": null,
	}
]
```

---

**associaRuoloRisorsa** | **PUT**

Descrizione: Metodo per associare un ruolo ad una risorsa

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idRisorsa | 1 |
| idTipoRuolo | GUEST |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

**dissociaRuoloRisorsa** | **PUT**

Descrizione: Metodo per dissociare un ruolo da una risorsa

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idRisorsa | 1 |
| idTipoRuolo | GUEST |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

**getRisorsePerRuolo** | **GET**

Descrizione: Metodo per ottenere la lista delle risorse dato un ruolo e l'eventuale associazione

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| pagina | 1 |
| idTipoRuolo | GUEST |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
[
		{
		"nomeMetodo": "nomeDelMetodo",
		"descrizione": "Descrizione",
		"idRisorsa": "NOME_DEL_METODO",
		"dataCreazione": null,
		"idTipoRuolo": null,
	}
]
```

---

**associaRuoloVoceMenu** | **PUT**

Descrizione: Metodo per associare un ruolo ad una voce di menu

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idVoceMenu | 1 |
| idTipoRuolo | GUEST |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

**dissociaRuoloVoceMenu** | **PUT**

Descrizione: Metodo per dissociare un ruolo da una voce di menu

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idVoceMenu | 1 |
| idTipoRuolo | GUEST |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

**getVociMenuPerRuolo** | **GET**

Descrizione: Metodo per ottenere la lista delle voci di menu dato un ruolo e l'eventuale associazione

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| pagina | 1 |
| idTipoRuolo | GUEST |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
[
	{
		"idVoceMenu": "1",
		"descrizione": "Gestione utenti",
		"path": "#",
		"icona": "fa-solid fa-user-group",
		"dataCreazione": "2023-03-06 20:36:15",
		"idTipoRuolo": "GUEST"
	}
]
```

---

### Risorse.php

**getRisorse** | **GET**

Descrizione: Viene mostrata la lista di tutte le risorse/metodi configurati

Query param aggiuntivi: 

| Nome | Valore |
| --- | --- |
| pagina | 1 |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
[
	{
		"idRisorsa": "IDENTIFICATIVO_STRINGA",
		"nomeMetodo": "identificativoStringa",
		"descrizione": "Risorsa di prova",
	}
]
```

---

**inserisciRisorsa** | **POST**

Descrizione: Metodo per inserire una nuova risorsa

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json
{
	"idRisorsa": "IDENTIFICATIVO_STRINGA",
	"nomeMetodo": "identificativoStringa",
	"descrizione": "Risorsa di prova",
}
```

Response

```json

```

---

**modificaRisorsa** | **PUT**

Descrizione: Metodo per modificare una risorsa

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idRisorsa | IDENTIFICATIVO_STRINGA |


Headers aggiuntivi: nessuno

Body:

```json
{
	"nomeMetodo": "identificativoStringa",
	"descrizione": "Risorsa di prova",
}
```

Response

```json

```

---

**eliminaRisorsa** | **DELETE**

Descrizione: Metodo per eliminare una risorsa

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idRisorsa | IDENTIFICATIVO_STRINGA |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

**getRisorsa** | **GET**

Descrizione: Metodo per ottenere una risorsa

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idRisorsa | IDENTIFICATIVO_STRINGA |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
{
	"idRisorsa": "IDENTIFICATIVO_STRINGA",
	"nomeMetodo": "identificativoStringa",
	"descrizione": "Risorsa di prova",
}
```

---

### Combo.php

**getComboVociMenu** | **GET**

Descrizione: Servizio per popolare la combo delle voci menu

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
[
	{
		"idVoceMenu": "10",
		"descrizione": "Accessi attivi",
	},
]
```
Esiste anche il servizio getComboRuoli

---

### Log.php

**getLogs** | **GET**

Descrizione: Servizio per recuperare i log

Query param aggiuntivi: 

| Nome | Valore |
| --- | --- |
| pagina | 1 |
| livelloLog | ERROR/WARN/INFO/DEBUG |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
[
	{
		"dataEvento": "2000-06-12 00:00:00",
		"logLevel": "INFO",
		"testo": "Log di prova",
		"path": "\/Github-Repository\/php-rest-authenticator\/rest\/autenticazione.php?nomeMetodo=getMedotoAutenticazionePredefinito",
		"indirizzoIp": "127.0.0.1",
		"metodoHttp": "POST",
	}
]
```
---

### VociMenu.php

**getVociMenu** | **GET**

Descrizione: Servizio per recuperare la lista di tutte le voci di menu

Query param aggiuntivi: 

| Nome | Valore |
| --- | --- |
| pagina | 1 |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
[
	{
		"idVoceMenu": "1",
		"idVoceMenuPadre": null,
		"descrizione": "Gestione utenti",
		"descrizionePadre": null,
		"path": "#",
		"icona": "fa-solid fa-user-group",
		"ordine": "1",
	}
]
```

---

**inserisciVoceMenu** | **POST**

Descrizione: Metodo per inserire una nuova voce di menu

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json
{
	"idVoceMenuPadre":null,
	"descrizione":"Voce di prova",
	"path":"inserisci-prova",
	"icona":"fa-solid fa-users",
	"ordine":1
}
```

Response

```json

```

---

**modificaVoceMenu** | **PUT**

Descrizione: Metodo per modificare una voce di menu

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idVoceMenu | 1 |


Headers aggiuntivi: nessuno

Body:

```json
{
	"idVoceMenuPadre":null,
	"descrizione":"Voce di prova",
	"path":"inserisci-prova",
	"icona":"fa-solid fa-users",
	"ordine":1,
	"visibile":1
}
```

Response

```json

```

---

**eliminaVoceMenu** | **DELETE**

Descrizione: Metodo per eliminare una voce di menu

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idVoceMenu | 1 |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

**getVoceMenu** | **GET**

Descrizione: Metodo per ottenere una voce di menu

Query param aggiuntivi:

| Nome | Valore |
| --- | --- |
| idTipoRuolo | GUEST |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
{
	"idTipoRuolo": "AMM",
	"descrizione": "Amministratore",
}
```

---

**getVociMenuPerUtente** | **GET**

Descrizione: Servizio per recuperare il menu da mostrare nella Sidebar

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
[
	{
		"idVoceMenu": "1",
		"0": "1",
		"idVoceMenuPadre": null,
		"1": null,
		"descrizione": "Voci di menu",
		"2": "Voci di menu",
		"path": "#",
		"3": "#",
		"icona": "fa-solid fa-bars",
		"4": "fa-solid fa-bars",
		"ordine": "1",
		"5": "1",
		"figli": [
			{
				"idVoceMenu": "2",
				"0": "2",
				"idVoceMenuPadre": "1",
				"1": "1",
				"descrizione": "Inserisci voce di menu",
				"2": "Inserisci voce di menu",
				"path": "inserisci-voce-menu",
				"3": "inserisci-voce-menu",
				"icona": "fa-solid fa-plus",
				"4": "fa-solid fa-plus",
				"ordine": "1",
				"5": "1",
				"figli": [
					{
						"idVoceMenu": "3",
						"0": "3",
						"idVoceMenuPadre": "2",
						"1": "2",
						"descrizione": "Voce nipote",
						"2": "Voce nipote",
						"path": "nipote-path",
						"3": "nipote-path",
						"icona": "fa-solid fa-otter",
						"4": "fa-solid fa-otter",
						"ordine": "1",
						"5": "1",
						"figli": [
							{
								"idVoceMenu": "4",
								"0": "4",
								"idVoceMenuPadre": "3",
								"1": "3",
								"descrizione": "Voce pro nipote",
								"2": "Voce pro nipote",
								"path": "pronoipote-path",
								"3": "pronoipote-path",
								"icona": "aaa",
								"4": "aaa",
								"ordine": "1",
								"5": "1",
								"figli": []
							}
						]
					}
				]
			}
		]
	}
]
```

---

### UtenteLoggato.php

**generaCodiciBackup** | **GET**

Descrizione: Servizio per generare una lista di codici di backup

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
[
	"641156305390355513",
	"901914188645850925",
	"589616375289285775",
	"418214464776258682",
	"374392776320180920"
]
```

---

**getUtenteLoggato** | **GET**

Descrizione: Servizio per recuperare le informazioni dell'utente loggato

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
{
	"idUtente": "1",
	"nome": "Riccardo",
	"cognome": "Riggi",
	"email": "info@riccardoriggi.it",
	"dataCreazione": "2000-06-12 00:00:00",
	"dataUltimaModifica": null
}
```

---

**getStoricoAccessi** | **GET**

Descrizione: Servizio per recuperare l'elenco degli accessi effettuati

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
[
	{
		"dataInizioValidita": "2000-06-12 00:00:00",
		"dataFineValidita": null,
		"dataUltimoUtilizzo": "2000-06-12 00:00:00",
		"indirizzoIp": "127.0.0.1",
		"userAgent": "Mozilla\/5.0 (Linux; Android 6.0; Nexus 5 Build\/MRA58N) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/111.0.0.0 Mobile Safari\/537.36",
	}
]
```

---

**getMetodiAutenticazionePerUtenteLoggato** | **GET**

Descrizione: Servizio che mostra la configurazione per il secondo fattore d'autenticazione

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
[
	{
		"idTipoMetodoLogin": "EMAIL_PSW_BACKUP_CODE",
		"0": "EMAIL_PSW_BACKUP_CODE",
		"descrizione": "Inserisci la password e un codice di backup  generato precedentemente # Inserisci un codice di backup per completare l'autenticazione",
		"1": "Inserisci la password e un codice di backup  generato precedentemente # Inserisci un codice di backup per completare l'autenticazione",
		"idUtente": null,
		"2": null
	}
]
```

---

**getMetodiRecuperoPasswordPerUtenteLoggato** | **GET**

Descrizione: Servizio che mostra la configurazione per il secondo fattore per il recupero password

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

**invalidaToken** | **PUT**

Descrizione: Servizio che invalida il token

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

**abilitaTipoMetodoLogin** | **PUT**

Descrizione: Servizio che abilita il metodo di autenticazione all'utente

Query param aggiuntivi: 

| Nome | Valore |
| --- | --- |
| idTipoMetodoLogin | EMAIL_SIX_APP |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

**disabilitaTipoMetodoLogin** | **PUT**

Descrizione: Servizio che disabilita il metodo di autenticazione dall'utente

Query param aggiuntivi: 

| Nome | Valore |
| --- | --- |
| idTipoMetodoLogin | EMAIL_SIX_APP |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

**abilitaTipoRecuperoPassword** | **PUT**

Descrizione: Servizio che abilita il metodo di recupero password all'utente

Query param aggiuntivi: 

| Nome | Valore |
| --- | --- |
| idTipoMetodoRecPsw | EMAIL_SIX_APP |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

**disabilitaTipoMetodoLogin** | **PUT**

Descrizione: Servizio che disabilita il metodo di recupero password dall'utente

Query param aggiuntivi: 

| Nome | Valore |
| --- | --- |
| idTipoMetodoRecPsw | EMAIL_SIX_APP |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json

```

---

### IndirizziIp.php

**getIndirizziIp** | **GET**

Descrizione: Servizio che mostra la lista di tutti gli indirizzi ip che hanno contattato l'applicazione

Query param aggiuntivi: 

| Nome | Valore |
| --- | --- |
| pagina | 1 |


Headers aggiuntivi: nessuno

Body:

```json

```

Response

```json
[
	{
		"indirizzoIp": "127.0.0.1",
		"contatoreAlert": "0",
		"dataBlocco": null,
	}
]
```

---

**sbloccaIndirizzoIp** | **PUT**

Descrizione: Servizio che sblocca un indirizzo ip

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json
{
	"indirizzoIp":"127.0.0.1"
}
```

Response

```json

```

---

**bloccaIndirizzoIp** | **PUT**

Descrizione: Servizio che blocca un indirizzo ip

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json
{
	"indirizzoIp":"127.0.0.1"
}
```

Response

```json

```

---

**azzeraContatoreAlert** | **PUT**

Descrizione: Servizio che azzera il numero di alert per un indirizzo ip

Query param aggiuntivi: nessuno

Headers aggiuntivi: nessuno

Body:

```json
{
	"indirizzoIp":"127.0.0.1"
}
```

Response

```json

```

---

## Installazione

0. Ricordati di modificare il file config.php a tuo piacimento
1. Importa la struttura del database dal file php-rest-authenticator.sql
2. Lancia le INSERT dalla cartella configurazioniDaImportare
3. Apri il file utentiService.php e commenda la linea di codice come nell'esempio

```php

if (!function_exists('inserisciUtente')) {
    function inserisciUtente($nomeInChiaro, $cognomeInChiaro, $emailInChiaro, $passwordInChiaro)
    {
        //verificaValiditaToken();

        $nome = cifraStringa($nomeInChiaro);
        $cognome = cifraStringa($cognomeInChiaro);
        $email = cifraStringa($emailInChiaro);
        $password = md5(md5($passwordInChiaro));

```

Poi segui le istruzioni per inserire un nuovo utente, ricordati di togliere il commento dalla linea di codice.

4. Esegui una INSERT manualmente dentro la tavola PREFISSO_RUOLI_UTENTI con idTipoRuolo AMM e idUtente uguale a quello generato
5. Entra nell'applicativo per utilizzare tutte le funzionalità come utente amministratore


## Bom / Diba

Il codice è scritto in php nativo, non sono stati utilizzati framework. 

## Licenza
Il codice sorgente viene rilasciato con licenza [MIT](https://github.com/RiccardoRiggi/php-rest-authenticator/blob/main/LICENSE). Le varie esensioni di php utilizzate mantengono le loro relative licenze.
  

## Garanzia limitata ed esclusioni di responsabilità

Il software viene fornito "così com'è", senza garanzie. Riccardo Riggi non concede alcuna garanzia per il software e la relativa documentazione in termini di correttezza, accuratezza, affidabilità o altro. L'utente si assume totalmente il rischio utilizzando questo applicativo.