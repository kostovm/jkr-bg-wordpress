<?php
/**
 * Plugin Name: Daily Quiz
 * Description: Displays the quiz. Shortcode: [daily_quiz]
 * Version: 1.0
 * Author: Mihail Kostov
 */

defined('ABSPATH') || exit;

require_once plugin_dir_path(__FILE__) . 'includes/shortcode-daily-quiz.php';
require_once plugin_dir_path(__FILE__) . 'includes/enqueue.php';