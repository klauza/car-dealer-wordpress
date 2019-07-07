<?php
/**
 * @package    Themify_Builder
 * @subpackage Themify_Builder/classes
 */

class Themify_Builder_Include extends Themify_Builder_Component_Base{


	/**
	 * Constructor.
	 * 
	 * @param object Themify_Builder $builder 
	 */
	public function __construct(Themify_Builder $builder) {
			$is_frontend = Themify_Builder_Model::is_frontend_editor_page();
			$is_premium = Themify_Builder_Model::is_premium();
			if($is_premium || $is_frontend){
				include( THEMIFY_BUILDER_CLASSES_DIR . '/premium/class-themify-builder-visibility-controls.php' );
				// Visibility controls
				new Themify_Builder_Visibility_Controls();
			}
			//Only For premium version
			if($is_premium){
				if($is_frontend && file_exists(THEMIFY_BUILDER_CLASSES_DIR . '/premium/class-themify-builder-revisions.php')){
					include THEMIFY_BUILDER_CLASSES_DIR . '/premium/class-themify-builder-revisions.php';
					// Themify Builder Revisions
					new Themify_Builder_Revisions($builder);
				}
				add_filter( 'themify_builder_module_container_props', array( $this, 'parallax_props' ), 10, 4 );
				add_filter( 'themify_builder_row_attributes', array( $this, 'parallax_props' ), 10, 2 );
				add_filter( 'themify_builder_subrow_attributes', array( $this, 'parallax_props' ), 10, 2 );
				add_action('themify_builder_background_styling',array($this,'background_styling'),10,4);
				add_action('themify_builder_background_slider',array($this,'do_slider_background'),10,3);
			}
			 // Hover Animation
			add_filter( 'themify_builder_animation_settings_fields', array( $this, 'hover_elements_fields' ), 10 );
			// Parallax Element Scrolling - Module
			add_filter( 'themify_builder_animation_settings_fields', array( $this, 'parallax_elements_fields' ), 10 );
			add_filter('themify_builder_row_lightbox_form_settings',array($this,'row_animation'),10,1);
			add_filter('themify_builder_row_fields_styling',array($this,'row_styling_fields'),10,1);
			add_filter('themify_builder_subrow_fields_styling',array($this,'subrow_styling_fields'),10,1);
			add_filter('themify_builder_column_fields_styling',array($this,'column_styling_fields'),10,1);

			// Sub row animation
			add_filter('themify_builder_subrow_lightbox_form_settings', array($this,'row_animation'),10,1);
			
			add_filter( 'themify_builder_animation_settings_fields', array( $this, 'sticky_elements_fields' ), 10 );
			// custom Data ID attributes
			add_filter( 'themify_builder_module_container_props', array( $this, 'add_id_attributes' ), 10, 4 );
			add_filter( 'themify_builder_module_container_props', array( $this, 'sticky_element_props' ), 10, 2 );
			add_filter( 'themify_builder_row_attributes', array( $this, 'sticky_element_props' ), 10, 2 );
			add_filter( 'themify_builder_subrow_attributes', array( $this, 'sticky_element_props' ), 10, 2 );
			add_filter( 'themify_builder_module_settings_fields', array( $this, 'add_css_id_field' ) );
			add_filter( 'themify_builder_subrow_fields_styling', array( $this, 'add_css_id_field' ) );
	}

	/**
	 * Add custom ID attributes
	 * @param array $props 
	 * @param array $fields_args 
	 * @param string $mod_name 
	 * @param string $module_ID 
	 * @return array
	 */
	public function add_id_attributes( $props, $fields_args, $mod_name=false, $module_ID=false ) {
		if ( isset( $fields_args['element_id'] ) ) 
			$props['data-id'] = $fields_args['element_id'];

		return $props;	
	}

	/**
	 * Add custom css ID
	 * 
	 * @access public
	 * @param array $settings 
	 * @param object $this 
	 * @return array
	 */
	public function add_css_id_field( $settings ) {
		$settings[] = array(
			'id' => 'custom_css_id',
			'type' => 'text',
			'label' => __('ID Name', 'themify'),
			'help' => sprintf( '<br/><small>%s</small>', __('ID name should be unique (it is used to identify the element in Sticky Scrolling).', 'themify') ),
			'description' => sprintf( '<br/><small>%s</small>', __('ID name should be unique (it is used to identify the element in Sticky Scrolling).', 'themify') ),
			'class' => 'large exclude-from-reset-field',
			'wrap_with_class' => 'custom_css_subrow'
		);
		return $settings;
	}

	/**
	 * Add module hover animation fields to Animation Tab of module settings.
	 *
	 * @access public
	 * @param array $fields
	 * @return array
	 */
	public function hover_elements_fields( $fields ) {
		$new_fields = array(
			array(
				'id' => 'separator_hover',
				'type' => 'separator',
				'meta' => array('html'=>'<hr><h4>'.__('Hover Animation', 'themify').'</h4>'),
			),
			array(
				'id' => 'hover_animation Effect',
				'type' => 'multi',
				'label' => __('Effect', 'themify'),
				'fields' => array(
					array(
						'id' => 'hover_animation_effect',
						'type' => 'animation_select'
					)
				)
			),
		);
				
		return array_merge( $fields, $new_fields );
	}
		
