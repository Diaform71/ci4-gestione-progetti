/**
 * Script per gestire i datepicker e la formattazione delle date.
 * Questo script deve essere incluso in tutte le pagine che utilizzano campi data.
 */

$(function() {
    // Configurazione globale del datepicker
    $.fn.datepicker.defaults.format = "dd/mm/yyyy";
    $.fn.datepicker.defaults.autoclose = true;
    $.fn.datepicker.defaults.language = "it";
    $.fn.datepicker.defaults.todayHighlight = true;
    $.fn.datepicker.defaults.todayBtn = "linked";
    $.fn.datepicker.defaults.clearBtn = true;
    
    // Applica il datepicker a tutti gli input con class="datepicker"
    $('.datepicker').datepicker();
    
    // Applica il datepicker a tutti gli input di tipo date
    $('input[type="text"][name$="data_scadenza"]').datepicker();
    
    // Funzione per verificare se una data è valida
    function isValidDate(dateString) {
        if (!dateString) return false;
        
        // Regex per formato dd/mm/yyyy
        const regex = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/;
        if (!regex.test(dateString)) return false;
        
        const match = dateString.match(regex);
        const day = parseInt(match[1], 10);
        const month = parseInt(match[2], 10) - 1;
        const year = parseInt(match[3], 10);
        
        // Crea un oggetto Date con i valori estratti
        const date = new Date(year, month, day);
        
        // Verifica che i valori estratti corrispondano alla data creata
        return date.getDate() === day && date.getMonth() === month && date.getFullYear() === year;
    }
    
    // Verifica date prima dell'invio del form
    $('form').on('submit', function(e) {
        let hasErrors = false;
        
        // Controlla tutti i campi data nel form
        $(this).find('input[type="text"][name$="data_scadenza"]').each(function() {
            const value = $(this).val();
            if (value && !isValidDate(value)) {
                alert('La data inserita non è valida. Utilizzare il formato GG/MM/AAAA');
                $(this).focus();
                hasErrors = true;
                return false;
            }
        });
        
        if (hasErrors) {
            e.preventDefault();
            return false;
        }
    });
}); 