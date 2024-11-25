<?php $this->startSection('title'); ?>
Czat
<?php $this->endSection(); ?>

<style>
    .footer {
        display: none;
    }

    html,
    body {
        overflow: hidden;
    }
    .is-borderless {
        border:none;
    }
    .is-absolute {
        position: absolute;
    }
    .is-centered-x {
        left:50%;
        transform: translate(-50%, 0);
    }
</style>

<div class="columns is-gapless m-0" style="height:calc(100vh - 64px);">
    <div class="column">
        <article class="panel is-info is-radiusless is-shadowless">
            <p class="panel-heading is-radiusless">Czaty</p>
            <div class="panel-block">
                <p class="control has-icons-left">
                    <input class="input" type="text" placeholder="Szukaj" />
                    <span class="icon is-left">
                        <i class="fas fa-search" aria-hidden="true"></i>
                    </span>
                </p>
            </div>
            <div style="overflow-y: auto;">
                <a class="panel-block is-active">
                    <span class="panel-icon">
                        <i class="fas fa-book" aria-hidden="true"></i>
                    </span>
                    Jan Nowak
                </a>
                <a class="panel-block">
                    <span class="panel-icon">
                        <i class="fas fa-book" aria-hidden="true"></i>
                    </span>
                    Anna Ptak
                </a>
                <a class="panel-block">
                    <span class="panel-icon">
                        <i class="fas fa-book" aria-hidden="true"></i>
                    </span>
                    Konrad Łoboda
                </a>
                <a class="panel-block">
                    <span class="panel-icon">
                        <i class="fas fa-book" aria-hidden="true"></i>
                    </span>
                    Ryszard Rutkiewicz
                </a>
            </div>
        </article>
    </div>
    <div class="column is-three-fifths" style="border-left:1px solid rgb(46, 51, 61);border-right:1px solid rgb(46, 51, 61)">
        <div class="panel is-radiusless is-shadowless m-0">
            <p class="panel-heading is-radiusless" style="background-color: transparent;border-bottom:1px solid rgb(46, 51, 61);">Koszenie trawnika</p>
        </div>
        <div class="is-relative" style="height:calc(100% - 180px);">
            <div class="p-5" id="chat-messages-container" style="height:100%; overflow-y: auto;">

            </div>
            <span class="tag is-primary is-medium is-rounded is-unselectables is-hidden is-absolute is-centered-x" id="typing-notification" style="bottom:20px"><?= $chat->other_user_first_name ?> pisze<span id="is-typing-dots">...</span></span>
        </div>
        <div class="field has-addons p-5" style="border-top:1px solid rgb(46, 51, 61);">
            <div class="control is-expanded">
                <input class="input" type="text" id="msg-text" placeholder="Wiadomość">
            </div>
            <div class="control">
                <button class="button is-info" id="send-msg-btn">
                    Wyślij
                </button>
            </div>
        </div>
    </div>
    <div class="column">
        <div class="p-5">
            <strong class="title is-4">O tym ogłoszeniu:</strong>

            <p class="subtitle mt-4">Imię zleceniodawcy: <?= $job->first_name ?></p>

            <?php if (!empty($job->job_type)): ?>
                <p class="subtitle">Typ pracy: <?= $job->job_type ?></p>
            <?php endif ?>

            <?php if (!empty($job->description)): ?>
                <div class="subtitle">Opis
                    <p style="max-height:100px;min-height:50px;overflow-y:auto;"><?= $job->description ?></p>
                </div>
            <?php endif ?>

            <?php if (!empty($job->payment_type)): ?>
                <p class="subtitle">Rodzaj wynagrodzenia: <?= $job->payment_type ?></p>
            <?php endif ?>

            <?php if (!empty($job->payment)): ?>
                <p class="subtitle">Stawka: <?= $job->payment ?> zł</p>
            <?php endif ?>

            <?php if (!empty($job->estimated_time)): ?>
                <p class="subtitle">Szacowany czas pracy: <?= $job->estimated_time ?> godzin</p>
            <?php endif ?>

            <?php if (!empty($job->address)): ?>
                <p class="subtitle">Adres: <?= $job->address ?></p>
            <?php endif ?>

            <?php if (!empty($job->city)): ?>
                <p class="subtitle">Miasto: <?= $job->city ?></p>
            <?php endif ?>

            <?php if (!empty($job->equipment)): ?>
                <p class="subtitle">Sprzęt wymagany: <?= $job->equipment ?></p>
            <?php endif ?>

            <?php if (!empty($job->requirements)): ?>
                <p class="subtitle">Wymagania: <?= $job->requirements ?></p>
            <?php endif ?>

            <?php if (!empty($job->offer)): ?>
                <p class="subtitle">Oferta dodatkowa: <?= $job->offer ?></p>
            <?php endif ?>
        </div>
    </div>
