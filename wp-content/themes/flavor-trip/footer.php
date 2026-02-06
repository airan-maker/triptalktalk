<?php
/**
 * 푸터 템플릿
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;
?>
</main><!-- #main-content -->

<footer class="site-footer">
    <div class="footer-inner container">
        <div class="footer-grid">
            <div class="footer-about">
                <h3 class="footer-title"><?php bloginfo('name'); ?></h3>
                <p class="footer-description"><?php bloginfo('description'); ?></p>
                <?php
                $social_links = [
                    'instagram' => get_theme_mod('ft_social_instagram'),
                    'youtube'   => get_theme_mod('ft_social_youtube'),
                    'blog'      => get_theme_mod('ft_social_blog'),
                ];
                $social_links = array_filter($social_links);
                if ($social_links) : ?>
                    <div class="social-links">
                        <?php foreach ($social_links as $platform => $url) : ?>
                            <a href="<?php echo esc_url($url); ?>" class="social-link social-<?php echo esc_attr($platform); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr(ucfirst($platform)); ?>">
                                <?php echo esc_html(ucfirst($platform)); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="footer-nav">
                <h3 class="footer-title"><?php esc_html_e('메뉴', 'flavor-trip'); ?></h3>
                <?php
                wp_nav_menu([
                    'theme_location' => 'footer',
                    'container'      => false,
                    'menu_class'     => 'footer-menu',
                    'fallback_cb'    => false,
                    'depth'          => 1,
                ]);
                ?>
            </div>

            <div class="footer-destinations">
                <h3 class="footer-title"><?php esc_html_e('인기 여행지', 'flavor-trip'); ?></h3>
                <?php
                $destinations = get_terms([
                    'taxonomy'   => 'destination',
                    'number'     => 6,
                    'orderby'    => 'count',
                    'order'      => 'DESC',
                    'hide_empty' => true,
                ]);
                if (!is_wp_error($destinations) && $destinations) : ?>
                    <ul class="footer-destinations-list">
                        <?php foreach ($destinations as $dest) : ?>
                            <li><a href="<?php echo esc_url(get_term_link($dest)); ?>"><?php echo esc_html($dest->name); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="footer-styles">
                <h3 class="footer-title"><?php esc_html_e('여행 스타일', 'flavor-trip'); ?></h3>
                <?php
                $styles = get_terms([
                    'taxonomy'   => 'travel_style',
                    'number'     => 6,
                    'orderby'    => 'count',
                    'order'      => 'DESC',
                    'hide_empty' => true,
                ]);
                if (!is_wp_error($styles) && $styles) : ?>
                    <ul class="footer-styles-list">
                        <?php foreach ($styles as $style) : ?>
                            <li><a href="<?php echo esc_url(get_term_link($style)); ?>"><?php echo esc_html($style->name); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?>. <?php esc_html_e('All rights reserved.', 'flavor-trip'); ?></p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
