<?php

class Themify_Builder_Component_Row extends Themify_Builder_Component_Base {

	public function get_name() {
		return 'row';
	}

	private function get_settings() {

		return apply_filters('themify_builder_row_fields_options', array(
			// Row Width
			array(
				'id' => 'row_width',
				'label' => __('Row Width', 'themify'),
				'type' => 'layout',
                                'mode' => 'sprite',
                                'options' => array(
                                    array('img' => 'row_default', 'value' => '', 'label' => __('Default', 'themify')),
                                    array('img' => 'row_fullwidth', 'value' => 'fullwidth', 'label' => __('Fullwidth Row Container', 'themify')),
                                    array('img' => 'row_fullwidth_content', 'value' => 'fullwidth-content', 'label' => __('Fullwidth Row Content', 'themify'))
                                ),
                                'render_callback'=>array(
                                    'binding'=>'live'
                                )
			),
			// Row Height
			array(
				'id' => 'row_height',
				'label' => __('Row Height', 'themify'),
				'type' => 'layout',
                                'mode' => 'sprite',
                                'options' => array(
                                    array('img' => 'row_default', 'value' => '', 'label' => __('Default', 'themify')),
                                    array('img' => 'row_fullheight', 'value' => 'fullheight', 'label' => __('Full Height (100% vh)', 'themify'))
                                ),
                                'render_callback'=>array(
                                    'binding'=>'live'
                                )
			),
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array('html' => '<hr/>')
			),
			array(
				'id' => 'custom_css_row',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'class' => 'large exclude-from-reset-field',
				'description' => sprintf('<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling (<a href="https://themify.me/docs/builder#additional-css-class" target="_blank">learn more</a>).', 'themify'))
			),
			array(
				'id' => 'row_anchor',
				'type' => 'text',
				'label' => __('Row Anchor', 'themify'),
				'class' => 'large exclude-from-reset-field',
				'description' => sprintf('<br/><small>%s</small>', __('Example: enter ‘about’ as row anchor and add ‘#about’ link in menu link. When the link is clicked, it will scroll to this row (<a href="https://themify.me/docs/builder#scrollto-row-anchor"  target="_blank">learn more</a>).', 'themify'))
			),
			array(
				'id' => 'custom_css_id',
				'type' => 'text',
				'label' => __('ID Name', 'themify'),
				'class' => 'large exclude-from-reset-field',
				'description' => sprintf('<br/><small>%s</small>', __('ID name should be unique (it is used to identify the element in Sticky Scrolling).', 'themify'))
			)
		));
	}
	private function get_form_settings() {
		$row_form_settings = array(
			'setting' => array(
				'name' => esc_html__( 'Row Options', 'themify' ),
				'options' => $this->get_settings()
			),
			'styling' => array(
				'name' => esc_html__( 'Styling', 'themify' ),
				'options' => $this->get_styling()
			)
		);
		return apply_filters( 'themify_builder_row_lightbox_form_settings', $row_form_settings );
	}

	protected function _form_template() { 
		$row_form_settings = $this->get_form_settings();
	?>
        <form id="tb_row_settings">
                <div id="tb_lightbox_options_tab_items">
                        <?php foreach( $row_form_settings as $setting_key => $setting ): ?>
                            <li <?php if($setting_key==='setting'):?>class="current"<?php endif;?>>
                                <a href="#tb_options_<?php  echo esc_attr( $setting_key ); ?>">
                                    <?php  echo $setting['name']; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                </div>
                <?php $this->get_save_btn( esc_html__( 'Done', 'themify' ) ); ?>	
                <?php foreach( $row_form_settings as $setting_key => $setting ): ?>
                    <div id="tb_options_<?php echo $setting_key; ?>" class="tb_options_tab_wrapper">
                            <?php 
                                if ( 'styling' === $setting_key ){
                                    self::get_breakpoint_switcher();
                                }
                            ?>
                            <div class="tb_options_tab_content">
                                    <?php 
                                    themify_render_styling_settings( $setting['options'] );
                                    if ('styling' === $setting_key):
                                    ?>
                                        <p>
                                            <a href="#" class="reset-styling">
                                                <i class="ti-close"></i>
                                                <?php _e('Reset Styling', 'themify') ?>
                                            </a>
                                        </p>
                                    <?php endif; ?>
                            </div>
                    </div>
                <?php endforeach; ?>
        </form>
	<?php
	}
        
	/**
	 * Get template row
	 *
	 * @param array  $rows
	 * @param array  $row
	 * @param string $builder_id
	 * @param bool   $echo
	 *
	 * @return string
	 */
	public static function template($rows, $row, $builder_id, $echo = false) {
		// prevent empty rows from being rendered
		$count = ! Themify_Builder::$frontedit_active  && isset($row['cols'])?count($row['cols']):0;
		if ( ($count===0 && ! isset( $row['styling'] ) )
			|| ($count=== 1 && empty( $row['cols'][0]['modules'] ) && empty( $row['cols'][0]['styling'] ) &&  empty( $row['styling'] ) ) // there's only one column and it's empty
		) {
			return '';
		}    
		if ( ! Themify_Builder::$frontedit_active ) {
			/* allow addons to control the display of the rows */
			$display = apply_filters('themify_builder_row_display', true, $row, $builder_id);
			if (false === $display || (isset($row['styling']['visibility_all']) && $row['styling']['visibility_all'] === 'hide_all' )) {
				return false;
			}
		}
		$row['row_order'] = isset($row['row_order']) ? $row['row_order'] : uniqid();
		$row_classes = array('themify_builder_row module_row','clearfix');
		$row_attributes = array();
		$is_styling = !empty($row['styling']);
		if($is_styling){
			// @backward-compatibility
			if (!isset($row['styling']['background_type']) && !empty($row['styling']['background_video'])) {
				$row['styling']['background_type'] = 'video';
			}
			elseif(isset($row['styling']['background_type']) && $row['styling']['background_type']==='image' && isset($row['styling']['background_zoom']) && $row['styling']['background_zoom']==='zoom' && $row['styling']['background_repeat']=='repeat-none'){
				$row_classes[] = 'themify-bg-zoom';
			}
			$class_fields = array('custom_css_row', 'background_repeat', 'animation_effect', 'row_height', 'hover_animation_effect');
			foreach ($class_fields as $field) {
				if (!empty($row['styling'][$field])) {
					if ('animation_effect' === $field && !Themify_Builder::$frontedit_active && Themify_Builder_Model::is_animation_active()) {
						$row_classes[] = 'wow';
						$row_classes[] = $row['styling']['animation_effect'];
					}
					elseif ('hover_animation_effect' === $field && !Themify_Builder::$frontedit_active && Themify_Builder_Model::is_animation_active()) {
						$row_classes[] = 'hover-wow hover-animation-' . $row['styling'][$field];
					} else {
						$row_classes[] = $row['styling'][$field];
					}
				}
			}
			if (!empty($row['styling']['animation_effect_delay'])) {
				$row_classes[] = 'animation_effect_delay_' . $row['styling']['animation_effect_delay'];
			}
			if (!empty($row['styling']['animation_effect_repeat'])) {
				$row_classes[] = 'animation_effect_repeat_' . $row['styling']['animation_effect_repeat'];
			}
			/**
			 * Row Width class
			 * To provide backward compatibility, the CSS classname and the option label do not match. See #5284
			 */

			if( isset( $row['styling']['row_width'] ) ) {
				if( 'fullwidth' === $row['styling']['row_width'] ) {
					$row_classes[] = 'fullwidth_row_container';
					
				} elseif( 'fullwidth-content' === $row['styling']['row_width'] ) {
					$row_classes[] = 'fullwidth';
				}
				$breakpoints = themify_get_breakpoints(null,true);
				$breakpoints['desktop'] = 1;
				$prop =  'fullwidth' === $row['styling']['row_width']?'padding':'margin'; 
				foreach($breakpoints as $k=>$v){
					$styles = $k==='desktop'?$row['styling']:(!empty($row['styling']['breakpoint_'.$k])?$row['styling']['breakpoint_'.$k]:false);
					if($styles){
						$val = self::getDataValue($styles,$prop);
						if($val){
							$row_attributes['data-'.$k.'-'.$prop] = $val; 
						}
					}
				}
			}
		}
		
		// background video
		$video_data = $is_styling && Themify_Builder_Model::is_premium()?Themify_Builder_Include::get_video_background($row['styling']):'';
		if ( ! $echo ) {
			$output = PHP_EOL; // add line break
			ob_start();
		}
                // Class for Scroll Highlight
                if ($is_styling && !empty($row['styling']['row_anchor'])) {
                        $row_classes[] = 'tb_section-' . $row['styling']['row_anchor'];
                        $row_attributes['data-anchor'] = $row['styling']['row_anchor'];
                }
                if(!Themify_Builder::$frontedit_active){
			$row_content_classes = array();
			$row_classes[] =   'module_row_' . $row['row_order'].' themify_builder_' . $builder_id . '_row module_row_' . $builder_id . '-' . $row['row_order'];
			// Set column alignment
			$row_content_classes[] = ! empty( $row['column_alignment'] )?$row['column_alignment']:(function_exists('themify_theme_is_fullpage_scroll') && themify_theme_is_fullpage_scroll()?'col_align_middle':'col_align_top');
			if (!empty($row['gutter']) && $row['gutter']!=='gutter-default') {
				$row_content_classes[] = $row['gutter'];
			}
			if($count>0){
				$row_content_attr = self::get_directions_data($row,$count);
				$order_classes = self::get_order($count);
				$is_phone = themify_is_touch('phone');
				$is_tablet = !$is_phone && themify_is_touch('tablet');
				$is_right = false;
				if($is_tablet){
					$is_right = isset($row_content_attr['data-tablet_dir']) || isset($row_content_attr['data-tablet_landscape_dir']);
					if(isset($row_content_attr['data-col_tablet']) || isset($row_content_attr['data-col_tablet_landscape'])){
						$row_content_classes[] = isset($row_content_attr['data-col_tablet_landscape'])?$row_content_attr['data-col_tablet_landscape']:$row_content_attr['data-col_tablet'];
					}
				}
				elseif($is_phone){
					$is_right = isset($row_content_attr['data-mobile_dir']);
					if(isset($row_content_attr['data-col_mobile'])){
						$row_content_classes[] = $row_content_attr['data-col_mobile'];
					}
				}
				else{
					$is_right = isset($row_content_attr['data-desktop_dir']);
				}
				if($is_right){
					$row_content_classes[] = 'direction-rtl';
					$order_classes = array_reverse($order_classes);
				}
			}
			$row_content_classes = implode(' ',$row_content_classes);
		}
		$row_classes = apply_filters('themify_builder_row_classes', $row_classes, $row, $builder_id);
		$row_attributes['class'] = implode(' ', $row_classes);
		$row_attributes = apply_filters( 'themify_builder_row_attributes', $row_attributes, $is_styling ? $row['styling'] : array(),$builder_id );
                
        if  ( isset( $row['element_id'] ) ) 
        	$row_attributes['data-id'] = $row['element_id'];
		
		do_action('themify_builder_row_start', $builder_id, $row,$row['row_order']);
		
		echo (strpos($row_attributes['class'],'tb-page-break') !== false)?'<!-- tb_page_break -->':''; ?>

		<!-- module_row -->
		<div <?php echo $video_data,self::get_element_attributes( $row_attributes ); ?>>
			<?php
                        if ($is_styling) {
				do_action('themify_builder_background_styling',$builder_id,$row,$row['row_order'],'row');	
                                self::show_frame($row['styling']);
			}
			?>
			<div class="row_inner<?php if(!Themify_Builder::$frontedit_active):?> <?php echo $row_content_classes?><?php endif;?>" <?php if(!empty($row_content_attr)):?> <?php echo self::get_element_attributes( $row_content_attr );?><?php endif;?>>
                            <?php   if ($count > 0){
					foreach ($row['cols'] as $cols => $col){
						Themify_Builder_Component_Column::template( $rows, $row, $cols, $col, $builder_id, $order_classes, true );
					}
				}

                            ?>
                                </div>
                                <!-- /row_inner -->
                        </div>
                        <!-- /module_row -->
		<?php
                do_action('themify_builder_row_end', $builder_id, $row,$row['row_order']);
		if ( ! $echo ) {
			$output .= ob_get_clean();
			// add line break
			$output .= PHP_EOL;
			return $output;
		}
	}

	private static function getDataValue($styles,$type='padding'){
		$value = '';
		if (!empty($styles['checkbox_'.$type.'_apply_all']) && !empty($styles[$type.'_top'])) {
			$value = $styles[$type.'_top'];
			$value.= isset($styles[$type.'_top_unit']) ? $styles[$type.'_top_unit'] : 'px';
			$value = $value . ',' . $value;

		} elseif (!empty($styles[$type.'_left']) || !empty($styles[$type.'_right'])) {
			if (!empty($styles[$type.'_left'])) {
				$value = $styles[$type.'_left'];
				$value.= isset($styles[$type.'_left_unit']) ? $styles[$type.'_left_unit'] : 'px';
			}
			$value.=',';
			if (!empty($styles[$type.'_right'])) {
				$value.= $styles[$type.'_right'];
				$value.= isset($styles[$type.'_right_unit']) ? $styles[$type.'_right_unit'] : 'px';
			}
		}
		return $value;
	}
}