	/**
	 * Add module Float Scrolling fields to Styling Tab module settings.
	 * 
	 * @access public
	 * @param array $fields 
	 * @return array
	 */
	public function parallax_elements_fields( $fields ) {
				$is_premium = Themify_Builder_Model::is_premium();
		$new_fields = array(
			array(
				'id' => 'separator_parallax',
				'type' => 'separator',
				'meta' => array('html'=>'<hr><h4>'.__('Float Scrolling', 'themify').'</h4>'),
			),
			array(
				'id' => 'custom_parallax_scroll_speed',
				'type' => 'select',
				'label' => __( 'Scroll Speed', 'themify' ),
				'meta'  => array(
					array('value' => '',   'name' => '', 'selected' => true),
					array('value' => 1,   'name' => 1),
					array('value' => 2, 'name' => 2),
					array('value' => 3,  'name' => 3),
					array('value' => 4,  'name' => 4),
					array('value' => 5,   'name' => 5),
					array('value' => 6, 'name' => 6),
					array('value' => 7,  'name' => 7),
					array('value' => 8,  'name' => 8),
					array('value' => 9,  'name' => 9),
					array('value' => 10,  'name' => 10)
				),
				'description' => sprintf( '<small>%s <br>%s</small>', esc_html__( '1 = slow, 10 = very fast', 'themify' ), esc_html__( 'Produce floating effect by selecting different scroll speed', 'themify' ) ),
				'binding' => array(
					'empty' => array(
						'hide' => array('custom_parallax_scroll_reverse', 'custom_parallax_scroll_fade')
					),
					'not_empty' => array(
						'show' => array('custom_parallax_scroll_reverse', 'custom_parallax_scroll_fade')
					)
				),
				'wrap_with_class' => !$is_premium?'':'tb_parrallax',
								'is_premium'=>$is_premium
			),
			array(
				'id' => 'custom_parallax_scroll_reverse',
				'type' => 'checkbox',
				'label' => '',
				'options' => array(
					array( 'name' => 'reverse', 'value' => __('Reverse scrolling', 'themify')),
				),
				'wrap_with_class' => !$is_premium?'':'custom_parallax_scroll_reverse tb_parrallax',
								'is_premium'=>$is_premium
			),
			array(
				'id' => 'custom_parallax_scroll_fade',
				'type' => 'checkbox',
				'label' => '',
				'options' => array(
					array( 'name' => 'fade', 'value' => __('Fade off as it scrolls', 'themify')),
				),
				'wrap_with_class' => !$is_premium?'':'custom_parallax_scroll_fade tb_parrallax',
								'is_premium'=>$is_premium
			)
						
		);
		return array_merge( $fields, $new_fields );
	}

