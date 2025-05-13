<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Dettaglio Email<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Dettaglio Email - Ordine #<?= esc($ordine['numero']) ?><?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= site_url('ordini-materiale') ?>">Ordini Materiale</a></li>
<li class="breadcrumb-item"><a href="<?= site_url('ordini-materiale/' . $ordine['id']) ?>">Ordine #<?= esc($ordine['numero']) ?></a></li>
<li class="breadcrumb-item"><a href="<?= site_url('ordini-materiale/email-log/' . $ordine['id']) ?>">Storico Email</a></li>
<li class="breadcrumb-item active">Dettaglio Email</li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <?= view('layouts/partials/_alert') ?>

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="btn-group">
                <a href="<?= site_url('ordini-materiale/email-log/' . $ordine['id']) ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Torna allo storico
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Dettaglio Email</h3>
                    <div class="card-tools">
                        <?php if ($email['stato'] === 'inviato') : ?>
                            <span class="badge badge-success">Inviata</span>
                        <?php else : ?>
                            <span class="badge badge-danger">Errore</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Data invio:</strong> <?= date('d/m/Y H:i', strtotime($email['data_invio'])) ?></p>
                            <?php
                            // Recupera info mittente dall'id_utente se disponibile
                            $mittente = 'Sistema';
                            if (!empty($email['id_utente'])) {
                                $utentiModel = new \App\Models\UtentiModel();
                                $utente = $utentiModel->find($email['id_utente']);
                                if ($utente) {
                                    $mittente = esc($utente['nome'] . ' ' . $utente['cognome']);
                                    if (!empty($utente['email'])) {
                                        $mittente .= ' (' . esc($utente['email']) . ')';
                                    }
                                }
                            } else if (isset($email['mittente']) && !empty($email['mittente'])) {
                                $mittente = esc($email['mittente']);
                            }
                            ?>
                            <p><strong>Mittente:</strong> <?= $mittente ?></p>
                            <p><strong>Destinatario:</strong> <?= esc($email['destinatario']) ?></p>
                            
                            <?php if (!empty($email['cc'])) : ?>
                                <p><strong>CC:</strong> <?= esc($email['cc']) ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($email['ccn'])) : ?>
                                <p><strong>CCN:</strong> <?= esc($email['ccn']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Oggetto:</strong> <?= esc($email['oggetto']) ?></p>
                            
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
                            
                            // Visualizza allegati se presenti
                            $allegati = !empty($email['allegati']) ? safe_unserialize($email['allegati']) : [];
                            if (!empty($allegati)) : 
                            ?>
                                <p><strong>Allegati:</strong></p>
                                <ul class="list-group">
                                    <?php foreach ($allegati as $allegato) : ?>
                                        <li class="list-group-item">
                                            <i class="fas fa-paperclip"></i> <?= esc($allegato) ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <p><strong>Corpo dell'email:</strong></p>
                            <div class="card">
                                <div class="card-body">
                                    <!-- Aggiungiamo un div di fallback in caso l'iframe non funzioni -->
                                    <div id="emailCorpoFallback" style="display:none; padding:10px; border:1px solid #f0f0f0; max-height:500px; overflow-y:auto;"></div>
                                    <iframe id="emailCorpo" style="width:100%; min-height:400px; border:none;"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <?php if (!empty($email['data_invio'])) : ?>
                        <small class="text-muted">Inviato il <?= date('d/m/Y H:i:s', strtotime($email['data_invio'])) ?></small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Assicurati che ci sia un corpo dell'email da visualizzare
    var emailBody = <?= json_encode(isset($email['corpo']) ? $email['corpo'] : '') ?>;
    
    if (!emailBody) {
        // Se non c'è corpo, mostra un messaggio
        $("#emailCorpo").hide();
        $("#emailCorpoFallback").show().html('<em>Nessun contenuto disponibile</em>');
        return;
    }
    
    // Prova a caricare il corpo dell'email nell'iframe
    try {
        var iframe = document.getElementById('emailCorpo');
        iframe.contentWindow.document.open();
        iframe.contentWindow.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><style>body { font-family: Arial, sans-serif; padding: 10px; }</style></head><body>' + emailBody + '</body></html>');
        iframe.contentWindow.document.close();
        
        // Regola l'altezza dell'iframe in base al contenuto
        setTimeout(function() {
            try {
                var height = iframe.contentWindow.document.body.scrollHeight;
                iframe.style.height = (height + 30) + 'px';
            } catch (e) {
                console.error("Errore nel ridimensionamento dell'iframe:", e);
                // Se fallisce il ridimensionamento, impostiamo un'altezza predefinita
                iframe.style.height = "500px";
            }
        }, 300);
        
        // Registriamo un handler di errore per l'iframe
        iframe.onerror = function() {
            fallbackToDiv(emailBody);
        };
    } catch (e) {
        console.error("Errore nel caricamento dell'iframe:", e);
        fallbackToDiv(emailBody);
    }
    
    // Funzione per usare il div di fallback se l'iframe fallisce
    function fallbackToDiv(content) {
        $("#emailCorpo").hide();
        $("#emailCorpoFallback").show().html(content);
    }
});
</script>
<?= $this->endSection() ?> 