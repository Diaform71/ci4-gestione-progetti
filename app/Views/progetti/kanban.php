<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Progetti - Kanban<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Progetti - Vista Kanban<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('progetti') ?>">Progetti</a></li>
<li class="breadcrumb-item active">Kanban</li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .kanban-board {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 15px;
    }
    
    .kanban-column {
        min-width: 250px;
        max-width: 250px;
        margin-right: 15px;
        background-color: #f4f6f9;
        border-radius: 5px;
    }
    
    .kanban-column-header {
        padding: 10px;
        border-radius: 5px 5px 0 0;
        color: white;
        font-weight: bold;
    }
    
    .kanban-column-content {
        min-height: 200px;
        padding: 10px;
    }
    
    .kanban-card {
        margin-bottom: 10px;
        cursor: grab;
        transition: all 0.3s;
    }
    
    .kanban-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .priority-bassa {
        border-left: 4px solid #28a745;
    }
    
    .priority-media {
        border-left: 4px solid #17a2b8;
    }
    
    .priority-alta {
        border-left: 4px solid #ffc107;
    }
    
    .priority-critica {
        border-left: 4px solid #dc3545;
    }
    
    .kanban-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 text-right">
            <a href="<?= base_url('progetti/new') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuovo Progetto
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="kanban-board">
                <?php foreach ($fasiKanban as $fase) : ?>
                    <?php
                    $columnColor = '';
                    $columnTitle = '';
                    
                    switch ($fase) {
                        case 'backlog':
                            $columnColor = 'bg-secondary';
                            $columnTitle = 'Backlog';
                            break;
                        case 'da_iniziare':
                            $columnColor = 'bg-info';
                            $columnTitle = 'Da Iniziare';
                            break;
                        case 'in_corso':
                            $columnColor = 'bg-primary';
                            $columnTitle = 'In Corso';
                            break;
                        case 'in_revisione':
                            $columnColor = 'bg-warning';
                            $columnTitle = 'In Revisione';
                            break;
                        case 'completato':
                            $columnColor = 'bg-success';
                            $columnTitle = 'Completato';
                            break;
                        default:
                            $columnColor = 'bg-secondary';
                            $columnTitle = ucfirst($fase);
                    }
                    ?>
                    
                    <div class="kanban-column" data-fase="<?= $fase ?>">
                        <div class="kanban-column-header <?= $columnColor ?>">
                            <?= $columnTitle ?> 
                            <span class="badge badge-light float-right"><?= count($progetti[$fase] ?? []) ?></span>
                        </div>
                        <div class="kanban-column-content">
                            <?php if (empty($progetti[$fase])) : ?>
                                <div class="text-center text-muted p-3">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p>Nessun progetto</p>
                                </div>
                            <?php else : ?>
                                <?php foreach ($progetti[$fase] as $progetto) : ?>
                                    <div class="card kanban-card priority-<?= $progetto['priorita'] ?>" data-id="<?= $progetto['id'] ?>">
                                        <div class="card-body p-2">
                                            <h5 class="card-title">
                                                <a href="<?= base_url('progetti/' . $progetto['id']) ?>">
                                                    <?= esc($progetto['nome']) ?>
                                                </a>
                                            </h5>
                                            
                                            <?php if (!empty($progetto['id_anagrafica'])) : ?>
                                                <?php
                                                $anagraficaModel = new \App\Models\AnagraficaModel();
                                                $anagrafica = $anagraficaModel->find($progetto['id_anagrafica']);
                                                ?>
                                                <p class="card-text text-muted mb-2">
                                                    <small>
                                                        <i class="fas fa-building"></i> 
                                                        <?= esc($anagrafica['ragione_sociale'] ?? 'N/D') ?>
                                                    </small>
                                                </p>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($progetto['id_responsabile'])) : ?>
                                                <?php
                                                $utentiModel = new \App\Models\UtentiModel();
                                                $responsabile = $utentiModel->find($progetto['id_responsabile']);
                                                ?>
                                                <p class="card-text text-muted mb-2">
                                                    <small>
                                                        <i class="fas fa-user"></i> 
                                                        <?= esc($responsabile['nome'] ?? '') ?> <?= esc($responsabile['cognome'] ?? '') ?>
                                                    </small>
                                                </p>
                                            <?php endif; ?>
                                            
                                            <div class="kanban-card-footer">
                                                <?php if (!empty($progetto['data_scadenza'])) : ?>
                                                    <?php
                                                    $scadenza = new DateTime($progetto['data_scadenza']);
                                                    $oggi = new DateTime();
                                                    $isExpired = $oggi > $scadenza;
                                                    $badgeClass = $isExpired ? 'badge-danger' : 'badge-success';
                                                    
                                                    if (!$isExpired && $oggi->diff($scadenza)->days <= 7) {
                                                        $badgeClass = 'badge-warning';
                                                    }
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>">
                                                        <i class="far fa-calendar-alt"></i> 
                                                        <?= date('d/m/Y', strtotime($progetto['data_scadenza'])) ?>
                                                    </span>
                                                <?php else : ?>
                                                    <span></span>
                                                <?php endif; ?>
                                                
                                                <span class="badge badge-light">
                                                    <?php
                                                    $prioritaIcon = '';
                                                    switch ($progetto['priorita']) {
                                                        case 'bassa':
                                                            $prioritaIcon = '<i class="fas fa-arrow-down text-success"></i>';
                                                            break;
                                                        case 'media':
                                                            $prioritaIcon = '<i class="fas fa-equals text-info"></i>';
                                                            break;
                                                        case 'alta':
                                                            $prioritaIcon = '<i class="fas fa-arrow-up text-warning"></i>';
                                                            break;
                                                        case 'critica':
                                                            $prioritaIcon = '<i class="fas fa-exclamation text-danger"></i>';
                                                            break;
                                                    }
                                                    echo $prioritaIcon . ' ' . ucfirst($progetto['priorita']);
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    $(document).ready(function() {
        // Inizializza Sortable.js per ogni colonna
        document.querySelectorAll('.kanban-column-content').forEach(column => {
            new Sortable(column, {
                group: 'projects',
                animation: 150,
                ghostClass: 'bg-light',
                onEnd: function (evt) {
                    // Ottieni il progetto e la nuova fase
                    const progettoId = evt.item.dataset.id;
                    const nuovaFase = evt.to.parentNode.dataset.fase;
                    
                    // Aggiorna la fase del progetto tramite AJAX
                    $.post({
                        url: '<?= base_url('progetti/fase-kanban/') ?>' + progettoId,
                        data: {
                            fase_kanban: nuovaFase,
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                        },
                        success: function(response) {
                            // Aggiorna il contatore della colonna
                            updateColumnCounter();
                            
                            // Mostra notifica
                            showNotification('Successo', 'Progetto spostato con successo', 'success');
                        },
                        error: function() {
                            // Ripristina posizione in caso di errore
                            showNotification('Errore', 'Si è verificato un errore durante lo spostamento', 'error');
                            
                            // Ricarica la pagina per ripristinare lo stato
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        }
                    });
                }
            });
        });
        
        // Funzione per aggiornare i contatori delle colonne
        function updateColumnCounter() {
            document.querySelectorAll('.kanban-column').forEach(column => {
                const counter = column.querySelector('.badge');
                const cards = column.querySelectorAll('.kanban-card').length;
                counter.textContent = cards;
            });
        }
    });
</script>
<?= $this->endSection() ?> 