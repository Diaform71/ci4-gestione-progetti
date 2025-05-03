<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?? 'Condizioni Pagamento' ?><?= $this->endSection() ?>

<?= $this->section('page_title') ?><?= $title ?? 'Condizioni Pagamento' ?><?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?= base_url('condizioni-pagamento') ?>">Condizioni Pagamento</a></li>
<li class="breadcrumb-item active"><?= isset($condizione) ? $condizione['nome_breadcrumb'] : 'Nuova' ?></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Dettagli Condizione Pagamento</h3>
                </div>
                
                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                        <h4 class="alert-heading">Errori nel form!</h4>
                        <ul>
                            <?php foreach(session('errors') as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <!-- form start -->
                <form id="condizioniForm" role="form" method="post" action="<?= empty($condizione['id']) ? base_url('condizioni-pagamento/create') : base_url('condizioni-pagamento/update/'.$condizione['id']) ?>">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nome">Nome*</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="<?= old('nome', $condizione['nome']) ?>" required placeholder="Es. Bonifico 30gg">
                            <div class="help-block with-errors"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="descrizione">Descrizione</label>
                            <textarea class="form-control" id="descrizione" name="descrizione" rows="3" placeholder="Descrizione dettagliata della condizione di pagamento"><?= old('descrizione', $condizione['descrizione']) ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="giorni">Giorni</label>
                                    <input type="number" class="form-control" id="giorni" name="giorni" value="<?= old('giorni', $condizione['giorni']) ?>" min="0" placeholder="Numero di giorni">
                                    <small class="form-text text-muted">Inserire il numero di giorni per il pagamento (es. 30)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fine Mese</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input ignore-validate" id="fine_mese_checkbox" <?= (old('fine_mese', $condizione['fine_mese']) == 1) ? 'checked' : '' ?>>
                                        <input type="hidden" id="fine_mese" name="fine_mese" value="<?= old('fine_mese', $condizione['fine_mese']) ?>">
                                        <label class="custom-control-label" for="fine_mese_checkbox">Applicare fine mese</label>
                                    </div>
                                    <small class="form-text text-muted">Se selezionato, il pagamento è posticipato al fine mese</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Stato</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input ignore-validate" id="attivo_checkbox" <?= (old('attivo', $condizione['attivo']) == 1) ? 'checked' : '' ?>>
                                <input type="hidden" id="attivo" name="attivo" value="<?= old('attivo', $condizione['attivo']) ?>">
                                <label class="custom-control-label" for="attivo_checkbox">Attivo</label>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Salva</button>
                        <a href="<?= base_url('condizioni-pagamento') ?>" class="btn btn-secondary">Annulla</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script src="<?= base_url('plugins/jquery-validation/jquery.validate.min.js') ?>"></script>
<script src="<?= base_url('plugins/jquery-validation/additional-methods.min.js') ?>"></script>
<script src="<?= base_url('plugins/jquery-validation/localization/messages_it.js') ?>"></script>

<script>
$(document).ready(function() {
    // Controlla se ci sono input senza name nel form
    var checkEmptyNames = function() {
        $('input').each(function() {
            if ($(this).attr('name') === '' || $(this).attr('name') === undefined) {
                if (!$(this).hasClass('ignore-validate')) {
                    $(this).addClass('ignore-validate');
                }
            }
        });
    };

    // Esegui il controllo all'avvio
    checkEmptyNames();

    // Gestione checkbox Fine Mese
    $('#fine_mese_checkbox').change(function() {
        if($(this).is(':checked')) {
            $('#fine_mese').val(1);
        } else {
            $('#fine_mese').val(0);
        }
    });
    
    // Gestione checkbox Attivo
    $('#attivo_checkbox').change(function() {
        if($(this).is(':checked')) {
            $('#attivo').val(1);
        } else {
            $('#attivo').val(0);
        }
    });

    // Inizializza jQuery Validate
    $("#condizioniForm").validate({
        debug: false,
        ignore: ".ignore-validate",
        rules: {
            nome: {
                required: true,
                minlength: 3,
                maxlength: 100
            },
            giorni: {
                required: false,
                digits: true,
                min: 0
            },
            fine_mese: {
                required: true
            },
            attivo: {
                required: true
            }
        },
        messages: {
            nome: {
                required: "Il nome è obbligatorio",
                minlength: "Il nome deve essere di almeno 3 caratteri",
                maxlength: "Il nome non può superare i 100 caratteri"
            },
            giorni: {
                digits: "Inserire solo numeri interi",
                min: "Il valore deve essere maggiore o uguale a 0"
            },
            fine_mese: {
                required: "Il campo fine mese è obbligatorio"
            },
            attivo: {
                required: "Il campo stato è obbligatorio"
            }
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        },
        submitHandler: function(form) {
            // Verifica ulteriore prima dell'invio
            if ($("#nome").val().trim() === '') {
                $("#nome").addClass('is-invalid');
                $("<span id='nome-error' class='invalid-feedback'>Il nome è obbligatorio</span>").insertAfter("#nome");
                return false;
            }
            form.submit();
        }
    });
    
    // Assicura che il form non venga inviato se la validazione fallisce
    $("#condizioniForm").on('submit', function(e) {
        if (!$(this).valid()) {
            e.preventDefault();
            return false;
        }
        return true;
    });
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?> 