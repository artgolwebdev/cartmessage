<?php 
namespace  Cartmessage;

class Message {
	public $message,$position;
	private $message_options,$plugin;

	public function __construct() {
        $this->plugin = plugin_basename(__FILE__);
	}

	function create(){

		$message_options = get_option( 'message_option_name' ); // Array of All Options
    
		if(!empty($message_options)){
	 
			$this->message = $message_options['message_0']; 
			$this->position = $message_options['position_1'];
	
			if(!empty($this->message)){
				switch($this->position){
					case 'before' : 
						add_filter('woocommerce_before_cart',array($this,'cart_page_message'));
						break;
					case 'after' : 
						add_filter('woocommerce_after_cart_table',array($this,'cart_page_message'));
						break;
					case 'bottom' : 
						add_filter('woocommerce_after_cart',array($this,'cart_page_message'));
						break;
				}
				add_filter('woocommerce_cart_is_empty',array($this,'cart_page_message'));
			}
		}
	}

	function cart_page_message(){
		echo '<p class="cart-message-plugin">'.$this->message.'</p>';
	}

    function register_admin(){
		add_action( 'admin_menu', array( $this, 'message_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'message_page_init' ) );
        add_action('wp_enqueue_scripts',array($this,'enqueue_admin'));
        add_filter("plugin_action_links_$this->plugin",array($this,'settings_link'));
    }

	function register_front(){
		add_action('wp_enqueue_scripts',array($this,'enqueue_front'));
	}


    function settings_link($links){
        // add custom settings link 
        $settings_link = '<a href="admin.php?page=message">Settings</a>';
        array_push($links,$settings_link);
        return $links;
    }

    function enqueue_admin(){
        wp_enqueue_style('mypluginstyle',plugins_url('../assets/css/admin.css',__FILE__));
        wp_enqueue_script('mypluginscript',plugins_url('../assets/js/admin.js',__FILE__));
    }

	function enqueue_front(){
		wp_enqueue_style('mypluginstyle',plugins_url('../assets/css/style.css',__FILE__));
	}


	public function message_add_plugin_page() {
		add_menu_page(
			'Message', // page_title
			'Message', // menu_title
			'manage_options', // capability
			'message', // menu_slug
			array( $this, 'message_create_admin_page' ), // function
			'dashicons-admin-generic', // icon_url
			2 // position
		);
	}

	public function message_create_admin_page() {
		$this->message_options = get_option( 'message_option_name' ); ?>

		<div class="wrap">
			<h2>Message</h2>
			<p>Cart page message text</p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'message_option_group' );
					do_settings_sections( 'message-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function message_page_init() {
		register_setting(
			'message_option_group', // option_group
			'message_option_name', // option_name
			array( $this, 'message_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'message_setting_section', // id
			'Settings', // title
			array( $this, 'message_section_info' ), // callback
			'message-admin' // page
		);

		add_settings_field(
			'message_0', // id
			'Message', // title
			array( $this, 'message_0_callback' ), // callback
			'message-admin', // page
			'message_setting_section' // section
		);

		add_settings_field(
			'position_1', // id
			'Position', // title
			array( $this, 'position_1_callback' ), // callback
			'message-admin', // page
			'message_setting_section' // section
		);
	}

	public function message_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['message_0'] ) ) {
			$sanitary_values['message_0'] = sanitize_text_field( $input['message_0'] );
		}

		if ( isset( $input['position_1'] ) ) {
			$sanitary_values['position_1'] = $input['position_1'];
		}

		return $sanitary_values;
	}

	public function message_section_info() {
		
	}

	public function message_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="message_option_name[message_0]" id="message_0" value="%s">',
			isset( $this->message_options['message_0'] ) ? esc_attr( $this->message_options['message_0']) : ''
		);
	}

	public function position_1_callback() {
		?> <select name="message_option_name[position_1]" id="position_1">
			<?php $selected = (isset( $this->message_options['position_1'] ) && $this->message_options['position_1'] === 'before') ? 'selected' : '' ; ?>
			<option <?php echo $selected; ?>>before</option>
			<?php $selected = (isset( $this->message_options['position_1'] ) && $this->message_options['position_1'] === 'after') ? 'selected' : '' ; ?>
			<option <?php echo $selected; ?>>after</option>
			<?php $selected = (isset( $this->message_options['position_1'] ) && $this->message_options['position_1'] === 'bottom') ? 'selected' : '' ; ?>
			<option <?php echo $selected; ?>>bottom</option>
		</select> <?php
	}

}