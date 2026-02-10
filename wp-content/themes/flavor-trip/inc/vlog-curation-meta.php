<?php
/**
 * 브이로그 큐레이션 메타박스
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

add_action('add_meta_boxes', function () {
    add_meta_box(
        'ft_vlog_info',
        __('브이로그 정보', 'flavor-trip'),
        'ft_render_vlog_info_meta_box',
        'vlog_curation',
        'normal',
        'high'
    );

    add_meta_box(
        'ft_vlog_timeline',
        __('타임라인 요약', 'flavor-trip'),
        'ft_render_vlog_timeline_meta_box',
        'vlog_curation',
        'normal',
        'default'
    );

    add_meta_box(
        'ft_vlog_spots',
        __('영상 속 장소', 'flavor-trip'),
        'ft_render_vlog_spots_meta_box',
        'vlog_curation',
        'normal',
        'default'
    );
});

/**
 * 브이로그 기본 정보 메타박스
 */
function ft_render_vlog_info_meta_box($post) {
    wp_nonce_field('ft_vlog_nonce', 'ft_vlog_nonce_field');

    $youtube_id   = get_post_meta($post->ID, '_ft_vlog_youtube_id', true);
    $channel_name = get_post_meta($post->ID, '_ft_vlog_channel_name', true);
    $channel_url  = get_post_meta($post->ID, '_ft_vlog_channel_url', true);
    $duration     = get_post_meta($post->ID, '_ft_vlog_duration', true);
    ?>
    <table class="form-table ft-meta-table">
        <tr>
            <th><label for="_ft_vlog_youtube_id">유튜브 영상 ID</label></th>
            <td>
                <input type="text" id="_ft_vlog_youtube_id" name="_ft_vlog_youtube_id"
                       value="<?php echo esc_attr($youtube_id); ?>" class="widefat"
                       placeholder="예: dQw4w9WgXcQ">
                <p class="description">유튜브 URL에서 v= 뒤의 ID만 입력하세요.</p>
            </td>
        </tr>
        <tr>
            <th><label for="_ft_vlog_channel_name">크리에이터 채널명</label></th>
            <td><input type="text" id="_ft_vlog_channel_name" name="_ft_vlog_channel_name"
                       value="<?php echo esc_attr($channel_name); ?>" class="widefat"
                       placeholder="예: 곱창킴 여행"></td>
        </tr>
        <tr>
            <th><label for="_ft_vlog_channel_url">채널 URL</label></th>
            <td><input type="url" id="_ft_vlog_channel_url" name="_ft_vlog_channel_url"
                       value="<?php echo esc_attr($channel_url); ?>" class="widefat"
                       placeholder="https://www.youtube.com/@channelname"></td>
        </tr>
        <tr>
            <th><label for="_ft_vlog_duration">영상 길이</label></th>
            <td><input type="text" id="_ft_vlog_duration" name="_ft_vlog_duration"
                       value="<?php echo esc_attr($duration); ?>" class="widefat"
                       placeholder="예: 15:30"></td>
        </tr>
    </table>
    <?php
}

/**
 * 타임라인 요약 메타박스 (리피터)
 */
function ft_render_vlog_timeline_meta_box($post) {
    $timeline = get_post_meta($post->ID, '_ft_vlog_timeline', true);
    if (!is_array($timeline)) $timeline = [];
    ?>
    <div id="ft-vlog-timeline-repeater">
        <table class="widefat" id="ft-timeline-table">
            <thead>
                <tr>
                    <th style="width:100px;">타임스탬프</th>
                    <th style="width:200px;">제목</th>
                    <th>설명</th>
                    <th style="width:50px;"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($timeline as $i => $item) : ?>
                <tr class="ft-timeline-row">
                    <td><input type="text" name="_ft_vlog_timeline[<?php echo $i; ?>][time]"
                               value="<?php echo esc_attr($item['time'] ?? ''); ?>" placeholder="3:45" class="widefat"></td>
                    <td><input type="text" name="_ft_vlog_timeline[<?php echo $i; ?>][title]"
                               value="<?php echo esc_attr($item['title'] ?? ''); ?>" placeholder="숨겨진 맛집" class="widefat"></td>
                    <td><input type="text" name="_ft_vlog_timeline[<?php echo $i; ?>][description]"
                               value="<?php echo esc_attr($item['description'] ?? ''); ?>" placeholder="설명" class="widefat"></td>
                    <td><button type="button" class="button ft-remove-row">삭제</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><button type="button" class="button ft-add-timeline-row">+ 타임라인 추가</button></p>
    </div>

    <script>
    jQuery(function($) {
        var idx = <?php echo count($timeline); ?>;
        $('#ft-vlog-timeline-repeater').on('click', '.ft-add-timeline-row', function() {
            var row = '<tr class="ft-timeline-row">' +
                '<td><input type="text" name="_ft_vlog_timeline[' + idx + '][time]" placeholder="3:45" class="widefat"></td>' +
                '<td><input type="text" name="_ft_vlog_timeline[' + idx + '][title]" placeholder="제목" class="widefat"></td>' +
                '<td><input type="text" name="_ft_vlog_timeline[' + idx + '][description]" placeholder="설명" class="widefat"></td>' +
                '<td><button type="button" class="button ft-remove-row">삭제</button></td></tr>';
            $('#ft-timeline-table tbody').append(row);
            idx++;
        });
        $('#ft-vlog-timeline-repeater').on('click', '.ft-remove-row', function() {
            $(this).closest('tr').remove();
        });
    });
    </script>
    <?php
}