	public function sticky_elements_fields( $fields ) {
		$new_fields = array(
			array(
				'id' => 'separator_parallax',
				'type' => 'separator',
				'meta' => array('html'=>'<hr><h4>'.__('Sticky Scrolling', 'themify').'</h4>'),
			),
			array(
				'id' => 'stick_at_wrapper',
				'type' => 'multi',
				'fields' => array(
					array(
						'id' => 'stick_at_check',
						'type' => 'checkbox',
						'options' => array(
							array( 'name' => 'stick_at_check', 'value' => __('Stick at', 'themify')),
						),
						'binding' => array(
							'not_checked' => array(
								'hide' => array( 'unstick_when_wrapper' )
							),
							'checked' => array(
								'show' => array( 'unstick_when_wrapper' )
							)
						)
					),
					array(
						'id' => 'stick_at_position',
						'type' => 'select',
						'meta'  => array(
							array('value' => 'top', 'name' => esc_html__( 'Top Position', 'themify' ) ),
							array('value' => 'bottom', 'name' => esc_html__( 'Bottom Position', 'themify' ) )
						)
					),
					array(
						'id' => 'stick_at_pos_val',
						'type' => 'range',
						'units' => array(
							'px' => array(
								'min' => 0
							),
							'%' => array(
								'min' => 0,
								'max' => 100,
							)
						)
					)
				)
			),
			array(
				'id' => 'unstick_when_wrapper',
				'type' => 'multi',
				'fields' => array(
					array(
						'id' => 'unstick_when_check',
						'type' => 'checkbox',
						'options' => array(
							array( 'name' => 'unstick_when_check', 'value' => esc_html__( 'Un-stick when', 'themify' ) )
						)
					),
					array(
						'id' => 'unstick_when_element',
						'type' => 'select',
						'meta' => array(
							array( 'name' => esc_html__( 'Builder Content End', 'themify' ), 'value' => 'builder_end' ),
							array( 'name' => esc_html__( 'Row', 'themify' ), 'value' => 'row' ),
							array( 'name' => esc_html__( 'Module', 'themify' ), 'value' => 'module' ),
						),
						'binding' => array(
							'builder_end' => array(
								'hide' => array( 'unstick_wrapper_extend_option', 'unstick_when_el_row_select', 'unstick_when_el_module_select' )
							),
							'row' => array(
								'show' => array( 'unstick_wrapper_extend_option', 'unstick_when_el_row_select' ),
								'hide' => array( 'unstick_when_el_module_select' )
							),
							'module' => array(
								'show' => array( 'unstick_wrapper_extend_option', 'unstick_when_el_module_select' ),
								'hide' => array( 'unstick_when_el_row_select' )
							),
							'select'=>array(
								'value' => 'builder_end',
								'hide' => array( 'unstick_wrapper_extend_option', 'unstick_when_el_row_select', 'unstick_when_el_module_select' )
							),
						)
					),
					array(
						'id' => 'unstick_when_el_row_id',
						'type' => 'select',
						'wrap_with_class' => 'unstick_when_el_row_select',
						'class' => 'unstick_el_row_select_input',
						'meta' => array(
							array( 'name' => 'row', 'value' => 'row' )
						)
					),
					array(
						'id' => 'unstick_when_el_mod_id',
						'type' => 'select',
						'wrap_with_class' => 'unstick_when_el_module_select',
						'class' => 'unstick_el_module_select_input',
						'meta' => array(
							array( 'name' => 'module', 'value' => 'module' )
						)
					),
					array(
						'id' => 'unstick_when_condition',
						'type' => 'select',
						'wrap_with_class' => 'unstick_wrapper_extend_option',
						'meta' => array(
							array( 'name' => esc_html__( 'Hits', 'themify' ), 'value' => 'hits' ),
							array( 'name' => esc_html__( 'Passes', 'themify' ), 'value' => 'passes' )
						)
					),
					array(
						'id' => 'unstick_when_pos',
						'type' => 'select',
						'wrap_with_class' => 'unstick_wrapper_extend_option',
						'meta' => array(
							array( 'name' => esc_html__( 'This element', 'themify' ), 'value' => 'this' ),
							array( 'name' => esc_html__( 'Top Position', 'themify' ), 'value' => 'top' ),
							array( 'name' => esc_html__( 'Bottom Position', 'themify' ), 'value' => 'bottom' )
						),
						'binding' => array(
							'this' => array(
								'hide' => array( 'unstick_wrapper_position_unit .tb_input' )
							),
							'top' => array(
								'show' => array( 'unstick_wrapper_position_unit .tb_input' )
							),
							'bottom' => array(
								'show' => array( 'unstick_wrapper_position_unit .tb_input' )
							),
							'select' => array(
								'value' => 'this',
								'hide' => array( 'unstick_wrapper_position_unit .tb_input' )
							),
						)
					),
					array(
						'id' => 'unstick_when_pos_val',
						'type' => 'range',
						'wrap_with_class' => 'unstick_wrapper_position_unit unstick_wrapper_extend_option',
						'units' => array(
							'px' => array(
								'min' => 0
							),
							'%' => array(
								'min' => 0,
								'max' => 100,
							)
						)
					)
				)
			),
			array(
				'id' => 'separator_note_sticky',
				'type' => 'separator',
				'meta' => array('html'=>'<small>'.__('NOTE: Sticky Scrolling can not be combined with Float Scrolling, thus it will be disabled.', 'themify').'</small>'),
			),
			array(
				'id' => 'separator_parallax',
				'type' => 'separator',
				'meta' => array('html'=>'<hr><h4>'.__('Z-Index', 'themify').'</h4>'),
			),
			array(
				'id' => 'custom_parallax_scroll_zindex',
				'type' => 'text',
				'label' => __( 'Z-Index', 'themify' ),
				'class' => 'xsmall',
				'description' => sprintf( '%s <br>%s', esc_html__( 'Stack Order', 'themify' ), esc_html__( 'Module with greater stack order is always in front of an module with a lower stack order', 'themify' ) )
			)
		);
		return array_merge( $fields, $new_fields );
	}

	/**
	 * Sticky Element props attributes
	 * @param array $props 
	 * @param array $fields_args 
	 * @param string $mod_name 
	 * @param string $module_ID 
	 * @return array
	 */
	public function sticky_element_props( $props, $fields_args, $mod_name=false, $module_ID=false ) {
		
		if ( isset( $fields_args['stick_at_check'] ) && '' !== $fields_args['stick_at_check'] ) {
			$settings = array();
			
			if ( ! isset( $fields_args['stick_at_pos_val'] ) ) 
				$fields_args['stick_at_pos_val'] = '';

			if ( ! isset( $fields_args['stick_at_pos_val_unit'] ) ) 
				$fields_args['stick_at_pos_val_unit'] = 'px';
			
			if ( ! isset( $fields_args['unstick_when_pos_val'] ) ) 
				$fields_args['unstick_when_pos_val'] = '';

			if ( ! isset( $fields_args['unstick_when_pos_val_unit'] ) ) 
				$fields_args['unstick_when_pos_val_unit'] = 'px';

			$settings['stick'] = array(
				'position' => $fields_args['stick_at_position'],
				'value' => $fields_args['stick_at_pos_val'],
				'val_unit' => $fields_args['stick_at_pos_val_unit']
			);

			if ( isset( $fields_args['unstick_when_check'] ) && '' !== $fields_args['unstick_when_check'] ) {
				$settings['unstick'] = array(
					'el_type' => $fields_args['unstick_when_element'],
					'el_row_target' => $fields_args['unstick_when_el_row_id'],
					'el_mod_target' => $fields_args['unstick_when_el_mod_id'],
					'rule' => $fields_args['unstick_when_condition'],
					'current' => $fields_args['unstick_when_pos'],
					'value' => $fields_args['unstick_when_pos_val'],
					'val_unit' => $fields_args['unstick_when_pos_val_unit']
				);
			}

			$props['data-sticky-active'] = wp_json_encode( $settings );
		}

		if ( isset( $fields_args['custom_css_id'] ) ) 
			$props['id'] = $fields_args['custom_css_id'];

		return $props;
	}

