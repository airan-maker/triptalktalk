<?php
/**
 * Flavor Trip 테마 functions.php
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

define('FT_VERSION', '2.2.0');
define('FT_DIR', get_template_directory());
define('FT_URI', get_template_directory_uri());

// 테마 모듈 로드
$ft_includes = [
    'inc/theme-setup.php',
    'inc/enqueue-scripts.php',
    'inc/custom-post-types.php',
    'inc/custom-taxonomies.php',
    'inc/custom-meta-boxes.php',
    'inc/seo-functions.php',
    'inc/schema-markup.php',
    'inc/breadcrumbs.php',
    'inc/template-tags.php',
    'inc/customizer.php',
    'inc/widgets.php',
];

foreach ($ft_includes as $file) {
    $filepath = FT_DIR . '/' . $file;
    if (file_exists($filepath)) {
        require_once $filepath;
    }
}
