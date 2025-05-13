@echo off
SET "TASK_NAME=NotificheScadenzeProgetti"
SET "TASK_DESCRIPTION=Esegue il comando Spark di CodeIgniter4 per notificare i progetti in scadenza."
SET "PHP_PATH=C:\xampp\php\php.exe"
SET "PROJECT_PATH=C:\xampp\htdocs\ci4_gestione_progetti"

schtasks /create /tn "%TASK_NAME%" /tr "\"%PHP_PATH%\" \"%PROJECT_PATH%\spark\" notifiche:scadenze" /sc daily /st 18:00 /rl HIGHEST /f