<?php

/**
 * Plugin Name: WT Role Plugin
 * Plugin URI: https://oktaryan.com/wprl
 * Description: WT Role Plugin on SS
 * Version: 1.0
 * Author: Oktaryan Nh
 * Author URI: https://oktaryan.com
 */

class WT_Role {

	function install() {

		// remove_role('staff');
		// remove_role('manager');

		$capability = array(
			// 'edit_users'	=> true,
			'read'			=> true,  // true allows this capability
			'edit_posts'	=> true,
			// 'delete_posts'	=> false, // Use false to explicitly deny
		);

		add_role( 'staff', __( 'Staff' ), $capability );

		$capability = array(
			'read'			=> true,
			'edit_posts'	=> true,
			'list_users'	=> true,
			'create_users'	=> true,
			'add_users'		=> true,
			'edit_users'	=> true,
			// 'promote_users'	=> true,
			'remove_users'	=> true,
			'delete_users'	=> true,
		);
		add_role( 'manager', __( 'Manager' ), $capability );
	}

	/**
	 * The HTML form code to display in user form.
	 */
	public function html_form_code( $atts ) {

		var_dump($atts);

		if ( empty( $atts ) ) {
			$role_in = array( 'manager', 'staff' );
		}
		else {
			$attribute = shortcode_atts(
				array(
					'manager' => 'false',
					'staff' => 'false',
				),
				$atts
			);

			$role_in = array();

			foreach ( $attribute as $i => $j ) {
				if  ( 'true' == $j ) {
					$role_in[] = $i;
				}
			}
		}



		$paged = ( isset( $_GET['up']) ) ? $_GET['up'] : 1;
		$limit = 1;

		$args = array(
			'role__in'	=> $role_in,
			'number'	=> $limit,
			'paged'		=> $paged,
		);

		$user_query = new WP_User_Query( $args );

		if ( ! empty( $user_query->get_results() ) ) {
			foreach ( $user_query->get_results() as $user ) {
				echo '<p>' . $user->display_name . '</p>';
			}

			echo paginate_links( array(
				'base' => '%_%',
				'format' => '?up=%#%',
				'current' => max( 1, $paged ),
				'total' => ceil($user_query->get_total()/$limit),
				// 'add_args' => 'input-text=' . $input_text,
			) );

		} else {
			echo 'No users found.';
		}
	}

	function shortcode( $atts ) {

		ob_start();

		$this->html_form_code( $atts );

		return ob_get_clean();
	}
}

$wt_role = new WT_Role;

register_activation_hook( __FILE__, array( $wt_role, 'install' ) );

add_shortcode( 'wp7_users', array( $wt_role, 'shortcode' ) );