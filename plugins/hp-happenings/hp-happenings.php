<?php
/**
 * Plugin Name: HP Happenings
 * Description: Displays a list of upcoming happenings with icons. Shortcode: [hp_happening_list]
 * Version: 1.0
 * Author: Mihail Kostov
 */

if (!defined('ABSPATH')) {
    exit;
}

function hp_happenings_enqueue_assets() {
    wp_enqueue_style(
        'hp-happenings-style',
        plugin_dir_url(__FILE__) . 'assets/hp-happenings.css',
        [],
        '1.0'
    );
}
add_action('wp_enqueue_scripts', 'hp_happenings_enqueue_assets');

function hp_happening_list()
{
    $today = new DateTime();
    $today->setTime(0, 0);

    $args = array(
        'post_type' => 'happening',
        'posts_per_page' => -1,
        'meta_key' => 'happening_date',
        'orderby' => 'meta_value',
        'order' => 'ASC',
    );

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        return '<p>Не знаем за предстоящи събития.</p>';
    }

    ob_start();
    echo '<div class="event-list">';

    while ($query->have_posts()) {
        $query->the_post();

        $title = get_field('happening_title');
        $city = get_field('happening_city');
        $place = get_field('happening_place');
        $date_string = get_field('happening_date');

        $date_object = DateTime::createFromFormat('d.m.Y', $date_string);
        if (!$date_object) {
            continue;
        }
        $date_object->setTime(0, 0);

        if ($date_object < $today) {
            continue;
        }

        $formatted_date_for_sorting = $date_object->format('Y-m-d');
        $formatted_date_display = $date_object->format('d.m');

        $events[] = array(
            'formatted_date' => $formatted_date_for_sorting,
            'formatted_date_display' => $formatted_date_display,
            'title' => $title,
            'city' => $city,
            'place' => $place,
            'time' => get_field('happening_time'),
            'permalink' => get_permalink(),
            'icons' => get_event_icons(),
        );
    }

    usort($events, function ($a, $b) {
        return strcmp($a['formatted_date'], $b['formatted_date']);
    });

    foreach ($events as $event) {
        echo "<div class='event'>
                <div class='event-header'>
                    <span class='event-title'>{$event['title']}</span>
                    <span class='event-icons'>{$event['icons']}</span>
                </div>
                <div class='event-info'>
                    <span class='event-location'><strong>{$event['city']}</strong> • {$event['place']}</span>
                    <span class='event-date'>{$event['formatted_date_display']}, {$event['time']}</span>
                </div>
                <a href='{$event['permalink']}' class='event-button'>Още</a>
              </div>";
    }

    echo '</div>';
    wp_reset_postdata();
    return ob_get_clean();
}

function get_event_icons()
{
    $icons = '';
    if (get_field('quiz')) {
        $icons .= '<i class="fa-solid fa-brain"></i> ';
    }
    if (get_field('for_children')) {
        $icons .= '<i class="fa-solid fa-children"></i> ';
    }
    if (get_field('price')) {
        $icons .= '<i class="fa-solid fa-circle-dollar-to-slot"></i> ';
    }
    if (get_field('reading')) {
        $icons .= '<i class="fa-solid fa-book-open-reader"></i> ';
    }
    return $icons;
}

add_shortcode('hp_happening_list', 'hp_happening_list');