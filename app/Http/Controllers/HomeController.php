<?php

namespace App\Http\Controllers;

use App\Services\AnikotoService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        private readonly AnikotoService $anikoto
    ) {}

    public function index(Request $request)
    {
        $page = (int) $request->get('page', 1);
        $perPage = 20;

        $response = $this->anikoto->recentAnime($page, $perPage);

        // Validate response before rendering
        if (!$response || !isset($response['data'])) {
            \Log::error('Anikoto API recent-anime request failed or returned invalid JSON');
            $anime = [];
            $pagination = [
                'page' => 1,
                'per_page' => $perPage,
                'total' => 0,
                'total_pages' => 1
            ];
        } else {
            $anime = $response['data'];
            $pagination = $response['pagination'] ?? [
                'page' => $page,
                'per_page' => $perPage,
                'total' => count($anime),
                'total_pages' => 1
            ];
        }

        return view('anikoto.home', [
            'anime' => $anime,
            'pagination' => $pagination,
            'currentPage' => $page,
            'metaTitle' => 'Home - AniVerse Anime Streaming Catalog',
            'metaDescription' => 'Browse the latest anime series and releases on AniVerse. Free online streaming database.'
        ]);
    }
}
