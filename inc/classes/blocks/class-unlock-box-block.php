<?php
/**
 * Unlock box dynamic block class.
 *
 * @since 3.0.0
 *
 * @package unlock-protocol
 */

namespace Unlock_Protocol\Inc\Blocks;

use Unlock_Protocol\Inc\Login;
use Unlock_Protocol\Inc\Traits\Singleton;
use Unlock_Protocol\Inc\Unlock;

/**
 * Class Unlock_Box_Block
 *
 * @since 3.0.0
 */
class Unlock_Box_Block {

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
		/**
		 * Actions.
		 */
		add_action( 'init', array( $this, 'register_block_type' ) );

	}

	/**
	 * Register block type.
	 *
	 * @since 3.0.0
	 */
	public function register_block_type() {
		register_block_type(
			'unlock-protocol/unlock-box',
			array(
				'render_callback' => array( $this, 'render_block' ),
				'attributes'      => array(
					'lockAddress'      => array(
						'type'    => 'string',
						'default' => '',
					),
					'ethereumNetworks' => array(
						'type'    => 'array',
						'default' => array(),
					),
					'ethereumNetwork'  => array(
						'type'    => 'string',
						'default' => '',
					),
				),
				'supports'        => array(
					'align' => true,
				),
			)
		);
	}

	/**
	 * Render block.
	 *
	 * @param array  $attributes List of attributes passed in block.
	 * @param string $content Block Content.
	 *
	 * @since 3.0.0
	 *
	 * @return string HTML elements.
	 */
	public function render_block( $attributes, $content ) {
		if ( ! is_user_logged_in() ) {
			$login = Login::get_instance();

			$login_button_text       = get_general_settings( 'login_button_text', __( 'Login with Unlock', 'unlock-protocol' ) );
			$login_button_bg_color   = get_general_settings( 'login_button_bg_color', '#000' );
			$login_button_text_color = get_general_settings( 'login_button_text_color', '#fff' );

			return unlock_protocol_get_template(
				'login/button',
				array(
					'login_url'               => $login->get_login_url(),
					'login_button_text'       => $login_button_text,
					'login_button_bg_color'   => $login_button_bg_color,
					'login_button_text_color' => $login_button_text_color,
				)
			);
		}

		$ethereum_network = $attributes['ethereumNetwork'];

		$user_ethereum_address = get_user_meta( get_current_user_id(), 'unlock_ethereum_address', true );

		if ( Unlock::has_access( $ethereum_network, $attributes['lockAddress'], $user_ethereum_address ) ) {
			return $content;
		}

		$checkout_url = Unlock::get_checkout_url( $attributes['lockAddress'], 4, get_permalink() );

		$checkout_button_text       = get_general_settings( 'checkout_button_text', __( 'Purchase this', 'unlock-protocol' ) );
		$checkout_button_bg_color   = get_general_settings( 'checkout_button_bg_color', '#000' );
		$checkout_button_text_color = get_general_settings( 'checkout_button_text_color', '#fff' );

		return unlock_protocol_get_template(
			'login/checkout-button',
			array(
				'checkout_url'               => $checkout_url,
				'checkout_button_text'       => $checkout_button_text,
				'checkout_button_bg_color'   => $checkout_button_bg_color,
				'checkout_button_text_color' => $checkout_button_text_color,
			)
		);
	}
}
