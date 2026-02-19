<?php
/**
 * Hook registration loader for the Login Logout Redirect plugin.
 *
 * @package Wbcom_Redirect
 * @since   2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wbcom_Redirect_Loader
 *
 * Collects actions and filters, then registers them with WordPress on run().
 *
 * @since 2.1.0
 */
class Wbcom_Redirect_Loader {

	/**
	 * Registered actions.
	 *
	 * @var array
	 */
	protected $actions = array();

	/**
	 * Registered filters.
	 *
	 * @var array
	 */
	protected $filters = array();

	/**
	 * Queue an action hook.
	 *
	 * @param string $hook          WordPress hook name.
	 * @param object $component     Object instance containing the callback.
	 * @param string $callback      Method name on the component.
	 * @param int    $priority      Hook priority.
	 * @param int    $accepted_args Number of accepted arguments.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions[] = compact( 'hook', 'component', 'callback', 'priority', 'accepted_args' );
	}

	/**
	 * Queue a filter hook.
	 *
	 * @param string $hook          WordPress hook name.
	 * @param object $component     Object instance containing the callback.
	 * @param string $callback      Method name on the component.
	 * @param int    $priority      Hook priority.
	 * @param int    $accepted_args Number of accepted arguments.
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters[] = compact( 'hook', 'component', 'callback', 'priority', 'accepted_args' );
	}

	/**
	 * Register all queued actions and filters with WordPress.
	 */
	public function run() {
		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}
		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}
	}
}
