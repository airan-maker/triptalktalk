<?php
/**
 * Ïª§Ïä§?Ä Î©îÌ?Î∞ïÏä§ + JS Î¶¨Ìîº?? *
 * @package TripTalk
 */

defined('ABSPATH') || exit;

/**
 * Î©îÌ?Î∞ïÏä§ ?±Î°ù
 */
add_action('add_meta_boxes', function () {
    add_meta_box(
        'ft_itinerary_details',
        __('?¨Ìñâ ?ºÏ†ï ?ÅÏÑ∏ ?ïÎ≥¥', 'flavor-trip'),
        'ft_render_itinerary_meta_box',
        'travel_itinerary',
        'normal',
        'high'
    );

    add_meta_box(
        'ft_itinerary_days',
        __('?ºÏûêÎ≥??ºÏ†ï', 'flavor-trip'),
        'ft_render_days_meta_box',
        'travel_itinerary',
        'normal',
        'high'
    );

    add_meta_box(
        'ft_itinerary_gallery',
        __('?¨ÌÜ† Í∞§Îü¨Î¶?, 'flavor-trip'),
        'ft_render_gallery_meta_box',
        'travel_itinerary',
        'side',
        'default'
    );

    add_meta_box(
        'ft_itinerary_map',
        __('ÏßÄ??Ï¢åÌëú', 'flavor-trip'),
        'ft_render_map_meta_box',
        'travel_itinerary',
        'side',
        'default'
    );
});

/**
 * ?¨Ìñâ ?ºÏ†ï ?ÅÏÑ∏ ?ïÎ≥¥ Î©îÌ?Î∞ïÏä§ ?åÎçîÎß? */
function ft_render_itinerary_meta_box($post) {
    wp_nonce_field('ft_itinerary_nonce', 'ft_itinerary_nonce_field');

    $fields = [
        '_ft_destination_name' => ['label' => 'Î™©Ï†ÅÏßÄ', 'type' => 'text', 'placeholder' => '?? ?ÑÏøÑ, ?ºÎ≥∏'],
        '_ft_duration'         => ['label' => '?¨Ìñâ Í∏∞Í∞Ñ', 'type' => 'text', 'placeholder' => '?? 3Î∞?4??],
        '_ft_price_range'      => ['label' => 'Í∞ÄÍ≤©Î?', 'type' => 'select', 'options' => ['' => '?†ÌÉù', 'budget' => '?í∞ Í∞Ä?±ÎπÑ', 'moderate' => '?í∞?í∞ Î≥¥ÌÜµ', 'premium' => '?í∞?í∞?í∞ ?ÑÎ¶¨ÎØ∏ÏóÑ', 'luxury' => '?í∞?í∞?í∞?í∞ ??ÖîÎ¶?]],
        '_ft_difficulty'       => ['label' => '?úÏù¥??, 'type' => 'select', 'options' => ['' => '?†ÌÉù', 'easy' => '?¨Ï?', 'moderate' => 'Î≥¥ÌÜµ', 'hard' => '?¥Î†§?Ä']],
        '_ft_best_season'      => ['label' => 'Ï∂îÏ≤ú ?úÍ∏∞', 'type' => 'text', 'placeholder' => '?? 3??5?? 9??11??],
        '_ft_highlights'       => ['label' => '?òÏù¥?ºÏù¥??, 'type' => 'textarea', 'placeholder' => '?ºÌëúÎ°?Íµ¨Î∂Ñ (?? ?†Ï£ºÏø??ºÍ≤Ω, Ï∏†ÌÇ§ÏßÄ ?úÏû•, ?ÑÏÇ¨Ïø†ÏÇ¨ ?¨Ïõê)'],
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
 * ?ºÏûêÎ≥??ºÏ†ï Î¶¨Ìîº???åÎçîÎß? */
function ft_render_days_meta_box($post) {
    $days = get_post_meta($post->ID, '_ft_days', true);
    if (!is_array($days)) {
        $days = [];
    }
    ?>
    <div id="ft-days-repeater">
        <div id="ft-days-list">
            <?php if (empty($days)) : ?>
                <p class="ft-no-days"><?php esc_html_e('?ÑÎûò Î≤ÑÌäº???¥Î¶≠?òÏó¨ ?ºÏ†ï??Ï∂îÍ??òÏÑ∏??', 'flavor-trip'); ?></p>
            <?php else : ?>
                <?php foreach ($days as $i => $day) : ?>
                    <div class="ft-day-item" data-index="<?php echo esc_attr($i); ?>">
                        <div class="ft-day-header">
                            <strong>Day <?php echo esc_html($i + 1); ?></strong>
                            <button type="button" class="ft-remove-day button-link-delete">&times;</button>
                        </div>
                        <p>
                            <label>?úÎ™©</label>
                            <input type="text" name="_ft_days[<?php echo esc_attr($i); ?>][title]" value="<?php echo esc_attr($day['title'] ?? ''); ?>" class="widefat" placeholder="?? ?ÑÏøÑ ?ÑÏ∞© & ?úÎ????êÌóò">
                        </p>
                        <p>
                            <label>?ÅÏÑ∏ ?¥Ïö©</label>
                            <textarea name="_ft_days[<?php echo esc_attr($i); ?>][description]" rows="4" class="widefat" placeholder="???†Ïùò ?ºÏ†ï???ÅÏÑ∏???ëÏÑ±?òÏÑ∏??"><?php echo esc_textarea($day['description'] ?? ''); ?></textarea>
                        </p>
                        <p>
                            <label>Ï£ºÏöî ?•ÏÜå</label>
                            <input type="text" name="_ft_days[<?php echo esc_attr($i); ?>][places]" value="<?php echo esc_attr($day['places'] ?? ''); ?>" class="widefat" placeholder="?ºÌëúÎ°?Íµ¨Î∂Ñ (?? ?úÎ????§ÌÅ¨?®Î∏î, ?òÎùºÏ£ºÏø†, Î©îÏù¥ÏßÄ ?†Í∂Å)">
                        </p>
                        <p>
                            <label>??/label>
                            <input type="text" name="_ft_days[<?php echo esc_attr($i); ?>][tip]" value="<?php echo esc_attr($day['tip'] ?? ''); ?>" class="widefat" placeholder="?¨Ìñâ ?ÅÏùÑ ?ÖÎ†•?òÏÑ∏??">
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" id="ft-add-day" class="button button-primary"><?php esc_html_e('+ ?ºÏ†ï Ï∂îÍ?', 'flavor-trip'); ?></button>
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
                '<p><label>?úÎ™©</label><input type="text" name="_ft_days[' + index + '][title]" class="widefat" placeholder="?? ?ÑÏøÑ ?ÑÏ∞© & ?úÎ????êÌóò"></p>' +
                '<p><label>?ÅÏÑ∏ ?¥Ïö©</label><textarea name="_ft_days[' + index + '][description]" rows="4" class="widefat" placeholder="???†Ïùò ?ºÏ†ï???ÅÏÑ∏???ëÏÑ±?òÏÑ∏??"></textarea></p>' +
                '<p><label>Ï£ºÏöî ?•ÏÜå</label><input type="text" name="_ft_days[' + index + '][places]" class="widefat" placeholder="?ºÌëúÎ°?Íµ¨Î∂Ñ"></p>' +
                '<p><label>??/label><input type="text" name="_ft_days[' + index + '][tip]" class="widefat" placeholder="?¨Ìñâ ?ÅÏùÑ ?ÖÎ†•?òÏÑ∏??"></p>';
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
 * Í∞§Îü¨Î¶?Î©îÌ?Î∞ïÏä§ ?åÎçîÎß? */
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
        <button type="button" id="ft-gallery-add" class="button"><?php esc_html_e('?¥Î?ÏßÄ ?†ÌÉù', 'flavor-trip'); ?></button>
    </div>

    <script>
    (function() {
        var frame;
        document.getElementById('ft-gallery-add').addEventListener('click', function(e) {
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({
                title: 'Í∞§Îü¨Î¶??¥Î?ÏßÄ ?†ÌÉù',
                button: { text: '?†ÌÉù' },
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
 * ÏßÄ??Ï¢åÌëú Î©îÌ?Î∞ïÏä§ ?åÎçîÎß? */
function ft_render_map_meta_box($post) {
    $lat = get_post_meta($post->ID, '_ft_map_lat', true);
    $lng = get_post_meta($post->ID, '_ft_map_lng', true);
    $zoom = get_post_meta($post->ID, '_ft_map_zoom', true) ?: '12';
    ?>
    <p>
        <label for="_ft_map_lat"><?php esc_html_e('?ÑÎèÑ (Latitude)', 'flavor-trip'); ?></label>
        <input type="text" id="_ft_map_lat" name="_ft_map_lat" value="<?php echo esc_attr($lat); ?>" class="widefat" placeholder="?? 35.6762">
    </p>
    <p>
        <label for="_ft_map_lng"><?php esc_html_e('Í≤ΩÎèÑ (Longitude)', 'flavor-trip'); ?></label>
        <input type="text" id="_ft_map_lng" name="_ft_map_lng" value="<?php echo esc_attr($lng); ?>" class="widefat" placeholder="?? 139.6503">
    </p>
    <p>
        <label for="_ft_map_zoom"><?php esc_html_e('Ï§??àÎ≤® (1~18)', 'flavor-trip'); ?></label>
        <input type="number" id="_ft_map_zoom" name="_ft_map_zoom" value="<?php echo esc_attr($zoom); ?>" class="widefat" min="1" max="18">
    </p>
    <?php
}

/**
 * Î©îÌ? ?∞Ïù¥???Ä?? */
add_action('save_post_travel_itinerary', function ($post_id) {
    if (!isset($_POST['ft_itinerary_nonce_field']) || !wp_verify_nonce($_POST['ft_itinerary_nonce_field'], 'ft_itinerary_nonce')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // ?®Ïùº ?ÑÎìú
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

    // ÏßÄ??Ï¢åÌëú
    if (isset($_POST['_ft_map_lat'])) {
        update_post_meta($post_id, '_ft_map_lat', sanitize_text_field($_POST['_ft_map_lat']));
    }
    if (isset($_POST['_ft_map_lng'])) {
        update_post_meta($post_id, '_ft_map_lng', sanitize_text_field($_POST['_ft_map_lng']));
    }
    if (isset($_POST['_ft_map_zoom'])) {
        update_post_meta($post_id, '_ft_map_zoom', absint($_POST['_ft_map_zoom']));
    }

    // Í∞§Îü¨Î¶?    if (isset($_POST['_ft_gallery'])) {
        $gallery = sanitize_text_field($_POST['_ft_gallery']);
        $ids = $gallery ? array_map('absint', explode(',', $gallery)) : [];
        update_post_meta($post_id, '_ft_gallery', $ids);
    }

    // ?ºÏûêÎ≥??ºÏ†ï Î¶¨Ìîº??    if (isset($_POST['_ft_days']) && is_array($_POST['_ft_days'])) {
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

// ÎØ∏Îîî???ÖÎ°ú???§ÌÅ¨Î¶ΩÌä∏ Î°úÎìú
add_action('admin_enqueue_scripts', function ($hook) {
    global $post_type;
    if ($post_type === 'travel_itinerary' && in_array($hook, ['post.php', 'post-new.php'])) {
        wp_enqueue_media();
    }
});
