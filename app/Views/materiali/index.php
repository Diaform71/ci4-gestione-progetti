<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Gestione Materiali<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Gestione Materiali<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
<li class="breadcrumb-item active">Materiali</li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .material-status {
        width: 80px;
    }
    .material-thumbnail {
        width: 60px;
        height: 60px;
        object-fit: contain;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 2px;
    }
    .badge-category {
        margin-right: 3px;
        margin-bottom: 3px;
        display: inline-block;
    }
    .search-box {
        margin-bottom: 20px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Elenco Materiali</h3>
        <div class="card-tools">
            <a href="<?= base_url('materiali/new') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nuovo Materiale
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtri di ricerca -->
        <div class="search-box">
            <form action="<?= base_url('materiali') ?>" method="get">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">Ricerca</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="Codice, descrizione..." value="<?= $search ?? '' ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="category">Categoria</label>
                            <select class="form-control" id="category" name="category">
                                <option value="">Tutte le categorie</option>
                                <option value="commerciale" <?= ($category ?? '') == 'commerciale' ? 'selected' : '' ?>>Commerciale</option>
                                <option value="meccanica" <?= ($category ?? '') == 'meccanica' ? 'selected' : '' ?>>Meccanica</option>
                                <option value="elettrica" <?= ($category ?? '') == 'elettrica' ? 'selected' : '' ?>>Elettrica</option>
                                <option value="pneumatica" <?= ($category ?? '') == 'pneumatica' ? 'selected' : '' ?>>Pneumatica</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Stato</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">Tutti gli stati</option>
                                <option value="1" <?= ($status ?? '') == '1' ? 'selected' : '' ?>>In Produzione</option>
                                <option value="0" <?= ($status ?? '') == '0' ? 'selected' : '' ?>>Fuori Produzione</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-search"></i> Filtra
                                </button>
                                <a href="<?= base_url('materiali') ?>" class="btn btn-secondary">
                                    <i class="fas fa-eraser"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <?php if (empty($materiali)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Nessun materiale trovato.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width: 70px">Immagine</th>
                            <th>Codice</th>
                            <th>Descrizione</th>
                            <th>Produttore</th>
                            <th>Materiale</th>
                            <th>Categorie</th>
                            <th class="text-center material-status">Stato</th>
                            <th style="width: 140px">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($materiali as $materiale): ?>
                            <tr>
                                <td class="text-center">
                                    <?php if (!empty($materiale['immagine'])): ?>
                                        <img src="<?= base_url('uploads/materiali/' . $materiale['immagine']) ?>" 
                                             alt="<?= esc($materiale['codice']) ?>" class="material-thumbnail">
                                    <?php else: ?>
                                        <i class="fas fa-image text-muted"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($materiale['codice']) ?></td>
                                <td>
                                    <?php if (!empty($materiale['descrizione'])): ?>
                                        <?= character_limiter(esc($materiale['descrizione']), 50) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($materiale['produttore'])): ?>
                                        <?= esc($materiale['produttore']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($materiale['materiale'])): ?>
                                        <?= esc($materiale['materiale']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!$materiale['commerciale'] && !$materiale['meccanica'] && !$materiale['elettrica'] && !$materiale['pneumatica']): ?>
                                        <span class="text-muted">-</span>
                                    <?php else: ?>
                                        <?php if ($materiale['commerciale']): ?>
                                            <span class="badge badge-info badge-category">Commerciale</span>
                                        <?php endif; ?>
                                        <?php if ($materiale['meccanica']): ?>
                                            <span class="badge badge-secondary badge-category">Meccanica</span>
                                        <?php endif; ?>
                                        <?php if ($materiale['elettrica']): ?>
                                            <span class="badge badge-warning badge-category">Elettrica</span>
                                        <?php endif; ?>
                                        <?php if ($materiale['pneumatica']): ?>
                                            <span class="badge badge-primary badge-category">Pneumatica</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($materiale['in_produzione']): ?>
                                        <span class="badge badge-success">Attivo</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inattivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('materiali/show/' . $materiale['id']) ?>" class="btn btn-info btn-sm mb-1" title="Dettagli">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= base_url('materiali/edit/' . $materiale['id']) ?>" class="btn btn-primary btn-sm mb-1" title="Modifica">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm mb-1" title="Elimina" 
                                            onclick="confirmDelete(<?= $materiale['id'] ?>, '<?= esc($materiale['codice']) ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (isset($pager)): ?>
                <div class="mt-4">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Elimina -->
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
                Sei sicuro di voler eliminare il materiale <strong id="deleteItemName"></strong>?<br>
                Questa operazione non può essere annullata.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <form id="deleteForm" action="" method="post">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">Elimina</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function confirmDelete(id, name) {
        document.getElementById('deleteItemName').textContent = name;
        document.getElementById('deleteForm').action = '<?= base_url('materiali/delete/') ?>' + id;
        $('#deleteModal').modal('show');
    }
</script>
<?= $this->endSection() ?> 