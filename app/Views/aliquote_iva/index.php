<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item active">Aliquote IVA</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Elenco Aliquote IVA</h3>
                        <a href="<?= base_url('aliquote-iva/new') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuova Aliquota IVA
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('message')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('message') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="table-aliquote">
                            <thead>
                                <tr>
                                    <th width="70">Codice</th>
                                    <th>Descrizione</th>
                                    <th width="100">Percentuale</th>
                                    <th>Note</th>
                                    <th width="120">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($aliquote_iva)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Nessun dato disponibile</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($aliquote_iva as $aliquota): ?>
                                        <tr>
                                            <td><?= esc($aliquota['codice']) ?></td>
                                            <td><?= esc($aliquota['descrizione']) ?></td>
                                            <td class="text-right"><?= number_format((float)$aliquota['percentuale'], 2, ',', '.') ?>%</td>
                                            <td><?= esc($aliquota['note']) ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?= base_url('aliquote-iva/edit/' . $aliquota['id']) ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="<?= $aliquota['id'] ?>" data-name="<?= esc($aliquota['codice'] . ' - ' . $aliquota['descrizione']) ?>">
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
                Sei sicuro di voler eliminare l'aliquota IVA <strong id="delete-name"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <a href="#" id="btn-confirm-delete" class="btn btn-danger">Elimina</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/sweetalert2/sweetalert2.min.js') ?>"></script>
<script src="<?= base_url('plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>

<script>
    $(function() {
        // Inizializzazione DataTable
        $('#table-aliquote').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "url": "<?= base_url('plugins/datatables/Italian.json') ?>"
            }
        });
        
        // Gestione eliminazione
        $('.btn-delete').on('click', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            
            $('#delete-name').text(name);
            $('#btn-confirm-delete').attr('href', `<?= base_url('aliquote-iva/delete') ?>/${id}`);
            $('#deleteModal').modal('show');
        });
    });
</script>
<?= $this->endSection() ?> 