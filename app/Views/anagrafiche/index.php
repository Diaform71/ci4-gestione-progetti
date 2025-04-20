<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item active">Anagrafiche</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Elenco Anagrafiche</h3>
                        <a href="<?= base_url('anagrafiche/new') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuova Anagrafica
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
                        <table class="table table-striped table-hover" id="table-anagrafiche">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Ragione Sociale</th>
                                    <th>Email</th>
                                    <th>Telefono</th>
                                    <th>Città</th>
                                    <th width="70">Cliente</th>
                                    <th width="70">Fornitore</th>
                                    <th width="70">Stato</th>
                                    <th width="120">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($anagrafiche)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center">Nessun dato disponibile</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($anagrafiche as $anagrafica): ?>
                                        <tr>
                                            <td><?= $anagrafica['id'] ?></td>
                                            <td><?= esc($anagrafica['ragione_sociale']) ?></td>
                                            <td><?= esc($anagrafica['email']) ?></td>
                                            <td><?= esc($anagrafica['telefono']) ?></td>
                                            <td><?= esc($anagrafica['citta']) ?></td>
                                            <td class="text-center">
                                                <?php if ($anagrafica['cliente']): ?>
                                                    <span class="badge badge-success"><i class="fas fa-check"></i></span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary"><i class="fas fa-times"></i></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($anagrafica['fornitore']): ?>
                                                    <span class="badge badge-success"><i class="fas fa-check"></i></span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary"><i class="fas fa-times"></i></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($anagrafica['attivo']): ?>
                                                    <span class="badge badge-success">Attivo</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Inattivo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?= base_url('anagrafiche/show/' . $anagrafica['id']) ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= base_url('anagrafiche/edit/' . $anagrafica['id']) ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="<?= $anagrafica['id'] ?>" data-name="<?= esc($anagrafica['ragione_sociale']) ?>">
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
                Sei sicuro di voler eliminare l'anagrafica <strong id="delete-name"></strong>?
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
<script src="<?= base_url('plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>

<script>
    $(function() {
        // Inizializzazione DataTable
        $('#table-anagrafiche').DataTable({
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
            $('#btn-confirm-delete').attr('href', `<?= base_url('anagrafiche/delete') ?>/${id}`);
            $('#deleteModal').modal('show');
        });
    });
</script>
<?= $this->endSection() ?>