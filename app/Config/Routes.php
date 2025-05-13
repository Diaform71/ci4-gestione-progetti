<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

// Rotte per l'autenticazione
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
$routes->get('cambio-password', 'Auth::cambioPassword');
$routes->post('cambio-password', 'Auth::aggiornaPassword');

// Rotte per il profilo utente
$routes->group('profilo', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'Utenti::profilo');
    $routes->get('modifica', 'Utenti::modificaProfilo');
    $routes->post('aggiorna', 'Utenti::aggiornaProfilo');
});

// Rotte per le anagrafiche
$routes->group('anagrafiche', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'Anagrafiche::index');
    $routes->get('new', 'Anagrafiche::new');
    $routes->post('create', 'Anagrafiche::create');
    $routes->get('show/(:num)', 'Anagrafiche::show/$1');
    $routes->get('edit/(:num)', 'Anagrafiche::edit/$1');
    $routes->post('update/(:num)', 'Anagrafiche::update/$1');
    $routes->get('delete/(:num)', 'Anagrafiche::delete/$1');
    $routes->get('fornitori', 'Anagrafiche::getFornitori');
    $routes->get('clienti', 'Anagrafiche::getClienti');
});

// Rotte per le aliquote IVA
$routes->group('aliquote-iva', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'AliquoteIva::index');
    $routes->get('new', 'AliquoteIva::new');
    $routes->post('create', 'AliquoteIva::create');
    $routes->get('edit/(:num)', 'AliquoteIva::edit/$1');
    $routes->post('update/(:num)', 'AliquoteIva::update/$1');
    $routes->get('delete/(:num)', 'AliquoteIva::delete/$1');
    $routes->get('list', 'AliquoteIva::getAliquoteIva');
});

// Rotte per i materiali
$routes->group('materiali', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'Materiali::index');
    $routes->get('new', 'Materiali::new');
    $routes->post('create', 'Materiali::create');
    $routes->get('show/(:num)', 'Materiali::show/$1');
    $routes->get('edit/(:num)', 'Materiali::edit/$1');
    $routes->post('update/(:num)', 'Materiali::update/$1');
    $routes->get('delete/(:num)', 'Materiali::delete/$1');
    $routes->get('search', 'Materiali::search');
    $routes->get('barcode/(:num)', 'Materiali::barcode/$1');
});

// Rotte per i contatti
$routes->group('contatti', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'Contatti::index');
    $routes->get('new', 'Contatti::new');
    $routes->post('create', 'Contatti::create');
    $routes->get('show/(:num)', 'Contatti::show/$1');
    $routes->get('edit/(:num)', 'Contatti::edit/$1');
    $routes->post('update/(:num)', 'Contatti::update/$1');
    $routes->get('delete/(:num)', 'Contatti::delete/$1');
});

// Rotte per i progetti
$routes->group('progetti', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'ProgettiController::index');
    $routes->get('new', 'ProgettiController::new');
    $routes->post('create', 'ProgettiController::create');
    $routes->get('(:num)', 'ProgettiController::show/$1');
    $routes->get('edit/(:num)', 'ProgettiController::edit/$1');
    $routes->post('update/(:num)', 'ProgettiController::update/$1');
    $routes->get('delete/(:num)', 'ProgettiController::delete/$1');
    $routes->post('stato/(:num)', 'ProgettiController::updateStato/$1');
    $routes->post('fase-kanban/(:num)', 'ProgettiController::updateFaseKanban/$1');
    $routes->get('toggle-attivo/(:num)', 'ProgettiController::toggleAttivo/$1');
    $routes->get('kanban', 'ProgettiController::kanban');
    $routes->get('in-scadenza/(:num)', 'ProgettiController::inScadenza/$1');
    $routes->get('in-scadenza', 'ProgettiController::inScadenza');
    $routes->get('per-anagrafica/(:num)', 'ProgettiController::perAnagrafica/$1');
    $routes->get('barcode-materiale/(:num)/(:num)', 'ProgettiController::barcodeMateriale/$1/$2');
    $routes->post('barcode-materiali/(:num)', 'ProgettiController::barcodeProgettoMateriali/$1');
    
    // Rotte per la gestione dei materiali nei progetti
    $routes->post('aggiungi-materiale/(:num)', 'ProgettiController::aggiungiMateriale/$1');
    $routes->post('aggiungi-nuovo-materiale/(:num)', 'ProgettiController::aggiungiNuovoMateriale/$1');
    $routes->post('aggiorna-materiale/(:num)', 'ProgettiController::aggiornaMateriale/$1');
    $routes->get('rimuovi-materiale/(:num)/(:num)', 'ProgettiController::rimuoviMateriale/$1/$2');

    // Rotte per l'importazione Excel
    $routes->post('analizza-excel', 'ProgettiController::analizzaExcel');
    $routes->post('analizza-foglio', 'ProgettiController::analizzaFoglio');
    $routes->post('anteprima-importazione', 'ProgettiController::anteprimaImportazione');
    $routes->post('importa-materiali-excel/(:num)', 'ProgettiController::importaMaterialiExcel/$1');
});

