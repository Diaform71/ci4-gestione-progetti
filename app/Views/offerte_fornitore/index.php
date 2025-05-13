<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Offerte Fornitore<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Offerte Fornitore<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item active">Offerte Fornitore</li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <?= view('layouts/partials/_alert') ?>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title">Elenco Offerte Fornitore</h3>
                </div>
                <div class="col-md-6 text-right">
                    <a href="<?= base_url('offerte-fornitore/new') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuova Offerta
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabellaOfferte" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Numero</th>
                            <th>Data</th>
                            <th>Fornitore</th>
                            <th>Oggetto</th>
                            <th>Importo</th>
                            <th>Stato</th>
                            <th>Rif. RDO</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($offerte)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Nessuna offerta fornitore trovata</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($offerte as $offerta): ?>
                                <tr>
                                    <td>
                                        <a href="<?= base_url('offerte-fornitore/' . $offerta['id']) ?>">
                                            <?= esc($offerta['numero']) ?>
                                        </a>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($offerta['data'])) ?></td>
                                    <td>
                                        <?php if (!empty($offerta['id_anagrafica'])): ?>
                                            <a href="<?= base_url('anagrafiche/show/' . $offerta['id_anagrafica']) ?>">
                                                <?= esc($offerta['nome_fornitore'] ?? 'N/A') ?>
                                            </a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($offerta['oggetto']) ?></td>
                                    <td class="text-right">
                                        <?php if (!empty($offerta['importo_totale'])): ?>
                                            <?= number_format($offerta['importo_totale'], 2, ',', '.') ?> <?= esc($offerta['valuta']) ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $statoClass = [
                                            'ricevuta' => 'info',
                                            'in_valutazione' => 'warning',
                                            'approvata' => 'success',
                                            'rifiutata' => 'danger',
                                            'scaduta' => 'secondary'
                                        ];
                                        $statoText = [
                                            'ricevuta' => 'Ricevuta',
                                            'in_valutazione' => 'In Valutazione',
                                            'approvata' => 'Approvata',
                                            'rifiutata' => 'Rifiutata',
                                            'scaduta' => 'Scaduta'
                                        ];
                                        ?>
                                        <span class="badge badge-<?= $statoClass[$offerta['stato']] ?? 'secondary' ?>">
                                            <?= $statoText[$offerta['stato']] ?? ucfirst($offerta['stato']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($offerta['id_richiesta_offerta'])): ?>
                                            <a href="<?= base_url('richieste-offerta/' . $offerta['id_richiesta_offerta']) ?>">
                                                <?= esc($offerta['numero_rdo'] ?? 'N/A') ?>
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= base_url('offerte-fornitore/' . $offerta['id']) ?>" class="btn btn-sm btn-primary" title="Dettagli">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('offerte-fornitore/edit/' . $offerta['id']) ?>" class="btn btn-sm btn-warning" title="Modifica">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($offerta['stato'] !== 'approvata'): ?>
                                                <a href="javascript:void(0);" 
                                                   class="btn btn-sm btn-danger btn-elimina-offerta" 
                                                   data-id="<?= $offerta['id'] ?>"
                                                   data-numero="<?= $offerta['numero'] ?>"
                                                   title="Elimina">
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

<!-- Modal Conferma Eliminazione -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Conferma Eliminazione</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Sei sicuro di voler eliminare l'offerta <span id="offerta-numero"></span>?</p>
                <p>Questa operazione non può essere annullata.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <a href="#" id="btn-conferma-elimina" class="btn btn-danger">Elimina</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script>
$(document).ready(function() {
    // Verifica se ci sono dati nella tabella
    var hasData = $('#tabellaOfferte tbody tr').length > 0 && !$('#tabellaOfferte tbody tr td:first-child').hasClass('text-center');
    
    try {
        // Inizializzazione semplificata senza opzioni avanzate
        if (hasData) {
            $('#tabellaOfferte').DataTable({
                "language": {
                    "url": "<?= base_url('plugins/datatables/Italian.json') ?>"
                }
            });
        }
    } catch (error) {
        console.error("Errore nell'inizializzazione di DataTable:", error);
    }
    
    // Gestione eliminazione offerta
    $('.btn-elimina-offerta').on('click', function() {
        var id = $(this).data('id');
        var numero = $(this).data('numero');
        
        $('#offerta-numero').text(numero);
        $('#btn-conferma-elimina').attr('href', '<?= base_url('offerte-fornitore/delete/') ?>' + id);
        $('#deleteModal').modal('show');
    });
});
</script>
<?= $this->endSection() ?>

