<?php

// This will scan all Scripts Organizer Gutenberg Add-on posts and extract classes to be compiled.  

use WP_Query;

class SCORG_ACF_Gutenberg_Block_Worker
{
    public function __construct()
    {
        add_filter('f!winden/core/worker:compile_content_payload', [$this, 'compile_content_payload'], 10);
    }

    /**
     * Compose the content payload to generate the css cache.
     * 
     * @param string $content The content payload
     * @return string 
     */
    public function compile_content_payload($content)
    {
        $posts = [];

        $meta_key = 'SCORG_GACF_php_script';

        $query = new WP_Query([
            'posts_per_page' => -1,
            'fields' => 'ids',
            'post_type' => ['scorg_ga'],
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => $meta_key,
                ],
            ],
        ]);

        foreach ($query->posts as $post_id) {
            $posts[] = $post_id;
        }

        foreach ($posts as $post_id) {
            $meta_value = get_post_meta($post_id, $meta_key, true);
            if ($meta_value) {
                $content .= base64_decode($meta_value);
            }
        }

        return $content;
    }
}

new SCORG_ACF_Gutenberg_Block_Worker();
