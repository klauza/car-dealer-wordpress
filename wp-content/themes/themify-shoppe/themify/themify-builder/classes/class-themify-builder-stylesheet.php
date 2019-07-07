<?php

class Themify_Builder_Stylesheet {

    private $builder;

    /**
     * Flag to know if we're in the middle of saving the stylesheet.
     * @var string
     */
    private $saving_stylesheet = false;

    /**
     * Constructor
     * 
     * @access public
     * @param object Themify_Builder $builder 
     */
    public function __construct(Themify_Builder $builder) {
        $this->builder = $builder;
		
        add_filter('themify_google_fonts', array($this, 'enqueue_fonts'));
        if (defined('DOING_AJAX')) {
            add_action('wp_ajax_tb_load_rules', array($this, 'get_module_rules'), 10);
            add_action('wp_ajax_tb_set_newrules', array($this, 'set_ajax_styles'), 10);
            add_action('wp_ajax_tb_slider_live_styling', array($this, 'slider_live_styling'), 10);
        } else {
            add_action('wp_enqueue_scripts', array($this, 'enqueue_stylesheet'), 14);
            add_action('themify_builder_before_template_content_render', array($this, 'enqueue_stylesheet'), 10);
        }
    }

    /**
     * Output row styling style
     * @param int $builder_id
     * @param array $row
     * @return string
     */
    public function render_styling($builder_id, $row, $order, $type) {
        $style_id = '.themify_builder';
        if ($type === 'row' || $type === 'column') {
            $style_id.='.themify_builder .module_' . $type . '_' . $builder_id . '-';
        } elseif ($type === 'subrow') {
            $style_id.=' .module_' . $type . '.sub_row_';
        } elseif($type === 'module'){
			$style_id = ".themify_builder .";
			$type = $row['mod_name'];
			$row['styling'] = $row['mod_settings'];
		}else {
            $style_id.='.sub_column_post_' . $builder_id . 'sub_column_';
        }
        $style_id.=$order;
        echo $this->get_custom_styling($style_id, $type, $row['styling'], 'tag'),$this->render_responsive_style($style_id, $type, $row['styling'], 'tag');
    }

