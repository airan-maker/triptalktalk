<?php
/**
 * 사진 갤러리
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;

$gallery_ids = get_query_var('ft_gallery_ids', []);

if (empty($gallery_ids)) return;
?>

<section class="itinerary-gallery" id="photo-gallery">
    <h2 class="section-heading"><?php esc_html_e('포토 갤러리', 'flavor-trip'); ?></h2>
    <div class="gallery-grid">
        <?php foreach ($gallery_ids as $id) :
            $full  = wp_get_attachment_image_url($id, 'large');
            $thumb = wp_get_attachment_image_url($id, 'ft-gallery');
            $alt   = get_post_meta($id, '_wp_attachment_image_alt', true);
            $caption = wp_get_attachment_caption($id);
            if (!$full) continue;
        ?>
            <a href="<?php echo esc_url($full); ?>"
               class="gallery-item"
               data-caption="<?php echo esc_attr($caption); ?>"
               aria-label="<?php echo esc_attr($alt ?: __('갤러리 이미지', 'flavor-trip')); ?>">
                <img src="<?php echo esc_url($thumb); ?>"
                     alt="<?php echo esc_attr($alt); ?>"
                     loading="lazy"
                     width="800"
                     height="600">
            </a>
        <?php endforeach; ?>
    </div>
</section>
