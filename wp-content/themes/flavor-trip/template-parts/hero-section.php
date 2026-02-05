<?php
/**
 * ???ˆì–´ë¡??¹ì…˜
 *
 * @package TripTalk
 */

defined('ABSPATH') || exit;

$hero_title    = get_theme_mod('ft_hero_title', 'Traveler\'s Real Talk');
$hero_subtitle = get_theme_mod('ft_hero_subtitle', '?¬í–‰ ?¼ì •???¤ë§ˆ?¸í•˜ê²? ?¬í–‰?ì˜ ì§„ì§œ ?´ì•¼ê¸°ë? ë§Œë‚˜ë³´ì„¸??');
$hero_image    = get_theme_mod('ft_hero_image');
?>

<section class="hero" <?php if ($hero_image) : ?>style="background-image: url('<?php echo esc_url($hero_image); ?>')"<?php endif; ?>>
    <div class="hero-overlay"></div>
    <div class="hero-content container">
        <h1 class="hero-title"><?php echo esc_html($hero_title); ?></h1>
        <p class="hero-subtitle"><?php echo esc_html($hero_subtitle); ?></p>
        <div class="hero-actions">
            <a href="<?php echo esc_url(get_post_type_archive_link('travel_itinerary')); ?>" class="btn btn-primary btn-lg">
                <?php esc_html_e('?¬í–‰ ?¼ì • ?˜ëŸ¬ë³´ê¸°', 'flavor-trip'); ?>
            </a>
            <?php get_search_form(); ?>
        </div>
    </div>
</section>
