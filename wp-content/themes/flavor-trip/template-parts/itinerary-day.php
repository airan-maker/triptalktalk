<?php
/**
 * 일자별 일정 블록
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

$day    = get_query_var('ft_day_data', []);
$number = get_query_var('ft_day_number', 1);
$places = !empty($day['places']) ? array_map('trim', explode(',', $day['places'])) : [];
?>

<div class="timeline-item">
    <div class="timeline-marker">
        <span class="day-number">Day <?php echo esc_html($number); ?></span>
    </div>
    <div class="timeline-content">
        <?php if (!empty($day['title'])) : ?>
            <h3 class="day-title"><?php echo esc_html($day['title']); ?></h3>
        <?php endif; ?>

        <?php if (!empty($day['description'])) : ?>
            <div class="day-description"><?php echo wp_kses_post(wpautop($day['description'])); ?></div>
        <?php endif; ?>

        <?php if ($places) : ?>
            <div class="day-places">
                <span class="places-label"><?php esc_html_e('주요 장소:', 'flavor-trip'); ?></span>
                <?php foreach ($places as $place) : ?>
                    <span class="place-tag"><?php echo esc_html($place); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($day['tip'])) : ?>
            <div class="day-tip">
                <strong><?php esc_html_e('💡 팁:', 'flavor-trip'); ?></strong>
                <?php echo wp_kses_post($day['tip']); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
