<?php
/**
 * Registers assets for full post/page block and enqueue them.
 *
 * @package unlock-protocol
 */

namespace Unlock_Protocol\Inc;

use Unlock_Protocol\Inc\Blocks\Full_Post_Page_Block;
use Unlock_Protocol\Inc\Traits\Singleton;

/**
 * Class Full_Post_Page
 *
 * @since 3.0.0
 */
class Full_Post_Page {

	use Singleton;

	/**
	 * Construct method.
	 *
	 * @since 3.0.0
	 */
	protected function __construct() {

		$this->setup_hooks();
		Full_Post_Page_Block::get_instance();

	}

	/**
	 * Setup hooks.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function setup_hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	/**
	 * Enqueue assets.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		$this->enqueue_scripts();
		$this->enqueue_styles();
	}

	/**
	 * Enqueue scripts for full post/page block.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	private function enqueue_scripts() {
		// Automatically load dependencies and version.
		$asset_file = include UNLOCK_PROTOCOL_BUILD_DIR . '/js/full-post-page.asset.php';

		wp_enqueue_script(
			'unlock-protocol-full-post-page',
			UNLOCK_PROTOCOL_URL . '/assets/build/js/full-post-page.js',
			$asset_file['dependencies'],
			filemtime( UNLOCK_PROTOCOL_PATH . '/assets/build/js/full-post-page.js' ),
			true
		);
	}

	/**
	 * Enqueue styles for full post/page block.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	private function enqueue_styles() {
		wp_enqueue_style(
			'unlock-protocol-full-post-page',
			UNLOCK_PROTOCOL_URL . '/assets/build/css/full-post-page.css',
			array(),
			filemtime( UNLOCK_PROTOCOL_PATH . '/assets/build/css/full-post-page.css' )
		);
	}
}