	/**
	 * Add custom attributes html5 data to module container div to show parallax options.
	 * 
	 * @access public
	 * @param array $props 
	 * @param array $fields_args 
	 * @param string $mod_name 
	 * @param string $module_ID 
	 * @return array
	 */
	public function parallax_props( $props, $fields_args, $mod_name=false, $module_ID=false ) {
		$sticky_active = isset( $fields_args['stick_at_check'] ) && '' !== $fields_args['stick_at_check'];

		if (!Themify_Builder::$frontedit_active && Themify_Builder_Model::is_parallax_active() && !empty( $fields_args['custom_parallax_scroll_speed'] ) && ! $sticky_active ){
			$props['data-parallax-element-speed'] = $fields_args['custom_parallax_scroll_speed'];
			if ( !empty( $fields_args['custom_parallax_scroll_reverse'] ) && $fields_args['custom_parallax_scroll_reverse']!=='|' ){
				$props['data-parallax-element-reverse'] =1;
			}
			if ( !empty( $fields_args['custom_parallax_scroll_fade'] ) && $fields_args['custom_parallax_scroll_fade']!=='|'){
				$props[ 'data-parallax-fade'] = 1;
			}
		}
		if ( isset( $fields_args['custom_parallax_scroll_zindex'] ) && $fields_args['custom_parallax_scroll_zindex']!=='') {
			$zIndex = 'z-index:'.(int)$fields_args['custom_parallax_scroll_zindex'].';';
			if(isset($props['style'])){
				$props['style'].=$zIndex;
			}
			else{
				$props['style']=$zIndex;
			}
		}
		return $props;
	}
		
		/**
	 * Computes and returns the HTML a color overlay.
	 *
	 * @since 2.3.3
	 *
	 * @param array $styling The row's or column's styling array.
	 *
	 * @return bool Returns false if $styling doesn't have a color overlay. Otherwise outputs the HTML;
	 */
	private function do_color_overlay($styling) {
		  
		$type = !isset( $styling['cover_color-type'] ) || $styling['cover_color-type'] === 'color' ? 'color' : 'gradient';
		$hover_type = !isset( $styling['cover_color_hover-type'] ) || $styling['cover_color_hover-type'] === 'hover_color' ? 'color' : 'gradient';
		$is_empty = $type === 'color' ? empty( $styling['cover_color'] ) : empty( $styling['cover_gradient-gradient'] );
		$is_empty_hover = $hover_type === 'color' ? empty( $styling['cover_color_hover'] ) : empty( $styling['cover_gradient_hover-gradient'] );
		$hover_class = $is_empty_hover ? '' : 'tb-row-cover-hover';
		if( $is_empty && $is_empty_hover ){
					return false;
				}
		printf( '<div class="builder_row_cover %s"></div>', $hover_class );
				return true;
	}

	/**
	 * Computes and returns the HTML for a background slider.
	 *
	 * @since 2.3.3
	 *
	 * @param array  $row_or_col   Row or column definition.
	 * @param string $order        Order of row/column (e.g. 0 or 0-1-0-1 for sub columns)
	 * @param string $builder_type Accepts 'row', 'col', 'sub-col'
	 *
	 * @return bool Returns false if $row_or_col doesn't have a bg slider. Otherwise outputs the HTML for the slider.
	 */
	public function do_slider_background($row_or_col, $order, $builder_type = 'row') {
		if (empty($row_or_col['styling']['background_slider']) || 'slider' !== $row_or_col['styling']['background_type']) {
			return false;
		}
		$size = isset($row_or_col['styling']['background_slider_size']) ? $row_or_col['styling']['background_slider_size'] : false;
		if ($images = Themify_Builder_Model::get_images_from_gallery_shortcode($row_or_col['styling']['background_slider'])) :
			$bgmode = !empty($row_or_col['styling']['background_slider_mode']) ?$row_or_col['styling']['background_slider_mode'] : 'fullcover';
			$slider_speed = !empty($row_or_col['styling']['background_slider_speed']) ?$row_or_col['styling']['background_slider_speed'] : '2000';
			if (!$size) {
				$size = Themify_Builder_Model::get_gallery_param_option($row_or_col['styling']['background_slider'], 'size');
				if (!$size) {
					$size = 'large';
				}
			}
			?>
			<div id="<?php echo $builder_type; ?>-slider-<?php echo $order; ?>" class="<?php echo $builder_type; ?>-slider" data-sliderspeed="<?php echo $slider_speed ?>" data-bgmode="<?php echo $bgmode; ?>">
				<ul class="row-slider-slides clearfix">
					<?php
					$dot_i = 0;
					foreach ($images as $image) :
						$img_data = wp_get_attachment_image_src($image->ID, $size);
						?>
						<li data-bg="<?php echo esc_url(themify_https_esc($img_data[0])); ?>">
							<a class="row-slider-dot" data-index="<?php echo $dot_i; ?>"></a>
						</li>
						<?php
							++$dot_i;
					endforeach;
					?>
				</ul>
				<div class="row-slider-nav">
						<a class="row-slider-arrow row-slider-prev">&lsaquo;</a>
						<a class="row-slider-arrow row-slider-next">&rsaquo;</a>
				</div>
			</div>
			<!-- /.row-bgs -->
			<?php
		endif; // images
	}
		
