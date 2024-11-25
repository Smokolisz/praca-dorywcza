<nav class="navbar" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">

        <figure class="image is-64x64">
            <img src="/pictures/logo_swiftjobs.png" />
        </figure>
        <a class="navbar-item" href="/">
            <h1 class="title" ,>SwiftJobs</h1>
        </a>


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

            <a class="navbar-item">
                Kategorie
            </a>
            <a class="navbar-item" href="/faq">
                FAQ
            </a>
            <a class="navbar-item" href="/profil">
                Mój profil
            </a>
            <a class="navbar-item" href="/kontakt">
                Kontakt
            </a>




            <div class="navbar-item">
                <a class="button is-primary" href="/add-listing">Dodaj Ogłoszenie</a>
            </div>



        </div>

        <div class="navbar-end">
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