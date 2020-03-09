<?php
namespace Undone\InstagramScraper\controllers;

use App\Http\Controllers\Controller;
use Undone\InstagramScraper\services\InstagramScraperService;


class InstagramScraperController extends Controller {
    private $scraper;

    public function __construct(InstagramScraperService $scraper)
    {
        $this->scraper = $scraper;
    }
    
    public function getPostsByTag($tag = '') {
        $posts = $this->scraper->scrapeTag($tag, 10);
        return response()->json([
            "data" => $posts
        ]);
    }
}