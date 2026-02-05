<?php
/**
 * ?“ê? ?œí”Œë¦? *
 * @package TripTalk
 */

defined('ABSPATH') || exit;

if (post_password_required()) {
    return;
}
?>

<section id="comments" class="comments-area">
    <?php if (have_comments()) : ?>
        <h2 class="comments-title">
            <?php printf(
                esc_html(_n('?“ê? %dê°?, '?“ê? %dê°?, get_comments_number(), 'flavor-trip')),
                get_comments_number()
            ); ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments([
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 48,
            ]);
            ?>
        </ol>

        <?php the_comments_navigation(); ?>
    <?php endif; ?>

    <?php
    comment_form([
        'title_reply'         => __('?“ê? ?¨ê¸°ê¸?, 'flavor-trip'),
        'label_submit'        => __('?“ê? ?±ë¡', 'flavor-trip'),
        'comment_notes_after' => '',
    ]);
    ?>
</section>
