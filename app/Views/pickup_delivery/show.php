<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('pickup-delivery') ?>">Pickup & Delivery</a></li>
<li class="breadcrumb-item active">Dettaglio Operazione</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-truck mr-2"></i>
                            Dettaglio Operazione #<?= $operazione['id'] ?>
                        </h3>
                        <div>
                            <a href="<?= base_url('pickup-delivery') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Torna alla Lista
                            </a>
                            <a href="<?= base_url('pickup-delivery/edit/' . $operazione['id']) ?>" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Modifica
                            </a>
                            <a href="<?= base_url('pickup-delivery/stampa/' . $operazione['id']) ?>" class="btn btn-info" target="_blank">
                                <i class="fas fa-print"></i> Stampa Promemoria
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <!-- Colonna sinistra -->
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-info-circle"></i> Informazioni Generali
                            </h5>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Titolo:</strong></td>
                                    <td><?= esc($operazione['titolo']) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Tipo:</strong></td>
                                    <td>
                                        <?php if ($operazione['tipo'] === 'ritiro'): ?>
                                            <span class="badge badge-info badge-lg">
                                                <i class="fas fa-truck-loading"></i> Ritiro
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-primary badge-lg">
                                                <i class="fas fa-truck"></i> Consegna
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Priorità:</strong></td>
                                    <td>
                                        <?php
                                        $priorityClass = match($operazione['priorita']) {
                                            'bassa' => 'secondary',
                                            'normale' => 'info',
                                            'alta' => 'warning',
                                            'urgente' => 'danger',
                                            default => 'secondary'
                                        };
                                        ?>
                                        <span class="badge badge-<?= $priorityClass ?> badge-lg">
                                            <?= ucfirst($operazione['priorita']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Stato:</strong></td>
                                    <td>
                                        <?php
                                        $statusClass = match($operazione['stato']) {
                                            'programmata' => 'primary',
                                            'in_corso' => 'warning',
                                            'completata' => 'success',
                                            'annullata' => 'danger',
                                            default => 'secondary'
                                        };
                                        ?>
                                        <span class="badge badge-<?= $statusClass ?> badge-lg">
                                            <?= ucfirst(str_replace('_', ' ', $operazione['stato'])) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Anagrafica:</strong></td>
                                    <td>
                                        <strong><?= esc($anagrafica['ragione_sociale']) ?></strong>
                                        <?php if (!empty($anagrafica['citta'])): ?>
                                            <br><small class="text-muted"><?= esc($anagrafica['citta']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if ($contatto): ?>
                                <tr>
                                    <td><strong>Contatto DB:</strong></td>
                                    <td>
                                        <?= esc($contatto['nome']) ?>
                                        <?php if (!empty($contatto['telefono'])): ?>
                                            <br><small class="text-muted"><i class="fas fa-phone"></i> <?= esc($contatto['telefono']) ?></small>
                                        <?php endif; ?>
                                        <?php if (!empty($contatto['email'])): ?>
                                            <br><small class="text-muted"><i class="fas fa-envelope"></i> <?= esc($contatto['email']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($attivita): ?>
                                <tr>
                                    <td><strong>Attività:</strong></td>
                                    <td>
                                        <i class="fas fa-tasks"></i> <?= esc($attivita['titolo']) ?>
                                        <br><small class="text-muted"><?= esc($attivita['descrizione']) ?></small>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                        
                        <!-- Colonna destra -->
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-calendar-alt"></i> Date e Orari
                            </h5>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Data Programmata:</strong></td>
                                    <td>
                                        <i class="fas fa-calendar"></i> 
                                        <?= date('d/m/Y H:i', strtotime($operazione['data_programmata'])) ?>
                                        <?php if (!empty($operazione['orario_preferito'])): ?>
                                            <br><small class="text-muted">Orario preferito: <?= esc($operazione['orario_preferito']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if ($operazione['data_completata']): ?>
                                <tr>
                                    <td><strong>Data Completata:</strong></td>
                                    <td>
                                        <i class="fas fa-check-circle text-success"></i> 
                                        <?= date('d/m/Y H:i', strtotime($operazione['data_completata'])) ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td><strong>Creata il:</strong></td>
                                    <td>
                                        <i class="fas fa-plus-circle"></i> 
                                        <?= date('d/m/Y H:i', strtotime($operazione['created_at'])) ?>
                                        <?php if ($utente_creatore): ?>
                                            <br><small class="text-muted">da <?= esc($utente_creatore['nome'] . ' ' . $utente_creatore['cognome']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if ($operazione['updated_at'] && $operazione['updated_at'] !== $operazione['created_at']): ?>
                                <tr>
                                    <td><strong>Ultima modifica:</strong></td>
                                    <td>
                                        <i class="fas fa-edit"></i> 
                                        <?= date('d/m/Y H:i', strtotime($operazione['updated_at'])) ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td><strong>Assegnato a:</strong></td>
                                    <td>
                                        <?php if ($utente_assegnato): ?>
                                            <i class="fas fa-user"></i> 
                                            <?= esc($utente_assegnato['nome'] . ' ' . $utente_assegnato['cognome']) ?>
                                            <?php if (!empty($utente_assegnato['email'])): ?>
                                                <br><small class="text-muted"><?= esc($utente_assegnato['email']) ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">Non assegnato</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Sezione indirizzo -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-map-marker-alt"></i> Indirizzo
                            </h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <address class="mb-0">
                                        <strong><?= esc($operazione['indirizzo']) ?></strong><br>
                                        <?php if (!empty($operazione['citta'])): ?>
                                            <?= esc($operazione['citta']) ?>
                                            <?php if (!empty($operazione['cap'])): ?>
                                                <?= esc($operazione['cap']) ?>
                                            <?php endif; ?>
                                            <?php if (!empty($operazione['provincia'])): ?>
                                                (<?= esc($operazione['provincia']) ?>)
                                            <?php endif; ?>
                                            <br>
                                        <?php endif; ?>
                                        <?php if (!empty($operazione['nazione'])): ?>
                                            <?= esc($operazione['nazione']) ?>
                                        <?php endif; ?>
                                    </address>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sezione contatto -->
                    <?php if (!empty($operazione['nome_contatto']) || !empty($operazione['telefono_contatto']) || !empty($operazione['email_contatto'])): ?>
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-user"></i> Informazioni Contatto
                            </h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        <?php if (!empty($operazione['nome_contatto'])): ?>
                                        <div class="col-md-4">
                                            <strong>Nome:</strong><br>
                                            <?= esc($operazione['nome_contatto']) ?>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($operazione['telefono_contatto'])): ?>
                                        <div class="col-md-4">
                                            <strong>Telefono:</strong><br>
                                            <a href="tel:<?= esc($operazione['telefono_contatto']) ?>">
                                                <i class="fas fa-phone"></i> <?= esc($operazione['telefono_contatto']) ?>
                                            </a>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($operazione['email_contatto'])): ?>
                                        <div class="col-md-4">
                                            <strong>Email:</strong><br>
                                            <a href="mailto:<?= esc($operazione['email_contatto']) ?>">
                                                <i class="fas fa-envelope"></i> <?= esc($operazione['email_contatto']) ?>
                                            </a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Sezione descrizione e note -->
                    <?php if (!empty($operazione['descrizione']) || !empty($operazione['note']) || !empty($operazione['note_trasportatore'])): ?>
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-sticky-note"></i> Descrizione e Note
                            </h5>
                            <div class="row">
                                <?php if (!empty($operazione['descrizione'])): ?>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0">Descrizione</h6>
                                        </div>
                                        <div class="card-body">
                                            <?= nl2br(esc($operazione['descrizione'])) ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($operazione['note'])): ?>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0">Note</h6>
                                        </div>
                                        <div class="card-body">
                                            <?= nl2br(esc($operazione['note'])) ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($operazione['note_trasportatore'])): ?>
                                <div class="col-12 mt-3">
                                    <div class="card border-warning">
                                        <div class="card-header bg-warning">
                                            <h6 class="card-title mb-0">
                                                <i class="fas fa-truck"></i> Note per il Trasportatore
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <?= nl2br(esc($operazione['note_trasportatore'])) ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Sezione DDT e costi -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-file-invoice"></i> DDT e Costi
                            </h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-<?= $operazione['richiesta_ddt'] ? 'success' : 'secondary' ?>">
                                            <i class="fas fa-file-alt"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Richiesta DDT</span>
                                            <span class="info-box-number">
                                                <?= $operazione['richiesta_ddt'] ? 'Sì' : 'No' ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($operazione['numero_ddt'])): ?>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info">
                                            <i class="fas fa-hashtag"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Numero DDT</span>
                                            <span class="info-box-number"><?= esc($operazione['numero_ddt']) ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($operazione['costo_stimato'])): ?>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-warning">
                                            <i class="fas fa-euro-sign"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Costo Stimato</span>
                                            <span class="info-box-number">€ <?= number_format($operazione['costo_stimato'], 2, ',', '.') ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($operazione['costo_effettivo'])): ?>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success">
                                            <i class="fas fa-euro-sign"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Costo Effettivo</span>
                                            <span class="info-box-number">€ <?= number_format($operazione['costo_effettivo'], 2, ',', '.') ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.badge-lg {
    font-size: 0.9rem;
    padding: 0.5rem 0.75rem;
}
.info-box {
    display: block;
    min-height: 90px;
    background: #fff;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 2px;
    margin-bottom: 15px;
}
.info-box-icon {
    border-top-left-radius: 2px;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 2px;
    display: block;
    float: left;
    height: 90px;
    width: 90px;
    text-align: center;
    font-size: 45px;
    line-height: 90px;
    background: rgba(0,0,0,0.2);
}
.info-box-content {
    padding: 5px 10px;
    margin-left: 90px;
}
.info-box-text {
    text-transform: uppercase;
    font-weight: bold;
    font-size: 13px;
}
.info-box-number {
    display: block;
    font-weight: bold;
    font-size: 18px;
}
</style>
<?= $this->endSection() ?> 