<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $titolo ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= $titolo ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Attività</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Elenco attività</h3>
                        <div class="card-tools">
                            <?php if ($is_admin): ?>
                            <a href="<?= site_url('attivita/new') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Nuova Attività
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filtri -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default" data-filter="all">Tutte</button>
                                    <button type="button" class="btn btn-info" data-filter="in_corso">In corso</button>
                                    <button type="button" class="btn btn-warning" data-filter="da_iniziare">Da iniziare</button>
                                    <button type="button" class="btn btn-success" data-filter="completata">Completate</button>
                                    <button type="button" class="btn btn-danger" data-filter="in_ritardo">In ritardo</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="tabella-attivita">
                                <thead>
                                    <tr>
                                        <th width="50">ID</th>
                                        <th>Titolo</th>
                                        <th>Progetto</th>
                                        <th>Assegnata a</th>
                                        <th>Priorità</th>
                                        <th>Stato</th>
                                        <th>Scadenza</th>
                                        <th width="80">Sottoattività</th>
                                        <th width="120">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($attivita)): ?>
                                        <tr>
                                            <td colspan="9" class="text-center">Nessuna attività trovata</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($attivita as $task): ?>
                                        <tr>
                                            <td><?= $task['id'] ?></td>
                                            <td><?= esc($task['titolo']) ?></td>
                                            <td><?= esc($task['nome_progetto']) ?></td>
                                            <td>
                                                <?php if (isset($task['nome_assegnato'])): ?>
                                                    <?= esc($task['nome_assegnato'] . ' ' . $task['cognome_assegnato']) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Non assegnato</span>
                                                <?php endif; ?>
                                            </td>
                                            <td data-priorita="<?= $task['priorita'] ?>">
                                                <?php 
                                                $prioritaClass = 'secondary';
                                                switch ($task['priorita']) {
                                                    case 'alta':
                                                        $prioritaClass = 'danger';
                                                        break;
                                                    case 'media':
                                                        $prioritaClass = 'warning';
                                                        break;
                                                    case 'bassa':
                                                        $prioritaClass = 'info';
                                                        break;
                                                    case 'urgente':
                                                        $prioritaClass = 'danger';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge badge-<?= $prioritaClass ?>">
                                                    <?= ucfirst(esc($task['priorita'])) ?>
                                                </span>
                                            </td>
                                            <td data-stato="<?= $task['stato'] ?>">
                                                <?php 
                                                $statoClass = 'secondary';
                                                switch ($task['stato']) {
                                                    case 'da_iniziare':
                                                        $statoClass = 'warning';
                                                        break;
                                                    case 'in_corso':
                                                        $statoClass = 'info';
                                                        break;
                                                    case 'in_pausa':
                                                        $statoClass = 'secondary';
                                                        break;
                                                    case 'completata':
                                                        $statoClass = 'success';
                                                        break;
                                                    case 'annullata':
                                                        $statoClass = 'danger';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge badge-<?= $statoClass ?>">
                                                    <?= str_replace('_', ' ', ucfirst(esc($task['stato']))) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($task['data_scadenza'])): ?>
                                                    <?= date('d/m/Y', strtotime($task['data_scadenza'])) ?>
                                                    <?php 
                                                    // Evidenzia le attività in ritardo
                                                    if ($task['stato'] !== 'completata' && $task['stato'] !== 'annullata' && 
                                                        strtotime($task['data_scadenza']) < strtotime('today')) {
                                                        echo '<span class="badge badge-danger">In ritardo</span>';
                                                    }
                                                    ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Nessuna</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (isset($task['sotto_attivita_completate']) && isset($task['sotto_attivita_totali'])): ?>
                                                    <?php if ($task['sotto_attivita_totali'] > 0): ?>
                                                        <a href="<?= site_url('attivita/view/' . $task['id']) ?>#sottoattivita" class="badge badge-info">
                                                            <?= $task['sotto_attivita_completate'] ?>/<?= $task['sotto_attivita_totali'] ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">0/0</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?= site_url('attivita/view/' . $task['id']) ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    <?php if ($is_admin || $task['id_utente_creatore'] == session()->get('utente_id')): ?>
                                                    <a href="<?= site_url('attivita/edit/' . $task['id']) ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <a href="<?= site_url('attivita/delete/' . $task['id']) ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Sei sicuro di voler eliminare questa attività?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    <?php endif; ?>
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
</section>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>

<script>
$(function() {
    // Inizializza DataTables
    var table = $('#tabella-attivita').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "language": {
            "url": "<?= base_url('plugins/datatables/Italian.json') ?>"
        },
        "columnDefs": [
            {
                "targets": [4, 5, 6], // Colonne con badge
                "orderable": false
            },
            {
                "targets": [7], // Colonna azioni
                "orderable": false,
                "searchable": false
            }
        ]
    });
    
    // Gestione filtri
    $('.btn-group button[data-filter]').click(function() {
        var filterValue = $(this).data('filter');
        
        // Evidenzia il pulsante attivo
        $(this).siblings().removeClass('active');
        $(this).addClass('active');
        
        // Resetta la ricerca
        table.search('').columns().search('').draw();
        
        // Applica il filtro appropriato
        if (filterValue === 'all') {
            // Mostra tutte le righe
            table.column(5).search('').draw();
        } else if (filterValue === 'in_ritardo') {
            // Filtra per attività in ritardo
            table.column(6).search('In ritardo').draw();
        } else {
            // Usa una funzione di ricerca personalizzata per filtrare per stato
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var row = table.row(dataIndex).node();
                    var statoCell = $(row).find('td[data-stato]');
                    var stato = statoCell.attr('data-stato');
                    
                    if (stato === filterValue) {
                        return true;
                    }
                    return false;
                }
            );
            
            // Applica il filtro e poi rimuovi la funzione personalizzata
            table.draw();
            $.fn.dataTable.ext.search.pop();
        }
    });
});
</script>
<?= $this->endSection() ?> 