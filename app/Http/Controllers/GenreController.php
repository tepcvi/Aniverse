<?php

namespace App\Http\Controllers;

use App\Services\AniListService;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function __construct(
        private readonly AniListService $anilist
    ) {}

    public function index()
    {
        $data = $this->anilist->getGenres();

        return view('genres.index', [
            'genres' => $data['GenreCollection'] ?? [],
        ]);
    }

    public function show(Request $request, string $genre)
    {
        $page = (int) $request->get('page', 1);
        $data = $this->anilist->search([
            'genre' => $genre,
            'page' => $page,
            'perPage' => 24,
            'sort' => 'popularity',
        ]);

        return view('genres.show', [
            'genre' => $genre,
            'anime' => $data['Page']['media'] ?? [],
            'pageInfo' => $data['Page']['pageInfo'] ?? [],
            'currentPage' => $page,
        ]);
    }
}
