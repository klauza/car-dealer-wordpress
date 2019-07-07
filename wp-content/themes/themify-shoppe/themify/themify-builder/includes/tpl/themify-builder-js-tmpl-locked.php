<div id="tmpl-builder-restriction"<?php if ($takeover): ?> class="tb_istakeover"<?php endif; ?>>
    <div class="tb_locked_popup">
        <div class="tb_locked_avatar">
            <?php echo get_avatar($this->restriction_id, 64, 'mystery') ?>
        </div>
        <div class="tb_locked_content">
            <div class="tb_locked_info">
                <?php
                $string = $takeover ? __('<strong>%s</strong> has taken over and is currently editing.<br/> Your latest changes were saved as a revision', 'themify') : __('<strong>%s</strong> is already editing this Builder. Do you want to take over?', 'themify');
                printf($string, $data->display_name);
                if ($takeover) {
                    $current_user = wp_get_current_user();
                    echo '(<strong class="tb_locked_revision">' . $current_user->user_login . '_' . date('Y-m-d-H-i-s') . '</strong></i>)';
                }
                if (!isset($id)) {
                    $id = null;
                }
                ?>
            </div>
            <div class="tb_locked_takeover">
                <a class="tb_button" href="<?php echo admin_url('edit.php?post_type=' . get_post_type($id)) ?>"><?php _e('All Pages', 'themify') ?></a>
                <?php if (!$takeover): ?>
                    <button class="tb_button tb_locked_btn"><?php _e('Take over', 'themify'); ?></button>
                <?php endif; ?>
            </div>
        </div>
        <?php if (!$takeover): ?>
            <span class="tb_locked_close">X</span>
        <?php endif; ?>
    </div>
</div>