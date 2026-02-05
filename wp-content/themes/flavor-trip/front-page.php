<?php
/**
 * ?ˆíŽ˜?´ì? ?œí”Œë¦? *
 * @package TripTalk
 */

get_header();
?>

<?php get_template_part('template-parts/hero-section'); ?>

<section class="section section-itineraries">
    <div class="container">
        <h2 class="section-title"><?php esc_html_e('ì¶”ì²œ ?¬í–‰ ?¼ì •', 'flavor-trip'); ?></h2>
        <p class="section-subtitle"><?php esc_html_e('?„ì„ ???¬í–‰ ì½”ìŠ¤ë¥?ë§Œë‚˜ë³´ì„¸??, 'flavor-trip'); ?></p>

        <div class="posts-grid posts-grid--3">
            <?php
            $itineraries = new WP_Query([
                'post_type'      => 'travel_itinerary',
                'posts_per_page' => 6,
                'orderby'        => 'date',
                'order'          => 'DESC',
            ]);
            if ($itineraries->have_posts()) :
                while ($itineraries->have_posts()) : $itineraries->the_post();
                    get_template_part('template-parts/content', 'itinerary');
                endwhile;
                wp_reset_postdata();
            else : ?>
                <p class="no-content"><?php esc_html_e('?„ì§ ?±ë¡???¬í–‰ ?¼ì •???†ìŠµ?ˆë‹¤.', 'flavor-trip'); ?></p>
            <?php endif; ?>
        </div>

        <div class="section-cta">
            <a href="<?php echo esc_url(get_post_type_archive_link('travel_itinerary')); ?>" class="btn btn-outline">
                <?php esc_html_e('ëª¨ë“  ?¼ì • ë³´ê¸° ??, 'flavor-trip'); ?>
            </a>
        </div>
    </div>
</section>

<?php get_template_part('template-parts/destination-grid'); ?>

<section class="section section-blog">
    <div class="container">
        <h2 class="section-title"><?php esc_html_e('?¬í–‰ ?´ì•¼ê¸?, 'flavor-trip'); ?></h2>
        <p class="section-subtitle"><?php esc_html_e('?ìƒ???¬í–‰ ?„ê¸°?€ ?ì„ ê³µìœ ?©ë‹ˆ??, 'flavor-trip'); ?></p>

        <div class="posts-grid posts-grid--3">
            <?php
            $blog = new WP_Query([
                'post_type'      => 'post',
                'posts_per_page' => 3,
            ]);
            if ($blog->have_posts()) :
                while ($blog->have_posts()) : $blog->the_post();
                    get_template_part('template-parts/content');
                endwhile;
                wp_reset_postdata();
            else : ?>
                <p class="no-content"><?php esc_html_e('?„ì§ ?±ë¡??ê¸€???†ìŠµ?ˆë‹¤.', 'flavor-trip'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
get_footer();
