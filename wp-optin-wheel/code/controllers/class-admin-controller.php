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

			$this->add_mediamanager_scripts = true;

			$this->add_script_dependencies('wp-color-picker');
			$this->add_style('wp-color-picker',null);

			$this->add_ajax_function('mb-wof-lite-get-wheels', $this,'get_wheels',false,true);
			$this->add_ajax_function('mb-wof-lite-get-wheel', $this,'get_wheel',false,true);
			$this->add_ajax_function('mb-wof-lite-add-wheel', $this,'add_wheel',false,true);
			$this->add_ajax_function('mb-wof-lite-update-wheel', $this,'update_wheel',false,true);
			$this->add_ajax_function('mb-wof-lite-delete-wheel', $this, 'delete_wheel', false, true);
			$this->add_ajax_function('mb-wof-lite-toggle-activation', $this,'toggle_wheel_activation',false,true);

			$this->add_ajax_function('mb-wof-lite-get-mailchimp-lists', $this, 'get_mailchimp_lists', false, true);
			$this->add_ajax_function('mb-wof-lite-get-mailchimp-fields', $this, 'get_mailchimp_fields', false, true);
			$this->add_ajax_function('mb-wof-lite-get-log', $this, 'get_logs', false, true);
			$this->add_ajax_function('mb-wof-lite-clear-log', $this, 'clear_log', false, true);
		}


		public function get_mailchimp_fields() {
			if(!current_user_can($this->capability) || !isset($_REQUEST['wof_nonce']) || !wp_verify_nonce($_REQUEST['wof_nonce'],'wof_data_nonce')) {
				wp_send_json_error();
			}
			wp_send_json(MailChimp_Service::get_fields_from_list(sanitize_text_field($_GET['id'])));
		}

		public function clear_log() {
			if(!current_user_can($this->capability) || !isset($_REQUEST['wof_nonce']) || !wp_verify_nonce($_REQUEST['wof_nonce'],'wof_data_nonce')) {
				wp_send_json_error();
			}
			Log_Service::clear();
			wp_die();
		}

		public function get_logs(){
			if(!current_user_can($this->capability) || !isset($_REQUEST['wof_nonce']) || !wp_verify_nonce($_REQUEST['wof_nonce'],'wof_data_nonce')) {
				wp_send_json_error();
			}
			wp_send_json(Log_Service::get_log());
		}

		public function get_mailchimp_lists() {
			if(!current_user_can($this->capability) || !isset($_REQUEST['wof_nonce']) || !wp_verify_nonce($_REQUEST['wof_nonce'],'wof_data_nonce')) {
				wp_send_json_error();
			}

			wp_send_json(MailChimp_Service::get_email_lists());
		}

		public function toggle_wheel_activation()
		{
			if(!current_user_can($this->capability) || !isset($_REQUEST['wof_nonce']) || !wp_verify_nonce($_REQUEST['wof_nonce'],'wof_data_nonce')) {
				wp_send_json_error();
			}

			Wheel_service::toggle_activation(sanitize_text_field($_REQUEST['id']), sanitize_text_field($_REQUEST['toggle']));
			wp_die();
		}

		public function delete_wheel()
		{
			if(!current_user_can($this->capability) || !isset($_REQUEST['wof_nonce']) || !wp_verify_nonce($_REQUEST['wof_nonce'],'wof_data_nonce')) {
				wp_send_json_error();
			}

			Wheel_service::delete_wheel(sanitize_text_field($_REQUEST['id']));
			wp_die();
		}

		public function get_wheel()
		{
			if(!current_user_can($this->capability) || !isset($_REQUEST['wof_nonce']) || !wp_verify_nonce($_REQUEST['wof_nonce'],'wof_data_nonce')) {
				wp_send_json_error();
			}

			if(!isset($_GET['id'])) wp_die();

			$notification = Wheel_service::get_wheel(sanitize_text_field($_GET['id']));
			wp_send_json($notification);
		}

		public function get_wheels()
		{
			if(!current_user_can($this->capability) || !isset($_REQUEST['wof_nonce']) || !wp_verify_nonce($_REQUEST['wof_nonce'],'wof_data_nonce')) {
				wp_send_json_error();
			}

			$notifications = Wheel_service::get_all_wheels();
			wp_send_json($notifications);
		}

		public function update_wheel()
		{
			if(!current_user_can($this->capability) || !isset($_REQUEST['wof_nonce']) || !wp_verify_nonce($_REQUEST['wof_nonce'],'wof_data_nonce')) {
				wp_send_json_error();
			}

			if(!isset($_POST['options']) || !isset($_POST['id']))
				wp_send_json_error();

			$decoded = json_decode(stripslashes($_POST['options']));

			if($decoded === null)
			    wp_send_json_error(json_last_error_msg());

			$validated_decoded = $this->sanitize_options($decoded);

			Wheel_service::edit_wheel(sanitize_text_field($_POST['id']),addslashes(json_encode($validated_decoded)));

			wp_send_json_success();
		}

		public function add_wheel()
		{
			if(!current_user_can($this->capability) || !isset($_REQUEST['wof_nonce']) || !wp_verify_nonce($_REQUEST['wof_nonce'],'wof_data_nonce')) {
				wp_send_json_error();
			}

			if(!isset($_POST['options']))
				wp_send_json_error();

			$decoded = json_decode(stripslashes($_POST['options']));

			if($decoded === null)
			    wp_send_json_error(json_last_error_msg());

			$validated_decoded = $this->sanitize_options($decoded);

			$id = Wheel_service::add_wheel(addslashes(json_encode($validated_decoded)));
			wp_die($id);
		}

		public function init_admin_page()
		{

			add_action(Config_Manager::$slug . '-render-sidebar', [$this,'render_main_sidebar']);

			$this->options_manager->add_section('settings', __('General settings',$this->slug), 'admin-settings', true);
			$this->options_manager->add_section('apis', __('Email Integration', $this->slug), 'email-alt');
			$this->options_manager->add_section('addwheel', __('Add Wheel',$this->slug), 'plus');
			$this->options_manager->add_section('wheels', __('Wheels',$this->slug), 'dashboard');

			$this->options_manager->add_option('settings', new Checkbox_Option(
				'log',
				__('Use log file', $this->slug),
				__('Log all opt-ins and play-results.',$this->slug),
				Settings_Manager::get_setting('log')
			));
			$this->options_manager->add_option('settings', new Pro_option(
				'Woocommerce coupon integration',
				'This is a premium feature which enables <b>fully automated WooCommerce</b> integration. The integration comes with extra coupon settings such as coupon duration and validity settings.'
			));


			$this->options_manager->add_option('settings',new Custom_Option(
				' ',
				'log'
			));

			$this->options_manager->add_option('apis', new Text_Option(
				'mailchimp_api',
				__('MailChimp API Key', $this->slug),
				Settings_Manager::get_setting('mailchimp_api'),
				null,
				__('If you want to use Mailchimp for email optin, enter your API Key here.', $this->slug)
			));

			$this->options_manager->add_option('apis', new Pro_option('Other integrations','<strong>Don\'t want to use MailChimp? The premium version also integrates with:</strong> <ul><li>Your own WordPress database</li><li>Zapier</li><li>Drip</li><li>Mailchimp</li><li>ActiveCampaign</li><li>Campaign Monitor</li><li>GetResponse</li><li>MailerLite</li><li>Klaviyo</li><li>Mailster</li><li>SendInBlue</li><li>Newsletter2Go</li><li>ConvertKit</li><li>Remarkety</li></ul>You can integrate with anything else via webhooks.'));

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
				'vintage' => 'Vintage',
				'deep-purple' => 'Deep Purple',
				'yellow' => 'Yellow',
				'red' => 'Red',
				'orange' => 'Orange',
				'purple' => 'Purple',
				'green' => 'Green',
			];

			$slices = [ [
					'label' => __('5% Discount', $this->slug),
					'value' => '5OFF',
					'chance' => 30,
					'type' => 1
				], [
					'label' => __('No prize', $this->slug),
					'type' => 0
				], [
					'label' => __('Next time', $this->slug),
					'type' => 0
				], [
					'label' => __('Almost!', $this->slug),
					'type' => 0
				], [
					'label' => __('10% Discount', $this->slug),
					'value' => '10OFF',
					'chance' => 30,
					'type' => 1
				], [
					'label' => __('Free Ebook', $this->slug),
					'value' => 'https://google.com/',
					'chance' => 30,
					'type' => 2
				], [
					'label' => __('No Prize', $this->slug),
					'type' => 0
				], [
					'label' => __('No luck today', $this->slug),
					'type' => 0
				], [
					'label' => __('Almost!', $this->slug),
					'type' => 0
				], [
					'label' => __('50% Discount', $this->slug),
					'value' => '50OFF',
					'chance' => 10,
					'type' => 1
				], [
					'label' => __('No prize', $this->slug),
					'type' => 0
				], [
					'label' => __('Unlucky', $this->slug),
					'type' => 0
				],
			];

			$content_settings = new Container_Option(null, __('Content settings',$this->slug));

			$content_settings->options = [
				$this->add_data_attribute_for_data_bind(
					new Text_Option(
						'title',
						__('Title', $this->slug),
						null,
						__('Get your chance to <em>win a price</em>!', $this->slug),
						__('Use <em></em> to emphasise text (it will have a different color).', $this->slug)
					)
				),
				$this->add_data_attribute_for_data_bind(
					new Editor_Option(
						'explainer',
						__('Explainer text', $this->slug),
						null,
						[
							'tinymce' => [
								'toolbar1' => 'bold,italic,underline',
								'toolbar2' => false
							],
							'quicktags' => false
						],
						__('A short paragraph explaining how it works.', $this->slug)
					)
				),
				$this->add_data_attribute_for_data_bind(
					new Editor_Option(
						'disclaimer',
						__('Disclaimer text', $this->slug),
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
						__('Add a short paragraph explaining the rules & regulations.', $this->slug)
					)
				),
				$this->add_data_attribute_for_data_bind(new Text_Option(
					'button_text',
					__('Spin-button text', $this->slug),
					null,
					__('Try your luck', $this->slug),
					__('This text will appear on the button the visitor has to click to spin the wheel.')
				)),
				$this->add_data_attribute_for_data_bind(new Text_Option(
					'close_text',
					__('Close popup text', $this->slug),
					null,
					__("I don't feel lucky", $this->slug),
					__('This link will close the popup. It appears on the lower right side of the popup.')
				)),
				$this->add_data_attribute_for_data_bind(new Text_Option(
					'losing_title',
					__('Losing title', $this->slug),
					null,
					__("Uh oh! Looks like you lost", $this->slug),
					__('This title will appear after a player hits a losing segment.')
				)),
				$this->add_data_attribute_for_data_bind(new Text_Option(
					'losing_text',
					__("Losing text", $this->slug),
					null,
					__("We're sorry, the wheel of fortune has let you down. Better luck next time!", $this->slug),
					__('This text will appear below the losing title after a player hits a losing segment.')
				)),
				$this->add_data_attribute_for_data_bind(new Text_Option(
					'winning_title',
					__('Winning title', $this->slug),
					null,
					__("Hurray! You've hit {x}. Lucky you!", $this->slug),
					__("This title will appear after a player hits a winning segment. Use {x} to denote the segment's label.")
				)),
				$this->add_data_attribute_for_data_bind(new Text_Option(
					'winning_text_coupon',
					__("Winning text for coupons", $this->slug),
					null,
					__("Nicely done! You can use the coupon code below to claim your prize:", $this->slug),
					__('This text will appear below the winning title after a player hits a winning coupon-segment.')
				)),
				$this->add_data_attribute_for_data_bind(new Text_Option(
					'winning_text_link',
					__("Winning text for links", $this->slug),
					null,
					__("Nicely done! here's the link to your free product:", $this->slug),
					__('This text will appear below the winning title after a player hits a winning link-segment.',$this->slug)
				)),
				$this->add_data_attribute_for_data_bind(new Text_Option(
					'button_done',
					__("'Done' button text", $this->slug),
					null,
					__("I'm done playing", $this->slug),
					__('When the player has done playing, this button will appear to allow to close the popup.', $this->slug)
				))
			];

			$design_settings = new Container_Option(null, __('Design settings',$this->slug));
			$design_settings->options = [
				$this->add_data_attribute_for_data_bind(new Dropdown_Option('bgpattern','Background pattern', [
					'none' => 'No pattern',
					'hearts' => 'Hearts'
				], 'hearts', 'More options in Pro.')),
				new Pro_option('Advanced design settings (colors, logo, custom background, confetti, audio, ...)','This is a pro feature. You can define each slice\'s color, add a logo, custom background, and more to make your wheel pop!.')
			];

			$behavior_settings = new Container_Option(null, __('Behavior setting', $this->slug));

			$behavior_settings->options = [
				new Pro_option(
					__('Hide on mobile', $this->slug),
					__('This is a Pro feature.', $this->slug)
				),
                new Pro_option(
                    __('Logged in/logged out', $this->slug),
                    __('This is a Pro feature where you can decide to show the wheel to all users, or only logged in/logged out users.', $this->slug)
                ),
				new Pro_option(
					__('Show on these pages', $this->slug),
					__('This is a Pro feature in which you can decide on which pages or custom post types to show/hide the wheel.', $this->slug)
				)
			];

			array_push($behavior_settings->options,$this->add_data_attribute_for_data_bind(
				new Dropdown_Option(
					'appeartype',
					__('Show wheel',$this->slug), [
						'immediately' => 'Immediately',
						'delay' => 'After a delay',
					],
					null,
					__('More options in Pro, such as by clicking a widget or button, on a timer, ...', $this->slug)
			)));
			array_push($behavior_settings->options,$this->add_data_attribute_for_data_bind(new Number_Option(
				'appeardelay',
				__('Appearance delay',$this->slug),
				5,
				null,null,
				[ new Option_Dependency('appeartype','delay') ],
				__('Show popup after', $this->slug),
				__('seconds', $this->slug)
			)));

			array_push($behavior_settings->options,$this->add_data_attribute_for_data_bind(new Number_Option(
				'occurancedelay',
				__('Occurance delay',$this->slug),
				5,
				null,null,
				[ new Option_Dependency('occurance','delay') ],
				__('Show popup again after', $this->slug),
				__('days', $this->slug)
			)));

                      array_push($behavior_settings->options,$this->add_data_attribute_for_data_bind(new Pro_option(
              __('Spinning Speed', $this->slug),
              __('This is a Pro feature.', $this->slug)
          )));

            array_push($behavior_settings->options,$this->add_data_attribute_for_data_bind(new Pro_option(
                __('Spinning Time', $this->slug),
                __('This is a Pro feature.', $this->slug)
            )));

            			$list_settings = new Container_Option(null, __('List settings',$this->slug));

			$list_settings->options = [
				new Dropdown_Option(
					'list_provider',
					__('List provider', $this->slug),
					[],
					null,
					__('What email list software are you using?', $this->slug)
				),
				new Dropdown_Option(
					'list',
					__('Email list', $this->slug),
					[],
					null,
					__('To which email list should your visitors opt in?', $this->slug)
				),
				new Pro_option('Form fields builder','This is a pro feature in which you can add more fields, rather than only an email field.'),
               new Pro_option(
                    __('Validate email domains.', $this->slug),
                    __('This is a Pro feature where you email addresses will be validated against an up-to-date list of fake emails .', $this->slug)
                ),
                new Pro_option(
                    __('Check IP addresses.', $this->slug),
                    __('This is a Pro anti-cheat feature.', $this->slug)
                ),
                new Pro_option(
                    __('Only display prizes in emails, not on screen.', $this->slug),
                    __('This is a Pro anti-cheat feature.', $this->slug)
                )
			];

			$form_builder_for_lists_settings = 	new Custom_Option(null,'form-builder-lists');

			$gdpr_settings = new Container_Option(null, __('GDPR', $this->slug));
			$gdpr_settings->name = 'gdpr_settings';
			$gdpr_custom_setting = new Custom_Option('Send data to email list','gdpr-settings');
			$gdpr_settings->options = [ $gdpr_custom_setting ];

			$chance_settings = [
				$this->add_data_attribute_for_data_bind(new Number_Option(
					'winning_chance',
					__('Winning chance',$this->slug),
					75,null,
					__("What's the chance your visitor will win something? If you want your visitor to always win (recommended), set this to 100%.", $this->slug),
					null,' ',
					' % '
				)),
				new Pro_option('Replays','Replays is a premium feature. You can set how many times a visitor can retry if they lose.')
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