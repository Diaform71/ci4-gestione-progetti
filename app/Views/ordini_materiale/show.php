<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Dettaglio Ordine Materiale<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Dettaglio Ordine Materiale <?= esc($ordine['numero']) ?><?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= site_url('ordini-materiale') ?>">Ordini Materiale</a></li>
<li class="breadcrumb-item active">Dettaglio Ordine</li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/select2/css/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('plugins/summernote/summernote-bs4.min.css') ?>">
<style>
    .select2-container .select2-selection--multiple .select2-selection__choice {
        background-color: #007bff;
        border-color: #006fe6;
        color: #fff;
        padding: 0 10px;
        margin-top: 0.31rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <?= view('layouts/partials/_alert') ?>

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="btn-group">
                <a href="<?= site_url('ordini-materiale/edit/' . $ordine['id']) ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Modifica
                </a>

                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                    <i class="fas fa-trash"></i> Elimina
                </button>

                <a href="<?= site_url('pdf/openOrdineMateriale/' . $ordine['id']) ?>" class="btn btn-info" target="_blank">
                    <i class="fas fa-file-pdf"></i> Genera PDF
                </a>

                <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-cog"></i> Azioni
                    </button>
                    <div class="dropdown-menu">
                        <h6 class="dropdown-header">Gestione Stato</h6>
                        <a class="dropdown-item" href="<?= site_url('ordini-materiale/cambia-stato/' . $ordine['id']) ?>?stato=in_attesa">Imposta In Attesa</a>
                        <a class="dropdown-item" href="<?= site_url('ordini-materiale/cambia-stato/' . $ordine['id']) ?>?stato=inviato">Imposta come Inviato</a>
                        <a class="dropdown-item" href="<?= site_url('ordini-materiale/cambia-stato/' . $ordine['id']) ?>?stato=completato">Imposta come Completato</a>
                        <a class="dropdown-item" href="<?= site_url('ordini-materiale/cambia-stato/' . $ordine['id']) ?>?stato=confermato">Imposta come Confermato</a>
                        <a class="dropdown-item" href="<?= site_url('ordini-materiale/cambia-stato/' . $ordine['id']) ?>?stato=in_consegna">Imposta come In Consegna</a>
                        <a class="dropdown-item" href="<?= site_url('ordini-materiale/cambia-stato/' . $ordine['id']) ?>?stato=consegnato">Imposta come Consegnato</a>
                        <a class="dropdown-item" href="<?= site_url('ordini-materiale/cambia-stato/' . $ordine['id']) ?>?stato=annullato">Annulla Ordine</a>
                        <div class="dropdown-divider"></div>
                        <h6 class="dropdown-header">Comunicazioni</h6>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalPreviewEmail">
                            <i class="fas fa-envelope"></i> Invia Email
                        </a>
                        <a class="dropdown-item" href="<?= site_url('ordini-materiale/email-log/' . $ordine['id']) ?>">
                            <i class="fas fa-history"></i> Storico Email
                        </a>
                        <div class="dropdown-divider"></div>
                        <h6 class="dropdown-header">Gestione Voci</h6>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#addVoceModal">
                            <i class="fas fa-plus"></i> Aggiungi Articolo
                        </a>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#importaVociModal">
                            <i class="fas fa-file-import"></i> Importa da Offerta
                        </a>
                        <div class="dropdown-divider"></div>
                        <h6 class="dropdown-header">Gestione Costi</h6>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#scontoTrasportoModal">
                            <i class="fas fa-percentage"></i> Modifica Sconto e Trasporto
                        </a>
                    </div>
                </div>

                <a href="<?= site_url('ordini-materiale') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Torna all'elenco
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Informazioni Ordine -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informazioni Ordine</h3>
                    <div class="card-tools">
                        <span class="badge badge-<?php
                                                    switch ($ordine['stato']) {
                                                        case 'bozza':
                                                            echo 'secondary';
                                                            break;
                                                        case 'confermato':
                                                            echo 'success';
                                                            break;
                                                        case 'in_attesa':
                                                            echo 'warning';
                                                            break;
                                                        case 'inviato':
                                                            echo 'info';
                                                            break;
                                                        case 'completato':
                                                            echo 'primary';
                                                            break;
                                                        case 'annullato':
                                                            echo 'danger';
                                                            break;
                                                        case 'in_consegna':
                                                            echo 'warning';
                                                            break;
                                                        case 'consegnato':
                                                            echo 'success';
                                                            break;
                                                        default:
                                                            echo 'secondary';
                                                    }
                                                    ?>">
                            <?php
                            switch ($ordine['stato']) {
                                case 'bozza':
                                    echo 'Bozza';
                                    break;
                                case 'confermato':
                                    echo 'Confermato';
                                    break;
                                case 'in_attesa':
                                    echo 'In Attesa';
                                    break;
                                case 'inviato':
                                    echo 'Inviato';
                                    break;
                                case 'completato':
                                    echo 'Completato';
                                    break;
                                case 'annullato':
                                    echo 'Annullato';
                                    break;
                                case 'in_consegna':
                                    echo 'In Consegna';
                                    break;
                                case 'consegnato':
                                    echo 'Consegnato';
                                    break;
                                default:
                                    echo ucfirst($ordine['stato']);
                            }
                            ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Numero:</strong> <?= esc($ordine['numero']) ?></p>
                            <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($ordine['data'])) ?></p>
                            <p><strong>Oggetto:</strong> <?= esc($ordine['oggetto']) ?></p>
                            <?php if (!empty($ordine['descrizione'])): ?>
                                <p><strong>Descrizione:</strong> <?= esc($ordine['descrizione']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($ordine['note'])): ?>
                                <p><strong>Note:</strong> <?= nl2br(esc($ordine['note'])) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($ordine['riferimento_offerta'])): ?>
                                <p><strong>Riferimento Offerta:</strong> <?= esc($ordine['riferimento_offerta']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Valuta:</strong> <?= esc($ordine['valuta'] ?? 'EUR') ?></p>
                            <p><strong>Importo Totale:</strong> <?= number_format($ordine['importo_totale'] ?? 0, 2, ',', '.') ?> <?= esc($ordine['valuta'] ?? 'EUR') ?></p>
                            <?php
                            $importoVoci = 0;
                            foreach ($voci as $voce) {
                                $importoVoci += $voce['importo'];
                            }
                            ?>
                            <p><strong>Importo Totale Voci:</strong>
                                <?= number_format($importoVoci, 2, ',', '.') ?> <?= esc($ordine['valuta'] ?? 'EUR') ?>
                            </p>
                            <?php if (!empty($ordine['sconto_totale']) && $ordine['sconto_totale'] > 0): ?>
                                <p><strong>Sconto Totale (<?= number_format($ordine['sconto_totale'], 2, ',', '.') ?>%):</strong>
                                    <?= number_format($importoVoci * ($ordine['sconto_totale'] / 100), 2, ',', '.') ?> <?= esc($ordine['valuta'] ?? 'EUR') ?>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($ordine['sconto_fisso']) && $ordine['sconto_fisso'] > 0): ?>
                                <p><strong>Sconto Fisso:</strong>
                                    <?= number_format($ordine['sconto_fisso'], 2, ',', '.') ?> <?= esc($ordine['valuta'] ?? 'EUR') ?>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($ordine['costo_trasporto']) && $ordine['costo_trasporto'] > 0): ?>
                                <p><strong>Costo Trasporto:</strong>
                                    <?= number_format($ordine['costo_trasporto'], 2, ',', '.') ?> <?= esc($ordine['valuta'] ?? 'EUR') ?>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($ordine['data_consegna_prevista'])): ?>
                                <p><strong>Data Consegna Prevista:</strong> <?= date('d/m/Y', strtotime($ordine['data_consegna_prevista'])) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($ordine['data_consegna_effettiva'])): ?>
                                <p><strong>Data Consegna Effettiva:</strong> <?= date('d/m/Y', strtotime($ordine['data_consegna_effettiva'])) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($ordine['nome_progetto'])): ?>
                                <p><strong>Progetto:</strong> <?= esc($ordine['nome_progetto']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($ordine['condizioni_pagamento'])): ?>
                                <p><strong>Condizioni di Pagamento:</strong> <?= esc($ordine['condizioni_pagamento']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($ordine['condizioni_consegna'])): ?>
                                <p><strong>Condizioni di Consegna:</strong> <?= esc($ordine['condizioni_consegna']) ?></p>
                            <?php endif; ?>
                            <p><strong>Creato da:</strong> <?= esc($ordine['nome_utente'] . ' ' . $ordine['cognome_utente']) ?></p>
                            <p><strong>Data Creazione:</strong> <?= date('d/m/Y H:i', strtotime($ordine['created_at'])) ?></p>
                            <?php if (!empty($ordine['data_invio'])): ?>
                                <p><strong>Data Invio:</strong> <?= date('d/m/Y H:i', strtotime($ordine['data_invio'])) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Voci Ordine -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Articoli Ordine</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-toggle="modal" data-target="#addVoceModal">
                            <i class="fas fa-plus"></i> Aggiungi Articolo
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Descrizione</th>
                                    <th>Quantità</th>
                                    <th>Prezzo Unitario</th>
                                    <th>Sconto</th>
                                    <th>Importo</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($voci)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Nessun articolo presente</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($voci as $index => $voce): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <?= esc($voce['descrizione']) ?>
                                                <?php if (!empty($voce['codice'])): ?>
                                                    <small class="text-muted d-block">Codice: <?= esc($voce['codice']) ?></small>
                                                <?php endif; ?>
                                                <?php if (!empty($voce['id_materiale']) && !empty($voce['nome_materiale'])): ?>
                                                    <small class="text-primary d-block">Materiale collegato: <?= esc($voce['nome_materiale']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= number_format($voce['quantita'], 2, ',', '.') ?> <?= esc($voce['unita_misura']) ?></td>
                                            <td><?= number_format($voce['prezzo_unitario'], 2, ',', '.') ?> <?= esc($ordine['valuta'] ?? 'EUR') ?></td>
                                            <td><?= !empty($voce['sconto']) ? number_format($voce['sconto'], 2, ',', '.') . '%' : '-' ?></td>
                                            <td><?= number_format($voce['importo'], 2, ',', '.') ?> <?= esc($ordine['valuta'] ?? 'EUR') ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-primary edit-voce-btn"
                                                        data-id="<?= $voce['id'] ?>"
                                                        data-descrizione="<?= esc($voce['descrizione']) ?>"
                                                        data-codice="<?= esc($voce['codice']) ?>"
                                                        data-quantita="<?= $voce['quantita'] ?>"
                                                        data-unita-misura="<?= esc($voce['unita_misura']) ?>"
                                                        data-prezzo-unitario="<?= $voce['prezzo_unitario'] ?>"
                                                        data-sconto="<?= $voce['sconto'] ?? 0 ?>"
                                                        data-importo="<?= $voce['importo'] ?>"
                                                        data-materiale-id="<?= $voce['id_materiale'] ?? '' ?>"
                                                        data-progetto-id="<?= $voce['id_progetto'] ?? '' ?>"
                                                        data-toggle="modal" data-target="#editVoceModal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="<?= site_url('ordini-materiale/rimuovi-materiale/' . $ordine['id'] . '/' . $voce['id']) ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Sei sicuro di voler eliminare questo articolo?');">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-right">Totale:</th>
                                    <th><?= number_format($ordine['importo_totale'] ?? 0, 2, ',', '.') ?> <?= esc($ordine['valuta'] ?? 'EUR') ?></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="text-right mt-2 pb-2 pr-2">
                        <button type="button" id="btnAggiornaImporto" class="btn btn-sm btn-primary">
                            <i class="fas fa-sync"></i> Aggiorna Totale
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Fornitore -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Fornitore</h3>
                </div>
                <div class="card-body">
                    <h5><?= esc($ordine['nome_fornitore']) ?></h5>
                    <p>
                        <?php if (!empty($ordine['indirizzo'])): ?>
                            <?= esc($ordine['indirizzo']) ?><br>
                            <?= esc($ordine['cap']) ?> <?= esc($ordine['citta']) ?><br>
                            <?= esc($ordine['nazione']) ?><br>
                        <?php endif; ?>

                        <?php if (!empty($ordine['partita_iva']) || !empty($ordine['codice_fiscale'])): ?>
                            <?php if (!empty($ordine['partita_iva'])): ?>P.IVA: <?= esc($ordine['partita_iva']) ?><br><?php endif; ?>
                        <?php if (!empty($ordine['codice_fiscale'])): ?>C.F.: <?= esc($ordine['codice_fiscale']) ?><br><?php endif; ?>
                <?php endif; ?>

                <?php if (!empty($ordine['email']) || !empty($ordine['telefono'])): ?>
                    <?php if (!empty($ordine['email'])): ?>Email: <?= esc($ordine['email']) ?><br><?php endif; ?>
                <?php if (!empty($ordine['telefono'])): ?>Tel: <?= esc($ordine['telefono']) ?><?php endif; ?>
            <?php endif; ?>
                    </p>

                    <?php if (!empty($ordine['nome_referente'])): ?>
                        <h6 class="mt-3">Referente</h6>
                        <p>
                            <?= esc($ordine['nome_referente'] . ' ' . $ordine['cognome_referente']) ?><br>
                            <?php if (!empty($ordine['email_referente'])): ?>Email: <?= esc($ordine['email_referente']) ?><br><?php endif; ?>
                        <?php if (!empty($ordine['telefono_referente'])): ?>Tel: <?= esc($ordine['telefono_referente']) ?><?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Stato Consegna -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Stato Consegna</h3>
                </div>
                <div class="card-body">
                    <p>
                        <strong>Stato Attuale:</strong>
                        <span class="badge badge-<?php
                                                    switch ($ordine['stato']) {
                                                        case 'bozza':
                                                            echo 'secondary';
                                                            break;
                                                        case 'confermato':
                                                            echo 'success';
                                                            break;
                                                        case 'in_attesa':
                                                            echo 'warning';
                                                            break;
                                                        case 'inviato':
                                                            echo 'info';
                                                            break;
                                                        case 'completato':
                                                            echo 'primary';
                                                            break;
                                                        case 'annullato':
                                                            echo 'danger';
                                                            break;
                                                        case 'in_consegna':
                                                            echo 'warning';
                                                            break;
                                                        case 'consegnato':
                                                            echo 'success';
                                                            break;
                                                        default:
                                                            echo 'secondary';
                                                    }
                                                    ?>">
                            <?php
                            switch ($ordine['stato']) {
                                case 'bozza':
                                    echo 'Bozza';
                                    break;
                                case 'confermato':
                                    echo 'Confermato';
                                    break;
                                case 'in_attesa':
                                    echo 'In Attesa';
                                    break;
                                case 'inviato':
                                    echo 'Inviato';
                                    break;
                                case 'completato':
                                    echo 'Completato';
                                    break;
                                case 'annullato':
                                    echo 'Annullato';
                                    break;
                                case 'in_consegna':
                                    echo 'In Consegna';
                                    break;
                                case 'consegnato':
                                    echo 'Consegnato';
                                    break;
                                default:
                                    echo ucfirst($ordine['stato']);
                            }
                            ?>
                        </span>
                    </p>

                    <p><strong>Data Consegna Prevista:</strong>
                        <?= !empty($ordine['data_consegna_prevista']) ? date('d/m/Y', strtotime($ordine['data_consegna_prevista'])) : 'Non specificata' ?>
                    </p>

                    <?php if (!empty($ordine['data_consegna_effettiva'])): ?>
                        <p><strong>Data Consegna Effettiva:</strong> <?= date('d/m/Y', strtotime($ordine['data_consegna_effettiva'])) ?></p>
                    <?php endif; ?>

                    <?php
                    // Calcolo ritardo/anticipo
                    if (!empty($ordine['data_consegna_prevista']) && !empty($ordine['data_consegna_effettiva'])) {
                        $dataPrevista = new DateTime($ordine['data_consegna_prevista']);
                        $dataEffettiva = new DateTime($ordine['data_consegna_effettiva']);
                        $diff = $dataPrevista->diff($dataEffettiva);
                        $giorni = $diff->days;

                        if ($dataEffettiva > $dataPrevista) {
                            echo '<p class="text-danger"><strong>Ritardo:</strong> ' . $giorni . ' giorni</p>';
                        } else if ($dataEffettiva < $dataPrevista) {
                            echo '<p class="text-success"><strong>Anticipo:</strong> ' . $giorni . ' giorni</p>';
                        } else {
                            echo '<p class="text-success"><strong>Consegna puntuale</strong></p>';
                        }
                    }
                    ?>
                </div>
            </div>

            <!-- Allegati -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Allegati</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-toggle="modal" data-target="#addAllegatoModal">
                            <i class="fas fa-plus"></i> Aggiungi Allegato
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php if (empty($allegati)): ?>
                            <li class="list-group-item text-center">Nessun allegato presente</li>
                        <?php else: ?>
                            <?php foreach ($allegati as $allegato): ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <a href="<?= site_url('ordini-materiale/download-allegato/' . $allegato['id']) ?>">
                                                <i class="fas fa-file mr-2"></i> <?= esc($allegato['nome_file']) ?>
                                            </a>
                                            <?php if (!empty($allegato['descrizione'])): ?>
                                                <small class="text-muted d-block"><?= esc($allegato['descrizione']) ?></small>
                                            <?php endif; ?>
                                            <small class="text-muted">
                                                Caricato il <?= date('d/m/Y H:i', strtotime($allegato['created_at'])) ?>
                                            </small>
                                        </div>
                                        <div>
                                            <a href="javascript:void(0)"
                                                class="btn btn-sm btn-danger delete-allegato-btn"
                                                data-id="<?= $allegato['id'] ?>"
                                                data-nome="<?= esc($allegato['nome_file']) ?>">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Elimina Ordine -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Conferma Eliminazione</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Sei sicuro di voler eliminare questo ordine di acquisto?<br>
                <strong><?= esc($ordine['numero']) ?> - <?= esc($ordine['oggetto']) ?></strong>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <a href="<?= site_url('ordini-materiale/delete/' . $ordine['id']) ?>" class="btn btn-danger">Elimina</a>
            </div>
        </div>
    </div>
</div>

<!-- Placeholder per i modali delle funzionalità (da implementare in seguito) -->
<div class="modal fade" id="addVoceModal" tabindex="-1" role="dialog" aria-labelledby="addVoceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVoceModalLabel">Aggiungi Articolo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Tabs per scegliere tra ricerca e nuovo materiale -->
                <ul class="nav nav-tabs" id="materialeTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="search-tab" data-toggle="tab" href="#search-content" role="tab" aria-controls="search-content" aria-selected="true">
                            <i class="fas fa-search"></i> Cerca Materiale
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="new-tab" data-toggle="tab" href="#new-content" role="tab" aria-controls="new-content" aria-selected="false">
                            <i class="fas fa-plus"></i> Nuovo Materiale
                        </a>
                    </li>
                </ul>

                <div class="tab-content pt-3" id="materialeTabsContent">
                    <!-- Tab per la ricerca materiali -->
                    <div class="tab-pane fade show active" id="search-content" role="tabpanel" aria-labelledby="search-tab">
                        <!-- Form di ricerca materiali -->
                        <div class="form-group">
                            <label for="searchMateriale">Cerca Materiale:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchMateriale" placeholder="Digita codice o descrizione del materiale...">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="btnCercaMateriale">
                                        <i class="fas fa-search"></i> Cerca
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Risultati della ricerca -->
                        <div id="risultatiRicerca" class="mt-3">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Codice</th>
                                            <th>Descrizione</th>
                                            <th>Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody id="materialiRisultati">
                                        <tr>
                                            <td colspan="3" class="text-center">Effettua una ricerca per visualizzare i risultati</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tab per nuovo materiale -->
                    <div class="tab-pane fade" id="new-content" role="tabpanel" aria-labelledby="new-tab">
                        <form id="formNuovoMateriale" action="<?= site_url('ordini-materiale/aggiungi-nuovo-materiale/' . $ordine['id']) ?>" method="post" novalidate>
                            <?= csrf_field() ?>

                            <!-- Area per messaggi di debug -->
                            <div id="debugErrorContainer" class="alert alert-danger" style="display:none;">
                                <h5><i class="icon fas fa-exclamation-triangle"></i> Errore</h5>
                                <div id="debugErrorMessage"></div>
                                <pre id="debugErrorDetails" style="max-height: 200px; overflow: auto;"></pre>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="codice">Codice*:</label>
                                        <input type="text" class="form-control" id="codice" name="codice" required>
                                        <div class="invalid-feedback">
                                            Il codice è obbligatorio
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="quantita">Quantità*:</label>
                                        <input type="number" class="form-control" id="quantitaNuovo" name="quantita" min="0.01" step="0.01" required>
                                        <div class="invalid-feedback">
                                            La quantità deve essere maggiore di zero
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="descrizione">Descrizione*:</label>
                                <textarea class="form-control" id="descrizione" name="descrizione" rows="2" required></textarea>
                                <div class="invalid-feedback">
                                    La descrizione è obbligatoria
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="prezzo_unitario">Prezzo Unitario*:</label>
                                        <input type="number" class="form-control" id="prezzo_unitario" name="prezzo_unitario" step="0.01" min="0" required>
                                        <div class="invalid-feedback">
                                            Il prezzo unitario è obbligatorio
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sconto">Sconto (%):</label>
                                        <input type="number" class="form-control" id="sconto" name="sconto" step="0.01" min="0" max="100">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="unitaMisuraNuovo">Unità di Misura*:</label>
                                        <select class="form-control" id="unitaMisuraNuovo" name="unita_misura" required>
                                            <option value="pz">pz - Pezzi</option>
                                            <option value="m">m - Metri</option>
                                            <option value="kg">kg - Chilogrammi</option>
                                            <option value="g">g - Grammi</option>
                                            <option value="cm">cm - Centimetri</option>
                                            <option value="mm">mm - Millimetri</option>
                                            <option value="l">l - Litri</option>
                                            <option value="ml">ml - Millilitri</option>
                                            <option value="m2">m² - Metri quadri</option>
                                            <option value="m3">m³ - Metri cubi</option>
                                            <option value="h">h - Ore</option>
                                            <option value="gg">gg - Giorni</option>
                                            <option value="set">set - Set</option>
                                            <option value="cf">cf - Confezione</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Seleziona un'unità di misura
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="idProgettoNuovo">Progetto (opzionale):</label>
                                        <select class="form-control" id="idProgettoNuovo" name="id_progetto">
                                            <option value="">Nessun progetto</option>
                                            <?php if (isset($progetti) && count($progetti) > 0): ?>
                                                <?php foreach ($progetti as $progetto): ?>
                                                    <option value="<?= $progetto['id'] ?>"><?= esc($progetto['nome']) ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer px-0 pb-0">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                <button type="submit" class="btn btn-success" id="btnSalvaNuovoMateriale">Salva e Aggiungi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editVoceModal" tabindex="-1" role="dialog" aria-labelledby="editVoceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editVoceModalLabel">Modifica Articolo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formModificaVoce" action="<?= site_url('ordini-materiale/aggiorna-materiale/' . $ordine['id']) ?>" method="post" novalidate>
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <input type="hidden" id="editId" name="id">
                    <input type="hidden" id="editMaterialeId" name="id_materiale">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editCodice">Codice*:</label>
                                <input type="text" class="form-control" id="editCodice" name="codice" required>
                                <div class="invalid-feedback">
                                    Il codice è obbligatorio
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editQuantita">Quantità*:</label>
                                <input type="number" class="form-control" id="editQuantita" name="quantita" min="0.01" step="0.01" required>
                                <div class="invalid-feedback">
                                    La quantità deve essere maggiore di zero
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="editDescrizione">Descrizione*:</label>
                        <textarea class="form-control" id="editDescrizione" name="descrizione" rows="2" required></textarea>
                        <div class="invalid-feedback">
                            La descrizione è obbligatoria
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editPrezzoUnitario">Prezzo Unitario*:</label>
                                <input type="number" class="form-control" id="editPrezzoUnitario" name="prezzo_unitario" step="0.01" min="0" required>
                                <div class="invalid-feedback">
                                    Il prezzo unitario è obbligatorio
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editSconto">Sconto (%):</label>
                                <input type="number" class="form-control" id="editSconto" name="sconto" step="0.01" min="0" max="100">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editUnitaMisura">Unità di Misura*:</label>
                                <select class="form-control" id="editUnitaMisura" name="unita_misura" required>
                                    <option value="pz">pz - Pezzi</option>
                                    <option value="m">m - Metri</option>
                                    <option value="kg">kg - Chilogrammi</option>
                                    <option value="g">g - Grammi</option>
                                    <option value="cm">cm - Centimetri</option>
                                    <option value="mm">mm - Millimetri</option>
                                    <option value="l">l - Litri</option>
                                    <option value="ml">ml - Millilitri</option>
                                    <option value="m2">m² - Metri quadri</option>
                                    <option value="m3">m³ - Metri cubi</option>
                                    <option value="h">h - Ore</option>
                                    <option value="gg">gg - Giorni</option>
                                    <option value="set">set - Set</option>
                                    <option value="cf">cf - Confezione</option>
                                </select>
                                <div class="invalid-feedback">
                                    Seleziona un'unità di misura
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editIdProgetto">Progetto (opzionale):</label>
                                <select class="form-control" id="editIdProgetto" name="id_progetto">
                                    <option value="">Nessun progetto</option>
                                    <?php if (isset($progetti) && count($progetti) > 0): ?>
                                        <?php foreach ($progetti as $progetto): ?>
                                            <option value="<?= $progetto['id'] ?>"><?= esc($progetto['nome']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Salva Modifiche</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal per specificare la quantità del materiale selezionato -->
<div class="modal fade" id="modalQuantitaMateriale" tabindex="-1" role="dialog" aria-labelledby="modalQuantitaMaterialeLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalQuantitaMaterialeLabel">Specifica Quantità e Prezzo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAggiungiMateriale" action="<?= site_url('ordini-materiale/aggiungi-materiale/' . $ordine['id']) ?>" method="post" novalidate>
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <input type="hidden" id="idMateriale" name="id_materiale">

                    <div class="form-group">
                        <label for="quantita">Quantità*:</label>
                        <input type="number" class="form-control" id="quantita" name="quantita" min="0.01" step="0.01" required>
                        <div class="invalid-feedback">
                            La quantità deve essere maggiore di zero
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="prezzo_unitario_mat">Prezzo Unitario*:</label>
                        <input type="number" class="form-control" id="prezzo_unitario_mat" name="prezzo_unitario" step="0.01" min="0" required>
                        <div class="invalid-feedback">
                            Il prezzo unitario è obbligatorio
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="sconto_mat">Sconto (%):</label>
                        <input type="number" class="form-control" id="sconto_mat" name="sconto" step="0.01" min="0" max="100">
                    </div>

                    <div class="form-group">
                        <label for="unitaMisura">Unità di misura*:</label>
                        <select class="form-control" id="unitaMisura" name="unita_misura" required>
                            <option value="pz">Pezzi (pz)</option>
                            <option value="kg">Kilogrammi (kg)</option>
                            <option value="g">Grammi (g)</option>
                            <option value="m">Metri (m)</option>
                            <option value="cm">Centimetri (cm)</option>
                            <option value="mm">Millimetri (mm)</option>
                            <option value="l">Litri (l)</option>
                            <option value="ml">Millilitri (ml)</option>
                            <option value="m2">Metri quadri (m²)</option>
                            <option value="m3">Metri cubi (m³)</option>
                            <option value="h">Ore (h)</option>
                            <option value="gg">Giorni (gg)</option>
                            <option value="set">Set</option>
                            <option value="cf">Confezione (cf)</option>
                        </select>
                        <div class="invalid-feedback">
                            Seleziona un'unità di misura
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="idProgetto">Progetto (opzionale):</label>
                        <select class="form-control" id="idProgetto" name="id_progetto">
                            <option value="">Nessun progetto</option>
                            <?php if (isset($progetti) && count($progetti) > 0): ?>
                                <?php foreach ($progetti as $progetto): ?>
                                    <option value="<?= $progetto['id'] ?>"><?= esc($progetto['nome']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Aggiungi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Aggiungi Allegato -->
<div class="modal fade" id="addAllegatoModal" tabindex="-1" role="dialog" aria-labelledby="addAllegatoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAllegatoModalLabel">Aggiungi Allegato</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= site_url('ordini-materiale/carica-allegato/' . $ordine['id']) ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="allegato">File*</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="allegato" name="allegato" required>
                            <label class="custom-file-label" for="allegato">Scegli file...</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="descrizione_allegato">Descrizione</label>
                        <textarea class="form-control" id="descrizione_allegato" name="descrizione" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Carica</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal per Sconto Totale e Costo Trasporto -->
<div class="modal fade" id="scontoTrasportoModal" tabindex="-1" role="dialog" aria-labelledby="scontoTrasportoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scontoTrasportoModalLabel">Modifica Sconto e Trasporto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= site_url('ordini-materiale/aggiorna-costi/' . $ordine['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="sconto_totale">Sconto Totale (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" max="100" class="form-control" id="sconto_totale" name="sconto_totale" value="<?= esc($ordine['sconto_totale'] ?? 0) ?>">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <small class="form-text text-muted">Sconto percentuale applicato al totale delle voci</small>
                    </div>
                    <div class="form-group">
                        <label for="sconto_fisso">Sconto Fisso</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" class="form-control" id="sconto_fisso" name="sconto_fisso" value="<?= esc($ordine['sconto_fisso'] ?? 0) ?>">
                            <div class="input-group-append">
                                <span class="input-group-text"><?= esc($ordine['valuta'] ?? 'EUR') ?></span>
                            </div>
                        </div>
                        <small class="form-text text-muted">Sconto fisso da sottrarre al totale</small>
                    </div>
                    <div class="form-group">
                        <label for="costo_trasporto">Costo Trasporto</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" class="form-control" id="costo_trasporto" name="costo_trasporto" value="<?= esc($ordine['costo_trasporto'] ?? 0) ?>">
                            <div class="input-group-append">
                                <span class="input-group-text"><?= esc($ordine['valuta'] ?? 'EUR') ?></span>
                            </div>
                        </div>
                        <small class="form-text text-muted">Costo di spedizione o trasporto da aggiungere al totale</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal per la preview dell'email -->
<div class="modal fade" id="modalPreviewEmail" tabindex="-1" role="dialog" aria-labelledby="modalPreviewEmailLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPreviewEmailLabel">Anteprima Email</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formInviaEmail" action="<?= site_url('ordini-materiale/invia-email/' . $ordine['id']) ?>" method="post" enctype="multipart/form-data" novalidate>
                <div class="modal-body">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label for="templateEmail">Template Email:</label>
                        <div class="input-group">
                            <select class="form-control" id="templateEmail" name="template_id">
                                <option value="">Seleziona un template...</option>
                                <?php if (isset($emailTemplates) && !empty($emailTemplates)): ?>
                                    <?php foreach ($emailTemplates as $template): ?>
                                        <option value="<?= $template['id'] ?>"><?= esc($template['nome']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary" id="btnRicaricaTemplate">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Seleziona un template per caricare automaticamente oggetto e corpo dell'email</small>
                    </div>

                    <div class="form-group">
                        <label for="emailDestinatario">Destinatario:</label>
                        <select class="form-control select2-tags" id="emailDestinatario" name="destinatario[]" multiple required>
                            <?php if (!empty($contattoPrincipale) && !empty($contattoPrincipale['email'])): ?>
                                <option value="<?= $contattoPrincipale['email'] ?>" selected>
                                    <?= $contattoPrincipale['nome'] ?> <?= $contattoPrincipale['cognome'] ?> (<?= $contattoPrincipale['email'] ?>)
                                </option>
                            <?php endif; ?>

                            <?php if (!empty($contatti)): ?>
                                <?php foreach ($contatti as $contatto): ?>
                                    <?php if (!empty($contatto['email']) && (empty($contattoPrincipale) || $contatto['id'] != $contattoPrincipale['id'])): ?>
                                        <option value="<?= $contatto['email'] ?>">
                                            <?= $contatto['nome'] ?> <?= $contatto['cognome'] ?> (<?= $contatto['email'] ?>)
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="emailCC">CC (opzionale):</label>
                                <select class="form-control select2-tags" id="emailCC" name="cc[]" multiple>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="emailCCN">CCN (opzionale):</label>
                                <select class="form-control select2-tags" id="emailCCN" name="ccn[]" multiple>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="emailOggetto">Oggetto:</label>
                        <input type="text" class="form-control" id="emailOggetto" name="oggetto" value="Ordine di Acquisto <?= esc($ordine['numero']) ?> - <?= esc($ordine['oggetto']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="emailCorpo">Corpo dell'email:</label>
                        <textarea class="form-control summernote" id="emailCorpo" name="corpo" rows="6" required>Gentile <?= esc($ordine['nome_fornitore']) ?>,

In allegato inviamo l'ordine di acquisto n. <?= esc($ordine['numero']) ?> relativo a "<?= esc($ordine['oggetto']) ?>".

<?php if (!empty($ordine['data_consegna_prevista'])): ?>
La consegna è prevista per il giorno <?= date('d/m/Y', strtotime($ordine['data_consegna_prevista'])) ?>.
<?php endif; ?>

Cordiali saluti,
<?= esc(session()->get('utente_nome') . ' ' . session()->get('utente_cognome')) ?>
</textarea>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Articoli Ordine</h6>
                            <div class="custom-control custom-checkbox float-right">
                                <input type="checkbox" class="custom-control-input" id="mostraTabellaEmail" checked>
                                <label class="custom-control-label" for="mostraTabellaEmail">Mostra tabella articoli</label>
                            </div>
                        </div>
                        <div class="card-body p-0" id="tabellaEmailMateriali">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Codice</th>
                                            <th>Descrizione</th>
                                            <th>Quantità</th>
                                            <th>Prezzo</th>
                                            <th>Totale</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($voci) && count($voci) > 0): ?>
                                            <?php foreach ($voci as $voce): ?>
                                                <tr>
                                                    <td><?= esc($voce['codice']) ?></td>
                                                    <td><?= esc($voce['descrizione']) ?></td>
                                                    <td><?= number_format($voce['quantita'], 2, ',', '.') ?> <?= esc($voce['unita_misura']) ?></td>
                                                    <td><?= number_format($voce['prezzo_unitario'], 2, ',', '.') ?> <?= esc($ordine['valuta'] ?? 'EUR') ?></td>
                                                    <td><?= number_format($voce['importo'], 2, ',', '.') ?> <?= esc($ordine['valuta'] ?? 'EUR') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <tr>
                                                <td colspan="4" class="text-right"><strong>Totale:</strong></td>
                                                <td><?= number_format($ordine['importo_totale'], 2, ',', '.') ?> <?= esc($ordine['valuta'] ?? 'EUR') ?></td>
                                            </tr>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center">Nessun articolo associato a questo ordine</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="includiPDF" name="allega_pdf" value="1" checked>
                            <label class="custom-control-label" for="includiPDF">Includi PDF dell'ordine</label>
                        </div>
                    </div>

                    <!-- Aggiungiamo un campo nascosto per indicare se la tabella dei materiali è mostrata -->
                    <input type="hidden" name="tabella_materiali" id="tabellaMaterialiField" value="1">

                    <div class="form-group">
                        <label>Allegati aggiuntivi (opzionale):</label>
                        <input type="file" name="allegati[]" class="form-control-file" multiple>
                        <small class="form-text text-muted">Puoi selezionare più file. Formati supportati: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF, ZIP, RAR</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane"></i> Invia Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Email Dettaglio -->
<div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailModalLabel">Dettaglio Email</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Dettagli email -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Informazioni Email</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Data invio:</strong> <span id="emailDataInvio"></span></p>
                                        <p><strong>Stato:</strong> <span id="emailStato"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Destinatario:</strong> <span id="emailDestinatarioDettaglio"></span></p>
                                        <div id="emailCCRow" style="display:none">
                                            <p><strong>CC:</strong> <span id="emailCCDettaglio"></span></p>
                                        </div>
                                        <div id="emailCCNRow" style="display:none">
                                            <p><strong>CCN:</strong> <span id="emailCCNDettaglio"></span></p>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <p><strong>Oggetto:</strong> <span id="emailOggettoDettaglio"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Corpo email -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Corpo Email</h3>
                            </div>
                            <div class="card-body p-0">
                                <iframe id="emailCorpoFrame" style="width:100%; height:300px; border:none;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sezione allegati, visibile solo se ci sono allegati -->
                <div class="row mt-3" id="emailAllegatiRow">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Allegati</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-group" id="emailAllegatiList">
                                    <!-- Gli allegati verranno aggiunti qui dinamicamente -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal per importare voci da offerte -->
<div class="modal fade" id="importaVociModal" tabindex="-1" role="dialog" aria-labelledby="importaVociModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importaVociModalLabel">Importa Voci da Offerta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="searchOfferta">Cerca Offerta:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchOfferta" placeholder="Digita numero o oggetto dell'offerta...">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="btnCercaOfferta">
                                <i class="fas fa-search"></i> Cerca
                            </button>
                        </div>
                    </div>
                </div>

                <div id="risultatiOfferte" class="mt-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Numero</th>
                                    <th>Data</th>
                                    <th>Oggetto</th>
                                    <th>Fornitore</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody id="offerteRisultati">
                                <tr>
                                    <td colspan="5" class="text-center">Effettua una ricerca per visualizzare le offerte</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal per visualizzare le voci dell'offerta selezionata -->
<div class="modal fade" id="vociOffertaModal" tabindex="-1" role="dialog" aria-labelledby="vociOffertaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vociOffertaModalLabel">Voci Offerta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="selectAllVoci">
                                        <label class="custom-control-label" for="selectAllVoci"></label>
                                    </div>
                                </th>
                                <th>Codice</th>
                                <th>Descrizione</th>
                                <th>Quantità</th>
                                <th>Prezzo Unitario</th>
                                <th>Sconto</th>
                                <th>Importo</th>
                            </tr>
                        </thead>
                        <tbody id="vociOffertaList">
                            <!-- Le voci verranno caricate qui dinamicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary" id="btnImportaVociSelezionate">
                    <i class="fas fa-file-import"></i> Importa Voci Selezionate
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/select2/js/select2.full.min.js') ?>"></script>
<script src="<?= base_url('plugins/bs-custom-file-input/bs-custom-file-input.min.js') ?>"></script>
<script src="<?= base_url('plugins/summernote/summernote-bs4.min.js') ?>"></script>
<script src="<?= base_url('plugins/summernote/lang/summernote-it-IT.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        // Inizializza Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        // Inizializza Select2 con tags per gli indirizzi email
        $('.select2-tags').select2({
            theme: 'bootstrap4',
            width: '100%',
            tags: true,
            tokenSeparators: [',', ' '],
            createTag: function(params) {
                var term = $.trim(params.term);

                // Ignora gli spazi vuoti
                if (term === '') {
                    return null;
                }

                // Verifica se è un indirizzo email valido
                var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
                if (!emailPattern.test(term)) {
                    // Non è un indirizzo email valido - non creare un tag
                    return null;
                }

                return {
                    id: term,
                    text: term,
                    newTag: true
                }
            }
        });

        // Inizializza plugin per i file input
        bsCustomFileInput.init();

        // Inizializzazione Summernote se presente
        if ($.fn.summernote) {
            $('.summernote').summernote({
                lang: 'it-IT',
                height: 250,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'italic', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                placeholder: 'Scrivi il corpo dell\'email qui...',
                disableDragAndDrop: true
            });
        }

        // Gestione checkbox mostra tabella materiali
        $('#mostraTabellaEmail').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('#tabellaMaterialiField').val(isChecked ? '1' : '0');
            if (isChecked) {
                $('#tabellaEmailMateriali').show();
            } else {
                $('#tabellaEmailMateriali').hide();
            }
        });

        // Caricamento template email al cambio della selezione
        $('#templateEmail').on('change', function() {
            const templateId = $(this).val();
            if (!templateId) return;

            $.ajax({
                url: '<?= site_url('email-templates/compila-ordine') ?>/' + templateId,
                type: 'POST',
                dataType: 'json',
                data: {
                    id_ordine: '<?= $ordine['id'] ?>',
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                beforeSend: function() {
                    // Disabilitiamo i controlli durante il caricamento
                    $('#templateEmail').prop('disabled', true);
                    $('#btnRicaricaTemplate').prop('disabled', true);
                },
                success: function(response) {
                    if (response.success && response.data) {
                        // Debugging - visualizza i dati della risposta
                        console.log('Risposta compilazione template:', response);

                        // Popola i campi dell'email con i dati del template compilato
                        $('#emailOggetto').val(response.data.oggetto);

                        // Se stiamo usando summernote
                        if ($.fn.summernote) {
                            $('#emailCorpo').summernote('code', response.data.corpo);
                        } else {
                            $('#emailCorpo').val(response.data.corpo);
                        }

                        // Verifica esplicita se il corpo contiene il segnaposto dei materiali
                        if (response.data.corpo.indexOf('{{materiali}}') !== -1) {
                            console.log('Segnaposto {{materiali}} trovato, nascondiamo la tabella');
                            $('#mostraTabellaEmail').prop('checked', false).trigger('change');
                        }

                        // Aggiungi debugging per verificare cosa contiene il corpo
                        console.log('Corpo template ricevuto:', response.data.corpo);
                    } else {
                        console.error('Errore nel caricamento del template:', response.message || 'Risposta non valida');
                        alert('Errore nel caricamento del template: ' + (response.message || 'Errore sconosciuto'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Errore AJAX nel caricamento del template:', error);
                    console.error('Dettagli risposta:', xhr.responseText);
                    alert('Errore nel caricamento del template. Verifica la console per dettagli.');
                },
                complete: function() {
                    // Riabilitiamo i controlli
                    $('#templateEmail').prop('disabled', false);
                    $('#btnRicaricaTemplate').prop('disabled', false);
                }
            });
        });

        // Ricarica template email
        $('#btnRicaricaTemplate').click(function() {
            $(this).prop('disabled', true);
            $('#templateEmail').prop('disabled', true);

            $.ajax({
                url: '<?= site_url('email-templates/get-by-type/ORDINE') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#templateEmail').empty();
                    $('#templateEmail').append('<option value="">Seleziona un template...</option>');

                    if (Array.isArray(response) && response.length > 0) {
                        $.each(response, function(i, template) {
                            $('#templateEmail').append('<option value="' + template.id + '">' + template.nome + '</option>');
                        });
                    } else if (response.success && Array.isArray(response.data) && response.data.length > 0) {
                        $.each(response.data, function(i, template) {
                            $('#templateEmail').append('<option value="' + template.id + '">' + template.nome + '</option>');
                        });
                    } else {
                        $('#templateEmail').append('<option value="" disabled>Nessun template disponibile</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Errore nel caricamento dei template:', error);
                    $('#templateEmail').append('<option value="" disabled>Errore nel caricamento dei template</option>');
                },
                complete: function() {
                    $('#templateEmail').prop('disabled', false);
                    $('#btnRicaricaTemplate').prop('disabled', false);
                }
            });
        });

        // Visualizzazione dettaglio email
        $('.visualizzaEmail').on('click', function() {
            var id = $(this).data('id');
            var oggetto = $(this).data('oggetto');
            var corpo = $(this).data('corpo');
            var destinatario = $(this).data('destinatario');
            var cc = $(this).data('cc');
            var ccn = $(this).data('ccn');
            var data = $(this).data('data');
            var stato = $(this).data('stato');
            var allegati = $(this).data('allegati');

            $('#emailDataInvio').text(data);
            $('#emailDestinatarioDettaglio').text(destinatario);
            $('#emailOggettoDettaglio').text(oggetto);

            // Gestione CC
            if (cc && cc !== '') {
                $('#emailCCDettaglio').text(cc);
                $('#emailCCRow').show();
            } else {
                $('#emailCCRow').hide();
            }

            // Gestione CCN
            if (ccn && ccn !== '') {
                $('#emailCCNDettaglio').text(ccn);
                $('#emailCCNRow').show();
            } else {
                $('#emailCCNRow').hide();
            }

            // Gestione stato
            if (stato === 'inviato') {
                $('#emailStato').html('<span class="badge badge-success">Inviata</span>');
            } else {
                $('#emailStato').html('<span class="badge badge-danger">Errore</span>');
            }

            // Visualizza il corpo dell'email in un iframe
            var iframe = document.getElementById('emailCorpoFrame');
            iframe.contentWindow.document.open();
            iframe.contentWindow.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><style>body { font-family: Arial, sans-serif; padding: 10px; }</style></head><body>' + corpo + '</body></html>');
            iframe.contentWindow.document.close();

            // Gestione allegati
            $('#emailAllegatiList').empty();
            if (allegati && allegati !== '') {
                try {
                    let allegatiArray = [];

                    if (typeof allegati === 'object' && Array.isArray(allegati)) {
                        allegatiArray = allegati;
                    } else {
                        let allegatiString = allegati.toString().trim();

                        if (allegatiString.startsWith('"') && allegatiString.endsWith('"')) {
                            allegatiString = allegatiString.substring(1, allegatiString.length - 1);
                        }

                        try {
                            allegatiArray = JSON.parse(allegatiString);
                        } catch (parseError) {
                            allegatiArray = [allegatiString];
                        }
                    }

                    if (allegatiArray && allegatiArray.length > 0) {
                        allegatiArray.forEach(function(allegato) {
                            if (allegato) {
                                $('#emailAllegatiList').append('<li class="list-group-item d-flex justify-content-between align-items-center">' +
                                    '<span><i class="fas fa-paperclip"></i> ' + allegato + '</span>' +
                                    '</li>');
                            }
                        });
                        $('#emailAllegatiRow').show();
                    } else {
                        $('#emailAllegatiRow').hide();
                    }
                } catch (e) {
                    console.error('Errore nel processing degli allegati:', e);
                    $('#emailAllegatiRow').hide();
                }
            } else {
                $('#emailAllegatiRow').hide();
            }

            // Mostra il modal
            $('#emailModal').modal('show');
        });

        // Gestione della conferma di cambio stato dell'ordine
        $('.dropdown-menu a[href*="cambia-stato"]').click(function(e) {
            e.preventDefault();

            const link = $(this).attr('href');
            const stato = link.split('stato=')[1];
            let statoLabel = '';
            let icon = 'question';
            let confirmButtonColor = '#3085d6';

            // Imposta messaggio e colore in base allo stato
            switch (stato) {
                case 'bozza':
                    statoLabel = 'Bozza';
                    icon = 'info';
                    confirmButtonColor = '#6c757d';
                    break;
                case 'confermato':
                    statoLabel = 'Confermato';
                    icon = 'success';
                    confirmButtonColor = '#28a745';
                    break;
                case 'in_attesa':
                    statoLabel = 'In Attesa';
                    icon = 'warning';
                    confirmButtonColor = '#ffc107';
                    break;
                case 'inviato':
                    statoLabel = 'Inviato';
                    icon = 'info';
                    confirmButtonColor = '#17a2b8';
                    break;
                case 'completato':
                    statoLabel = 'Completato';
                    icon = 'success';
                    confirmButtonColor = '#007bff';
                    break;
                case 'in_consegna':
                    statoLabel = 'In Consegna';
                    icon = 'info';
                    confirmButtonColor = '#ffc107';
                    break;
                case 'consegnato':
                    statoLabel = 'Consegnato';
                    icon = 'success';
                    confirmButtonColor = '#28a745';
                    break;
                case 'annullato':
                    statoLabel = 'Annullato';
                    icon = 'warning';
                    confirmButtonColor = '#dc3545';
                    break;
                default:
                    statoLabel = stato;
                    icon = 'question';
                    confirmButtonColor = '#3085d6';
            }

            Swal.fire({
                title: 'Cambio stato',
                text: 'Vuoi impostare lo stato dell\'ordine a "' + statoLabel + '"?',
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: confirmButtonColor,
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sì, cambia stato',
                cancelButtonText: 'Annulla'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = link;
                }
            });
        });

        // Gestione della conferma di eliminazione allegato
        $('.delete-allegato-btn').click(function() {
            const idAllegato = $(this).data('id');
            const nomeAllegato = $(this).data('nome');

            Swal.fire({
                title: 'Conferma eliminazione',
                text: 'Sei sicuro di voler eliminare l\'allegato "' + nomeAllegato + '"?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sì, elimina',
                cancelButtonText: 'Annulla'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= site_url('ordini-materiale/delete-allegato/') ?>' + idAllegato;
                }
            });
        });

        // Ricerca materiali
        $('#btnCercaMateriale').click(function() {
            cercaMateriali();
        });

        $('#searchMateriale').keypress(function(e) {
            if (e.which == 13) {
                cercaMateriali();
                e.preventDefault();
            }
        });

        // Funzione per cercare i materiali
        function cercaMateriali() {
            const searchTerm = $('#searchMateriale').val();
            if (searchTerm.length < 2) {
                Swal.fire({
                    title: 'Attenzione',
                    text: 'Inserisci almeno 2 caratteri per la ricerca',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            $.ajax({
                url: '<?= site_url('materiali/search') ?>',
                type: 'GET',
                data: {
                    term: searchTerm
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#materialiRisultati').html('<tr><td colspan="3" class="text-center"><i class="fas fa-spinner fa-spin"></i> Ricerca in corso...</td></tr>');
                },
                success: function(response) {
                    $('#materialiRisultati').empty();

                    if (response.length === 0) {
                        $('#materialiRisultati').html('<tr><td colspan="3" class="text-center">Nessun materiale trovato</td></tr>');
                        return;
                    }

                    $.each(response, function(index, materiale) {
                        $('#materialiRisultati').append(`
                        <tr>
                            <td>${materiale.codice}</td>
                            <td>${materiale.descrizione}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-success selezionaMateriale" 
                                        data-id="${materiale.id}" 
                                        data-dismiss="modal"
                                        data-toggle="modal" 
                                        data-target="#modalQuantitaMateriale">
                                    <i class="fas fa-plus"></i> Seleziona
                                </button>
                            </td>
                        </tr>
                    `);
                    });

                    // Evento per selezionare un materiale
                    $('.selezionaMateriale').click(function() {
                        const idMateriale = $(this).data('id');
                        $('#idMateriale').val(idMateriale);
                        $('#addVoceModal').modal('hide');
                        setTimeout(function() {
                            $('#modalQuantitaMateriale').modal('show');
                        }, 500);
                    });
                },
                error: function(xhr, status, error) {
                    $('#materialiRisultati').html('<tr><td colspan="3" class="text-center text-danger">Errore durante la ricerca. Riprova.</td></tr>');
                    console.error(error);
                }
            });
        }

        // Evento per modificare una voce
        $('.edit-voce-btn').click(function() {
            const id = $(this).data('id');
            const descrizione = $(this).data('descrizione');
            const codice = $(this).data('codice');
            const quantita = $(this).data('quantita');
            const unitaMisura = $(this).data('unita-misura');
            const prezzoUnitario = $(this).data('prezzo-unitario');
            const sconto = $(this).data('sconto');
            const materialeId = $(this).data('materiale-id');
            const progettoId = $(this).data('progetto-id');

            $('#editId').val(id);
            $('#editDescrizione').val(descrizione);
            $('#editCodice').val(codice);
            $('#editQuantita').val(quantita);
            $('#editUnitaMisura').val(unitaMisura);
            $('#editPrezzoUnitario').val(prezzoUnitario);
            $('#editSconto').val(sconto);
            $('#editMaterialeId').val(materialeId);
            $('#editIdProgetto').val(progettoId);

            $('#editVoceModal').modal('show');
        });

        // Validazione del form di aggiunta materiale
        $('#formAggiungiMateriale').on('submit', function(e) {
            const form = this;
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();

                // Evidenzia gli errori
                $(form).find(':input').each(function() {
                    if (!this.validity.valid) {
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                return false;
            }

            return true;
        });

        // Validazione del form di modifica voce
        $('#formModificaVoce').on('submit', function(e) {
            const form = this;
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();

                // Evidenzia gli errori
                $(form).find(':input').each(function() {
                    if (!this.validity.valid) {
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                return false;
            }

            return true;
        });

        // Gestione AJAX del form nuovo materiale con debug
        $('#formNuovoMateriale').on('submit', function(e) {
            e.preventDefault();

            // Validazione del form
            const form = this;
            if (!form.checkValidity()) {
                // Evidenzia gli errori
                $(form).find(':input').each(function() {
                    if (!this.validity.valid) {
                        $(this).addClass('is-invalid');
                        console.log('Campo non valido:', this.name, 'Valore:', this.value, 'ValidityState:', this.validity);
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                $('#debugErrorContainer').show();
                $('#debugErrorMessage').text('Errore di validazione del form. Controlla i campi obbligatori.');
                $('#debugErrorDetails').text('Form non valido. Controlla i campi evidenziati in rosso.');

                return false;
            }

            // Reset dei messaggi di errore
            $('#debugErrorContainer').hide();

            // Debug dei dati che verranno inviati
            const formData = new FormData(form);
            const formValues = {};
            for (let [key, value] of formData.entries()) {
                formValues[key] = value;
            }
            console.log('Dati form inviati:', formValues);

            // Disabilita il pulsante di invio durante la richiesta
            $('#btnSalvaNuovoMateriale').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Salvataggio...');

            // Invia il form tramite AJAX
            $.ajax({
                url: $(form).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Risposta server (success):', response);

                    try {
                        // Se la risposta è già un oggetto
                        if (typeof response === 'object') {
                            handleResponse(response);
                        }
                        // Se la risposta è una stringa JSON
                        else if (typeof response === 'string' && response.trim().startsWith('{')) {
                            handleResponse(JSON.parse(response));
                        }
                        // Se la risposta è HTML (probabilmente un redirect)
                        else {
                            if (response.includes('success-message')) {
                                // È probabilmente una pagina di successo - ricarichiamo la pagina
                                window.location.reload();
                            } else {
                                // Debug della risposta HTML
                                $('#debugErrorContainer').show();
                                $('#debugErrorMessage').text('Risposta HTML non prevista dal server');
                                $('#debugErrorDetails').text(response.substring(0, 500) + '...');
                                console.log('Risposta HTML completa:', response);
                            }
                        }
                    } catch (e) {
                        console.error('Errore nel parsing della risposta:', e);
                        $('#debugErrorContainer').show();
                        $('#debugErrorMessage').text('Errore nell\'elaborazione della risposta');
                        $('#debugErrorDetails').text('Errore: ' + e.message + '\nRisposta: ' + response.substring(0, 500) + '...');
                    }

                    // Riabilita il pulsante di invio
                    $('#btnSalvaNuovoMateriale').prop('disabled', false).text('Salva e Aggiungi');
                },
                error: function(xhr, status, error) {
                    console.error('Errore AJAX:', {
                        xhr,
                        status,
                        error
                    });

                    $('#debugErrorContainer').show();
                    $('#debugErrorMessage').text('Errore durante il salvataggio');

                    // Cerco di estrarre informazioni dettagliate dall'errore
                    let errorDetails = '';

                    try {
                        if (xhr.responseJSON) {
                            errorDetails = JSON.stringify(xhr.responseJSON, null, 2);
                        } else if (xhr.responseText) {
                            // Se la risposta è HTML, mostra solo le prime righe
                            if (xhr.responseText.trim().startsWith('<!DOCTYPE html>') ||
                                xhr.responseText.trim().startsWith('<html')) {
                                const parser = new DOMParser();
                                const htmlDoc = parser.parseFromString(xhr.responseText, 'text/html');

                                // Prova a trovare un messaggio di errore nell'HTML
                                const errorElement = htmlDoc.querySelector('.error-message, .alert-danger');
                                if (errorElement) {
                                    errorDetails = "Errore dal server: " + errorElement.textContent;
                                } else {
                                    // Cerca il titolo della pagina di errore
                                    const title = htmlDoc.querySelector('title');
                                    if (title && title.textContent.includes('Error')) {
                                        errorDetails = "Titolo errore: " + title.textContent + "\n";

                                        // Cerca anche il body per eventuali dettagli
                                        const body = htmlDoc.querySelector('body');
                                        if (body) {
                                            errorDetails += "Dettagli: " + body.textContent.substring(0, 500);
                                        }
                                    } else {
                                        errorDetails = "Risposta HTML (prime 500 caratteri): " + xhr.responseText.substring(0, 500);
                                    }
                                }
                            } else {
                                errorDetails = xhr.responseText;
                            }
                        } else {
                            errorDetails = "Stato: " + status + "\nErrore: " + error;
                        }
                    } catch (e) {
                        errorDetails = "Errore nell'elaborazione della risposta: " + e.message;
                    }

                    $('#debugErrorDetails').text(errorDetails);

                    // Riabilita il pulsante di invio
                    $('#btnSalvaNuovoMateriale').prop('disabled', false).text('Salva e Aggiungi');
                }
            });

            // Funzione per gestire la risposta JSON
            function handleResponse(response) {
                if (response.success) {
                    // Operazione riuscita
                    window.location.reload();
                } else {
                    // Errore
                    $('#debugErrorContainer').show();
                    $('#debugErrorMessage').text(response.message || 'Errore durante il salvataggio');

                    if (response.errors) {
                        const errorDetails = typeof response.errors === 'object' ?
                            JSON.stringify(response.errors, null, 2) :
                            response.errors;
                        $('#debugErrorDetails').text(errorDetails);
                    } else {
                        $('#debugErrorDetails').text('Nessun dettaglio disponibile');
                    }
                }
            }
        });

        // Calcolo automatico dell'importo nei form
        function calcolaImporto(quantita, prezzoUnitario, sconto) {
            quantita = parseFloat(quantita) || 0;
            prezzoUnitario = parseFloat(prezzoUnitario) || 0;
            sconto = parseFloat(sconto) || 0;

            const importoSenzaSconto = quantita * prezzoUnitario;
            const importoSconto = importoSenzaSconto * (sconto / 100);
            return importoSenzaSconto - importoSconto;
        }

        // Calcolo automatico importo nel form di aggiunta
        $('#quantita, #prezzo_unitario_mat, #sconto_mat').on('change keyup', function() {
            const quantita = $('#quantita').val();
            const prezzoUnitario = $('#prezzo_unitario_mat').val();
            const sconto = $('#sconto_mat').val();

            const importo = calcolaImporto(quantita, prezzoUnitario, sconto);
            // Se volessi visualizzare l'importo in un campo
            // $('#importo').val(importo.toFixed(2));
        });

        // Calcolo automatico importo nel form di modifica
        $('#editQuantita, #editPrezzoUnitario, #editSconto').on('change keyup', function() {
            const quantita = $('#editQuantita').val();
            const prezzoUnitario = $('#editPrezzoUnitario').val();
            const sconto = $('#editSconto').val();

            const importo = calcolaImporto(quantita, prezzoUnitario, sconto);
            // Se volessi visualizzare l'importo in un campo
            // $('#editImporto').val(importo.toFixed(2));
        });

        // Calcolo automatico importo nel form nuovo materiale
        $('#quantitaNuovo, #prezzo_unitario, #sconto').on('change keyup', function() {
            const quantita = $('#quantitaNuovo').val();
            const prezzoUnitario = $('#prezzo_unitario').val();
            const sconto = $('#sconto').val();

            const importo = calcolaImporto(quantita, prezzoUnitario, sconto);
            // Se volessi visualizzare l'importo in un campo
            // $('#importoNuovo').val(importo.toFixed(2));
        });

        // Evento per aggiornare manualmente l'importo totale
        $('#btnAggiornaImporto').click(function() {
            const idOrdine = <?= $ordine['id'] ?>;
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Aggiornamento...');

            $.ajax({
                url: '<?= site_url('ordini-materiale/forza-aggiorna-importo/') ?>' + idOrdine,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Successo',
                            text: 'Importo totale aggiornato',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Errore',
                            text: response.message || 'Errore durante l\'aggiornamento dell\'importo',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    Swal.fire({
                        title: 'Errore',
                        text: 'Si è verificato un errore durante l\'aggiornamento dell\'importo',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                },
                complete: function() {
                    $('#btnAggiornaImporto').prop('disabled', false).html('<i class="fas fa-sync"></i> Aggiorna Totale');
                }
            });
        });
    });

    // Funzione per cercare le offerte
    function cercaOfferte() {
        const searchTerm = $('#searchOfferta').val();
        if (searchTerm.length < 2) {
            Swal.fire({
                title: 'Attenzione',
                text: 'Inserisci almeno 2 caratteri per la ricerca',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        $.ajax({
            url: '<?= site_url('offerte-fornitore/search') ?>',
            type: 'GET',
            data: {
                term: searchTerm
            },
            dataType: 'json',
            beforeSend: function() {
                $('#offerteRisultati').html('<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin"></i> Ricerca in corso...</td></tr>');
            },
            success: function(response) {
                $('#offerteRisultati').empty();

                if (response.length === 0) {
                    $('#offerteRisultati').html('<tr><td colspan="5" class="text-center">Nessuna offerta trovata</td></tr>');
                    return;
                }

                $.each(response, function(index, offerta) {
                    $('#offerteRisultati').append(`
                    <tr>
                        <td>${offerta.numero}</td>
                        <td>${offerta.data}</td>
                        <td>${offerta.oggetto}</td>
                        <td>${offerta.nome_fornitore}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary visualizzaVociOfferta" 
                                    data-id="${offerta.id}"
                                    data-numero="${offerta.numero}"
                                    data-toggle="modal" 
                                    data-target="#vociOffertaModal">
                                <i class="fas fa-list"></i> Visualizza Voci
                            </button>
                        </td>
                    </tr>
                `);
                });
            },
            error: function(xhr, status, error) {
                $('#offerteRisultati').html('<tr><td colspan="5" class="text-center text-danger">Errore durante la ricerca. Riprova.</td></tr>');
                console.error(error);
            }
        });
    }

    // Evento per il pulsante di ricerca offerte
    $('#btnCercaOfferta').click(function() {
        cercaOfferte();
    });

    // Evento per la ricerca offerte con tasto invio
    $('#searchOfferta').keypress(function(e) {
        if (e.which == 13) {
            cercaOfferte();
            e.preventDefault();
        }
    });

    // Evento per visualizzare le voci di un'offerta
    $(document).on('click', '.visualizzaVociOfferta', function() {
        const idOfferta = $(this).data('id');
        const numeroOfferta = $(this).data('numero');

        $('#vociOffertaModalLabel').text(`Voci Offerta ${numeroOfferta}`);

        $.ajax({
            url: '<?= site_url('offerte-fornitore/get-voci/') ?>' + idOfferta,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('#vociOffertaList').html('<tr><td colspan="7" class="text-center"><i class="fas fa-spinner fa-spin"></i> Caricamento voci...</td></tr>');
            },
            success: function(response) {
                $('#vociOffertaList').empty();

                if (response.length === 0) {
                    $('#vociOffertaList').html('<tr><td colspan="7" class="text-center">Nessuna voce presente nell\'offerta</td></tr>');
                    return;
                }

                $.each(response, function(index, voce) {
                    $('#vociOffertaList').append(`
                    <tr>
                        <td>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input voce-checkbox" 
                                       id="voce_${voce.id}" 
                                       data-id="${voce.id}"
                                       data-codice="${voce.codice}"
                                       data-descrizione="${voce.descrizione}"
                                       data-quantita="${voce.quantita}"
                                       data-prezzo="${voce.prezzo_unitario}"
                                       data-sconto="${voce.sconto || 0}"
                                       data-unita="${voce.unita_misura}">
                                <label class="custom-control-label" for="voce_${voce.id}"></label>
                            </div>
                        </td>
                        <td>${voce.codice}</td>
                        <td>${voce.descrizione}</td>
                        <td>${voce.quantita} ${voce.unita_misura}</td>
                        <td>${voce.prezzo_unitario} €</td>
                        <td>${voce.sconto || 0}%</td>
                        <td>${voce.importo} €</td>
                    </tr>
                `);
                });
            },
            error: function(xhr, status, error) {
                $('#vociOffertaList').html('<tr><td colspan="7" class="text-center text-danger">Errore durante il caricamento delle voci. Riprova.</td></tr>');
                console.error(error);
            }
        });
    });

    // Gestione checkbox "Seleziona tutti"
    $('#selectAllVoci').change(function() {
        $('.voce-checkbox').prop('checked', $(this).prop('checked'));
    });

    // Importazione voci selezionate
    $('#btnImportaVociSelezionate').click(function() {
        const vociSelezionate = [];

        $('.voce-checkbox:checked').each(function() {
            vociSelezionate.push({
                id: $(this).data('id'),
                codice: $(this).data('codice'),
                descrizione: $(this).data('descrizione'),
                quantita: $(this).data('quantita'),
                prezzo_unitario: $(this).data('prezzo'),
                sconto: $(this).data('sconto'),
                unita_misura: $(this).data('unita')
            });
        });

        if (vociSelezionate.length === 0) {
            Swal.fire({
                title: 'Attenzione',
                text: 'Seleziona almeno una voce da importare',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Disabilita il pulsante durante l'importazione
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Importazione...');

        $.ajax({
            url: '<?= site_url('ordini-materiale/importa-voci-offerta/') . $ordine['id'] ?>',
            type: 'POST',
            data: {
                voci: vociSelezionate,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Successo',
                        text: 'Voci importate con successo',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Errore',
                        text: response.message || 'Errore durante l\'importazione delle voci',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    title: 'Errore',
                    text: 'Errore durante l\'importazione delle voci',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                console.error(error);
            },
            complete: function() {
                // Riabilita il pulsante
                $('#btnImportaVociSelezionate').prop('disabled', false).html('<i class="fas fa-file-import"></i> Importa Voci Selezionate');
            }
        });
    });
</script>

<?= $this->endSection() ?>