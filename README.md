# Documentazione BE

# Otter Guardian BE

Questo repository contiene la componente di backend di Otter Guardian sviluppata in PHP nativo, la struttura del database, gli script di configurazione e altre informazioni utili. 

## Struttura del progetto

## Database

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

**generaIdentificativoDispositivoFisico** | **GET**

Descrizione: Viene generato un idDispositivoFisico associato all'utente da assegnare.

Query param aggiuntivi: 

| Nome | Valore |
| --- | --- |
|  |  |
|  |  |

Headers aggiuntivi:

| Header | Valore |
| --- | --- |
|  |  |
|  |  |

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

## Installazione

## Bom / Diba

## Licenza