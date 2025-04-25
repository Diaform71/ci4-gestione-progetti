<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Configurazione Database</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="mb-4">Inserisci i dettagli di connessione al database</h5>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <?= $error ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($validation)): ?>
                            <div class="alert alert-danger">
                                <?= $validation->listErrors() ?>
                            </div>
                        <?php endif; ?>
                        
                        <form action="<?= site_url('install/database') ?>" method="post" id="databaseForm">
                            <div class="mb-3">
                                <label for="hostname" class="form-label">Host Database</label>
                                <input type="text" class="form-control" id="hostname" name="hostname" value="localhost" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="port" class="form-label">Porta Database</label>
                                <input type="number" class="form-control" id="port" name="port" value="3306" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="database" class="form-label">Nome Database</label>
                                <input type="text" class="form-control" id="database" name="database" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">Utente Database</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password Database</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                            
                            <div class="mb-3">
                                <label for="prefix" class="form-label">Prefisso Tabelle (opzionale)</label>
                                <input type="text" class="form-control" id="prefix" name="prefix">
                            </div>
                            
                            <div class="mt-4 d-flex justify-content-between">
                                <a href="<?= site_url('install/requirements') ?>" class="btn btn-secondary">Indietro</a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <span id="defaultText">Verifica Connessione</span>
                                    <span id="loadingText" class="d-none">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Connessione in corso...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('databaseForm');
        const defaultText = document.getElementById('defaultText');
        const loadingText = document.getElementById('loadingText');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', function() {
            // Mostra loading state
            defaultText.classList.add('d-none');
            loadingText.classList.remove('d-none');
            submitBtn.disabled = true;
        });
    });
    </script>
</body>
</html>