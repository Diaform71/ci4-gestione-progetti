<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Impostazioni<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Impostazioni<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item active">Impostazioni</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="float-right">
                    <a href="<?= site_url('impostazioni/nuova') ?>" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Nuova Impostazione
                    </a>
                </div>
            </div>
        </div>
        
        <?= view('layouts/partials/_alert') ?>
        
        <?php if (empty($impostazioni)): ?>
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Nessuna impostazione di sistema trovata.
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Impostazioni di Sistema</h3>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="impostazioniTab" role="tablist">
                        <?php $first = true; ?>
                        <?php foreach ($impostazioni as $gruppo => $imp): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= $first ? 'active' : '' ?>" 
                                   id="<?= $gruppo ?>-tab" 
                                   data-toggle="tab" 
                                   href="#<?= $gruppo ?>" 
                                   role="tab" 
                                   aria-controls="<?= $gruppo ?>" 
                                   aria-selected="<?= $first ? 'true' : 'false' ?>">
                                    <?= ucfirst($gruppo) ?>
                                </a>
                            </li>
                            <?php $first = false; ?>
                        <?php endforeach; ?>
                    </ul>
                    
                    <div class="tab-content mt-3" id="impostazioniTabContent">
                        <?php $first = true; ?>
                        <?php foreach ($impostazioni as $gruppo => $imp): ?>
                            <div class="tab-pane fade <?= $first ? 'show active' : '' ?>" 
                                 id="<?= $gruppo ?>" 
                                 role="tabpanel" 
                                 aria-labelledby="<?= $gruppo ?>-tab">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Chiave</th>
                                                <th>Valore</th>
                                                <th>Tipo</th>
                                                <th>Descrizione</th>
                                                <th>Azioni</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($imp as $impostazione): ?>
                                                <tr>
                                                    <td><?= esc($impostazione['chiave']) ?></td>
                                                    <td>
                                                        <?php if ($impostazione['tipo'] === 'password' || $impostazione['chiave'] === 'smtp_pass'): ?>
                                                            ********
                                                        <?php elseif ($impostazione['tipo'] === 'booleano'): ?>
                                                            <?= $impostazione['valore'] ? '<span class="badge badge-success">Sì</span>' : '<span class="badge badge-danger">No</span>' ?>
                                                        <?php elseif ($impostazione['tipo'] === 'json'): ?>
                                                            <pre class="m-0"><?= json_encode(json_decode($impostazione['valore_raw']), JSON_PRETTY_PRINT) ?></pre>
                                                        <?php else: ?>
                                                            <?= esc($impostazione['valore_raw']) ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><span class="badge badge-info"><?= esc($impostazione['tipo']) ?></span></td>
                                                    <td><?= esc($impostazione['descrizione']) ?></td>
                                                    <td>
                                                        <a href="<?= site_url('impostazioni/modifica/' . $impostazione['id']) ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="<?= site_url('impostazioni/elimina/' . $impostazione['id']) ?>" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Sei sicuro di voler eliminare questa impostazione?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php $first = false; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?= $this->endSection() ?> 