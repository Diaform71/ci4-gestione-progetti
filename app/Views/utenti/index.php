<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?? 'Gestione Utenti' ?><?= $this->endSection() ?>

<?= $this->section('page_title') ?><?= $title ?? 'Gestione Utenti' ?><?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
<li class="breadcrumb-item active">Gestione Utenti</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="content">
    <div class="container-fluid">
        <?php if (session()->has('success')): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?= session('success') ?>
            </div>
        <?php endif; ?>
        
        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?= session('error') ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista Utenti</h3>
                <div class="card-tools">
                    <a href="<?= base_url('utenti/new') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuovo Utente
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="tbl-utenti" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Nome</th>
                            <th>Cognome</th>
                            <th>Email</th>
                            <th>Ruolo</th>
                            <th>Stato</th>
                            <th>Ultimo Accesso</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utenti as $utente): ?>
                            <tr>
                                <td><?= $utente['id'] ?></td>
                                <td><?= esc($utente['username']) ?></td>
                                <td><?= esc($utente['nome']) ?></td>
                                <td><?= esc($utente['cognome']) ?></td>
                                <td><?= esc($utente['email']) ?></td>
                                <td><span class="badge <?= $utente['ruolo'] === 'admin' ? 'badge-danger' : 'badge-info' ?>"><?= ucfirst(esc($utente['ruolo'])) ?></span></td>
                                <td>
                                    <?php if ($utente['attivo']): ?>
                                        <span class="badge badge-success">Attivo</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Disattivato</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $utente['ultimo_accesso'] ? date('d/m/Y H:i', strtotime($utente['ultimo_accesso'])) : 'Mai' ?></td>
                                <td>
                                    <div class="d-flex">
                                        <a href="<?= base_url('utenti/edit/' . $utente['id']) ?>" class="btn btn-sm btn-warning mr-1" title="Modifica">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ((int)$utente['id'] !== (int)session('utente_id')): ?>
                                            <button type="button" class="btn btn-sm btn-danger btn-delete" 
                                                    data-toggle="modal" data-target="#modal-delete" 
                                                    data-id="<?= $utente['id'] ?>" 
                                                    data-name="<?= esc($utente['username']) ?>"
                                                    title="Elimina">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Modal per conferma eliminazione -->
<div class="modal fade" id="modal-delete">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Conferma Eliminazione</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Sei sicuro di voler eliminare l'utente <strong id="user-to-delete"></strong>?</p>
                <p class="text-danger">Questa operazione non può essere annullata.</p>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annulla</button>
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
    // Inizializza DataTables
    $('#tbl-utenti').DataTable({
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "language": {
            "url": "<?= base_url('plugins/datatables/Italian.json') ?>"
        }
    });
    
    // Gestione del modal di conferma eliminazione
    $('.btn-delete').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        $('#user-to-delete').text(name);
        $('#btn-confirm-delete').attr('href', '<?= base_url('utenti/delete/') ?>' + id);
    });
});
</script>
<?= $this->endSection() ?> 