<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('pickup-delivery') ?>">Pickup & Delivery</a></li>
<li class="breadcrumb-item active">Calendario</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            Calendario Pickup & Delivery
                        </h3>
                        <div>
                            <a href="<?= base_url('pickup-delivery') ?>" class="btn btn-secondary">
                                <i class="fas fa-list"></i> Vista Lista
                            </a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-print"></i> Stampa
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="<?= base_url('pickup-delivery/stampa-lista?data_inizio=' . date('Y-m-d') . '&data_fine=' . date('Y-m-d')) ?>" target="_blank">
                                        <i class="fas fa-calendar-day"></i> Lista Oggi
                                    </a>
                                    <a class="dropdown-item" href="<?= base_url('pickup-delivery/stampa-lista?data_inizio=' . date('Y-m-d', strtotime('monday this week')) . '&data_fine=' . date('Y-m-d', strtotime('sunday this week'))) ?>" target="_blank">
                                        <i class="fas fa-calendar-week"></i> Lista Settimana
                                    </a>
                                </div>
                            </div>
                            <a href="<?= base_url('pickup-delivery/new') ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Nuova Operazione
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('message')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= session()->getFlashdata('message') ?>
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Legenda -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-info-circle"></i> Legenda
                                    </h5>
                                </div>
                                <div class="card-body py-2">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <span class="badge badge-primary mr-2">■</span> Programmata
                                        </div>
                                        <div class="col-md-3">
                                            <span class="badge badge-warning mr-2">■</span> In Corso
                                        </div>
                                        <div class="col-md-3">
                                            <span class="badge badge-success mr-2">■</span> Completata
                                        </div>
                                        <div class="col-md-3">
                                            <span class="badge badge-danger mr-2">■</span> Annullata
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <i class="fas fa-truck-loading text-info mr-2"></i> Ritiro
                                        </div>
                                        <div class="col-md-6">
                                            <i class="fas fa-truck text-primary mr-2"></i> Consegna
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <small class="text-muted">
                                                <i class="fas fa-mouse-pointer"></i> Passa il mouse sopra un evento per vedere i dettagli |
                                                <i class="fas fa-arrows-alt"></i> Trascina un evento per modificare la data
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Calendario -->
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal per dettagli evento -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Dettagli Operazione</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="eventModalBody">
                <!-- Contenuto caricato dinamicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                <a href="#" id="editEventBtn" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Modifica
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('plugins/fullcalendar/main.min.css') ?>">
<style>
.fc-event {
    cursor: pointer;
    border-radius: 4px;
}

.fc-event:hover {
    opacity: 0.8;
    transform: scale(1.02);
    transition: all 0.2s ease;
}

.fc-event .fc-title {
    font-weight: 500;
}

.fc-event .fc-time {
    font-weight: bold;
}

