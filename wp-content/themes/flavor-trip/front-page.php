<?php
/**
 * 홈페이지 템플릿
 *
 * @package Flavor_Trip
 */

get_header();
?>

<?php get_template_part('template-parts/hero-section'); ?>

<section class="section section-itineraries">
    <div class="container">
        <h2 class="section-title"><?php esc_html_e('추천 여행 일정', 'flavor-trip'); ?></h2>
        <p class="section-subtitle"><?php esc_html_e('엄선된 여행 코스를 만나보세요', 'flavor-trip'); ?></p>

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
                <p class="no-content"><?php esc_html_e('아직 등록된 여행 일정이 없습니다.', 'flavor-trip'); ?></p>
            <?php endif; ?>
        </div>

        <div class="section-cta">
            <a href="<?php echo esc_url(get_post_type_archive_link('travel_itinerary')); ?>" class="btn btn-outline">
                <?php esc_html_e('모든 일정 보기 →', 'flavor-trip'); ?>
            </a>
        </div>
    </div>
</section>

<?php get_template_part('template-parts/destination-grid'); ?>

<?php get_template_part('template-parts/guide-front-section'); ?>

<section class="section section-blog">
    <div class="container">
        <h2 class="section-title"><?php esc_html_e('여행 이야기', 'flavor-trip'); ?></h2>
        <p class="section-subtitle"><?php esc_html_e('생생한 여행 후기와 팁을 공유합니다', 'flavor-trip'); ?></p>

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
                <p class="no-content"><?php esc_html_e('아직 등록된 글이 없습니다.', 'flavor-trip'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
get_footer();
