<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><?= $title ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('richieste-offerta') ?>">Richieste d'Offerta</a></li>
                    <li class="breadcrumb-item active"><?= $title ?></li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?= view('layouts/partials/_alert') ?>
        
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="float-right">
                    <a href="<?= site_url('richieste-offerta/new') ?>" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Nuova Richiesta d'Offerta
                    </a>
                    <a href="<?= site_url('anagrafiche/show/' . $fornitore['id']) ?>" class="btn btn-secondary">
                        <i class="fas fa-building"></i> Vai all'Anagrafica
                    </a>
                    <a href="<?= site_url('richieste-offerta') ?>" class="btn btn-secondary">
                        <i class="fas fa-list"></i> Tutte le Richieste
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-building mr-2"></i>
                    Fornitore: <strong><?= esc($fornitore['ragione_sociale']) ?></strong>
                    <?php if (!empty($fornitore['partita_iva'])): ?>
                        (P.IVA: <?= esc($fornitore['partita_iva']) ?>)
                    <?php endif; ?>
                </h3>
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
                                <th width="40%">Oggetto</th>
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
                                        <td><?= esc($richiesta['oggetto']) ?></td>
                                        <td>
                                            <?php if (!empty($richiesta['id_progetto'])): ?>
                                                <a href="<?= site_url('progetti/' . $richiesta['id_progetto']) ?>">
                                                    <?= esc($richiesta['nome_progetto'] ?? 'Progetto') ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badgeClass = 'secondary';
                                            switch ($richiesta['stato']) {
                                                case 'bozza': $badgeClass = 'warning'; break;
                                                case 'inviata': $badgeClass = 'primary'; break;
                                                case 'accettata': $badgeClass = 'success'; break;
                                                case 'rifiutata': $badgeClass = 'danger'; break;
                                                case 'annullata': $badgeClass = 'secondary'; break;
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
                                    <td colspan="6" class="text-center">Nessuna richiesta d'offerta trovata per questo fornitore</td>
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

<?= $this->section('scripts') ?>
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
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json"
        },
        "order": [[1, 'desc']] // Ordina per data (discendente)
    });
});
</script>
<?= $this->endSection() ?> 