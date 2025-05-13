<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Storico Email - Ordine #<?= esc($ordine['numero']) ?><?= $this->endSection() ?>

<?= $this->section('page_title') ?>Storico Email - Ordine #<?= esc($ordine['numero']) ?><?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= site_url('ordini-materiale') ?>">Ordini Materiale</a></li>
<li class="breadcrumb-item"><a href="<?= site_url('ordini-materiale/' . $ordine['id']) ?>">Ordine #<?= esc($ordine['numero']) ?></a></li>
<li class="breadcrumb-item active">Storico Email</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <?= view('layouts/partials/_alert') ?>

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="btn-group">
                <a href="<?= site_url('ordini-materiale/' . $ordine['id']) ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Torna all'ordine
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Email inviate per l'ordine #<?= esc($ordine['numero']) ?></h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Destinatario</th>
                            <th>Oggetto</th>
                            <th>Stato</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($emails)) : ?>
                            <tr>
                                <td colspan="5" class="text-center">Nessuna email inviata per questo ordine</td>
                            </tr>
                        <?php else : ?>
                            <?php 
                            // Funzione per deserializzare in modo sicuro
                            function safe_unserialize($data) {
                                if (empty($data)) return [];
                                
                                try {
                                    $result = @unserialize($data);
                                    return $result !== false ? $result : [];
                                } catch (Exception $e) {
                                    return [];
                                }
                            }
                            ?>
                            
                            <?php foreach ($emails as $email) : ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($email['data_invio'])) ?></td>
                                    <td>
                                        <?= esc($email['destinatario']) ?>
                                        <?php if (!empty($email['cc'])) : ?>
                                            <small class="d-block text-muted">CC: <?= esc($email['cc']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($email['oggetto']) ?></td>
                                    <td>
                                        <?php if ($email['stato'] === 'inviato') : ?>
                                            <span class="badge badge-success">Inviata</span>
                                        <?php else : ?>
                                            <span class="badge badge-danger">Errore</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info visualizzaEmail" 
                                                data-id="<?= $email['id'] ?>"
                                                data-oggetto="<?= esc($email['oggetto']) ?>"
                                                data-corpo="<?= htmlspecialchars($email['corpo']) ?>"
                                                data-destinatario="<?= esc($email['destinatario']) ?>"
                                                data-cc="<?= esc($email['cc'] ?? '') ?>"
                                                data-ccn="<?= esc($email['ccn'] ?? '') ?>"
                                                data-data="<?= date('d/m/Y H:i', strtotime($email['data_invio'])) ?>"
                                                data-stato="<?= $email['stato'] ?>"
                                                data-allegati='<?= json_encode(safe_unserialize($email['allegati'] ?? '')) ?>'>
                                            <i class="fas fa-eye"></i> Visualizza
                                        </button>
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
                                <!-- Fallback per il corpo dell'email -->
                                <div id="emailCorpoFallback" style="display:none; padding:10px; border:1px solid #f0f0f0; max-height:400px; overflow-y:auto;"></div>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
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

            // Visualizza il corpo dell'email nell'iframe o nel fallback
            try {
                var iframe = document.getElementById('emailCorpoFrame');
                $('#emailCorpoFallback').hide();
                iframe.style.display = 'block';
                
                iframe.contentWindow.document.open();
                iframe.contentWindow.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><style>body { font-family: Arial, sans-serif; padding: 10px; }</style></head><body>' + corpo + '</body></html>');
                iframe.contentWindow.document.close();
                
                // Regola l'altezza dell'iframe in base al contenuto
                setTimeout(function() {
                    try {
                        var height = iframe.contentWindow.document.body.scrollHeight;
                        iframe.style.height = (height + 30) + 'px';
                    } catch (e) {
                        console.error("Errore nel ridimensionamento dell'iframe:", e);
                        iframe.style.height = "400px";
                    }
                }, 300);
            } catch (e) {
                console.error("Errore nella visualizzazione del corpo dell'email:", e);
                $('#emailCorpoFrame').hide();
                $('#emailCorpoFallback').show().html(corpo || '<em>Nessun contenuto disponibile</em>');
            }

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
    });
</script>
<?= $this->endSection() ?> 