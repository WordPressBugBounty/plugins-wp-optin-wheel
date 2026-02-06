<?php

namespace MABEL_WOF_LITE\Code\Controllers
{

	use MABEL_WOF_LITE\Code\Services\Log_Service;
	use MABEL_WOF_LITE\Code\Services\MailChimp_Service;
	use MABEL_WOF_LITE\Code\Services\Wheel_service;
	use MABEL_WOF_LITE\Core\Common\Admin;
	use MABEL_WOF_LITE\Core\Common\Managers\Config_Manager;
	use MABEL_WOF_LITE\Core\Common\Managers\Options_Manager;
	use MABEL_WOF_LITE\Core\Common\Managers\Settings_Manager;
	use MABEL_WOF_LITE\Core\Models\Checkbox_Option;
	use MABEL_WOF_LITE\Core\Models\Container_Option;
	use MABEL_WOF_LITE\Core\Models\Custom_Option;
	use MABEL_WOF_LITE\Core\Models\Dropdown_Option;
	use MABEL_WOF_LITE\Core\Models\Editor_Option;
	use MABEL_WOF_LITE\Core\Models\Number_Option;
	use MABEL_WOF_LITE\Core\Models\Option;
	use MABEL_WOF_LITE\Core\Models\Option_Dependency;
	use MABEL_WOF_LITE\Core\Models\Pro_option;
	use MABEL_WOF_LITE\Core\Models\Text_Option;

	if(!defined('ABSPATH')){die;}

	class Admin_Controller extends Admin
	{
		private $slug;
		public function __construct()
		{
			parent::__construct(new Options_Manager());
			$this->slug = Config_Manager::$slug;

			// Add media scripts for media selector
			$this->add_mediamanager_scripts = true;

			// Add the wp color picker
			$this->add_script_dependencies('wp-color-picker');
			$this->add_style('wp-color-picker',null);

			// Add ajax functions: wheels API.
			$this->add_ajax_function('mb-wof-lite-get-wheels', $this,'get_wheels',false,true);
			$this->add_ajax_function('mb-wof-lite-get-wheel', $this,'get_wheel',false,true);
			$this->add_ajax_function('mb-wof-lite-add-wheel', $this,'add_wheel',false,true);
			$this->add_ajax_function('mb-wof-lite-update-wheel', $this,'update_wheel',false,true);
			$this->add_ajax_function('mb-wof-lite-delete-wheel', $this, 'delete_wheel', false, true);
			$this->add_ajax_function('mb-wof-lite-toggle-activation', $this,'toggle_wheel_activation',false,true);

			// Other APIs
			$this->add_ajax_function('mb-wof-lite-get-mailchimp-lists', $this, 'get_mailchimp_lists', false, true);
			$this->add_ajax_function('mb-wof-lite-get-mailchimp-fields', $this, 'get_mailchimp_fields', false, true);
			$this->add_ajax_function('mb-wof-lite-get-log', $this, 'get_logs', false, true);
			$this->add_ajax_function('mb-wof-lite-clear-log', $this, 'clear_log', false, true);
		}


