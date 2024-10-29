<?php

use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\RegisterController;
use App\Controllers\ProfileController;
use App\Controllers\JobController;
use App\Controllers\ListingController;
use Slim\App;

return function (App $app) {

    // strona główna
    $app->get('/', [HomeController::class, 'index']);

    // rejestracja
    $app->get('/zarejestruj-sie', [RegisterController::class, 'index']);
    $app->post('/zarejestruj-sie', [RegisterController::class, 'store']);

    // logowanie
    $app->get('/zaloguj-sie', [LoginController::class, 'index']);
    $app->post('/zaloguj-sie', [LoginController::class, 'login']);

    //wylogowanie
    $app->get('/wyloguj-sie', [LoginController::class, 'logout']);

    //profil użytkownika
    $app->get('/profil', [ProfileController::class, 'index']);

    //edytowanie profilu użytkownika
    $app->get('/profil/edytuj', [ProfileController::class, 'edit']);
    $app->post('/profil/edytuj', [ProfileController::class, 'updateProfile']);

    //zmiana hasła 
    $app->get('/profil/zmien-haslo', [ProfileController::class, 'updatePasswordForm']);
    $app->post('/profil/zmien-haslo', [ProfileController::class, 'updatePassword']);

    $app->get('/job/{id}', [JobController::class, 'index']);

    // dodawanie ogloszenia
    $app->get('/add-listing', [ListingController::class, 'showAddListingForm']);
    $app->post('/add-listing', [ListingController::class, 'submitListing']);

    //powiadomienia użytkownika
    $app->get('/profil/powiadomienia', [ProfileController::class, 'notifications']);

    //historia aktywności użytkownika
    $app->get('/profil/aktywnosc', [ProfileController::class,  'activityLog']);

    //przesyłanie zdjęcia profilowego
    $app->post('/profil/upload-profile-picture', [ProfileController::class,  'uploadProfilePicture']);
};
