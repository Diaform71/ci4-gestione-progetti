<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gestione Personale</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url('/plugins/bootstrap/css/bootstrap.min.css') ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('/plugins/fontawesome-free/css/all.min.css') ?>">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="<?= base_url('/dist/css/adminlte.min.css') ?>">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?= base_url('/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
    <style>
        body {
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            width: 400px;
        }
        .login-logo img {
            max-width: 150px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-logo">
            <h1><b>Gestione</b> Progetti</h1>
        </div>
        
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Accedi per iniziare la sessione</p>
                
                <?php if (session()->has('error')): ?>
                <div class="alert alert-danger">
                    <?= session('error') ?>
                </div>
                <?php endif; ?>
                
                <?php if (session()->has('message')): ?>
                <div class="alert alert-success">
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
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember" name="remember">
                                <label for="remember">
                                    Ricordami
                                </label>
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Accedi</button>
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