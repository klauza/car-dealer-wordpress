<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Page Break
 *
 * Access original fields: $mod_settings
 * @author Themify
 */
if (TFCache::start_cache($mod_name, self::$post_id, array('ID' => $module_ID))):
    ?>
    <!-- module page break -->
    <div class="tb-page-break-pagination">
    </div>
    <!-- /module page break -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>
