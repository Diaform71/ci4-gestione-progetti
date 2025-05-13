<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <style>
        .install-box {
            max-width: 800px;
            margin: 0 auto;
        }
        .login-page, .register-page {
            background: linear-gradient(to bottom, #007bff, #6c757d);
            height: auto;
            min-height: 100vh;
            align-items: start;
            padding-top: 50px;
        }
        .card-header {
            padding: 1rem;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
            width: 100%;
            display: flex;
            justify-content: center;
        }
        .logo-container h1 {
            font-weight: 300;
            color: #fff;
        }
        .logo-container h1 b {
            font-weight: 700;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 0 15px;
        }
        .step {
            text-align: center;
            width: 33%;
            position: relative;
        }
        .step.active .step-icon {
            background-color: #007bff;
            color: white;
        }
        .step-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 5px;
            color: #6c757d;
            font-weight: bold;
            border: 2px solid #e9ecef;
        }
        .step-title {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .step.active .step-title {
            color: #007bff;
            font-weight: bold;
        }
    </style>
</head>
<body class="hold-transition login-page">
    <div class="logo-container">
    <h1><b>Gestione</b> Progetti</h1>
    </div>
    <div class="install-box">
        <div class="step-indicator">
            <div class="step">
                <div class="step-icon">
                    <i class="fas fa-check"></i>
                </div>
                <div class="step-title">Benvenuto</div>
            </div>
            <div class="step">
                <div class="step-icon">
                    <i class="fas fa-check"></i>
                </div>
                <div class="step-title">Requisiti</div>
            </div>
            <div class="step">
                <div class="step-icon">
                    <i class="fas fa-check"></i>
                </div>
                <div class="step-title">Database</div>
            </div>
            <div class="step active">
                <div class="step-icon">4</div>
                <div class="step-title">Amministratore</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary">
                <h4 class="mb-0 text-white"><i class="fas fa-user-shield mr-2"></i> Creazione Account Amministratore</h4>
            </div>
            <div class="card-body">
                <h5 class="mb-3">Crea un account amministratore</h5>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <h5><i class="icon fas fa-ban"></i> Errore!</h5>
                        <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> Informazione</h5>
                    <p>Connessione al database riuscita! Ora è necessario creare un account amministratore per accedere al sistema.</p>
                </div>
                
                <form action="<?= site_url('install/migrate') ?>" method="post" id="migrateForm" class="form-horizontal">
                    <div class="card card-outline card-primary">
                        <div class="card-body">
                            <div class="form-group row">
                                <label for="admin_username" class="col-sm-3 col-form-label">Nome Utente</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="admin_username" name="admin_username" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="admin_email" class="col-sm-3 col-form-label">Email</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        </div>
                                        <input type="email" class="form-control" id="admin_email" name="admin_email" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="admin_password" class="col-sm-3 col-form-label">Password</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <div class="offset-sm-3 col-sm-9">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="insert_demo_data" name="insert_demo_data" value="1">
                                        <label class="custom-control-label" for="insert_demo_data">Inserisci dati dimostrativi (demo) nel database</label>
                                    </div>
                                    <small class="form-text text-muted">Utile per testare il sistema con dati di esempio precaricati.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 d-flex justify-content-between">
                        <a href="<?= site_url('install/database') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-2"></i> Indietro
                        </a>
                        <button type="submit" class="btn btn-success" id="submitBtn">
                            <span id="defaultText"><i class="fas fa-check mr-2"></i> Completa Installazione</span>
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

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
    
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