<?php
/**
 * Plugin loader class – registers actions and filters with WordPress.
 *
 * Provides a clean, reusable way to organize and register all plugin hooks
 * (actions and filters) in one central place. Inspired by the loader pattern
 * used in the official WordPress Plugin Boilerplate.
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/includes
 * @author     Kevin Gillispie
 * @link       https://schemascalpel.com/
 * @since      1.0.0
 */

namespace SchemaScalpel;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Responsible for registering all actions and filters for the plugin.
 *
 * Maintains internal arrays of hooks that are then registered with WordPress
 * via the run() method.
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/includes
 * @since      1.0.0
 */
class SCSC_Loader {

	/**
	 * Collection of actions to register.
	 *
	 * @since 1.0.0
	 * @var   array[]
	 */
	protected array $actions = array();

	/**
	 * Collection of filters to register.
	 *
	 * @since 1.0.0
	 * @var   array[]
	 */
	protected array $filters = array();

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->actions = array();
		$this->filters = array();
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook          The name of the WordPress action that is being registered.
	 * @param object $component     A reference to the instance of the object on which the action is defined.
	 * @param string $callback      The name of the function/method you wish to be called.
	 * @param int    $priority      Optional. The priority at which the function should be fired. Default is 10.
	 * @param int    $accepted_args Optional. The number of arguments the function accepts. Default is 1.
	 */
	public function add_action(
		string $hook,
		object $component,
		string $callback,
		int $priority = 10,
		int $accepted_args = 1
	): void {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook          The name of the WordPress filter that is being registered.
	 * @param object $component     A reference to the instance of the object on which the filter is defined.
	 * @param string $callback      The name of the function/method you wish to be called.
	 * @param int    $priority      Optional. The priority at which the function should be fired. Default is 10.
	 * @param int    $accepted_args Optional. The number of arguments the function accepts. Default is 1.
	 */
	public function add_filter(
		string $hook,
		object $component,
		string $callback,
		int $priority = 10,
		int $accepted_args = 1
	): void {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Shared method used by add_action() and add_filter() to store hook data.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @param array  $hooks         The collection of hooks that is being registered (actions or filters).
	 * @param string $hook          The name of the WordPress hook.
	 * @param object $component     The object instance the callback belongs to.
	 * @param string $callback      The name of the callback function/method.
	 * @param int    $priority      The priority value.
	 * @param int    $accepted_args The number of arguments the callback accepts.
	 *
	 * @return array The updated collection of hooks.
	 */
	private function add(
		array $hooks,
		string $hook,
		object $component,
		string $callback,
		int $priority,
		int $accepted_args
	): array {
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
	 * Register all collected filters and actions with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function run(): void {
		foreach ( $this->filters as $hook_data ) {
			add_filter(
				$hook_data['hook'],
				array( $hook_data['component'], $hook_data['callback'] ),
				$hook_data['priority'],
				$hook_data['accepted_args']
			);
		}

		foreach ( $this->actions as $hook_data ) {
			add_action(
				$hook_data['hook'],
				array( $hook_data['component'], $hook_data['callback'] ),
				$hook_data['priority'],
				$hook_data['accepted_args']
			);
		}
	}
}
