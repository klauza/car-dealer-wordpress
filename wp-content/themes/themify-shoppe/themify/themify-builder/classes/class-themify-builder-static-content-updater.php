<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Async_Request', false ) ) {
	include_once( THEMIFY_BUILDER_INCLUDES_DIR . '/libraries/wp-async-request.php' );
}

if ( ! class_exists( 'WP_Background_Process', false ) ) {
	include_once( THEMIFY_BUILDER_INCLUDES_DIR . '/libraries/wp-background-process.php' );
}

class Themify_Builder_Static_Content_Updater extends WP_Background_Process {

	/**
	 * @var string
	 */
	protected $action = 'themify_builder_static_content_updater';

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function task( $item ) {
		global $ThemifyBuilder_Data_Manager;

		$ThemifyBuilder_Data_Manager->run_static_content_updater( $item );

		return false;
	}

	/**
	 * Is the updater running?
	 * @return boolean
	 */
	public function is_updating() {
		return false === $this->is_queue_empty();
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();

		// Show notice to user or perform some other arbitrary task...
		update_option( 'themify_builder_static_content_done', 'yes' );
	}

}