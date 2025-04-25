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
                        <h4 class="mb-0">Migrazione Database</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="mb-4">Crea un account amministratore</h5>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <?= $error ?>
                            </div>
                        <?php endif; ?>
                        
                        <form action="<?= site_url('install/migrate') ?>" method="post" id="migrateForm">
                            <div class="alert alert-info">
                                <p>Connessione al database riuscita! Ora è necessario creare un account amministratore per accedere al sistema.</p>
                            </div>
                            
                            <div class="mb-3">
                                <label for="admin_username" class="form-label">Nome Utente</label>
                                <input type="text" class="form-control" id="admin_username" name="admin_username" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="admin_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="admin_email" name="admin_email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="admin_password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="insert_demo_data" name="insert_demo_data" value="1">
                                <label class="form-check-label" for="insert_demo_data">Inserisci dati dimostrativi (demo) nel database</label>
                                <div class="form-text text-muted">Utile per testare il sistema con dati di esempio precaricati.</div>
                            </div>
                            
                            <div class="mt-4 d-flex justify-content-between">
                                <a href="<?= site_url('install/database') ?>" class="btn btn-secondary">Indietro</a>
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <span id="defaultText">Completa Installazione</span>
                                    <span id="loadingText" class="d-none">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Installazione in corso...
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
        const form = document.getElementById('migrateForm');
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