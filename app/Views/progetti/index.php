<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Progetti<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Progetti<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item active">Progetti</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h3 class="card-title">Elenco Progetti</h3>
                        </div>
                        <div class="col-md-6 text-right d-flex justify-content-end">
                            <div class="btn-group mr-2">
                                <a href="<?= base_url('progetti?mostra_disattivati=0') ?>" class="btn btn-<?= $mostraDisattivati === '0' ? 'primary' : 'outline-primary' ?>">
                                    <i class="fas fa-eye"></i> Solo Attivi
                                </a>
                                <a href="<?= base_url('progetti?mostra_disattivati=1') ?>" class="btn btn-<?= $mostraDisattivati === '1' ? 'primary' : 'outline-primary' ?>">
                                    <i class="fas fa-eye-slash"></i> Visualizza Tutti
                                </a>
                            </div>
                            <a href="<?= base_url('progetti/new') ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Nuovo Progetto
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 25%">Nome</th>
                                    <th style="width: 15%">Cliente</th>
                                    <th style="width: 10%">Progetto Padre</th>
                                    <th style="width: 10%">Fase Kanban</th>
                                    <th style="width: 10%">Stato</th>
                                    <th style="width: 10%">Scadenza</th>
                                    <th style="width: 10%">Priorità</th>
                                    <th style="width: 15%">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($progetti)) : ?>
                                    <tr>
                                        <td colspan="9" class="text-center">Nessun progetto trovato</td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($progetti as $progetto) : ?>
                                        <tr <?= $progetto['attivo'] ? '' : ' class="table-secondary text-muted"' ?>>
                                            <td><?= $progetto['id'] ?></td>
                                            <td>
                                                <?= esc($progetto['nome']) ?>
                                                <?php if (!$progetto['attivo']) : ?>
                                                    <span class="badge badge-danger">Disattivato</span>
                                                <?php endif; ?>
                                                <?php 
                                                $progettoModel = new \App\Models\ProgettoModel();
                                                if ($progettoModel->hasSottoprogetti($progetto['id'])) : ?>
                                                    <span class="badge badge-info">Ha sottoprogetti</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                if (!empty($progetto['id_anagrafica'])) {
                                                    $anagraficaModel = new \App\Models\AnagraficaModel();
                                                    $anagrafica = $anagraficaModel->find($progetto['id_anagrafica']);
                                                    echo esc($anagrafica['ragione_sociale'] ?? 'N/D');
                                                } else {
                                                    echo 'N/D';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if (!empty($progetto['id_progetto_padre'])) {
                                                    $progettoPadre = $progettoModel->find($progetto['id_progetto_padre']);
                                                    if ($progettoPadre) {
                                                        echo '<a href="' . base_url('progetti/' . $progettoPadre['id']) . '" class="badge badge-primary">';
                                                        echo esc($progettoPadre['nome']);
                                                        echo '</a>';
                                                    }
                                                } else {
                                                    echo '<span class="badge badge-secondary">Progetto Principale</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?= esc(ucfirst($progetto['fase_kanban'])) ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                $statoClass = '';
                                                switch ($progetto['stato']) {
                                                    case 'in_corso':
                                                        $statoClass = 'bg-primary';
                                                        $statoText = 'In corso';
                                                        break;
                                                    case 'completato':
                                                        $statoClass = 'bg-success';
                                                        $statoText = 'Completato';
                                                        break;
                                                    case 'sospeso':
                                                        $statoClass = 'bg-warning';
                                                        $statoText = 'Sospeso';
                                                        break;
                                                    case 'annullato':
                                                        $statoClass = 'bg-danger';
                                                        $statoText = 'Annullato';
                                                        break;
                                                    default:
                                                        $statoClass = 'bg-secondary';
                                                        $statoText = 'Sconosciuto';
                                                }
                                                ?>
                                                <span class="badge <?= $statoClass ?>"><?= $statoText ?></span>
                                            </td>
                                            <td>
                                                <?php if (!empty($progetto['data_scadenza'])) : ?>
                                                    <?php
                                                    $scadenza = new DateTime($progetto['data_scadenza']);
                                                    $oggi = new DateTime();
                                                    $diff = $oggi->diff($scadenza);
                                                    $isExpired = $oggi > $scadenza;
                                                    $badgeClass = 'bg-success';
                                                    
                                                    if ($isExpired) {
                                                        $badgeClass = 'bg-danger';
                                                    } elseif ($diff->days <= 7) {
                                                        $badgeClass = 'bg-warning';
                                                    }
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>">
                                                        <?= date('d/m/Y', strtotime($progetto['data_scadenza'])) ?>
                                                    </span>
                                                <?php else : ?>
                                                    <span class="badge bg-secondary">Non definita</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $prioritaClass = '';
                                                switch ($progetto['priorita']) {
                                                    case 'bassa':
                                                        $prioritaClass = 'bg-success';
                                                        break;
                                                    case 'media':
                                                        $prioritaClass = 'bg-info';
                                                        break;
                                                    case 'alta':
                                                        $prioritaClass = 'bg-warning';
                                                        break;
                                                    case 'critica':
                                                        $prioritaClass = 'bg-danger';
                                                        break;
                                                    default:
                                                        $prioritaClass = 'bg-secondary';
                                                }
                                                ?>
                                                <span class="badge <?= $prioritaClass ?>"><?= ucfirst($progetto['priorita']) ?></span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <a href="<?= base_url('progetti/' . $progetto['id']) ?>" class="btn btn-sm btn-info" title="Visualizza">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= base_url('progetti/edit/' . $progetto['id']) ?>" class="btn btn-sm btn-primary" title="Modifica">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?= base_url('progetti/toggle-attivo/' . $progetto['id']) ?>" class="btn btn-sm <?= $progetto['attivo'] ? 'btn-warning' : 'btn-success' ?>" title="<?= $progetto['attivo'] ? 'Disattiva' : 'Attiva' ?>">
                                                        <i class="fas <?= $progetto['attivo'] ? 'fa-ban' : 'fa-check' ?>"></i>
                                                    </a>
                                                    <a href="javascript:void(0)" class="btn btn-sm btn-danger btn-elimina-progetto" title="Elimina" data-id="<?= $progetto['id'] ?>" data-nome="<?= esc($progetto['nome']) ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
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
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>

<script>
    $(document).ready(function() {
        // Inizializzazione DataTable per ordinamento e ricerca
        $('table').DataTable({
            "language": {
                "url": "<?= base_url('plugins/datatables/Italian.json') ?>"
            },
            "responsive": true,
            "order": [
                [0, "desc"]
            ]
        });
        
        // Gestione eliminazione progetto con SweetAlert2
        $('.btn-elimina-progetto').click(function() {
            var id = $(this).data('id');
            var nome = $(this).data('nome');
            
            Swal.fire({
                title: 'Sei sicuro?',
                text: "Stai per eliminare il progetto '" + nome + "'. Questa azione non può essere annullata!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sì, elimina!',
                cancelButtonText: 'Annulla'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('progetti/delete/') ?>' + id;
                }
            });
        });
    });
</script>
<?= $this->endSection() ?> 