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
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">Installazione Completata</h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 64px;"></i>
                        </div>
                        
                        <h4 class="mb-3">Congratulazioni!</h4>
                        <p class="mb-4">L'applicazione è stata installata con successo.</p>
                        
                        <div class="alert alert-warning">
                            <p><strong>Importante:</strong> Per motivi di sicurezza, si consiglia di eliminare o rinominare la cartella "install" o disabilitare il controller di installazione.</p>
                        </div>
                        
                        <div class="mt-4">
                            <a href="<?= base_url() ?>" class="btn btn-primary">Vai alla pagina iniziale</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>