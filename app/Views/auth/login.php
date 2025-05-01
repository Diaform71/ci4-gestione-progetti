<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gestione Progetti</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('/plugins/fontawesome-free/css/all.min.css') ?>">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?= base_url('/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('/dist/css/adminlte.min.css') ?>">
    <style>
        .install-box {
            max-width: 450px;
            margin: 0 auto;
        }
        .login-page, .register-page {
            background: linear-gradient(to bottom, #007bff, #6c757d);
            height: auto;
            min-height: 100vh;
            align-items: start;
            padding-top: 50px;
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
        .login-card-body {
            border-radius: 0.25rem;
        }
    </style>
</head>
<body class="hold-transition login-page">
    <div class="logo-container">
        <h1><b>Gestione</b> Progetti</h1>
    </div>
    <div class="install-box">
        <div class="card ">
            <div class="card-header bg-primary">
                <h4 class="mb-0 text-white"><i class="fas fa-lock mr-2"></i> Accesso</h4>
            </div>
            <div class="card-body login-card-body">
                <p class="login-box-msg">Accedi per iniziare la sessione</p>
                
                <?php if (session()->has('error')): ?>
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Errore!</h5>
                    <?= session('error') ?>
                </div>
                <?php endif; ?>
                
                <?php if (session()->has('message')): ?>
                <div class="alert alert-success">
                    <h5><i class="icon fas fa-check"></i> Successo!</h5>
                    <?= session('message') ?>
                </div>
                <?php endif; ?>
                
                <form action="<?= base_url('login') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Username" name="username" value="<?= old('username') ?>">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" placeholder="Password" name="password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember" name="remember">
                                <label for="remember">
                                    Ricordami
                                </label>
                            </div>
                        </div>
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-sign-in-alt mr-2"></i> Accedi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="<?= base_url('/plugins/jquery/jquery.min.js') ?>"></script>
    <!-- Bootstrap 4 -->
    <script src="<?= base_url('/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?= base_url('/dist/js/adminlte.min.js') ?>"></script>
</body>
</html> 