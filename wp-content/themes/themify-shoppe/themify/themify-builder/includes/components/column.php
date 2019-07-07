<?php

class Themify_Builder_Component_Column extends Themify_Builder_Component_Base {

	public function get_name() {
		return 'column';
	}
        public function get_label(){
            return __('Column Styling', 'themify');
        }
        
        

	/**
	 * Get template column.
	 * 
	 * @param int $rows Row key
	 * @param array $row 
	 * @param array $cols 
	 * @param array $col 
	 * @param string $builder_id 
	 */
	public static function template( $rows, $row, $cols, $col, $builder_id, $order_classes = array(), $echo = false) {
		$grid_class = trim(str_replace(array('first','last'),array('',''),$col['grid_class']));
                $print_column_classes = array('module_column tb-column',$grid_class);
                $column_tag_attrs = array();
                $is_styling = !empty($col['styling']);
                if(!Themify_Builder::$frontedit_active){
                    if (isset( $order_classes[ $cols ] ) ){
                        $print_column_classes[] = $order_classes[ $cols ];
                    }
                    $print_column_classes[] = 'tb_' . $builder_id . '_column';
                    if (isset($col['column_order'])) {
                        $print_column_classes[] = 'module_column_' . $col['column_order'].' module_column_' . $builder_id . '-' . $row['row_order'] . '-' . $col['column_order'];
                    }
                }
                if($is_styling){
                    if (!empty($col['styling']['background_repeat'])) {
                            $print_column_classes[] = $col['styling']['background_repeat'];
                    }
                    if(isset($col['styling']['background_type']) && $col['styling']['background_type']==='image' && isset($col['styling']['background_zoom']) && $col['styling']['background_zoom']==='zoom' && $col['styling']['background_repeat']=='repeat-none'){
                            $print_column_classes[] = 'themify-bg-zoom';
                    }
                    if (!empty($col['styling']['custom_css_column'])) {
                            $print_column_classes[] = $col['styling']['custom_css_column'];
                    }
                    
                }
		$print_column_classes = implode(' ', $print_column_classes);
        $column_tag_attrs['class'] = esc_attr( $print_column_classes );

        if(!empty($col['grid_width']) && !Themify_Builder::$frontedit_active) {
            $column_tag_attrs['style'] = 'width: ' . $col['grid_width'] . '%';
        }

        if  ( isset( $col['element_id'] ) ) 
            $column_tag_attrs['data-id'] = $col['element_id'];

		// background video
        $video_data = $is_styling && Themify_Builder_Model::is_premium()?Themify_Builder_Include::get_video_background($col['styling']):'';

		if ( ! $echo ) {
			$output = PHP_EOL; // add line break
			ob_start();
		}

		// Start Column Render ######
		?>
        <div <?php echo self::get_element_attributes( $column_tag_attrs ); ?> <?php echo $video_data; ?>>
                    <?php
                        if ($is_styling) {
                                $column_order = $row['row_order'] . '-' . $col['column_order'];
                                do_action('themify_builder_background_styling',$builder_id,$col,$column_order,'column');	
                                self::show_frame($col['styling']);
                            }
                    ?>
                    <?php if (!empty($col['modules'])):?>
                        <div class="tb-column-inner">
                            <?php
                                foreach ($col['modules'] as $k => $mod) {
                                    if (isset($mod['mod_name'])) {
                                        $identifier = array($rows, $cols, $k); // define module id
                                        Themify_Builder_Component_Module::template($mod, $builder_id, true, $identifier);
                                    }
                                    if (!empty($mod['cols'])) {// Check for Sub-rows
                                        Themify_Builder_Component_SubRow::template( $rows, $cols, $k, $mod, $builder_id, true );	
                                    }
                                }
                            ?>
                        </div>
                    <?php endif;?>
		</div>
		<?php
		// End Column Render ######

		if ( ! $echo ) {
			$output .= ob_get_clean();
			// add line break
			$output .= PHP_EOL;
			return $output;
		}
                
	}
        
        

	/**
	 * Get template sub-column
	 * @param int|string $rows 
	 * @param int|string $cols 
	 * @param int|string $modules 
	 * @param int $col_key 
	 * @param array $sub_col 
	 * @param string $builder_id 
	 * @param boolean $echo 
	 */
	public static function template_sub_column( $rows, $cols, $modules, $col_key, $sub_col, $builder_id,$order_classes=array(), $echo = false ) {
		$print_sub_col_classes = array();
		$print_sub_col_classes[] = str_replace(array('first','last'),array('',''),$sub_col['grid_class']);
		$print_sub_col_classes[] = 'sub_column module_column';
                $is_styling = !empty($sub_col['styling']);
                if(!Themify_Builder::$frontedit_active){
                    if (isset( $order_classes[ $col_key ] ) ){
                        $print_sub_col_classes[] = $order_classes[ $col_key ];
                    }
                    $print_sub_col_classes[] = 'sub_column_post_'.$builder_id.' sub_column_' . $rows . '-' . $cols . '-' . $modules . '-' . $col_key;
                }
		$sub_row_class = 'sub_row_' . $rows . '-' . $cols . '-' . $modules;
                if($is_styling){
                    if (!empty($sub_col['styling']['background_repeat'])) {
                            $print_sub_col_classes[] = $sub_col['styling']['background_repeat'];
                    }
                    if (!empty($sub_col['styling']['custom_css_column'])) {
                            $print_sub_col_classes[] = $sub_col['styling']['custom_css_column'];
                    }
                    if(isset($sub_col['styling']['background_type']) && $sub_col['styling']['background_type']==='image' && isset($sub_col['styling']['background_zoom']) && $sub_col['styling']['background_zoom']==='zoom' && $sub_col['styling']['background_repeat']=='repeat-none'){
                        $print_sub_col_classes[] = 'themify-bg-zoom';
                    }
                }
		$print_sub_col_classes = implode(' ', $print_sub_col_classes);

		// background video
		$video_data = $is_styling && Themify_Builder_Model::is_premium()?' '.Themify_Builder_Include::get_video_background($sub_col['styling']):'';

		if ( ! $echo ) {
			$output = PHP_EOL; // add line break
			ob_start();
		}
		?>
               <div <?php echo !empty($sub_col['grid_width']) && !Themify_Builder::$frontedit_active?'style="width:'.$sub_col['grid_width'].'%;"':''?> class="<?php echo esc_attr($print_sub_col_classes)?>"<?php echo $video_data?>> 
                   <?php 
                        if ($is_styling) {
                            $sub_column_order = $rows . '-' . $cols . '-' . $modules . '-' . $col_key;
                            do_action('themify_builder_background_styling',$builder_id,$sub_col,$sub_column_order,'sub_column');
                            self::show_frame($sub_col['styling']);
                        }
                   ?>
                    <?php if (!empty($sub_col['modules'])):?>
                       <div class="tb-column-inner">
                            <?php 
                                foreach ($sub_col['modules'] as $sub_module_k => $sub_module) {
                                    $sub_identifier = array($sub_row_class, $col_key, $sub_module_k); // define module id
                                    Themify_Builder_Component_Module::template($sub_module, $builder_id, true,  $sub_identifier);
                                }
                            ?>
                        </div>
                    <?php endif;?>
               </div>
		<?php

		// End Sub-Column Render ######

		if ( ! $echo ) {
			$output .= ob_get_clean();
			// add line break
			$output .= PHP_EOL;
			return $output;
		}
	}
}