<?php
if( ! class_exists( 'Themify_Builder_Component_Base' ) ) {
class Themify_Builder_Component_Base {

    /**
     * The original post id
     */
    public static $post_id = false;

    /**
     * The layout_part_id
     */
    public static $layout_part_id = false;

    /**
     * Array of classnames to add to post objects
     */
    private static $_post_classes = array();
    
    
    /**
     * The names of settings for tooltip.
     * 
     * @access public
     */
    protected static $texts =  array();

    public function __construct() {
        
    }

    public function get_type() {
        return 'component';
    }

    public function get_name() {
        
    }
    
    public final function get_class_name() {
        return get_class($this);
    }

    public function get_styling() {
        $type = $this->get_name();
        $key = '.module_' . $type;
        $options = array(
            //frame
            self::get_seperator( 'frame', __( 'Frame', 'themify' ) ),
            self::get_frame_tabs($key),
            // Font
            self::get_seperator('font', __('Font', 'themify'), false),
            self::get_font_family(array($key,' h1', ' h2', ' h3:not(.module-title)', ' h4', ' h5', ' h6')),
            self::get_element_font_weight(array($key,' h1', ' h2', ' h3:not(.module-title)', ' h4', ' h5', ' h6')),
            self::get_color(array($key,' h1', ' h2', ' h3:not(.module-title)',' h4', ' h5', ' h6'), 'font_color', __('Font Color', 'themify')),
            self::get_font_size($key),
            self::get_line_height($key),
            self::get_letter_spacing($key),
            self::get_text_align($key),
            self::get_text_transform($key),
            self::get_font_style($key),
            self::get_text_decoration($key,'text_decoration_regular'),
            // Link
            self::get_seperator('link', __('Link', 'themify')),
            self::get_color(' a', 'link_color'),
            self::get_text_decoration(' a'),
            // Padding
            self::get_seperator('padding', __('Padding', 'themify')),
            self::get_padding($key),
            // Margin
            'separator_margin'=>self::get_seperator('margin', __('Margin', 'themify')),
            'margin'=>self::get_margin($key),
            'margin_top'=> self::get_margin_top($key),
            'margin_bottom'=> self::get_margin_bottom($key),
            // Border
            self::get_seperator('border', __('Border', 'themify')),
            self::get_border($key)
        );
        if ($type !== 'row') {
            $options[] = self::get_seperator();
            $options[] = array(
                'id' => 'custom_css_' . $type,
                'type' => 'text',
                'label' => __('Additional CSS Class', 'themify'),
                'class' => 'large exclude-from-reset-field',
                'description' => sprintf('<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling (<a href="https://themify.me/docs/builder#additional-css-class" target="_blank">learn more</a>).', 'themify'))
            );
	        unset( $options['margin_top'],$options['margin_bottom']);
        }else{
	        unset($options['margin']);
        }
           
        return apply_filters('themify_builder_' . $type . '_fields_styling', $options);
    }

    public static function get_breakpoint_switcher() {
        static $data = null;
        if ($data === null):
            ob_start();
            ?>
            <ul class="tb_lightbox_switcher clearfix">
                <?php
                $breakpoints = themify_get_breakpoints();
                $breakpoints = array_merge(array('desktop' => ''), $breakpoints);
                $is_premium = Themify_Builder_Model::is_premium();
                ?>
                <?php foreach ($breakpoints as $b => $v): ?>
                    <li<?php if(!$is_premium && $b!=='desktop'):?> class="tb_lite"<?php endif;?>>
                        <?php if(!$is_premium && $b!=='desktop'):?><span class="themify_lite_tooltip"></span><?php endif;?>
                        <a href="#<?php echo $b ?>" class="tab_<?php echo $b ?>" title="<?php printf(__('%s', 'themify'), ($b === 'tablet_landscape' ? 'Tablet Landscape' : ucfirst($b))); ?>">
                            <i class="<?php if($b==='tablet_landscape'):?>ti-tablet <?php endif;?>ti-<?php echo $b ?>"></i>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php
            $data = ob_get_contents();
            ob_end_clean();
        endif;
        echo $data;
    }

    public function get_label() {
        
    }
	
    protected function get_save_btn($label = null){
        $save_txt = $label !== null ? $label : __('Save', 'themify');
        ?>
        <div id="tb_lightbox_actions_items">
            <button id="builder_submit_<?php echo $this->get_name() ?>_settings" class="builder_button builder_save_button" title="<?php _e('Ctrl + S', 'themify') ?>"><?php echo $save_txt; ?></button>
        </div>
        <?php
    }

    protected function _form_template() {
        $label_txt = in_array( $this->get_name(), array('column', 'subrow') ,true) ? esc_html__( 'Done', 'themify' ) : null;
        ?>
        <form id="tb_<?php echo $this->get_name() ?>_settings">
            <div id="tb_lightbox_options_tab_items">
                <li class="title"><?php echo $this->get_label(); ?></li>
            </div>		
            <?php $this->get_save_btn( $label_txt ); ?>	
            <div id="tb_options_styling" class="tb_options_tab_wrapper">
                <?php self::get_breakpoint_switcher(); ?>
                <div class="tb_options_tab_content">
                    <?php themify_render_styling_settings($this->get_styling()); ?>
                    <p>
                        <a href="#" class="reset-styling">
                            <i class="ti-close"></i>
                            <?php _e('Reset Styling', 'themify') ?>
                        </a>
                    </p>
                </div>
            </div>
            <!-- /.tb_options_tab_wrapper -->
        </form>
        <?php
    }

    public function print_template_form($echo=false) {
        ob_start();

        $this->_form_template();

        $output = ob_get_clean();
        if (empty($output)) {
            return;
        }
        $output = '<script type="text/html" id="tmpl-builder_form_'.$this->get_name().'">'.$output.'</script>';
        if(!$echo){
            return $output;
        }
        echo $output;
    }

    protected static function get_directions_data(array $row, $count) {
        $directions = array('desktop', 'tablet','tablet_landscape', 'mobile');
        $row_attributes = array();
        foreach ($directions as $dir) {
            if (!empty($row[$dir . '_dir']) && $row[$dir . '_dir'] !== 'ltr') {
                $row_attributes['data-' . $dir . '_dir'] = $row[$dir . '_dir'];
            }
        }
        $col_mobile = !empty($row['col_mobile']) && $row['col_mobile'] !== 'mobile-auto' ? $row['col_mobile'] : false;
        $col_tablet = !empty($row['col_tablet']) && $row['col_tablet'] !== 'tablet-auto' ? $row['col_tablet'] : false;
        $col_tablet_landscape = !empty($row['col_tablet_landscape']) && $row['col_tablet_landscape'] !== 'tablet_landscape-auto' ? $row['col_tablet_landscape'] : false;
        if ($col_mobile !== false || $col_tablet !== false || $col_tablet_landscape!==false) {
            $row_attributes['data-basecol'] = $count;
            if ($col_tablet !== false) {
                $row_attributes['data-col_tablet'] = $col_tablet;
            }
            if ($col_tablet_landscape !== false) {
                $row_attributes['data-col_tablet_landscape'] = $col_tablet_landscape;
            }
            if ($col_mobile !== false) {
                $row_attributes['data-col_mobile'] = $col_mobile;
            }
        }
        return $row_attributes;
    }

    protected static function get_order($count) {
        switch ($count) {

            case 6:
                $order_classes = array('first', 'second', 'third', 'fourth', 'fifth', 'last');
                break;

            case 5:
                $order_classes = array('first', 'second', 'third', 'fourth', 'last');
                break;

            case 4:
                $order_classes = array('first', 'second', 'third', 'last');
                break;

            case 3:
                $order_classes = array('first', 'middle', 'last');
                break;

            case 2:
                $order_classes = array('first', 'last');
                break;

            default:
                $order_classes = array('first');
                break;
        }
        return $order_classes;
    }
    
    public static function get_frame_tabs( $selector, $id = 'frame_tabs' ) {
            $top = self::get_frame_props($selector);
            $bottom = self::get_frame_props($selector, 'bottom');
            $left = self::get_frame_props($selector, 'left');
            $right = self::get_frame_props($selector, 'right');

            return array(
                    'type' => 'tabs',
                    'id' => $id,
                    'tabs_class' => 'tb_field_expanded',
                    'tabs' => array(
                            'top' => array(
                                    'label' => __('Top', 'themify'),
                                    'fields' => $top,
                            ),
                            'bottom' => array(
                                    'label' => __('Bottom', 'themify'),
                                    'fields' => $bottom
                            ),
                            'left' => array(
                                    'label' => __('Left', 'themify'),
                                    'fields' => $left
                            ),
                            'right' => array(
                                    'label' => __('Right', 'themify'),
                                    'fields' => $right
                            ),
                    )
            );
    }
        
        
	

    protected static function get_frame_props( $selector, $id = 'top' ) {
            $path = THEMIFY_BUILDER_URI . '/img/row-frame/';

            return array(
                    array(
                            'id' => $id . '-frame_type',
                            'label' => __('Type', 'themify'),
                            'type' => 'radio',
                            'meta' => array(
                                    /**
                                     * @note the value in this option is prefixed with $id, this is to ensure option_js works properly
                                     */
                                    array('value' => $id . '-presets', 'name' => __('Presets', 'themify'),'selected' => true ),
                                    array('value' => $id . '-custom', 'name' => __('Custom', 'themify') ),
                            ),
                            'prop' => 'frame-custom',
                            'wrap_with_class'=>'tb_frame',
							/**
							 * the second selector is for themes with Builder Section Scrolling feature
							 * @ref #7241
							 */
                            'selector' => $selector.'>.tb_row_frame_'.$id . ',' . $selector.' > .fp-tableCell > .tb_row_frame_'.$id,
                            'option_js' => true,
                    ),
                    array(
                            'id' => $id.'-frame_layout',
                            'label' => '',
                            'description' => '',
                            'type' => 'layout',
                            'options' => array(
                                    array('value' => '', 'label' => __('None', 'themify'), 'img' => $path .  'none.png'),
                                    array('value' => 'slant1', 'label' => __('Slant 1', 'themify'), 'img' => $path . 'slant1.svg'),
                                    array('value' => 'slant2', 'label' => __('Slant 2', 'themify'), 'img' => $path . 'slant2.svg'),
                                    array('value' => 'arrow1', 'label' => __('Arrow 1', 'themify'), 'img' => $path . 'arrow1.svg'),
                                    array('value' =>'arrow2', 'label' => __('Arrow 2', 'themify'), 'img' => $path . 'arrow2.svg'),
                                    array('value' =>'arrow3', 'label' => __('Arrow 3', 'themify'), 'img' => $path . 'arrow3.svg'),
                                    array('value' =>'arrow4', 'label' => __('Arrow 4', 'themify'), 'img' => $path . 'arrow4.svg'),
                                    array('value' =>'arrow5', 'label' => __('Arrow 5', 'themify'), 'img' => $path . 'arrow5.svg'),
                                    array('value' =>'arrow6', 'label' => __('Arrow 6', 'themify'), 'img' => $path . 'arrow6.svg'),
                                    array('value' => 'cloud1', 'label' => __('Cloud 1', 'themify'), 'img' => $path . 'cloud1.svg'),
                                    array('value' => 'cloud2' , 'label' => __('Cloud 2', 'themify'), 'img' => $path . 'cloud2.svg'),
                                    array('value' => 'curve1', 'label' => __('Curve 1', 'themify'), 'img' => $path . 'curve1.svg'),
                                    array('value' => 'curve2', 'label' => __('Curve 2', 'themify'), 'img' => $path . 'curve2.svg'),
                                    array('value' => 'mountain1', 'label' => __('Mountain 1', 'themify'), 'img' => $path . 'mountain1.svg'),
                                    array('value' => 'mountain2', 'label' => __('Mountain 2', 'themify'), 'img' => $path . 'mountain2.svg'),
                                    array('value' => 'mountain3', 'label' => __('Mountain 3', 'themify'), 'img' => $path . 'mountain3.svg'),
                                    array('value' => 'wave1' , 'label' => __('Wave 1', 'themify'), 'img' => $path . 'wave1.svg'),
                                    array('value' => 'wave2', 'label' => __('Wave 2', 'themify'), 'img' => $path . 'wave2.svg'),
                                    array('value' => 'wave3', 'label' => __('Wave 3', 'themify'), 'img' => $path . 'wave3.svg'),
                                    array('value' => 'wave4', 'label' => __('Wave 4', 'themify'), 'img' => $path . 'wave4.svg'),
                                    array('value' => 'ink-splash1', 'label' => __('Ink Splash 1', 'themify'), 'img' => $path . 'ink-splash1.svg'),
                                    array('value' => 'ink-splash2', 'label' => __('Ink Splash 2', 'themify'), 'img' => $path . 'ink-splash2.svg'),
                                    array('value' => 'zig-zag', 'label' => __('Zig Zag', 'themify'), 'img' => $path . 'zig-zag.svg'),
                                    array('value' => 'grass', 'label' => __('Grass', 'themify'), 'img' => $path . 'grass.svg'),
                                    array('value' => 'melting', 'label' => __('Melting', 'themify'), 'img' => $path . 'melting.svg'),
                                    array('value' => 'lace', 'label' => __('Lace', 'themify'), 'img' => $path . 'lace.svg'),
                            ),
                            'prop' => 'frame',
                            'class'=>'tb_frame',
                            'wrap_with_class' => 'tb_group_element tb_group_element_' . $id . '-presets',
                            'selector' => $selector.'>.tb_row_frame_'.$id . ',' . $selector.' > .fp-tableCell > .tb_row_frame_'.$id,
                            ),
                    array(
                            'id' => $id . '-frame_custom',
                            'type' => 'image',
                            'label' => '',
                            'class' => 'tb_frame xlarge',
                            'wrap_with_class' => 'tb_group_element tb_group_element_' . $id . '-custom',
                    ),
                    array(
                            'id' => $id.'-frame_color',
                            'type' => 'color',
                            'label' => '',
                            'description' => __('Color', 'themify'),
                            'class' => 'tb_frame small',
                            'wrap_with_class' => 'tb_group_element tb_group_element_' . $id . '-presets',
                    ),
                    array(
                            'id' => 'frame_dimensions',
                            'type' => 'multi',
                            'label' => '',
                            'fields' => array(
                                    array(
                                            'id' => $id.'-frame_width',
                                            'type' => 'range',
                                            'class' => 'tb_frame xsmall',
                                            'label' => __( 'Width', 'themify' ),
                                            'select_class'=>'tb_frame_unit',
                                            'units' => array(
                                                    '%' => array(
                                                            'min' => 0,
                                                            'max' => 200
                                                    ),
                                                    'PX' => array(
                                                            'min' => 0,
                                                            'max' => 10000
                                                    ),
                                                    'EM' => array(
                                                            'min' => 0,
                                                            'max' => 10
                                                    )
                                            )
                                    ),
                                    array(
                                            'id' => $id.'-frame_height',
                                            'type' => 'range',
                                            'label' => '',
                                            'class' => 'tb_frame xsmall',
                                            'label' => __( 'Height', 'themify' ),
                                            'select_class'=>'tb_frame_unit',
                                            'units' => array(
                                                    '%' => array(
                                                            'min' => 0,
                                                            'max' => 200
                                                    ),
                                                    'PX' => array(
                                                            'min' => 0,
                                                            'max' => 10000
                                                    ),
                                                    'EM' => array(
                                                            'min' => 0,
                                                            'max' => 10
                                                    )
                                            )
                                    ),
                                    array(
                                            'id' => $id.'-frame_repeat',
                                            'type' => 'range',
                                            'label' => __( 'Repeat', 'themify' ),
                                            'class' => 'tb_frame xsmall',
                                            'description' => __( 'times', 'themify' ),
                                    ),
                                ),
                            ),
                    array(
                            'id' => $id . '-frame_location',
                            'label' => '',
                            'type' => 'select',
                            'description' => '',
                            'class' => 'tb_frame reponive_disable',
                            'meta' => array(
                                    array( 'value' => '', 'name' => __('Display below content', 'themify') ),
                                    array( 'value' => 'in_front', 'name' => __('Display above content', 'themify') )
                            ),
                    ),
            );
    }

    /**
     * Return the correct animation css class name
     * @param string $effect 
     * @return string
     */
	public static function parse_animation_effect($effect, $mod_settings = null) {

		$class = '';
		if ( ! Themify_Builder_Model::is_animation_active() ) {
			return $class;
		}

		if ( ! empty( $mod_settings['hover_animation_effect'] ) ) {
			$class .= ' hover-wow hover-animation-' . $mod_settings['hover_animation_effect'];
		}

		if ( $effect === '' ) {
			return $class;
		}
		$class .=  '' !== $effect && !in_array($effect, array('fade-in', 'fly-in', 'slide-up'), true)? ' wow ' . $effect : $effect;
		if (!empty($mod_settings['animation_effect_delay'])) {
			$class .= ' animation_effect_delay_' . $mod_settings['animation_effect_delay'];
		}
		if (!empty($mod_settings['animation_effect_repeat'])) {
			$class .= ' animation_effect_repeat_' . $mod_settings['animation_effect_repeat'];
		}

		return $class;
	}


	/**
     * Retrieve builder templates
     * @param $template_name
     * @param array $args
     * @param string $template_path
     * @param string $default_path
     * @param bool $echo
     * @return string
     */
    public static function retrieve_template($template_name, $args = array(), $template_path = '', $default_path = '', $echo = true) {

        ob_start();
        self::get_template($template_name, $args, $template_path = '', $default_path = '');
        if ($echo) {
            echo ob_get_clean();
        } else {
            return ob_get_clean();
        }
    }

    /**
     * Get template builder
     * @param $template_name
     * @param array $args
     * @param string $template_path
     * @param string $default_path
     */
    public static function get_template($template_name, $args = array(), $template_path = '', $default_path = '') {
        static $paths = array();
        if (!empty($args) && is_array($args)) {
            extract($args);
        }
        $key = $template_name . $template_path . $default_path;        
        if (!isset($paths[$key])) {
            $paths[$key] = self::locate_template($template_name, $template_path, $default_path);
        }
        if (isset($paths[$key])) {
            global $ThemifyBuilder;
            include($paths[$key]);
        }
    }

    /**
     * Locate a template and return the path for inclusion.
     *
     * This is the load order:
     *
     * 		yourtheme		/	$template_path	/	$template_name
     * 		$default_path	/	$template_name
     */
    public static function locate_template($template_name, $template_path = '', $default_path = '') {
        $template = '';
        $templates = Themify_Builder_Model::get_directory_path('templates');
        foreach ($templates as $dir) {
            if (is_file($dir . $template_name)) {
                $template = $dir . $template_name;
                break;
            }
        }
        // Get default template
        if (!$template) {
                $template = $default_path . $template_name;
        }
        // Return what we found
        return apply_filters('themify_builder_locate_template', $template, $template_name, $template_path);
	}

	/**
	 * Get checkbox data
	 * @param $setting
	 * @return string
	 */
	public static function get_checkbox_data($setting) {
		return implode(' ', explode('|', $setting));
	}

	/**
	 * Return only value setting
	 * @param $string
	 * @return string
	 */
	public static function get_param_value($string) {
		$val = explode('|', $string);
		return $val[0];
	}

	/**
	 * Helper to get element attributes return as string.
	 *
	 * @access public
	 * @param array $props
	 * @return string
	 */
	public static function get_element_attributes($props) {
		$out = '';
		foreach ($props as $atts => $val) {
			$out .= ' ' . $atts . '="' . esc_attr($val) . '"';
		}
		return $out;
	}

	/**
	 * Filter post_class to add the classnames to posts
	 *
	 * @return array
	 */
	public static function filter_post_class($classes) {
		return !empty(self::$_post_classes) ? array_merge($classes, self::$_post_classes) : $classes;
	}

	/**
	 * Add classes to post_class
	 * @param string|array $classes
	 */
	public static function add_post_class($classes) {
		foreach ((array) $classes as $class) {
			self::$_post_classes[$class] = $class;
		}
	}

	/**
	 * Remove sepecified classnames from post_class
	 * @param string|array $classes
	 */
	public static function remove_post_class($classes) {
		foreach ((array) $classes as $class) {
			unset(self::$_post_classes[$class]);
		}
	}

	/**
	 * Get query page
	 */
	public static function get_paged_query() {
		global $wp;
		$page = 1;
		$qpaged = get_query_var('paged');
		if (!empty($qpaged)) {
			$page = $qpaged;
		} else {
			$qpaged = wp_parse_args($wp->matched_query);
			if (isset($qpaged['paged']) && $qpaged['paged'] > 0) {
				$page = $qpaged['paged'];
			}
		}
		return $page;
	}

	/**
	 * Returns page navigation
	 * @param string Markup to show before pagination links
	 * @param string Markup to show after pagination links
	 * @param object WordPress query object to use
	 * @param original_offset number of posts configured to skip over
	 * @return string
	 */
	public static function get_pagenav($before = '', $after = '', $query = false, $original_offset = 0) {
		global $wp_query;

		if (false == $query) {
			$query = $wp_query;
		}

		$paged = (int)self::get_paged_query();
		$numposts = $query->found_posts;

		// $query->found_posts does not take offset into account, we need to manually adjust that
		if ((int) $original_offset) {
			$numposts = $numposts - (int) $original_offset;
		}

		$max_page = ceil($numposts / $query->query_vars['posts_per_page']);
		$out = '';

		if (empty($paged)) {
			$paged = 1;
		}
		$pages_to_show = apply_filters('themify_filter_pages_to_show', 5);
		$pages_to_show_minus_1 = $pages_to_show - 1;
		$half_page_start = floor($pages_to_show_minus_1 / 2);
		$half_page_end = ceil($pages_to_show_minus_1 / 2);
		$start_page = $paged - $half_page_start;
		if ($start_page <= 0) {
			$start_page = 1;
		}
		$end_page = $paged + $half_page_end;
		if (($end_page - $start_page) != $pages_to_show_minus_1) {
			$end_page = $start_page + $pages_to_show_minus_1;
		}
		if ($end_page > $max_page) {
			$start_page = $max_page - $pages_to_show_minus_1;
			$end_page = $max_page;
		}
		if ($start_page <= 0) {
			$start_page = 1;
		}

		if ($max_page > 1) {
			$out .= $before . '<div class="pagenav clearfix">';
			if ($start_page >= 2 && $pages_to_show < $max_page) {
				$first_page_text = "&laquo;";
				$out .= '<a href="' . esc_url(get_pagenum_link()) . '" title="' . esc_attr($first_page_text) . '" class="number">' . $first_page_text . '</a>';
			}
			if ($pages_to_show < $max_page)
				$out .= get_previous_posts_link('&lt;');
			for ($i = $start_page; $i <= $end_page; $i++) {
				if ($i == $paged) {
					$out .= ' <span class="number current">' . $i . '</span> ';
				} else {
					$out .= ' <a href="' . esc_url(get_pagenum_link($i)) . '" class="number">' . $i . '</a> ';
				}
			}
			if ($pages_to_show < $max_page)
				$out .= get_next_posts_link('&gt;');
			if ($end_page < $max_page) {
				$last_page_text = "&raquo;";
				$out .= '<a href="' . esc_url(get_pagenum_link($max_page)) . '" title="' . esc_attr($last_page_text) . '" class="number">' . $last_page_text . '</a>';
			}
			$out .= '</div>' . $after;
		}
		return $out;
	}

	public static function get_seperator($id = '', $label = '', $hr = true) {
		return $id !== '' ?
			array(
				'id' => 'separator_' . $id,
				'type' => 'separator',
				'meta' => array('html' => '<h4 class="tb_style_toggle">' . $label . '<i class="ti-angle-up"></i></h4>'),
			) :
			array(
				'type' => 'separator',
				'meta' => array('html' => '<hr />')
			);
	}

	protected static function get_font_family($selector, $id = 'font_family') {
		return array(
			'id' => $id,
			'type' => 'font_select',
			'label' => __('Font Family', 'themify'),
			'class' => 'font-family-select',
			'prop' => 'font-family',
			'selector' => $selector
		);
	}

	protected static function get_element_font_weight($selector, $id = 'element_font_weight') {
		return array(
			'id' => $id,
			'type' => 'select',
			'label' => __('Font Weight', 'themify'),
			'class' => 'font-weight-select',
			'prop' => 'font-weight',
			'meta' => array(
				'400' => 400,
			),
			'selector' => $selector
		);
	}


	protected static function get_font_size($selector, $id = 'font_size', $label = '') {
		if ($label === '') {
			$label = __('Font Size', 'themify');
		}
		return array(
			'id' => $id,
			'type' => 'range',
			'label' => $label,
			'selector' => $selector,
			'prop' => 'font-size',
			'units' => array(
				'PX' => array(
					'min' => 6,
					'max' => 70
				),
				'EM' => array(
					'min' => 0.5,
					'max' => 5
				),
				'%' => array(
					'min' => 70,
					'max' => 300,
				)
			)
		);
	}

	protected static function get_line_height($selector, $id = 'line_height') {
		return array(
			'id' => $id,
			'type' => 'range',
			'label' => __('Line Height', 'themify'),
			'selector' => $selector,
			'prop' => 'line-height',
			'units' => array(
				'PX' => array(
					'min' => -150,
					'max' => 150
				),
				'EM' => array(
					'min' => -3,
					'max' => 3
				),
				'%' => array(
					'min' => 100,
					'max' => 300,
				)
			)
		);
	}

	protected static function get_letter_spacing($selector, $id = 'letter_spacing') {
		return array(
			'id' => $id,
			'type' => 'range',
			'label' => __('Letter Spacing', 'themify'),
			'selector' => $selector,
			'prop' => 'letter-spacing',
			'units' => array(
				'PX' => array(
					'min' => -20,
					'max' => 100
				),
				'EM' => array(
					'min' => -2,
					'max' => 5
				)
			)
		);
	}

	protected static function get_text_align($selector, $id = 'text_align') {
		return array(
			'id' => $id,
			'label' => __('Text Align', 'themify'),
			'type' => 'icon_radio',
			'meta' => Themify_Builder_Model::get_text_aligment(),
			'prop' => 'text-align',
			'selector' => $selector,
			'default' => ''
		);
	}

	protected static function get_text_transform($selector, $id = 'text_transform') {
		return array(
			'id' => $id,
			'label' => __('Text Transform', 'themify'),
			'type' => 'icon_radio',
			'meta' => Themify_Builder_Model::get_text_transform(),
			'prop' => 'text-transform',
			'selector' => $selector,
			'default' => ''
		);
	}

	protected static function get_text_decoration($selector, $id = 'text_decoration') {
		return array(
			'id' => $id,
			'type' => 'icon_radio',
			'label' => __('Text Decoration', 'themify'),
			'meta' => Themify_Builder_Model::get_text_decoration(),
			'prop' => 'text-decoration',
			'selector' => $selector,
			'default' => ''
		);
	}

	protected static function get_font_style($selector, $id = 'font_style', $id2 = 'font_weight') {
		return array(
			'id' => 'multi_' . $id,
			'type' => 'multi',
                        'wrap_with_class'=>'tb_multi_fonts',
			'label' => __('Font Style', 'themify'),
			'fields' => array(
				array(
					'id' => $id . '_regular',
					'type' => 'icon_radio',
					'meta' => Themify_Builder_Model::get_font_style(),
					'prop' => 'font-style',
					'selector' => $selector,
					'default' => ''
				),
				array(
					'id' => $id2,
					'type' => 'icon_radio',
					'meta' => Themify_Builder_Model::get_font_weight(),
					'prop' => 'font-weight',
					'selector' => $selector,
					'default' => ''
				)
			)
		);
	}

	protected static function get_color($selector, $id, $label = '', $prop = 'color',$color_type_dependency=false) {
		if ($label === '') {
			$label = __('Color', 'themify');
		}
        $color = array(
			'id' => $id,
			'type' => 'color',
			'prop' => $prop,
            'selector' => $selector
        );
        if($color_type_dependency){
            $color['wrap_with_class'] = 'tb_group_element tb_group_element_'.$id.'_solid';
        }
        if($label){
            $color['label'] = $label;
        }
		return $color;
	}

	protected static function get_image($selector, $id = 'background_image') {
		return array(
			'id' => $id,
			'type' => 'image_and_gradient',
			'label' => __('Background Image', 'themify'),
			'class' => 'xlarge',
			'prop' => 'background-image',
			'selector' => $selector,
			'option_js' => true,
                        'binding' => array(
                                'empty' => array(
                                        'hide' => array('tb_image_options')
                                ),
                                'not_empty' => array(
                                        'show' => array('tb_image_options')
                                )
                        )
		);
	}

	protected static function get_repeat($selector, $id = 'background_repeat') {
		return array(
			'id' => $id,
			'label' => __('Background Repeat', 'themify'),
			'type' => 'select',
			'meta' => Themify_Builder_Model::get_repeat(),
			'prop' => 'background-repeat',
			'selector' => $selector,
			'wrap_with_class' => 'tb_group_element tb_group_element_image tb_image_options'
		);
	}

	protected static function get_position( $selector, $id = 'background_position' ) {
		return array(
			'id' => $id,
			'label' => __( 'Background Position', 'themify' ),
			'type' => 'select',
			'meta' => Themify_Builder_Model::get_position(),
			'prop' => 'background-position',
			'selector' => $selector,
			'wrap_with_class' => 'tb_group_element tb_group_element_image tb_image_options'
		);
	}

	protected static function get_padding($selector, $id = 'padding') {
		return array(
			'id' => $id,
			'type' => 'padding',
			'label' => __('Padding', 'themify'),
			'prop' => 'padding',
			'selector' => $selector
		);
	}


	protected static function get_margin($selector, $id = 'margin') {
		return array(
			'id' => $id,
			'type' => 'margin',
			'label' => __('Margin', 'themify'),
			'prop' => 'margin',
			'selector' => $selector
		);
	}

	protected static function get_margin_top($selector, $id = 'margin-top') {
            
		return array(
			'id' => $id,
			'type' => 'range',
			'label' => __('Margin', 'themify'),
			'prop' => 'margin-top',
			'selector' => $selector,
			'wrapper_class' => 'display-inline-block',
			'after' => '<small>' . __( 'Top', 'themify' ) . '</small>',
			'units' => array(
				'PX' => array(
					'min' => -500,
					'max' => 500,
				),
				'EM' => array(
					'min' => -10,
					'max' => 10,
				),
				'%' => array(
					'min' => -100,
					'max' => 100,
				)
			)
		);
	}

	protected static function get_margin_bottom($selector, $id = 'margin-bottom') {
		return array(
			'id' => $id,
			'type' => 'range',
			'label' => '',
			'prop' => 'margin-bottom',
			'selector' => $selector,
			'wrapper_class' => 'display-inline-block',
			'after' => '<small>' . __( 'Bottom', 'themify' ) . '</small>',
			'units' => array(
				'PX' => array(
					'min' => -500,
					'max' => 500,
				),
				'EM' => array(
					'min' => -10,
					'max' => 10,
				),
				'%' => array(
					'min' => -100,
					'max' => 100,
				)
			)

		);
	}

    protected static function get_border($selector, $id = 'border') {
        return array(
            'id' => $id,
            'type' => 'border',
            'label' => __('Border', 'themify'),
            'prop' => 'border',
            'selector' => $selector
        );
    }

	protected static function get_width($selector, $id = 'width') {
            
		return array(
			'id' => $id,
			'type' => 'range',
			'label' => __('Width', 'themify'),
			'prop' => 'width',
			'selector' => $selector,
			'units' => array(
				'PX' => array(
					'min' => -500,
					'max' => 500
				),
				'EM' => array(
					'min' => -10,
					'max' => 10
				),
				'%' => array(
					'min' => 0,
					'max' => 100,
					'increment' => 1
				)
			)
		);
	}

	protected static function get_multi_columns_count( $selector, $id = 'column' ) {
                return array(
                                    'id' => $id . '_count',
                                    'type' => 'select',
                                    'label' => __('Column Count', 'themify'),
                                    'meta' => array(
                                            array('value' => '', 'name' => ''),
                                            array('value' => 1, 'name' => 1),
                                            array('value' => 2, 'name' => 2),
                                            array('value' => 3, 'name' => 3),
                                            array('value' => 4, 'name' => 4),
                                            array('value' => 5, 'name' => 5),
                                            array('value' => 6, 'name' => 6)
                                    ),
                                    'prop' => 'column-count',
                                    'binding' => array(
                                        'empty'=>array(
                                            'hide' => array('tb_multi_columns_wrap')
                                        ),
                                        'not_empty'=>array(
                                            'show' => array('tb_multi_columns_wrap')
                                        )
                                    ),
                                    'selector' => $selector
		);
	}
	
	protected static function get_multi_columns_gap( $selector, $id = 'column' ) {
		return array(
			'id' => $id . '_gap',
			'type' => 'range',
			'label' => __('Column Gap', 'themify'),
			'class' => 'style_field_px xsmall column_gap',
			'prop' => 'column-gap',
			'selector' => $selector,
                        'wrap_with_class' => 'tb_multi_columns_wrap',
                        'units' => array(
				'PX' => array(
					'min' => 0,
					'max' => 500
				)
			)
		);
	}

	protected static function get_multi_columns_divider( $selector, $id = 'column' ) {
		return array(
			'id' => $id . '_divider',
			'type' => 'multi',
			'label' => __('Column Divider', 'themify'),
                        'wrap_with_class' => 'tb_multi_columns_wrap',
			'fields' => array(
				self::get_color($selector, $id . '_divider_color', false, 'column-rule-color'),
				array(
					'id' => $id . '_divider_width',
					'type' => 'range',
					'class' => 'style_field_px xsmall tb_multi_columns_width',
					'prop' => 'column-rule-width',
					'selector' => $selector,
					'units' => array(
						'PX' => array(
							'min' => 0,
							'max' => 500
						)
					)
				),
				array(
					'id' => $id . '_divider_style',
					'type' => 'select',
					'meta' => Themify_Builder_Model::get_border_styles(),
					'class' => 'style_field_select',
					'prop' => 'column-rule-style',
					'selector' => $selector
				)
			)
		);
	}

    protected static function get_heading_margin_multi_field($selector, $h_level = 'h1', $margin_side = 'top',$id='') {
        $id = $id===''?$h_level:$id;
        if($h_level===''){
            $h_level.=' ';
        }
        return array(
            'id' => 'multi_' . $id . '_margin_' . $margin_side,
            'type' => 'multi',
            'label' => ('top' === $margin_side ? __('Margin', 'themify') : ''),
            'fields' => array(
                array(
                    'id' => $id . '_margin_' . $margin_side,
                    'type' => 'range',
                    'class' => 'xsmall',
                    'prop' => 'margin-' . $margin_side,
                    'selector' => $selector . ' ' . $h_level,
                    'wrapper_class' => 'display-inline-block',
                    'after' => __($margin_side, 'themify'),
                    'units' => array(
                                    'PX' => array(
                                        'min' => -500,
                                        'max' => 500
                                    ),
                                    'EM' => array(
                                        'min' => -10,
                                        'max' => 10
                                    ),
                                    '%' => array(
                                        'min' => 0,
                                        'max' => 100,
                                        'increment' => 1
                                    )
                                )
                    )
            ),

        );
    }
    
    protected static function show_frame($styles){
        $breakpoints = themify_get_breakpoints(false,true);
        $breakpoints = array('desktop'=>'')+$breakpoints;   
        foreach (array( 'top', 'bottom', 'left', 'right' ) as $side ) {   
            foreach ( $breakpoints as $bp=>$v) {
                $settings = 'desktop' === $bp ?$styles: ( !empty( $styles[ 'breakpoint_' . $bp ] ) ? $styles[ 'breakpoint_' . $bp ] : array() );        
                if (!empty($settings) &&  Themify_Builder_Model::get_frame( $settings, $side ) ) {
                ?>
                    <div class="tb_row_frame tb_row_frame_<?php echo $side; ?> <?php if(isset( $settings[ "{$side}-frame_location" ] ) ){ echo $settings[ "{$side}-frame_location" ];} ?>"></div>
                <?php
                    break;
                }
            }
        }
    }

    protected static function get_color_type($id, $label = '',$solid_id,$gradient_id) {
		if ($label === '') {
			$label = __('Color Type', 'themify');
		}
        $color_type = array(
			'id' => $id,
            'type' => 'radio',
            'meta' => array(
                array('value' => $solid_id.'_solid', 'name' => __('Solid', 'themify'),'selected' => true ),
                array('value' => $gradient_id.'_gradient', 'name' => __('Gradient', 'themify') ),
            ),
            'option_js' => true,
            'default'=>$solid_id.'_solid',
            'solid_id' => $solid_id,
            'gradient_id' => $gradient_id
		);
        if($label){
            $color_type['label'] = $label;
        }
		return $color_type;
    }
    
    protected static function get_gradient_color($selector, $id, $label = '', $prop = 'background-image') {
		if ($label === '') {
			$label = __('Color', 'themify');
		}
        $color = array(
			'id' => $id,
            'type' => 'gradient',
			'prop' => $prop,
            'selector' => $selector,
            'wrap_with_class' => 'tb_group_element tb_group_element_'.$id.'_gradient',
		);
        if($label){
            $color['label'] = $label;
        }
		return $color;
	}

}
}
