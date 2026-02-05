<?php
/**
 * ?¼ì • ë©”í? ?¬ì´?œë°”
 *
 * @package TripTalk
 */

defined('ABSPATH') || exit;

$post_id     = get_the_ID();
$dest_name   = get_post_meta($post_id, '_ft_destination_name', true);
$duration    = get_post_meta($post_id, '_ft_duration', true);
$price       = get_post_meta($post_id, '_ft_price_range', true);
$difficulty  = get_post_meta($post_id, '_ft_difficulty', true);
$best_season = get_post_meta($post_id, '_ft_best_season', true);
$highlights  = get_post_meta($post_id, '_ft_highlights', true);
?>

<div class="sidebar-card sidebar-card-info">
    <h3 class="sidebar-card-title"><?php esc_html_e('?¬í–‰ ?•ë³´', 'flavor-trip'); ?></h3>
    <dl class="info-list">
        <?php if ($dest_name) : ?>
            <dt><?php esc_html_e('ëª©ì ì§€', 'flavor-trip'); ?></dt>
            <dd><?php echo esc_html($dest_name); ?></dd>
        <?php endif; ?>

        <?php if ($duration) : ?>
            <dt><?php esc_html_e('?¬í–‰ ê¸°ê°„', 'flavor-trip'); ?></dt>
            <dd><?php echo esc_html($duration); ?></dd>
        <?php endif; ?>

        <?php if ($price) : ?>
            <dt><?php esc_html_e('ê°€ê²©ë?', 'flavor-trip'); ?></dt>
            <dd><?php echo esc_html(ft_get_price_label($price)); ?></dd>
        <?php endif; ?>

        <?php if ($difficulty) : ?>
            <dt><?php esc_html_e('?œì´??, 'flavor-trip'); ?></dt>
            <dd><?php echo esc_html(ft_get_difficulty_label($difficulty)); ?></dd>
        <?php endif; ?>

        <?php if ($best_season) : ?>
            <dt><?php esc_html_e('ì¶”ì²œ ?œê¸°', 'flavor-trip'); ?></dt>
            <dd><?php echo esc_html($best_season); ?></dd>
        <?php endif; ?>
    </dl>
</div>

<?php if ($highlights) :
    $highlight_items = array_map('trim', explode(',', $highlights));
    if ($highlight_items) : ?>
        <div class="sidebar-card sidebar-card-highlights">
            <h3 class="sidebar-card-title"><?php esc_html_e('?˜ì´?¼ì´??, 'flavor-trip'); ?></h3>
            <ul class="highlights-list">
                <?php foreach ($highlight_items as $item) : ?>
                    <li><?php echo esc_html($item); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif;
endif; ?>

<div class="sidebar-card sidebar-card-share">
    <h3 class="sidebar-card-title"><?php esc_html_e('ê³µìœ ?˜ê¸°', 'flavor-trip'); ?></h3>
    <div class="share-buttons">
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" class="share-btn share-facebook" target="_blank" rel="noopener noreferrer">Facebook</a>
        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" class="share-btn share-twitter" target="_blank" rel="noopener noreferrer">X</a>
        <a href="https://social-plugins.line.me/lineit/share?url=<?php echo urlencode(get_permalink()); ?>" class="share-btn share-line" target="_blank" rel="noopener noreferrer">Line</a>
    </div>
</div>

<?php
// ê´€???¼ì •
$destinations = get_the_terms($post_id, 'destination');
if ($destinations && !is_wp_error($destinations)) :
    $related = new WP_Query([
        'post_type'      => 'travel_itinerary',
        'posts_per_page' => 3,
        'post__not_in'   => [$post_id],
        'tax_query'      => [[
            'taxonomy' => 'destination',
            'field'    => 'term_id',
            'terms'    => wp_list_pluck($destinations, 'term_id'),
        ]],
    ]);
    if ($related->have_posts()) : ?>
        <div class="sidebar-card sidebar-card-related">
            <h3 class="sidebar-card-title"><?php esc_html_e('ê´€???¼ì •', 'flavor-trip'); ?></h3>
            <ul class="related-list">
                <?php while ($related->have_posts()) : $related->the_post(); ?>
                    <li>
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('ft-thumbnail-sm'); ?>
                            <?php endif; ?>
                            <span><?php the_title(); ?></span>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
        <?php wp_reset_postdata();
    endif;
endif;
