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
        <?= view('layouts/partials/_alert') ?>
        
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Le tue impostazioni personali</h3>
            </div>
            <div class="card-body">
                <form action="<?= site_url('impostazioni/salva-utente') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <?php if (empty($impostazioni)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Non sono presenti impostazioni di sistema configurabili.
                        </div>
                    <?php else: ?>
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
                                    
                                    <?php foreach ($imp as $impostazione): ?>
                                        <div class="form-group row">
                                            <label for="<?= $impostazione['chiave'] ?>" class="col-sm-3 col-form-label">
                                                <?= esc($impostazione['descrizione'] ?: $impostazione['chiave']) ?>
                                                <?php if (!empty($impostazione['personalizzata']) && $impostazione['personalizzata']): ?>
                                                    <span class="badge badge-primary">Personalizzata</span>
                                                <?php endif; ?>
                                            </label>
                                            <div class="col-sm-7">
                                                <?php if ($impostazione['tipo'] === 'booleano'): ?>
                                                    <div class="custom-control custom-switch mt-2">
                                                        <input type="checkbox" 
                                                               class="custom-control-input" 
                                                               id="<?= $impostazione['chiave'] ?>" 
                                                               name="impostazioni[<?= $impostazione['chiave'] ?>]" 
                                                               value="1" 
                                                               <?= $impostazione['valore'] ? 'checked' : '' ?>>
                                                        <label class="custom-control-label" for="<?= $impostazione['chiave'] ?>">
                                                            <?= $impostazione['valore'] ? 'Attivo' : 'Disattivo' ?>
                                                        </label>
                                                    </div>
                                                <?php elseif ($impostazione['tipo'] === 'textarea' || ($impostazione['tipo'] === 'stringa' && strlen($impostazione['valore_raw']) > 100)): ?>
                                                    <textarea class="form-control" 
                                                              id="<?= $impostazione['chiave'] ?>" 
                                                              name="impostazioni[<?= $impostazione['chiave'] ?>]" 
                                                              rows="3"><?= esc($impostazione['valore_raw']) ?></textarea>
                                                <?php elseif ($impostazione['tipo'] === 'select' && $impostazione['chiave'] === 'tema_predefinito'): ?>
                                                    <select class="form-control" 
                                                            id="<?= $impostazione['chiave'] ?>" 
                                                            name="impostazioni[<?= $impostazione['chiave'] ?>]">
                                                        <option value="light" <?= $impostazione['valore'] === 'light' ? 'selected' : '' ?>>Light</option>
                                                        <option value="dark" <?= $impostazione['valore'] === 'dark' ? 'selected' : '' ?>>Dark</option>
                                                    </select>
                                                <?php else: ?>
                                                    <input type="<?= $impostazione['tipo'] === 'intero' || $impostazione['tipo'] === 'decimale' ? 'number' : 'text' ?>" 
                                                           class="form-control" 
                                                           id="<?= $impostazione['chiave'] ?>" 
                                                           name="impostazioni[<?= $impostazione['chiave'] ?>]" 
                                                           value="<?= esc($impostazione['valore_raw']) ?>"
                                                           <?= $impostazione['tipo'] === 'intero' ? 'step="1"' : '' ?>
                                                           <?= $impostazione['tipo'] === 'decimale' ? 'step="0.01"' : '' ?>>
                                                <?php endif; ?>
                                                <small class="form-text text-muted"><?= esc($impostazione['descrizione']) ?></small>
                                            </div>
                                            <div class="col-sm-2">
                                                <?php if (!empty($impostazione['personalizzata']) && $impostazione['personalizzata'] && isset($impostazione['id'])): ?>
                                                    <a href="<?= site_url('impostazioni/reimposta/' . $impostazione['id']) ?>" 
                                                       class="btn btn-sm btn-outline-secondary mt-1" 
                                                       title="Reimposta al valore predefinito"
                                                       onclick="return confirm('Sei sicuro di voler reimpostare questa impostazione al valore predefinito?')">
                                                        <i class="fas fa-undo"></i> Ripristina default
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <hr>
                                    <?php endforeach; ?>
                                </div>
                                <?php $first = false; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group row mt-4">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salva Impostazioni
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?> 