		public function background_styling($builder_id,$row,$order,$type){
			// Background cover color
			if ( !empty( $row['styling'] ) && !$this->do_color_overlay( $row['styling'] ) ) {
				$breakpoints = themify_get_breakpoints();
				foreach($breakpoints as $bp=>$v){
					if(!empty($row['styling']['breakpoint_'.$bp]) && $this->do_color_overlay( $row['styling']['breakpoint_'.$bp] )){
						break;
					}
				}
			}
			
			// Background Slider
			$this->do_slider_background($row,$order, $type);
		  
		}
		
		/**
	 * Computes and returns data for Builder row or column video background.
	 *
	 * @since 2.3.3
	 *
	 * @param array $styling The row's or column's styling array.
	 *
	 * @return bool|string Return video data if row/col has a background video, else return false.
	 */
	public static function get_video_background($styling) {
		if (!(isset($styling['background_type']) && 'video' === $styling['background_type'] && !empty($styling['background_video']))) {
			return false;
		}
		$video_data = 'data-fullwidthvideo="' . esc_url(themify_https_esc($styling['background_video'])) . '"';

		// Will only be written if they exist, for backwards compatibility with global JS variable tbLocalScript.backgroundVideoLoop

		if (isset($styling['background_video_options'])) {
			if (is_array($styling['background_video_options'])) {
				$video_data .= in_array('mute', $styling['background_video_options']) ? ' data-mutevideo="mute"' : ' data-mutevideo="unmute"';
				$video_data .= in_array('unloop', $styling['background_video_options']) ? ' data-unloopvideo="unloop"' : ' data-unloopvideo="loop"';
				$video_data .= in_array('playonmobile', $styling['background_video_options']) ? ' data-playonmobile="play"' : ' data-unloopvideo="not-play"';
			} else {
				$video_data .= ( false !== stripos($styling['background_video_options'], 'mute') ) ? ' data-mutevideo="mute"' : ' data-mutevideo="unmute"';
				$video_data .= ( false !== stripos($styling['background_video_options'], 'unloop') ) ? ' data-unloopvideo="unloop"' : ' data-unloopvideo="loop"';
				$video_data .= ( false !== stripos($styling['background_video_options'], 'playonmobile') ) ? ' data-playonmobile="play"' : ' data-unloopvideo="not-play"';
			}
		}
		return apply_filters('themify_builder_row_video_background', $video_data, $styling);
	}
		
		
	public function row_animation($settings){

		// Parallax Element Scrolling - Row
		add_filter( 'themify_builder_row_fields_animation', array( $this, 'hover_elements_fields' ), 10 );
		add_filter( 'themify_builder_row_fields_animation', array( $this, 'parallax_elements_fields' ), 10 );
		add_filter( 'themify_builder_row_fields_animation', array( $this, 'sticky_elements_fields' ), 10 );

		$row_fields_animation = apply_filters('themify_builder_row_fields_animation', Themify_Builder_Model::get_animation());
		$settings['animation'] = array(
				'name' => esc_html__( 'Animation', 'themify' ),
				'options' => $row_fields_animation
		);
		return $settings;
	}

	public function row_styling_fields($fields){
		return $this->styling_fields('row', $fields);
	}
	
		
	public function column_styling_fields($fields){
			return $this->styling_fields('column', $fields);
	}
	
	public function subrow_styling_fields($fields){
			return $this->styling_fields('subrow', $fields);
	}
		
