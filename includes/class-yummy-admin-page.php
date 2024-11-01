<?php
/**
 * Class-yummy-admin-page.php
 *
 * @package yummy-recipes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Yummy_Admin_Page.
 */
class Yummy_Admin_Page {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_options_menu_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings_and_fields' ) );
		add_action( 'admin_notices', array( __CLASS__, 'user_should_save_options_notice' ) );

		// Use dynamic hooks to delete transients when saving options.
		add_action( 'update_option_yummy_options', array( __CLASS__, 'delete_related_posts_transients' ) );
		add_action( 'add_option_yummy_options', array( __CLASS__, 'delete_related_posts_transients' ) );
	}

	/**
	 * Registers a custom menu page.
	 */
	public static function register_options_menu_page() {
		add_menu_page(
			'Yummy Recipes',
			'Yummy Recipes',
			'manage_options',
			'yummy-options-page',
			array( __CLASS__, 'add_options_menu_page_cb' ),
			'dashicons-food',
			101
		);
	}

	/**
	 * Registers plugin settings.
	 *
	 * @return void
	 */
	public static function register_settings_and_fields() {

		// Register a new setting.
		register_setting( 'yummy_options_page', 'yummy_options', array( 'sanitize_callback' => array( __CLASS__, 'sandbox_theme_validate_input_examples' ) ) );

		// Add setting sections.
		add_settings_section(
			'yummy_section_general',
			__( 'General', 'yummy-recipes' ),
			'__return_false',
			'yummy_options_page'
		);

		add_settings_section(
			'yummy_section_cards',
			__( 'Cards', 'yummy-recipes' ),
			'__return_false',
			'yummy_options_page'
		);

		// Add setting fields.
		add_settings_field(
			'yummy_section_general_1',
			__( 'Show recipes along with blog posts in', 'yummy-recipes' ),
			array( __CLASS__, 'options_field_multicheck_cb' ),
			'yummy_options_page',
			'yummy_section_general',
			array(
				'option_key'  => 'show_recipes_in_loops',
				'options'     => array(
					'home'     => 'Blog homepage',
					'archives' => 'Blog archives',
					'author'   => 'Author page',
				),
				'description' => esc_html__( 'Please note that this option does not always work as expected because of the used theme and other plugins.', 'yummy-recipes' ),
			)
		);

		add_settings_field(
			'yummy_section_general_2',
			__( 'Show "Jump to recipe" button before the recipe post content', 'yummy-recipes' ),
			array( __CLASS__, 'options_field_checkbox_cb' ),
			'yummy_options_page',
			'yummy_section_general',
			array(
				'option_key' => 'jump_to_recipe_button_before_content',
			)
		);

		add_settings_field(
			'yummy_section_general_3',
			__( 'Show "Print recipe" button before the recipe post content', 'yummy-recipes' ),
			array( __CLASS__, 'options_field_checkbox_cb' ),
			'yummy_options_page',
			'yummy_section_general',
			array(
				'option_key' => 'print_recipe_button_before_content',
			)
		);

		add_settings_field(
			'yummy_section_general_4',
			__( 'Show daily values in nutrition facts', 'yummy-recipes' ),
			array( __CLASS__, 'options_field_checkbox_cb' ),
			'yummy_options_page',
			'yummy_section_general',
			array(
				'option_key' => 'display_daily_values',
			)
		);

		add_settings_field(
			'yummy_section_general_5',
			__( 'Show nutrition facts on print page', 'yummy-recipes' ),
			array( __CLASS__, 'options_field_checkbox_cb' ),
			'yummy_options_page',
			'yummy_section_general',
			array(
				'option_key' => 'display_nutrition_facts_on_print',
			)
		);

		add_settings_field(
			'yummy_section_cards_1',
			__( 'Show servings', 'yummy-recipes' ),
			array( __CLASS__, 'options_field_checkbox_cb' ),
			'yummy_options_page',
			'yummy_section_cards',
			array(
				'option_key' => 'show_servings_on_big_card',
			)
		);

		add_settings_field(
			'yummy_section_cards_2',
			__( 'Show print button', 'yummy-recipes' ),
			array( __CLASS__, 'options_field_checkbox_cb' ),
			'yummy_options_page',
			'yummy_section_cards',
			array(
				'option_key' => 'display_print_button_on_big_card',
			)
		);

		add_settings_field(
			'yummy_section_cards_3',
			__( 'Link taxonomies to taxonomy archives', 'yummy-recipes' ),
			array( __CLASS__, 'options_field_checkbox_cb' ),
			'yummy_options_page',
			'yummy_section_cards',
			array(
				'option_key' => 'link_card_taxonomy_terms_to_archives',
			)
		);

		add_settings_field(
			'yummy_section_cards_4',
			__( "Show 'Powered by' text below the recipe card", 'yummy-recipes' ),
			array( __CLASS__, 'options_field_checkbox_cb' ),
			'yummy_options_page',
			'yummy_section_cards',
			array(
				'option_key' => 'display_powered_by_text',
			)
		);

		do_action( 'yummy_action_add_admin_page_settings', __CLASS__ );
	}

	/**
	 * Sanitize values.
	 *
	 * @param array $input Input option values.
	 *
	 * @return array
	 */
	public static function sandbox_theme_validate_input_examples( $input ) {

		// Create our array for storing the validated options.
		$output = array();

		// Loop through each of the incoming options.
		foreach ( $input as $key => $value ) {

			// Check to see if the current option has a value. If so, process it.
			if ( isset( $input[ $key ] ) ) {
				if ( is_array( $input[ $key ] ) ) {
					foreach ( $input[ $key ] as $key1 => $value1 ) {
						$output[ $key ][ $key1 ] = wp_strip_all_tags( stripslashes( $value1 ) );
					}
				} else {
					// Strip all HTML and PHP tags and properly handle quoted strings.
					$output[ $key ] = wp_strip_all_tags( stripslashes( $input[ $key ] ) );
				}
			}
		}

		// Return the array processing any additional functions filtered by this action.
		return $output;
	}

	/**
	 * Renders multicheck option field.
	 *
	 * @param array $args Options field arguments.
	 */
	public static function options_field_multicheck_cb( $args ) {

		$options = get_option( 'yummy_options' );
		?>

		<fieldset>
			<?php foreach ( $args['options'] as $slug => $title ) : ?>
				<?php
				$id            = 'id-' . $args['option_key'] . '-' . $slug;
				$name          = 'yummy_options[' . $args['option_key'] . '][]';
				$current_value = false;

				if ( ! empty( $options ) && array_key_exists( $args['option_key'], $options ) && in_array( $slug, $options[ $args['option_key'] ], true ) ) {
					$current_value = 'on';
				}
				?>
				<label for="<?php echo esc_attr( $id ); ?>">
					<input type="checkbox" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $slug ); ?>" <?php checked( $current_value, 'on', true ); ?> id="<?php echo esc_attr( $id ); ?>">
					<?php echo esc_html( $title ); ?>
				</label><br>
			<?php endforeach; ?>
		</fieldset>

		<?php if ( ! empty( $args['description'] ) ) : ?>
			<p class="description">
				<?php echo esc_html( $args['description'] ); ?>
			</p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Renders text input option field.
	 *
	 * @param array $args Options field arguments.
	 */
	public static function options_field_input_text_cb( $args ) {

		$options = get_option( 'yummy_options' );

		$value = '';

		if ( ! empty( $options ) && array_key_exists( $args['option_key'], $options ) && ! empty( $options[ $args['option_key'] ] ) ) {
			$value = $options[ $args['option_key'] ];
		}
		?>

		<input type="<?php echo esc_attr( isset( $args['yummy_input_type'] ) ? $args['yummy_input_type'] : 'text' ); ?>" class="<?php echo esc_attr( isset( $args['yummy_input_class'] ) ? $args['yummy_input_class'] : 'regular-text' ); ?>" id="<?php echo esc_attr( $args['option_key'] ); ?>" name="yummy_options[<?php echo esc_attr( $args['option_key'] ); ?>]" value="<?php echo esc_attr( $value ); ?>" />

		<?php if ( ! empty( $args['description'] ) ) : ?>
			<p class="description">
				<?php echo esc_html( $args['description'] ); ?>
			</p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Renders checkbox option field.
	 *
	 * @param array $args Options field arguments.
	 */
	public static function options_field_checkbox_cb( $args ) {

		$options = get_option( 'yummy_options' );
		$checked = '';
		if ( ! empty( $options ) && array_key_exists( $args['option_key'], $options ) && ! empty( $options[ $args['option_key'] ] ) ) {
			$checked = 'checked="checked"';
		}
		?>
		<input type="checkbox" id="<?php echo esc_attr( $args['option_key'] ); ?>" name="yummy_options[<?php echo esc_attr( $args['option_key'] ); ?>]" <?php echo esc_html( $checked ); ?>>
		<?php
	}

	/**
	 * Displays a custom menu page
	 */
	public static function add_options_menu_page_cb() {

		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Check if the user has submitted the settings. Nonce is handled by settings_fields().
		if ( isset( $_GET['settings-updated'] ) ) { // phpcs:ignore
			add_settings_error( 'yummy_settings_messages', 'yummy_message', __( 'Settings saved', 'yummy-recipes' ), 'updated' );
		}

		// Show error/update messages.
		settings_errors( 'yummy_settings_messages' );
		?>

		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<form action="options.php" method="post" enctype=”multipart/form-data”>
				<?php settings_fields( 'yummy_options_page' ); ?>
				<?php do_settings_sections( 'yummy_options_page' ); ?>

				<input type="hidden" name="yummy_options[options_saved]" value="<?php echo esc_attr( time() ); ?>">

				<?php submit_button( __( 'Save Changes', 'yummy-recipes' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Displays admin notice if options are not saved.
	 * Unsaved default options may cause errors.
	 */
	public static function user_should_save_options_notice() {

		if ( ! yummy_get_option( 'options_saved' ) ) {
			?>
			<div class="notice notice-error">
				<p>Please save the options to finish the installation of Yummy. Go to <a href="<?php echo esc_url( admin_url( 'admin.php?page=yummy_options_page' ) ); ?>">Yummy Options</a> and click <strong>Save Changes</strong>.</p>
			</div>
			<?php
		}
	}

	/**
	 * Calls a function to delete transients for related recipes when saving the options.
	 */
	public static function delete_related_posts_transients() {
		yummy_delete_related_recipes_transients();
	}

	/**
	 * Sets default values for plugin options. Called from the plugin activation hook.
	 */
	public static function set_default_options_values() {

		$defaults = array(
			'jump_to_recipe_button_before_content' => 'on',
			'print_recipe_button_before_content'   => 'on',
			'show_servings_on_big_card'            => 'on',
			'display_print_button_on_big_card'     => 'on',
			'display_daily_values'                 => 'on',
			'options_saved'                        => time(),
		);

		// Use add_option instead of update_option, so if the option value is already set, it won't be replaced.
		add_option( 'yummy_options', $defaults );
	}
}

Yummy_Admin_Page::init();
