<?php

if( !class_exists( 'forcereauthenticationadmin') ) {

	class forcereauthenticationadmin {

		function __construct() {

			// Add the row actions
			add_filter( 'user_row_actions', array( &$this, 'add_user_action' ), 99, 2 );
			add_filter( 'ms_user_row_actions', array( &$this, 'add_user_action' ), 99, 2 );

			add_filter( 'bulk_actions-users', array( &$this, 'bulk_add_action' ), 99 );
			add_filter( 'bulk_actions-users-network', array( &$this, 'bulk_add_action' ), 99 );

			// Process any action clicks
			add_action( 'load-users.php', array( &$this, 'process_user_queue_action' ) );

			// Output a notice confirming operation
			add_action( 'admin_notices', array( &$this, 'output_admin_notices' ) );
			add_action( 'network_admin_notices', array( &$this, 'output_admin_notices' ) );

		}

		function forcereauthenticationadmin() {
			$this->__construct();
		}

		function add_user_action( $actions, $user_object ) {

			return $actions;
		}

		function bulk_add_action( $actions ) {

			if ( current_user_can( 'delete_users' ) ) {
				$actions['bulkuserforcereauthenticate'] = __( 'Log Out', 'forcereauthentication' );
			}

			return $actions;
		}

		function current_action() {

			if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] )
				return $_REQUEST['action'];

			if ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] )
				return $_REQUEST['action2'];

			return false;

		}

		function process_user_queue_action() {

			$action = $this->current_action();

			if( $action ) {

				switch( $action ) {

					case 'userforcereauthenticate':			//check_admin_referer( 'action' );
															//wp_safe_redirect( add_query_arg( 'reauthenticationmsg', 1, wp_get_referer() ) );
															break;

					case 'bulkuserforcereauthenticate':		//check_admin_referer( 'action' );
															//wp_safe_redirect( add_query_arg( 'reauthenticationmsg', 3, wp_get_referer() ) );
															break;

				}

			}

			// This is to attempt to add in some bulk operations
			add_action( 'all_admin_notices',  array( &$this, 'start_object_to_modify_bulk'), 99 );

		}

		function add_modify_bulk( $content ) {

			$ouroption = "<option value='bulkuserforcereauthenticate'>" . __( 'Log Out', 'forcereauthentication' ) . "</option>\n";

			if( is_multisite() && is_network_admin() ) {
				//<option value='notspam'>Not Spam</option>
				$content = preg_replace( "/<option value='notspam'>" . __( 'Not Spam', 'user' ) . "<\/option>/", "<option value='notspam'>" . __( 'Not Spam', 'user' ) . "</option>\n" . $ouroption, $content );
			} else {
				if( is_multisite() ) {
					$content = preg_replace( "/<option value='remove'>" . __( 'Remove' ) . "<\/option>/", "<option value='remove'>" . __( 'Remove' ) . "</option>\n" . $ouroption, $content );
				} else {
					$content = preg_replace( "/<option value='remove'>" . __( 'Delete' ) . "<\/option>/", "<option value='remove'>" . __( 'Delete' ) . "</option>\n" . $ouroption, $content );
				}
			}


			return $content;

		}

		function start_object_to_modify_bulk() {

			// Start the object cache
			ob_start( array( &$this, 'add_modify_bulk' ) );

		}

		function output_admin_notices() {

			if(isset( $_GET['reauthenticationmsg'] )) {
				switch( $_GET['reauthenticationmsg'] ) {

					case 1:		echo '<div id="message" class="updated fade"><p>' . __('User .', 'forcereauthentication') . '</p></div>';
								break;

					case 2:		echo '<div id="message" class="error"><p>' . __('User could not be .', 'forcereauthentication') . '</p></div>';
								break;

					case 3:		echo '<div id="message" class="updated fade"><p>' . __('Users .', 'forcereauthentication') . '</p></div>';
								break;

					case 4:		echo '<div id="message" class="error"><p>' . __('Users could not be.', 'forcereauthentication') . '</p></div>';
								break;
				}
			}

		}

	}

}

$forcereauthenticationadmin = new forcereauthenticationadmin();