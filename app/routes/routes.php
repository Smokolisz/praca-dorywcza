<?php

use App\Controllers\ChatController;
use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\RegisterController;
use App\Controllers\ProfileController;
use App\Controllers\JobController;
use App\Controllers\ListingController;
use App\Controllers\LogoutController;
use App\Controllers\VerifyEmailController;
use App\Controllers\NegotiationController;
use App\Controllers\ResetPasswordController;
use App\Controllers\StatuteController;
use App\Controllers\FaqController;
use App\Controllers\ReviewController;
use App\Controllers\SearchController;
use App\Controllers\CategoryController;
use Slim\App;

return function (App $app) {

    // strona główna
    $app->get('/', [HomeController::class, 'index']);

    // rejestracja
    $app->get('/zarejestruj-sie', [RegisterController::class, 'index']);
    $app->post('/zarejestruj-sie', [RegisterController::class, 'store']);

    $app->get('/potwierdz-email/{token}', [VerifyEmailController::class, 'index']);

    // logowanie
    $app->get('/zaloguj-sie', [LoginController::class, 'index']);
    $app->post('/zaloguj-sie', [LoginController::class, 'login']);

    $app->get('/resetuj-haslo', [ResetPasswordController::class, 'index']);
    $app->post('/resetuj-haslo/wyslij-email', [ResetPasswordController::class, 'sendPasswordResetEmail']);
    $app->get('/resetuj-haslo/{token}', [ResetPasswordController::class, 'edit']);
    $app->post('/resetuj-haslo/{token}', [ResetPasswordController::class, 'update']);

    //wylogowanie
    $app->get('/wyloguj-sie', [LogoutController::class, 'logout']);

    //profil użytkownika
    $app->get('/profil', [ProfileController::class, 'index']);

    //edytowanie profilu użytkownika
    $app->get('/profil/edytuj', [ProfileController::class, 'edit']);
    $app->post('/profil/edytuj', [ProfileController::class, 'updateProfile']);

    //zmiana hasła 
    $app->get('/profil/zmien-haslo', [ProfileController::class, 'updatePasswordForm']);
    $app->post('/profil/zmien-haslo', [ProfileController::class, 'updatePassword']);

    $app->get('/job/{id}', [JobController::class, 'index']);
    $app->post('/contracts/create', \App\Controllers\JobController::class . ':createContract');
    $app->post('/contracts/accept/{id}', \App\Controllers\JobController::class . ':acceptContract');
    $app->post('/contracts/reject/{id}', \App\Controllers\JobController::class . ':rejectContract');
    
    
    // dodawanie ogloszenia
    $app->get('/add-listing', [ListingController::class, 'showAddListingForm']);
    $app->post('/add-listing', [ListingController::class, 'submitListing']);

    //powiadomienia użytkownika
    $app->get('/profil/powiadomienia', [ProfileController::class, 'notifications']);

    //historia aktywności użytkownika
    $app->get('/profil/aktywnosc', [ProfileController::class,  'activityLog']);

    //przesyłanie zdjęcia profilowego
    $app->post('/profil/upload-profile-picture', [ProfileController::class,  'uploadProfilePicture']);
    // Opinie o zalogowanym użytkowniku
    $app->get('/profil/opinie', [ProfileController::class, 'userReviews']);

    //formularz rozpoczęcia negocjacji
    $app->get('/negocjacje/start/{id}', [NegotiationController::class, 'startNegotiationForm']);

    //wyświetlanie negocjacji
    $app->get('/negocjacje/{id}', [NegotiationController::class, 'showNegotiation']);

    //rozpoczęcie nowej negocjacji
    $app->post('/negocjacje/start', [NegotiationController::class, 'startNegotiation']);

    // Akceptacja oferty
    $app->post('/negocjacje/{id}/akceptacja', [NegotiationController::class, 'acceptOffer']);

    // Odrzucenie oferty
    $app->post('/negocjacje/{id}/odrzucenie', [NegotiationController::class, 'rejectOffer']);

    $app->get('/czat', [ChatController::class,  'index']);
    $app->get('/czat/utworz/{jobId}', [ChatController::class,  'create']);
    $app->get('/czat/{chatId}', [ChatController::class,  'show']);
    $app->get('/czat/historia/{chatId}', [ChatController::class,  'getMessages']);

    // Strona regulaminu
    $app->get('/regulamin', [StatuteController::class, 'show']);

    // Strona FAQ
    $app->get('/faq', [FaqController::class, 'show']);

    // Opinie
    $app->get('/opinie/dodaj/{negotiation_id}', [ReviewController::class, 'showAddReviewForm']);

    $app->get('/opinie', [ReviewController::class, 'showReviews']);

    $app->post('/opinie/dodaj', [ReviewController::class, 'submitReview']);

    $app->get('/szukaj', [SearchController::class,  'index']);

    $app->get('/kategoria', [CategoryController::class, 'index']);
    $app->get('/kategoria/{name}', [CategoryController::class, 'show']);

    $app->get('/kategoria/ulubione/dodaj/{id}', [CategoryController::class, 'addFavorite']);
    $app->get('/kategoria/ulubione/usun/{id}', [CategoryController::class, 'removeFavorite']);

};
