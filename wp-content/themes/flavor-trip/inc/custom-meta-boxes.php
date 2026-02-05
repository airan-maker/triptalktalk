<?php
/**
 * 커스텀 메타박스 + JS 리피터
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

/**
 * 메타박스 등록
 */
add_action('add_meta_boxes', function () {
    add_meta_box(
        'ft_itinerary_details',
        __('여행 일정 상세 정보', 'flavor-trip'),
        'ft_render_itinerary_meta_box',
        'travel_itinerary',
        'normal',
        'high'
    );

    add_meta_box(
        'ft_itinerary_days',
        __('일자별 일정', 'flavor-trip'),
        'ft_render_days_meta_box',
        'travel_itinerary',
        'normal',
        'high'
    );

    add_meta_box(
        'ft_itinerary_gallery',
        __('포토 갤러리', 'flavor-trip'),
        'ft_render_gallery_meta_box',
        'travel_itinerary',
        'side',
        'default'
    );

    add_meta_box(
        'ft_itinerary_map',
        __('지도 좌표', 'flavor-trip'),
        'ft_render_map_meta_box',
        'travel_itinerary',
        'side',
        'default'
    );
});

/**
 * 여행 일정 상세 정보 메타박스 렌더링
 */
function ft_render_itinerary_meta_box($post) {
    wp_nonce_field('ft_itinerary_nonce', 'ft_itinerary_nonce_field');

    $fields = [
        '_ft_destination_name' => ['label' => '목적지', 'type' => 'text', 'placeholder' => '예: 도쿄, 일본'],
        '_ft_duration'         => ['label' => '여행 기간', 'type' => 'text', 'placeholder' => '예: 3박 4일'],
        '_ft_price_range'      => ['label' => '가격대', 'type' => 'select', 'options' => ['' => '선택', 'budget' => '💰 가성비', 'moderate' => '💰💰 보통', 'premium' => '💰💰💰 프리미엄', 'luxury' => '💰💰💰💰 럭셔리']],
        '_ft_difficulty'       => ['label' => '난이도', 'type' => 'select', 'options' => ['' => '선택', 'easy' => '쉬움', 'moderate' => '보통', 'hard' => '어려움']],
        '_ft_best_season'      => ['label' => '추천 시기', 'type' => 'text', 'placeholder' => '예: 3월~5월, 9월~11월'],
        '_ft_highlights'       => ['label' => '하이라이트', 'type' => 'textarea', 'placeholder' => '쉼표로 구분 (예: 신주쿠 야경, 츠키지 시장, 아사쿠사 사원)'],
    ];

    echo '<table class="form-table ft-meta-table">';
    foreach ($fields as $key => $field) {
        $value = get_post_meta($post->ID, $key, true);
        echo '<tr>';
        echo '<th><label for="' . esc_attr($key) . '">' . esc_html($field['label']) . '</label></th>';
        echo '<td>';
        switch ($field['type']) {
            case 'text':
                printf(
                    '<input type="text" id="%s" name="%s" value="%s" placeholder="%s" class="widefat">',
                    esc_attr($key), esc_attr($key), esc_attr($value), esc_attr($field['placeholder'] ?? '')
                );
                break;
            case 'textarea':
                printf(
                    '<textarea id="%s" name="%s" rows="3" placeholder="%s" class="widefat">%s</textarea>',
                    esc_attr($key), esc_attr($key), esc_attr($field['placeholder'] ?? ''), esc_textarea($value)
                );
                break;
            case 'select':
                printf('<select id="%s" name="%s" class="widefat">', esc_attr($key), esc_attr($key));
                foreach ($field['options'] as $opt_val => $opt_label) {
                    printf(
                        '<option value="%s" %s>%s</option>',
                        esc_attr($opt_val), selected($value, $opt_val, false), esc_html($opt_label)
                    );
                }
                echo '</select>';
                break;
        }
        echo '</td></tr>';
    }
    echo '</table>';
}

