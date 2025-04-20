<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?? 'Template Email' ?><?= $this->endSection() ?>

<?= $this->section('page_title') ?><?= $title ?? 'Template Email' ?><?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
<li class="breadcrumb-item active">Template Email</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Elenco Template Email</h3>
        <div class="card-tools">
            <a href="<?= site_url('email-templates/nuovo') ?>" class="btn btn-sm btn-success">
                <i class="fas fa-plus"></i> Nuovo Template
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Oggetto</th>
                        <th>Tipo</th>
                        <th>Data creazione</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($templates)): ?>
                    <tr>
                        <td colspan="5" class="text-center">Nessun template email trovato</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($templates as $template): ?>
                        <tr>
                            <td><?= esc($template['nome']) ?></td>
                            <td><?= esc($template['oggetto']) ?></td>
                            <td>
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
                                <span class="badge <?= $badge ?>"><?= esc($template['tipo']) ?></span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($template['created_at'])) ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= base_url('email-templates/dettaglio/' . $template['id']) ?>" class="btn btn-sm btn-info" title="Dettagli">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= base_url('email-templates/modifica/' . $template['id']) ?>" class="btn btn-sm btn-primary" title="Modifica">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= base_url('email-templates/anteprima/' . $template['id']) ?>" class="btn btn-sm btn-secondary" title="Anteprima">
                                        <i class="fas fa-search"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-danger" onclick="confermaEliminazione(<?= $template['id'] ?>)" title="Elimina">
                                        <i class="fas fa-trash"></i>
                                    </a>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function confermaEliminazione(id) {
        Swal.fire({
            title: 'Sei sicuro?',
            text: "Questa operazione non può essere annullata!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sì, elimina!',
            cancelButtonText: 'Annulla'
        }).then((result) => {
            if (result.isConfirmed) {
                // Crea e invia un form con il token CSRF
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url('email-templates/elimina') ?>/' + id;
                
                // Aggiungi token CSRF
                var csrfField = document.createElement('input');
                csrfField.type = 'hidden';
                csrfField.name = '<?= csrf_token() ?>';
                csrfField.value = '<?= csrf_hash() ?>';
                form.appendChild(csrfField);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
<?= $this->endSection() ?>
