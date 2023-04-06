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
	"idLogin": "642f1c93965fc0.34227908-642f1c93966020.20507271-642f1c93966034.99429615-642f1c93966040.12159849-642f1c93966053.78575796-642f1c93966067.35889901",
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
TOKEN	63f907cf1a77f9.37723667-63f907cf1a7aa9.47241054-63f907cf1a7c77.48547232-63f907cf1a7cb9.68996169-63f907cf1a7ce1.39915789-63f907cf1a7d35.59532876
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
TOKEN	63f907cf1a77f9.37723667-63f907cf1a7aa9.47241054-63f907cf1a7c77.48547232-63f907cf1a7cb9.68996169-63f907cf1a7ce1.39915789-63f907cf1a7d35.59532876
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
	"idQrCode": "642f1ee72c1957.94724209-642f1ee72c19e7.79964696-642f1ee72c19f2.33193355-642f1ee72c1a08.86634566-642f1ee72c1a16.74708134-642f1ee72c1a27.10688353"
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
TOKEN	63f907cf1a77f9.37723667-63f907cf1a7aa9.47241054-63f907cf1a7c77.48547232-63f907cf1a7cb9.68996169-63f907cf1a7ce1.39915789-63f907cf1a7d35.59532876
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
	"idDispositivoFisico": "63f0aaf45f4751.89502091-63f0aaf45f4825.15732768-63f0aaf45f4861.95274100-63f0aaf45f4898.16278241-63f0aaf45f48c7.01883347-63f0aaf45f48f9.73176656",
	"idQrCode": "63f8e6e4992894.37765412-63f8e6e4992905.88221546-63f8e6e4992926.57162399-63f8e6e4992946.63347334-63f8e6e4992965.96950008-63f8e6e4992988.71808184"
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
	"idDispositivoFisico": "63f0aaf45f4751.89502091-63f0aaf45f4825.15732768-63f0aaf45f4861.95274100-63f0aaf45f4898.16278241-63f0aaf45f48c7.01883347-63f0aaf45f48f9.73176656"
}
```

Response

```json
[
	{
		"idTwoFact": "63f8ed8a87fdd0.61206616-63f8ed8a87fe53.08963832-63f8ed8a87fe70.99466661-63f8ed8a87fe98.27412550-63f8ed8a87feb4.53975056-63f8ed8a87fec3.18741416",
		"idTipoLogin": "EMAIL_SI_NO_APP",
		"codice": "628859",
		"dataCreazione": "2023-02-24 18:02:02",
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
	"idDispositivoFisico": "63f0aaf45f4751.89502091-63f0aaf45f4825.15732768-63f0aaf45f4861.95274100-63f0aaf45f4898.16278241-63f0aaf45f48c7.01883347-63f0aaf45f48f9.73176656",
	"idTwoFact": "63f8ed8a87fdd0.61206616-63f8ed8a87fe53.08963832-63f8ed8a87fe70.99466661-63f8ed8a87fe98.27412550-63f8ed8a87feb4.53975056-63f8ed8a87fec3.18741416"
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
	"idRecPsw": "642f23e3992e21.62875171-642f23e3992e91.12275291-642f23e3992ea1.42070120-642f23e3992eb2.07657389-642f23e3992ec7.68146383-642f23e3992ed7.57117115",
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

**getRichiesteDiAccessoPendenti** | **POST**

Descrizione: Dato un idDispositivoFisico correttamente configurato viene restituito un eventuale accesso pre autorizzato da confermare.

Query param aggiuntivi: 

| Nome | Valore |
| --- | --- |
|  |  |
|  |  |

Headers:

| Header | Valore |
| --- | --- |
|  |  |
|  |  |

Body:

```json
{
	"idDispositivoFisico": "63f0aaf45f4751.89502091-63f0aaf45f4825.15732768-63f0aaf45f4861.95274100-63f0aaf45f4898.16278241-63f0aaf45f48c7.01883347-63f0aaf45f48f9.73176656"
}
```

Response

```json
[
	{
		"idTwoFact": "63f8ed8a87fdd0.61206616-63f8ed8a87fe53.08963832-63f8ed8a87fe70.99466661-63f8ed8a87fe98.27412550-63f8ed8a87feb4.53975056-63f8ed8a87fec3.18741416",
		"idTipoLogin": "EMAIL_SI_NO_APP",
		"codice": "628859",
		"dataCreazione": "2023-02-24 18:02:02",
		"tempoPassato": "0",
		"indirizzoIp": "127.0.0.1",
	}
]
```

---

## Bom / Diba

## Licenza