<?php
/**
 * 검색 폼
 *
 * @package Flavor_Trip
 */

defined('ABSPATH') || exit;
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label class="screen-reader-text" for="search-field"><?php esc_html_e('검색:', 'flavor-trip'); ?></label>
    <input type="search" id="search-field" class="search-field" placeholder="<?php esc_attr_e('여행지, 일정 검색...', 'flavor-trip'); ?>" value="<?php echo get_search_query(); ?>" name="s">
    <button type="submit" class="search-submit" aria-label="<?php esc_attr_e('검색', 'flavor-trip'); ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
    </button>
</form>
