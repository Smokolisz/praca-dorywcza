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
</style>

<div class="columns is-gapless m-0" style="height:calc(100vh - 104px);">
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
        <div class="panel is-radiusless is-shadowless">
            <p class="panel-heading is-radiusless" style="background-color: transparent;border-bottom:1px solid rgb(46, 51, 61);">Koszenie trawnika</p>
        </div>
        <div class="p-5" style="height:calc(100% - 180px);overflow-y: auto;">

            <div class="is-flex is-justify-content-flex-end mb-4">
                <div class="message">
                    <div class="message-body is-borderless">
                        Hej
                    </div>
                </div>
            </div>
            <div class="is-flex is-justify-content-flex-end mb-4">
                <div class="message">
                    <div class="message-body is-borderless">
                        Co tam
                    </div>
                </div>
            </div>
            <div class="is-flex is-justify-content-flex-start mb-4">
                <div class="message is-info">
                    <div class="message-body ">
                        Cześć
                    </div>
                </div>
            </div>
            <div class="is-flex is-justify-content-flex-start mb-4">
                <div class="message is-info">
                    <div class="message-body ">
                        Cześć
                    </div>
                </div>
            </div>
            <div class="is-flex is-justify-content-flex-start mb-4">
                <div class="message is-info">
                    <div class="message-body ">
                        Cześć
                    </div>
                </div>
            </div>
            <div class="is-flex is-justify-content-flex-start mb-4">
                <div class="message is-info">
                    <div class="message-body ">
                        Cześć
                    </div>
                </div>
            </div>
            <div class="is-flex is-justify-content-flex-start mb-4">
                <div class="message is-info">
                    <div class="message-body ">
                        Cześć
                    </div>
                </div>
            </div>
            <div class="is-flex is-justify-content-flex-start mb-4">
                <div class="message is-info">
                    <div class="message-body ">
                        Cześć
                    </div>
                </div>
            </div>
        </div>
        <div class="field has-addons p-5" style="border-top:1px solid rgb(46, 51, 61);">
            <div class="control is-expanded">
                <input class="input" type="text" placeholder="Wiadomość">
            </div>
            <div class="control">
                <button class="button is-info">
                    Wyślij
                </button>
            </div>
        </div>
    </div>
    <div class="column">
        <div class="p-5">
            <strong class="title is-4">O zleceniodawcy:</strong>
        </div>
    </div>
</div>

<?php $this->startSection('scripts'); ?>
<!-- <script src="path/to/script.js"></script> -->
<?php $this->endSection(); ?>