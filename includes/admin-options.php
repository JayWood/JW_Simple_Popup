<?php
/**
 * CMB2 Theme Options
 * @version 0.1.0
 */
class jw_simple_popup_Admin {

	/**
	 * Option key, and option page slug
	 * @var string
	 */
	private $key = 'jw_simple_popup_options';

	/**
	 * Array of metaboxes/fields
	 * @var array
	 */
	protected $option_metabox = array();

	/**
	 * Options Page title
	 * @var string
	 */
	protected $title = '';

	/**
	 * Options Page hook
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Constructor
	 * @since 0.1.0
	 */
	public function __construct() {
		require_once 'cmb2/init.php';
		// Set our title
		$this->title = __( 'Simple Popup', 'jw_simple_popup' );

		// Set our CMB2 fields
		$this->fields = array(
			array(
				'name'	  => __( 'Popup Overlay Color', 'jw_simple_popup' ),
				'desc'	  => __( 'Self explanitory.', 'jw_simple_popup' ),
				'id'	  => 'bg_color',
				'type'	  => 'rgba_colorpicker',
				'default' => 'rgba(0,0,0,0.50)',
			),
			array(
				'name'	  => __( 'Popup Delay', 'jw_simple_popup' ),
				'desc'	  => __( 'Time in seconds to delay the popup.', 'jw_simple_popup' ),
				'id'	  => 'delay',
				'type'	  => 'text_small',
				'default' => '5',
			),
			array(
				'name'	  => __( 'Modal', 'jw_simple_popup' ),
				'desc'	  => __( 'Should act like a modal window, ( ie. cannot close without clicking the X button.', 'jw_simple_popup' ),
				'id'	  => 'modal',
				'type'	  => 'checkbox',
			),
			array(
				'name'	  => __( 'Custom CSS', 'jw_simple_popup' ),
				'id'	  => 'css',
				'type'	  => 'textarea_code',
			),
			array(
				'name'	  => __( 'Dialog Content', 'jw_simple_popup' ),
				'id'	  => 'content',
				'type'	  => 'wysiwyg',
			),
		);
	}

	/**
	 * Initiate our hooks
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_filter( 'cmb2_sanitize_wysiwyg', array( $this, 'filter_wysiwyg' ), 10, 5 );
	}

	/**
	 * Register our setting to WP
	 * @since  0.1.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page
	 * @since 0.1.0
	 */
	public function add_options_page() {
		$this->options_page = add_menu_page( $this->title, $this->title, 'manage_options', $this->key, array( $this, 'admin_page_display' ), 'dashicons-admin-page' );
	}

	/**
	 * Filter WYSIWYG Field ( CMB2 )
	 * @since 0.1.0
	 * @param  string 		$na        	DEPRECATED: Override value
	 * @param  string 		$value     	Value passed by form
	 * @param  string 		$object_id 	CMB2 Object ID
	 * @param  array  		$args      	Array of field data
	 * @param  obj 			$cmb2      	CMB2 Object
	 * @return string|null            	Un-filtered value or null on failure
	 */
	public function filter_wysiwyg( $na = null, $value, $object_id, $args, $cmb2 ){
		$field_id = isset( $args['id'] ) ? $args['id'] : false;
		if ( 'content' != $field_id || 'jw_simple_popup_options' !== $object_id ){
			return;
		}
		return wp_kses_post( $value ); // Unfiltered value
	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 * @since  0.1.0
	 */
	public function admin_page_display() {
		?>
		<div class="wrap cmb2_options_page <?php echo $this->key; ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<p class="description"><? _e( sprintf( 'Options panel powered by %s.', '<a href="https://github.com/WebDevStudios/CMB2" target="_blank">CMB2</a>' ), 'jw_simple_popup'); ?></p>
			<?php cmb2_metabox_form( $this->option_metabox(), $this->key ); ?>
		</div>
		<?php
	}

	/**
	 * Defines the theme option metabox and field configuration
	 * @since  0.1.0
	 * @return array
	 */
	public function option_metabox() {
		return array(
			'id'		 => 'option_metabox',
			'show_on'	=> array( 'key' => 'options-page', 'value' => array( $this->key, ), ),
			'show_names' => true,
			'fields'	 => $this->fields,
		);
	}

	/**
	 * Public getter method for retrieving protected/private variables
	 * @since  0.1.0
	 * @param  string  $field Field to retrieve
	 * @return mixed		  Field value or exception is thrown
	 */
	public function __get( $field ) {

		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'fields', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}
		if ( 'option_metabox' === $field ) {
			return $this->option_metabox();
		}

		throw new Exception( 'Invalid property: ' . $field );
	}

}

// Get it started
$jw_simple_popup_Admin = new jw_simple_popup_Admin();
$jw_simple_popup_Admin->hooks();

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string  $key Options array key
 * @return mixed		Option value
 */
function jw_simple_popup_get_option( $key = '' ) {
	global $jw_simple_popup_Admin;
	return cmb2_get_option( $jw_simple_popup_Admin->key, $key );
}