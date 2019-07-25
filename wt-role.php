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
	public function html_show_users( $atts ) {

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

		//$paged = ( isset( $_GET['up']) ) ? $_GET['up'] : 1;

		if( is_front_page() OR is_single() ) {
			$paged = ( get_query_var('page') ) ? get_query_var('page') : 1;
		}else {
			$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
		}

		//$paged = ( get_query_var('page') ) ? get_query_var('page') : 1;

		//echo 'PG '; var_dump($paged);

		$limit = 1;

		$args = array(
			'role__in'	=> $role_in,
			'number'	=> $limit,
			'offset'	=> $limit * ($paged - 1),
			'paged'		=> $paged,
		);

		$user_query = new WP_User_Query( $args );

		if ( ! empty( $user_query->get_results() ) ) {

			$total_users = $user_query->get_total();
			
			printf( _n( 'Found %s person', 'Found %s people', $total_users, 'text-domain' ), number_format_i18n( $total_users ) );

			foreach ( $user_query->get_results() as $user ) {
				echo '<p>' . $user->display_name . '</p>';
				//var_dump($user);
			}

			if ( is_single() ) {
				$base = get_permalink().'%#%/';
			}
			else {
				$big = 6950;
				$base = str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) );
			}

			//echo get_pagenum_link($big);

			echo paginate_links( array(
				'base' => $base,
				'format' => '?paged=%#%',
				'current' => max( 1, $paged ),
				'total' => ceil( $total_users / $limit ),
				'prev_text' => __( '<< Calm Down' ),
				'next_text' => __( 'Go Loud >>' ),
				// 'add_args' => 'input-text=' . $input_text,
			) );

		} else {
			echo 'No users found.';
		}
	}

	function shortcode( $atts ) {

		ob_start();

		$this->html_show_users( $atts );

		return ob_get_clean();
	}
}

$wt_role = new WT_Role;

register_activation_hook( __FILE__, array( $wt_role, 'install' ) );

add_shortcode( 'wp7_users', array( $wt_role, 'shortcode' ) );