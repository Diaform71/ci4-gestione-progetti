<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Il Mio Profilo<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Il Mio Profilo<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item active">Il Mio Profilo</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <!-- Profilo card -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>

                    <h3 class="profile-username text-center">
                        <?= esc($utente['nome']) ?> <?= esc($utente['cognome']) ?>
                    </h3>

                    <p class="text-muted text-center">
                        <?= esc($utente['username']) ?>
                    </p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Email</b> <a class="float-right"><?= esc($utente['email']) ?: '-' ?></a>
                        </li>
                        <li class="list-group-item">
                            <b>Ultimo accesso</b> <a class="float-right">
                                <?= $utente['ultimo_accesso'] ? date('d/m/Y H:i', strtotime($utente['ultimo_accesso'])) : '-' ?>
                            </a>
                        </li>
                    </ul>

                    <a href="<?= base_url('profilo/modifica') ?>" class="btn btn-primary btn-block">
                        <i class="fas fa-edit"></i> Modifica Profilo
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <!-- About Me Box -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Informazioni Utente</h3>
                </div>
                <div class="card-body">
                    <strong><i class="fas fa-user mr-1"></i> Nome Completo</strong>
                    <p class="text-muted">
                        <?= esc($utente['nome']) ?> <?= esc($utente['cognome']) ?>
                    </p>
                    <hr>

                    <strong><i class="fas fa-envelope mr-1"></i> Email</strong>
                    <p class="text-muted"><?= esc($utente['email']) ?: 'Non specificata' ?></p>
                    <hr>

                    <strong><i class="fas fa-calendar mr-1"></i> Data Registrazione</strong>
                    <p class="text-muted">
                        <?= date('d/m/Y H:i', strtotime($utente['created_at'])) ?>
                    </p>
                    <hr>

                    <strong><i class="fas fa-clock mr-1"></i> Ultimo Accesso</strong>
                    <p class="text-muted">
                        <?= $utente['ultimo_accesso'] ? date('d/m/Y H:i', strtotime($utente['ultimo_accesso'])) : 'Mai' ?>
                    </p>
                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <a href="<?= base_url('cambio-password') ?>" class="btn btn-secondary">
                                <i class="fas fa-key"></i> Cambio Password
                            </a>
                            <a href="<?= base_url('profilo/modifica') ?>" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Modifica Profilo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 