/* Tooltip personalizzato */
.tooltip-pickup {
    position: absolute;
    background: rgba(0, 0, 0, 0.9);
    color: white;
    padding: 10px;
    border-radius: 6px;
    font-size: 12px;
    z-index: 9999;
    max-width: 300px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

.tooltip-pickup::after {
    content: '';
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: rgba(0, 0, 0, 0.9) transparent transparent transparent;
}

/* Stili per i diversi tipi di priorità */
.priority-bassa { border-left: 4px solid #6c757d !important; }
.priority-normale { border-left: 4px solid #17a2b8 !important; }
.priority-alta { border-left: 4px solid #ffc107 !important; }
.priority-urgente { border-left: 4px solid #dc3545 !important; }

/* Animazione per il drag & drop */
.fc-event.fc-dragging {
    opacity: 0.7;
    transform: rotate(5deg);
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/fullcalendar/main.min.js') ?>"></script>
<script src="<?= base_url('plugins/fullcalendar/locales/it.js') ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const eventi = <?= $eventi ?>;
    
    // Inizializza il calendario
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'it',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
            today: 'Oggi',
            month: 'Mese',
            week: 'Settimana',
            day: 'Giorno',
            list: 'Lista'
        },
        height: 'auto',
        events: eventi,
        editable: true, // Abilita drag & drop
        droppable: true,
        eventResizableFromStart: false,
        eventDurationEditable: false,
        
        // Personalizza la visualizzazione degli eventi
        eventDidMount: function(info) {
            const event = info.event;
            const props = event.extendedProps;
            
            // Aggiungi icona al titolo
            const iconClass = props.icona || 'fa-truck';
            const titleEl = info.el.querySelector('.fc-title, .fc-event-title');
            if (titleEl) {
                titleEl.innerHTML = `<i class="fas ${iconClass} mr-1"></i>${event.title}`;
            }
            
            // Aggiungi classe per priorità
            if (props.priorita) {
                info.el.classList.add(`priority-${props.priorita}`);
            }
        },
        
        // Gestione click su evento
        eventClick: function(info) {
            showEventDetails(info.event);
        },
        
        // Gestione drag & drop
        eventDrop: function(info) {
            updateEventDate(info.event, info.oldEvent);
        },
        
        // Gestione hover per tooltip personalizzato
        eventMouseEnter: function(info) {
            showCustomTooltip(info.jsEvent, info.event);
        },
        
        eventMouseLeave: function(info) {
            hideCustomTooltip();
        }
    });
    
    calendar.render();
    
    // Tooltip personalizzato
    let customTooltip = null;
    
    function showCustomTooltip(mouseEvent, event) {
        hideCustomTooltip();
        
        const props = event.extendedProps;
        const startDate = new Date(event.start);
        const formattedDate = startDate.toLocaleDateString('it-IT', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        customTooltip = document.createElement('div');
        customTooltip.className = 'tooltip-pickup';
        customTooltip.innerHTML = `
            <div style="font-weight: bold; margin-bottom: 5px;">
                <i class="fas ${props.icona} mr-1"></i> ${event.title}
            </div>
            <div><strong>Tipo:</strong> ${props.tipo.charAt(0).toUpperCase() + props.tipo.slice(1)}</div>
            <div><strong>Anagrafica:</strong> ${props.anagrafica}</div>
            <div><strong>Data:</strong> ${formattedDate}</div>
            <div><strong>Priorità:</strong> <span class="badge badge-${getPriorityClass(props.priorita)}">${props.priorita}</span></div>
            <div><strong>Stato:</strong> <span class="badge badge-${getStatusClass(props.stato)}">${props.stato}</span></div>
            <div style="margin-top: 5px; font-size: 11px; opacity: 0.8;">
                <i class="fas fa-mouse-pointer"></i> Clicca per dettagli | <i class="fas fa-arrows-alt"></i> Trascina per spostare
            </div>
        `;
        
        document.body.appendChild(customTooltip);
        
        // Posiziona il tooltip con controllo dei bordi dello schermo
        const rect = customTooltip.getBoundingClientRect();
        const windowWidth = window.innerWidth;
        const windowHeight = window.innerHeight;
        
        let left = mouseEvent.pageX - rect.width / 2;
        let top = mouseEvent.pageY - rect.height - 10;
        
        // Controlla bordo destro
        if (left + rect.width > windowWidth) {
            left = windowWidth - rect.width - 10;
        }
        
        // Controlla bordo sinistro
        if (left < 10) {
            left = 10;
        }
        
        // Controlla bordo superiore
        if (top < 10) {
            top = mouseEvent.pageY + 10; // Mostra sotto il mouse
        }
        
        customTooltip.style.left = left + 'px';
        customTooltip.style.top = top + 'px';
    }
    
    function hideCustomTooltip() {
        if (customTooltip) {
            customTooltip.remove();
            customTooltip = null;
        }
    }
    
    // Funzioni helper per le classi CSS
    function getPriorityClass(priority) {
        const classes = {
            'bassa': 'secondary',
            'normale': 'info',
            'alta': 'warning',
            'urgente': 'danger'
        };
        return classes[priority] || 'secondary';
    }
    
    function getStatusClass(status) {
        const classes = {
            'programmata': 'primary',
            'in_corso': 'warning',
            'completata': 'success',
            'annullata': 'danger'
        };
        return classes[status] || 'secondary';
    }
    
    // Mostra dettagli evento in modal
    function showEventDetails(event) {
        const props = event.extendedProps;
        const startDate = new Date(event.start);
        const formattedDate = startDate.toLocaleDateString('it-IT', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const modalBody = document.getElementById('eventModalBody');
        modalBody.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-info-circle text-primary"></i> Informazioni Generali</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Titolo:</strong></td><td>${event.title}</td></tr>
                        <tr><td><strong>Tipo:</strong></td><td><i class="fas ${props.icona} mr-1"></i> ${props.tipo.charAt(0).toUpperCase() + props.tipo.slice(1)}</td></tr>
                        <tr><td><strong>Anagrafica:</strong></td><td>${props.anagrafica}</td></tr>
                        <tr><td><strong>Data:</strong></td><td>${formattedDate}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6><i class="fas fa-cogs text-info"></i> Stato e Priorità</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Stato:</strong></td><td><span class="badge badge-${getStatusClass(props.stato)}">${props.stato}</span></td></tr>
                        <tr><td><strong>Priorità:</strong></td><td><span class="badge badge-${getPriorityClass(props.priorita)}">${props.priorita}</span></td></tr>
                    </table>
                    
                    <div class="mt-3">
                        <a href="<?= base_url('pickup-delivery/show') ?>/${event.id}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> Visualizza Completo
                        </a>
                    </div>
                </div>
            </div>
        `;
        
        // Aggiorna il link di modifica
        document.getElementById('editEventBtn').href = `<?= base_url('pickup-delivery/edit') ?>/${event.id}`;
        
        $('#eventModal').modal('show');
    }
    
    // Aggiorna data evento via AJAX
    function updateEventDate(event, oldEvent) {
        const newDate = event.start.toISOString().slice(0, 19).replace('T', ' ');
        
        $.ajax({
            url: `<?= base_url('pickup-delivery/updateDate') ?>/${event.id}`,
            method: 'POST',
            data: {
                data_programmata: newDate,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    // Mostra messaggio di successo
                    showNotification('Successo', 'Data aggiornata con successo', 'success');
                } else {
                    // Ripristina la posizione originale
                    event.setStart(oldEvent.start);
                    event.setEnd(oldEvent.end);
                    calendar.render();
                    showNotification('Errore', response.message || 'Errore durante l\'aggiornamento', 'error');
                }
            },
            error: function() {
                // Ripristina la posizione originale
                event.setStart(oldEvent.start);
                event.setEnd(oldEvent.end);
                calendar.render();
                showNotification('Errore', 'Errore durante l\'aggiornamento della data', 'error');
            }
        });
    }
    
    // Funzione per mostrare notifiche
    function showNotification(title, message, type = 'success') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: message,
                icon: type,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        } else {
            alert(`${title}: ${message}`);
        }
    }
});
</script>
<?= $this->endSection() ?> 