// Rotte per i documenti
$routes->group('documenti', ['filter' => 'auth'], function ($routes) {
    $routes->post('upload', 'Documenti::upload');
    $routes->post('update', 'Documenti::update');
    $routes->get('download/(:num)', 'Documenti::download/$1');
    $routes->get('delete/(:num)', 'Documenti::delete/$1');
});

$routes->get('api/anagrafiche/(:num)/contatti', 'AnagraficheContatti::getByAnagrafica/$1');
$routes->get('api/contatti/(:num)/anagrafiche', 'AnagraficheContatti::getByContatto/$1');
$routes->post('api/anagrafiche/contatti', 'AnagraficheContatti::create');
$routes->put('api/anagrafiche/contatti/(:num)', 'AnagraficheContatti::update/$1');
$routes->put('api/anagrafiche/(:num)/contatti/(:num)/principale', 'AnagraficheContatti::setPrincipale/$1/$2');
$routes->delete('api/anagrafiche/contatti/(:num)', 'AnagraficheContatti::delete/$1');
$routes->get('api/anagrafiche/getAnagrafiche', 'AnagraficheContatti::getAnagrafiche');

// DECOMMENTARE ROUTE
$routes->get('api/contatti/getContatti', 'Contatti::getContatti');
$routes->get('api/contatti/getActiveContatti', 'Contatti::getActiveContatti');

// Rotte per le attività
$routes->group('attivita', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Attivita::index');
    $routes->get('new/(:num)', 'Attivita::new/$1');
    $routes->get('new', 'Attivita::new');
    $routes->post('create', 'Attivita::create');
    $routes->get('view/(:num)', 'Attivita::view/$1');
    $routes->get('edit/(:num)', 'Attivita::edit/$1');
    $routes->post('update', 'Attivita::update');
    $routes->get('delete/(:num)', 'Attivita::delete/$1');
    $routes->post('cambiaStato', 'Attivita::cambiaStato');
    $routes->post('creaSottoAttivita', 'Attivita::creaSottoAttivita');
    $routes->post('aggiornaSottoAttivita', 'Attivita::aggiornaSottoAttivita');
    $routes->get('eliminaSottoAttivita/(:num)', 'Attivita::eliminaSottoAttivita/$1');
    $routes->get('per-progetto/(:num)', 'Attivita::perProgetto/$1');
});

// Rotte per le scadenze
$routes->group('scadenze', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Scadenze::index');
    $routes->get('nuovo', 'Scadenze::nuovo');
    $routes->post('salva', 'Scadenze::salva');
    $routes->get('modifica/(:num)', 'Scadenze::modifica/$1');
    $routes->get('dettaglio/(:num)', 'Scadenze::dettaglio/$1');
    $routes->get('elimina/(:num)', 'Scadenze::elimina/$1');
    $routes->get('completa/(:num)', 'Scadenze::completa/$1');
    $routes->get('progetto/(:num)', 'Scadenze::progetto/$1');
    $routes->get('attivita/(:num)', 'Scadenze::attivita/$1');
    $routes->get('inScadenza', 'Scadenze::inScadenza');
    $routes->get('scadute', 'Scadenze::scadute');
    $routes->get('calendario_eventi', 'Scadenze::calendarioEventi');
});

