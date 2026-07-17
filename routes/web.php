<?php

use App\Http\Controllers\AnimeController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AnikotoController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PlayerController;
use App\Http\Controllers\EpisodeController;

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Anime detail (AniList)
Route::get('/anime/{id}', [AnimeController::class, 'show'])->name('anime.show')->where('id', '[0-9]+');

// Browse pages
Route::get('/trending', [AnimeController::class, 'trending'])->name('anime.trending');
Route::get('/popular', [AnimeController::class, 'popular'])->name('anime.popular');
Route::get('/top-rated', [AnimeController::class, 'topRated'])->name('anime.top-rated');
Route::get('/seasonal', [AnimeController::class, 'seasonal'])->name('anime.seasonal');
Route::get('/latest', [AnimeController::class, 'latest'])->name('anime.latest');

// Search (AniList)
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/api/autocomplete', [SearchController::class, 'autocomplete'])->name('api.autocomplete');

// Genres
Route::get('/genres', [GenreController::class, 'index'])->name('genres.index');
Route::get('/genres/{genre}', [GenreController::class, 'show'])->name('genres.show');

use App\Http\Controllers\WatchController;

// Static pages
Route::get('/about', [PageController::class, 'about'])->name('about');

// Streaming Player & Episode List Routes
Route::get('/watch/{id}/{episode}', [WatchController::class, 'watch'])->name('watch');

// ──────────────────────────────────────────────
// Anikoto API Integration (Legacy/Scraper endpoints)
// ──────────────────────────────────────────────
Route::prefix('anikoto')->name('anikoto.')->group(function () {
    Route::get('/search', [AnikotoController::class, 'search'])->name('search');
    Route::get('/anime/{slug}', [AnikotoController::class, 'show'])->name('show');
    Route::get('/episodes/{id}', [AnikotoController::class, 'episodes'])->name('episodes');
    Route::get('/watch/{slug}/{episode}', function () {
        return redirect()->route('home');
    })->name('watch');
    Route::get('/schedule', [AnikotoController::class, 'schedule'])->name('schedule');
    Route::get('/health', [AnikotoController::class, 'health'])->name('health');
});