/**
 * 영상 속 장소 메타박스 (리피터)
 */
function ft_render_vlog_spots_meta_box($post) {
    $spots = get_post_meta($post->ID, '_ft_vlog_spots', true);
    if (!is_array($spots)) $spots = [];
    ?>
    <div id="ft-vlog-spots-repeater">
        <table class="widefat" id="ft-spots-table">
            <thead>
                <tr>
                    <th style="width:180px;">장소명</th>
                    <th style="width:100px;">위도</th>
                    <th style="width:100px;">경도</th>
                    <th>설명</th>
                    <th style="width:50px;"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($spots as $i => $spot) : ?>
                <tr class="ft-spot-row">
                    <td><input type="text" name="_ft_vlog_spots[<?php echo $i; ?>][name]"
                               value="<?php echo esc_attr($spot['name'] ?? ''); ?>" placeholder="도톤보리" class="widefat"></td>
                    <td><input type="text" name="_ft_vlog_spots[<?php echo $i; ?>][lat]"
                               value="<?php echo esc_attr($spot['lat'] ?? ''); ?>" placeholder="34.67" class="widefat"></td>
                    <td><input type="text" name="_ft_vlog_spots[<?php echo $i; ?>][lng]"
                               value="<?php echo esc_attr($spot['lng'] ?? ''); ?>" placeholder="135.50" class="widefat"></td>
                    <td><input type="text" name="_ft_vlog_spots[<?php echo $i; ?>][description]"
                               value="<?php echo esc_attr($spot['description'] ?? ''); ?>" placeholder="설명" class="widefat"></td>
                    <td><button type="button" class="button ft-remove-spot">삭제</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><button type="button" class="button ft-add-spot-row">+ 장소 추가</button></p>
    </div>

    <script>
    jQuery(function($) {
        var idx = <?php echo count($spots); ?>;
        $('#ft-vlog-spots-repeater').on('click', '.ft-add-spot-row', function() {
            var row = '<tr class="ft-spot-row">' +
                '<td><input type="text" name="_ft_vlog_spots[' + idx + '][name]" placeholder="장소명" class="widefat"></td>' +
                '<td><input type="text" name="_ft_vlog_spots[' + idx + '][lat]" placeholder="위도" class="widefat"></td>' +
                '<td><input type="text" name="_ft_vlog_spots[' + idx + '][lng]" placeholder="경도" class="widefat"></td>' +
                '<td><input type="text" name="_ft_vlog_spots[' + idx + '][description]" placeholder="설명" class="widefat"></td>' +
                '<td><button type="button" class="button ft-remove-spot">삭제</button></td></tr>';
            $('#ft-spots-table tbody').append(row);
            idx++;
        });
        $('#ft-vlog-spots-repeater').on('click', '.ft-remove-spot', function() {
            $(this).closest('tr').remove();
        });
    });
    </script>
    <?php
}

/**
 * 메타 저장
 */
add_action('save_post_vlog_curation', function ($post_id) {
    if (!isset($_POST['ft_vlog_nonce_field']) || !wp_verify_nonce($_POST['ft_vlog_nonce_field'], 'ft_vlog_nonce')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // 텍스트 필드
    foreach (['_ft_vlog_youtube_id', '_ft_vlog_channel_name', '_ft_vlog_duration'] as $key) {
        if (isset($_POST[$key])) {
            update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
        }
    }

    // URL 필드
    if (isset($_POST['_ft_vlog_channel_url'])) {
        update_post_meta($post_id, '_ft_vlog_channel_url', esc_url_raw($_POST['_ft_vlog_channel_url']));
    }

    // 타임라인 (JSON array)
    if (isset($_POST['_ft_vlog_timeline']) && is_array($_POST['_ft_vlog_timeline'])) {
        $timeline = [];
        foreach ($_POST['_ft_vlog_timeline'] as $item) {
            if (empty($item['time']) && empty($item['title'])) continue;
            $timeline[] = [
                'time'        => sanitize_text_field($item['time'] ?? ''),
                'title'       => sanitize_text_field($item['title'] ?? ''),
                'description' => sanitize_text_field($item['description'] ?? ''),
            ];
        }
        update_post_meta($post_id, '_ft_vlog_timeline', $timeline);
    } else {
        update_post_meta($post_id, '_ft_vlog_timeline', []);
    }

    // 장소 (JSON array)
    if (isset($_POST['_ft_vlog_spots']) && is_array($_POST['_ft_vlog_spots'])) {
        $spots = [];
        foreach ($_POST['_ft_vlog_spots'] as $spot) {
            if (empty($spot['name'])) continue;
            $spots[] = [
                'name'        => sanitize_text_field($spot['name'] ?? ''),
                'lat'         => floatval($spot['lat'] ?? 0),
                'lng'         => floatval($spot['lng'] ?? 0),
                'description' => sanitize_text_field($spot['description'] ?? ''),
            ];
        }
        update_post_meta($post_id, '_ft_vlog_spots', $spots);
    } else {
        update_post_meta($post_id, '_ft_vlog_spots', []);
    }
});
