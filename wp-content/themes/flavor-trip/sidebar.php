<?php
/**
 * 사이드바
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

if (!is_active_sidebar('sidebar-main')) {
    return;
}
?>

<aside class="sidebar" role="complementary">
    <?php dynamic_sidebar('sidebar-main'); ?>
</aside>
