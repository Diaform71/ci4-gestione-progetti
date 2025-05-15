# Gestione Progetti - Sistema di Project Management basato su CodeIgniter 4

Un sistema completo per la gestione dei progetti, degli acquisti e delle attività aziendali basato su CodeIgniter 4.6.0 e template AdminLTE.

![immagine](https://github.com/user-attachments/assets/ec804842-d1a3-4bbf-aa17-16e9b3dc10ed)


## Funzionalità Principali

### Gestione Anagrafica e Contatti
- **Anagrafiche**: Gestione completa dei dati anagrafici di clienti e fornitori
- **Contatti**: Rubrica completa dei contatti aziendali con relative informazioni

### Gestione Progetti
- **Progetti e Sottoprogetti**: Organizzazione gerarchica dei progetti
- **Vista Kanban**: Visualizzazione dei progetti in modalità Kanban per un controllo immediato dello stato di avanzamento
- **Timeline e Dashboard**: Visualizzazione temporale delle attività e panoramica dello stato dei progetti

![immagine](https://github.com/user-attachments/assets/0fdfd1ba-306b-40ab-8b3c-92231147bb26) 

![immagine](https://github.com/user-attachments/assets/6d058abb-66da-4f2e-8402-eba46fd37333)

### Attività e Scadenze
- **Gestione Attività**: Creazione e monitoraggio delle attività e sottoattività
- **Scadenze**: Calendario delle scadenze con visualizzazione intuitiva e sistema di alert

![immagine](https://github.com/user-attachments/assets/419a1eb1-8a96-4b6e-9942-818016665daf)

![immagine](https://github.com/user-attachments/assets/fb18d27d-215f-4b57-9780-7a8adfc546ad)

### Ciclo di Acquisto Completo
- **Richieste d'Offerta**: Gestione delle richieste a fornitori con tracciamento dello stato
- **Offerte Fornitori**: Archiviazione e confronto delle offerte ricevute
- **Ordini d'Acquisto**: Gestione completa degli ordini con generazione automatica di documenti
- **Materiali**: Catalogo completo dei materiali con storico prezzi e disponibilità

![immagine](https://github.com/user-attachments/assets/b68bf420-101a-49d3-8d7d-2859f5e13dd0)
![immagine](https://github.com/user-attachments/assets/3988d9f3-55eb-4dd3-9956-701559982a76)
![immagine](https://github.com/user-attachments/assets/c1aafd64-c7be-416f-aa0b-7920ad5995ea)
![immagine](https://github.com/user-attachments/assets/2b4764da-0140-4054-8fd2-2995b863f409)
![immagine](https://github.com/user-attachments/assets/0f7a2516-e6e6-4b1c-a497-f68c4e89b8d2)
![immagine](https://github.com/user-attachments/assets/ba02ade9-4bcd-413b-b953-a4081ca6f22a)

### Funzionalità Avanzate
- **Generazione PDF**: Creazione automatica di documenti in formato PDF
- **Sistema Email Integrato**: Invio email ai fornitori con tracciamento e storico
- **Gestione Allegati**: Upload e archiviazione di documenti e file per ogni entità
- **Aliquote IVA**: Gestione completa delle aliquote fiscali
- **Template Email**: Personalizzazione dei modelli di email utilizzati dal sistema
- **Impostazioni Personalizzate**: Configurazioni di sistema personalizzabili per ogni utente

## Installazione

### Requisiti
- PHP 8.1 o superiore
- MySQL o MariaDB
- Composer

### Procedura per Windows (XAMPP)
1. Copiare il contenuto del file .zip in una sottocartella di Xampp/htdocs
2. Creare il file `.env` con il comando
   ```
   cp env .env
   ```
3. Modificare il file .env appena creato impostando il corretto baseUrl con la sottocartella scelta  
   ```
   ad es. app.baseURL = 'http://localhost/ci4-gestione-progetti/public/' 
   ```
4. Eseguire la procedura iniziale di setup per creare in automatico il db, l'utente admin.

   E' possibile inserire dati iniziali per testare il sistema

## Funzionalità in Sviluppo
- Sistema di notifiche push
- Gestione email utente (IMAP)
- Integrazione con API esterne

## Licenza
Questo progetto è distribuito con licenza MIT. Vedere il file LICENSE per i dettagli.