// Rotte per le richieste d'offerta
$routes->group('richieste-offerta', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'RichiesteOffertaController::index');
    $routes->get('new', 'RichiesteOffertaController::new');
    $routes->post('create', 'RichiesteOffertaController::create');
    $routes->post('create-from-project', 'RichiesteOffertaController::createFromProject');
    $routes->get('(:num)', 'RichiesteOffertaController::show/$1');
    $routes->get('edit/(:num)', 'RichiesteOffertaController::edit/$1');
    $routes->post('update/(:num)', 'RichiesteOffertaController::update/$1');
    $routes->post('cambia-stato/(:num)', 'RichiesteOffertaController::cambiaStato/$1');
    $routes->get('delete/(:num)', 'RichiesteOffertaController::delete/$1');
    $routes->post('get-contatti-by-anagrafica', 'RichiesteOffertaController::getContattiByAnagrafica');
    $routes->post('get-richieste-by-fornitore', 'RichiesteOffertaController::getRichiesteByFornitore');
    $routes->get('per-fornitore/(:num)', 'RichiesteOffertaController::perFornitore/$1');
    $routes->get('per-progetto/(:num)', 'RichiesteOffertaController::perProgetto/$1');

    // Rotte per la gestione dei materiali nelle richieste d'offerta
    $routes->post('aggiungi-materiale/(:num)', 'RichiesteOffertaController::aggiungiMateriale/$1');
    $routes->post('aggiungi-nuovo-materiale/(:num)', 'RichiesteOffertaController::aggiungiNuovoMateriale/$1');
    $routes->post('aggiorna-materiale/(:num)', 'RichiesteOffertaController::aggiornaMateriale/$1');
    $routes->get('rimuovi-materiale/(:num)/(:num)', 'RichiesteOffertaController::rimuoviMateriale/$1/$2');
    
    // Rotte per la gestione delle email
    $routes->post('invia-email/(:num)', 'RichiesteOffertaController::inviaEmail/$1');
    $routes->get('email-log/(:num)', 'RichiesteOffertaController::emailLog/$1');
    $routes->get('visualizza-email/(:num)', 'RichiesteOffertaController::visualizzaEmail/$1');
    $routes->post('rispondi-email/(:num)', 'RichiesteOffertaController::rispondiEmail/$1');
});

// Rotte per le impostazioni
$routes->group('impostazioni', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ImpostazioniController::index');
    $routes->get('utente', 'ImpostazioniController::utente');
    $routes->get('nuova', 'ImpostazioniController::nuova');
    $routes->post('salva', 'ImpostazioniController::salva');
    $routes->get('modifica/(:num)', 'ImpostazioniController::modifica/$1');
    $routes->post('aggiorna/(:num)', 'ImpostazioniController::aggiorna/$1');
    $routes->get('elimina/(:num)', 'ImpostazioniController::elimina/$1');
    $routes->post('salva-utente', 'ImpostazioniController::salvaImpostazioniUtente');
    $routes->get('reimposta/(:num)', 'ImpostazioniController::reimpostaDefault/$1');
});

// Rotte per le condizioni di pagamento
$routes->group('condizioni-pagamento', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'CondizioniPagamentoController::index');
    $routes->get('new', 'CondizioniPagamentoController::new');
    $routes->post('create', 'CondizioniPagamentoController::create');
    $routes->get('edit/(:num)', 'CondizioniPagamentoController::edit/$1');
    $routes->post('update/(:num)', 'CondizioniPagamentoController::update/$1');
    $routes->get('delete/(:num)', 'CondizioniPagamentoController::delete/$1');
});

