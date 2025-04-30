<?php
/**
 * Plugin Name: HP Events or Books
 * Description: Displays either today's events or upcoming books. Shortcode: [hp_events_or_books]
 * Version: 1.0
 * Author: Mihail Kostov
 */

 function hp_events_or_books()
{
    $today = date('d/m');

    // Query for events
    $events_query = new WP_Query([
        'post_type' => 'event',
        'posts_per_page' => -1,
        'meta_query' => [
            [
                'key' => 'event_date',
                'value' => $today,
                'compare' => '=' // Only get events where event_date matches $today
            ]
        ]
    ]);

    if ($events_query->have_posts()) {
        ob_start();
        echo '<div class="hp-component"><h3>На днешната дата</h3><div class="hp-scrollable">';

        while ($events_query->have_posts()) {
            $events_query->the_post();

            $event_description = get_field('event_description');
            $event_link = get_field('event_link');

            echo '<div class="event"><p>';
            if ($event_link) {
                echo '<a href="' . esc_url($event_link) . '">' . esc_html($event_description) . '</a>';
            } else {
                echo esc_html($event_description);
            }
            echo '</p></div>';
        }

        echo '</div></div>';
        wp_reset_postdata();
        return ob_get_clean();
    }

    // Query for books
    $books_query = new WP_Query([
        'post_type' => 'book',
        'posts_per_page' => -1,
        'meta_key' => 'book_date',
        'orderby' => 'meta_value',
        'order' => 'ASC',
    ]);

    if ($books_query->have_posts()) {
        $books = [];

        while ($books_query->have_posts()) {
            $books_query->the_post();

            $cover = get_field('book_cover');
            $pub_date = get_field('book_date');
            $date_parts = explode('/', $pub_date);

            if (count($date_parts) === 3) {
                $pub_date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
            }

            $days_remaining = (strtotime($pub_date) - time()) / 86400;
            $is_new = get_field('book_type');

            $books[] = [
                'cover' => $cover,
                'title' => get_the_title(),
                'pub_date' => $pub_date,
                'days_remaining' => ceil($days_remaining),
                'is_new' => $is_new,
            ];
        }

        usort($books, function ($a, $b) {
            return $b['is_new'] <=> $a['is_new']; // true (1) comes before false (0)
        });

        ob_start();
        echo '<div class="hp-component"><h3>Предстоящи книги</h3><div class="hp-books">';

        foreach ($books as $book) {
            echo '<div class="hp-book ' . ($book['is_new'] ? 'new_book' : 'new_edition') . '">
                    <img src="' . esc_url($book['cover']) . '" alt="' . esc_attr($book['title']) . '">
                    <div class="hp-book-info">
                        <p>' . esc_html($book['title']) . '</p>
                        <p>' . date('d.m.Y', strtotime($book['pub_date'])) . '</p>
                        <p>Излиза след ' . $book['days_remaining'] . ' дни</p>
                    </div>
                  </div>';
        }

        echo '</div></div>';
        wp_reset_postdata();
        return ob_get_clean();
    }
    return '';
}
add_shortcode('hp_events_or_books', 'hp_events_or_books');

function hp_events_or_books_enqueue_styles()
{
    wp_enqueue_style(
        'hp-events-or-books-style',
        plugin_dir_url(__FILE__) . 'css/style.css',
        [],
        '1.0'
    );
}
add_action('wp_enqueue_scripts', 'hp_events_or_books_enqueue_styles');