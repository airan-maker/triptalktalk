<?php
/**
 * 댓글 템플릿
 *
 * @package Flavor_Trip
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
                esc_html(_n('댓글 %d개', '댓글 %d개', get_comments_number(), 'flavor-trip')),
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
        'title_reply'         => __('댓글 남기기', 'flavor-trip'),
        'label_submit'        => __('댓글 등록', 'flavor-trip'),
        'comment_notes_after' => '',
    ]);
    ?>
</section>