// Rotte per i template email
$routes->group('email-templates', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'EmailTemplateController::index');
    $routes->get('nuovo', 'EmailTemplateController::nuovo');
    $routes->post('salva', 'EmailTemplateController::salva');
    $routes->get('modifica/(:num)', 'EmailTemplateController::modifica/$1');
    $routes->get('dettaglio/(:num)', 'EmailTemplateController::dettaglio/$1');
    $routes->post('elimina/(:num)', 'EmailTemplateController::elimina/$1');
    $routes->get('anteprima/(:num)', 'EmailTemplateController::anteprima/$1');
    $routes->get('get-by-type/(:any)', 'EmailTemplateController::getByType/$1');
    $routes->post('compila/(:num)', 'EmailTemplateController::compila/$1');
    $routes->get('get/(:num)', 'EmailTemplateController::get/$1');
    $routes->post('compila-ordine/(:num)', 'EmailTemplateController::compilaOrdine/$1');
});

// PDF Routes
$routes->group('pdf', ['filter' => 'auth'], function ($routes) {
    $routes->get('openRDO/(:num)', 'PdfController::openRDO/$1');
    $routes->get('openOfferta/(:num)', 'PdfController::openOfferta/$1');
    $routes->get('openOrdine/(:num)', 'PdfController::openOrdine/$1');
    $routes->get('openOffertaFornitore/(:num)', 'PdfController::openOffertaFornitore/$1');
    $routes->get('openOrdineMateriale/(:num)', 'PdfController::openOrdineMateriale/$1');
});

// Rotte per l'installazione
$routes->group('install', function ($routes) {
    $routes->get('', 'InstallController::index');
    $routes->get('requirements', 'InstallController::requirements');
    $routes->match(['GET', 'POST'], 'database', 'InstallController::database');
    $routes->match(['GET', 'POST'], 'migrate', 'InstallController::migrate');
    $routes->get('complete', 'InstallController::complete');
});

// Rotta per verificare l'installazione - esclusa dal filtro di installazione
$routes->get('check-installation', 'InstallController::checkInstallation');

// Rotta di diagnostica per testare il funzionamento
$routes->get('test', 'TestController::index');

// Rotte per le offerte fornitore
$routes->group('offerte-fornitore', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'OfferteFornitoreController::index');
    $routes->get('new', 'OfferteFornitoreController::new');
    $routes->post('create', 'OfferteFornitoreController::create');
    $routes->get('(:num)', 'OfferteFornitoreController::show/$1');
    $routes->get('edit/(:num)', 'OfferteFornitoreController::edit/$1');
    $routes->match(['POST', 'PUT'], 'update/(:num)', 'OfferteFornitoreController::update/$1');
    $routes->post('cambia-stato/(:num)', 'OfferteFornitoreController::cambiaStato/$1');
    $routes->get('cambia-stato/(:num)', 'OfferteFornitoreController::cambiaStato/$1');
    $routes->get('delete/(:num)', 'OfferteFornitoreController::delete/$1');
    $routes->get('download-allegato/(:num)', 'OfferteFornitoreController::downloadAllegato/$1');
    $routes->get('delete-allegato/(:num)', 'OfferteFornitoreController::deleteAllegato/$1');
    $routes->post('carica-allegato/(:num)', 'OfferteFornitoreController::caricaAllegato/$1');
    $routes->post('carica-allegato-dropzone/(:num)', 'OfferteFornitoreController::caricaAllegatoDropzone/$1');
    $routes->post('aggiungi-voce/(:num)', 'OfferteFornitoreController::aggiungiVoce/$1');
    $routes->post('aggiungi-materiale-voce/(:num)', 'OfferteFornitoreController::aggiungiMaterialeVoce/$1');
    $routes->post('aggiorna-voce/(:num)', 'OfferteFornitoreController::aggiornaVoce/$1');
    $routes->get('rimuovi-voce/(:num)/(:num)', 'OfferteFornitoreController::rimuoviVoce/$1/$2');
    $routes->post('importa-voci-richiesta/(:num)', 'OfferteFornitoreController::importaVociRichiesta/$1');
    $routes->post('aggiorna-costi/(:num)', 'OfferteFornitoreController::aggiornaCosti/$1');
    $routes->get('per-fornitore/(:num)', 'OfferteFornitoreController::perFornitore/$1');
    $routes->get('per-progetto/(:num)', 'OfferteFornitoreController::perProgetto/$1');
    $routes->get('per-richiesta/(:num)', 'OfferteFornitoreController::perRichiesta/$1');
    $routes->get('search', 'OfferteFornitoreController::search');
    $routes->get('get-voci/(:num)', 'OfferteFornitoreController::getVoci/$1');
});

