<?php

namespace SchemaScalpel;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Plugin load procedure.
 *
 * @link       https://schemascalpel.com/
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/includes
 * @author     Kevin Gillispie
 */
class Schema_Scalpel_Loader {


	/**
	 * WP actions.
	 *
	 * @access   protected
	 * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * WP filters.
	 *
	 * @access   protected
	 * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 */
	public function __construct() {

		$this->actions = array();
		$this->filters = array();
	}

	/**
	 * Initialize actions.
	 *
	 * @param string  $hook Hook name.
	 * @param string  $component Component name.
	 * @param string  $callback Callback function name.
	 * @param integer $priority Priority level.
	 * @param integer $accepted_args Argument count.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Initialize filters.
	 *
	 * @param string  $hook Hook name.
	 * @param string  $component Component name.
	 * @param string  $callback Callback function name.
	 * @param integer $priority Priority level.
	 * @param integer $accepted_args Argument count.
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add actions/filters.
	 *
	 * @param array   $hooks All hooks data.
	 * @param string  $hook Hook name.
	 * @param string  $component Component name.
	 * @param string  $callback Callback function name.
	 * @param integer $priority Priority level.
	 * @param integer $accepted_args Argument count.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {
		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $hooks;
	}

	/**
	 * Register the filters and actions with WordPress.
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
