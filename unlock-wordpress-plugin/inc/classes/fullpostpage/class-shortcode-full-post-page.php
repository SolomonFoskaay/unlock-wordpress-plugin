<?php
/**
 * Shortcodes class file.
 *
 * @since 3.0.0
 *
 * @package unlock-protocol
 */

namespace Unlock_Protocol\Inc\Classes;

use Unlock_Protocol\Inc\Traits\Singleton;
use Unlock_Protocol\Inc\Blocks\Unlock_Box_Block;

/**
 * Class Shortcodes
 *
 * @since 3.0.0
 */
class Shortcodes {

	use Singleton;

	/**
	 * Construct method.
	 *
	 * @since 3.0.0
	 */
	protected function __construct() {
		$this->setup_hooks();
	}

	/**
	 * Setup hooks.
	 *
	 * @since 3.0.0
	 */
	protected function setup_hooks() {
		add_action( 'init', array( $this, 'register_shortcodes' ) );
	}

	/**
	 * Register shortcodes.
	 *
	 * @since 3.0.0
     * 
	 */
	public function register_shortcodes() {
		add_shortcode( 'unlock_block_content_lock', array( $this, 'block_content_lock_shortcode' ) );
	}

	/**
	 * Shortcode for Gutenberg Block Content Lock.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML elements.
	 *
	 * @since 3.0.0
     * 
	 * @param string $content The original post content.
     * 
	 */
	public function block_content_lock_shortcode( $atts, $content ) {
		$attributes = shortcode_atts(
			array(
				'locks'            => array(),
				'ethereumNetworks' => array(),
			),
			$atts
		);

		$unlock_box_block = Unlock_Box_Block::get_instance();

		return $unlock_box_block->render_block( $attributes, $content );
	}
}
