# CodeIgniter 4 Gestione progetti

Il progetto è basato sul framework Codeigniter 4.6.0 e template gratuito AdminLTE 

![immagine](https://github.com/user-attachments/assets/3f92a666-33de-4e07-aa06-40e35e076598)

Sezioni:
- Anagrafiche 
- Contatti
- Progetti (comprende gestione sottoprogetti, vista kanban)  ![immagine](https://github.com/user-attachments/assets/7f7f3cd6-087d-442a-b5ee-e3381efa89c5)  ![immagine](https://github.com/user-attachments/assets/9d784109-92a5-41f1-8c72-f57fe34c8b85)


- Scadenze  ![immagine](https://github.com/user-attachments/assets/31cf73d6-a41f-4ac2-ae8d-69d047e167b5)


- Attività (comprende gestione sottoattività)
- Richieste d'offerta (gestione delle richieste a fornitore, stato richiesta, allegati, esportazione pdf, invio email a fornitore integrato con storico invio)
- Offerte fornitore (gestione delle offerte ricevute, allegati)
- Ordini d'acquisto (gestione degli ordini d'acquisto inviati, allegati, invio email integrato con storico invio)
- Materiali (gestione dei materiali inseriti nelle richieste d'offerta e negli ordini d'acquisto, tabelle per consultare storico)       ![immagine](https://github.com/user-attachments/assets/71e7f2f8-672f-4fa3-9055-c08a60fd773d)

- Gestione tabella aliquote IVA
- Gestione template email
- Impostazioni sistema  (di base l'utente dispone delle stesse impostazioni del sistema, al momento della modifica vengono create delle impostazioni personalizzate)

Installazione

Windows XAMPP
- copiare il contenuto del file .zip in un a sottocartella di Xampp/htdocs
- editare il file .env e modificare baseUrl con la sottocartella scelta
- creare un nuovo db in PhpMyAdmin (eventualmente creare un utente con password di accesso)
- eseguire le migrations con il comando 'php spark migrate --all' o in alternativa importare il dump del db (cartella db_sql del progetto)
- eseguire il seed AdminUtente con il comando 'php spark db:seed AdminUtente'

Release 1.0.1
- Aggiunto setup iniziale con creazione utente admin
- Correzioni minori


Sezioni al momento da sviluppare
- Notifiche sistema / push
- Gestione email utente (IMAP)
