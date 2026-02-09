<?php
/**
 * 일자별 일정 블록 (type 구분: place/restaurant)
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

$day    = get_query_var('ft_day_data', []);
$number = get_query_var('ft_day_number', 1);

if (!empty($day['spots']) && is_array($day['spots'])) :
    // spots를 시간순 정렬
    $spots = $day['spots'];
    usort($spots, function($a, $b) {
        $time_a = $a['time'] ?? '00:00';
        $time_b = $b['time'] ?? '00:00';
        return strcmp($time_a, $time_b);
    });
?>
<div class="day-block" data-day="<?php echo esc_attr($number); ?>">
    <div class="day-header">
        <span class="day-badge">Day <?php echo esc_html($number); ?></span>
        <?php if (!empty($day['title'])) : ?>
            <h3 class="day-header-title"><?php echo esc_html(preg_replace('/^Day\s*\d+\s*[:：]\s*/u', '', $day['title'])); ?></h3>
        <?php endif; ?>
        <?php if (!empty($day['summary'])) : ?>
            <p class="day-summary"><?php echo esc_html($day['summary']); ?></p>
        <?php endif; ?>
    </div>

    <div class="spots-timeline">
        <?php
        $spot_index = 0;
        foreach ($spots as $spot) :
            $spot_index++;
            $type = $spot['type'] ?? 'place';
            $is_restaurant = ($type === 'restaurant');
        ?>
            <div class="spot-card spot-card--<?php echo esc_attr($type); ?>">
                <span class="spot-number-badge"><?php echo esc_html($spot_index); ?></span>
                <div class="spot-content">
                    <div class="spot-header">
                        <?php if (!empty($spot['name'])) : ?>
                            <h4 class="spot-name"><?php echo esc_html($spot['name']); ?></h4>
                        <?php endif; ?>
                        <?php if (!empty($spot['time'])) : ?>
                            <span class="spot-time"><?php echo esc_html($spot['time']); ?></span>
                        <?php endif; ?>
                        <?php if ($is_restaurant && !empty($spot['cuisine'])) : ?>
                            <span class="spot-cuisine"><?php echo esc_html($spot['cuisine']); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($spot['duration'])) : ?>
                            <span class="spot-duration"><?php echo esc_html($spot['duration']); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($spot['description'])) : ?>
                        <p class="spot-description"><?php echo wp_kses_post($spot['description']); ?></p>
                    <?php endif; ?>
                    <?php if ($is_restaurant) : ?>
                        <div class="spot-restaurant-meta">
                            <?php if (!empty($spot['menu'])) : ?>
                                <span class="spot-menu"><?php echo esc_html__('추천:', 'flavor-trip') . ' ' . esc_html($spot['menu']); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($spot['price'])) : ?>
                                <span class="spot-price"><?php echo esc_html($spot['price']); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($spot['wait_tip'])) : ?>
                                <span class="spot-wait-tip">⏰ <?php echo esc_html($spot['wait_tip']); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($spot['tip'])) : ?>
                        <div class="spot-tip">
                            <span class="tip-icon">💡</span>
                            <span class="tip-text"><?php echo wp_kses_post($spot['tip']); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($spot['link'])) : ?>
                        <a href="<?php echo esc_url($spot['link']); ?>" class="spot-link" target="_blank" rel="noopener">
                            <?php echo $is_restaurant ? esc_html__('예약하기 →', 'flavor-trip') : esc_html__('자세히 보기 →', 'flavor-trip'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($day['tip'])) : ?>
        <div class="day-tip-box">
            <div class="day-tip-content">
                <strong><?php esc_html_e('이 날의 핵심 팁', 'flavor-trip'); ?></strong>
                <p><?php echo wp_kses_post($day['tip']); ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php
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
