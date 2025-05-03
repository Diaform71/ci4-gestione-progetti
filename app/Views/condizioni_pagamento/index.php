<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?? 'Condizioni Pagamento' ?><?= $this->endSection() ?>

<?= $this->section('page_title') ?><?= $title ?? 'Condizioni Pagamento' ?><?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
<li class="breadcrumb-item active">Condizioni Pagamento</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Elenco Condizioni di Pagamento</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('condizioni-pagamento/new') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nuova Condizione
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                    <?php if (session()->has('message')): ?>
                        <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
                            <?= session('message') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->has('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert">
                            <?= session('error') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Descrizione</th>
                                <th>Giorni</th>
                                <th>Fine Mese</th>
                                <th>Stato</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($condizioni)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Nessuna condizione di pagamento trovata</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($condizioni as $condizione): ?>
                                    <tr>
                                        <td><?= $condizione['id'] ?></td>
                                        <td><?= esc($condizione['nome']) ?></td>
                                        <td><?= esc($condizione['descrizione']) ?></td>
                                        <td><?= $condizione['giorni'] ?></td>
                                        <td>
                                            <?php if ($condizione['fine_mese'] == 1): ?>
                                                <span class="badge badge-success">Sì</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($condizione['attivo'] == 1): ?>
                                                <span class="badge badge-success">Attivo</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Disattivato</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('condizioni-pagamento/edit/' . $condizione['id']) ?>" class="btn btn-info btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal<?= $condizione['id'] ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    <!-- Modal Conferma Eliminazione -->
                                    <div class="modal fade" id="deleteModal<?= $condizione['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel<?= $condizione['id'] ?>" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel<?= $condizione['id'] ?>">Conferma Eliminazione</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    Sei sicuro di voler eliminare la condizione di pagamento "<?= esc($condizione['nome']) ?>"?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                                    <a href="<?= base_url('condizioni-pagamento/delete/' . $condizione['id']) ?>" class="btn btn-danger">Elimina</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>

<?= $this->endSection() ?> 