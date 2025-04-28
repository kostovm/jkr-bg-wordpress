<?php

function display_today_quiz() {
    $today = date('Ymd'); 

    $args = array(
        'post_type' => 'pop_quiz',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => 'question_date',
                'value' => $today,
                'compare' => '='
            )
        )
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $question = get_field('question_text');
            $correct_answer = get_field('correct_answer');
            $correct_answer_value = get_field($correct_answer);
            $answer_quote = get_field('answer_quote');

            $answers = array(
                get_field('answer_1'),
                get_field('answer_2'),
                get_field('answer_3'),
                get_field('answer_4')
            );

            shuffle($answers);

            ob_start();
            ?>
            <div id="quiz-section" class="daily-quiz-section">
                <h3><?php echo esc_html($question); ?></h3>
                <form id="quiz-form">
                    <?php foreach ($answers as $index => $answer): ?>
                        <label>
                            <input type="radio" name="hp_answer" value="<?php echo esc_attr($answer); ?>">
                            <?php echo esc_html($answer); ?>
                        </label><br>
                    <?php endforeach; ?>
                    <button type="submit">Отговори на въпроса</button>
                </form>
                <div id="quiz-result" style="margin-top: 1em; font-weight: bold;"></div>
            </div>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const today = "<?php echo $today; ?>";
                    const correctAnswer = <?php echo json_encode($correct_answer_value); ?>;
                    const answerQuote = <?php echo json_encode($answer_quote); ?>;

                    const quizSection = document.getElementById("quiz-section");
                    const form = document.getElementById("quiz-form");
                    const resultDiv = document.getElementById("quiz-result");
                    const outerSection = document.querySelector(".daily-quiz-section");

                    if (localStorage.getItem("answered_quiz") === today) {
                        if (outerSection) {
                            outerSection.style.display = "none";
                        }
                        return;
                    }

                    form.addEventListener("submit", function (e) {
                        e.preventDefault();

                        const selected = document.querySelector('input[name="hp_answer"]:checked');
                        if (!selected) {
                            resultDiv.textContent = "Please select an answer.";
                            return;
                        }

                        const userAnswer = selected.value;
                        localStorage.setItem("answered_quiz", today);
                        form.style.display = "none";

                        if (userAnswer === correctAnswer) {
                            resultDiv.innerHTML = "✅ Правилно!<br><em>" + answerQuote + "</em>";
                        } else {
                            resultDiv.innerHTML = "❌ Грешка. Верният отговор е: <strong>" + correctAnswer + "</strong>.<br><em>" + answerQuote + "</em>";
                        }
                    });
                });
            </script>
            <?php
            wp_reset_postdata();
            return ob_get_clean();
        }
    } else {
        return '<p>За днес няма въпрос.</p>';
    }
}
add_shortcode('daily_quiz', 'display_today_quiz');