// Rotte per gli ordini di materiale
$routes->group('ordini-materiale', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'OrdiniMaterialeController::index');
    $routes->get('new', 'OrdiniMaterialeController::new');
    $routes->post('create', 'OrdiniMaterialeController::create');
    $routes->get('edit/(:num)', 'OrdiniMaterialeController::edit/$1');
    $routes->post('update/(:num)', 'OrdiniMaterialeController::update/$1');
    $routes->get('delete/(:num)', 'OrdiniMaterialeController::delete/$1');
    $routes->get('(:num)', 'OrdiniMaterialeController::show/$1');
    $routes->get('cambia-stato/(:num)', 'OrdiniMaterialeController::cambiaStato/$1');
    $routes->post('cambia-stato/(:num)', 'OrdiniMaterialeController::cambiaStato/$1');
    $routes->get('per-fornitore/(:num)', 'OrdiniMaterialeController::perFornitore/$1');
    $routes->get('per-progetto/(:num)', 'OrdiniMaterialeController::perProgetto/$1');
    $routes->get('forza-aggiorna-importo/(:num)', 'OrdiniMaterialeController::forzaAggiornaImporto/$1');
    $routes->post('aggiungi-materiale/(:num)', 'OrdiniMaterialeController::aggiungiMateriale/$1');
    $routes->post('aggiungi-nuovo-materiale/(:num)', 'OrdiniMaterialeController::aggiungiNuovoMateriale/$1');
    $routes->post('aggiorna-materiale/(:num)', 'OrdiniMaterialeController::aggiornaMateriale/$1');
    $routes->post('aggiorna-costi/(:num)', 'OrdiniMaterialeController::aggiornaCosti/$1');
    $routes->get('rimuovi-materiale/(:num)/(:num)', 'OrdiniMaterialeController::rimuoviMateriale/$1/$2');
    
    // Nuove route per la gestione degli allegati
    $routes->post('carica-allegato/(:num)', 'OrdiniMaterialeController::caricaAllegato/$1');
    $routes->get('download-allegato/(:num)', 'OrdiniMaterialeController::downloadAllegato/$1');
    $routes->get('delete-allegato/(:num)', 'OrdiniMaterialeController::deleteAllegato/$1');
    
    // Route per l'email
    $routes->get('invia-email/(:num)', 'OrdiniMaterialeController::inviaEmail/$1');
    $routes->post('invia-email/(:num)', 'OrdiniMaterialeController::inviaEmail/$1');
    $routes->get('email-log/(:num)', 'OrdiniMaterialeController::emailLog/$1');
    $routes->get('visualizza-email/(:num)', 'OrdiniMaterialeController::visualizzaEmail/$1');
    
    // Route AJAX
    $routes->post('get-contatti-by-anagrafica', 'OrdiniMaterialeController::getContattiByAnagrafica');
    $routes->post('get-ordini-by-fornitore', 'OrdiniMaterialeController::getOrdiniByFornitore');
    $routes->post('importa-voci-offerta/(:num)', 'OrdiniMaterialeController::importaVociOfferta/$1');
});

// Gestione Utenti (solo admin)
$routes->group('utenti', static function ($routes) {
    $routes->get('/', 'Utenti::index');
    $routes->get('new', 'Utenti::new');
    $routes->post('create', 'Utenti::create');
    $routes->get('edit/(:num)', 'Utenti::edit/$1');
    $routes->post('update/(:num)', 'Utenti::update/$1');
    $routes->get('delete/(:num)', 'Utenti::delete/$1');
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
