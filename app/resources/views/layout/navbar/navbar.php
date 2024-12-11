<nav class="navbar p-3" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">

        <figure class="image is-64x64">
            <img src="/pictures/logo_swiftjobs.png" />
        </figure>

        <div class="navbar-item">
            <h1 class="title">
                <a href="/" class="has-text-white">SwiftJobs</a>
            </h1>
        </div>

        <button class="button is-ghost">
            <figure class="image is-32x32">
                <img src="/pictures/daynight.png" />
            </figure>
        </button>

        <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>
    </div>

    <div class="navbar-menu">
        <div class="navbar-start">

            <a class="navbar-item" href="/kategoria">
                Kategorie
            </a>
            <a class="navbar-item" href="/faq">
                FAQ
            </a>
            <a class="navbar-item" href="/kontakt">
                Kontakt
            </a>

            <div class="navbar-item">
                <a class="button is-primary" href="/add-listing">Dodaj Ogłoszenie</a>
            </div>

            <?php
            if (strpos($_SERVER['REQUEST_URI'], '/szukaj') === false):
            ?>
            <div class="navbar-item">
                <form method="GET" action="/szukaj">
                    <div class="field has-addons">
                        <div class="control">
                            <input class="input" type="text" name="q" placeholder="">
                        </div>
                        <div class="control">
                            <button class="button is-info">Szukaj</button>
                        </div>
                    </div>
                </form>
            </div>
            <?php
            endif;
            ?>

        </div>

        <div class="navbar-end">

            <a class="navbar-item" href="/czat" title="Czaty">
                <i class="fa-regular fa-comments"></i>
            </a>

            <div class="navbar-item">
                <?php
                if (isset($_SESSION['user_id'])):
                ?>
                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link" href="/profil">
                            Witaj, <?= $_SESSION['first_name'] ?>
                        </a>

                        <div class="navbar-dropdown" style="margin-left:-25px;">
                            <a class="navbar-item">
                                Moje ogłoszenia
                            </a>
                            <a class="navbar-item">
                                Moje prace
                            </a>
                            <a class="navbar-item">
                                Wiadomości
                            </a>
                            <hr class="navbar-divider">
                            <div class="navbar-item">
                                <a class="button is-danger is-dark is-small is-fullwidth" href="/wyloguj-sie">
                                    Wyloguj się
                                </a>
                            </div>
                        </div>
                    </div>
                <?php
                else:
                ?>
                    <div class="buttons">
                        <a class="button is-primary" href="/zarejestruj-sie">
                            <strong>Zarejestruj się</strong>
                        </a>
                        <a class="button is-light" href="/zaloguj-sie">
                            Zaloguj się
                        </a>
                    </div>
                <?php
                endif;
                ?>
            </div>
        </div>
    </div>
</nav>