<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item active">Home</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    
    <!-- Small Box -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?= count($progetti) ?></h3>
                    <p>Progetti</p>
                </div>
                <div class="icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <a href="<?= base_url('progetti') ?>" class="small-box-footer">
                    Mostra tutti <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?= count($attivita) ?></h3>
                    <p>Attività</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <a href="<?= base_url('attivita') ?>" class="small-box-footer">
                    Mostra tutte <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?= count($scadenze) ?></h3>
                    <p>Scadenze</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <a href="<?= base_url('scadenze') ?>" class="small-box-footer">
                    Mostra tutte <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3><?= count($richiesteOfferta) ?></h3>
                    <p>Richieste d'offerta</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <a href="<?= base_url('richieste-offerta') ?>" class="small-box-footer">
                    Mostra tutte <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Ultimi progetti -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-project-diagram mr-1"></i>
                        Ultimi progetti da completare
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('progetti') ?>" class="btn btn-tool">
                            <i class="fas fa-list"></i> Vedi tutti
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Stato</th>
                                    <th>Scadenza</th>
                                    <th>Priorità</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($progetti)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Nessun progetto da completare</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($progetti as $progetto): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= base_url('progetti/' . $progetto['id']) ?>">
                                                    <?= esc($progetto['nome']) ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php
                                                $statoClass = [
                                                    'in_corso' => 'success',
                                                    'completato' => 'info',
                                                    'sospeso' => 'warning',
                                                    'annullato' => 'danger',
                                                ];
                                                $statoText = [
                                                    'in_corso' => 'In corso',
                                                    'completato' => 'Completato',
                                                    'sospeso' => 'Sospeso',
                                                    'annullato' => 'Annullato',
                                                ];
                                                ?>
                                                <span class="badge badge-<?= $statoClass[$progetto['stato']] ?? 'secondary' ?>">
                                                    <?= $statoText[$progetto['stato']] ?? $progetto['stato'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= $progetto['data_scadenza'] ? date('d/m/Y', strtotime($progetto['data_scadenza'])) : 'N/D' ?>
                                            </td>
                                            <td>
                                                <?php
                                                $prioritaClass = [
                                                    'bassa' => 'success',
                                                    'media' => 'primary',
                                                    'alta' => 'warning',
                                                    'critica' => 'danger',
                                                ];
                                                ?>
                                                <span class="badge badge-<?= $prioritaClass[$progetto['priorita']] ?? 'secondary' ?>">
                                                    <?= ucfirst($progetto['priorita']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Attività da completare -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tasks mr-1"></i>
                        Attività da completare
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('attivita') ?>" class="btn btn-tool">
                            <i class="fas fa-list"></i> Vedi tutte
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Titolo</th>
                                    <th>Stato</th>
                                    <th>Scadenza</th>
                                    <th>Priorità</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($attivita)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Nessuna attività da completare</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($attivita as $task): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= base_url('attivita/view/' . $task['id']) ?>">
                                                    <?= esc($task['titolo']) ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php
                                                $statoClass = [
                                                    'da_iniziare' => 'secondary',
                                                    'in_corso' => 'success',
                                                    'in_pausa' => 'warning',
                                                    'completata' => 'info',
                                                    'annullata' => 'danger',
                                                ];
                                                $statoText = [
                                                    'da_iniziare' => 'Da iniziare',
                                                    'in_corso' => 'In corso',
                                                    'in_pausa' => 'In pausa',
                                                    'completata' => 'Completata',
                                                    'annullata' => 'Annullata',
                                                ];
                                                ?>
                                                <span class="badge badge-<?= $statoClass[$task['stato']] ?? 'secondary' ?>">
                                                    <?= $statoText[$task['stato']] ?? $task['stato'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= $task['data_scadenza'] ? date('d/m/Y', strtotime($task['data_scadenza'])) : 'N/D' ?>
                                            </td>
                                            <td>
                                                <?php
                                                $prioritaClass = [
                                                    'bassa' => 'success',
                                                    'media' => 'primary',
                                                    'alta' => 'warning',
                                                    'urgente' => 'danger',
                                                ];
                                                ?>
                                                <span class="badge badge-<?= $prioritaClass[$task['priorita']] ?? 'secondary' ?>">
                                                    <?= ucfirst($task['priorita']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Scadenze -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        Scadenze imminenti
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('scadenze') ?>" class="btn btn-tool">
                            <i class="fas fa-list"></i> Vedi tutte
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Titolo</th>
                                    <th>Progetto</th>
                                    <th>Scadenza</th>
                                    <th>Priorità</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($scadenze)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Nessuna scadenza imminente</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($scadenze as $scadenza): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= base_url('scadenze/dettaglio/' . $scadenza['id']) ?>">
                                                    <?= esc($scadenza['titolo']) ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php if (!empty($scadenza['nome_progetto'])): ?>
                                                    <a href="<?= base_url('progetti/' . $scadenza['id_progetto']) ?>">
                                                        <?= esc($scadenza['nome_progetto']) ?>
                                                    </a>
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $today = date('Y-m-d');
                                                $scadenza_date = date('Y-m-d', strtotime($scadenza['data_scadenza']));
                                                $class = 'text-dark';
                                                
                                                if ($scadenza_date < $today) {
                                                    $class = 'text-danger font-weight-bold';
                                                } elseif ($scadenza_date == $today) {
                                                    $class = 'text-warning font-weight-bold';
                                                }
                                                ?>
                                                <span class="<?= $class ?>">
                                                    <?= date('d/m/Y', strtotime($scadenza['data_scadenza'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $prioritaClass = [
                                                    'bassa' => 'success',
                                                    'media' => 'primary',
                                                    'alta' => 'warning',
                                                    'urgente' => 'danger',
                                                ];
                                                ?>
                                                <span class="badge badge-<?= $prioritaClass[$scadenza['priorita']] ?? 'secondary' ?>">
                                                    <?= ucfirst($scadenza['priorita']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Richieste d'offerta -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-invoice mr-1"></i>
                        Richieste d'offerta in attesa
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('richieste-offerta') ?>" class="btn btn-tool">
                            <i class="fas fa-list"></i> Vedi tutte
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Numero</th>
                                    <th>Oggetto</th>
                                    <th>Fornitore</th>
                                    <th>Data invio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($richiesteOfferta)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Nessuna richiesta d'offerta in attesa</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($richiesteOfferta as $richiesta): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= base_url('richieste-offerta/' . $richiesta['id']) ?>">
                                                    <?= esc($richiesta['numero']) ?>
                                                </a>
                                            </td>
                                            <td><?= esc($richiesta['oggetto']) ?></td>
                                            <td>
                                                <?php if (!empty($richiesta['id_anagrafica'])): ?>
                                                    <a href="<?= base_url('anagrafiche/show/' . $richiesta['id_anagrafica']) ?>">
                                                        <?= isset($richiesta['ragione_sociale']) ? esc($richiesta['ragione_sociale']) : (isset($richiesta['nome_fornitore']) ? esc($richiesta['nome_fornitore']) : 'Fornitore #' . $richiesta['id_anagrafica']) ?>
                                                    </a>
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= $richiesta['data_invio'] ? date('d/m/Y', strtotime($richiesta['data_invio'])) : 'N/D' ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
<?= $this->endSection() ?> 