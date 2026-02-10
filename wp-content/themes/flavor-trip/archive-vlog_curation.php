<?php
/**
 * 브이로그 큐레이션 아카이브 템플릿
 *
 * @package Flavor_Trip
 */

get_header();
?>

<div class="vlog-archive-hero">
    <div class="container">
        <?php get_template_part('template-parts/breadcrumbs'); ?>
        <h1><?php esc_html_e('브이로그', 'flavor-trip'); ?></h1>
        <p><?php esc_html_e('여행 크리에이터의 생생한 브이로그를 큐레이션합니다', 'flavor-trip'); ?></p>
    </div>
</div>

<div class="container">
    <?php
    // 카테고리 필터
    $vlog_cats = get_terms([
        'taxonomy'   => 'vlog_category',
        'hide_empty' => true,
    ]);
    if ($vlog_cats && !is_wp_error($vlog_cats)) : ?>
        <div class="vlog-category-filter">
            <a href="<?php echo esc_url(get_post_type_archive_link('vlog_curation')); ?>"
               class="<?php echo !is_tax('vlog_category') ? 'active' : ''; ?>">
                <?php esc_html_e('전체', 'flavor-trip'); ?>
            </a>
            <?php foreach ($vlog_cats as $cat) :
                $is_active = is_tax('vlog_category', $cat->slug) ? 'active' : '';
            ?>
                <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="<?php echo $is_active; ?>">
                    <?php echo esc_html($cat->name); ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (have_posts()) : ?>
        <div class="posts-grid posts-grid--3">
            <?php while (have_posts()) : the_post();
                get_template_part('template-parts/vlog-card');
            endwhile; ?>
        </div>
        <?php ft_pagination(); ?>
    <?php else : ?>
        <?php get_template_part('template-parts/content', 'none'); ?>
    <?php endif; ?>
</div>

<?php
get_footer();
