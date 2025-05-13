<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Ordini d'acquisto<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Ordini d'acquisto<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Home</a></li>
<li class="breadcrumb-item active"><?= $title ?></li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8">
                                <h3 class="card-title">Elenco degli ordini di materiale</h3>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?= site_url('ordini-materiale/new') ?>" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Nuovo Ordine
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="ordini-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Numero</th>
                                    <th>Data</th>
                                    <th>Fornitore</th>
                                    <th>Oggetto</th>
                                    <th>Progetto</th>
                                    <th>Stato</th>
                                    <th>Creato da</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ordini as $ordine): ?>
                                    <tr>
                                        <td><?= esc($ordine['numero']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($ordine['data'])) ?></td>
                                        <td><?= esc($ordine['nome_fornitore']) ?></td>
                                        <td><?= esc($ordine['oggetto']) ?></td>
                                        <td><?= esc($ordine['nome_progetto'] ?? 'N/D') ?></td>
                                        <td>
                                            <?php
                                            switch ($ordine['stato']) {
                                                case 'bozza':
                                                    echo '<span class="badge badge-secondary">Bozza</span>';
                                                    break;
                                                case 'in_attesa':
                                                    echo '<span class="badge badge-warning">In Attesa</span>';
                                                    break;
                                                case 'completato':
                                                    echo '<span class="badge badge-primary">Completato</span>';
                                                    break;
                                                case 'inviato':
                                                    echo '<span class="badge badge-info">Inviato</span>';
                                                    break;
                                                case 'confermato':
                                                    echo '<span class="badge badge-primary">Confermato</span>';
                                                    break;
                                                case 'in_consegna':
                                                    echo '<span class="badge badge-warning">In Consegna</span>';
                                                    break;
                                                case 'consegnato':
                                                    echo '<span class="badge badge-success">Consegnato</span>';
                                                    break;
                                                case 'annullato':
                                                    echo '<span class="badge badge-danger">Annullato</span>';
                                                    break;
                                                default:
                                                    echo '<span class="badge badge-secondary">' . ucfirst($ordine['stato']) . '</span>';
                                            }
                                            ?>
                                        </td>
                                        <td><?= esc($ordine['nome_utente'] ?? '') ?> <?= esc($ordine['cognome_utente'] ?? '') ?></td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="<?= site_url('ordini-materiale/' . $ordine['id']) ?>" class="btn btn-sm btn-primary" title="Visualizza">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($ordine['stato'] === 'bozza'): ?>
                                                    <a href="<?= site_url('ordini-materiale/edit/' . $ordine['id']) ?>" class="btn btn-sm btn-info" title="Modifica">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (in_array($ordine['stato'], ['bozza', 'annullato'])): ?>
                                                    <a href="<?= site_url('ordini-materiale/delete/' . $ordine['id']) ?>" class="btn btn-sm btn-danger delete-confirm" title="Elimina">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
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
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script>
    $(function() {
        $('#ordini-table').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "url": "<?= base_url('plugins/datatables/Italian.json') ?>"
            },
            "order": [
                [1, 'desc']
            ] // Ordina per data decrescente
        });

        // Conferma eliminazione
        $('.delete-confirm').on('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');

            Swal.fire({
                title: 'Sei sicuro?',
                text: "Questa azione non può essere annullata!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sì, elimina!',
                cancelButtonText: 'Annulla'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>