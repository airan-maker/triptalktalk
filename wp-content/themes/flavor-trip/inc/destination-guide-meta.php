<?php
/**
 * 도시 가이드 메타박스
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

add_action('add_meta_boxes', function () {
    add_meta_box(
        'ft_guide_details',
        __('도시 가이드 정보', 'flavor-trip'),
        'ft_render_guide_meta_box',
        'destination_guide',
        'normal',
        'high'
    );
});

function ft_render_guide_meta_box($post) {
    wp_nonce_field('ft_guide_nonce', 'ft_guide_nonce_field');

    $city    = get_post_meta($post->ID, '_ft_guide_city', true);
    $country = get_post_meta($post->ID, '_ft_guide_country', true);
    $intro   = get_post_meta($post->ID, '_ft_guide_intro', true);
    $data    = get_post_meta($post->ID, '_ft_guide_data', true);

    $places_count = !empty($data['places']) ? count($data['places']) : 0;
    $restaurants_count = !empty($data['restaurants']) ? count($data['restaurants']) : 0;
    $hotels_count = !empty($data['hotels']) ? count($data['hotels']) : 0;
    ?>
    <table class="form-table ft-meta-table">
        <tr>
            <th><label for="_ft_guide_city">도시명</label></th>
            <td><input type="text" id="_ft_guide_city" name="_ft_guide_city" value="<?php echo esc_attr($city); ?>" class="widefat" placeholder="예: 삿포로"></td>
        </tr>
        <tr>
            <th><label for="_ft_guide_country">국가</label></th>
            <td><input type="text" id="_ft_guide_country" name="_ft_guide_country" value="<?php echo esc_attr($country); ?>" class="widefat" placeholder="예: 일본"></td>
        </tr>
        <tr>
            <th><label for="_ft_guide_intro">소개</label></th>
            <td><textarea id="_ft_guide_intro" name="_ft_guide_intro" rows="3" class="widefat" placeholder="도시 소개 문구"><?php echo esc_textarea($intro); ?></textarea></td>
        </tr>
        <?php if ($data) : ?>
        <tr>
            <th>데이터 요약</th>
            <td>
                <strong>관광지 <?php echo esc_html($places_count); ?>개</strong> |
                <strong>식당 <?php echo esc_html($restaurants_count); ?>개</strong> |
                <strong>호텔 <?php echo esc_html($hotels_count); ?>개</strong>
                <p class="description">데이터는 WP-CLI 시드 스크립트로 관리합니다.</p>
            </td>
        </tr>
        <?php endif; ?>
    </table>
    <?php
}

add_action('save_post_destination_guide', function ($post_id) {
    if (!isset($_POST['ft_guide_nonce_field']) || !wp_verify_nonce($_POST['ft_guide_nonce_field'], 'ft_guide_nonce')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    foreach (['_ft_guide_city', '_ft_guide_country'] as $key) {
        if (isset($_POST[$key])) {
            update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
        }
    }
    if (isset($_POST['_ft_guide_intro'])) {
        update_post_meta($post_id, '_ft_guide_intro', sanitize_textarea_field($_POST['_ft_guide_intro']));
    }
});
