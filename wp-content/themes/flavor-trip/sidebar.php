<?php
/**
 * ?¬ì´?œë°”
 *
 * @package TripTalk
 */

defined('ABSPATH') || exit;

if (!is_active_sidebar('sidebar-main')) {
    return;
}
?>

<aside class="sidebar" role="complementary">
    <?php dynamic_sidebar('sidebar-main'); ?>
</aside>
