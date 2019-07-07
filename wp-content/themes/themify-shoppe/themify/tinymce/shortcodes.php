<?php

return array(
	'themify_author_box' => array(
		'label' => __( 'Author Box', 'themify' ),
		'fields' => array(
			array(
				'name' => 'avatar',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'yes', 'text' => __( 'Yes', 'themify' ) ),
					array( 'value' => 'no', 'text' => __( 'No', 'themify' ) ),
				),
				'label' => __( 'Author profile\'s avatar:', 'themify' ),
				'tooltip' => __( 'Default = yes', 'themify' )
			),
			array(
				'name' => 'avatar_size',
				'type' => 'textbox',
				'label' => __( 'Avatar image size:', 'themify' ),
				'tooltip' => __( 'Default = 48.', 'themify' )
			),
			array(
				'name' => 'author_link',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'yes', 'text' => __( 'Yes', 'themify' ) ),
					array( 'value' => 'no', 'text' => __( 'No', 'themify' ) ),
				),
				'label' => __( 'Show author profile link:', 'themify' ),
				'tooltip' => __( 'Default = no', 'themify' )
			),
			array(
				'name' => 'color',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'blue', 'text' => __( 'Blue', 'themify' ) ),
					array( 'value' => 'green', 'text' => __( 'Green', 'themify' ) ),
					array( 'value' => 'red', 'text' => __( 'Red', 'themify' ) ),
					array( 'value' => 'purple', 'text' => __( 'Purple', 'themify' ) ),
					array( 'value' => 'yellow', 'text' => __( 'Yellow', 'themify' ) ),
					array( 'value' => 'orange', 'text' => __( 'Orange', 'themify' ) ),
					array( 'value' => 'pink', 'text' => __( 'Pink', 'themify' ) ),
					array( 'value' => 'lavender', 'text' => __( 'Lavender', 'themify' ) ),
					array( 'value' => 'gray', 'text' => __( 'Gray', 'themify' ) ),
					array( 'value' => 'black', 'text' => __( 'Black', 'themify' ) ),
					array( 'value' => 'light-yellow', 'text' => __( 'Light Yellow', 'themify' ) ),
					array( 'value' => 'light-blue', 'text' => __( 'Light Blue', 'themify' ) ),
					array( 'value' => 'light-green', 'text' => __( 'Light Green', 'themify' ) ),
				),
				'label' => __( 'Color', 'themify' ),
			),
			array(
				'name' => 'icon',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'announcement', 'text' => __( 'Announcement', 'themify' ) ),
					array( 'value' => 'comment', 'text' => __( 'Comment', 'themify' ) ),
					array( 'value' => 'question', 'text' => __( 'Question', 'themify' ) ),
					array( 'value' => 'upload', 'text' => __( 'Upload', 'themify' ) ),
					array( 'value' => 'download', 'text' => __( 'Download', 'themify' ) ),
					array( 'value' => 'highlight', 'text' => __( 'Highlight', 'themify' ) ),
					array( 'value' => 'map', 'text' => __( 'Map', 'themify' ) ),
					array( 'value' => 'warning', 'text' => __( 'Warning', 'themify' ) ),
					array( 'value' => 'info', 'text' => __( 'Info', 'themify' ) ),
					array( 'value' => 'note', 'text' => __( 'Note', 'themify' ) ),
					array( 'value' => 'contact', 'text' => __( 'Contact', 'themify' ) ),
				),
				'label' => __( 'Icon', 'themify' ),
			),
			array(
				'name' => 'style',
				'type' => 'textbox',
				'label' => __( 'Additional Styles', 'themify' ),
			),
		),
	),
	'themify_box' => array(
		'label' => __( 'Box', 'themify' ),
		'fields' => array(
			array(
				'name' => 'color',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'blue', 'text' => __( 'Blue', 'themify' ) ),
					array( 'value' => 'green', 'text' => __( 'Green', 'themify' ) ),
					array( 'value' => 'red', 'text' => __( 'Red', 'themify' ) ),
					array( 'value' => 'purple', 'text' => __( 'Purple', 'themify' ) ),
					array( 'value' => 'yellow', 'text' => __( 'Yellow', 'themify' ) ),
					array( 'value' => 'orange', 'text' => __( 'Orange', 'themify' ) ),
					array( 'value' => 'pink', 'text' => __( 'Pink', 'themify' ) ),
					array( 'value' => 'lavender', 'text' => __( 'Lavender', 'themify' ) ),
					array( 'value' => 'gray', 'text' => __( 'Gray', 'themify' ) ),
					array( 'value' => 'black', 'text' => __( 'Black', 'themify' ) ),
					array( 'value' => 'light-yellow', 'text' => __( 'Light Yellow', 'themify' ) ),
					array( 'value' => 'light-blue', 'text' => __( 'Light Blue', 'themify' ) ),
					array( 'value' => 'light-green', 'text' => __( 'Light Green', 'themify' ) ),
				),
				'label' => __( 'Color', 'themify' ),
			),
			array(
				'name' => 'icon',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'announcement', 'text' => __( 'Announcement', 'themify' ) ),
					array( 'value' => 'comment', 'text' => __( 'Comment', 'themify' ) ),
					array( 'value' => 'question', 'text' => __( 'Question', 'themify' ) ),
					array( 'value' => 'upload', 'text' => __( 'Upload', 'themify' ) ),
					array( 'value' => 'download', 'text' => __( 'Download', 'themify' ) ),
					array( 'value' => 'highlight', 'text' => __( 'Highlight', 'themify' ) ),
					array( 'value' => 'map', 'text' => __( 'Map', 'themify' ) ),
					array( 'value' => 'warning', 'text' => __( 'Warning', 'themify' ) ),
					array( 'value' => 'info', 'text' => __( 'Info', 'themify' ) ),
					array( 'value' => 'note', 'text' => __( 'Note', 'themify' ) ),
					array( 'value' => 'contact', 'text' => __( 'Contact', 'themify' ) ),
				),
				'label' => __( 'Icon', 'themify' ),
			),
			array(
				'name' => 'style',
				'type' => 'textbox',
				'label' => __( 'Additional Styles', 'themify' ),
			),
		),
		'closing_tag' => true,
	),
	'themify_button' => array(
		'label' => __( 'Button', 'themify' ),
		'fields' => array(
			array(
				'name' => 'label',
				'type' => 'textbox',
				'label' => __( 'Button Text:', 'themify' ),
				'ignore' => true,
			),
			array(
				'name' => 'bgcolor',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'blue', 'text' => __( 'Blue', 'themify' ) ),
					array( 'value' => 'green', 'text' => __( 'Green', 'themify' ) ),
					array( 'value' => 'red', 'text' => __( 'Red', 'themify' ) ),
					array( 'value' => 'purple', 'text' => __( 'Purple', 'themify' ) ),
					array( 'value' => 'yellow', 'text' => __( 'Yellow', 'themify' ) ),
					array( 'value' => 'orange', 'text' => __( 'Orange', 'themify' ) ),
					array( 'value' => 'pink', 'text' => __( 'Pink', 'themify' ) ),
					array( 'value' => 'lavender', 'text' => __( 'Lavender', 'themify' ) ),
					array( 'value' => 'gray', 'text' => __( 'Gray', 'themify' ) ),
					array( 'value' => 'black', 'text' => __( 'Black', 'themify' ) ),
					array( 'value' => 'light-yellow', 'text' => __( 'Light Yellow', 'themify' ) ),
					array( 'value' => 'light-blue', 'text' => __( 'Light Blue', 'themify' ) ),
					array( 'value' => 'light-green', 'text' => __( 'Light Green', 'themify' ) ),
				),
				'label' => __( 'Color', 'themify' ),
			),
			array(
				'name' => 'size',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'small', 'text' => __( 'Small', 'themify' ) ),
					array( 'value' => 'large', 'text' => __( 'Large', 'themify' ) ),
					array( 'value' => 'xlarge', 'text' => __( 'xLarge', 'themify' ) ),
				),
				'label' => __( 'Size', 'themify' ),
			),
			array(
				'name' => 'link',
				'type' => 'textbox',
				'value' => 'http://',
				'label' => __( 'Button Link:', 'themify' )
			),
			array(
				'name' => 'target',
				'type' => 'textbox',
				'label' => __( 'Link Target:', 'themify' ),
				'tooltip' => sprintf( __( 'Entering %s will open link in a new window (leave blank for default).', 'themify' ), '<strong>_blank</strong>' )
			),
			array(
				'name' => 'color',
				'type' => 'colorbox',
				'value' => '',
				'label' => __( 'Custom Background Color:', 'themify' ),
				'tooltip' => __( 'Enter color in hexadecimal format. For example, #ddd.', 'themify' )
			),
			array(
				'name' => 'text',
				'type' => 'colorbox',
				'label' => __( 'Custom Button Text Color:', 'themify' ),
				'tooltip' => __( 'Enter color in hexadecimal format. For example, #000.', 'themify' )
			),
			array(
				'name' => 'block',
				'type' => 'checkbox',
				'label' => __( 'Block Style', 'themify' ),
			),
			array(
				'name' => 'style',
				'type' => 'textbox',
				'label' => __( 'Additional Styles', 'themify' ),
			),
		),
		'closing_tag' => true,
		'wrap_with' => '{{{data.label}}}',
	),
	'themify_columns' => array(
		'label' => __( 'Columns', 'themify' ),
		'menu' => array(
			'equal-half' => array(
				'label' => __( 'Equal Half', 'themify' ),
				'fields' => array(),
				'template' => '[themify_col grid="2-1 first"]{{{data.selectedContent}}}[/themify_col] [themify_col grid="2-1"][/themify_col]',
			),
			'equal-third' => array(
				'label' => __( 'Equal Third', 'themify' ),
				'fields' => array(),
				'template' => '[themify_col grid="3-1 first"]{{{data.selectedContent}}}[/themify_col] [themify_col grid="3-1"][/themify_col][themify_col grid="3-1"][/themify_col]',
			),
			'equal-four' => array(
				'label' => __( 'Equal Four', 'themify' ),
				'fields' => array(),
				'template' => '[themify_col grid="4-1 first"]{{{data.selectedContent}}}[/themify_col] [themify_col grid="4-1"][/themify_col] [themify_col grid="4-1"][/themify_col] [themify_col grid="4-1"][/themify_col]',
			),
			'double-n-halves' => array(
				'label' => __( 'Double and Halves', 'themify' ),
				'fields' => array(),
				'template' => '[themify_col grid="4-2 first"]{{{data.selectedContent}}}[/themify_col] [themify_col grid="4-1"][/themify_col] [themify_col grid="4-1"][/themify_col]',
			)
		),
	),
	'themify_slider' => array(
		'label' => __( 'Custom Slider', 'themify' ),
		'fields' => array(
			array(
				'name' => 'visible',
				'type' => 'textbox',
				'label' => __( 'Number of items visible at the same time:', 'themify' ),
				'tooltip' => __( 'Default = 1.', 'themify' )
			),
			array(
				'name' => 'scroll',
				'type' => 'textbox',
				'label' => __( 'Number of items to scroll:', 'themify' ),
				'tooltip' => __( 'Default = 1.', 'themify' )
			),
			array(
				'name' => 'auto',
				'type' => 'textbox',
				'label' => __( 'Auto play slider in number of seconds:', 'themify' ),
				'tooltip' => __( 'Default = 0, the slider will not auto play.', 'themify' )
			),
			array(
				'name' => 'wrap',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'yes', 'text' => __( 'Yes', 'themify' ) ),
					array( 'value' => 'no', 'text' => __( 'No', 'themify' ) ),
				),
				'label' => __( 'Wrap slider:', 'themify' ),
				'tooltip' => __( 'Default = yes, the slider will loop back to the first item', 'themify' )
			),
			array(
				'name' => 'speed',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'normal', 'text' => __( 'Normal', 'themify' ) ),
					array( 'value' => 'slow', 'text' => __( 'Slow', 'themify' ) ),
					array( 'value' => 'fast', 'text' => __( 'Fast', 'themify' ) ),
				),
				'label' => __( 'Animation speed:', 'themify' )
			),
			array(
				'name' => 'slider_nav',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'yes', 'text' => __( 'Yes', 'themify' ) ),
					array( 'value' => 'no', 'text' => __( 'No', 'themify' ) ),
				),
				'label' => __( 'Show slider navigation:', 'themify' ),
				'tooltip' => __( 'Default = yes.', 'themify' )
			),
			array(
				'name' => 'class',
				'type' => 'textbox',
				'label' => __( 'Custom CSS class name:', 'themify' )
			),
		),
		'closing_tag' => true,
		'wrap_with' => '[themify_slide]{{{data.selectedContent}}}[/themify_slide]',
	),
	'themify_flickr' => array(
		'label' => __( 'Flickr Gallery', 'themify' ),
		'fields' => array(
			array(
				'name' => 'user',
				'type' => 'textbox',
				'label' => __( 'Flickr ID:', 'themify' ),
				'tooltip' => sprintf( __( 'Example: 52839779@N02. Use %s to find your user ID', 'themify' ), '<a href="http://idgettr.com/" target="_blank">idGettr.com</a>' )
			),
			array(
				'name' => 'set',
				'type' => 'textbox',
				'label' => __( 'Flickr Set ID:', 'themify' )
			),
			array(
				'name' => 'group',
				'type' => 'textbox',
				'label' => __( 'Flickr Group ID:', 'themify' )
			),
			array(
				'name' => 'limit',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => '1', 'text' => '1' ),
					array( 'value' => '2', 'text' => '2' ),
					array( 'value' => '3', 'text' => '3' ),
					array( 'value' => '4', 'text' => '4' ),
					array( 'value' => '5', 'text' => '5' ),
					array( 'value' => '6', 'text' => '6' ),
					array( 'value' => '7', 'text' => '7' ),
					array( 'value' => '8', 'text' => '8' ),
					array( 'value' => '9', 'text' => '9' ),
					array( 'value' => '10', 'text' => '10' ),
				),
				'label' => __( 'Number of items to show:', 'themify' ),
				'tooltip' => __( 'Default = 8.', 'themify' )
			),
			array(
				'name' => 'size',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 's', 'text' => 's' ),
					array( 'value' => 't', 'text' => 't' ),
					array( 'value' => 'm', 'text' => 'm' ),
				),
				'label' => __( 'Photo Size:', 'themify' ),
				'tooltip' => __( 'Enter s, t or m. Default = s.', 'themify' )
			),
			array(
				'name' => 'display',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'latest', 'text' => __( 'Latest', 'themify' ) ),
					array( 'value' => 'latest', 'text' => __( 'Random', 'themify' ) ),
				),
				'label' => __( 'Display:', 'themify' ),
				'tooltip' => __( 'Display latest photos or random (default = latest)', 'themify' )
			)
		),
	),
	'themify_hr' => array(
		'label' => __( 'Horizontal Rule', 'themify' ),
		'fields' => array(
			array(
				'name' => 'color',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'pink', 'text' => __( 'Pink', 'themify' ) ),
					array( 'value' => 'red', 'text' => __( 'Red', 'themify' ) ),
					array( 'value' => 'light-gray', 'text' => __( 'Light Gray', 'themify' ) ),
					array( 'value' => 'dark-gray', 'text' => __( 'Dark Gray', 'themify' ) ),
					array( 'value' => 'black', 'text' => __( 'Black', 'themify' ) ),
					array( 'value' => 'orange', 'text' => __( 'Orange', 'themify' ) ),
					array( 'value' => 'yellow', 'text' => __( 'Yellow', 'themify' ) ),
					array( 'value' => 'white', 'text' => __( 'White', 'themify' ) ),
				),
				'label' => __( 'Rule Color:', 'themify' ),
			),
			array(
				'name' => 'width',
				'type' => 'textbox',
				'label' => __( 'Horizontal Width (in px or %):', 'themify' ),
				'tooltip' => __( 'Example: 50px or 50%.', 'themify' )
			),
			array(
				'name' => 'border_width',
				'type' => 'textbox',
				'label' => __( 'Border Width (in px):', 'themify' ),
				'tooltip' => __( 'Example: 1px.', 'themify' )
			)
		),
	),
	'themify_icon' => array(
		'label' => __( 'Icon', 'themify' ),
		'fields' => array(
			array(
				'name'    => 'icon',
				'type'  => 'iconpicker',
				'text' => __( 'Pick', 'themify' ),
				'label' => __( 'Icon:', 'themify' )
			),
			array(
				'name'    => 'label',
				'type'  => 'textbox',
				'label' => __( 'Label:', 'themify' )
			),
			array(
				'name'    => 'link',
				'type'  => 'textbox',
				'value' => 'http://',
				'label' => __( 'Link:', 'themify' )
			),
			array(
				'name'    => 'style',
				'type'  => 'textbox',
				'label' => __( 'Style:', 'themify' ),
				'tooltip'  => __( 'Combine rounded, squared, small and large.', 'themify' ),
			),
			array(
				'name'    => 'icon_color',
				'type'  => 'colorbox',
				'label' => __( 'Icon Color:', 'themify' ),
				'tooltip'  => __( 'Enter color in hexadecimal format. For example, #ddd.', 'themify' )
			),
			array(
				'name'    => 'icon_bg',
				'type'  => 'colorbox',
				'label' => __( 'Background Color:', 'themify' ),
				'tooltip'  => __( 'Enter color in hexadecimal format. For example, #ddd.', 'themify' )
			),
		),
	),
	'themify_list' => array(
		'label' => __( 'Icon List', 'themify' ),
		'fields' => array(
			array(
				'name'    => 'icon',
				'type'  => 'iconpicker',
				'text' => __( 'Pick', 'themify' ),
				'label' => __( 'Icon:', 'themify' )
			),
			array(
				'name'    => 'icon_color',
				'type'  => 'colorbox',
				'label' => __( 'Icon Color:', 'themify' ),
				'tooltip'  => __( 'Enter color in hexadecimal format. For example, #ddd.', 'themify' )
			),
			array(
				'name'    => 'icon_bg',
				'type'  => 'colorbox',
				'label' => __( 'Background Color:', 'themify' ),
				'tooltip'  => __( 'Enter color in hexadecimal format. For example, #ddd.', 'themify' )
			),
			array(
				'name'    => 'style',
				'type'  => 'textbox',
				'label' => __( 'Style:', 'themify' ),
			),
		),
		'closing_tag' => true,
		'wrap_with' => '<ul><li><# if ( ! data.selectedContent ) { data.selectedContent = "&nbsp;"; } #>{{{data.selectedContent}}}</li></ul>',
	),
	'themify_is_guest' => array(
		'label' => __( 'Is Guest', 'themify' ),
		'fields' => array(),
		'closing_tag' => true,
	),
	'themify_is_logged_in' => array(
		'label' => __( 'Is Logged In', 'themify' ),
		'fields' => array(),
		'closing_tag' => true,
	),
	'themify_list_posts' => array(
		'label' => __( 'List Posts', 'themify' ),
		'fields' => array(
			array(
				'name' => 'style',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'list-post', 'text' => __( 'Post list', 'themify' ) ),
					array( 'value' => 'grid4', 'text' => __( 'Grid 4', 'themify' ) ),
					array( 'value' => 'grid3', 'text' => __( 'Grid 3', 'themify' ) ),
					array( 'value' => 'grid2', 'text' => __( 'Grid 2', 'themify' ) ),
					array( 'value' => 'grid2-thumb', 'text' => __( 'Grid 2 Thumb', 'themify' ) ),
					array( 'value' => 'list-thumb-image', 'text' => __( 'List Thumb', 'themify' ) ),
				),
				'label' => __( 'Layout Style:', 'themify' ),
				'tooltip' => __( 'Default = list-post.', 'themify' )
			),
			array(
				'name' => 'limit',
				'type' => 'textbox',
				'label' => __( 'Number of Posts to Query:', 'themify' ),
				'tooltip' => __( 'Default = 5', 'themify' )
			),
			array(
				'name' => 'category',
				'type' => 'textbox',
				'label' => __( 'Categories to include', 'themify' ),
				'tooltip' => __( 'Enter the category ID numbers (eg. 2,5,6) or leave blank for default (all categories). Use minus number to exclude category (eg. category=-1 will exclude category 1).', 'themify' )
			),
			array(
				'name' => 'order',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'DESC', 'text' => __( 'Descending', 'themify' ) ),
					array( 'value' => 'ASC', 'text' => __( 'Ascending', 'themify' ) ),
				),
				'label' => __( 'Post Order:', 'themify' ),
				'tooltip' => __( 'Default = descending.', 'themify' )
			),
			array(
				'name' => 'orderby',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'date', 'text' => __( 'Date', 'themify' ) ),
					array( 'value' => 'title', 'text' => __( 'Title', 'themify' ) ),
					array( 'value' => 'rand', 'text' => __( 'Random', 'themify' ) ),
					array( 'value' => 'author', 'text' => __( 'Author', 'themify' ) ),
					array( 'value' => 'comment_count', 'text' => __( 'Comments number', 'themify' ) ),
				),
				'label' => __( 'Sort Posts By:', 'themify' ),
				'tooltip' => __( 'Default = date.', 'themify' )
			),
			array(
				'name' => 'image',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'yes', 'text' => __( 'Yes', 'themify' ) ),
					array( 'value' => 'no', 'text' => __( 'No', 'themify' ) ),
				),
				'label' => __( 'Show Post Image:', 'themify' ),
				'tooltip' => __( 'Default = yes', 'themify' )
			),
			array(
				'name' => 'image_w',
				'type' => 'textbox',
				'label' => __( 'Post Image Width:', 'themify' ),
				'tooltip' => __( 'Example: 400px or 94%.', 'themify' )
			),
			array(
				'name' => 'image_h',
				'type' => 'textbox',
				'label' => __( 'Post Image Height:', 'themify' ),
				'tooltip' => __( 'Example: 400px or 94%.', 'themify' )
			),
			array(
				'name' => 'image_size',
				'type' => 'listbox',
				'values' => array(
					array( 'text' => '', 'value' => '' ),
					array( 'text' => __( 'Thumbnail', 'themify' ), 'value' => 'thumbnail' ),
					array( 'text' => __( 'Medium', 'themify' ), 'value' => 'medium' ),
					array( 'text' => __( 'Large', 'themify' ), 'value' => 'large' ),
					array( 'text' => __( 'Original', 'themify' ), 'value' => 'full' ),
				),
				'label' => __( 'Post Image Size:', 'themify' ),
				'tooltip' => __( 'Use this if you have disabled the image script', 'themify' )
			),
			array(
				'name' => 'title',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'yes', 'text' => __( 'Yes', 'themify' ) ),
					array( 'value' => 'no', 'text' => __( 'No', 'themify' ) ),
				),
				'label' => __( 'Show Post Title:', 'themify' ),
				'tooltip' => __( 'Default = yes', 'themify' )
			),
			array(
				'name' => 'display',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'content', 'text' => __( 'Content', 'themify' ) ),
					array( 'value' => 'excerpt', 'text' => __( 'Excerpt', 'themify' ) ),
					array( 'value' => 'none', 'text' => __( 'None', 'themify' ) ),
				),
				'label' => __( 'Show Post Text:', 'themify' ),
				'tooltip' => __( 'Default = none, neither content or excerpt are displayed.', 'themify' )
			),
			array(
				'name' => 'post_meta',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'yes', 'text' => __( 'Yes', 'themify' ) ),
					array( 'value' => 'no', 'text' => __( 'No', 'themify' ) ),
				),
				'label' => __( 'Show Post Meta:', 'themify' ),
				'tooltip' => __( 'Default = no.', 'themify' )
			),
			array(
				'name' => 'more_text',
				'type' => 'textbox',
				'label' => __( 'More Text:', 'themify' ),
				'tooltip' => __( 'Only available if display=content and post has more tag.', 'themify' )
			),
			array(
				'name' => 'post_date',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'yes', 'text' => __( 'Yes', 'themify' ) ),
					array( 'value' => 'no', 'text' => __( 'No', 'themify' ) ),
				),
				'label' => __( 'Show Post Date:', 'themify' ),
				'tooltip' => __( 'Default = no.', 'themify' )
			),
		),
	),
	'themify_map' => array(
		'label' => __( 'Map', 'themify' ),
		'fields' => array(
			array(
				'name' => 'address',
				'type' => 'textbox',
				'label' => __( 'Location Address:', 'themify' ),
				'tooltip' => __( 'Example: 238 Street Ave., Toronto, Ontario, Canada', 'themify' )
			),
			array(
				'name' => 'width',
				'type' => 'textbox',
				'label' => __( 'Map Width (in px or %):', 'themify' ),
				'tooltip' => __( 'Example: 400px or 94%.', 'themify' )
			),
			array(
				'name' => 'height',
				'type' => 'textbox',
				'label' => __( 'Map Height (in px):', 'themify' ),
				'tooltip' => __( 'Example: 400px.', 'themify' )
			),
			array(
				'name' => 'zoom',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => '1', 'text' => '1' ),
					array( 'value' => '2', 'text' => '2' ),
					array( 'value' => '3', 'text' => '3' ),
					array( 'value' => '4', 'text' => '4' ),
					array( 'value' => '5', 'text' => '5' ),
					array( 'value' => '6', 'text' => '6' ),
					array( 'value' => '7', 'text' => '7' ),
					array( 'value' => '8', 'text' => '8' ),
					array( 'value' => '9', 'text' => '9' ),
					array( 'value' => '10', 'text' => '10' ),
					array( 'value' => '11', 'text' => '11' ),
					array( 'value' => '12', 'text' => '12' ),
					array( 'value' => '13', 'text' => '13' ),
					array( 'value' => '14', 'text' => '14' ),
					array( 'value' => '15', 'text' => '15' ),
					array( 'value' => '16', 'text' => '16' ),
					array( 'value' => '17', 'text' => '17' ),
					array( 'value' => '18', 'text' => '18' ),
					array( 'value' => '19', 'text' => '19' ),
					array( 'value' => '20', 'text' => '20' ),
				),
				'label' => __( 'Map Zoom Level:', 'themify' ),
				'tooltip' => __( 'Default = 8', 'themify' )
			),
			array(
				'name' => 'type',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'roadmap', 'text' => __( 'Road map', 'themify' ) ),
					array( 'value' => 'satellite', 'text' => __( 'Satellite', 'themify' ) ),
					array( 'value' => 'hybrid', 'text' => __( 'Hybrid', 'themify' ) ),
					array( 'value' => 'terrain', 'text' => __( 'Terrain', 'themify' ) ),
				),
				'label' => __( 'Map Type:', 'themify' ),
				'tooltip' => __( 'Default = Road Map', 'themify' ),
			),
		),
	),
	'themify_post_slider' => array(
		'label' => __( 'Post Slider', 'themify' ),
		'fields' => array(
			array(
				'name' => 'limit',
				'type' => 'textbox',
				'label' => __( 'Number of Posts to Query:', 'themify' ),
				'tooltip' => __( 'Default = 5', 'themify' )
			),
			array(
				'name' => 'category',
				'type' => 'textbox',
				'label' => __( 'Categories to include', 'themify' ),
				'tooltip' => __( 'Enter the category ID numbers (eg. 2,5,6) or leave blank for default (all categories). Use minus number to exclude category (eg. category=-1 will exclude category 1).', 'themify' )
			),
			array(
				'name' => 'order',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'ASC', 'text' => __( 'Descending', 'themify' ) ),
					array( 'value' => 'DESC', 'text' => __( 'Ascending', 'themify' ) ),
				),
				'label' => __( 'Post Order:', 'themify' ),
				'tooltip' => __( 'Default = descending.', 'themify' )
			),
			array(
				'name' => 'orderby',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'date', 'text' => __( 'Date', 'themify' ) ),
					array( 'value' => 'title', 'text' => __( 'Title', 'themify' ) ),
					array( 'value' => 'rand', 'text' => __( 'Random', 'themify' ) ),
					array( 'value' => 'author', 'text' => __( 'Author', 'themify' ) ),
					array( 'value' => 'comment_count', 'text' => __( 'Comments number', 'themify' ) ),
				),
				'label' => __( 'Sort Posts By:', 'themify' ),
				'tooltip' => __( 'Default = date.', 'themify' )
			),
			array(
				'name' => 'image',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'yes', 'text' => __( 'Yes', 'themify' ) ),
					array( 'value' => 'no', 'text' => __( 'No', 'themify' ) ),
				),
				'label' => __( 'Show Post Image:', 'themify' ),
				'tooltip' => __( 'Default = yes', 'themify' )
			),
			array(
				'name' => 'image_w',
				'type' => 'textbox',
				'label' => __( 'Post Image Width:', 'themify' ),
				'tooltip' => __( 'Example: 400px or 94%.', 'themify' )
			),
			array(
				'name' => 'image_h',
				'type' => 'textbox',
				'label' => __( 'Post Image Height:', 'themify' ),
				'tooltip' => __( 'Example: 400px or 94%.', 'themify' )
			),
			array(
				'name' => 'image_size',
				'type' => 'listbox',
				'values' => array(
					array( 'text' => '', 'value' => '' ),
					array( 'text' => __( 'Thumbnail', 'themify' ), 'value' => 'thumbnail' ),
					array( 'text' => __( 'Medium', 'themify' ), 'value' => 'medium' ),
					array( 'text' => __( 'Large', 'themify' ), 'value' => 'large' ),
					array( 'text' => __( 'Original', 'themify' ), 'value' => 'full' ),
				),
				'label' => __( 'Post Image Size:', 'themify' ),
				'tooltip' => __( 'Use this if you have disabled the image script', 'themify' )
			),
			array(
				'name' => 'title',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'yes', 'text' => __( 'Yes', 'themify' ) ),
					array( 'value' => 'no', 'text' => __( 'No', 'themify' ) ),
				),
				'label' => __( 'Show Post Title:', 'themify' ),
				'tooltip' => __( 'Default = yes', 'themify' )
			),
			array(
				'name' => 'display',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'content', 'text' => __( 'Content', 'themify' ) ),
					array( 'value' => 'excerpt', 'text' => __( 'Excerpt', 'themify' ) ),
				),
				'label' => __( 'Show Post Text:', 'themify' ),
				'tooltip' => __( 'Default = none, neither content or excerpt are displayed.', 'themify' )
			),
			array(
				'name' => 'post_meta',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'yes', 'text' => __( 'Yes', 'themify' ) ),
					array( 'value' => 'no', 'text' => __( 'No', 'themify' ) ),
				),
				'label' => __( 'Show Post Meta:', 'themify' ),
				'tooltip' => __( 'Default = no.', 'themify' )
			),
			array(
				'name' => 'more_text',
				'type' => 'textbox',
				'label' => __( 'More Text:', 'themify' ),
				'tooltip' => __( 'Only available if display=content and post has more tag.', 'themify' )
			),
			array(
				'name' => 'visible',
				'type' => 'textbox',
				'label' => __( 'Number of posts visible at the same time:', 'themify' ),
				'tooltip' => __( 'Default = 1.', 'themify' )
			),
			array(
				'name' => 'scroll',
				'type' => 'textbox',
				'label' => __( 'Number of items to scroll:', 'themify' ),
				'tooltip' => __( 'Default = 1.', 'themify' )
			),
			array(
				'name' => 'auto',
				'type' => 'textbox',
				'label' => __( 'Auto play slider in number of seconds:', 'themify' ),
				'tooltip' => __( 'Default = 0, the slider will not auto play.', 'themify' )
			),
			array(
				'name' => 'wrap',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'yes', 'text' => __( 'Yes', 'themify' ) ),
					array( 'value' => 'no', 'text' => __( 'No', 'themify' ) ),
				),
				'label' => __( 'Wrap slider posts:', 'themify' ),
				'tooltip' => __( 'Default = yes, the slider will loop back to the first item', 'themify' )
			),
			array(
				'name' => 'speed',
				'type' => 'listbox',
				'options' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'normal', 'text' => __( 'Normal', 'themify' ) ),
					array( 'value' => 'slow', 'text' => __( 'Slow', 'themify' ) ),
					array( 'value' => 'fast', 'text' => __( 'Fast', 'themify' ) ),
				),
				'label' => __( 'Animation speed:', 'themify' )
			),
			array(
				'name' => 'slider_nav',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'yes', 'text' => __( 'Yes', 'themify' ) ),
					array( 'value' => 'no', 'text' => __( 'No', 'themify' ) ),
				),
				'label' => __( 'Show slider navigation:', 'themify' ),
				'tooltip' => __( 'Default = yes.', 'themify' )
			),
			array(
				'name' => 'width',
				'type' => 'textbox',
				'label' => __( 'Slider div tag width:', 'themify' )
			),
			array(
				'name' => 'height',
				'type' => 'textbox',
				'label' => __( 'Slider div tag height:', 'themify' )
			),
			array(
				'name' => 'class',
				'type' => 'textbox',
				'label' => __( 'Custom CSS class name:', 'themify' )
			),
		),
	),
	'themify_quote' => array(
		'label' => __( 'Quote', 'themify' ),
		'fields' => array(),
		'closing_tag' => true,
	),
	'themify_twitter' => array(
		'label' => __( 'Twitter Stream', 'themify' ),
		'fields' => array(
			array(
				'name' => 'username',
				'type' => 'textbox',
				'label' => __( 'Twitter username:', 'themify' ),
				'tooltip' => __( 'Example: themify', 'themify' )
			),
			array(
				'name' => 'show_count',
				'type' => 'textbox',
				'label' => __( 'Number of tweets to show:', 'themify' )
			),
			array(
				'name' => 'show_timestamp',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'yes', 'text' => __( 'Yes', 'themify' ) ),
					array( 'value' => 'no', 'text' => __( 'No', 'themify' ) ),
				),
				'label' => __( 'Show tweet date and time:', 'themify' )
			),
			array(
				'name' => 'show_follow',
				'type' => 'listbox',
				'values' => array(
					array( 'value' => '', 'text' => '' ),
					array( 'value' => 'yes', 'text' => __( 'Yes', 'themify' ) ),
					array( 'value' => 'no', 'text' => __( 'No', 'themify' ) ),
				),
				'label' => __( 'Show a link to your account:', 'themify' )
			),
			array(
				'name' => 'follow_text',
				'type' => 'textbox',
				'label' => __( 'Text linked to your Twitter account:', 'themify' )
			)
		),
	),
);