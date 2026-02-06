<?php
/**
 * 여행 일정 상세 페이지
 *
 * @package Flavor_Trip
 */

get_header();

while (have_posts()) : the_post();
    $days       = get_post_meta(get_the_ID(), '_ft_days', true) ?: [];
    $gallery    = get_post_meta(get_the_ID(), '_ft_gallery', true) ?: [];
    $lat        = get_post_meta(get_the_ID(), '_ft_map_lat', true);
    $lng        = get_post_meta(get_the_ID(), '_ft_map_lng', true);
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('itinerary-single'); ?>>
    <div class="container">
        <?php get_template_part('template-parts/breadcrumbs'); ?>
    </div>

    <div class="container itinerary-layout">
        <div class="itinerary-content">
            <header class="itinerary-header">
                <?php
                $destinations = get_the_terms(get_the_ID(), 'destination');
                if ($destinations && !is_wp_error($destinations)) : ?>
                    <div class="itinerary-destinations">
                        <?php foreach ($destinations as $dest) : ?>
                            <a href="<?php echo esc_url(get_term_link($dest)); ?>" class="tag tag-destination"><?php echo esc_html($dest->name); ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <h1 class="itinerary-title"><?php the_title(); ?></h1>

                <div class="itinerary-meta-bar">
                    <?php
                    $duration = get_post_meta(get_the_ID(), '_ft_duration', true);
                    $difficulty = get_post_meta(get_the_ID(), '_ft_difficulty', true);
                    $price = get_post_meta(get_the_ID(), '_ft_price_range', true);
                    ?>
                    <?php if ($duration) : ?>
                        <span class="meta-item"><span class="meta-icon">📅</span> <?php echo esc_html($duration); ?></span>
                    <?php endif; ?>
                    <?php if ($difficulty) : ?>
                        <span class="meta-item"><span class="meta-icon">⭐</span> <?php echo esc_html(ft_get_difficulty_label($difficulty)); ?></span>
                    <?php endif; ?>
                    <?php if ($price) : ?>
                        <span class="meta-item"><span class="meta-icon">💰</span> <?php echo esc_html(ft_get_price_label($price)); ?></span>
                    <?php endif; ?>
                    <span class="meta-item"><span class="meta-icon">📝</span> <?php echo esc_html(get_the_date()); ?></span>
                </div>
            </header>

            <?php if (has_post_thumbnail()) : ?>
                <div class="itinerary-featured-image">
                    <?php the_post_thumbnail('ft-hero', ['loading' => 'eager']); ?>
                </div>
            <?php endif; ?>

            <div class="itinerary-description entry-content">
                <?php the_content(); ?>
            </div>

            <?php if (!empty($days)) :
                // spots 구조 여부 확인
                $has_spots = false;
                foreach ($days as $d) {
                    if (!empty($d['spots'])) { $has_spots = true; break; }
                }
            ?>
                <section class="itinerary-days" id="daily-itinerary">
                    <h2 class="section-heading"><?php esc_html_e('일자별 일정', 'flavor-trip'); ?></h2>
                    <?php if ($has_spots) : ?>
                        <div class="days-spots-container">
                            <?php
                            set_query_var('ft_spot_counter', 0);
                            foreach ($days as $i => $day) :
                                set_query_var('ft_day_data', $day);
                                set_query_var('ft_day_number', $i + 1);
                                get_template_part('template-parts/itinerary-day');
                            endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="timeline">
                            <?php foreach ($days as $i => $day) :
                                set_query_var('ft_day_data', $day);
                                set_query_var('ft_day_number', $i + 1);
                                get_template_part('template-parts/itinerary-day');
                            endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <?php if (!empty($gallery)) :
                set_query_var('ft_gallery_ids', $gallery);
                get_template_part('template-parts/photo-gallery');
            endif; ?>

            <?php
            // spots 좌표 수집
            $all_spots = [];
            if (!empty($days)) {
                foreach ($days as $di => $d) {
                    if (!empty($d['spots']) && is_array($d['spots'])) {
                        $sn = 0;
                        foreach ($d['spots'] as $s) {
                            $sn++;
                            if (!empty($s['lat']) && !empty($s['lng'])) {
                                $all_spots[] = [
                                    'day'  => $di + 1,
                                    'n'    => $sn,
                                    'name' => $s['name'] ?? '',
                                    'lat'  => (float) $s['lat'],
                                    'lng'  => (float) $s['lng'],
                                ];
                            }
                        }
                    }
                }
            }

            // spots 좌표가 있으면 지도 표시, 없으면 기존 단일 좌표 사용
            if (!empty($all_spots) || ($lat && $lng)) :
                set_query_var('ft_map_lat', $lat);
                set_query_var('ft_map_lng', $lng);
                set_query_var('ft_map_zoom', get_post_meta(get_the_ID(), '_ft_map_zoom', true) ?: 12);
                set_query_var('ft_map_spots', $all_spots);
                get_template_part('template-parts/map-placeholder');
            endif; ?>

            <?php
            $styles = get_the_terms(get_the_ID(), 'travel_style');
            if ($styles && !is_wp_error($styles)) : ?>
                <div class="itinerary-styles">
                    <h3><?php esc_html_e('여행 스타일', 'flavor-trip'); ?></h3>
                    <div class="tags">
                        <?php foreach ($styles as $style) : ?>
                            <a href="<?php echo esc_url(get_term_link($style)); ?>" class="tag tag-style"><?php echo esc_html($style->name); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <nav class="post-navigation">
                <?php
                previous_post_link('<div class="nav-prev">%link</div>', '← %title');
                next_post_link('<div class="nav-next">%link</div>', '%title →');
                ?>
            </nav>

            <?php if (comments_open() || get_comments_number()) :
                comments_template();
            endif; ?>
        </div>

        <aside class="itinerary-sidebar">
            <?php get_template_part('template-parts/itinerary-sidebar'); ?>
        </aside>
    </div>
</article>

<?php
endwhile;
get_footer();
