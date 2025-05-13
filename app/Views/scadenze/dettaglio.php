<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Dettaglio Scadenza<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Dettaglio Scadenza<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('scadenze') ?>">Scadenze</a></li>
<li class="breadcrumb-item active">Dettaglio</li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .badge-urgente {
        background-color: #dc3545;
        color: white;
    }
    .badge-alta {
        background-color: #fd7e14;
        color: white;
    }
    .badge-media {
        background-color: #ffc107;
        color: black;
    }
    .badge-bassa {
        background-color: #28a745;
        color: white;
    }
    
    .timeline-item {
        margin-bottom: 15px;
    }
    .timeline-item .time {
        color: #999;
        font-size: 0.85em;
    }
    
    .card-header .btn-group {
        position: absolute;
        right: 10px;
        top: 5px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Dettaglio Scadenza -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <?= esc($scadenza['titolo']) ?>
                    <?php if ($scadenza['completata']): ?>
                        <span class="badge badge-success">Completata</span>
                    <?php endif; ?>
                </h3>
                
                <div class="card-tools">
                    <a href="<?= base_url('scadenze/modifica/' . $scadenza['id']) ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i> Modifica
                    </a>
                    <a href="<?= base_url('scadenze/completa/' . $scadenza['id']) ?>" class="btn btn-sm btn-success">
                        <i class="fas <?= $scadenza['completata'] ? 'fa-undo' : 'fa-check' ?>"></i> 
                        <?= $scadenza['completata'] ? 'Riapri' : 'Completa' ?>
                    </a>
                    <a href="javascript:void(0);" class="btn btn-sm btn-danger btn-delete" onclick="confermaEliminazione(<?= $scadenza['id'] ?>)" data-id="<?= $scadenza['id'] ?>">
                        <i class="fas fa-trash"></i> Elimina
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4>Descrizione</h4>
                        <div class="p-3 bg-light rounded">
                            <?php if (!empty($scadenza['descrizione'])): ?>
                                <?= nl2br(esc($scadenza['descrizione'])) ?>
                            <?php else: ?>
                                <em>Nessuna descrizione disponibile</em>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h4>Progetto</h4>
                        <?php if (!empty($scadenza['nome_progetto'])): ?>
                            <p>
                                <a href="<?= base_url('progetti/dettaglio/' . $scadenza['id_progetto']) ?>">
                                    <?= esc($scadenza['nome_progetto']) ?>
                                </a>
                            </p>
                        <?php else: ?>
                            <p><em>Nessun progetto collegato</em></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6">
                        <h4>Attività</h4>
                        <?php if (!empty($scadenza['titolo_attivita'])): ?>
                            <p>
                                <a href="<?= base_url('attivita/dettaglio/' . $scadenza['id_attivita']) ?>">
                                    <?= esc($scadenza['titolo_attivita']) ?>
                                </a>
                            </p>
                        <?php else: ?>
                            <p><em>Nessuna attività collegata</em></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Dettagli</h3>
            </div>
            <div class="card-body p-0">
                <table class="table">
                    <tr>
                        <th style="width:40%">Priorità</th>
                        <td>
                            <span class="badge badge-<?= esc($scadenza['priorita']) ?>">
                                <?= ucfirst(esc($scadenza['priorita'])) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Stato</th>
                        <td>
                            <?php
                                $statoLabel = '';
                                $statoClass = '';
                                
                                switch($scadenza['stato']) {
                                    case 'da_iniziare':
                                        $statoLabel = 'Da iniziare';
                                        $statoClass = 'badge-secondary';
                                        break;
                                    case 'in_corso':
                                        $statoLabel = 'In corso';
                                        $statoClass = 'badge-primary';
                                        break;
                                    case 'completata':
                                        $statoLabel = 'Completata';
                                        $statoClass = 'badge-success';
                                        break;
                                    case 'annullata':
                                        $statoLabel = 'Annullata';
                                        $statoClass = 'badge-danger';
                                        break;
                                    default:
                                        $statoLabel = ucfirst($scadenza['stato']);
                                        $statoClass = 'badge-info';
                                }
                            ?>
                            <span class="badge <?= $statoClass ?>"><?= $statoLabel ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Data Scadenza</th>
                        <td>
                            <?= date('d/m/Y', strtotime($scadenza['data_scadenza'])) ?>
                            <?php 
                                $dataScadenza = new DateTime($scadenza['data_scadenza']);
                                $dataScadenza->setTime(0, 0, 0); // Imposta l'ora a 00:00:00
                                
                                $today = new DateTime();
                                $today->setTime(0, 0, 0); // Imposta l'ora a 00:00:00
                                
                                if (!$scadenza['completata'] && $scadenza['stato'] !== 'annullata'): 
                                    if ($dataScadenza < $today): 
                            ?>
                                <span class="badge badge-danger">Scaduta</span>
                            <?php 
                                    elseif ($dataScadenza == $today): 
                            ?>
                                <span class="badge badge-warning">Scade oggi</span>
                            <?php 
                                    endif;
                                endif; 
                            ?>
                        </td>
                    </tr>
                    <?php if (!empty($scadenza['data_promemoria'])): ?>
                    <tr>
                        <th>Data Promemoria</th>
                        <td><?= date('d/m/Y', strtotime($scadenza['data_promemoria'])) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th>Assegnata a</th>
                        <td>
                            <?= esc($scadenza['nome_assegnato'] ?? '') ?> <?= esc($scadenza['cognome_assegnato'] ?? '') ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Creata da</th>
                        <td>
                            <?= esc($scadenza['nome_creatore'] ?? '') ?> <?= esc($scadenza['cognome_creatore'] ?? '') ?>
                        </td>
                    </tr>
                    <?php if ($scadenza['completata']): ?>
                    <tr>
                        <th>Completata il</th>
                        <td><?= date('d/m/Y H:i', strtotime($scadenza['completata_il'])) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th>Creata il</th>
                        <td><?= date('d/m/Y H:i', strtotime($scadenza['created_at'])) ?></td>
                    </tr>
                    <tr>
                        <th>Ultima modifica</th>
                        <td><?= date('d/m/Y H:i', strtotime($scadenza['updated_at'])) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Conferma eliminazione
        $('.btn-delete').click(function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var url = $(this).attr('href');
            
            Swal.fire({
                title: 'Sei sicuro?',
                text: "Questa operazione non può essere annullata!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sì, elimina!',
                cancelButtonText: 'Annulla'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });

    function confermaEliminazione(id) {
        Swal.fire({
            title: 'Sei sicuro?',
            text: "Questa operazione non può essere annullata!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sì, elimina!',
            cancelButtonText: 'Annulla'
        }).then((result) => {
            if (result.isConfirmed) {
                // Crea e invia un form con il token CSRF
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url('scadenze/elimina') ?>/' + id;
                
                // Aggiungi token CSRF
                var csrfField = document.createElement('input');
                csrfField.type = 'hidden';
                csrfField.name = '<?= csrf_token() ?>';
                csrfField.value = '<?= csrf_hash() ?>';
                form.appendChild(csrfField);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
<?= $this->endSection() ?> 