	private function styling_fields( $type, $fields ) {

		$is_premium = Themify_Builder_Model::is_premium();
		$is_mobile = themify_is_touch();

		$key = '.module_'.$type;
		if ( $type === 'row' ) {
			$overlay = __( 'Row Overlay', 'themify' );
			$inner = __( 'Row Inner Container', 'themify' );
						$inner_selector = 'row_inner';
		} elseif ( $type === 'column' ) {
			$overlay = __( 'Column Overlay', 'themify' );
			$inner = __( 'Column Inner Container', 'themify' );
						$inner_selector = 'tb-column-inner';
		} else {
			$overlay = __( 'Subrow Overlay', 'themify' );
			$inner = __( 'Subrow Inner Container', 'themify' );
						$inner_selector = 'subrow_inner';
		}
		$inner_selector = array( $key . '>div.'.$inner_selector );
		
		if( $type === 'column' ) {
			$inner_selector[] = $key . '>.tb_holder';
		}

		// Image size
		$image_size = themify_get_image_sizes_list( true );
		unset( $image_size[ key( $image_size ) ] );
		$props = array(
			// Background
			self::get_seperator('image_background',__('Background', 'themify')),
			array(
				'id' => 'background_type',
				'label' => __('Background Type', 'themify'),
				'type' => 'radio',
				'meta' => array(
					array('value' => 'image', 'name' => __('Image', 'themify'),'selected'=>true),
					array('value' => 'gradient', 'name' => __('Gradient', 'themify'),'disable'=>!$is_premium),
					array('value' => 'video', 'name' => __('Video', 'themify'),'disable'=>!$is_premium,'class'=>'reponive_disable'),
					array('value' => 'slider', 'name' => __('Slider', 'themify'),'disable'=>!$is_premium,'class'=>'reponive_disable')
				),
				'option_js' => true,
				'is_premium'=>$is_premium
			),
			// Background Slider
			array(
				'id' => 'background_slider',
				'type' => 'textarea',
				'label' => __('Background Slider', 'themify'),
				'class' => 'tb_hide tb_shortcode_input',
				'wrap_with_class' => 'reponive_disable tb_group_element tb_group_element_slider',
				'description' => sprintf('<a href="#" class="builder_button tb_gallery_btn">%s</a>', __('Insert Gallery', 'themify'))
			),
			// Background Slider Image Size
			array(
				'id' => 'background_slider_size',
				'label' => '',
				'description' => __('Image Size', 'themify'),
				'type' => 'select',
				'meta' => $image_size,
				'default'=>'large',
				'wrap_with_class' => 'reponive_disable tb_group_element tb_group_element_slider'
			),
			array(
				'id' => 'background_slider_mode',
				'label' => '',
				'description' => __('Background Slider Mode', 'themify'),
				'type' => 'select',
				'meta' => array(

					array('value' => 'fullcover', 'name' => __('Fullcover', 'themify')),
					array('value' => 'best-fit', 'name' => __('Best Fit', 'themify')),
					array('value' => 'kenburns-effect', 'name' => __('Ken Burns Effect', 'themify'))
				),
				'wrap_with_class' => 'reponive_disable tb_group_element tb_group_element_slider'
			),
			array(
				'id' => 'background_slider_speed',
				'label' => '',
				'description' => __('Slider Speed', 'themify'),
				'type' => 'select',
				'default' => '2000',
				'meta' => array(
					array('value' => '3500', 'name' => __('Slow', 'themify')),
					array('value' => '2000', 'name' => __('Normal', 'themify')),
					array('value' => '500', 'name' => __('Fast', 'themify'))
				),
				'wrap_with_class' => 'reponive_disable tb_group_element tb_group_element_slider'
			),

			// Video Background

			array(
				'id' => 'background_video',
				'type' => 'video',
				'label' => __('Background Video', 'themify'),
				'description' => __('Video format: mp4, YouTube, and Vimeo. Note: video background does not play on some mobile devices, background image will be used as fallback. Audio should be disabled to have auto play.', 'themify'),
				'class' => 'xlarge',
				'wrap_with_class' => 'reponive_disable tb_group_element tb_group_element_video'
			),
			array(
				'id' => 'background_video_options',
				'type' => 'checkbox',
				'label' => '',
				'options' => array(
					array('name' => 'unloop', 'value' => __('Disable looping', 'themify')),
					array('name' => 'mute', 'value' => __('Disable audio', 'themify')),
					array('name' => 'playonmobile', 'value' => __('Play video background on mobile', 'themify'))
				),
				'default' => 'mute',
				'wrap_with_class' => 'reponive_disable tb_group_element tb_group_element_video'
			),
			// Background Image
			array(
				'id' => 'background_image',
				'type' => 'image',
				'label' => __('Background Image', 'themify'),
				'class' => 'xlarge',
				'wrap_with_class' => 'tb_group_element tb_group_element_image tb_group_element_video',
				'prop' => 'background-image',
				'selector' => $key,
				'binding' => array(
					'empty' => array(
						'hide' => array('tb_image_options')
					),
					'not_empty' => array(
						'show' => array('tb_image_options')
					)
				)
			),
			array(
				'id' => 'background_gradient',
				'type' => 'gradient',
				'label' => __('Background Gradient', 'themify'),
				'class' => 'xlarge',
				'wrap_with_class' => 'tb_group_element tb_group_element_gradient',
				'prop' => 'background-image',
				'selector' => $key
			),
			// Background repeat
			array(
				'id' => 'background_repeat',
				'label' => '',
				'type' => 'select',
				'description'=>__('Background Mode', 'themify'),
				'prop' => 'background-mode',
				'selector' => $key,
				'meta' => array(
					array('value' => 'repeat', 'name' => __('Repeat All', 'themify')),
					array('value' => 'repeat-x', 'name' => __('Repeat Horizontally', 'themify')),
					array('value' => 'repeat-y', 'name' => __('Repeat Vertically', 'themify')),
					array('value' => 'repeat-none', 'name' => __('Do not repeat', 'themify')),
					array('value' => 'fullcover', 'name' => __('Fullcover', 'themify')),
					array('value' => 'best-fit-image', 'name' => __('Best Fit', 'themify')),
					array('value' => 'builder-parallax-scrolling', 'name' => __('Parallax Scrolling', 'themify'),'disable'=>( !$is_premium || $is_mobile ) ),
					array('value' => 'builder-zoom-scrolling', 'name' => __('Zoom Scrolling', 'themify'), 'disable'=>( !$is_premium || $is_mobile ) ),
					array('value' => 'builder-zooming', 'name' => __('Zooming', 'themify'), 'disable'=>( !$is_premium || $is_mobile ) )
				),
				'wrap_with_class' => 'tb_group_element tb_group_element_image tb_image_options',
				'binding' => array(
					'repeat-none' => array(
						'show' => array('background_zoom')
					),
					'builder-parallax-scrolling' => array(
						'hide' => array('background_attachment'),
						'responsive' => array(
							'disabled' => array( 'background_repeat' )
						)
					),
					'builder-zoom-scrolling' => array(
						'hide' => array('background_attachment'),
						'responsive' => array(
							'disabled' => array( 'background_repeat' )
						)
					),
					'builder-zooming' => array(
						'hide' => array('background_attachment'),
						'responsive' => array(
							'disabled' => array( 'background_repeat' )
						)
					),
					'select'=>array(
						'value' => 'repeat-none',
						'hide' => array('background_zoom'),
						'show' => array('background_attachment')
					),
					'responsive' => array(
						'disabled' => array( 'builder-parallax-scrolling', 'builder-zoom-scrolling', 'builder-zooming' )
					)
				)
			),
			// Background attachment
			array(
				'id' => 'background_attachment',
				'label' => '',
				'type' => 'select',
				'description'=>__('Background Attachment', 'themify'),
				'meta' => array(
					array('value' => 'scroll', 'name' => __('Scroll', 'themify')),
					array('value' => 'fixed', 'name' => __('Fixed', 'themify'))
				),
				'wrap_with_class' => 'tb_group_element tb_group_element_image tb_image_options',
				'prop' => 'background-attachment',
				'selector' => $key
			),
			// Background Zoom
			array(
				'id' => 'background_zoom',
				'label' => '',
				'type' => 'checkbox',
				'default' => '',
				'options' => array(
					array('value' => __('Zoom background image on hover', 'themify'), 'name' => 'zoom')
				),
				'wrap_with_class' => 'reponive_disable tb_group_element_image tb_group_element'
			),
			// Background position
			array(
				'id' => 'background_position',
				'label' => '',
				'type' => 'select',
				'description'=>__('Background Position', 'themify'),
				'meta' => array(
					array('value' => 'left-top', 'name' => __('Left Top', 'themify')),
					array('value' => 'left-center', 'name' => __('Left Center', 'themify')),
					array('value' => 'left-bottom', 'name' => __('Left Bottom', 'themify')),
					array('value' => 'right-top', 'name' => __('Right top', 'themify')),
					array('value' => 'right-center', 'name' => __('Right Center', 'themify')),
					array('value' => 'right-bottom', 'name' => __('Right Bottom', 'themify')),
					array('value' => 'center-top', 'name' => __('Center Top', 'themify')),
					array('value' => 'center-center', 'name' => __('Center Center', 'themify')),
					array('value' => 'center-bottom', 'name' => __('Center Bottom', 'themify'))
				),
				'default'=>'center-center',
				'wrap_with_class' => 'tb_group_element tb_group_element_image tb_image_options',
				'prop' => 'background-position',
				'selector' => $key
			),
			// Background Color
			array(
				'id' => 'background_color',
				'type' => 'color',
				'label' => __('Background Color', 'themify'),
				'class' => 'small',
				'wrap_with_class' => 'tb_group_element tb_group_element_image tb_group_element_slider tb_group_element_video',
				'prop' => 'background-color',
				'selector' => $key
			),
			// Overlay Color
			self::get_seperator('separator_cover',$overlay),
			array(
				'id' => 'cover_color-type',
				'label' => __('Overlay', 'themify'),
				'type' => 'radio',
				'meta' => array(
					array('value' => 'color', 'name' => __('Color', 'themify')),
					array('value' => 'cover_gradient', 'name' => __('Gradient', 'themify'))
				),
				'default'=>'color',
				'option_js' => true,
				'wrap_with_class' => 'tb_overlay_element',
				'is_premium' => $is_premium
			),
			array(
				'id' => 'cover_color',
				'type' => 'color',
				'label' => '',
				'class' => 'small',
				'wrap_with_class' => 'tb_group_element tb_group_element_color',
				'is_premium' => $is_premium,
				'prop' => 'background-color',
				'selector' => $key.'>.builder_row_cover::before'
			),
			array(
				'id' => 'cover_gradient',
				'type' => 'gradient',
				'label' =>'',
				'wrap_with_class' => 'tb_group_element tb_group_element_cover_gradient',
				'is_premium' => $is_premium,
				'prop' => 'background-image',
				'selector' => $key.'>.builder_row_cover::before'
			),
			array(
				'id' => 'cover_color_hover-type',
				'label' => __('Overlay Hover', 'themify'),
				'type' => 'radio',
				'meta' => array(
					array('value' => 'hover_color', 'name' => __('Color', 'themify')),
					array('value' => 'hover_gradient', 'name' => __('Gradient', 'themify'))
				),
				'default' => 'hover_color',
				'option_js' => true,
				'wrap_with_class' => 'tb_overlay_element',
				'is_premium' => $is_premium
			),
			array(
				'id' => 'cover_color_hover',
				'type' => 'color',
				'label' => '',
				'class' => 'small',
				'wrap_with_class' => 'tb_group_element tb_group_element_hover_color',
				'is_premium' => $is_premium,
				'prop' => 'background-color',
				'selector' => $key.'>.builder_row_cover::after'
			),
			array(
				'id' => 'cover_gradient_hover',
				'type' => 'gradient',
				'label' => '',
				'wrap_with_class' => 'tb_group_element tb_group_element_hover_gradient',
				'is_premium' => $is_premium,
				'prop' => 'background-image',
				'selector' => $key.'>.builder_row_cover::after'
			),
			// Inner Container
			self::get_seperator( 'separator_cover', $inner ),
			// Background Image
			array(
				'id' => 'background_image_inner',
				'type' => 'image',
				'label' => __('Background Image', 'themify'),
				'class' => 'xlarge',
				'prop' => 'background-image',
				'selector' => $inner_selector,
				'binding' => array(
					'empty' => array(
						'hide' => array('tb_image_inner_options')
					),
					'not_empty' => array(
						'show' => array('tb_image_inner_options')
					)
				)
			),
			// Background repeat
			array(
				'id' => 'background_repeat_inner',
				'label' => '',
				'type' => 'select',
				'description'=>__('Background Mode', 'themify'),
				'prop' => 'background-mode',
				'selector' => $inner_selector,
				'meta' => array(
					array('value' => 'repeat', 'name' => __('Repeat All', 'themify')),
					array('value' => 'repeat-x', 'name' => __('Repeat Horizontally', 'themify')),
					array('value' => 'repeat-y', 'name' => __('Repeat Vertically', 'themify')),
					array('value' => 'repeat-none', 'name' => __('Do not repeat', 'themify')),
					array('value' => 'fullcover', 'name' => __('Fullcover', 'themify')),
					array('value' => 'best-fit-image', 'name' => __('Best Fit', 'themify'))
				),
				'wrap_with_class' => 'tb_group_element tb_group_element_image tb_image_inner_options'
			),
			// Background attachment
			array(
				'id' => 'background_attachment_inner',
				'label' => '',
				'type' => 'select',
				'description'=>__('Background Attachment', 'themify'),
				'meta' => array(
					array('value' => 'scroll', 'name' => __('Scroll', 'themify')),
					array('value' => 'fixed', 'name' => __('Fixed', 'themify'))
				),
				'wrap_with_class' => 'tb_group_element tb_group_element_image tb_image_inner_options',
				'prop' => 'background-attachment',
				'selector' => $inner_selector
			),
			// Background position
			array(
				'id' => 'background_position_inner',
				'label' => '',
				'type' => 'select',
				'description'=>__('Background Position', 'themify'),
				'meta' => array(
					array('value' => 'left-top', 'name' => __('Left Top', 'themify')),
					array('value' => 'left-center', 'name' => __('Left Center', 'themify')),
					array('value' => 'left-bottom', 'name' => __('Left Bottom', 'themify')),
					array('value' => 'right-top', 'name' => __('Right top', 'themify')),
					array('value' => 'right-center', 'name' => __('Right Center', 'themify')),
					array('value' => 'right-bottom', 'name' => __('Right Bottom', 'themify')),
					array('value' => 'center-top', 'name' => __('Center Top', 'themify')),
					array('value' => 'center-center', 'name' => __('Center Center', 'themify')),
					array('value' => 'center-bottom', 'name' => __('Center Bottom', 'themify'))
				),
				'default'=>'center-center',
				'wrap_with_class' => 'tb_group_element tb_group_element_image tb_image_inner_options',
				'prop' => 'background-position',
				'selector' => $inner_selector
			),
			// Background Color
			array(
				'id' => 'background_color_inner',
				'type' => 'color',
				'label' => __('Background Color', 'themify'),
				'class' => 'small',
				'wrap_with_class' => 'tb_group_element tb_group_element_image',
				'prop' => 'background-color',
				'selector' => $inner_selector
			),
			self::get_padding( $inner_selector, 'padding_inner' ),
			self::get_border( $inner_selector, 'border_inner' )
		);
		$props = array_reverse($props);

		foreach( $props as $p ) {
			array_unshift( $fields, $p );
		}

		return $fields;
	}
	
	
	public function admin_bar_menu($menu){
		$is_premium = Themify_Builder_Model::is_premium();
		$layouts = array(
			array(
				'id' => 'layout_themify_builder',
				'parent' => 'themify_builder',
				'title' => __('Layouts', 'themify'),
				'href' => '#',
				'meta' => array('class' => !$is_premium?'tb_lite':'')
			),
			// Sub Menu
			array(
				'id' => 'load_layout_themify_builder',
				'parent' => 'layout_themify_builder',
				'title' => __('Load Layout', 'themify'),
				'href' => '#',
				'meta' => array('class' =>'tb_load_layout'),
								'is_premium'=>$is_premium
			),
			array(
				'id' => 'save_layout_themify_builder',
				'parent' => 'layout_themify_builder',
				'title' => __('Save as Layout', 'themify'),
				'href' => '#',
				'meta' => array('class' =>'tb_save_layout'),
								'is_premium'=>$is_premium
			),
		);
		
		return array_merge($menu, $layouts);
	}
}