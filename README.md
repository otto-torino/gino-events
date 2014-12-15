Modulo events per gino CMS by Otto Srl, MIT license {#mainpage}
==============================================================
Libreria per la gestione di eventi categorizzati e calendarizzati.
La documentazione per lo sviluppatore è contenuta all'interno della directory doc.
La documentazione dell'ultima versione disponibile (1.0.0) si trova qui:

http://otto-torino.github.io/gino-events/

CARATTERISTICHE
------------------------------
- data
- nome
- slug (pretty url)
- durata
- descrizione
- tag
- immagine (con ridimensionamento)
- file allegato
- visualizzazione ristretta a gruppi di utenti di sistema
- geolocalizzazione
- condivisione social networks
- gestione di un flusso redazionale
- personalizzazione dei template
- contenuti ricercabili attraverso il modulo "Ricerca nel sito" di Gino
- contenuti resi disponibili al modulo newsletter di Gino (il modulo deve essere installato sul sistema)
- feed RSS
- microdata (schema.org)
- contenuti correlati

OPZIONI CONFIGURABILI
------------------------------
- calendario, primo giorno della settimana (lunedì, domenica)
- numero caratteri giorno in caliendario (L, LU, ...)
- apertura dettaglio evento in layer
- eventi per pagina in archivio
- numero di eventi mostrati nella vista vetrina
- id categoria eventi mostrati in vetrina
- larghezza massima immagini
- larghezza thumb
- numero di eventi esportati per il modulo newsletter

OUTPUTS PER INSERIMENTO IN LAYOUT
------------------------------
- calendario eventi
- vetrina eventi

OUTPUTS
------------------------------
- dettaglio evento
- archivio eventi con ricerca
- eventi mese
- feed RSS

INSTALLAZIONE
------------------------------
Per installare questa libreria seguire la seguente procedura:

- creare un pacchetto zip di nome "events_pkg.zip" con tutti i file e le cartelle eccetto README.md, Doxyfile e la directory doc
- loggarsi nell'area amministrativa e entrare nella sezione "moduli di sistema"
- seguire il link (+) "installa nuovo modulo" e caricare il pacchetto creato al punto 1
- creare nuove istanze del modulo nella sezione "moduli" dell'area amministrativa.

RELEASES
------------------------------

- 2014/12/15 | v 1.0.0 | Richiede gino 2.0.0
