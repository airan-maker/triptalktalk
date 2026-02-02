<?php
/**
 * 헤더 템플릿
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php ft_seo_meta_tags(); ?>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main-content">
    <?php esc_html_e('본문으로 건너뛰기', 'flavor-trip'); ?>
</a>

<header class="site-header" id="site-header">
    <div class="header-inner container">
        <div class="site-branding">
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="site-title-link" rel="home">
                    <span class="site-title"><?php bloginfo('name'); ?></span>
                </a>
            <?php endif; ?>
        </div>

        <button class="menu-toggle" aria-controls="primary-nav" aria-expanded="false" aria-label="<?php esc_attr_e('메뉴 열기', 'flavor-trip'); ?>">
            <span class="hamburger"></span>
        </button>

        <nav id="primary-nav" class="main-navigation" aria-label="<?php esc_attr_e('메인 메뉴', 'flavor-trip'); ?>">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'nav-menu',
                'fallback_cb'    => false,
                'depth'          => 2,
            ]);
            ?>
            <div class="nav-search">
                <?php get_search_form(); ?>
            </div>
        </nav>
    </div>
</header>

<main id="main-content" class="site-main">