    /**
     * Generate CSS styling.
     * 
     * @since 1.0.0
     * @since 2.2.5 Added the ability to return pure CSS without <style> tags for stylesheet generation.
     *
     * @param int $style_id
     * @param string $mod_name Name of the module to build styles for. Example 'row' for row styling.
     * @param array $settings List of settings to generate style.
     * @param bool $array Used for legacy styling generation.
     * @param string $format Use 'tag' to return the CSS enclosed in <style> tags. This mode is used while user is logged in and Builder is active. Use 'css' to return only the CSS. This mode is used on stylesheet generation.
     *
     * @return string
     */
    private function get_custom_styling($style_id, $mod_name, $settings, $format = 'tag') {

        if (empty($settings)) {
            return '';
        }
        global $themify;
        static $rules = array();
        /**
         * Filter style id selector. This can be used to modify the selector on a theme by theme basis.
         * 
         * @since 2.3.1
         *
         * @param string $style_id Full selector string to be filtered.
         * @param string $builder_id ID of Builder instance.
         * @param array $row Current row.
         */
        $style_id = apply_filters('themify_builder_row_styling_style_id', $style_id);

        if (!isset($themify->builder_google_fonts)) {
            $themify->builder_google_fonts = '';
        }
        if (!isset($rules[$mod_name])) {
            $component = Themify_Builder_Components_Manager::get_component_types($mod_name);
            $styling = array();
            if (isset($component)) {
                $styling = $component->get_styling();
            } elseif (isset(Themify_Builder_model::$modules[$mod_name])) {
                $styling = Themify_Builder_model::$modules[$mod_name]->get_styling();
            }
            $rules[$mod_name] = !empty($styling) ? $this->make_styling_rules($styling) : array();
        }
        $css = array();
        // Check for font_color_type in Text module
         if(isset($settings['font_color_type'])){
            $text_tags = ('text' === $mod_name || 'box' === $mod_name) ? array('','_h1','_h2','_h3','_h4','_h5','_h6') : array('');
            foreach($text_tags as $text_tag){
                $font_color_type = isset($settings['font_color_type'.$text_tag])?$settings['font_color_type'.$text_tag]:'solid';
                if(isset($settings['font_gradient_color'.$text_tag.'-gradient']) && strpos($font_color_type, '_solid') !== false){
                        unset($settings['font_gradient_color'.$text_tag.'-gradient']);
                }elseif(isset($settings['font_color'.$text_tag]) && strpos($font_color_type, '_gradient') !== false){
                        unset($settings['font_color'.$text_tag]);
                }
            }      
        }	
        foreach ($settings as $id => $value) {
            if (!is_array($value)) {
                $value = trim($value);
                if ($value === '') {
                    continue;
                }
            }
            $is_gradient = ($id === 'background_image-gradient' || $id === 'background_gradient-gradient');
            $is_text_gradient = strpos($id, 'font_gradient_color') !== false;
            $is_cover = !$is_gradient && ($id === 'cover_gradient-gradient' || $id === 'cover_gradient_hover-gradient');
            if (!empty($rules[$mod_name][$id]) || $is_gradient || $is_cover || $is_text_gradient) {
                if ($is_gradient || $is_cover) {
                    $type = 'background-image';
                    $opt = array();
                }
                elseif($is_text_gradient){
                    $opt = array();
                    $type='';
                }
                else {
                    $opt = $rules[$mod_name][$id];
                    $type = $opt['prop'];
                }
                $selector = '';

                if (in_array($type, array('background-color', 'color'),true)) {
                    if ($id === 'cover_color_hover' || $id === 'cover_color') {
                        $mode = isset($settings[$id . '-type']) ? $settings[$id . '-type'] : 'color';
                        if ($mode === 'hover_gradient' || $mode === 'cover_gradient') {
                            continue;
                        }
                    }
                    $value = self::get_rgba_color($value);
                    if ($value !== false) {
                        $selector = sprintf('%s:%s; ', $type, $value);
                        /*
                        if($id==='background_color' && empty($settings['background_image'])){//if parent mode has gradient/image
                            $selector.='background-image:none;';
                        }
                         */
                    }
                } elseif (in_array($type, array('border-top', 'border-bottom', 'border-left', 'border-right'), true)) {
                        $is_empty_width = strpos( $id, '_width', 2 ) === false;  
                        if ( $is_empty_width && strpos( $id, '_style', 2 )===false) {
                            continue;
                        }

                        $multi_id = !$is_empty_width? str_replace( '_width', '', $id ):str_replace( '_style', '', $id );
                        $tmp_id = str_replace( str_replace( 'border-', '_', $type ), '', $multi_id );

                        if ( isset( $settings[$tmp_id . '-type'] ) && $settings[$tmp_id . '-type'] === 'all' ) {
                            if( $type !== 'border-top') {
                                continue;
                            }
                            $type = explode( '-', $type );
                            $type = $type[0];
                        }
                        $border_style = !$is_empty_width?(! empty( $settings[$multi_id . '_style'] ) ? $settings[$multi_id . '_style'] : 'solid'):$value;
                        if( $border_style === 'none' ) {
                            $selector = sprintf( '%s:%s; ', $type, 'none' );
                        } elseif ( $value && !$is_empty_width) {
                            $color = ! empty( $settings[$multi_id . '_color'] )  ? self::get_rgba_color( $settings[$multi_id . '_color'] ) : false;
                            if( $color ) {
                                    $selector = sprintf( '%s:%spx %s %s; ', $type, $value, $border_style, $color );
                            } else {
                                $selector = sprintf( '%s-width:%spx;%s-style:%s; ', $type, $value, $type, $border_style  );
                            }
                        }
                } elseif (in_array($type, array('font-size', 'letter-spacing', 'line-height','padding-top', 'padding-right', 'padding-bottom', 'padding-left', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left', 'width'), true)) {
                    if (in_array($type, array('padding-top', 'padding-right', 'padding-bottom', 'padding-left', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left'), true)) {
                        $tmp_id = str_replace(array('_top','_left','_right','_bottom'),'',$id);
                        if (isset($settings['checkbox_' . $tmp_id . '_apply_all']) && $settings['checkbox_' . $tmp_id . '_apply_all'] !== '|') {
                            if($type!=='padding-top' && $type!=='margin-top'){
                                continue;
                            }
                            $type = explode('-',$type);
                            $type = $type[0];
                        }
                    }
                    $unit = isset($settings[$id . '_unit']) ? $settings[$id . '_unit'] : 'px';
                    $selector = sprintf('%s:%s%s; ', $type, $value, $unit);
                } elseif (in_array($type, array('text-decoration', 'text-align', 'background-repeat', 'background-attachment', 'background-position', 'font-family', 'font-style', 'font-weight', 'text-transform'), true)) {
                    if ($type === 'font-family' && !in_array($value, themify_get_web_safe_font_list(true))) {
                        if ($value === 'default') {
                            continue;
                        }
                        $themify->builder_google_fonts .= str_replace(' ', '+', '|' . $value );
                    }
                    elseif($type === 'font-weight') {
						if( ! empty( $themify->builder_google_fonts ) ) {
							if ( ! empty ( $value ) ) {
								$themify->builder_google_fonts .= ':' . $value;
							}

							$default_sizes = array(
								'regular' => 400,
								'italic'  => 400,
								'bold'	  => 700
							);

							if ( strpos( $value, 'italic' ) !== false ) {
								$italic = '; font-style: italic;';
							} else {
								$italic = '';
							}
							$value = ! empty( $default_sizes[ $value ] ) ? $default_sizes[ $value ] : preg_replace( '/[^0-9]/', '', $value );
							$value .= $italic;
						}
                    }
                    elseif ( $type === 'background-position' ){
                        $value = str_replace( '-', ' ', $value );
                    }
                    elseif( $type === 'background-repeat' && $value === 'fullcover' ) {
                            $type = 'background-size';
                            $value = 'cover';
                    }
                    $selector = sprintf('%s: %s; ', $type, $value);
                } elseif ($type === 'background-image') {
                    if ($is_cover || in_array($mod_name, array('row', 'column', 'subrow', 'sub_column'), true)) {
                        $key = $is_cover ? str_replace(array('_gradient', '-gradient'), array('_color', '-type'), $id) : 'background_type';
                    } else {
                        $key = 'background_image-type';
                    }
                    $mode = isset($settings[$key]) ? $settings[$key] : 'image';

                    if ($mode === 'image' || $mode==='video') {
                        if (!$is_gradient && !$is_cover && !empty($value)) {
                            $selector = sprintf('%s: url("%s"); ', $type, themify_https_esc($value));
                            if($mode==='video'){
                                $selector.='background-size:cover;background-position:center center;';
                            }
                        }
                    } elseif (($is_cover && ($mode === 'hover_gradient' || $mode === 'cover_gradient')) || ($mode === 'gradient' && $is_gradient) && !empty($settings[$id])) {
                        $id = str_replace('-gradient', '', $id);
                        if (!empty($rules[$mod_name][$id])) {
                            $opt = $rules[$mod_name][$id];
                            $selector = self::get_gradient($settings, $opt['id']);
                        }
                    }
                } elseif( in_array($type, array('column-count', 'column-gap', 'column-rule-width'),true ) ) {
					
					if( $type === 'column-rule-width' ) {
						$tmp_value = array( $value . 'px' );
						$multi_id = str_replace( '_width', '', $id );

						$tmp_value[] = ! empty( $settings[$multi_id . '_style'] ) ? $settings[$multi_id . '_style'] : 'solid';
						$tmp_value[] = ! empty( $settings[$multi_id . '_color'] ) ? $settings[$multi_id . '_color'] : '';

						$type = 'column-rule';
						$value = implode( ' ', $tmp_value );
					}
					elseif( $type === 'column-gap' ) { 
                                            $value .= 'px'; 
                                        }
					$selector = sprintf('%s: %s; ', $type, $value);
                } elseif ( $type === 'background-mode' ) {
                    $bg_values = array(
                            'repeat' => 'repeat',
                            'repeat-x' => 'repeat-x',
                            'repeat-y' => 'repeat-y',
                            'repeat-none' => 'no-repeat',
                            'fullcover' => 'cover',
                            'best-fit-image' => 'contain',
                            'builder-parallax-scrolling' => 'cover',
                            'builder-zoom-scrolling' => '100%',
                            'builder-zooming' => '100%'
                    );
                    $bg_css =$selector ='';

                    if( strpos( $value, 'repeat' ) !== false ) {
                            $type = 'background-repeat';
                    } else {
                            $type = 'background-size';
                            $selector = 'background-repeat: no-repeat;';
                            if( $value === 'best-fit-image' || $value === 'builder-zooming' ) {
                                    $bg_css = 'center center;';
                            } elseif( $value === 'builder-zoom-scrolling' ) {
                                    $bg_css = '50%;';
                            }
                    }

                    if( ! empty( $bg_values[ $value ] ) ) {
                            $selector .= sprintf( '%s: %s; ', $type, $bg_values[ $value ] );

                            if( ! empty( $bg_css ) ) {
                                    $selector .= 'background-position:' . $bg_css;
                            }
                    }
                } elseif($type==='frame' || $type==='frame-custom'){
                    $side = explode('-',$id);
                    $side = $side[0];
                    if (  $frame_type = Themify_Builder_Model::get_frame( $settings, $side ) ) {
                        if ( $frame_type === 'presets' ) {
                            if($type==='frame-custom'){
                                continue;
                            }
				            $file_name = $value;
                            if ( $side === 'left' || $side === 'right' ) {
                                $file_name .= '-l';
                            }
                            $path = THEMIFY_BUILDER_DIR . '/img/row-frame/' . $file_name . '.svg';
                            $svg = file_get_contents( $path );

                            if ( isset( $settings[ "{$side}-frame_color" ] ) ) {
                                $svg = str_ireplace( array( '#D3D3D3' ), self::get_rgba_color( $settings[ "{$side}-frame_color" ] ), $svg );
                            }

                            $svg_data = 'data:image/svg+xml;base64,' . base64_encode( $svg );
                            $selector= 'background-image: url("' . $svg_data . '");';
			            } else {
                            if($type==='frame'){
                                continue;
                            }
                            $selector = 'background-image: url("' . $settings[ "{$side}-frame_custom" ] . '");';
			            }
                        
                        if ( isset( $settings[ "{$side}-frame_width" ] ) ) {
				            $unit = isset( $settings[ "{$side}-frame_width_unit" ] ) ? $settings[ "{$side}-frame_width_unit" ] : '%';
				            $selector.= 'width: ' . $settings[ "{$side}-frame_width" ] . $unit.';';
			            }

			            if ( isset( $settings[ "{$side}-frame_height" ] ) ) {
				            $unit = isset( $settings[ "{$side}-frame_height_unit" ] ) ? $settings[ "{$side}-frame_height_unit" ] : '%';
				            $selector.= 'height: ' . $settings[ "{$side}-frame_height" ] . $unit.';';
			            }

			            if ( isset( $settings[ "{$side}-frame_repeat" ] ) && (int) $settings[ "{$side}-frame_repeat" ] !== 0 ) {
                            /**
                            * @note: The 0.1% added is to fix a bug in Chrome where it shows extra pixels of the frame
                            */
                            if ( $side === 'left' || $side === 'right' ) {
                                $selector.= 'background-size: 100% ' . ( ( 100 / $settings[ "{$side}-frame_repeat" ] ) + 0.1 ) . '%;';
                            } else {
                                $selector.= 'background-size: ' . ( ( 100 / $settings[ "{$side}-frame_repeat" ] ) + 0.1 ) . '% 100%;';
                            }
                        }
                    }
                } elseif($is_text_gradient){
                    $id = str_replace('-gradient', '', $id);
                    if (!empty($rules[$mod_name][$id])) {
                        $opt = $rules[$mod_name][$id];
                        $selector = self::get_gradient($settings, $opt['id']);
                        $selector .= 'color:transparent;-webkit-background-clip:text;background-clip:text;';
                    }
                }

                if (!empty($selector) && !empty($opt['selector'])) {
                    if (is_array($opt['selector'])) {
                        $opt['selector'] = implode(',', $opt['selector']);
                    }
                    $css[$opt['selector']] = isset($css[$opt['selector']]) ? $css[$opt['selector']] : '';
                    $css[$opt['selector']].= $selector;
                }
            }
        }
        $output = array();
        if (!empty($css)) {
            foreach ($css as $selector => $defs) {
                $selector = explode(',', $selector);
                $styles = array();
                foreach ($selector as $s) {
                    $styles[] = "{$style_id}{$s}";
                }
                $output[] = implode(',', $styles) . '{' . $defs . '}' . "\n";
            }
        }

        $output = apply_filters( 'builder_custom_styling', $output, $style_id, $mod_name, $settings, $format );

        if ('tag' === $format) {
            $output = !empty($output) ? '<style type="text/css">' . implode('', $output) . '</style>' : '';
        }
        return $format === 'array' || 'tag' === $format ? $output : implode('', $output);
    }

    /**
     * Render responsive style media queries.
     * 
     * @since 2.6.6
     * @access public
     * @param string $style_id 
     * @param string $element 
     * @param array $settings 
     * @return string
     */
    private function render_responsive_style($style_id, $element, $settings, $format = 'css') {
        static $breakpoints = false;
        $output = '';
        if (!$breakpoints) {
            $breakpoints = themify_get_breakpoints();
            foreach ($breakpoints as $bp => $value) {
                $breakpoints[$bp] = array(is_array($value) ? $value[1] : $value);
            }
        }
        foreach ($breakpoints as $bp => $val) {
            // responsive styling
            if (!empty($settings['breakpoint_' . $bp]) && is_array($settings['breakpoint_' . $bp])) {
                $style = $this->get_custom_styling($style_id, $element, $settings['breakpoint_' . $bp], 'css');
                if (!empty($style)) {
                    $media_queries = count($val) === 2 ?
                            sprintf('@media only screen and (min-width : %spx) and (max-width : %spx) {', $val[0], $val[1]) : sprintf('@media screen and (max-width: %spx) {', $val[0]);

                    $output .= $media_queries . $style . '}' . PHP_EOL;
                }
            }
        }
        if ('tag' === $format && $output !== '') {
            $output = '<style type="text/css">' . $output . '</style>';
        }
        return $output;
    }

    /**
     * Tries to enqueue stylesheet. If it's not possible, it hooks an action to wp_head to build the CSS and output it.
     * 
     * @since 2.2.5
     */
    public function enqueue_stylesheet() {
        $load = true;
        // If enqueue fails, maybe the file doesn't exist...
        if (!$this->test_and_enqueue()) {
            // Try to generate it right now.
            $id = Themify_Builder_Model::get_ID();
            $post_data = $GLOBALS['ThemifyBuilder_Data_Manager']->get_data($id);
			
            if ($post_data) {
                // Write Stylesheet
                $this->write_stylesheet(array('id' => $id, 'data' => $post_data));
                $load = !$this->test_and_enqueue();
            }
        }
        else{
            $load =false;
        }
        if ($load) {
            // No luck. Let's do it inline.
            add_action('themify_builder_background_styling', array($this, 'render_styling'), 10, 4);
        }
    }

    /**
     * Write stylesheet file.
     * 
     * @since 2.2.5
     * 
     * @return array
     */
    public function write_stylesheet($data_set,$is_temp=false) {
        // Information about how writing went.
        $results = array();

        $this->saving_stylesheet = true;
        $style_id = (int) $data_set['id'];
        $css_to_save = $this->recursive_style_generator($data_set['data'], $style_id);
   
        unset($data_set);
        $css_file = self::get_stylesheet('bydir', $style_id,$is_temp);
        $filesystem = Themify_Filesystem::get_instance();
        if ($filesystem->execute->is_file($css_file)) {
            $filesystem->execute->delete($css_file);
        }
        if (!empty($css_to_save)) {
            $write = $filesystem->execute->put_contents($css_file, $css_to_save, FS_CHMOD_FILE);
            if($write){
                // Add information about writing.
                $results['css_file'] = self::get_stylesheet('byurl', $style_id,$is_temp);
                $results['write'] = $write;
                // Save Google Fonts
                global $themify;
                if (!$is_temp && !empty($themify->builder_google_fonts)) {
                    $builder_fonts = get_option('themify_builder_google_fonts');
                    if (!is_array($builder_fonts)) {
                        $builder_fonts = array();
                    }
                    if (isset($builder_fonts[$style_id])) {
                        $builder_fonts[$style_id] = $themify->builder_google_fonts;
                        $entry_fonts = $builder_fonts;
                    } else {
                        $entry_fonts = array($style_id => $themify->builder_google_fonts) + $builder_fonts;
                    }
                    update_option('themify_builder_google_fonts', $entry_fonts);
                }
             }
             else{
                 $results['write'] = esc_html__('Styles can`t be written.Please check permission of uploading folder', 'themify');
             }
        } else {
            // Add information about writing.
            $results['write'] = esc_html__('Nothing written. Empty CSS.', 'themify');
        }

        $this->saving_stylesheet = false;
        return $results;
    }

    /**
     * Build style recursively. Written for sub_row styling generation.
     * 
     * @since 2.2.6
     * 
     * @param array $data Collection of styling data.
     * @param int $style_id ID of the current entry.
     * @param string $sub_row Row ID when it's a sub row. This is used starting from second level depth.
     *
     * @return string
     */
    public function recursive_style_generator($data, $style_id, $sub_row = '') {
        $css_to_save = '';
        $data = json_decode(json_encode($data), true);

        if (empty($data)) {
            return $css_to_save;
        }
        $is_subrow = !empty($sub_row);
        foreach ($data as $row_index => $row) {
            $row_order = isset($row['row_order']) ? $row['row_order'] : $row_index;
            if (!$is_subrow && !empty($row['styling']) && is_array($row['styling'])) {
                $selector = ".themify_builder_{$style_id}_row.module_row_{$row_order}";


                $css_to_save .= $this->get_custom_styling($selector, 'row', $row['styling'], 'css');

                // responsive styling
                $css_to_save .= $this->render_responsive_style($selector, 'row', $row['styling']);
            }
            // Sub Row Style
            elseif ($is_subrow) {
                $sub_row_parts = explode('-', str_replace('sub_row_', '', $sub_row));
                if (!empty($row['styling']) && is_array($row['styling'])) {

                    $selector2 = '.module_row_' . $sub_row_parts[0] . ' .module_column_' . $sub_row_parts[1] . ' .sub_row_' . $sub_row_parts[0] . '-' . $sub_row_parts[1] . '-' . $row['row_order'];
                    $css_to_save .= $this->get_custom_styling($selector2, 'subrow', $row['styling'], 'css');
                    // responsive styling
                    $css_to_save .= $this->render_responsive_style($selector2, 'subrow', $row['styling']);
                }
            }
            if (!isset($row['cols']) || !is_array($row['cols'])) {
                continue;
            }
            foreach ($row['cols'] as $col_index => $col) {
                // column styling
                if (!empty($col['styling']) && is_array($col['styling'])) {
                    $column_order = isset($col['column_order']) ? $col['column_order'] : $col_index;
                    // dealing with 1st level columns
                    if (!$is_subrow) {
                        $selector = ".module_row_{$row_order}" . " .module_column_{$column_order}.tb_{$style_id}_column";
                    } else { // dealing with 2nd level columns (sub-columns)
                        $row_col = $sub_row_parts[0] . '-' . $sub_row_parts[1];
                        $selector = ".sub_column_post_{$style_id}.sub_column_{$row_col}-{$row_order}-{$column_order}";
                    }
                    $css_to_save .= $this->get_custom_styling($selector, 'column', $col['styling'], 'css');

                    // responsive styling
                    $css_to_save .= $this->render_responsive_style($selector, 'column', $col['styling']);
                }

                if (!isset($col['modules']) || !is_array($col['modules'])) {
                    continue;
                }
                foreach ($col['modules'] as $mod_index => $mod) {
                    if (isset($mod['mod_name'],$mod['mod_settings'])) {
                        $this_index = empty($sub_row)?"$row_index-$col_index-$mod_index":$sub_row . "$row_order-$col_index-$mod_index";
                        $css_to_save .= $this->get_custom_styling(".themify_builder .{$mod['mod_name']}-$style_id-$this_index", $mod['mod_name'], $mod['mod_settings'], 'css');

                        // responsive styling modules
                        $css_to_save .= $this->render_responsive_style(".themify_builder .{$mod['mod_name']}-$style_id-$this_index", $mod['mod_name'], $mod['mod_settings']);
                        
                    }
                    if (isset($mod['row_order'])) {
                        $css_to_save .= $this->recursive_style_generator(array($mod), $style_id, "sub_row_$row_index-$col_index-");
                    }
                }
            }
        }
        return $css_to_save;
    }

    /**
     * Return the URL or the directory path for a template, template part or content builder styling stylesheet.
     * 
     * @since 2.2.5
     *
     * @param string $mode Whether to return the directory or the URL. Can be 'bydir' or 'byurl' correspondingly. 
     * @param int $single ID of layout, layour part or entry that we're working with.
     *
     * @return string
     */
    private static function get_stylesheet($mode = 'bydir', $single = null,$is_temp=false) {
        static $before = null;
        if ($before === null) {
            $upload_dir = wp_upload_dir();
            $before = array(
                'bydir' => $upload_dir['basedir'],
                'byurl' => $upload_dir['baseurl'],
            );
        }
      
        if ($single===null) {
            $single = Themify_Builder_Model::get_ID();
        }
        $single = is_int($single) ? get_post($single) : get_page_by_path($single, OBJECT, 'tbuilder_layout_part');
        if (!is_object($single)) {
            return '';
        }
        $single = $single->ID;
        $path = "$before[$mode]/themify-css";
        if ('bydir' === $mode) {
            $filesystem = Themify_Filesystem::get_instance();
            if ( ! $filesystem->execute->is_dir($path) ) {
                wp_mkdir_p( $path);
            }
        }

        $stylesheet = "$path/themify-builder-$single-generated";
        if($is_temp){
           $stylesheet.='-tmp'; 
        }
        $stylesheet.='.css';
        /**
         * Filters the return URL or directory path including the file name.
         *
         * @param string $stylesheet Path or URL for the global styling stylesheet.
         * @param string $mode What was being retrieved, 'bydir' or 'byurl'.
         * @param int $single ID of the template, template part or content builder that we're fetching.
         *
         */
        return apply_filters('themify_builder_get_stylesheet', $stylesheet, $mode, $single);
    }

    /**
     * Checks if the builder stylesheet exists and enqueues it.
     * 
     * @since 2.2.5
     * 
     * @return bool True if enqueue was successful, false otherwise.
     */
    public function test_and_enqueue( $return = false, $post_id = null ) {
		//remove tmp path
		$tmp_path = self::get_stylesheet( 'bydir', $post_id, true );
		$filesystem = Themify_Filesystem::get_instance();
		
		if( $filesystem->execute->is_file( $tmp_path ) ) {
			$filesystem->execute->delete( $tmp_path );
		}
		
		$stylesheet_path = self::get_stylesheet( 'bydir', $post_id );
		if ( self::is_readable_and_not_empty( $stylesheet_path ) ) {
			setlocale( LC_CTYPE, get_locale() . '.UTF-8' );
			$handler = pathinfo( $stylesheet_path );
			$version = filemtime( $stylesheet_path );
			$url = themify_https_esc( self::get_stylesheet( 'byurl', $post_id ) );

			if( $return ) {
				remove_action( 'themify_builder_background_styling', array($this, 'render_styling'), 10, 4 );
				
				return array(
                                    'handler' => $handler['filename'],
                                    'url' => $url . '?ver=' . $version
                                );
			} else {
				wp_enqueue_style( $handler['filename'], $url, array(), $version);
			}

			return true;
		}
		
		return false;
	}


    /**
     * Enqueues Google Fonts
     * 
     * @since 2.2.6
     */
    public function enqueue_fonts($google_fonts) {
        $entry_google_fonts = get_option('themify_builder_google_fonts');
        if (!empty($entry_google_fonts) && is_array($entry_google_fonts)) {
            $entry_id = Themify_Builder_Model::get_ID();
            if (isset($entry_google_fonts[$entry_id])) {
                $fonts = explode('|', $entry_google_fonts[$entry_id]);
                $fonts = array_unique(array_filter($fonts));
                foreach ($fonts as $font) {
                    $google_fonts[] = $font;
                }
            }
        }

        return $google_fonts;
    }

    /**
     * Make styling rules.
     * 
     * @access public
     * @param array $def 
     * @param array $settings 
     * @param boolean $empty 
     * @return array
     */
    public function make_styling_rules($def) {
        $result = array();
        if (empty($def)) {
            return $result;
        }

        foreach ($def as $option) {
            if ($option['type'] === 'multi') {
                $result = array_merge($result, $this->make_styling_rules($option['fields']));
            } elseif ($option['type'] === 'tabs') {
                foreach ($option['tabs'] as $tab) {
                    $result = array_merge($result, $this->make_styling_rules($tab['fields']));
                }
            } elseif (isset($option['prop']) && $option['type'] !== 'seperator') {
                unset($option['wrap_with_class'], $option['class'], $option['label']);
                $result[$option['id']] = $option;
                if ($option['prop'] === 'padding' || $option['prop'] === 'margin' || $option['prop'] === 'border') {
                    $is_border = $option['prop'] === 'border';
                    $values = array('top', 'right', 'bottom', 'left');
                    $borders = array('color', 'width', 'style');
                    foreach ($values as $v) {
                        if ($is_border) {
                            foreach ($borders as $b) {
                                $result[$option['id'] . '_' . $v . '_' . $b] = array(
                                    'selector' => $option['selector'],
                                    'prop' => $option['prop'] . '-' . $v
                                );
                            }
                        } else {
                            $result[$option['id'] . '_' . $v] = array(
                                'selector' => $option['selector'],
                                'prop' => $option['prop'] . '-' . $v
                            );
                        }
                    }
                    if ($is_border) {
                        foreach ($borders as $b) {
                            $result[$option['id'] . '_' . $b] = array(
                                'selector' => $option['selector'],
                                'prop' => $option['prop']
                            );
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Checks whether a file exists, can be loaded and is not empty.
     * 
     * @since 2.2.5
     * 
     * @param string $file_path Path in server to the file to check.
     * 
     * @return bool
     */
    private static function is_readable_and_not_empty($file_path = '') {
        return empty($file_path)?false:is_readable($file_path) && 0 !== filesize($file_path);
    }

    /**
     * Get gradient value.
     * 
     * @param string $string 
     * @return string
     */
    public static function get_gradient($settings, $key) {
        $result = '';
        if (isset($settings["$key-gradient"])) {

            $gradient = explode('|', $settings["$key-gradient"]);
           
            $type = isset($settings["$key-gradient-type"]) ? $settings["$key-gradient-type"] : 'linear';
            if ($type === 'radial') {
                $angle = isset($settings["$key-circle-radial"])?'circle':'';
            } else {
                $angle = isset($settings["$key-gradient-angle"]) ? $settings["$key-gradient-angle"] . 'deg' : '180deg';
            }
            $data = array();
            foreach ($gradient as $v) {
                $point = (int) $v . '%';
                $color = trim(str_replace($point, '', $v));
                $data[] = $color . ' ' . $point;
            }
            $data = implode(',', $data);
            $result = '';
            $vendors = array('-webkit-','-o-', '');
            if($angle!==''){
                $angle.=',';
            }
            foreach ($vendors as $v) {
                $result.='background-image:' . $v . $type . '-gradient(' . $angle. $data . ');' . PHP_EOL;
            } 
        }
        return $result;
    }

    /**
     * Converts color in hexadecimal format to RGB format.
     *
     * @since 1.9.6
     *
     * @param string $hex Color in hexadecimal format.
     * @return string Color in RGB components separated by comma.
     */
    public static function hex2rgb($hex) {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) === 3) {
            $r = substr($hex, 0, 1);
            $g = substr($hex, 1, 1);
            $b = substr($hex, 2, 1);
            $r = hexdec($r . $r);
            $g = hexdec($g . $g);
            $b = hexdec($b. $b);
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        return implode(',', array($r, $g, $b));
    }

    /**
     * Get RGBA color format from hex color
     *
     * @return string
     */
    public static function get_rgba_color($color) {
        if (strpos($color, 'rgba') !== false) {
            return $color;
        }
        $color = explode('_', $color);
        $opacity = isset($color[1]) && $color[1] !== '' ? $color[1] : '1';
        return $opacity >= 0 && $opacity !== '1' && $opacity !== '1.00' && $opacity !== '0.99' ? 'rgba(' . self::hex2rgb($color[0]) . ', ' . $opacity . ')' : ($color[0] !== '' ? ('#' . str_replace('#', '', $color[0])) : false);
    }

    public function get_module_rules() {
        check_ajax_referer('tb_load_nonce', 'tb_load_nonce');
        echo json_encode(Themify_Builder_model::get_elements_style_rules());
        wp_die();
    }

    public function set_ajax_styles() {
        check_ajax_referer('tb_load_nonce', 'tb_load_nonce');
        if (!empty($_POST['data'])) {
            $data = json_decode(stripslashes_deep($_POST['data']), true);
            $output = array('desktop' => array());
            $breakpoints = themify_get_breakpoints();
            foreach ($data as $i => $component) {
                $type = $component['type'];
                $style_id = '.themify_builder .tb_element_cid_' . $component['cid'];
                if($type==='subrow' || $type === 'column'){
                    $style_id='.themify_builder_content'.$style_id;
                }
                $style = $this->get_custom_styling($style_id, $type, $component['data'], 'array');
                if ($style) {
                    $output['desktop'] = array_merge($output['desktop'], $style);
                }
                foreach ($breakpoints as $k => $v) {
                    if (!empty($component['data']['breakpoint_' . $k])) {
                        $style = $this->get_custom_styling($style_id, $type, $component['data']['breakpoint_' . $k], 'array');
                        if ($style) {
                            if (!isset($output[$k])) {
                                $output[$k] = array();
                            }
                            $output[$k] = array_merge($style, $output[$k]);
                        }
                    }
                }
            }
            global $themify;
            if(!empty($themify->builder_google_fonts)){
                $output['fonts'] = $themify->builder_google_fonts;
            }
            echo wp_json_encode($output);
        }
        wp_die();
    }

    public function slider_live_styling() {
        check_ajax_referer('tb_load_nonce', 'nonce');
        $bg_slider_data = $_POST['tb_background_slider_data'];
        $row_or_col = array(
            'styling' => array(
                'background_slider' => urldecode($bg_slider_data['shortcode']),
                'background_type' => 'slider',
                'background_slider_mode' => $bg_slider_data['mode'],
                'background_slider_speed' => $bg_slider_data['speed'],
                'background_slider_size' => $bg_slider_data['size']
            )
        );
        do_action('themify_builder_background_slider', $row_or_col, $bg_slider_data['order'], $bg_slider_data['type']);
        wp_die();
    }
}
