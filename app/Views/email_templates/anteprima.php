<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?? 'Anteprima Template Email' ?><?= $this->endSection() ?>

<?= $this->section('page_title') ?><?= $title ?? 'Anteprima Template Email' ?><?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('email-templates') ?>">Template Email</a></li>
<li class="breadcrumb-item active">Anteprima</li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .template-header {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        border-left: 4px solid #28a745;
    }
    .email-preview {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .email-header {
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }
    .example-data {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .example-data h5 {
        margin-bottom: 15px;
        color: #495057;
    }
    .example-data table {
        margin-bottom: 0;
    }
    .template-badge {
        font-size: 85%;
        vertical-align: middle;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Anteprima Template Email</h3>
        <div class="card-tools">
            <a href="<?= base_url('email-templates/modifica/' . $template['id']) ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-edit"></i> Modifica
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="template-header">
            <div class="row">
                <div class="col-md-12">
                    <h4><?= esc($template['nome']) ?> 
                        <?php 
                        $badge = 'badge-info';
                        switch ($template['tipo']) {
                            case 'RDO':
                                $badge = 'badge-primary';
                                break;
                            case 'ORDINE':
                                $badge = 'badge-success';
                                break;
                            case 'OFFERTA':
                                $badge = 'badge-warning';
                                break;
                        }
                        ?>
                        <span class="badge <?= $badge ?> template-badge"><?= esc($template['tipo']) ?></span>
                    </h4>
                    <p class="mb-0">Questa è un'anteprima del template con dati di esempio. I placeholder sono stati sostituiti per mostrare come apparirà l'email finale.</p>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-12">
                <div class="example-data">
                    <h5>Dati di esempio utilizzati:</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Placeholder</th>
                                    <th>Valore</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($datiEsempio as $key => $value): ?>
                                <tr>
                                    <td><code>{{<?= $key ?>}}</code></td>
                                    <td><?= is_string($value) && substr($value, 0, 1) !== '<' ? esc($value) : $value ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="email-preview">
                    <div class="email-header">
                        <strong>Oggetto:</strong> <?= esc($templateCompilato['oggetto']) ?>
                    </div>
                    <div class="email-body">
                        <?= $templateCompilato['corpo'] ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <a href="<?= base_url('email-templates') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Torna all'elenco
                </a>
                <a href="<?= base_url('email-templates/dettaglio/' . $template['id']) ?>" class="btn btn-info">
                    <i class="fas fa-eye"></i> Dettagli template
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 