</div>

<?php $this->startSection('scripts'); ?>
<script>
    const userId = <?= $_SESSION['user_id'] ?>;
    const chatId = <?= $chat->chat_id ?>;
    const jobId = <?= $chat->job_id ?>;

    const msgText = document.getElementById('msg-text');
    const sendMsgBtn = document.getElementById('send-msg-btn');
    const chatMessagesContainer = document.getElementById('chat-messages-container');

    const typingNotification = document.getElementById('typing-notification');
    const isTypingDots = document.getElementById('is-typing-dots');

    let dots = 0;
    setInterval(() => {
        isTypingDots.innerText = '.'.repeat(dots = (dots < 3 ? dots + 1 : 0));
    }, 500);

    sendMsgBtn.addEventListener('click', () => sendMessage(msgText.value));
    
    fetch(`/czat/historia/${jobId}`)
    .then(response => response.json())
    .then(messages => {
        messages.forEach(message => {
            showMessage(message);
        });
        chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
    });
    
    let conn = null;

    // Funkcja inicjalizująca połączenie WebSocket
    function initWebSocket() {
        try {
            conn = new WebSocket(`ws://localhost:8080?user_id=${userId}&chat_id=${chatId}`);

            conn.onopen = function() {
                console.log("Połączono z serwerem WebSocket");
            };

            conn.onclose = function(e) {
                console.log("Rozłączono z serwerem WebSocket, próba ponownego połączenia...");
                setTimeout(initWebSocket, 3000); // Próba ponownego połączenia po 3 sekundach
            };

            conn.onerror = function(e) {
                console.error("Błąd WebSocket:", e);
                conn.close(); // Zamknij połączenie w przypadku błędu
            };

            conn.onmessage = function(e) {
                const data = JSON.parse(e.data);

                switch(data.type) {
                    case 'message':
                        showMessage(data);
                        chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
                        break;
                    case 'typing':
                        showOtherPersonTyping(data.status);
                        break;
                }
            };
        } catch (e) {
            console.error("Nie udało się nawiązać połączenia WebSocket:", e);
            setTimeout(initWebSocket, 3000); // Ponowna próba połączenia w przypadku błędu
        }
    }

    // Wywołaj funkcję inicjalizacji na początku
    initWebSocket();

    // Wysłanie wiadomości
    function sendMessage(text) {
        if(!text) return;
        showMessage({
            text: text, 
            user_id: userId
        });
        conn.send(JSON.stringify({
            type: 'message',
            text: text,
        }));
        msgText.value = '';
        chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
    }

    function sendStartedTyping() {
        conn.send(JSON.stringify({
            type: 'typing',
            status: 'start',
        }));
    }

    function sendEndedTyping() {
        conn.send(JSON.stringify({
            type: 'typing',
            status: 'end',
        }));
    }
    let typingTimeout;

    msgText.addEventListener('input', () => {
        clearTimeout(typingTimeout); // Jeśli użytkownik nadal pisze, resetuj timer
        sendStartedTyping(); // Powiadom o rozpoczęciu pisania

        // Ustaw timer na zakończenie pisania
        typingTimeout = setTimeout(() => {
            sendEndedTyping(); // Powiadom o zakończeniu pisania po 2 sekundach bezczynności
        }, 2000);
    });

    // Gdy użytkownik opuści pole
    msgText.addEventListener('blur', sendEndedTyping);

    function showOtherPersonTyping(status) {
        if(status == 'start') {
            typingNotification.classList.remove('is-hidden');
        } else if(status == 'end') {
            typingNotification.classList.add('is-hidden');
        }
    }

    function createMessageHtml(text, authorId) {
        const isMessageOwner = authorId == userId;
        const html = `<div class="is-flex ${isMessageOwner ? 'is-justify-content-flex-end' :        'is-justify-content-flex-start'} mb-4">
                <div class="message ${isMessageOwner ? '' : 'is-info'} ">
                    <div class="message-body ${isMessageOwner ? 'is-borderless' : ''}">
                        ${text}
                    </div>
                </div>
            </div>`;
        return html;
    }

    function showMessage(message) {
        const html = createMessageHtml(message.text, message.user_id);
        chatMessagesContainer.insertAdjacentHTML('beforeend', html);
    }

</script>
<?php $this->endSection(); ?>