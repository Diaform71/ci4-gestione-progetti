<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Richieste d'Offerta<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Richieste d'Offerta<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item active">Richieste d'Offerta</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="float-right">
                    <a href="<?= site_url('richieste-offerta/new') ?>" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Nuova Richiesta d'Offerta
                    </a>
                </div>
            </div>
        </div>
        
        <?= view('layouts/partials/_alert') ?>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Elenco Richieste d'Offerta</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabellaRichieste" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="12%">Numero</th>
                                <th width="10%">Data</th>
                                <th width="20%">Fornitore</th>
                                <th width="20%">Oggetto</th>
                                <th width="15%">Progetto</th>
                                <th width="8%">Stato</th>
                                <th width="15%">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($richieste)): ?>
                                <?php foreach ($richieste as $richiesta): ?>
                                    <tr>
                                        <td><?= esc($richiesta['numero']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($richiesta['data'])) ?></td>
                                        <td><?= esc($richiesta['nome_fornitore']) ?></td>
                                        <td><?= esc($richiesta['oggetto']) ?></td>
                                        <td>
                                            <?php if (!empty($richiesta['id_progetto']) && !empty($richiesta['nome_progetto'])): ?>
                                                <a href="<?= site_url('progetti/' . $richiesta['id_progetto']) ?>">
                                                    <?= esc($richiesta['nome_progetto']) ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badgeClass = 'secondary';
                                            switch ($richiesta['stato']) {
                                                case 'bozza':
                                                    $badgeClass = 'warning';
                                                    break;
                                                case 'inviata':
                                                    $badgeClass = 'primary';
                                                    break;
                                                case 'accettata':
                                                    $badgeClass = 'success';
                                                    break;
                                                case 'rifiutata':
                                                    $badgeClass = 'danger';
                                                    break;
                                                case 'annullata':
                                                    $badgeClass = 'secondary';
                                                    break;
                                            }
                                            ?>
                                            <span class="badge badge-<?= $badgeClass ?>">
                                                <?= ucfirst(str_replace('_', ' ', $richiesta['stato'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= site_url('richieste-offerta/' . $richiesta['id']) ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php if ($richiesta['stato'] === 'bozza'): ?>
                                                <a href="<?= site_url('richieste-offerta/edit/' . $richiesta['id']) ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <a href="<?= site_url('richieste-offerta/delete/' . $richiesta['id']) ?>" class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Sei sicuro di voler eliminare questa richiesta d\'offerta?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Nessuna richiesta d'offerta trovata</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
$(document).ready(function() {
    $('#tabellaRichieste').DataTable({
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
        "order": [[1, 'desc']] // Ordina per data (discendente)
    });
});
</script>
<?= $this->endSection() ?> 