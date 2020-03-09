<?php
namespace Undone\InstagramScraper\services;

use Exception;

class InstagramScraperService
{
    private $listURL = 'https://www.instagram.com/explore/tags/';
    private $medias = [];

    public function scrapeTag($tag = '', $limit = 10) {
        $url = sprintf($this->listURL . $tag);
        $content = file_get_contents($url);
        $content = explode("window._sharedData = ", $content)[1];
        $content = explode(";</script>", $content)[0];
        $data    = json_decode($content, true);
        $media = $data['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media'] ?? [];

        if ($data && $media) {
            $edges = array_splice($media['edges'], 0, $limit);
            foreach($edges as $post) {
                array_push($this->medias, $this->scrapePostData($post));
            }
        } else {
            throw new Exception('Error scraping tag page');
        }

        return $this->medias;
    }

    public function scrape($html) {
        try {
            $dataString = $html->match($this->dataExp)[1];
            return json_encode($dataString);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function scrapePostData($post)  {
        $scrapedData = [
            "media_id" => $post['node']['id'],
            "shortcode" => $post['node']['shortcode'],
            "text" => $post['node']['edge_media_to_caption']['edges'][0]['node.text'] ?? '',
            "comment_count" => $post['node']['edge_media_to_comment']['count'],
            "like_count" => $post['node']['edge_liked_by']['count'],
            "display_url" => $post['node']['display_url'],
            "owner_id" => $post['node']['owner']['id'],
            "date" => $post['node']['taken_at_timestamp'],
            "thumbnail" => $post['node']['thumbnail_src'],
            "thumbnail_resource" => $post['node']['thumbnail_resources'],
            "is_video" => $post['node']['is_video']
        ];

        if ($post['node']['is_video']) {
            $scrapedData['video_view_count'] = $post['node']['video_view_count'];
        }

        return $scrapedData;
    }
}
