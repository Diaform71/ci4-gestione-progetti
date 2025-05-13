<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?? 'Scadenze' ?><?= $this->endSection() ?>

<?= $this->section('page_title') ?><?= $title ?? 'Scadenze' ?><?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
<li class="breadcrumb-item active">Scadenze</li>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/fullcalendar/main.min.css') ?>">
<style>
    .scadenza-urgente {
        background-color: #fcbdbd !important;
    }
    .scadenza-alta {
        background-color: #ffe0b2 !important;
    }
    .scadenza-media {
        background-color: #fff9c4 !important;
    }
    .scadenza-completata {
        text-decoration: line-through;
        color: #757575;
    }
    .badge-urgente {
        background-color: #dc3545;
        color: white;
    }
    .badge-alta {
        background-color: #fd7e14;
        color: white;
    }
    .badge-media {
        background-color: #ffc107;
        color: black;
    }
    .badge-bassa {
        background-color: #28a745;
        color: white;
    }
    
    .scadenza-overdue {
        color: #dc3545;
        font-weight: bold;
    }

    .scadenza-oggi {
        color: #ffc107;
        font-weight: bold;
    }

    .badge-scade-oggi {
        background-color: #ffc107;
        color: #212529;
    }
    /* Stili per FullCalendar */
    #calendar {
        max-width: 1100px;
        margin: 0 auto;
    }
    /* Colori eventi calendario */
    .fc-event-urgente { background-color: #dc3545 !important; border-color: #dc3545 !important; }
    .fc-event-alta { background-color: #fd7e14 !important; border-color: #fd7e14 !important; }
    .fc-event-media { background-color: #ffc107 !important; border-color: #ffc107 !important; color: black !important; }
    .fc-event-bassa { background-color: #28a745 !important; border-color: #28a745 !important; }
    .fc-event-completata { background-color: #6c757d !important; border-color: #6c757d !important; opacity: 0.7; }
    .fc-event-annullata { background-color: #adb5bd !important; border-color: #adb5bd !important; text-decoration: line-through; }
    
    /* Nascondi una vista all'inizio */
    #vista-calendario { display: none; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Pulsanti switch vista -->
<div class="mb-3 text-right">
    <button id="btn-vista-tabella" class="btn btn-primary"><i class="fas fa-list"></i> Vista Tabella</button>
    <button id="btn-vista-calendario" class="btn btn-secondary"><i class="fas fa-calendar-alt"></i> Vista Calendario</button>
</div>

<!-- Filtri (nascosti se vista speciale) -->
<?php if (!isset($vistaSpeciale)): ?>
<div class="card mb-4" id="filtri-card">
    <div class="card-header">
        <h3 class="card-title">Filtri</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form action="<?= base_url('scadenze') ?>" method="get" id="filter-form">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filter-priorita">Priorità</label>
                        <select class="form-control" id="filter-priorita" name="priorita">
                            <option value="">Tutte</option>
                            <option value="bassa">Bassa</option>
                            <option value="media">Media</option>
                            <option value="alta">Alta</option>
                            <option value="urgente">Urgente</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filter-stato">Stato</label>
                        <select class="form-control" id="filter-stato" name="stato">
                            <option value="">Tutti</option>
                            <option value="da_iniziare">Da iniziare</option>
                            <option value="in_corso">In corso</option>
                            <option value="completata">Completata</option>
                            <option value="annullata">Annullata</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filter-data-da">Data da</label>
                        <input type="date" class="form-control" id="filter-data-da" name="data_da">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filter-data-a">Data a</label>
                        <input type="date" class="form-control" id="filter-data-a" name="data_a">
                    </div>
                </div>
            </div>
            
            <?php if(isset($isAdmin) && $isAdmin): ?>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filter-utente">Filtro per utente</label>
                        <select class="form-control" id="filter-utente" name="id_utente_assegnato">
                            <option value="">Tutti gli utenti</option>
                            <?php foreach ($utenti ?? [] as $utente): ?>
                            <option value="<?= $utente['id'] ?>"><?= esc($utente['nome']) ?> <?= esc($utente['cognome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filter-progetto">Filtro per progetto</label>
                        <select class="form-control" id="filter-progetto" name="id_progetto">
                            <option value="">Tutti i progetti</option>
                            <?php foreach ($progetti ?? [] as $progetto): ?>
                            <option value="<?= $progetto['id'] ?>"><?= esc($progetto['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filter-completata">Stato completamento</label>
                        <select class="form-control" id="filter-completata" name="completata">
                            <option value="">Tutti</option>
                            <option value="1">Completate</option>
                            <option value="0">Non completate</option>
                        </select>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-12 text-right">
                    <button type="button" class="btn btn-default mr-2" id="reset-filters">Reset</button>
                    <button type="submit" class="btn btn-primary">Filtra</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Contenitore per la vista tabella -->
<div id="vista-tabella">
    <!-- Tabella Scadenze -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <?= $title ?? 'Elenco Scadenze' ?>
                <?php if(isset($progetto) && current_url() === base_url('scadenze/progetto/' . $progetto['id'])): ?>
                    - Progetto: <?= esc($progetto['nome']) ?>
                    <?php 
                    // Verifica se il progetto ha sottoprogetti
                    $progettoModel = new \App\Models\ProgettoModel();
                    if($progettoModel->hasSottoprogetti($progetto['id'])): 
                    ?>
                        <small class="text-muted">(include sottoprogetti)</small>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if(isset($attivita) && current_url() === base_url('scadenze/attivita/' . $attivita['id'])): ?>
                    - Attività: <?= esc($attivita['titolo']) ?>
                <?php endif; ?>
                
                <?php
                // Mostra i filtri attivi
                $filtriAttivi = [];
                $filtriLabels = [
                    'priorita' => 'Priorità',
                    'stato' => 'Stato',
                    'id_utente_assegnato' => 'Utente',
                    'id_progetto' => 'Progetto',
                    'completata' => 'Completamento'
                ];
                
                if (isset($vistaSpeciale)) {
                    // Mostra badge per vista speciale
                    $labelVista = '';
                    if ($vistaSpeciale === 'in_scadenza') {
                        $labelVista = 'In Arrivo';
                    } elseif ($vistaSpeciale === 'scadute') {
                        $labelVista = 'Scadute';
                    }
                    if ($labelVista) {
                        $filtriAttivi[] = "<span class='badge badge-warning'>Vista: {$labelVista}</span>";
                    }
                } else {
                    // Mostra filtri GET standard
                    foreach ($_GET as $key => $value) {
                        if (empty($value) || !isset($filtriLabels[$key])) continue;
                        
                        $label = $filtriLabels[$key];
                        $valueLabel = $value;
                        
                        // Formatta il valore per la visualizzazione
                        switch ($key) {
                            case 'priorita':
                                $valueLabel = ucfirst($value);
                                break;
                            case 'stato':
                                $stati = [
                                    'da_iniziare' => 'Da iniziare',
                                    'in_corso' => 'In corso',
                                    'completata' => 'Completata',
                                    'annullata' => 'Annullata'
                                ];
                                $valueLabel = $stati[$value] ?? ucfirst($value);
                                break;
                            case 'id_utente_assegnato':
                                foreach ($utenti ?? [] as $utente) {
                                    if ($utente['id'] == $value) {
                                        $valueLabel = $utente['nome'] . ' ' . $utente['cognome'];
                                        break;
                                    }
                                }
                                break;
                            case 'id_progetto':
                                foreach ($progetti ?? [] as $progetto) {
                                    if ($progetto['id'] == $value) {
                                        $valueLabel = $progetto['nome'];
                                        // Aggiungi indicazione che include sottoprogetti
                                        $progettoModel = new \App\Models\ProgettoModel();
                                        if($progettoModel->hasSottoprogetti((int)$progetto['id'])) {
                                            $valueLabel .= ' (include sottoprogetti)';
                                        }
                                        break;
                                    }
                                }
                                break;
                            case 'completata':
                                $valueLabel = ($value == '1') ? 'Completate' : 'Non completate';
                                break;
                        }
                        
                        $filtriAttivi[] = "<span class='badge badge-info'>{$label}: {$valueLabel}</span>";
                    }
                }
                
                if (!empty($filtriAttivi)):
                ?>
                    <div class="ml-2 d-inline-block">
                        <strong>Filtri attivi:</strong> <?= implode(' ', $filtriAttivi) ?>
                    </div>
                <?php endif; ?>
            </h3>
            <div class="card-tools">
                <a href="<?= site_url('scadenze/nuovo') ?>" class="btn btn-sm btn-success">
                    <i class="fas fa-plus"></i> Nuova Scadenza
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Titolo</th>
                            <th>Progetto</th>
                            <th>Attività</th>
                            <th>Assegnata a</th>
                            <th>Priorità</th>
                            <th>Stato</th>
                            <th>Data Scadenza</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($scadenze)): ?>
                        <tr>
                            <td colspan="8" class="text-center">Nessuna scadenza trovata</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($scadenze as $scadenza): 
                                $rowClass = '';
                                if ($scadenza['completata']) {
                                    $rowClass = 'scadenza-completata';
                                } else if ($scadenza['priorita'] === 'urgente') {
                                    $rowClass = 'scadenza-urgente';
                                } else if ($scadenza['priorita'] === 'alta') {
                                    $rowClass = 'scadenza-alta';
                                } else if ($scadenza['priorita'] === 'media') {
                                    $rowClass = 'scadenza-media';
                                }
                                
                                // Verifica se è scaduta
                                $isOverdue = false;
                                $scadeOggi = false;
                                if (!$scadenza['completata'] && $scadenza['stato'] !== 'annullata') {
                                    $dataScadenza = new DateTime($scadenza['data_scadenza']);
                                    $dataScadenza->setTime(0, 0, 0); // Imposta l'ora a 00:00:00
                                    
                                    $today = new DateTime();
                                    $today->setTime(0, 0, 0); // Imposta l'ora a 00:00:00
                                    
                                    if ($dataScadenza < $today) {
                                        $isOverdue = true;
                                    } elseif ($dataScadenza == $today) {
                                        $scadeOggi = true;
                                    }
                                }
                            ?>
                            <tr class="<?= $rowClass ?>">
                                <td><?= esc($scadenza['titolo']) ?></td>
                                <td><?= esc($scadenza['nome_progetto'] ?? '-') ?></td>
                                <td><?= esc($scadenza['titolo_attivita'] ?? '-') ?></td>
                                <td><?= esc($scadenza['nome_assegnato'] ?? '') ?> <?= esc($scadenza['cognome_assegnato'] ?? '') ?></td>
                                <td>
                                    <span class="badge badge-<?= esc($scadenza['priorita']) ?>">
                                        <?= ucfirst(esc($scadenza['priorita'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                        $statoLabel = '';
                                        $statoClass = '';
                                        
                                        switch($scadenza['stato']) {
                                            case 'da_iniziare':
                                                $statoLabel = 'Da iniziare';
                                                $statoClass = 'badge-secondary';
                                                break;
                                            case 'in_corso':
                                                $statoLabel = 'In corso';
                                                $statoClass = 'badge-primary';
                                                break;
                                            case 'completata':
                                                $statoLabel = 'Completata';
                                                $statoClass = 'badge-success';
                                                break;
                                            case 'annullata':
                                                $statoLabel = 'Annullata';
                                                $statoClass = 'badge-danger';
                                                break;
                                            default:
                                                $statoLabel = ucfirst($scadenza['stato']);
                                                $statoClass = 'badge-info';
                                        }
                                    ?>
                                    <span class="badge <?= $statoClass ?>"><?= $statoLabel ?></span>
                                </td>
                                <td class="<?= $isOverdue ? 'scadenza-overdue' : ($scadeOggi ? 'scadenza-oggi' : '') ?>">
                                    <?= date('d/m/Y', strtotime($scadenza['data_scadenza'])) ?>
                                    <?php if ($isOverdue): ?>
                                        <span class="badge badge-danger">Scaduta</span>
                                    <?php elseif ($scadeOggi): ?>
                                        <span class="badge badge-warning">Scade oggi</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url('scadenze/dettaglio/' . $scadenza['id']) ?>" class="btn btn-sm btn-info" title="Dettagli">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('scadenze/modifica/' . $scadenza['id']) ?>" class="btn btn-sm btn-primary" title="Modifica">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('scadenze/completa/' . $scadenza['id']) ?>" class="btn btn-sm btn-success" title="<?= $scadenza['completata'] ? 'Riapri' : 'Completa' ?>">
                                            <i class="fas <?= $scadenza['completata'] ? 'fa-undo' : 'fa-check' ?>"></i>
                                        </a>
                                        <!-- Modifica il bottone elimina per usare la funzione aggiornata -->
                                        <button type="button" class="btn btn-sm btn-danger btn-delete" onclick="confermaEliminazione(<?= $scadenza['id'] ?>)" title="Elimina" data-id="<?= $scadenza['id'] ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
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
</div>

<!-- Contenitore per la vista calendario -->
<div id="vista-calendario">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Calendario Scadenze</h3>
             <?php if (!empty($filtriAttivi)): ?>
                <div class="ml-2 d-inline-block">
                    <!-- Usa la stessa logica della tabella per i filtri attivi -->
                    <?php
                    $filtriAttiviCal = []; // Usa un array separato per evitare conflitti
                    if (isset($vistaSpeciale)) {
                        // Mostra badge per vista speciale
                        $labelVista = '';
                        if ($vistaSpeciale === 'in_scadenza') {
                            $labelVista = 'In Arrivo';
                        } elseif ($vistaSpeciale === 'scadute') {
                            $labelVista = 'Scadute';
                        }
                        if ($labelVista) {
                            $filtriAttiviCal[] = "<span class='badge badge-warning'>Vista: {$labelVista}</span>";
                        }
                    } else {
                        // Mostra filtri GET standard (riutilizza la logica)
                         foreach ($_GET as $key => $value) {
                            if (empty($value) || !isset($filtriLabels[$key])) continue;
                            $label = $filtriLabels[$key];
                            $valueLabel = $value; // Semplificato per il calendario, dettagli nel tooltip
                            $filtriAttiviCal[] = "<span class='badge badge-info'>{$label}: {$valueLabel}</span>";
                        }
                    }
                    ?>
                    <strong>Filtri attivi:</strong> <?= implode(' ', $filtriAttiviCal) ?>
                </div>
             <?php endif; ?>
            <div class="card-tools">
                 <a href="<?= site_url('scadenze/nuovo') ?>" class="btn btn-sm btn-success">
                    <i class="fas fa-plus"></i> Nuova Scadenza
                </a>
            </div>
        </div>
        <div class="card-body">
            <div id='calendar'></div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/fullcalendar/main.min.js') ?>"></script>
<script src='<?= base_url('plugins/fullcalendar/locales/it.js') ?>'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestione switch viste
        const vistaTabella = document.getElementById('vista-tabella');
        const vistaCalendario = document.getElementById('vista-calendario');
        const btnVistaTabella = document.getElementById('btn-vista-tabella');
        const btnVistaCalendario = document.getElementById('btn-vista-calendario');
        const filtriCard = document.getElementById('filtri-card'); // Card dei filtri
        
        btnVistaTabella.addEventListener('click', function() {
            vistaTabella.style.display = 'block';
            vistaCalendario.style.display = 'none';
            filtriCard.style.display = 'block'; // Mostra filtri in vista tabella
            btnVistaTabella.classList.replace('btn-secondary', 'btn-primary');
            btnVistaCalendario.classList.replace('btn-primary', 'btn-secondary');
        });
        
        btnVistaCalendario.addEventListener('click', function() {
            vistaTabella.style.display = 'none';
            vistaCalendario.style.display = 'block';
            if (filtriCard) { // Controlla se l'elemento esiste prima di accedervi
                filtriCard.style.display = 'block'; // Mostra filtri anche in vista calendario (se non è vista speciale)
            }
            btnVistaCalendario.classList.replace('btn-secondary', 'btn-primary');
            btnVistaTabella.classList.replace('btn-primary', 'btn-secondary');
            // Renderizza il calendario quando diventa visibile
            calendar.render(); 
        });
        
        // Inizializzazione FullCalendar
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'it',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            buttonText: { // Testi italiani per i pulsanti
                 today:    'Oggi',
                 month:    'Mese',
                 week:     'Settimana',
                 day:      'Giorno',
                 list:     'Agenda'
             },
            events: {
                url: '<?= base_url('scadenze/calendario_eventi') ?>',
                method: 'GET',
                extraParams: function() { // Aggiungi i parametri dei filtri alla richiesta eventi
                    const params = new URLSearchParams(window.location.search);
                    let extraParams = {};
                    for (const [key, value] of params) {
                         // Includi solo i filtri non vuoti
                         if (value) {
                             extraParams[key] = value;
                         }
                    }
                    <?php if (isset($vistaSpeciale)): ?>
                    // Aggiungi parametro vista_speciale quando siamo in una vista speciale
                    extraParams.vista_speciale = '<?= $vistaSpeciale ?>';
                    <?php endif; ?>
                    return extraParams;
                },
                failure: function() {
                    alert('Errore nel caricamento delle scadenze!');
                },
                color: '#378006', // Colore di default
                textColor: 'white' // Testo di default
            },
            eventDidMount: function(info) {
                // Aggiungi tooltip agli eventi (opzionale, richiede Popper.js/Bootstrap tooltip)
                 if (info.event.extendedProps.description) {
                     $(info.el).tooltip({
                         title: info.event.extendedProps.description,
                         placement: 'top',
                         trigger: 'hover',
                         container: 'body',
                         html: true // Permette HTML nel tooltip
                     });
                 }
            },
            eventClick: function(info) {
                // Reindirizza al dettaglio della scadenza al click
                 if (info.event.url) {
                     window.location.href = info.event.url;
                     info.jsEvent.preventDefault(); // previene l'apertura del link di default
                 }
             }
        });

        // Ripristino filtri dai parametri URL al caricamento della pagina
        const urlParams = new URLSearchParams(window.location.search);
        let hasFilters = false;
        // Non ripristinare i filtri se è una vista speciale
        if (!document.getElementById('filtri-card')) { 
            for (const [key, value] of urlParams) {
                if (key && value) {
                    const id = 'filter-' + key.replace(/_/g, '-');
                    const element = document.getElementById(id);
                    if (element) {
                        element.value = value;
                        hasFilters = true; // Imposta flag se almeno un filtro è attivo
                    }
                }
            }
        }

        // Reset filtri
        $('#reset-filters').click(function() {
            $('#filter-form')[0].reset();
             // Mantiene la vista corrente dopo il reset
             const currentPath = window.location.pathname;
             window.location.href = currentPath; // Ricarica senza parametri
        });
        
        // Submit filtri ricarica il calendario
        $('#filter-form').submit(function(e) {
            // Non prevenire il default, lascia che la pagina ricarichi con i nuovi parametri GET
            // Il calendario leggerà i nuovi parametri URL quando richiede gli eventi.
            // Potresti voler forzare il refetch degli eventi qui se non vuoi ricaricare la pagina,
            // ma ricaricare è più semplice per sincronizzare filtri, tabella e calendario.
        });
        
        // Funzione di conferma eliminazione (invariata ma spostata qui)
        window.confermaEliminazione = function(id) {
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
                    form.action = '<?= base_url('scadenze/elimina') ?>/' + id;
                    
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
    });
</script>
<?= $this->endSection() ?> 