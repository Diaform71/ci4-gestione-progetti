<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Installazione Applicazione</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="mb-4">Benvenuto nella procedura di installazione</h5>
                        <p>Questa procedura ti guiderà attraverso i seguenti passaggi:</p>
                        <ol>
                            <li>Verifica dei requisiti di sistema</li>
                            <li>Configurazione del database</li>
                            <li>Migrazione del database e creazione dell'utente amministratore</li>
                        </ol>
                        <div class="alert alert-info">
                            <strong>Nota:</strong> Assicurati di avere le seguenti informazioni prima di procedere:
                            <ul>
                                <li>Dati di accesso al database (host, nome utente, password)</li>
                                <li>Dati per la creazione dell'account amministratore</li>
                            </ul>
                        </div>
                        <div class="mt-4 text-end">
                            <a href="<?= site_url('install/requirements') ?>" class="btn btn-primary">Inizia Installazione</a>
                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        <small>Versione Installer: <?= $version ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>