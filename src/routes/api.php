<?php

Route::group(['prefix' => 'api/instagram-scraper', 'middleware' => ['Cors'], 'namespace' => 'Undone\InstagramScraper\controllers'], function() {
    Route::get('tag/{tag}', 'InstagramScraperController@getPostsByTag');
});