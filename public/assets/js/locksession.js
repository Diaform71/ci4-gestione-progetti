//imposta il tempo di inattività (es. 5 minuti = 300.000 millisecondi)
let idleTime = 0;

const minute = 1; //minuti di attesa prima del blocco  //era 60

const lockTimeout = minute * 60 * 1000; // 5 minuti

// Resetta il timer di inattività
function resetIdleTime() {
    idleTime = 0;
}

//Incrementa il tempo di inattività
function timerIncrement() {
    idleTime += 1000; //incremento di 1 secondo
    if (idleTime >= lockTimeout) {
        // Lock the user session
        console.log(idleTime);
        lockSession();
    }
}

//funzione per bloccare la sessione
function lockSession() {
    
$.ajax({
    url: '/locksession',
    type: 'post',
    // dataType: 'json',
    // data: {},
    success: function(response) {
        if (response.success) {
            // Se la sessione è stata bloccata correttamente
            // alert('Sessione bloccata!');
            window.location.href = '/lockscreen'; // Reindirizza alla schermata di lockscreen
        }
    },
    error: function(xhr, status, error) {
        // Gestione degli errori
        console.error('Errore nella richiesta AJAX: ', error);
    }
});
}

//ascolta eventi di attività dell'utente
window.onload = function() {
    //resetta il timer quando l'utente interagisce
    document.addEventListener('mousemove', resetIdleTime);
    document.addEventListener('keypress', resetIdleTime);
    document.addEventListener('click', resetIdleTime);
    document.addEventListener('scroll', resetIdleTime);
};

//imposta un intervallo per incrementare il timer di inattività
setInterval(timerIncrement, 1000); //1 secondo