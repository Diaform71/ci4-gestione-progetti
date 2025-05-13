<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Dettaglio Materiale<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Dettaglio Materiale<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('materiali') ?>">Materiali</a></li>
<li class="breadcrumb-item active">Dettaglio</li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .material-image {
        max-width: 300px;
        max-height: 300px;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px;
        margin-bottom: 20px;
    }
    .badge-category {
        margin-right: 5px;
    }
    .detail-label {
        font-weight: bold;
        margin-bottom: 5px;
    }
    .detail-value {
        margin-bottom: 15px;
    }
    #barcode-container {
        margin: 0 auto;
        max-width: 100%;
    }
    #barcode-container svg {
        max-width: 100%;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Aggiungiamo JsBarcode per la visualizzazione client-side del codice a barre -->
<script src="<?= base_url('plugins/JsBarcode/JsBarcode.all.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        // Generiamo il barcode utilizzando JsBarcode
        $("#barcode-container").html('<svg id="barcode"></svg>');
        JsBarcode("#barcode", "<?= esc($materiale['codice']) ?>", {
            format: "CODE128",
            lineColor: "#000",
            width: 2,
            height: 50,
            displayValue: true,
            fontSize: 12,
            margin: 5
        });
    });
</script>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Dettaglio Materiale: <?= esc($materiale['codice']) ?></h3>
        <div class="card-tools">
            <a href="<?= base_url('materiali/barcode/' . $materiale['id']) ?>" class="btn btn-info btn-sm mr-1" target="_blank">
                <i class="fas fa-barcode"></i> Genera Barcode
            </a>
            <a href="<?= base_url('materiali/edit/' . $materiale['id']) ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Modifica
            </a>
            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal">
                <i class="fas fa-trash"></i> Elimina
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <?php if (!empty($materiale['immagine'])): ?>
                    <img src="<?= base_url('uploads/materiali/' . $materiale['immagine']) ?>" alt="<?= esc($materiale['codice']) ?>" class="material-image">
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Nessuna immagine disponibile
                    </div>
                <?php endif; ?>
                
                <!-- Anteprima codice a barre -->
                <div class="card mb-3">
                    <div class="card-header bg-info">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-barcode"></i> Codice a Barre
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div id="barcode-container"></div>
                        <div class="mt-2">
                            <a href="<?= base_url('materiali/barcode/' . $materiale['id']) ?>" class="btn btn-sm btn-outline-info" target="_blank">
                                <i class="fas fa-print"></i> Stampa PDF
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="detail-label">Status:</div>
                <div class="detail-value">
                    <?php if ($materiale['in_produzione']): ?>
                        <span class="badge badge-success">In Produzione</span>
                    <?php else: ?>
                        <span class="badge badge-danger">Fuori Produzione</span>
                    <?php endif; ?>
                </div>
                
                <div class="detail-label">Categorie:</div>
                <div class="detail-value">
                    <?php if (!$materiale['commerciale'] && !$materiale['meccanica'] && !$materiale['elettrica'] && !$materiale['pneumatica']): ?>
                        <span class="text-muted">Nessuna categoria assegnata</span>
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
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="detail-label">Codice:</div>
                <div class="detail-value"><?= esc($materiale['codice']) ?></div>
                
                <div class="detail-label">Descrizione:</div>
                <div class="detail-value">
                    <?php if (!empty($materiale['descrizione'])): ?>
                        <?= nl2br(esc($materiale['descrizione'])) ?>
                    <?php else: ?>
                        <span class="text-muted">Nessuna descrizione disponibile</span>
                    <?php endif; ?>
                </div>
                
                <div class="detail-label">Materiale:</div>
                <div class="detail-value">
                    <?php if (!empty($materiale['materiale'])): ?>
                        <?= esc($materiale['materiale']) ?>
                    <?php else: ?>
                        <span class="text-muted">Non specificato</span>
                    <?php endif; ?>
                </div>
                
                <div class="detail-label">Produttore:</div>
                <div class="detail-value">
                    <?php if (!empty($materiale['produttore'])): ?>
                        <?= esc($materiale['produttore']) ?>
                    <?php else: ?>
                        <span class="text-muted">Non specificato</span>
                    <?php endif; ?>
                </div>
                
                <div class="detail-label">Data creazione:</div>
                <div class="detail-value">
                    <?= date('d/m/Y H:i', strtotime($materiale['created_at'])) ?>
                </div>
                
                <div class="detail-label">Ultima modifica:</div>
                <div class="detail-value">
                    <?= date('d/m/Y H:i', strtotime($materiale['updated_at'])) ?>
                </div>
            </div>
        </div>

        <!-- Schede per Richieste d'Offerta, Offerte e Ordini -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card card-primary card-outline card-tabs">
                    <div class="card-header p-0 pt-1 border-bottom-0">
                        <ul class="nav nav-tabs" id="documentTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="richieste-tab" data-toggle="pill" href="#richieste" role="tab" aria-controls="richieste" aria-selected="true">
                                    <i class="fas fa-file-invoice"></i> Richieste d'Offerta
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="offerte-tab" data-toggle="pill" href="#offerte" role="tab" aria-controls="offerte" aria-selected="false">
                                    <i class="fas fa-file-contract"></i> Offerte Ricevute
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="ordini-tab" data-toggle="pill" href="#ordini" role="tab" aria-controls="ordini" aria-selected="false">
                                    <i class="fas fa-shopping-cart"></i> Ordini d'Acquisto
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="documentTabsContent">
                            <!-- Tab Richieste d'Offerta -->
                            <div class="tab-pane fade show active" id="richieste" role="tabpanel" aria-labelledby="richieste-tab">
                                <?php if (isset($richiesteOfferta) && !empty($richiesteOfferta)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Numero</th>
                                                    <th>Data</th>
                                                    <th>Oggetto</th>
                                                    <th>Fornitore</th>
                                                    <th>Quantità</th>
                                                    <th>Stato</th>
                                                    <th>Azioni</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($richiesteOfferta as $richiesta): ?>
                                                    <tr>
                                                        <td>
                                                            <a href="<?= base_url('richieste-offerta/' . $richiesta['id_richiesta']) ?>">
                                                                <?= esc($richiesta['numero']) ?>
                                                            </a>
                                                        </td>
                                                        <td><?= date('d/m/Y', strtotime($richiesta['data'])) ?></td>
                                                        <td><?= esc($richiesta['oggetto']) ?></td>
                                                        <td>
                                                            <?php if (!empty($richiesta['ragione_sociale'])): ?>
                                                                <a href="<?= base_url('anagrafiche/show/' . $richiesta['id_anagrafica']) ?>">
                                                                    <?= esc($richiesta['ragione_sociale']) ?>
                                                                </a>
                                                            <?php else: ?>
                                                                <span class="text-muted">N/D</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= isset($richiesta['quantita']) ? $richiesta['quantita'] : 'N/D' ?></td>
                                                        <td>
                                                            <?php
                                                            $statoClass = [
                                                                'bozza' => 'secondary',
                                                                'inviata' => 'primary',
                                                                'accettata' => 'success',
                                                                'rifiutata' => 'danger',
                                                                'annullata' => 'warning'
                                                            ];
                                                            ?>
                                                            <span class="badge badge-<?= $statoClass[$richiesta['stato']] ?? 'secondary' ?>">
                                                                <?= ucfirst($richiesta['stato']) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="<?= base_url('richieste-offerta/' . $richiesta['id_richiesta']) ?>" class="btn btn-sm btn-info">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> Questo materiale non è presente in nessuna richiesta d'offerta.
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Tab Offerte Ricevute -->
                            <div class="tab-pane fade" id="offerte" role="tabpanel" aria-labelledby="offerte-tab">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Funzionalità in fase di sviluppo. Le offerte ricevute saranno disponibili in una futura implementazione.
                                </div>
                                
                                <!-- Placeholder per la futura implementazione -->
                                <div class="placeholder-content text-center py-5">
                                    <i class="fas fa-file-contract fa-4x text-muted mb-3"></i>
                                    <h5>Offerte Ricevute</h5>
                                    <p class="text-muted">In questa sezione verranno visualizzate le offerte ricevute per questo materiale.</p>
                                </div>
                            </div>
                            
                            <!-- Tab Ordini d'Acquisto -->
                            <div class="tab-pane fade" id="ordini" role="tabpanel" aria-labelledby="ordini-tab">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Funzionalità in fase di sviluppo. Gli ordini d'acquisto saranno disponibili in una futura implementazione.
                                </div>
                                
                                <!-- Placeholder per la futura implementazione -->
                                <div class="placeholder-content text-center py-5">
                                    <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                                    <h5>Ordini d'Acquisto</h5>
                                    <p class="text-muted">In questa sezione verranno visualizzati gli ordini d'acquisto per questo materiale.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <a href="<?= base_url('materiali') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Torna all'elenco
                </a>
            </div>
        </div>
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
                Sei sicuro di voler eliminare il materiale <strong><?= esc($materiale['codice']) ?></strong>?<br>
                Questa operazione non può essere annullata.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <form action="<?= base_url('materiali/delete/' . $materiale['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">Elimina</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 