/**
 * 일자별 일정 리피터 렌더링
 */
function ft_render_days_meta_box($post) {
    $days = get_post_meta($post->ID, '_ft_days', true);
    if (!is_array($days)) {
        $days = [];
    }
    ?>
    <div id="ft-days-repeater">
        <div id="ft-days-list">
            <?php if (empty($days)) : ?>
                <p class="ft-no-days"><?php esc_html_e('아래 버튼을 클릭하여 일정을 추가하세요.', 'flavor-trip'); ?></p>
            <?php else : ?>
                <?php foreach ($days as $i => $day) : ?>
                    <div class="ft-day-item" data-index="<?php echo esc_attr($i); ?>">
                        <div class="ft-day-header">
                            <strong>Day <?php echo esc_html($i + 1); ?></strong>
                            <button type="button" class="ft-remove-day button-link-delete">&times;</button>
                        </div>
                        <p>
                            <label>제목</label>
                            <input type="text" name="_ft_days[<?php echo esc_attr($i); ?>][title]" value="<?php echo esc_attr($day['title'] ?? ''); ?>" class="widefat" placeholder="예: 도쿄 도착 & 시부야 탐험">
                        </p>
                        <p>
                            <label>상세 내용</label>
                            <textarea name="_ft_days[<?php echo esc_attr($i); ?>][description]" rows="4" class="widefat" placeholder="이 날의 일정을 상세히 작성하세요."><?php echo esc_textarea($day['description'] ?? ''); ?></textarea>
                        </p>
                        <p>
                            <label>주요 장소</label>
                            <input type="text" name="_ft_days[<?php echo esc_attr($i); ?>][places]" value="<?php echo esc_attr($day['places'] ?? ''); ?>" class="widefat" placeholder="쉼표로 구분 (예: 시부야 스크램블, 하라주쿠, 메이지 신궁)">
                        </p>
                        <p>
                            <label>팁</label>
                            <input type="text" name="_ft_days[<?php echo esc_attr($i); ?>][tip]" value="<?php echo esc_attr($day['tip'] ?? ''); ?>" class="widefat" placeholder="여행 팁을 입력하세요.">
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" id="ft-add-day" class="button button-primary"><?php esc_html_e('+ 일정 추가', 'flavor-trip'); ?></button>
    </div>

    <script>
    (function() {
        var list = document.getElementById('ft-days-list');
        var addBtn = document.getElementById('ft-add-day');
        var index = <?php echo count($days); ?>;

        addBtn.addEventListener('click', function() {
            var noMsg = list.querySelector('.ft-no-days');
            if (noMsg) noMsg.remove();

            var item = document.createElement('div');
            item.className = 'ft-day-item';
            item.dataset.index = index;
            item.innerHTML =
                '<div class="ft-day-header"><strong>Day ' + (index + 1) + '</strong>' +
                '<button type="button" class="ft-remove-day button-link-delete">&times;</button></div>' +
                '<p><label>제목</label><input type="text" name="_ft_days[' + index + '][title]" class="widefat" placeholder="예: 도쿄 도착 & 시부야 탐험"></p>' +
                '<p><label>상세 내용</label><textarea name="_ft_days[' + index + '][description]" rows="4" class="widefat" placeholder="이 날의 일정을 상세히 작성하세요."></textarea></p>' +
                '<p><label>주요 장소</label><input type="text" name="_ft_days[' + index + '][places]" class="widefat" placeholder="쉼표로 구분"></p>' +
                '<p><label>팁</label><input type="text" name="_ft_days[' + index + '][tip]" class="widefat" placeholder="여행 팁을 입력하세요."></p>';
            list.appendChild(item);
            index++;
        });

        list.addEventListener('click', function(e) {
            if (e.target.classList.contains('ft-remove-day')) {
                e.target.closest('.ft-day-item').remove();
            }
        });
    })();
    </script>

    <style>
    .ft-day-item { background: #f9f9f9; border: 1px solid #ddd; padding: 12px; margin-bottom: 10px; border-radius: 4px; }
    .ft-day-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
    .ft-day-item label { display: block; font-weight: 600; margin-bottom: 4px; font-size: 12px; }
    .ft-day-item p { margin: 8px 0; }
    .ft-remove-day { font-size: 18px; border: none; background: none; color: #b32d2e; cursor: pointer; padding: 0 4px; }
    .ft-meta-table th { width: 120px; }
    </style>
    <?php
}

/**
 * 갤러리 메타박스 렌더링
 */
function ft_render_gallery_meta_box($post) {
    $gallery_ids = get_post_meta($post->ID, '_ft_gallery', true);
    if (!is_array($gallery_ids)) {
        $gallery_ids = [];
    }
    ?>
    <div id="ft-gallery-box">
        <input type="hidden" id="ft-gallery-ids" name="_ft_gallery" value="<?php echo esc_attr(implode(',', $gallery_ids)); ?>">
        <div id="ft-gallery-preview">
            <?php foreach ($gallery_ids as $id) :
                $img = wp_get_attachment_image_src($id, 'thumbnail');
                if ($img) : ?>
                    <div class="ft-gallery-thumb" data-id="<?php echo esc_attr($id); ?>">
                        <img src="<?php echo esc_url($img[0]); ?>" alt="">
                        <button type="button" class="ft-gallery-remove">&times;</button>
                    </div>
                <?php endif;
            endforeach; ?>
        </div>
        <button type="button" id="ft-gallery-add" class="button"><?php esc_html_e('이미지 선택', 'flavor-trip'); ?></button>
    </div>

    <script>
    (function() {
        var frame;
        document.getElementById('ft-gallery-add').addEventListener('click', function(e) {
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({
                title: '갤러리 이미지 선택',
                button: { text: '선택' },
                multiple: true,
                library: { type: 'image' }
            });
            frame.on('select', function() {
                var attachments = frame.state().get('selection').toJSON();
                var preview = document.getElementById('ft-gallery-preview');
                var input = document.getElementById('ft-gallery-ids');
                var ids = input.value ? input.value.split(',') : [];
                attachments.forEach(function(att) {
                    if (ids.indexOf(String(att.id)) === -1) {
                        ids.push(att.id);
                        var thumb = document.createElement('div');
                        thumb.className = 'ft-gallery-thumb';
                        thumb.dataset.id = att.id;
                        var url = att.sizes && att.sizes.thumbnail ? att.sizes.thumbnail.url : att.url;
                        thumb.innerHTML = '<img src="' + url + '" alt=""><button type="button" class="ft-gallery-remove">&times;</button>';
                        preview.appendChild(thumb);
                    }
                });
                input.value = ids.join(',');
            });
            frame.open();
        });

        document.getElementById('ft-gallery-preview').addEventListener('click', function(e) {
            if (e.target.classList.contains('ft-gallery-remove')) {
                var thumb = e.target.closest('.ft-gallery-thumb');
                var id = thumb.dataset.id;
                var input = document.getElementById('ft-gallery-ids');
                var ids = input.value.split(',').filter(function(v) { return v !== id; });
                input.value = ids.join(',');
                thumb.remove();
            }
        });
    })();
    </script>

    <style>
    #ft-gallery-preview { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 8px; }
    .ft-gallery-thumb { position: relative; width: 60px; height: 60px; }
    .ft-gallery-thumb img { width: 100%; height: 100%; object-fit: cover; border-radius: 4px; }
    .ft-gallery-remove { position: absolute; top: -4px; right: -4px; background: #b32d2e; color: #fff; border: none; border-radius: 50%; width: 18px; height: 18px; font-size: 12px; line-height: 1; cursor: pointer; }
    </style>
    <?php
}

/**
 * 지도 좌표 메타박스 렌더링
 */
function ft_render_map_meta_box($post) {
    $lat = get_post_meta($post->ID, '_ft_map_lat', true);
    $lng = get_post_meta($post->ID, '_ft_map_lng', true);
    $zoom = get_post_meta($post->ID, '_ft_map_zoom', true) ?: '12';
    ?>
    <p>
        <label for="_ft_map_lat"><?php esc_html_e('위도 (Latitude)', 'flavor-trip'); ?></label>
        <input type="text" id="_ft_map_lat" name="_ft_map_lat" value="<?php echo esc_attr($lat); ?>" class="widefat" placeholder="예: 35.6762">
    </p>
    <p>
        <label for="_ft_map_lng"><?php esc_html_e('경도 (Longitude)', 'flavor-trip'); ?></label>
        <input type="text" id="_ft_map_lng" name="_ft_map_lng" value="<?php echo esc_attr($lng); ?>" class="widefat" placeholder="예: 139.6503">
    </p>
    <p>
        <label for="_ft_map_zoom"><?php esc_html_e('줌 레벨 (1~18)', 'flavor-trip'); ?></label>
        <input type="number" id="_ft_map_zoom" name="_ft_map_zoom" value="<?php echo esc_attr($zoom); ?>" class="widefat" min="1" max="18">
    </p>
    <?php
}

/**
 * 메타 데이터 저장
 */
add_action('save_post_travel_itinerary', function ($post_id) {
    if (!isset($_POST['ft_itinerary_nonce_field']) || !wp_verify_nonce($_POST['ft_itinerary_nonce_field'], 'ft_itinerary_nonce')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // 단일 필드
    $text_fields = ['_ft_destination_name', '_ft_duration', '_ft_best_season', '_ft_highlights'];
    foreach ($text_fields as $key) {
        if (isset($_POST[$key])) {
            update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
        }
    }

    $select_fields = ['_ft_price_range', '_ft_difficulty'];
    foreach ($select_fields as $key) {
        if (isset($_POST[$key])) {
            update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
        }
    }

    // 지도 좌표
    if (isset($_POST['_ft_map_lat'])) {
        update_post_meta($post_id, '_ft_map_lat', sanitize_text_field($_POST['_ft_map_lat']));
    }
    if (isset($_POST['_ft_map_lng'])) {
        update_post_meta($post_id, '_ft_map_lng', sanitize_text_field($_POST['_ft_map_lng']));
    }
    if (isset($_POST['_ft_map_zoom'])) {
        update_post_meta($post_id, '_ft_map_zoom', absint($_POST['_ft_map_zoom']));
    }

    // 갤러리
    if (isset($_POST['_ft_gallery'])) {
        $gallery = sanitize_text_field($_POST['_ft_gallery']);
        $ids = $gallery ? array_map('absint', explode(',', $gallery)) : [];
        update_post_meta($post_id, '_ft_gallery', $ids);
    }

    // 일자별 일정 리피터
    if (isset($_POST['_ft_days']) && is_array($_POST['_ft_days'])) {
        $days = [];
        foreach ($_POST['_ft_days'] as $day) {
            $days[] = [
                'title'       => sanitize_text_field($day['title'] ?? ''),
                'description' => sanitize_textarea_field($day['description'] ?? ''),
                'places'      => sanitize_text_field($day['places'] ?? ''),
                'tip'         => sanitize_text_field($day['tip'] ?? ''),
            ];
        }
        update_post_meta($post_id, '_ft_days', $days);
    } else {
        update_post_meta($post_id, '_ft_days', []);
    }
});

// 미디어 업로더 스크립트 로드
add_action('admin_enqueue_scripts', function ($hook) {
    global $post_type;
    if ($post_type === 'travel_itinerary' && in_array($hook, ['post.php', 'post-new.php'])) {
        wp_enqueue_media();
    }
});
