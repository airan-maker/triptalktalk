<?php
/**
 * 일자별 일정 블록 (spots 구조 + 구 데이터 호환)
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

$day    = get_query_var('ft_day_data', []);
$number = get_query_var('ft_day_number', 1);

if (!empty($day['spots']) && is_array($day['spots'])) :
    // ── 새 구조: Day > Spot 카드 ──
    $spot_counter = get_query_var('ft_spot_counter', 0);
?>
<div class="day-block" data-day="<?php echo esc_attr($number); ?>">
    <div class="day-header">
        <span class="day-badge">Day <?php echo esc_html($number); ?></span>
        <?php if (!empty($day['title'])) : ?>
            <span class="day-header-title"><?php echo esc_html(preg_replace('/^Day\s*\d+\s*[:：]\s*/u', '', $day['title'])); ?></span>
        <?php endif; ?>
    </div>
    <div class="spots-timeline">
        <?php foreach ($day['spots'] as $spot) :
            $spot_counter++;
        ?>
            <div class="spot-card">
                <div class="spot-marker">
                    <span class="spot-number"><?php echo esc_html($spot_counter); ?></span>
                </div>
                <div class="spot-content">
                    <?php if (!empty($spot['name'])) : ?>
                        <h4 class="spot-name"><?php echo esc_html($spot['name']); ?></h4>
                    <?php endif; ?>
                    <?php if (!empty($spot['description'])) : ?>
                        <p class="spot-description"><?php echo wp_kses_post($spot['description']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($spot['tip'])) : ?>
                        <div class="spot-tip">💡 <?php echo wp_kses_post($spot['tip']); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php
    set_query_var('ft_spot_counter', $spot_counter);
else :
    // ── 구 구조: timeline-item 레이아웃 (호환) ──
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
<?php endif; ?>
