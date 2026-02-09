<?php
/**
 * 도시 가이드 — 정렬 가능 테이블 파셜
 *
 * Variables:
 *   $ft_guide_tab     — places / restaurants / hotels
 *   $ft_guide_items   — 아이템 배열
 *   $ft_guide_columns — 컬럼 정의 배열
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

$tab     = get_query_var('ft_guide_tab', 'places');
$items   = get_query_var('ft_guide_items', []);
$columns = get_query_var('ft_guide_columns', []);

if (empty($items)) return;

$rating_keys = ['family', 'couple', 'solo', 'friends', 'filial'];
$rating_labels = [
    'family'  => __('가족', 'flavor-trip'),
    'couple'  => __('커플', 'flavor-trip'),
    'solo'    => __('솔로', 'flavor-trip'),
    'friends' => __('친구', 'flavor-trip'),
    'filial'  => __('효도', 'flavor-trip'),
];
?>

<p class="guide-sort-hint"><?php esc_html_e('정렬하려면 열 제목을 클릭하세요', 'flavor-trip'); ?></p>

<div class="guide-table-wrapper">
    <table class="guide-table">
        <thead>
            <tr>
                <th class="col-num-header">#</th>
                <th class="col-name" data-sort-key="name"><?php esc_html_e('이름', 'flavor-trip'); ?> <span class="sort-icon">⇅</span></th>
                <th data-sort-key="area"><?php esc_html_e('지역', 'flavor-trip'); ?> <span class="sort-icon">⇅</span></th>

                <?php foreach ($columns as $col_key => $col_label) : ?>
                    <th data-sort-key="<?php echo esc_attr($col_key); ?>"><?php echo esc_html($col_label); ?> <span class="sort-icon">⇅</span></th>
                <?php endforeach; ?>

                <?php foreach ($rating_keys as $rk) : ?>
                    <th data-sort-key="<?php echo esc_attr($rk); ?>"><?php echo esc_html($rating_labels[$rk]); ?>★ <span class="sort-icon">⇅</span></th>
                <?php endforeach; ?>

                <th class="col-note-header"><?php esc_html_e('메모', 'flavor-trip'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $idx => $item) :
                $has_coords = !empty($item['lat']) && !empty($item['lng']);
            ?>
                <tr<?php if ($has_coords) : ?> data-lat="<?php echo esc_attr($item['lat']); ?>" data-lng="<?php echo esc_attr($item['lng']); ?>"<?php endif; ?>>
                    <td class="col-num"><?php echo esc_html($idx + 1); ?></td>
                    <td class="col-name"><?php echo esc_html($item['name'] ?? ''); ?></td>
                    <td class="col-area" data-value="<?php echo esc_attr($item['area'] ?? ''); ?>"><?php echo esc_html($item['area'] ?? ''); ?></td>

                    <?php foreach ($columns as $col_key => $col_label) : ?>
                        <td class="<?php echo $col_key === 'price' ? 'col-price' : ''; ?>" data-value="<?php echo esc_attr($item[$col_key] ?? ''); ?>">
                            <?php echo esc_html($item[$col_key] ?? ''); ?>
                        </td>
                    <?php endforeach; ?>

                    <?php foreach ($rating_keys as $rk) :
                        $rating = intval($item[$rk] ?? 0);
                    ?>
                        <td data-value="<?php echo esc_attr($rating); ?>">
                            <span class="guide-stars">
                                <?php for ($s = 1; $s <= 5; $s++) : ?>
                                    <span class="guide-star <?php echo $s <= $rating ? 'guide-star--filled' : ''; ?>">★</span>
                                <?php endfor; ?>
                            </span>
                            <span class="guide-rating-compact"><?php echo esc_html($rating); ?></span>
                        </td>
                    <?php endforeach; ?>

                    <td class="col-note"><?php echo esc_html($item['note'] ?? ''); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
