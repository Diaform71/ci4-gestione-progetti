<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - Gestione Progetti</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url('/plugins/bootstrap/css/bootstrap.min.css') ?>">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('/plugins/fontawesome-free/css/all.min.css') ?>">

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="<?= base_url('/adminlte/css/adminlte.min.css') ?>">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="<?= base_url('/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') ?>">

    <!-- Bootstrap Datepicker CSS -->
    <link rel="stylesheet" href="<?= base_url('/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">

    <!-- Custom CSS -->
    <style>
        .content-wrapper {
            padding: 20px;
        }

        .card-header {
            background-color: #f8f9fa;
        }

        .table th {
            border-top: none;
        }
    </style>

    <!-- Additional CSS -->
    <?= $this->renderSection('styles') ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <?= $this->include('layouts/partials/navbar'); ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="<?= base_url() ?>" class="brand-link">
                <span class="brand-text font-weight-light">Gestione Progetti</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <i class="fas fa-user-circle fa-2x text-light"></i>
                    </div>
                    <div class="info">
                        <?php if (session()->has('utente_logged_in')): ?>
                            <a href="<?= base_url('profilo') ?>" class="d-block">
                                <?php 
                                $nome = trim(session('utente_nome') ?? '');
                                $cognome = trim(session('utente_cognome') ?? '');
                                $username = session('utente_username');
                                
                                if (!empty($nome) && !empty($cognome)): ?>
                                    <?= esc($nome) ?> <?= esc($cognome) ?>
                                <?php else: ?>
                                    <?= esc($username) ?>
                                <?php endif; ?>
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url('login') ?>" class="d-block">Accedi</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <?= $this->include('layouts/partials/sidebar_menu'); ?>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0"><?= $this->renderSection('page_title') ?></h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <?= $this->renderSection('breadcrumb') ?>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <?= $this->renderSection('content') ?>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <footer class="main-footer">
            <div class="float-right d-none d-sm-inline">
                Gestione Progetti
            </div>
            <strong>&copy; <?= date('Y') ?> Diaform Solutions</strong>
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="<?= base_url('/plugins/jquery/jquery.min.js') ?>"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="<?= base_url('/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?= base_url('/adminlte/js/adminlte.min.js') ?>"></script>
    <!-- SweetAlert2 -->
    <script src="<?= base_url('/plugins/sweetalert2/sweetalert2.min.js') ?>"></script>

    <!-- Common JavaScript -->
    <script>
        // Funzione per mostrare notifiche
        function showNotification(title, message, type = 'success') {
            Swal.fire({
                title: title,
                text: message,
                icon: type,
                confirmButtonText: 'Ok'
            });
        }

        // Gestione dei messaggi flash
        <?php if (session()->getFlashdata('success')): ?>
            showNotification('Successo', <?= json_encode(session()->getFlashdata('success')) ?>, 'success');
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            showNotification('Errore', <?= json_encode(session()->getFlashdata('error')) ?>, 'error');
        <?php endif; ?>
    </script>

    <!-- Additional JavaScript -->
    <?= $this->renderSection('scripts') ?>

    <!-- Bootstrap Datepicker -->
    
    <script src="<?= base_url('/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') ?>"></script>
    <script src="<?= base_url('/plugins/bootstrap-datepicker/bootstrap-datepicker-it.min.js') ?>"></script>
    <script src="<?= base_url('/js/date-helper.js') ?>"></script>

    <script>
    // Fix per il dropdown utente
    $(document).ready(function() {
        // Assicurati che i dropdown di Bootstrap siano inizializzati correttamente
        $('[data-toggle="dropdown"]').dropdown();
    });
    </script>
</body>

</html>
