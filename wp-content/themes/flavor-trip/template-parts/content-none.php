<?php
/**
 * ê²°ê³¼ ?†ìŒ ?œí”Œë¦? *
 * @package TripTalk
 */

defined('ABSPATH') || exit;
?>

<div class="no-results">
    <h2><?php esc_html_e('ê²°ê³¼ê°€ ?†ìŠµ?ˆë‹¤', 'flavor-trip'); ?></h2>

    <?php if (is_search()) : ?>
        <p><?php esc_html_e('ê²€?‰ì–´?€ ?¼ì¹˜?˜ëŠ” ê²°ê³¼ê°€ ?†ìŠµ?ˆë‹¤. ?¤ë¥¸ ?¤ì›Œ?œë¡œ ?¤ì‹œ ê²€?‰í•´ë³´ì„¸??', 'flavor-trip'); ?></p>
        <?php get_search_form(); ?>
    <?php else : ?>
        <p><?php esc_html_e('?„ì§ ê²Œì‹œ??ì½˜í…ì¸ ê? ?†ìŠµ?ˆë‹¤.', 'flavor-trip'); ?></p>
    <?php endif; ?>
</div>
