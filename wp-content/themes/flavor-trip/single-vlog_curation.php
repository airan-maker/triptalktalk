<?php
/**
 * 브이로그 큐레이션 상세 페이지
 *
 * @package Flavor_Trip
 */

get_header();

while (have_posts()) : the_post();
    $youtube_id   = get_post_meta(get_the_ID(), '_ft_vlog_youtube_id', true);
    $channel_name = get_post_meta(get_the_ID(), '_ft_vlog_channel_name', true);
    $channel_url  = get_post_meta(get_the_ID(), '_ft_vlog_channel_url', true);
    $duration     = get_post_meta(get_the_ID(), '_ft_vlog_duration', true);
    $timeline     = get_post_meta(get_the_ID(), '_ft_vlog_timeline', true) ?: [];
    $spots        = get_post_meta(get_the_ID(), '_ft_vlog_spots', true) ?: [];
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('vlog-single'); ?>>
    <div class="container">
        <?php get_template_part('template-parts/breadcrumbs'); ?>
    </div>

    <div class="container">
        <!-- 헤더 -->
        <header class="vlog-header">
            <?php
            $destinations = get_the_terms(get_the_ID(), 'destination');
            if ($destinations && !is_wp_error($destinations)) : ?>
                <div class="vlog-tags">
                    <?php foreach ($destinations as $dest) : ?>
                        <a href="<?php echo esc_url(get_term_link($dest)); ?>" class="tag tag-sm"><?php echo esc_html($dest->name); ?></a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <h1 class="vlog-title"><?php the_title(); ?></h1>

            <?php if ($channel_name) : ?>
                <div class="vlog-channel">
                    <span class="vlog-channel-icon"><?php echo esc_html(mb_substr($channel_name, 0, 1)); ?></span>
                    <?php if ($channel_url) : ?>
                        <a href="<?php echo esc_url($channel_url); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo esc_html($channel_name); ?>
                        </a>
                    <?php else : ?>
                        <span><?php echo esc_html($channel_name); ?></span>
                    <?php endif; ?>
                    <?php if ($duration) : ?>
                        <span>&middot; <?php echo esc_html($duration); ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </header>

        <!-- 유튜브 영상 (Lazy Embed) -->
        <?php if ($youtube_id) : ?>
            <div class="vlog-player" data-youtube-id="<?php echo esc_attr($youtube_id); ?>">
                <img src="https://img.youtube.com/vi/<?php echo esc_attr($youtube_id); ?>/maxresdefault.jpg"
                     alt="<?php the_title_attribute(); ?>" loading="eager">
                <button class="vlog-play-btn" aria-label="<?php esc_attr_e('영상 재생', 'flavor-trip'); ?>">&#9654;</button>
            </div>
        <?php endif; ?>

        <!-- 타임라인 요약 -->
        <?php if (!empty($timeline)) : ?>
            <div class="vlog-timeline">
                <h2><?php esc_html_e('타임라인', 'flavor-trip'); ?></h2>
                <ul class="vlog-timeline-list">
                    <?php foreach ($timeline as $item) : ?>
                        <li class="vlog-timeline-item">
                            <span class="vlog-timestamp" data-time="<?php echo esc_attr($item['time']); ?>">
                                <?php echo esc_html($item['time']); ?>
                            </span>
                            <div class="vlog-timeline-content">
                                <div class="vlog-timeline-title"><?php echo esc_html($item['title']); ?></div>
                                <?php if (!empty($item['description'])) : ?>
                                    <div class="vlog-timeline-desc"><?php echo esc_html($item['description']); ?></div>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- 본문 (에디터 콘텐츠) -->
        <?php if (get_the_content()) : ?>
            <div class="vlog-content">
                <?php the_content(); ?>
            </div>
        <?php endif; ?>

        <!-- 영상 속 장소 지도 -->
        <?php
        $has_coords = false;
        foreach ($spots as $s) {
            if (!empty($s['lat']) && !empty($s['lng'])) {
                $has_coords = true;
                break;
            }
        }
        if ($has_coords) : ?>
            <div class="vlog-spots-section">
                <h2><?php esc_html_e('영상 속 장소', 'flavor-trip'); ?></h2>
                <div id="ft-vlog-spots-map" class="vlog-spots-map"></div>
            </div>
        <?php endif; ?>

        <!-- 여행 스타일 태그 -->
        <?php
        $travel_styles = get_the_terms(get_the_ID(), 'travel_style');
        if ($travel_styles && !is_wp_error($travel_styles)) : ?>
            <div class="vlog-tags">
                <?php foreach ($travel_styles as $style) : ?>
                    <a href="<?php echo esc_url(get_term_link($style)); ?>" class="tag tag-sm"><?php echo esc_html($style->name); ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- 포스트 네비게이션 -->
        <nav class="post-navigation">
            <?php
            previous_post_link('<div class="nav-prev">%link</div>', '&larr; %title');
            next_post_link('<div class="nav-next">%link</div>', '%title &rarr;');
            ?>
        </nav>

        <!-- 댓글 -->
        <?php if (comments_open() || get_comments_number()) :
            comments_template();
        endif; ?>
    </div>
</article>

<?php
endwhile;
get_footer();
