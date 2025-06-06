<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item active">Pickup & Delivery</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Statistiche -->
    <?php if (isset($statistiche)): ?>
    <div class="row mb-3">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?= $statistiche['totale'] ?></h3>
                    <p>Totale Operazioni</p>
                </div>
                <div class="icon">
                    <i class="fas fa-truck"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?= $statistiche['programmata'] + $statistiche['in_corso'] ?></h3>
                    <p>In Corso</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?= $statistiche['completata'] ?></h3>
                    <p>Completate</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3><?= $statistiche['in_scadenza'] ?></h3>
                    <p>In Scadenza</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <?= isset($tipo) ? ucfirst($tipo) : 'Tutte le Operazioni' ?>
                        </h3>
                        <div>
                            <div class="btn-group ml-2">
                                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-filter"></i> Filtri
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="<?= base_url('pickup-delivery') ?>">
                                        <i class="fas fa-list"></i> Tutte
                                    </a>
                                    <a class="dropdown-item" href="<?= base_url('pickup-delivery/ritiri') ?>">
                                        <i class="fas fa-truck-loading"></i> Solo Ritiri
                                    </a>
                                    <a class="dropdown-item" href="<?= base_url('pickup-delivery/consegne') ?>">
                                        <i class="fas fa-truck"></i> Solo Consegne
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="<?= base_url('pickup-delivery/calendario') ?>">
                                        <i class="fas fa-calendar"></i> Vista Calendario
                                    </a>
                                </div>
                            </div>
                            <div class="btn-group ml-2">
                                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-print"></i> Stampa
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="<?= base_url('pickup-delivery/stampa-lista?data_inizio=' . date('Y-m-d') . '&data_fine=' . date('Y-m-d')) ?>" target="_blank">
                                        <i class="fas fa-calendar-day"></i> Lista Oggi
                                    </a>
                                    <a class="dropdown-item" href="<?= base_url('pickup-delivery/stampa-lista?data_inizio=' . date('Y-m-d', strtotime('monday this week')) . '&data_fine=' . date('Y-m-d', strtotime('sunday this week'))) ?>" target="_blank">
                                        <i class="fas fa-calendar-week"></i> Lista Settimana
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#" onclick="showPrintModal()">
                                        <i class="fas fa-filter"></i> Lista Personalizzata
                                    </a>
                                </div>
                            </div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary" data-toggle="offcanvas" data-target="#offcanvasNuovaOperazione">
                                    <i class="fas fa-plus"></i> Nuova Operazione
                                </button>
                                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" data-toggle="offcanvas" data-target="#offcanvasNuovaOperazione" data-tipo="ritiro">
                                        <i class="fas fa-truck-loading"></i> Nuovo Ritiro
                                    </a>
                                    <a class="dropdown-item" href="#" data-toggle="offcanvas" data-target="#offcanvasNuovaOperazione" data-tipo="consegna">
                                        <i class="fas fa-truck"></i> Nuova Consegna
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="<?= base_url('pickup-delivery/new') ?>">
                                        <i class="fas fa-external-link-alt"></i> Form Completo
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('message')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= session()->getFlashdata('message') ?>
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="table-pickup-delivery">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Titolo</th>
                                    <th width="80">Tipo</th>
                                    <th>Anagrafica</th>
                                    <th>Data Programmata</th>
                                    <th>Indirizzo</th>
                                    <th width="80">Priorità</th>
                                    <th width="100">Stato</th>
                                    <th>Assegnato a</th>
                                    <th width="120">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($operazioni)): ?>
                                    <tr>
                                        <td colspan="10" class="text-center">Nessuna operazione disponibile</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($operazioni as $operazione): ?>
                                        <tr>
                                            <td><?= $operazione['id'] ?></td>
                                            <td>
                                                <strong><?= esc($operazione['titolo']) ?></strong>
                                                <?php if (!empty($operazione['titolo_attivita'])): ?>
                                                    <br><small class="text-muted">
                                                        <i class="fas fa-tasks"></i> <?= esc($operazione['titolo_attivita']) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($operazione['tipo'] === 'ritiro'): ?>
                                                    <span class="badge badge-info">
                                                        <i class="fas fa-truck-loading"></i> Ritiro
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-primary">
                                                        <i class="fas fa-truck"></i> Consegna
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc($operazione['ragione_sociale']) ?></td>
                                            <td>
                                                <?= date('d/m/Y H:i', strtotime($operazione['data_programmata'])) ?>
                                                <?php if (!empty($operazione['orario_preferito'])): ?>
                                                    <br><small class="text-muted"><?= esc($operazione['orario_preferito']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= esc($operazione['indirizzo']) ?>
                                                <?php if (!empty($operazione['citta'])): ?>
                                                    <br><small class="text-muted"><?= esc($operazione['citta']) ?></small>
                                                <?php endif; ?>
                                            </td>
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
                                                <span class="badge badge-<?= $priorityClass ?>">
                                                    <?= ucfirst($operazione['priorita']) ?>
                                                </span>
                                            </td>
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
                                                <select class="form-control form-control-sm stato-select" data-id="<?= $operazione['id'] ?>">
                                                    <option value="programmata" <?= $operazione['stato'] === 'programmata' ? 'selected' : '' ?>>Programmata</option>
                                                    <option value="in_corso" <?= $operazione['stato'] === 'in_corso' ? 'selected' : '' ?>>In Corso</option>
                                                    <option value="completata" <?= $operazione['stato'] === 'completata' ? 'selected' : '' ?>>Completata</option>
                                                    <option value="annullata" <?= $operazione['stato'] === 'annullata' ? 'selected' : '' ?>>Annullata</option>
                                                </select>
                                            </td>
                                            <td>
                                                <?php if (!empty($operazione['nome_utente_assegnato'])): ?>
                                                    <?= esc($operazione['nome_utente_assegnato'] . ' ' . $operazione['cognome_utente_assegnato']) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Non assegnato</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?= base_url('pickup-delivery/show/' . $operazione['id']) ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= base_url('pickup-delivery/edit/' . $operazione['id']) ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?= base_url('pickup-delivery/stampa/' . $operazione['id']) ?>" class="btn btn-sm btn-secondary" target="_blank" title="Stampa Promemoria">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="<?= $operazione['id'] ?>" data-title="<?= esc($operazione['titolo']) ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
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

<!-- Offcanvas per nuova operazione -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNuovaOperazione" aria-labelledby="offcanvasNuovaOperazioneLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasNuovaOperazioneLabel">
            <i class="fas fa-truck mr-2"></i> Nuova Operazione
        </h5>
        <button type="button" class="btn-close" data-dismiss="offcanvas" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="offcanvas-body">
        <form id="form-nuova-operazione" action="<?= base_url('pickup-delivery/create') ?>" method="post">
            <?= csrf_field() ?>
            
            <!-- Campo nascosto per utente creatore -->
            <input type="hidden" name="id_utente_creatore" value="<?= session('utente_id') ?>">
            
            <!-- Informazioni base -->
            <div class="mb-3">
                <label for="offcanvas_titolo" class="form-label">Titolo *</label>
                <input type="text" class="form-control" id="offcanvas_titolo" name="titolo" required>
            </div>
            
            <div class="row mb-3">
                <div class="col-6">
                    <label for="offcanvas_tipo" class="form-label">Tipo *</label>
                    <select class="form-control" id="offcanvas_tipo" name="tipo" required>
                        <option value="">Seleziona...</option>
                        <option value="ritiro">Ritiro</option>
                        <option value="consegna">Consegna</option>
                    </select>
                </div>
                <div class="col-6">
                    <label for="offcanvas_priorita" class="form-label">Priorità</label>
                    <select class="form-control" id="offcanvas_priorita" name="priorita">
                        <option value="bassa">Bassa</option>
                        <option value="normale" selected>Normale</option>
                        <option value="alta">Alta</option>
                        <option value="urgente">Urgente</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="offcanvas_stato" class="form-label">Stato</label>
                <select class="form-control" id="offcanvas_stato" name="stato">
                    <option value="programmata" selected>Programmata</option>
                    <option value="in_corso">In Corso</option>
                    <option value="completata">Completata</option>
                    <option value="annullata">Annullata</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="offcanvas_id_anagrafica" class="form-label">Anagrafica *</label>
                <select class="form-control select2-offcanvas" id="offcanvas_id_anagrafica" name="id_anagrafica" required>
                    <option value="">Seleziona anagrafica...</option>
                    <!-- Le opzioni verranno caricate via AJAX -->
                </select>
            </div>
            
            <div class="mb-3">
                <label for="offcanvas_data_programmata" class="form-label">Data Programmata *</label>
                <input type="datetime-local" 
                       class="form-control" 
                       id="offcanvas_data_programmata" 
                       name="data_programmata" 
                       lang="it-IT"
                       required>
            </div>
            
            <div class="mb-3">
                <label for="offcanvas_indirizzo" class="form-label">Indirizzo *</label>
                <textarea class="form-control" id="offcanvas_indirizzo" name="indirizzo" rows="2" required></textarea>
            </div>
            
            <div class="row mb-3">
                <div class="col-8">
                    <label for="offcanvas_citta" class="form-label">Città</label>
                    <input type="text" class="form-control" id="offcanvas_citta" name="citta">
                </div>
                <div class="col-4">
                    <label for="offcanvas_cap" class="form-label">CAP</label>
                    <input type="text" class="form-control" id="offcanvas_cap" name="cap">
                </div>
            </div>
            
            <div class="mb-3">
                <label for="offcanvas_id_utente_assegnato" class="form-label">Assegnato a</label>
                <select class="form-control select2-offcanvas" id="offcanvas_id_utente_assegnato" name="id_utente_assegnato">
                    <option value="">Seleziona utente...</option>
                    <!-- Le opzioni verranno caricate via AJAX -->
                </select>
            </div>
            
            <div class="mb-3">
                <label for="offcanvas_descrizione" class="form-label">Descrizione</label>
                <textarea class="form-control" id="offcanvas_descrizione" name="descrizione" rows="3"></textarea>
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="offcanvas_richiesta_ddt" name="richiesta_ddt" value="1">
                    <label class="form-check-label" for="offcanvas_richiesta_ddt">
                        Richiesta DDT
                    </label>
                </div>
            </div>
            
            <input type="hidden" name="nazione" value="Italia">
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salva Operazione
                </button>
                <a href="<?= base_url('pickup-delivery/new') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-external-link-alt"></i> Apri Form Completo
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Modal di conferma eliminazione -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Conferma eliminazione</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Sei sicuro di voler eliminare l'operazione <strong id="delete-name"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <a href="#" id="btn-confirm-delete" class="btn btn-danger">Elimina</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal per stampa personalizzata -->
<div class="modal fade" id="printModal" tabindex="-1" role="dialog" aria-labelledby="printModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printModalLabel">Stampa Lista Personalizzata</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="printForm" method="get" action="<?= base_url('pickup-delivery/stampa-lista') ?>" target="_blank">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="print_data_inizio">Data Inizio *</label>
                                <input type="date" class="form-control" id="print_data_inizio" name="data_inizio" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="print_data_fine">Data Fine *</label>
                                <input type="date" class="form-control" id="print_data_fine" name="data_fine" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="print_tipo">Tipo</label>
                                <select class="form-control" id="print_tipo" name="tipo">
                                    <option value="tutti">Tutti</option>
                                    <option value="ritiro">Solo Ritiri</option>
                                    <option value="consegna">Solo Consegne</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="print_stato">Stato</label>
                                <select class="form-control" id="print_stato" name="stato">
                                    <option value="tutti">Tutti</option>
                                    <option value="programmata">Programmata</option>
                                    <option value="in_corso">In Corso</option>
                                    <option value="completata">Completata</option>
                                    <option value="annullata">Annullata</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="print_utente">Utente Assegnato</label>
                        <select class="form-control" id="print_utente" name="utente">
                            <option value="tutti">Tutti</option>
                            <?php if (isset($utenti)): ?>
                                <?php foreach ($utenti as $utente): ?>
                                    <option value="<?= $utente['id'] ?>"><?= esc($utente['nome'] . ' ' . $utente['cognome']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-print"></i> Stampa Lista
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugins/select2/css/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">
<style>
.stato-select {
    min-width: 120px;
}
.small-box .inner h3 {
    font-size: 2.2rem;
}

/* Proteggi i campi datetime-local dall'interferenza dei plugin datepicker */
input[type="datetime-local"].no-datepicker {
    background-image: none !important;
    padding-right: 12px !important;
}

input[type="datetime-local"]::-webkit-calendar-picker-indicator {
    opacity: 1;
    cursor: pointer;
}

/* Stili per offcanvas (compatibilità Bootstrap 4) */
.offcanvas {
    position: fixed;
    bottom: 0;
    z-index: 1045;
    display: flex;
    flex-direction: column;
    max-width: 100%;
    visibility: hidden;
    background-color: #fff;
    background-clip: padding-box;
    outline: 0;
    transition: transform 0.3s ease-in-out;
}

.offcanvas-end {
    top: 0;
    right: 0;
    width: 400px;
    border-left: 1px solid rgba(0, 0, 0, 0.2);
    transform: translateX(100%);
}

.offcanvas.show {
    transform: none;
    visibility: visible;
}

.offcanvas-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1rem;
    border-bottom: 1px solid #dee2e6;
}

.offcanvas-title {
    margin-bottom: 0;
    line-height: 1.5;
}

.offcanvas-body {
    flex-grow: 1;
    padding: 1rem 1rem;
    overflow-y: auto;
}

.btn-close {
    background: none;
    border: 0;
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    color: #000;
    text-shadow: 0 1px 0 #fff;
    opacity: 0.5;
    cursor: pointer;
}

.btn-close:hover {
    opacity: 0.75;
}

.offcanvas-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1040;
    width: 100vw;
    height: 100vh;
    background-color: #000;
    opacity: 0.5;
}

