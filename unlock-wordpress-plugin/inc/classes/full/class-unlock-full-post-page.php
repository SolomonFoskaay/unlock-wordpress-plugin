<?php
/**
 * Unlock full post/page class.
 *
 * @since 3.0.0
 *
 * @package unlock-protocol
 */

namespace Unlock_Protocol\Inc\Classes\full;

use Unlock_Protocol\Inc\Traits\Singleton;
use Unlock_Protocol\Inc\Unlock;

/**
 * Class Unlock_Full_Post_Page
 *
 * @since 3.0.0
 */
class Unlock_Full_Post_Page {

	use Singleton;




	/**
     *[Method 1]     
     * _construct() - This is the constructor method for the class,  
     * which sets up hooks using the setup_hooks() method.
	 *
	 * @since 3.0.0
	 */
	protected function __construct() {
        $this->setup_hooks();
	}




	/**
     * [Method 2]
     *  setup_hooks() - This method sets up hooks for the class.
	 *
	 * @since 3.0.0
	 */
	protected function setup_hooks() {
		/**
		 * Actions.
		 */
		add_action( 'init', array( $this, 'register_full_post_page_type' ) );
	}




	/**
	 * [Method 3]
     * register_block_type() 
     * - This method registers the "Unlock box" full post/page lock features
	 *
	 *  @since 3.0.0
	 */
	public function register_full_post_page_type() {
		register_full_post_page_type(
			'unlock-protocol/unlock-full-post-page',
			array(
				'render_callback' => array( $this, 'render_full_post_page' ),
				'attributes'      => array(
					'locks'      => array(
						'type'    => 'array',
						'default' => array(),
					),
					'ethereumNetworks' => array(
						'type'    => 'array',
						'default' => array(),
					),
				),
			)
		);
	}




/**
 * [Method 4]
 * render_full_post_page() - This method is responsible for rendering the full post/page content.
 * 
 * 1. It checks if the current user can manage options or is the author and returns the full post/page content if true.
 * 2. If the user is not logged in, it retrieves the login settings and returns the HTML template using the unlock_protocol_get_template() function for the user to login first.
 * 3. If the user is logged in (current user can not manage options or is not the author), it then do either of the two options below:
 *     3a. Retrieves the locks and networks attributes, checks if the user has access is true, and returns the full post/page content
 *     3b. If user does not have the lock ID NFT to unlock the content in (3a) above, it then shows the checkout URL using the get_checkout_url() method.
 *
 * @param array  $attributes List of attributes passed to full post/page.
 * @param string $content Full Post/Page Content.
 *
 * @since 3.0.0
 *
 * @return string HTML elements.
 */
public function render_full_post_page( $attributes, $content ) {
    $post_id = get_the_ID();

    // Bail out if current user is admin or the author.
    if ( current_user_can( 'manage_options' ) || ( get_post_field( 'post_author', $post_id ) === get_current_user_id() ) ) {
        return $content;
    }

    if (
        ! is_user_logged_in() ||
        ( is_user_logged_in() && ! up_get_user_ethereum_address() )
    ) {
        $login_button_text       = up_get_general_settings( 'login_button_text', __( 'Login with Unlock', 'unlock-protocol' ) );
        $login_button_bg_color   = up_get_general_settings( 'login_button_bg_color', '#000' );
        $login_button_text_color = up_get_general_settings( 'login_button_text_color', '#fff' );
        $blurred_image_activated = wp_validate_boolean( up_get_general_settings( 'login_blurred_image_button', false ) );

        $template_data = array(
            'login_url'               => Unlock::get_login_url( get_permalink() ),
            'login_button_text'       => $login_button_text,
            'login_button_bg_color'   => $login_button_bg_color,
            'login_button_text_color' => $login_button_text_color,
            'blurred_image_activated' => $blurred_image_activated,
        );

        // Fetching some more data if blurred image button type is activated.
        if ( $blurred_image_activated ) {
            $login_button_description = up_get_general_settings( 'login_blurred_image_description', __( 'You need to login with Unlock to view this content', 'unlock-protocol' ) );
            $template_data['login_blurred_image_description'] = $login_button_description;
        }

        $template = Unlock::get_template( 'login', $template_data );

        return $template;
    }

    $locks = $attributes['locks'] ?? array();
    $networks = $attributes['ethereumNetworks'] ?? array();

    if ( empty( $locks ) ) {
        return $content;
    }

    $can_access = false;
    $user_address = up_get_user_ethereum_address();

    foreach ( $locks as $lock ) {
        $lock_id = $lock['id'];
        $nft_address = $lock['nftAddress'];
        $network_id = $lock['network'];
        $network_name = Unlock::get_network_name( $network_id );

        // Check if user has access to lock ID NFT.
        if ( $this->unlock->is_unlocked( $nft_address, $lock_id, $user_address, $network_id ) ) {
            $can_access = true;
            break;
        }

        // Check if user has access to lock ID NFT through another network.
        if ( ! empty( $networks ) && $this->unlock->can_access_through_network( $nft_address, $lock_id, $user_address, $networks ) ) {
            $can_access = true;
            break;
        }
    }

    if ( $can_access ) {
        return $content;
    }

    // If user does not have access, show checkout URL.
    return $this->get_checkout_url( $attributes );
}





	/**
	 * [Method 5]
     * get_checkout_url() - This method generates the checkout URL for the block but it should be modified if need be to work same way but for full post/page.
     * Get checkout URL for full post/page.
	 *
	 * @param array $attributes attributes.
	 * @param array $networks networks from configuration.
	 *
	 * @return mixed|void
	 */
	private function get_checkout_url( $attributes ) {
		$checkout_url = Unlock::get_checkout_url( $attributes["locks"], get_permalink() );

		$checkout_button_text       = up_get_general_settings( 'checkout_button_text', __( 'Purchase this', 'unlock-protocol' ) );
		$checkout_button_bg_color   = up_get_general_settings( 'checkout_button_bg_color', '#000' );
		$checkout_button_text_color = up_get_general_settings( 'checkout_button_text_color', '#fff' );
		$blurred_image_activated    = wp_validate_boolean( up_get_general_settings( 'checkout_blurred_image_button', false ) );

		$template_data = array(
			'checkout_url'               => $checkout_url,
			'checkout_button_text'       => $checkout_button_text,
			'checkout_button_bg_color'   => $checkout_button_bg_color,
			'checkout_button_text_color' => $checkout_button_text_color,
			'blurred_image_activated'    => $blurred_image_activated,
		);

		// Fetching some more data if blurred image button type is activated.
		if ( $blurred_image_activated ) {
			$checkout_button_description = up_get_general_settings( 'checkout_button_description', __( 'To view this content please', 'unlock-protocol' ) );
			$checkout_bg_image           = up_get_general_settings( 'checkout_bg_image' );

			$template_data['checkout_button_description'] = $checkout_button_description;
			$template_data['checkout_bg_image']           = $checkout_bg_image;
		}

		$html_template = unlock_protocol_get_template( 'login/checkout-button', $template_data );

		return apply_filters( 'unlock_protocol_checkout_content', $html_template, $template_data );
	}




}

