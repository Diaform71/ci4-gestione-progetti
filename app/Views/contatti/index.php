<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item active">Contatti</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Elenco Contatti</h3>
                        <a href="<?= base_url('contatti/new') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuovo Contatto
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
                        <?php if (empty($contatti)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Nessun contatto disponibile</h5>
                                <p class="text-muted">Inizia aggiungendo il tuo primo contatto.</p>
                                <a href="<?= base_url('contatti/new') ?>" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Aggiungi Primo Contatto
                                </a>
                            </div>
                        <?php else: ?>
                        <table class="table table-striped table-hover" id="table-contatti">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Nome</th>
                                    <th>Cognome</th>
                                    <th>Email</th>
                                    <th>Telefono</th>
                                    <th>Cellulare</th>
                                    <th width="70">Stato</th>
                                    <th width="120">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contatti as $contatto): ?>
                                    <tr>
                                        <td><?= $contatto['id'] ?></td>
                                        <td><?= esc($contatto['nome']) ?></td>
                                        <td><?= esc($contatto['cognome']) ?></td>
                                        <td><?= esc($contatto['email']) ?></td>
                                        <td><?= esc($contatto['telefono']) ?></td>
                                        <td><?= esc($contatto['cellulare']) ?></td>
                                        <td class="text-center">
                                            <?php if ($contatto['attivo']): ?>
                                                <span class="badge badge-success">Attivo</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Inattivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?= base_url('contatti/show/' . $contatto['id']) ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?= base_url('contatti/edit/' . $contatto['id']) ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="<?= $contatto['id'] ?>" data-name="<?= esc($contatto['nome'] . ' ' . $contatto['cognome']) ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
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
                Sei sicuro di voler eliminare il contatto <strong id="delete-name"></strong>?
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
        // Mostra messaggi di notifica con SweetAlert2
        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                title: 'Successo',
                text: '<?= session()->getFlashdata('success') ?>',
                icon: 'success',
                confirmButtonText: 'Ok'
            });
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('message')): ?>
            Swal.fire({
                title: 'Successo',
                text: '<?= session()->getFlashdata('message') ?>',
                icon: 'success',
                confirmButtonText: 'Ok'
            });
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({
                title: 'Errore',
                text: '<?= session()->getFlashdata('error') ?>',
                icon: 'error',
                confirmButtonText: 'Ok'
            });
        <?php endif; ?>
        
        // Inizializzazione DataTable
        <?php if (!empty($contatti)): ?>
        $('#table-contatti').DataTable({
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
                    "targets": [-1], // Ultima colonna (azioni)
                    "orderable": false,
                    "searchable": false
                },
                {
                    "targets": [-2], // Penultima colonna (stato)
                    "orderable": false
                }
            ]
        });
        <?php endif; ?>

        // Gestione eliminazione
        $('.btn-delete').on('click', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');

            $('#delete-name').text(name);
            $('#btn-confirm-delete').attr('href', `<?= base_url('contatti/delete') ?>/${id}`);
            $('#deleteModal').modal('show');
        });
    });
</script>
<?= $this->endSection() ?> 