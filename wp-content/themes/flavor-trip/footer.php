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
                $current_lang = function_exists('pll_current_language') ? pll_current_language() : 'ko';
                $destinations = get_terms([
                    'taxonomy'   => 'destination',
                    'number'     => 6,
                    'orderby'    => 'count',
                    'order'      => 'DESC',
                    'hide_empty' => true,
                    'lang'       => $current_lang,
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
                $current_lang = function_exists('pll_current_language') ? pll_current_language() : 'ko';
                $styles = get_terms([
                    'taxonomy'   => 'travel_style',
                    'number'     => 6,
                    'orderby'    => 'count',
                    'order'      => 'DESC',
                    'hide_empty' => true,
                    'lang'       => $current_lang,
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

        <?php if (function_exists('pll_the_languages')) : ?>
        <div class="footer-language">
            <div class="footer-lang-selector">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                <ul class="footer-lang-list">
                    <?php
                    $ft_allowed_langs = ['ko', 'en', 'zh-cn', 'ja', 'fr', 'de'];
                    $ft_langs = pll_the_languages(['raw' => 1]);
                    if ($ft_langs) :
                        foreach ($ft_langs as $lang) :
                            if (!in_array($lang['slug'], $ft_allowed_langs, true)) continue;
                            $css = $lang['current_lang'] ? ' class="current-lang"' : '';
                    ?>
                        <li<?php echo $css; ?>><a href="<?php echo esc_url($lang['url']); ?>" hreflang="<?php echo esc_attr($lang['slug']); ?>"><?php echo esc_html($lang['name']); ?></a></li>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <div class="footer-bottom">
            <p>&copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?>. <?php esc_html_e('All rights reserved.', 'flavor-trip'); ?></p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
