<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?? 'Dettaglio Template Email' ?><?= $this->endSection() ?>

<?= $this->section('page_title') ?><?= $title ?? 'Dettaglio Template Email' ?><?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('email-templates') ?>">Template Email</a></li>
<li class="breadcrumb-item active">Dettaglio</li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .template-header {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        border-left: 4px solid #007bff;
    }
    .template-content {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
        background-color: #fff;
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
    .template-badge {
        font-size: 85%;
        vertical-align: middle;
    }
    .metadata {
        color: #6c757d;
        font-size: 0.875rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Dettaglio Template Email</h3>
        <div class="card-tools">
            <a href="<?= base_url('email-templates/modifica/' . $template['id']) ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-edit"></i> Modifica
            </a>
            <a href="<?= base_url('email-templates/anteprima/' . $template['id']) ?>" class="btn btn-sm btn-secondary">
                <i class="fas fa-search"></i> Anteprima
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="template-header">
            <div class="row">
                <div class="col-md-8">
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
                </div>
                <div class="col-md-4 text-right">
                    <div class="metadata">
                        <div>Creato: <?= date('d/m/Y H:i', strtotime($template['created_at'])) ?></div>
                        <?php if($template['updated_at'] && $template['updated_at'] != $template['created_at']): ?>
                            <div>Aggiornato: <?= date('d/m/Y H:i', strtotime($template['updated_at'])) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="email-preview">
                    <div class="email-header">
                        <strong>Oggetto:</strong> <?= esc($template['oggetto']) ?>
                    </div>
                    <div class="email-body">
                        <?= $template['corpo'] ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <h5>Placeholder disponibili:</h5>
                <p>Questo template utilizza i seguenti placeholder che verranno sostituiti con i dati effettivi durante l'invio.</p>
                
                <?php
                $placeholders = [];
                preg_match_all('/\{\{([^}]+)\}\}/', $template['corpo'] . $template['oggetto'], $matches);
                if (!empty($matches[1])) {
                    $placeholders = array_unique($matches[1]);
                }
                ?>
                
                <?php if(!empty($placeholders)): ?>
                    <ul>
                        <?php foreach($placeholders as $placeholder): ?>
                            <li><code>{{<?= $placeholder ?>}}</code></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="alert alert-info">
                        Nessun placeholder trovato in questo template.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <a href="<?= base_url('email-templates') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Torna all'elenco
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 