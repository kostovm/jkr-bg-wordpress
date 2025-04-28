<?php

function daily_quiz_enqueue_assets()
{
    if (!is_singular() || !has_shortcode(get_post()->post_content, 'daily_quiz')) {
        return;
    }

    $plugin_url = plugin_dir_url(__DIR__);

    wp_enqueue_style(
        'daily-quiz-style',
        $plugin_url . 'assets/daily-quiz-style.css'
    );
}
add_action('wp_enqueue_scripts', 'daily_quiz_enqueue_assets');