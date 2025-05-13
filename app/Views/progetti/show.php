<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Dettagli Progetto<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Dettagli Progetto<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('progetti') ?>">Progetti</a></li>
<li class="breadcrumb-item active">Dettagli</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="card-title">
                                <?= esc($progetto['nome']) ?>
                                <?php if (!$progetto['attivo']) : ?>
                                    <span class="badge badge-danger">Disattivato</span>
                                <?php endif; ?>
                            </h3>
                        </div>
                        <div class="col-md-6 text-right">
                            <!-- <div class="btn-group btn-group-sm"> -->
                            <a href="<?= base_url('progetti/edit/' . $progetto['id']) ?>"
                                class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Modifica
                            </a>
                            <a href="<?= base_url('progetti/toggle-attivo/' . $progetto['id']) ?>"
                                class="btn <?= $progetto['attivo'] ? 'btn-warning' : 'btn-success' ?> btn-sm">
                                <i class="fas <?= $progetto['attivo'] ? 'fa-ban' : 'fa-check' ?>"></i>
                                <?= $progetto['attivo'] ? 'Disattiva' : 'Attiva' ?>
                            </a>
                            <a href="javascript:void(0)" class="btn btn-danger btn-elimina-progetto btn-sm"
                                data-id="<?= $progetto['id'] ?>" data-nome="<?= esc($progetto['nome']) ?>">
                                <i class="fas fa-trash"></i> Elimina
                            </a>
                            <!-- </div> -->
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Informazioni Generali</h5>
                                    <table class="table table-striped">
                                        <tr>
                                            <th>Nome:</th>
                                            <td><?= esc($progetto['nome']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Cliente:</th>
                                            <td>
                                                <?php if (isset($progetto['anagrafica'])) : ?>
                                                    <a
                                                        href="<?= base_url('anagrafiche/show/' . $progetto['id_anagrafica']) ?>">
                                                        <?= esc($progetto['anagrafica']['ragione_sociale']) ?>
                                                    </a>
                                                <?php else : ?>
                                                    <span class="text-muted">Non specificato</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Descrizione:</th>
                                            <td><?= $progetto['descrizione'] ? nl2br(esc($progetto['descrizione'])) : '<span class="text-muted">Non specificata</span>' ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Budget:</th>
                                            <td><?= $progetto['budget'] ? number_format($progetto['budget'], 2, ',', '.') . ' €' : '<span class="text-muted">Non specificato</span>' ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Date e Responsabili</h5>
                                    <table class="table table-striped">
                                        <tr>
                                            <th>Data Inizio:</th>
                                            <td>
                                                <?php if (!empty($progetto['data_inizio']) && $progetto['data_inizio'] !== '0000-00-00' && strtotime($progetto['data_inizio']) > 0) : ?>
                                                    <?= date('d/m/Y', strtotime($progetto['data_inizio'])) ?>
                                                <?php else : ?>
                                                    <span class="text-muted">Non specificata</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Data Scadenza:</th>
                                            <td>
                                                <?php if (!empty($progetto['data_scadenza']) && $progetto['data_scadenza'] !== '0000-00-00' && strtotime($progetto['data_scadenza']) > 0) : ?>
                                                    <?php
                                                    $scadenza = new DateTime($progetto['data_scadenza']);
                                                    $oggi = new DateTime();
                                                    $isExpired = $oggi > $scadenza;
                                                    $badgeClass = $isExpired ? 'badge-danger' : 'badge-success';
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>">
                                                        <?= date('d/m/Y', strtotime($progetto['data_scadenza'])) ?>
                                                    </span>
                                                <?php else : ?>
                                                    <span class="text-muted">Non specificata</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Data Fine:</th>
                                            <td>
                                                <?php if (!empty($progetto['data_fine']) && $progetto['data_fine'] !== '0000-00-00' && strtotime($progetto['data_fine']) > 0) : ?>
                                                    <?= date('d/m/Y', strtotime($progetto['data_fine'])) ?>
                                                <?php else : ?>
                                                    <span class="text-muted">Non specificata</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Creato da:</th>
                                            <td>
                                                <?php if (isset($progetto['creatore'])) : ?>
                                                    <?= esc($progetto['creatore']['nome']) ?>
                                                    <?= esc($progetto['creatore']['cognome']) ?>
                                                <?php else : ?>
                                                    <span class="text-muted">Non specificato</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Responsabile:</th>
                                            <td>
                                                <?php if (isset($progetto['responsabile'])) : ?>
                                                    <?= esc($progetto['responsabile']['nome']) ?>
                                                    <?= esc($progetto['responsabile']['cognome']) ?>
                                                <?php else : ?>
                                                    <span class="text-muted">Non specificato</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h5 class="card-title">Stato del Progetto</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Fase Kanban</h6>
                                            <?php
                                            $faseClass = '';
                                            switch ($progetto['fase_kanban']) {
                                                case 'backlog':
                                                    $faseClass = 'bg-secondary';
                                                    $faseText = 'Backlog';
                                                    break;
                                                case 'da_iniziare':
                                                    $faseClass = 'bg-info';
                                                    $faseText = 'Da Iniziare';
                                                    break;
                                                case 'in_corso':
                                                    $faseClass = 'bg-primary';
                                                    $faseText = 'In Corso';
                                                    break;
                                                case 'in_revisione':
                                                    $faseClass = 'bg-warning';
                                                    $faseText = 'In Revisione';
                                                    break;
                                                case 'completato':
                                                    $faseClass = 'bg-success';
                                                    $faseText = 'Completato';
                                                    break;
                                                default:
                                                    $faseClass = 'bg-secondary';
                                                    $faseText = ucfirst($progetto['fase_kanban']);
                                            }
                                            ?>
                                            <p><span class="badge <?= $faseClass ?> p-2"><?= $faseText ?></span></p>

                                            <form action="<?= base_url('progetti/fase-kanban/' . $progetto['id']) ?>"
                                                method="post" id="formFaseKanban">
                                                <?= csrf_field() ?>
                                                <div class="form-group">
                                                    <select class="form-control form-control-sm" name="fase_kanban"
                                                        id="changeFaseKanban">
                                                        <option value="backlog"
                                                            <?= $progetto['fase_kanban'] == 'backlog' ? 'selected' : '' ?>>
                                                            Backlog</option>
                                                        <option value="da_iniziare"
                                                            <?= $progetto['fase_kanban'] == 'da_iniziare' ? 'selected' : '' ?>>
                                                            Da Iniziare</option>
                                                        <option value="in_corso"
                                                            <?= $progetto['fase_kanban'] == 'in_corso' ? 'selected' : '' ?>>
                                                            In Corso</option>
                                                        <option value="in_revisione"
                                                            <?= $progetto['fase_kanban'] == 'in_revisione' ? 'selected' : '' ?>>
                                                            In Revisione</option>
                                                        <option value="completato"
                                                            <?= $progetto['fase_kanban'] == 'completato' ? 'selected' : '' ?>>
                                                            Completato</option>
                                                    </select>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Stato</h6>
                                            <?php
                                            $statoClass = '';
                                            switch ($progetto['stato']) {
                                                case 'in_corso':
                                                    $statoClass = 'bg-primary';
                                                    $statoText = 'In corso';
                                                    break;
                                                case 'completato':
                                                    $statoClass = 'bg-success';
                                                    $statoText = 'Completato';
                                                    break;
                                                case 'sospeso':
                                                    $statoClass = 'bg-warning';
                                                    $statoText = 'Sospeso';
                                                    break;
                                                case 'annullato':
                                                    $statoClass = 'bg-danger';
                                                    $statoText = 'Annullato';
                                                    break;
                                                default:
                                                    $statoClass = 'bg-secondary';
                                                    $statoText = 'Sconosciuto';
                                            }
                                            ?>
                                            <p><span class="badge <?= $statoClass ?> p-2"><?= $statoText ?></span></p>

                                            <form action="<?= base_url('progetti/stato/' . $progetto['id']) ?>"
                                                method="post" id="formStato">
                                                <?= csrf_field() ?>
                                                <div class="form-group">
                                                    <select class="form-control form-control-sm" name="stato"
                                                        id="changeStato">
                                                        <option value="in_corso"
                                                            <?= $progetto['stato'] == 'in_corso' ? 'selected' : '' ?>>In
                                                            Corso</option>
                                                        <option value="completato"
                                                            <?= $progetto['stato'] == 'completato' ? 'selected' : '' ?>>
                                                            Completato</option>
                                                        <option value="sospeso"
                                                            <?= $progetto['stato'] == 'sospeso' ? 'selected' : '' ?>>
                                                            Sospeso</option>
                                                        <option value="annullato"
                                                            <?= $progetto['stato'] == 'annullato' ? 'selected' : '' ?>>
                                                            Annullato</option>
                                                    </select>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <h6 class="mt-3">Priorità</h6>
                                    <?php
                                    $prioritaClass = '';
                                    switch ($progetto['priorita']) {
                                        case 'bassa':
                                            $prioritaClass = 'bg-success';
                                            break;
                                        case 'media':
                                            $prioritaClass = 'bg-info';
                                            break;
                                        case 'alta':
                                            $prioritaClass = 'bg-warning';
                                            break;
                                        case 'critica':
                                            $prioritaClass = 'bg-danger';
                                            break;
                                        default:
                                            $prioritaClass = 'bg-secondary';
                                    }
                                    ?>
                                    <p><span
                                            class="badge <?= $prioritaClass ?> p-2"><?= ucfirst($progetto['priorita']) ?></span>
                                    </p>

                                    <h6 class="mt-3">Creato il</h6>
                                    <p><?= date('d/m/Y H:i', strtotime($progetto['created_at'])) ?></p>

                                    <h6 class="mt-3">Ultimo aggiornamento</h6>
                                    <p><?= date('d/m/Y H:i', strtotime($progetto['updated_at'])) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progetto Padre -->
    <?php if (!empty($progetto['progetto_padre'])) : ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Progetto Padre</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Nome:</strong>
                                <a href="<?= base_url('progetti/' . $progetto['progetto_padre']['id']) ?>">
                                    <?= esc($progetto['progetto_padre']['nome']) ?>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <strong>Stato:</strong>
                                <span class="badge bg-<?= getStatoBadgeClass($progetto['progetto_padre']['stato']) ?>">
                                    <?= ucfirst($progetto['progetto_padre']['stato']) ?>
                                </span>
                            </div>
                            <div class="col-md-3">
                                <strong>Fase:</strong>
                                <span class="badge bg-info">
                                    <?= ucfirst($progetto['progetto_padre']['fase_kanban']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Sottoprogetti -->
    <?php if (!empty($progetto['sottoprogetti'])) : ?>
        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Sottoprogetti</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Stato</th>
                                        <th>Fase</th>
                                        <th>Scadenza</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($progetto['sottoprogetti'] as $sottoprogetto) : ?>
                                        <tr>
                                            <td>
                                                <a href="<?= base_url('progetti/' . $sottoprogetto['id']) ?>">
                                                    <?= esc($sottoprogetto['nome']) ?>
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= getStatoBadgeClass($sottoprogetto['stato']) ?>">
                                                    <?= ucfirst($sottoprogetto['stato']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?= ucfirst($sottoprogetto['fase_kanban']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= !empty($sottoprogetto['data_scadenza']) ? date('d/m/Y', strtotime($sottoprogetto['data_scadenza'])) : 'N/D' ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?= base_url('progetti/edit/' . $sottoprogetto['id']) ?>"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="javascript:void(0)"
                                                        class="btn btn-sm btn-danger btn-elimina-sottoprogetto"
                                                        data-id="<?= $sottoprogetto['id'] ?>"
                                                        data-nome="<?= esc($sottoprogetto['nome']) ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
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
    <?php endif; ?>

    <!-- Dettagli Progetto -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Materiali Associati</h3>
                        <div>
                            <!-- Dropdown per le opzioni di stampa -->
                            <div class="btn-group mr-2">
                                <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-print"></i> Stampa
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" id="btnStampaMateriali">
                                        <i class="fas fa-file-pdf"></i> Stampa lista materiali
                                    </a>
                                    <a class="dropdown-item" href="#" id="btnStampaBarcodes">
                                        <i class="fas fa-barcode"></i> Stampa etichette
                                    </a>
                                </div>
                            </div>
                            
                            <button type="button" class="btn btn-warning btn-sm mr-2" id="btnCreaRichiesta" disabled>
                                <i class="fas fa-file-invoice"></i> Crea Richiesta d'Offerta
                            </button>
                            
                            <!-- Dropdown per le opzioni di aggiunta materiali -->
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-plus"></i> Materiali
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAggiungiMateriale">
                                        <i class="fas fa-plus-circle"></i> Aggiungi materiale
                                    </a>
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalImportaExcel">
                                        <i class="fas fa-file-excel"></i> Importa da Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="materialiTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="vista-standard-tab" data-toggle="tab" href="#vista-standard"
                                role="tab" aria-controls="vista-standard" aria-selected="true">
                                Vista Standard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="vista-categorie-tab" data-toggle="tab" href="#vista-categorie"
                                role="tab" aria-controls="vista-categorie" aria-selected="false">
                                Vista per Categorie
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content pt-3" id="materialiTabContent">
                        <!-- Vista Standard -->
                        <div class="tab-pane fade show active" id="vista-standard" role="tabpanel"
                            aria-labelledby="vista-standard-tab">
                            <?php if (isset($materiali) && !empty($materiali)): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="icheck-primary">
                                                        <input type="checkbox" id="selectAllMateriali">
                                                        <label for="selectAllMateriali"></label>
                                                    </div>
                                                </th>
                                                <th>Codice</th>
                                                <th>Descrizione</th>
                                                <th>Produttore</th>
                                                <th>Quantità</th>
                                                <th>UM</th>
                                                <th>Categorie</th>
                                                <th>Note</th>
                                                <th>Azioni</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($materiali as $materiale): ?>
                                                <tr>
                                                    <td>
                                                        <div class="icheck-primary">
                                                            <input type="checkbox" class="materiale-checkbox"
                                                                id="materiale<?= $materiale['id'] ?>"
                                                                value="<?= $materiale['id'] ?>">
                                                            <label for="materiale<?= $materiale['id'] ?>"></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a
                                                            href="<?= base_url('materiali/show/' . $materiale['id_materiale']) ?>">
                                                            <?= esc($materiale['codice']) ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="<?= base_url('materiali/show/' . $materiale['id_materiale']) ?>"
                                                            class="text-dark">
                                                            <?= esc($materiale['descrizione']) ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="<?= base_url('materiali/show/' . $materiale['id_materiale']) ?>"
                                                            class="text-dark">
                                                            <?= esc($materiale['produttore']) ?>
                                                        </a>
                                                    </td>
                                                    <td class="text-right">
                                                        <a href="<?= base_url('materiali/show/' . $materiale['id_materiale']) ?>"
                                                            class="text-dark">
                                                            <?= number_format($materiale['quantita'], 2, ',', '.') ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="<?= base_url('materiali/show/' . $materiale['id_materiale']) ?>"
                                                            class="text-dark">
                                                            <?= esc($materiale['unita_misura']) ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <?php if (isset($materiale['commerciale']) && $materiale['commerciale']): ?>
                                                            <span class="badge badge-info">Commerciale</span>
                                                        <?php endif; ?>
                                                        <?php if (isset($materiale['meccanica']) && $materiale['meccanica']): ?>
                                                            <span class="badge badge-secondary">Meccanica</span>
                                                        <?php endif; ?>
                                                        <?php if (isset($materiale['elettrica']) && $materiale['elettrica']): ?>
                                                            <span class="badge badge-warning">Elettrica</span>
                                                        <?php endif; ?>
                                                        <?php if (isset($materiale['pneumatica']) && $materiale['pneumatica']): ?>
                                                            <span class="badge badge-primary">Pneumatica</span>
                                                        <?php endif; ?>
                                                        <?php if ((!isset($materiale['commerciale']) || !$materiale['commerciale']) &&
                                                            (!isset($materiale['meccanica']) || !$materiale['meccanica']) &&
                                                            (!isset($materiale['elettrica']) || !$materiale['elettrica']) &&
                                                            (!isset($materiale['pneumatica']) || !$materiale['pneumatica'])
                                                        ): ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= !empty($materiale['note']) ? esc($materiale['note']) : '<span class="text-muted">-</span>' ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-sm btn-primary editMateriale"
                                                                data-id="<?= $materiale['id'] ?>"
                                                                data-materiale-id="<?= $materiale['id_materiale'] ?>"
                                                                data-quantita="<?= $materiale['quantita'] ?>"
                                                                data-unita-misura="<?= $materiale['unita_misura'] ?>"
                                                                data-note="<?= esc($materiale['note'] ?? '') ?>">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <a href="<?= base_url('progetti/barcode-materiale/' . $progetto['id'] . '/' . $materiale['id']) ?>" 
                                                               class="btn btn-sm btn-info" target="_blank" title="Genera etichetta barcode">
                                                                <i class="fas fa-barcode"></i>
                                                            </a>
                                                            <a href="javascript:void(0)"
                                                                class="btn btn-sm btn-danger btn-rimuovi-materiale"
                                                                data-id="<?= $materiale['id'] ?>"
                                                                data-codice="<?= esc($materiale['codice']) ?>">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Nessun materiale associato a questo progetto.
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Vista per Categorie -->
                        <div class="tab-pane fade" id="vista-categorie" role="tabpanel"
                            aria-labelledby="vista-categorie-tab">
                            <?php if (isset($materiali) && !empty($materiali)):
                                // Prepariamo un array associativo per raggruppare i materiali per categoria
                                $materialiPerCategoria = [
                                    'commerciale' => [],
                                    'meccanica' => [],
                                    'elettrica' => [],
                                    'pneumatica' => [],
                                    'nessuna' => []
                                ];

                                // Raggruppiamo i materiali per categoria
                                foreach ($materiali as $materiale) {
                                    $assegnato = false;

                                    if (isset($materiale['commerciale']) && $materiale['commerciale']) {
                                        $materialiPerCategoria['commerciale'][] = $materiale;
                                        $assegnato = true;
                                    }
                                    if (isset($materiale['meccanica']) && $materiale['meccanica']) {
                                        $materialiPerCategoria['meccanica'][] = $materiale;
                                        $assegnato = true;
                                    }
                                    if (isset($materiale['elettrica']) && $materiale['elettrica']) {
                                        $materialiPerCategoria['elettrica'][] = $materiale;
                                        $assegnato = true;
                                    }
                                    if (isset($materiale['pneumatica']) && $materiale['pneumatica']) {
                                        $materialiPerCategoria['pneumatica'][] = $materiale;
                                        $assegnato = true;
                                    }

                                    if (!$assegnato) {
                                        $materialiPerCategoria['nessuna'][] = $materiale;
                                    }
                                }

                                // Definiamo i titoli e le classi delle categorie
                                $categorie = [
                                    'commerciale' => ['titolo' => 'Commerciale', 'classe' => 'info'],
                                    'meccanica' => ['titolo' => 'Meccanica', 'classe' => 'secondary'],
                                    'elettrica' => ['titolo' => 'Elettrica', 'classe' => 'warning'],
                                    'pneumatica' => ['titolo' => 'Pneumatica', 'classe' => 'primary'],
                                    'nessuna' => ['titolo' => 'Senza Categoria', 'classe' => 'light']
                                ];

                                // Output della tabella per ciascuna categoria che ha materiali
                                foreach ($categorie as $codiceCategoria => $categoria):
                                    if (empty($materialiPerCategoria[$codiceCategoria])) continue;
                            ?>
                                    <div class="card mb-4">
                                        <div class="card-header bg-<?= $categoria['classe'] ?> text-white">
                                            <h5 class="mb-0">
                                                <?= $categoria['titolo'] ?>
                                                <span
                                                    class="badge badge-light"><?= count($materialiPerCategoria[$codiceCategoria]) ?></span>
                                            </h5>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Codice</th>
                                                            <th>Descrizione</th>
                                                            <th>Produttore</th>
                                                            <th>Quantità</th>
                                                            <th>UM</th>
                                                            <th>Note</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($materialiPerCategoria[$codiceCategoria] as $materiale): ?>
                                                            <tr>
                                                                <td>
                                                                    <a
                                                                        href="<?= base_url('materiali/show/' . $materiale['id_materiale']) ?>">
                                                                        <?= esc($materiale['codice']) ?>
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <a href="<?= base_url('materiali/show/' . $materiale['id_materiale']) ?>"
                                                                        class="text-dark">
                                                                        <?= esc($materiale['descrizione']) ?>
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <a href="<?= base_url('materiali/show/' . $materiale['id_materiale']) ?>"
                                                                        class="text-dark">
                                                                        <?= esc($materiale['produttore']) ?>
                                                                    </a>
                                                                </td>
                                                                <td class="text-right">
                                                                    <a href="<?= base_url('materiali/show/' . $materiale['id_materiale']) ?>"
                                                                        class="text-dark">
                                                                        <?= number_format($materiale['quantita'], 2, ',', '.') ?>
                                                                    </a>
                                                                </td>
                                                                <td><?= esc($materiale['unita_misura']) ?></td>
                                                                <td><?= !empty($materiale['note']) ? esc($materiale['note']) : '<span class="text-muted">-</span>' ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                endforeach;
                            else: ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Nessun materiale associato a questo progetto.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gestione Documenti -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Documenti e Allegati</h3>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                            data-target="#uploadDocumentoModal">
                            <i class="fas fa-upload"></i> Carica documento
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($documenti ?? [])): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Nessun documento caricato per questo progetto.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome file</th>
                                        <th>Tipo</th>
                                        <th>Dimensione</th>
                                        <th>Caricato da</th>
                                        <th>Data caricamento</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($documenti as $doc): ?>
                                        <tr>
                                            <td><?= esc($doc['nome_originale']) ?></td>
                                            <td>
                                                <?php
                                                $estensione = pathinfo($doc['nome_originale'], PATHINFO_EXTENSION);
                                                $iconClass = 'fa-file';

                                                switch (strtolower($estensione)) {
                                                    case 'pdf':
                                                        $iconClass = 'fa-file-pdf';
                                                        break;
                                                    case 'doc':
                                                    case 'docx':
                                                        $iconClass = 'fa-file-word';
                                                        break;
                                                    case 'xls':
                                                    case 'xlsx':
                                                        $iconClass = 'fa-file-excel';
                                                        break;
                                                    case 'ppt':
                                                    case 'pptx':
                                                        $iconClass = 'fa-file-powerpoint';
                                                        break;
                                                    case 'jpg':
                                                    case 'jpeg':
                                                    case 'png':
                                                    case 'gif':
                                                        $iconClass = 'fa-file-image';
                                                        break;
                                                    case 'zip':
                                                    case 'rar':
                                                        $iconClass = 'fa-file-archive';
                                                        break;
                                                    case 'txt':
                                                        $iconClass = 'fa-file-alt';
                                                        break;
                                                }
                                                ?>
                                                <i class="fas <?= $iconClass ?>"></i> .<?= strtoupper($estensione) ?>
                                            </td>
                                            <td><?= formatFileSize($doc['dimensione']) ?></td>
                                            <td>
                                                <?php if (!empty($doc['utente']['nome']) && !empty($doc['utente']['cognome'])): ?>
                                                    <?= esc($doc['utente']['nome'] . ' ' . $doc['utente']['cognome']) ?>
                                                <?php elseif (!empty($doc['utente']['nome'])): ?>
                                                    <?= esc($doc['utente']['nome']) ?>
                                                <?php elseif (!empty($doc['utente']['cognome'])): ?>
                                                    <?= esc($doc['utente']['cognome']) ?>
                                                <?php else: ?>
                                                    Admin
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($doc['created_at'])) ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?= base_url('documenti/download/' . $doc['id']) ?>"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-warning" data-toggle="modal"
                                                        data-target="#editDocumentoModal" data-id="<?= $doc['id'] ?>"
                                                        data-nome="<?= esc($doc['nome_originale']) ?>"
                                                        data-descrizione="<?= esc($doc['descrizione'] ?? '') ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="javascript:void(0)"
                                                        class="btn btn-sm btn-danger btn-elimina-documento"
                                                        data-id="<?= $doc['id'] ?>"
                                                        data-nome="<?= esc($doc['nome_originale']) ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal per caricare un nuovo documento -->
<div class="modal fade" id="uploadDocumentoModal" tabindex="-1" role="dialog"
    aria-labelledby="uploadDocumentoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadDocumentoModalLabel">Carica Nuovo Documento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('documenti/upload') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="id_progetto" value="<?= $progetto['id'] ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="file">Seleziona file</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file" name="file" required>
                                <label class="custom-file-label" for="file">Scegli file</label>
                            </div>
                        </div>
                        <small class="form-text text-muted">Formati supportati: .pdf, .doc, .docx, .xls, .xlsx, .ppt,
                            .pptx, .jpg, .png, .zip, .rar, .txt (max 20MB)</small>
                    </div>
                    <div class="form-group">
                        <label for="descrizione">Descrizione (opzionale)</label>
                        <textarea class="form-control" id="descrizione" name="descrizione" rows="3"></textarea>
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

<!-- Modal per modificare un documento -->
<div class="modal fade" id="editDocumentoModal" tabindex="-1" role="dialog" aria-labelledby="editDocumentoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDocumentoModalLabel">Modifica Documento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('documenti/update') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id_documento" id="edit_id_documento">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_nome">Nome file</label>
                        <input type="text" class="form-control" id="edit_nome" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_descrizione">Descrizione (opzionale)</label>
                        <textarea class="form-control" id="edit_descrizione" name="descrizione" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Salva modifiche</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal per aggiungere materiale -->
<div class="modal fade" id="modalAggiungiMateriale" tabindex="-1" role="dialog"
    aria-labelledby="modalAggiungiMaterialeLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAggiungiMaterialeLabel">Aggiungi Materiale</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchMateriale"
                                placeholder="Cerca per codice o descrizione...">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="btnSearchMateriale">
                                    <i class="fas fa-search"></i> Cerca
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Codice</th>
                                <th>Descrizione</th>
                                <th>Produttore</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody id="materialiRisultati">
                            <tr>
                                <td colspan="4" class="text-center">Utilizza la ricerca per trovare materiali</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-12">
                        <h5>Aggiungi nuovo materiale</h5>
                        <p>Se il materiale non è presente nel database, puoi crearlo:</p>
                        <form id="formNuovoMateriale"
                            action="<?= base_url('progetti/aggiungi-nuovo-materiale/' . $progetto['id']) ?>"
                            method="post">
                            <?= csrf_field() ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="codiceMateriale">Codice *</label>
                                        <input type="text" class="form-control" id="codiceMateriale" name="codice"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="produttoreMateriale">Produttore</label>
                                        <input type="text" class="form-control" id="produttoreMateriale"
                                            name="produttore">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="descrizioneMateriale">Descrizione *</label>
                                <textarea class="form-control" id="descrizioneMateriale" name="descrizione" rows="2"
                                    required></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="quantitaMateriale">Quantità *</label>
                                        <input type="number" class="form-control" id="quantitaMateriale" name="quantita"
                                            min="0.01" step="0.01" value="1" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="unitaMisuraMateriale">Unità di misura</label>
                                        <select class="form-control" id="unitaMisuraMateriale" name="unita_misura">
                                            <option value="pz">Pezzi (pz)</option>
                                            <option value="kg">Chilogrammi (kg)</option>
                                            <option value="g">Grammi (g)</option>
                                            <option value="l">Litri (l)</option>
                                            <option value="ml">Millilitri (ml)</option>
                                            <option value="m">Metri (m)</option>
                                            <option value="cm">Centimetri (cm)</option>
                                            <option value="mm">Millimetri (mm)</option>
                                            <option value="m2">Metri quadri (m²)</option>
                                            <option value="m3">Metri cubi (m³)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Categorie</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="commercialeMateriale"
                                                name="commerciale" value="1">
                                            <label class="form-check-label"
                                                for="commercialeMateriale">Commerciale</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="meccanicaMateriale"
                                                name="meccanica" value="1">
                                            <label class="form-check-label" for="meccanicaMateriale">Meccanica</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="elettricaMateriale"
                                                name="elettrica" value="1">
                                            <label class="form-check-label" for="elettricaMateriale">Elettrica</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="pneumaticaMateriale"
                                                name="pneumatica" value="1">
                                            <label class="form-check-label" for="pneumaticaMateriale">Pneumatica</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="noteMateriale">Note</label>
                                <textarea class="form-control" id="noteMateriale" name="note" rows="2"></textarea>
                            </div>
                            <div class="modal-footer px-0 pb-0">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                                <button type="submit" class="btn btn-success">Salva e Aggiungi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal per quantità materiale -->
<div class="modal fade" id="modalQuantitaMateriale" tabindex="-1" role="dialog"
    aria-labelledby="modalQuantitaMaterialeLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalQuantitaMaterialeLabel">Specifica Quantità</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAggiungiMateriale" action="<?= base_url('progetti/aggiungi-materiale/' . $progetto['id']) ?>"
                method="post">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <input type="hidden" id="idMateriale" name="id_materiale">

                    <div class="form-group">
                        <label for="quantita">Quantità:</label>
                        <input type="number" class="form-control" id="quantita" name="quantita" min="0.01" step="0.01"
                            value="1" required>
                        <div class="invalid-feedback">
                            La quantità deve essere maggiore di zero
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="unitaMisura">Unità di misura:</label>
                        <select class="form-control" id="unitaMisura" name="unita_misura">
                            <option value="pz">Pezzi (pz)</option>
                            <option value="kg">Chilogrammi (kg)</option>
                            <option value="g">Grammi (g)</option>
                            <option value="l">Litri (l)</option>
                            <option value="ml">Millilitri (ml)</option>
                            <option value="m">Metri (m)</option>
                            <option value="cm">Centimetri (cm)</option>
                            <option value="mm">Millimetri (mm)</option>
                            <option value="m2">Metri quadri (m²)</option>
                            <option value="m3">Metri cubi (m³)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="note">Note (opzionale):</label>
                        <textarea class="form-control" id="note" name="note" rows="3"></textarea>
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

<!-- Modal per modificare quantità materiale -->
<div class="modal fade" id="modalModificaMateriale" tabindex="-1" role="dialog"
    aria-labelledby="modalModificaMaterialeLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalModificaMaterialeLabel">Modifica Materiale</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formModificaMateriale" action="<?= base_url('progetti/aggiorna-materiale/' . $progetto['id']) ?>"
                method="post">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <input type="hidden" id="editIdMateriale" name="id_materiale">
                    <input type="hidden" id="editId" name="id">

                    <div class="form-group">
                        <label for="editQuantita">Quantità:</label>
                        <input type="number" class="form-control" id="editQuantita" name="quantita" min="0.01"
                            step="0.01" required>
                        <div class="invalid-feedback">
                            La quantità deve essere maggiore di zero
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="editUnitaMisura">Unità di misura:</label>
                        <select class="form-control" id="editUnitaMisura" name="unita_misura">
                            <option value="pz">Pezzi (pz)</option>
                            <option value="kg">Chilogrammi (kg)</option>
                            <option value="g">Grammi (g)</option>
                            <option value="l">Litri (l)</option>
                            <option value="ml">Millilitri (ml)</option>
                            <option value="m">Metri (m)</option>
                            <option value="cm">Centimetri (cm)</option>
                            <option value="mm">Millimetri (mm)</option>
                            <option value="m2">Metri quadri (m²)</option>
                            <option value="m3">Metri cubi (m³)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="editNote">Note (opzionale):</label>
                        <textarea class="form-control" id="editNote" name="note" rows="3"></textarea>
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

<!-- Modal per la creazione della richiesta d'offerta -->
<div class="modal fade" id="modalCreaRichiesta" tabindex="-1" role="dialog" aria-labelledby="modalCreaRichiestaLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCreaRichiestaLabel">Crea Richiesta d'Offerta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formCreaRichiesta" action="<?= base_url('richieste-offerta/create-from-project') ?>"
                method="post">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_progetto" value="<?= $progetto['id'] ?>">
                    <input type="hidden" name="materiali_selezionati" id="materialiSelezionati">

                    <div class="form-group">
                        <label for="id_anagrafica">Fornitore*:</label>
                        <select class="form-control" id="id_anagrafica" name="id_anagrafica" required>
                            <option value="">Seleziona fornitore...</option>
                            <?php
                            // Carica i fornitori dal database utilizzando il modello
                            $anagraficheModel = new \App\Models\AnagraficaModel();
                            $fornitori = $anagraficheModel->where('fornitore', 1)->where('attivo', 1)->findAll();

                            foreach ($fornitori as $fornitore) {
                                echo '<option value="' . $fornitore['id'] . '">' . esc($fornitore['ragione_sociale']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="titoloRichiesta">Titolo richiesta*:</label>
                        <input type="text" class="form-control" id="titoloRichiesta" name="oggetto" required>
                    </div>

                    <div class="form-group">
                        <label for="descrizioneRichiesta">Descrizione:</label>
                        <textarea class="form-control" id="descrizioneRichiesta" name="descrizione" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="dataScadenzaRichiesta">Data scadenza:</label>
                        <input type="date" class="form-control" id="dataScadenzaRichiesta" name="data_scadenza">
                    </div>

                    <div class="form-group">
                        <label for="prioritaRichiesta">Priorità:</label>
                        <select class="form-control" id="prioritaRichiesta" name="priorita">
                            <option value="bassa">Bassa</option>
                            <option value="media" selected>Media</option>
                            <option value="alta">Alta</option>
                            <option value="critica">Critica</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Materiali selezionati:</label>
                        <div id="materialiRiepilogo" class="p-2 border rounded bg-light">
                            <p class="text-muted mb-0">Caricamento in corso...</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="noteRichiesta">Note:</label>
                        <textarea class="form-control" id="noteRichiesta" name="note" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Crea Richiesta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal per importazione Excel -->
<div class="modal fade" id="modalImportaExcel" tabindex="-1" role="dialog" aria-labelledby="modalImportaExcelLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImportaExcelLabel">Importa Materiali da Excel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formImportaExcel"
                    action="<?= base_url('progetti/importa-materiali-excel/' . $progetto['id']) ?>" method="post"
                    enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <!-- Step 1: Upload File -->
                    <div id="step1" class="import-step">
                        <h6>Step 1: Carica il file Excel</h6>
                        <div class="form-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="fileExcel" name="file"
                                    accept=".xlsx,.xls" required>
                                <label class="custom-file-label" for="fileExcel">Scegli file Excel...</label>
                            </div>
                            <small class="form-text text-muted">Formati supportati: .xlsx, .xls</small>
                        </div>
                        <button type="button" class="btn btn-primary" id="btnAnalizzaFile">Analizza File</button>
                    </div>

                    <!-- Step 2: Seleziona Foglio -->
                    <div id="step2" class="import-step" style="display: none;">
                        <h6>Step 2: Seleziona il foglio di lavoro</h6>
                        <div class="form-group">
                            <select class="form-control" id="selectFoglio" name="foglio">
                                <option value="">Seleziona un foglio...</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary" id="btnAnalizzaFoglio">Analizza Foglio</button>
                    </div>

                    <!-- Step 3: Mappa Colonne -->
                    <div id="step3" class="import-step" style="display: none;">
                        <h6>Step 3: Mappa le colonne</h6>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Seleziona le colonne che corrispondono ai campi richiesti
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="colonnaCodice">Colonna Codice</label>
                                    <select class="form-control" id="colonnaCodice" name="colonna_codice" required>
                                        <option value="">Seleziona colonna...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="colonnaDescrizione">Colonna Descrizione</label>
                                    <select class="form-control" id="colonnaDescrizione" name="colonna_descrizione"
                                        required>
                                        <option value="">Seleziona colonna...</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="colonnaMateriale">Colonna Materiale</label>
                                    <select class="form-control" id="colonnaMateriale" name="colonna_materiale">
                                        <option value="">Seleziona colonna...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="colonnaProduttore">Colonna Produttore</label>
                                    <select class="form-control" id="colonnaProduttore" name="colonna_produttore">
                                        <option value="">Seleziona colonna...</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="colonnaQuantita">Colonna Quantità</label>
                                    <select class="form-control" id="colonnaQuantita" name="colonna_quantita" required>
                                        <option value="">Seleziona colonna...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="colonnaUnitaMisura">Colonna Unità di Misura</label>
                                    <select class="form-control" id="colonnaUnitaMisura" name="colonna_unita_misura">
                                        <option value="">Seleziona colonna...</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="rigaInizio">Riga di inizio dati</label>
                            <input type="number" class="form-control" id="rigaInizio" name="riga_inizio" min="1"
                                value="2" required>
                            <small class="form-text text-muted">Inserisci il numero della riga da cui iniziano i dati
                                (esclusa l'intestazione)</small>
                        </div>
                    </div>

                    <!-- Step 4: Anteprima e Conferma -->
                    <div id="step4" class="import-step" style="display: none;">
                        <h6>Step 4: Anteprima e conferma</h6>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Verifica i dati prima di procedere con
                            l'importazione
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered" id="tabellaAnteprima">
                                <thead>
                                    <tr>
                                        <th>Codice</th>
                                        <th>Descrizione</th>
                                        <th>Materiale</th>
                                        <th>Produttore</th>
                                        <th>Quantità</th>
                                        <th>UM</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Popolato via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="skipDuplicati"
                                    name="skip_duplicati" checked>
                                <label class="custom-control-label" for="skipDuplicati">Salta materiali già presenti nel
                                    progetto</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary" id="btnPrevStep" style="display: none;">Indietro</button>
                <button type="button" class="btn btn-primary" id="btnNextStep" style="display: none;">Avanti</button>
                <button type="button" class="btn btn-success" id="btnImporta" style="display: none;">Importa</button>
            </div>
        </div>
    </div>
</div>

<!-- Form nascosto per la generazione di etichette barcode multiple -->
<form id="formGeneraBarcodes" action="<?= base_url('progetti/barcode-materiali/' . $progetto['id']) ?>" method="post" target="_blank">
    <?= csrf_field() ?>
    <input type="hidden" name="materiali_ids" id="materialiIdsBarcode">
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Cambio automatico della fase Kanban
        $('#changeFaseKanban').change(function() {
            $('#formFaseKanban').submit();
        });

        // Cambio automatico dello stato
        $('#changeStato').change(function() {
            $('#formStato').submit();
        });

        // Gestione del checkbox "seleziona tutti" per i materiali
        $('#selectAllMateriali').click(function() {
            $('.materiale-checkbox').prop('checked', this.checked);
            aggiornaBottoneRichiesta();
        });

        // Aggiorna il checkbox "seleziona tutti" quando cambiano i checkbox singoli
        $(document).on('click', '.materiale-checkbox', function() {
            const allChecked = $('.materiale-checkbox:checked').length === $('.materiale-checkbox').length;
            $('#selectAllMateriali').prop('checked', allChecked);
            aggiornaBottoneRichiesta();
        });

        // Funzione per abilitare/disabilitare il bottone della richiesta d'offerta
        function aggiornaBottoneRichiesta() {
            const numSelezionati = $('.materiale-checkbox:checked').length;
            $('#btnCreaRichiesta').prop('disabled', numSelezionati === 0);
        }

        // Gestione click sul bottone per creare una richiesta d'offerta
        $('#btnCreaRichiesta').click(function() {
            if ($('.materiale-checkbox:checked').length === 0) {
                return;
            }

            // Prepara l'elenco dei materiali selezionati
            const materialiIds = [];
            const materialiRiepilogo = [];

            $('.materiale-checkbox:checked').each(function() {
                const id = $(this).val();
                materialiIds.push(id);

                const row = $(this).closest('tr');
                const codice = row.find('td:eq(1)').text().trim();
                const descrizione = row.find('td:eq(2)').text().trim();
                const quantita = row.find('td:eq(4)').text().trim();
                const um = row.find('td:eq(5)').text().trim();

                materialiRiepilogo.push({
                    id: id,
                    codice: codice,
                    descrizione: descrizione,
                    quantita: quantita,
                    um: um
                });
            });

            // Popola il campo nascosto con gli ID dei materiali
            $('#materialiSelezionati').val(JSON.stringify(materialiIds));

            // Popola il riepilogo dei materiali
            let riepilogoHtml = '';
            if (materialiRiepilogo.length > 0) {
                riepilogoHtml = '<ul class="list-group list-group-flush">';
                $.each(materialiRiepilogo, function(index, mat) {
                    riepilogoHtml += `<li class="list-group-item p-2">
                        <strong>${mat.codice}</strong> - ${mat.descrizione.substring(0, 50)}${mat.descrizione.length > 50 ? '...' : ''}
                        <span class="float-right">${mat.quantita} ${mat.um}</span>
                    </li>`;
                });
                riepilogoHtml += '</ul>';
            } else {
                riepilogoHtml = '<p class="text-danger mb-0">Nessun materiale selezionato</p>';
            }

            $('#materialiRiepilogo').html(riepilogoHtml);

            // Compila automaticamente il titolo della richiesta
            if ($('#titoloRichiesta').val() === '') {
                $('#titoloRichiesta').val('RDO per <?= esc($progetto['nome']) ?> - ' + new Date()
                    .toLocaleDateString('it-IT'));
            }

            // Mostra il modal
            $('#modalCreaRichiesta').modal('show');
        });

        // Funzionalità di stampa per la tabella materiali
        $('#btnStampaMateriali').click(function() {
            // Determina quale tab è attivo
            const activeTab = $('#materialiTab .nav-link.active').attr('id');
            let printContent = '';

            if (activeTab === 'vista-standard-tab') {
                printContent = preparaStampaVistaStandard();
            } else {
                printContent = preparaStampaVistaCategorie();
            }

            // Crea una finestra di stampa
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Materiali Progetto: <?= esc($progetto['nome']) ?></title>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        h1, h2, h3 { color: #333; }
                        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
                        th { background-color: #f2f2f2; }
                        .text-right { text-align: right; }
                        .text-center { text-align: center; }
                        .text-muted { color: #6c757d; }
                        .header { margin-bottom: 20px; }
                        .badge {
                            padding: 3px 6px;
                            border-radius: 3px;
                            font-size: 12px;
                            font-weight: bold;
                            color: white;
                        }
                        .badge-info { background-color: #17a2b8; }
                        .badge-secondary { background-color: #6c757d; }
                        .badge-warning { background-color: #ffc107; color: #212529; }
                        .badge-primary { background-color: #007bff; }
                        .badge-light { background-color: #f8f9fa; color: #212529; }
                        .categoria-header {
                            padding: 10px;
                            margin-bottom: 10px;
                            border-radius: 4px;
                            color: white;
                            font-weight: bold;
                        }
                        .bg-info { background-color: #17a2b8; }
                        .bg-secondary { background-color: #6c757d; }
                        .bg-warning { background-color: #ffc107; color: #212529; }
                        .bg-primary { background-color: #007bff; }
                        .bg-light { background-color: #f8f9fa; color: #212529; }
                        @media print {
                            body { padding: 20px; }
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body onload="window.print()">
                    <div class="header">
                        <h1>Materiali del Progetto: <?= esc($progetto['nome']) ?></h1>
                        <p>Cliente: <?= isset($progetto['anagrafica']) ? esc($progetto['anagrafica']['ragione_sociale']) : 'Non specificato' ?></p>
                        <p>Data: ${new Date().toLocaleDateString('it-IT')}</p>
                    </div>
                    ${printContent}
                </body>
                </html>
            `);
            printWindow.document.close();
        });

        function preparaStampaVistaStandard() {
            let content = '<h2>Elenco Materiali</h2>';
            content += '<table>';
            content +=
                '<thead><tr><th>Codice</th><th>Descrizione</th><th>Quantità</th><th>UM</th><th>Categorie</th><th>Note</th></tr></thead>';
            content += '<tbody>';

            $('.materiale-checkbox:checked').each(function() {
                const id = $(this).val();
                const row = $(`#materiale${id}`).closest('tr');

                const codice = row.find('td:eq(1)').text().trim();
                const descrizione = row.find('td:eq(2)').text().trim();
                const produttore = row.find('td:eq(3)').text().trim();
                const quantita = row.find('td:eq(4)').text().trim();
                const um = row.find('td:eq(5)').text().trim();
                const categorie = row.find('td:eq(6)').html();
                const note = row.find('td:eq(7)').html();

                content += `<tr>
                    <td>${codice}</td>
                    <td>${descrizione}</td>
                    <td>${produttore}</td>
                    <td class="text-right">${quantita}</td>
                    <td>${um}</td>
                    <td>${categorie}</td>
                    <td>${note}</td>
                </tr>`;
            });

            // Se non ci sono elementi selezionati, prendi tutti i materiali
            if ($('.materiale-checkbox:checked').length === 0) {
                $('.materiale-checkbox').each(function() {
                    const id = $(this).val();
                    const row = $(`#materiale${id}`).closest('tr');

                    const codice = row.find('td:eq(1)').text().trim();
                    const descrizione = row.find('td:eq(2)').text().trim();
                    const produttore = row.find('td:eq(3)').text().trim();
                    const quantita = row.find('td:eq(4)').text().trim();
                    const um = row.find('td:eq(5)').text().trim();
                    const categorie = row.find('td:eq(6)').html();
                    const note = row.find('td:eq(7)').html();

                    content += `<tr>
                        <td>${codice}</td>
                        <td>${descrizione}</td>
                        <td class="text-right">${quantita}</td>
                        <td>${um}</td>
                        <td>${categorie}</td>
                        <td>${note}</td>
                    </tr>`;
                });
            }

            content += '</tbody></table>';
            return content;
        }

        function preparaStampaVistaCategorie() {
            let content = '';

            // Itera su tutte le card nella vista per categorie
            $('#vista-categorie .card').each(function() {
                const categoriaTitle = $(this).find('.card-header h5').text().trim();
                const categoriaClass = $(this).find('.card-header').attr('class').split('bg-')[1].split(
                    ' ')[0];

                content += `<div class="categoria-header bg-${categoriaClass}">${categoriaTitle}</div>`;
                content += '<table>';
                content +=
                    '<thead><tr><th>Codice</th><th>Descrizione</th><th>Produttore</th><th>Quantità</th><th>UM</th><th>Note</th></tr></thead>';
                content += '<tbody>';

                $(this).find('tbody tr').each(function() {
                    const codice = $(this).find('td:eq(0)').text().trim();
                    const descrizione = $(this).find('td:eq(1)').text().trim();
                    const produttore = $(this).find('td:eq(2)').text().trim();
                    const quantita = $(this).find('td:eq(3)').text().trim();
                    const um = $(this).find('td:eq(4)').text().trim();
                    const note = $(this).find('td:eq(5)').html();

                    content += `<tr>
                        <td>${codice}</td>
                        <td>${descrizione}</td>
                        <td>${produttore}</td>
                        <td class="text-right">${quantita}</td>
                        <td>${um}</td>
                        <td>${note}</td>
                    </tr>`;
                });

                content += '</tbody></table>';
            });

            return content;
        }

        // Script per mostrare il nome del file selezionato nel form di upload
        $(".custom-file-input").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });

        // Popola il modal di modifica documento
        $('#editDocumentoModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var nome = button.data('nome');
            var descrizione = button.data('descrizione');

            var modal = $(this);
            modal.find('#edit_id_documento').val(id);
            modal.find('#edit_nome').val(nome);
            modal.find('#edit_descrizione').val(descrizione);
        });

        // Gestione eliminazione documento con SweetAlert2
        $('.btn-elimina-documento').click(function() {
            var id = $(this).data('id');
            var nome = $(this).data('nome');

            Swal.fire({
                title: 'Sei sicuro?',
                text: "Vuoi eliminare il documento '" + nome + "'?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sì, elimina!',
                cancelButtonText: 'Annulla'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('documenti/delete/') ?>' + id;
                }
            });
        });

        // Gestione eliminazione progetto con SweetAlert2
        $('.btn-elimina-progetto').click(function() {
            var id = $(this).data('id');
            var nome = $(this).data('nome');

            Swal.fire({
                title: 'Sei sicuro?',
                text: "Stai per eliminare il progetto '" + nome +
                    "'. Questa azione non può essere annullata!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sì, elimina!',
                cancelButtonText: 'Annulla'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('progetti/delete/') ?>' + id;
                }
            });
        });

        // Gestione eliminazione sottoprogetto con SweetAlert2
        $('.btn-elimina-sottoprogetto').click(function() {
            var id = $(this).data('id');
            var nome = $(this).data('nome');

            Swal.fire({
                title: 'Sei sicuro?',
                text: "Stai per eliminare il sottoprogetto '" + nome +
                    "'. Questa azione non può essere annullata!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sì, elimina!',
                cancelButtonText: 'Annulla'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('progetti/delete/') ?>' + id;
                }
            });
        });

        // Gestione ricerca materiali AJAX
        $('#btnSearchMateriale').click(function() {
            cercaMateriali();
        });

        // Ricerca materiali anche con invio sulla textbox
        $('#searchMateriale').keypress(function(e) {
            if (e.which == 13) {
                e.preventDefault();
                cercaMateriali();
            }
        });

        function cercaMateriali() {
            const query = $('#searchMateriale').val();

            if (query.length < 2) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Ricerca troppo breve',
                    text: 'Inserisci almeno 2 caratteri per la ricerca',
                    showConfirmButton: false,
                    timer: 1500
                });
                return;
            }

            $.ajax({
                url: '<?= base_url('materiali/search') ?>',
                type: 'GET',
                data: {
                    term: query
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#materialiRisultati').html(
                        '<tr><td colspan="4" class="text-center"><i class="fas fa-spinner fa-spin"></i> Ricerca in corso...</td></tr>'
                    );
                },
                success: function(response) {
                    $('#materialiRisultati').empty();

                    if (response.length === 0) {
                        $('#materialiRisultati').html(
                            '<tr><td colspan="4" class="text-center">Nessun materiale trovato. Prova con una ricerca diversa o aggiungi un nuovo materiale.</td></tr>'
                        );
                        return;
                    }

                    $.each(response, function(index, materiale) {
                        $('#materialiRisultati').append(`
                        <tr>
                            <td>${materiale.codice}</td>
                            <td>${materiale.descrizione}</td>
                            <td>${materiale.produttore || '-'}</td>
                            <td><button type="button" class="btn btn-sm btn-success selezionaMateriale" data-id="${materiale.id}"><i class="fas fa-plus"></i> Aggiungi</button></td>
                        </tr>
                        `);
                    });

                    // Collega gli eventi ai nuovi bottoni
                    $('.selezionaMateriale').click(function() {
                        const idMateriale = $(this).data('id');
                        $('#idMateriale').val(idMateriale);
                        $('#modalAggiungiMateriale').modal('hide');
                        setTimeout(function() {
                            $('#modalQuantitaMateriale').modal('show');
                        }, 500);
                    });
                },
                error: function(xhr, status, error) {
                    $('#materialiRisultati').html(
                        '<tr><td colspan="4" class="text-center text-danger">Errore durante la ricerca. Riprova.</td></tr>'
                    );
                    console.error(error);
                }
            });
        }

        // Evento per modificare un materiale
        $('.editMateriale').click(function() {
            const id = $(this).data('id');
            const idMateriale = $(this).data('materiale-id');
            const quantita = $(this).data('quantita');
            const unitaMisura = $(this).data('unita-misura');
            const note = $(this).data('note');

            $('#editId').val(id);
            $('#editIdMateriale').val(idMateriale);
            $('#editQuantita').val(quantita);
            $('#editUnitaMisura').val(unitaMisura);
            $('#editNote').val(note);

            $('#modalModificaMateriale').modal('show');
        });

        // Gestione eliminazione materiale
        $('.btn-rimuovi-materiale').click(function() {
            const id = $(this).data('id');
            const codice = $(this).data('codice');

            Swal.fire({
                title: 'Sei sicuro?',
                text: "Vuoi rimuovere il materiale '" + codice + "' da questo progetto?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sì, rimuovi',
                cancelButtonText: 'Annulla'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href =
                        '<?= base_url('progetti/rimuovi-materiale/' . $progetto['id'] . '/') ?>' +
                        id;
                }
            });
        });

        // Gestione del pulsante per generare etichette barcode
        $('#btnStampaBarcodes').click(function() {
            const materialiIds = [];
            
            // Raccogli gli ID dei materiali selezionati
            $('.materiale-checkbox:checked').each(function() {
                materialiIds.push($(this).val());
            });
            
            // Se non ci sono materiali selezionati, prendi tutti
            if (materialiIds.length === 0) {
                $('.materiale-checkbox').each(function() {
                    materialiIds.push($(this).val());
                });
            }
            
            if (materialiIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nessun materiale',
                    text: 'Non ci sono materiali da stampare'
                });
                return;
            }
            
            // Popola il campo nascosto con gli ID
            $('#materialiIdsBarcode').val(JSON.stringify(materialiIds));
            
            // Invia il form
            $('#formGeneraBarcodes').submit();
        });
    });

    // Gestione importazione Excel
    $(document).ready(function() {
        let currentStep = 1;
        let fileData = null;
        let sheetData = null;
        let mappedData = null;

        // Gestione cambio nome file
        $(".custom-file-input").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });

        // Funzione per mostrare/nascondere i pulsanti di navigazione
        function updateNavigationButtons() {
            $('#btnPrevStep').toggle(currentStep > 1);
            $('#btnNextStep').toggle(currentStep < 4);
            $('#btnImporta').toggle(currentStep === 4);
        }

        // Funzione per cambiare step
        function changeStep(step) {
            $('.import-step').hide();
            $(`#step${step}`).show();
            currentStep = step;
            updateNavigationButtons();
        }

        // Gestione pulsante Analizza File
        $('#btnAnalizzaFile').click(function() {
            const fileInput = $('#fileExcel')[0];
            if (!fileInput.files.length) {
                Swal.fire({
                    icon: 'error',
                    title: 'Errore',
                    text: 'Seleziona un file Excel da analizzare'
                });
                return;
            }

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            $.ajax({
                url: '<?= base_url('progetti/analizza-excel') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#btnAnalizzaFile').prop('disabled', true).html(
                        '<i class="fas fa-spinner fa-spin"></i> Analisi in corso...');
                },
                success: function(response) {
                    if (response.success) {
                        fileData = response.data;
                        // Popola il select dei fogli
                        $('#selectFoglio').empty().append(
                            '<option value="">Seleziona un foglio...</option>');
                        fileData.sheets.forEach(function(sheet) {
                            $('#selectFoglio').append(
                                `<option value="${sheet}">${sheet}</option>`);
                        });
                        changeStep(2);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Errore',
                            text: response.message ||
                                'Errore durante l\'analisi del file'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Errore',
                        text: 'Errore durante l\'analisi del file'
                    });
                },
                complete: function() {
                    $('#btnAnalizzaFile').prop('disabled', false).html('Analizza File');
                }
            });
        });

        // Gestione pulsante Analizza Foglio
        $('#btnAnalizzaFoglio').click(function() {
            const foglio = $('#selectFoglio').val();
            if (!foglio) {
                Swal.fire({
                    icon: 'error',
                    title: 'Errore',
                    text: 'Seleziona un foglio da analizzare'
                });
                return;
            }

            $.ajax({
                url: '<?= base_url('progetti/analizza-foglio') ?>',
                type: 'POST',
                data: {
                    foglio: foglio,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                beforeSend: function() {
                    $('#btnAnalizzaFoglio').prop('disabled', true).html(
                        '<i class="fas fa-spinner fa-spin"></i> Analisi in corso...');
                },
                success: function(response) {
                    if (response.success) {
                        sheetData = response.data;
                        // Popola i select delle colonne
                        const colonne = response.data.colonne;
                        ['codice', 'descrizione', 'materiale', 'produttore', 'quantita',
                            'unita_misura'
                        ].forEach(function(campo) {
                            $(`#colonna${campo.charAt(0).toUpperCase() + campo.slice(1)}`)
                                .empty()
                                .append(
                                    '<option value="">Seleziona colonna...</option>');
                            colonne.forEach(function(colonna, index) {
                                $(`#colonna${campo.charAt(0).toUpperCase() + campo.slice(1)}`)
                                    .append(
                                        `<option value="${index}">${colonna}</option>`
                                    );
                            });
                        });
                        changeStep(3);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Errore',
                            text: response.message ||
                                'Errore durante l\'analisi del foglio'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Errore',
                        text: 'Errore durante l\'analisi del foglio'
                    });
                },
                complete: function() {
                    $('#btnAnalizzaFoglio').prop('disabled', false).html('Analizza Foglio');
                }
            });
        });

        // Gestione pulsante Avanti
        $('#btnNextStep').click(function() {
            if (currentStep === 3) {
                // Verifica che i campi obbligatori siano selezionati
                if (!$('#colonnaCodice').val() || !$('#colonnaDescrizione').val() || !$('#colonnaQuantita')
                    .val()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Errore',
                        text: 'Seleziona almeno le colonne Codice, Descrizione e Quantità'
                    });
                    return;
                }

                // Prepara i dati per l'anteprima
                const mappatura = {
                    codice: $('#colonnaCodice').val(),
                    descrizione: $('#colonnaDescrizione').val(),
                    materiale: $('#colonnaMateriale').val(),
                    produttore: $('#colonnaProduttore').val(),
                    quantita: $('#colonnaQuantita').val(),
                    unita_misura: $('#colonnaUnitaMisura').val()
                };

                // Mostra anteprima
                $.ajax({
                    url: '<?= base_url('progetti/anteprima-importazione') ?>',
                    type: 'POST',
                    data: {
                        mappatura: mappatura,
                        riga_inizio: $('#rigaInizio').val(),
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            mappedData = response.data;
                            // Popola la tabella di anteprima
                            const tbody = $('#tabellaAnteprima tbody');
                            tbody.empty();
                            response.data.forEach(function(row) {
                                tbody.append(`
                                    <tr>
                                        <td>${row.codice || '-'}</td>
                                        <td>${row.descrizione || '-'}</td>
                                        <td>${row.materiale || '-'}</td>
                                        <td>${row.produttore || '-'}</td>
                                        <td>${row.quantita || '-'}</td>
                                        <td>${row.unita_misura || '-'}</td>
                                    </tr>
                                `);
                            });
                            changeStep(4);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Errore',
                                text: response.message ||
                                    'Errore durante la generazione dell\'anteprima'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Errore',
                            text: 'Errore durante la generazione dell\'anteprima'
                        });
                    }
                });
            } else {
                changeStep(currentStep + 1);
            }
        });

        // Gestione pulsante Indietro
        $('#btnPrevStep').click(function() {
            changeStep(currentStep - 1);
        });

        // Gestione pulsante Importa
        $('#btnImporta').click(function() {
            if (!mappedData) {
                Swal.fire({
                    icon: 'error',
                    title: 'Errore',
                    text: 'Nessun dato da importare'
                });
                return;
            }

            $.ajax({
                url: '<?= base_url('progetti/importa-materiali-excel/' . $progetto['id']) ?>',
                type: 'POST',
                data: {
                    dati: mappedData,
                    skip_duplicati: $('#skipDuplicati').is(':checked'),
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                beforeSend: function() {
                    $('#btnImporta').prop('disabled', true).html(
                        '<i class="fas fa-spinner fa-spin"></i> Importazione in corso...');
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Successo',
                            text: `Importazione completata: ${response.importati} materiali importati${response.saltati ? `, ${response.saltati} saltati` : ''}`
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Errore',
                            text: response.message || 'Errore durante l\'importazione'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Errore',
                        text: 'Errore durante l\'importazione'
                    });
                },
                complete: function() {
                    $('#btnImporta').prop('disabled', false).html('Importa');
                }
            });
        });

        // Reset del modal quando viene chiuso
        $('#modalImportaExcel').on('hidden.bs.modal', function() {
            currentStep = 1;
            fileData = null;
            sheetData = null;
            mappedData = null;
            $('#formImportaExcel')[0].reset();
            $('.custom-file-label').removeClass('selected').html('Scegli file Excel...');
            changeStep(1);
        });
    });
</script>
<?= $this->endSection() ?>