		public function get_mailchimp_fields() {
            
            if( ! current_user_can( $this->capability ) || !isset( $_REQUEST['wof_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['wof_nonce'] ) ), 'wof_data_nonce' ) ) {
                wp_send_json_error();
			}

            if ( ! empty( $_GET['id'] ) ) {
                wp_send_json( MailChimp_Service::get_fields_from_list( sanitize_text_field( sanitize_text_field( wp_unslash( $_GET['id'] ) ) ) ) );
            }

            wp_send_json_error();
            
		}

		public function clear_log() {
            if( ! current_user_can( $this->capability ) || !isset( $_REQUEST['wof_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['wof_nonce'] ) ), 'wof_data_nonce' ) ) {
				wp_send_json_error();
			}
			Log_Service::clear();
			wp_die();
		}

		public function get_logs(){
            if( ! current_user_can( $this->capability ) || !isset( $_REQUEST['wof_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['wof_nonce'] ) ), 'wof_data_nonce' ) ) {
                wp_send_json_error();
			}
			wp_send_json(Log_Service::get_log());
		}

		public function get_mailchimp_lists() {
            if( ! current_user_can( $this->capability ) || !isset( $_REQUEST['wof_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['wof_nonce'] ) ), 'wof_data_nonce' ) ) {
                wp_send_json_error();
			}

			wp_send_json(MailChimp_Service::get_email_lists());
		}

		public function toggle_wheel_activation()
		{
            if( ! current_user_can( $this->capability ) || !isset( $_REQUEST['wof_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['wof_nonce'] ) ), 'wof_data_nonce' ) ) {
                wp_send_json_error();
			}

            if ( ! empty( $_REQUEST['id'] ) && isset( $_REQUEST['toggle'] ) ) { // toggle can be zero so "isset" instead of empty.
                Wheel_service::toggle_activation( sanitize_text_field( wp_unslash( $_REQUEST['id'] ) ), sanitize_text_field( wp_unslash( $_REQUEST['toggle'] ) ) );
            }
            
            wp_die();
            
        }

		public function delete_wheel()
		{
            if( ! current_user_can( $this->capability ) || !isset( $_REQUEST['wof_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['wof_nonce'] ) ), 'wof_data_nonce' ) ) {
                wp_send_json_error();
			}

            if ( isset( $_REQUEST['id'] ) && ! empty( $_REQUEST['id'] ) ){
                Wheel_service::delete_wheel( sanitize_text_field( wp_unslash( $_REQUEST['id'] ) ) );
            }

			wp_die();
		}

		public function get_wheel()
		{
            if( ! current_user_can( $this->capability ) || !isset( $_REQUEST['wof_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['wof_nonce'] ) ), 'wof_data_nonce' ) ) {
                wp_send_json_error();
			}

			if( empty( $_GET['id'] ) ) wp_die();

			$notification = Wheel_service::get_wheel( sanitize_text_field( wp_unslash( $_GET['id'] ) ) );
			
            wp_send_json( $notification );
            
		}

		public function get_wheels()
		{
            if( ! current_user_can( $this->capability ) || !isset( $_REQUEST['wof_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['wof_nonce'] ) ), 'wof_data_nonce' ) ) {
                wp_send_json_error();
			}

			$notifications = Wheel_service::get_all_wheels();
			wp_send_json( $notifications );
		}

		public function update_wheel()
		{
            if( ! current_user_can( $this->capability ) || !isset( $_REQUEST['wof_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['wof_nonce'] ) ), 'wof_data_nonce' ) ) {
                wp_send_json_error();
			}

			if(!isset($_POST['options']) || !isset($_POST['id']))
				wp_send_json_error();

            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$decoded = json_decode( wp_unslash( $_POST['options'] ) );

			if($decoded === null)
			    wp_send_json_error(json_last_error_msg());

			$validated_decoded = $this->sanitize_options( $decoded );

			Wheel_service::edit_wheel( sanitize_text_field( wp_unslash( $_POST['id'] ) ),addslashes( json_encode( $validated_decoded ) ) );

			wp_send_json_success();
		}

		public function add_wheel()
		{
			if(!current_user_can($this->capability) || !isset($_REQUEST['wof_nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['wof_nonce'] ) ),'wof_data_nonce')) {
				wp_send_json_error();
			}

			if( empty( $_POST['options'] ) )
				wp_send_json_error();

            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$decoded = json_decode( wp_unslash( $_POST['options'] ) );

			if( $decoded === null )
			    wp_send_json_error( json_last_error_msg() );

			$validated_decoded = $this->sanitize_options( $decoded );

			$id = Wheel_service::add_wheel( addslashes( json_encode( $validated_decoded ) ) );
			wp_die( esc_html( $id ) );
		}

		public function init_admin_page()
		{

			// Main sidebar
			add_action(Config_Manager::$slug . '-render-sidebar', [$this,'render_main_sidebar']);

			// The sections
			$this->options_manager->add_section('settings', __('General settings', 'wp-optin-wheel' ), 'admin-settings', true);
			$this->options_manager->add_section('apis', __('Email Integration', 'wp-optin-wheel' ), 'email-alt');
			$this->options_manager->add_section('addwheel', __('Add Wheel', 'wp-optin-wheel' ), 'plus');
			$this->options_manager->add_section('wheels', __('Wheels', 'wp-optin-wheel' ), 'dashboard');

			$this->options_manager->add_option('settings', new Checkbox_Option(
				'log',
				__('Use log file', 'wp-optin-wheel' ),
				__('Log all opt-ins and play-results.', 'wp-optin-wheel' ),
				Settings_Manager::get_setting('log')
			));
			$this->options_manager->add_option('settings', new Pro_option(
				__( 'Woocommerce coupon integration', 'wp-optin-wheel' ),
				__( 'This is a Pro feature, which enables a <b>fully automated WooCommerce</b> integration. The integration allows wheels to automatically create single-use coupon codes for winners. The integration comes with extra coupon settings such as coupon duration and product/cart validity settings.', 'wp-optin-wheel' )
                ));


			$this->options_manager->add_option('settings',new Custom_Option(
				' ',
				'log'
			));

			$this->options_manager->add_option('apis', new Text_Option(
				'mailchimp_api',
				__('MailChimp API Key', 'wp-optin-wheel' ),
				Settings_Manager::get_setting('mailchimp_api'),
				null,
				__('If you want to use Mailchimp for email optin, enter your API Key here.', 'wp-optin-wheel' )
			));

			$this->options_manager->add_option(
                'apis', 
                new Pro_option(
                    __( 'Other integrations', 'wp-optin-wheel' ), 
                    __( '<strong>Don\'t want to use MailChimp? The Pro version also integrates with:</strong> <ul><li>Your own WordPress database</li><li>Automation tools such as Zapier, IFTTT, N8N, Make, ...</li><li>Drip</li><li>Mailchimp</li><li>ActiveCampaign</li><li>Campaign Monitor</li><li>GetResponse</li><li>MailerLite</li><li>Klaviyo</li><li>Mailster</li><li>MailPoet</li><li>Brevo</li><li>ConvertKit</li><li>Remarkety</li></ul><p>You can also integrate with anything that supports webhooks.</p>', 'wp-optin-wheel' ) 
                )
            );

			$this->options_manager->add_option('addwheel',
				new Custom_Option(null,'add_wheel',$this->create_addwheel_model())
			);

			$this->options_manager->add_option('wheels',
				new Custom_Option(null,'all_wheels', ['base_url' => Config_Manager::$url] )
			);
		}

		public function render_main_sidebar() {
			include Config_Manager::$dir . 'admin/views/sidebar-main.php';
		}

		private function sanitize_options($options){

			$allow_html_minimal = [
				'a' => [
					'href' => [],
					'title' => []
				],
				'b' => [],
				'em' => [],
				'strong' => [],
				'i' => [],
				'span' => ['style'],
				'ul' => [],
				'li' => []
			];

			if(isset($options->appeardelay))
				$options->appeardelay = filter_var($options->appeardelay, FILTER_SANITIZE_NUMBER_INT);
			if(isset($options->winning_chance))
				$options->winning_chance = filter_var($options->winning_chance, FILTER_SANITIZE_NUMBER_INT);
			if(isset($options->occurancedelay))
				$options->occurancedelay = filter_var($options->occurancedelay, FILTER_SANITIZE_NUMBER_INT);
			if(isset($options->appeartype))
				$options->appeartype = sanitize_text_field($options->appeartype);
			if(isset($options->bgpattern))
				$options->bgpattern = sanitize_text_field($options->bgpattern);
			if(isset($options->button_done))
				$options->button_done = sanitize_text_field($options->button_done);
			if(isset($options->button_text))
				$options->button_text = sanitize_text_field($options->button_text);
			if(isset($options->close_text))
				$options->close_text = sanitize_text_field($options->close_text);
			if(isset($options->disclaimer))
				$options->disclaimer = wp_kses($options->disclaimer, $allow_html_minimal);
			if(isset($options->explainer))
				$options->explainer = wp_kses($options->explainer, $allow_html_minimal);
			if(isset($options->list))
				$options->list = sanitize_text_field($options->list);
			if(isset($options->list_provider))
				$options->list_provider = sanitize_text_field($options->list_provider);
			if(isset($options->email_placeholder))
				$options->email_placeholder = sanitize_text_field($options->email_placeholder);
			if(isset($options->losing_text))
				$options->losing_text = sanitize_text_field($options->losing_text);
			if(isset($options->losing_title))
				$options->losing_title = wp_kses($options->losing_title, $allow_html_minimal);
			if(isset($options->occurance))
				$options->occurance = sanitize_text_field($options->occurance);
			if(isset($options->theme))
				$options->theme = sanitize_text_field($options->theme);
			if(isset($options->title))
				$options->title = wp_kses($options->title,$allow_html_minimal);
			if(isset($options->winning_text_coupon))
				$options->winning_text_coupon = wp_kses($options->winning_text_coupon, $allow_html_minimal);
			if(isset($options->winning_text_link))
				$options->winning_text_link = wp_kses($options->winning_text_link, $allow_html_minimal);
			if(isset($options->winning_title))
				$options->winning_title = wp_kses($options->winning_title, $allow_html_minimal);

			return $options;
		}

		private function create_addwheel_model() {

			$themes = [
				'vintage'       => __( 'Vintage', 'wp-optin-wheel' ),
				'deep-purple'   => __( 'Deep Purple', 'wp-optin-wheel' ),
				'yellow'        => __( 'Yellow' , 'wp-optin-wheel' ),
				'red'           => __( 'Red', 'wp-optin-wheel' ),
				'orange'        => __( 'Orange', 'wp-optin-wheel' ),
				'purple'        => __( 'Purple', 'wp-optin-wheel' ),
				'green'         => __( 'Green', 'wp-optin-wheel' ),
			];

			$slices = [ [
					'label' => __('5% Discount', 'wp-optin-wheel' ),
					'value' => '5OFF',
					'chance' => 30,
					'type' => 1
				], [
					'label' => __('No prize', 'wp-optin-wheel' ),
					'type' => 0
				], [
					'label' => __('Next time', 'wp-optin-wheel' ),
					'type' => 0
				], [
					'label' => __('Almost!', 'wp-optin-wheel' ),
					'type' => 0
				], [
					'label' => __('10% Discount', 'wp-optin-wheel' ),
					'value' => '10OFF',
					'chance' => 30,
					'type' => 1
				], [
					'label' => __('Free Ebook', 'wp-optin-wheel' ),
					'value' => 'https://google.com/',
					'chance' => 30,
					'type' => 2
				], [
					'label' => __('No Prize', 'wp-optin-wheel' ),
					'type' => 0
				], [
					'label' => __('No luck today', 'wp-optin-wheel' ),
					'type' => 0
				], [
					'label' => __('Almost!', 'wp-optin-wheel' ),
					'type' => 0
				], [
					'label' => __('50% Discount', 'wp-optin-wheel' ),
					'value' => '50OFF',
					'chance' => 10,
					'type' => 1
				], [
					'label' => __('No prize', 'wp-optin-wheel' ),
					'type' => 0
				], [
					'label' => __('Unlucky', 'wp-optin-wheel' ),
					'type' => 0
				],
			];

			$content_settings = new Container_Option( null, __('Content settings', 'wp-optin-wheel' ) );

			$content_settings->options = [
				$this->add_data_attribute_for_data_bind(
					new Text_Option(
						'title',
						__('Title', 'wp-optin-wheel' ),
						null,
						__('Get your chance to <em>win a price</em>!', 'wp-optin-wheel' ),
						__('Use <em></em> to emphasise text (it will have a different color).', 'wp-optin-wheel' )
					)
				),
				$this->add_data_attribute_for_data_bind(
					new Editor_Option(
						'explainer',
						__('Explainer text', 'wp-optin-wheel' ),
						null,
						[
							'tinymce' => [
								'toolbar1' => 'bold,italic,underline',
								'toolbar2' => false
							],
							'quicktags' => false
						],
						__('A short paragraph explaining how it works.', 'wp-optin-wheel' )
					)
				),
				$this->add_data_attribute_for_data_bind(
					new Editor_Option(
						'disclaimer',
						__('Disclaimer text', 'wp-optin-wheel' ),
						null,
						[
							'tinymce' => [
								'toolbar1' =>
								'bold,italic,underline,bullist,justifyleft,justifycenter' .
								',justifyright,link,unlink',
								'toolbar2' => false
							],
							'quicktags' => false
						],
						__('Add a short paragraph explaining the rules & regulations.', 'wp-optin-wheel' )
					)
				),
				$this->add_data_attribute_for_data_bind( new Text_Option(
					'button_text',
					__('Spin-button text', 'wp-optin-wheel' ),
					null,
					__('Try your luck', 'wp-optin-wheel' ),
					__('This text will appear on the button the visitor has to click to spin the wheel.', 'wp-optin-wheel' )
				)),
				$this->add_data_attribute_for_data_bind( new Text_Option(
					'close_text',
					__('Close popup text', 'wp-optin-wheel' ),
					null,
					__("I don't feel lucky", 'wp-optin-wheel' ),
					__( 'This link will close the popup. It appears on the lower right side of the popup.', 'wp-optin-wheel' )
				)),
				$this->add_data_attribute_for_data_bind(new Text_Option(
					'losing_title',
					__( 'Losing title', 'wp-optin-wheel' ),
					null,
					__( "Uh oh! Looks like you lost", 'wp-optin-wheel' ),
					__( 'This title will appear after a player hits a losing segment.', 'wp-optin-wheel' )
				)),
				$this->add_data_attribute_for_data_bind(new Text_Option(
					'losing_text',
					__( "Losing text", 'wp-optin-wheel' ),
					null,
					__( "We're sorry, the wheel of fortune has let you down. Better luck next time!", 'wp-optin-wheel' ),
					__( 'This text will appear below the losing title after a player hits a losing segment.', 'wp-optin-wheel' )
				)),
				$this->add_data_attribute_for_data_bind(new Text_Option(
					'winning_title',
					__('Winning title', 'wp-optin-wheel' ),
					null,
					__("Hurray! You've hit {x}. Lucky you!", 'wp-optin-wheel' ),
					__("This title will appear after a player hits a winning segment. Use {x} to denote the segment's label.", 'wp-optin-wheel' )
				)),
				$this->add_data_attribute_for_data_bind(new Text_Option(
					'winning_text_coupon',
					__("Winning text for coupons", 'wp-optin-wheel' ),
					null,
					__("Nicely done! You can use the coupon code below to claim your prize:", 'wp-optin-wheel' ),
					__('This text will appear below the winning title after a player hits a winning coupon-segment.', 'wp-optin-wheel' )
				)),
				$this->add_data_attribute_for_data_bind(new Text_Option(
					'winning_text_link',
					__("Winning text for links", 'wp-optin-wheel' ),
					null,
					__("Nicely done! here's the link to your free product:", 'wp-optin-wheel' ),
					__('This text will appear below the winning title after a player hits a winning link-segment.', 'wp-optin-wheel' )
				)),
				$this->add_data_attribute_for_data_bind(new Text_Option(
					'button_done',
					__("'Done' button text", 'wp-optin-wheel' ),
					null,
					__("I'm done playing", 'wp-optin-wheel' ),
					__('When the player has done playing, this button will appear to allow to close the popup.', 'wp-optin-wheel' )
				))
			];

			$design_settings = new Container_Option(null, __( 'Design settings', 'wp-optin-wheel' ) );
			$design_settings->options = [
				$this->add_data_attribute_for_data_bind(new Dropdown_Option('bgpattern',__( 'Background pattern', 'wp-optin-wheel' ), [
					'none' => 'No pattern',
					'hearts' => 'Hearts'
				], 'hearts', __( 'More options in Pro.' , 'wp-optin-wheel' ) ) ),
				new Pro_option( __( 'Advanced design settings (colors, logo, custom background, confetti, audio, ...)', 'wp-optin-wheel' ),__( 'This is a pro feature. You can define each slice\'s color, add a logo, custom background, and more.','wp-optin-wheel' ) ),
				new Pro_option( __( 'Full-screen popup', 'wp-optin-wheel' ),__( 'This is a pro feature.','wp-optin-wheel' ) ),
				new Pro_option( __( 'Embed the wheel on a post or page (no popup)', 'wp-optin-wheel' ),__( 'This is a pro feature.','wp-optin-wheel' ) )
			];

			$behavior_settings = new Container_Option(null, __( 'Behavior setting', 'wp-optin-wheel' ) );

			$behavior_settings->options = [
				new Pro_option(
					__('Hide on mobile', 'wp-optin-wheel' ),
					__('This is a Pro feature.', 'wp-optin-wheel' )
				),
                new Pro_option(
                    __('Logged in/logged out', 'wp-optin-wheel' ),
                    __('This is a Pro feature.', 'wp-optin-wheel' )
                ),
				new Pro_option(
					__('Show on these pages only', 'wp-optin-wheel' ),
					__('This is a Pro feature.', 'wp-optin-wheel' )
				)
			];

			$behavior_settings->options[] = $this->add_data_attribute_for_data_bind(
				new Dropdown_Option(
					'appeartype',
					__( 'Show wheel', 'wp-optin-wheel' ), [
						'immediately' => __( 'Immediately', 'wp-optin-wheel' ),
						'delay' => __( 'After a delay', 'wp-optin-wheel' )
					],
					null,
					__('More options in Pro, such as by clicking a widget or button, on a timer, ...', 'wp-optin-wheel' )
			));
			$behavior_settings->options[] = $this->add_data_attribute_for_data_bind( new Number_Option(
				'appeardelay',
				__( 'Appearance delay', 'wp-optin-wheel' ),
				5,
				null,null,
				[ new Option_Dependency('appeartype','delay') ],
				__( 'Show popup after', 'wp-optin-wheel' ),
				__( 'seconds', 'wp-optin-wheel' )
			));

			$behavior_settings->options[] = $this->add_data_attribute_for_data_bind(new Number_Option(
				'occurancedelay',
				__('Occurance delay', 'wp-optin-wheel' ),
				5,
				null,null,
				[ new Option_Dependency('occurance','delay') ],
				__( 'Show popup again after', 'wp-optin-wheel' ),
				__( 'days', 'wp-optin-wheel' )
			));
            
          $behavior_settings->options[] = $this->add_data_attribute_for_data_bind(new Pro_option(
              __( 'Spinning Speed', 'wp-optin-wheel' ),
              __( 'This is a Pro feature.', 'wp-optin-wheel' )
          ));

          $behavior_settings->options[] = $this->add_data_attribute_for_data_bind(new Pro_option(
                __( 'Spinning Time', 'wp-optin-wheel' ),
                __( 'This is a Pro feature.', 'wp-optin-wheel' )
            ));
            
			$list_settings = new Container_Option( null, __('List settings', 'wp-optin-wheel' ) );

			$list_settings->options = [
				new Dropdown_Option(
					'list_provider',
					__('List provider', 'wp-optin-wheel' ),
					[],
					null,
					__('What email list software are you using?', 'wp-optin-wheel' )
				),
				new Dropdown_Option(
					'list',
					__('Email list', 'wp-optin-wheel' ),
					[],
					null,
					__('To which email list should your visitors opt in?', 'wp-optin-wheel' )
				),
                
				new Pro_option( __( 'Form fields builder', 'wp-optin-wheel' ), __( 'This is a pro feature.', 'wp-optin-wheel' ) ),
              
                new Pro_option(
                    __('Validate email domains.', 'wp-optin-wheel' ),
                    __('This is a Pro feature where you email addresses will be validated against an up-to-date list of fake email domains.', 'wp-optin-wheel' )
                ),
                new Pro_option(
                    __('Check IP addresses.', 'wp-optin-wheel' ),
                    __('This is a Pro anti-cheat feature.', 'wp-optin-wheel' )
                ),
                new Pro_option(
                    __('Only display prizes in emails, not on screen.', 'wp-optin-wheel' ),
                    __('This is a Pro anti-cheat feature.', 'wp-optin-wheel' )
                )
			];

			$form_builder_for_lists_settings = 	new Custom_Option(null,'form-builder-lists');

			$gdpr_settings = new Container_Option( null, __('GDPR', 'wp-optin-wheel' ) );
			$gdpr_settings->name = 'gdpr_settings';
			$gdpr_custom_setting = new Custom_Option( __( 'Send data to email list', 'wp-optin-wheel' ),'gdpr-settings');
			$gdpr_settings->options = [ $gdpr_custom_setting ];

			$chance_settings = [
				$this->add_data_attribute_for_data_bind(new Number_Option(
					'winning_chance',
					__('Winning chance', 'wp-optin-wheel' ),
					75,null,
					__("What's the chance your visitor will win something? If you want your visitor to always win (recommended), set this to 100%.", 'wp-optin-wheel' ),
					null,' ',
					' % '
				)),
				new Pro_option(__( 'Replays', 'wp-optin-wheel' ), __( 'Replays is a Pro feature. You can set how many times a visitor can retry if they lose.', 'wp-optin-wheel' ) )
			];

			return [
				'base_url' => Config_Manager::$url,
				'themes' => $themes,
				'slices' => $slices,
				'chance_settings' => $chance_settings,
				'form_builder_for_lists' => $form_builder_for_lists_settings,
				'settings' => [
					$content_settings, $design_settings, $behavior_settings, $list_settings, $gdpr_settings
				]
			];
		}

		private function add_data_attribute_for_data_bind(Option $option)
		{
            if( ! empty( $option->id) ) {
                $option->data_attributes[ 'key' ] = $option->id;
                if ( substr( $option->id, -strlen( '_list' ) ) === '_list' )
                    $option->data_attributes[ 'optin-list' ] = '';
            }
            return $option;
            
		}

	}
}