@media (max-width: 576px) {
    .offcanvas-end {
        width: 100%;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('plugins/select2/js/select2.full.min.js') ?>"></script>

<script>
    $(function() {
        // Proteggi i campi datetime-local dall'interferenza dei plugin datepicker
        $('input[type="datetime-local"]').each(function() {
            $(this).addClass('no-datepicker');
            // Rimuovi eventuali inizializzazioni datepicker
            if ($(this).data('datepicker')) {
                $(this).datepicker('destroy');
            }
            
            // Forza il locale italiano
            $(this).attr('lang', 'it-IT');
            
            // Aggiungi un placeholder per chiarire il formato
            if (!$(this).attr('placeholder')) {
                $(this).attr('placeholder', 'gg/mm/aaaa, hh:mm');
            }
        });
        
        // Inizializzazione DataTable
        $('#table-pickup-delivery').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "order": [[4, "asc"]], // Ordina per data programmata
            "language": {
                "url": "<?= base_url('plugins/datatables/Italian.json') ?>"
            },
            "columnDefs": [
                {
                    "targets": [-1], // Ultima colonna (azioni)
                    "orderable": false,
                    "searchable": false
                }
            ]
        });

        // Gestione cambio stato
        $('.stato-select').on('change', function() {
            const id = $(this).data('id');
            const nuovoStato = $(this).val();
            const $select = $(this);
            
            $.ajax({
                url: `<?= base_url('pickup-delivery/cambiaStato') ?>/${id}`,
                method: 'POST',
                data: {
                    stato: nuovoStato,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        // Mostra messaggio di successo
                        const alert = $('<div class="alert alert-success alert-dismissible fade show">' +
                            response.message +
                            '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>' +
                            '</div>');
                        $('.card-body').prepend(alert);
                        
                        // Rimuovi l'alert dopo 3 secondi
                        setTimeout(() => {
                            alert.alert('close');
                        }, 3000);
                    } else {
                        alert('Errore: ' + response.message);
                        // Ripristina il valore precedente
                        location.reload();
                    }
                },
                error: function() {
                    alert('Errore durante l\'aggiornamento dello stato');
                    location.reload();
                }
            });
        });

        // Gestione eliminazione
        $('.btn-delete').on('click', function() {
            const id = $(this).data('id');
            const name = $(this).data('title');

            $('#delete-name').text(name);
            $('#btn-confirm-delete').attr('href', `<?= base_url('pickup-delivery/delete') ?>/${id}`);
            $('#deleteModal').modal('show');
        });

        // === GESTIONE OFFCANVAS ===
        
        // Funzione per aprire l'offcanvas
        function openOffcanvas(target) {
            const $offcanvas = $(target);
            const $backdrop = $('<div class="offcanvas-backdrop"></div>');
            
            $('body').append($backdrop);
            $offcanvas.addClass('show');
            $('body').addClass('offcanvas-open');
            
            // Gestione chiusura con backdrop
            $backdrop.on('click', function() {
                closeOffcanvas(target);
            });
        }
        
        // Funzione per chiudere l'offcanvas
        function closeOffcanvas(target) {
            const $offcanvas = $(target);
            
            $offcanvas.removeClass('show');
            $('.offcanvas-backdrop').remove();
            $('body').removeClass('offcanvas-open');
            
            // Reset form
            $offcanvas.find('form')[0].reset();
            $offcanvas.find('.is-invalid').removeClass('is-invalid');
        }
        
        // Gestione apertura offcanvas
        $('[data-toggle="offcanvas"]').on('click', function(e) {
            e.preventDefault();
            const target = $(this).data('target');
            const tipo = $(this).data('tipo');
            
            openOffcanvas(target);
            
            // Pre-imposta il tipo se specificato
            if (tipo) {
                $('#offcanvas_tipo').val(tipo);
            }
            
            // Carica dati se non già caricati
            loadOffcanvasData();
        });
        
        // Gestione chiusura offcanvas
        $('[data-dismiss="offcanvas"]').on('click', function() {
            const target = $(this).closest('.offcanvas').attr('id');
            closeOffcanvas('#' + target);
        });
        
        // Chiusura con ESC
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $('.offcanvas.show').length) {
                closeOffcanvas('.offcanvas.show');
            }
        });
        
        // Caricamento dati per offcanvas
        function loadOffcanvasData() {
            // Carica anagrafiche
            if ($('#offcanvas_id_anagrafica option').length <= 1) {
                $.ajax({
                    url: '<?= base_url('api/anagrafiche/getAnagrafiche') ?>',
                    method: 'GET',
                    success: function(response) {
                        const $select = $('#offcanvas_id_anagrafica');
                        // La risposta ha la struttura {success: true, data: [array]}
                        if (response.success && response.data) {
                            response.data.forEach(function(anagrafica) {
                                const displayText = anagrafica.ragione_sociale + (anagrafica.citta ? ' - ' + anagrafica.citta : '');
                                $select.append(`<option value="${anagrafica.id}">${displayText}</option>`);
                            });
                        }
                    },
                    error: function() {
                        console.log('Errore nel caricamento anagrafiche');
                    }
                });
            }
            
            // Carica utenti
            if ($('#offcanvas_id_utente_assegnato option').length <= 1) {
                $.ajax({
                    url: '<?= base_url('api/utenti/getUtenti') ?>',
                    method: 'GET',
                    success: function(utenti) {
                        const $select = $('#offcanvas_id_utente_assegnato');
                        // L'API utenti restituisce direttamente l'array
                        if (Array.isArray(utenti)) {
                            utenti.forEach(function(utente) {
                                $select.append(`<option value="${utente.id}">${utente.nome} ${utente.cognome}</option>`);
                            });
                        }
                    },
                    error: function() {
                        console.log('Errore nel caricamento utenti');
                    }
                });
            }
        }
        
        // Inizializzazione Select2 per offcanvas
        $('.select2-offcanvas').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#offcanvasNuovaOperazione'),
            width: '100%'
        });
        
        // Gestione submit form offcanvas
        $('#form-nuova-operazione').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $submitBtn = $form.find('button[type="submit"]');
            
            // Disabilita il pulsante
            $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Salvando...');
            
            // Rimuovi errori precedenti
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.invalid-feedback').remove();
            
            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function(response) {
                    // Chiudi offcanvas
                    closeOffcanvas('#offcanvasNuovaOperazione');
                    
                    // Mostra messaggio di successo e ricarica pagina
                    const alert = $('<div class="alert alert-success alert-dismissible fade show">' +
                        'Operazione creata con successo!' +
                        '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>' +
                        '</div>');
                    $('.card-body').prepend(alert);
                    
                    // Ricarica la tabella
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        // Errori di validazione
                        const errors = xhr.responseJSON.errors || {};
                        
                        Object.keys(errors).forEach(function(field) {
                            const $field = $form.find(`[name="${field}"]`);
                            $field.addClass('is-invalid');
                            $field.after(`<div class="invalid-feedback">${errors[field]}</div>`);
                        });
                    } else {
                        alert('Errore durante il salvataggio. Riprova.');
                    }
                },
                complete: function() {
                    // Riabilita il pulsante
                    $submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Salva Operazione');
                }
            });
        });
        
        // Imposta data/ora corrente + 1 ora come default
        const now = new Date();
        now.setHours(now.getHours() + 1);
        const defaultDateTime = now.toISOString().slice(0, 16);
        $('#offcanvas_data_programmata').val(defaultDateTime);
    });

    // Funzione per mostrare il modal di stampa
    function showPrintModal() {
        $('#printModal').modal('show');
    }
</script>
<?= $this->endSection() ?> 