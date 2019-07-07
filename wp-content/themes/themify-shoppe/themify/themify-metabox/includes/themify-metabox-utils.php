<?php

/**
 * Takes an array of options and return a one dimensional array of all the field names
 *
 * @return array
 * @since 1.0.2
 */
function themify_metabox_get_field_names( $arr ) {
	$list = array();
	if( ! empty( $arr ) ) : foreach( $arr as $metabox ) :
		if( ! empty( $metabox['options'] ) ) {
			$list = array_merge( $list, wp_list_pluck( themify_metabox_make_flat_fields_array( $metabox['options'] ), 'name' ) );
		}
	endforeach; endif;

	return apply_filters( 'themify_metabox_get_field_names', array_unique( $list ), $arr );
}

/**
 * Takes an options array and returns a one-dimensional list of fields
 *
 * @return array
 * @since 1.0.2
 */
function themify_metabox_make_flat_fields_array( $arr ) {
	$list = array();
	foreach( $arr as $key => $field ) {
		if( $field['type'] === 'multi' ) {
			foreach( $field['meta']['fields'] as  $_field ) {
				$list[] = $_field;
			}
		} else {
			$list[] = $field;
		}
	}

	return $list;
}

/**
 * Check if assignments are applied in the current context
 *
 * @since 1.0
 */
function themify_verify_assignments( $assignments ) {
	$visible = true;
	$query_object = get_queried_object();

	if ( ! empty( $assignments['roles'] ) && ! in_array( $GLOBALS['current_user']->roles[0], array_keys( $assignments['roles'] ) )) {
		return false; // bail early.
	}
	unset( $assignments['roles'] );

	if ( ! empty($assignments ) ) {
		$visible = false; // if any condition is set for a hook, hide it on all pages of the site except for the chosen ones.

		if (
			( isset($assignments['general']['home'])  && is_front_page()  )
			|| ( isset( $assignments['general']['page'] ) && is_page() && ! is_front_page() )
			|| ( isset($assignments['general']['single']) && is_single())
			|| ( isset($assignments['general']['search']) && is_search() )
			|| ( isset($assignments['general']['author']) && is_author() )
			|| ( isset($assignments['general']['category']) && is_category())
			|| ( isset($assignments['general']['tag']) && is_tag() )
			|| ( isset($query_object->post_type,$assignments['general'][$query_object->post_type]) && is_singular() && $query_object->post_type !== 'page' && $query_object->post_type !== 'post' )
			|| ( isset($query_object->taxonomy,$assignments['general'][$query_object->taxonomy]) && is_tax()  )
		) {
			$visible = true;
		} else { // let's dig deeper into more specific visibility rules
			if ( ! empty( $assignments['tax'] ) ) {
				if ( is_single() ) {
					if (  ! empty( $assignments['tax']['category_single'] ) ) {
						$cat = get_the_category();
						if ( ! empty( $cat ) ) {
							foreach ( $cat as $c ) {
								if ( $c->taxonomy === 'category' && isset( $assignments['tax']['category_single'][$c->slug] ) ) {
									$visible = true;
									break;
								}
							}
						}
					}
				} else {
					foreach ( $assignments['tax'] as $tax => $terms ) {
						$terms = array_keys( $terms );
						if ( ( $tax === 'category' && is_category($terms) ) || ( $tax === 'post_tag' && is_tag( $terms ) ) || ( is_tax( $tax, $terms ) )
						) {
							$visible = true;
							break;
						}
					}
				}
			}
			if ( $visible===false  && ! empty( $assignments['post_type'] ) ) {
				foreach ( $assignments['post_type'] as $post_type => $posts ) {
					$posts = array_keys( $posts );
					if ( ( $post_type === 'post' && is_single($posts) ) || ( $post_type === 'page' && (
							( is_page( $posts ) ) || ( ! is_front_page() && is_home() && in_array( get_post_field( 'post_name', get_option('page_for_posts' ) ), $posts ) ) // check for Posts page
							) ) || ( is_singular( $post_type ) && in_array( $query_object->post_name, $posts,true ) )
					) {
						$visible = true;
						break;
					}
				}
			}
		}
	}

	return $visible;
}