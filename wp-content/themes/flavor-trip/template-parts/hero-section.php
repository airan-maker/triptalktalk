<?php
/**
 * 홈 히어로 섹션
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

$hero_title    = get_theme_mod('ft_hero_title', '맛있는 여행의 시작');
$hero_subtitle = get_theme_mod('ft_hero_subtitle', '특별한 여행 일정을 만나보세요. 전문가가 설계한 코스로 잊지 못할 여행을 떠나세요.');
$hero_image    = get_theme_mod('ft_hero_image');
?>

<section class="hero" <?php if ($hero_image) : ?>style="background-image: url('<?php echo esc_url($hero_image); ?>')"<?php endif; ?>>
    <div class="hero-overlay"></div>
    <div class="hero-content container">
        <h1 class="hero-title"><?php echo esc_html($hero_title); ?></h1>
        <p class="hero-subtitle"><?php echo esc_html($hero_subtitle); ?></p>
        <div class="hero-actions">
            <a href="https://triptalk.me" class="btn-cta-hero" target="_blank" rel="noopener noreferrer">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <?php esc_html_e('나만의 여행 일정 만들기', 'flavor-trip'); ?>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</section>
