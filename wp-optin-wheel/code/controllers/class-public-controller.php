<?php

namespace MABEL_WOF_LITE\Code\Controllers
{

	use MABEL_WOF_LITE\Code\Models\Wheel_Model;
	use MABEL_WOF_LITE\Code\Models\Wheels_VM;
	use MABEL_WOF_LITE\Code\Services\Log_Service;
	use MABEL_WOF_LITE\Code\Services\MailChimp_Service;
	use MABEL_WOF_LITE\Code\Services\Wheel_service;
	use MABEL_WOF_LITE\Core\Common\Frontend;
	use MABEL_WOF_LITE\Core\Common\Html;
	use MABEL_WOF_LITE\Core\Common\Linq\Enumerable;
	use MABEL_WOF_LITE\Core\Common\Managers\Config_Manager;
	use MABEL_WOF_LITE\Core\Common\Managers\Settings_Manager;

	if(!defined('ABSPATH')){die;}

	class Public_Controller extends Frontend
	{
		public function __construct()
		{
			parent::__construct();

			$this->add_script_dependencies('jquery');
			$this->add_script(Config_Manager::$slug,'public/js/public.min.js');
			$this->frontend_js_var = 'wofVars';

			$this->add_styles_but_dont_publish_yet(Config_Manager::$slug,'public/css/public.min.css');

			$this->add_styles_but_dont_publish_yet('wof-theme-vintage','public/css/theme-vintage.css');
			$this->add_styles_but_dont_publish_yet('wof-theme-deep-purple','public/css/theme-deep-purple.css');
			$this->add_styles_but_dont_publish_yet('wof-theme-yellow','public/css/theme-yellow.css');
			$this->add_styles_but_dont_publish_yet('wof-theme-red','public/css/theme-red.css');
			$this->add_styles_but_dont_publish_yet('wof-theme-orange','public/css/theme-orange.css');
			$this->add_styles_but_dont_publish_yet('wof-theme-purple','public/css/theme-purple.css');
			$this->add_styles_but_dont_publish_yet('wof-theme-green','public/css/theme-green.css');

			add_action('wp_footer', [ $this,'add_wheels' ], 1);

			$this->add_ajax_function('wof-lite-email-optin', $this, 'add_email_to_list',true, true);
			$this->add_ajax_function('wof-lite-play', $this, 'play',true, true);

            add_filter( 'rocket_lrc_exclusions', function( $exclusions ) {
                $exclusions[] = 'wof-wheels';
                return $exclusions;
            } );
        }

		public function play($wheel) {
			if(!isset($_POST['nonce']) || !isset($_POST['id']) || !isset($_POST['action']) ||
			   !isset($_POST['seq']) || !isset($_POST['pseq']) )
				wp_send_json_error(__('Not allowed.',Config_Manager::$slug));

			if(empty($wheel))
				$wheel = Wheel_service::get_wheel($_POST['id']);

			$current_play = Wheel_service::validate_sequence($wheel, $_POST['seq'], $_POST['pseq']);

			if(!is_int($current_play))
				wp_send_json_error(__('Not allowed.',Config_Manager::$slug));

			$segment = Wheel_service::calculate_segment_hit($wheel);

			$winning = $segment->type != 0;
			$is_last = true;

			if(Settings_Manager::get_setting('log') === true)
				Log_Service::log( sprintf('%s turned wheel %d, and landed on segment %d. They %s.%s',
					$_POST['mail'],
					$wheel->id,
					$segment->id,
					$winning? 'won' : 'lost',
					$winning? 'They hit "' . $segment->label . '", with value "'.$segment->value.'"' :''
				));

			wp_send_json_success( [
				'segment' => $segment->id,
				'winning' => $winning,
				'title' => $this->get_segment_title($wheel,$segment),
				'text' => $this->get_segment_text($wheel,$segment),
				'value' => $winning ? $segment->value : null,
				'seq' => Wheel_service::get_sequence($wheel,$current_play+1),
				'html' => Html::view($winning ? 'response-done' : ($is_last ? 'response-done' : 'response-lost'), $wheel)
			] );
		}

		public function add_email_to_list() {
			if(!isset($_POST['nonce']) || !isset($_POST['id']) || !isset($_POST['mail']) ||
			    !isset($_POST['seq']) || !isset($_POST['pseq']) )
				wp_send_json_error(__('Not allowed.',Config_Manager::$slug));

			$email = sanitize_email($_POST['mail']);
			if (!filter_var($email, FILTER_VALIDATE_EMAIL))
				wp_send_json_error(__('Badly formatted email.', Config_Manager::$slug));

			$wheel = Wheel_service::get_wheel($_POST['id']);

			if(Log_Service::is_in_log($email,$wheel->id)){
				wp_send_json_error(__("Email already used", Config_Manager::$slug));
			}

			$fields = isset($_POST['fields']) ? json_decode(sanitize_text_field(stripslashes($_POST['fields']))) : [];

			$should_optin = true;
			$response = null;

			if ( $wheel->has_setting( 'optin_if_checked' )) {

				$checkboxes = explode( ',', $wheel->optin_if_checked );
				foreach ( $checkboxes as $checkbox ) {

					$field = Enumerable::from( $fields )->firstOrDefault( function ( $x ) use ( $checkbox ) {
						return $x->id === $checkbox;
					} );

					if ( $field != null && $field->value === false ) {
						$should_optin = false;
						break;
					}
				}
			}

			if($should_optin) {
				$fieldsForProvider = Enumerable::from( $fields )->where( function ( $x ) {
					return $x->type !== 'consent_checkbox';
				} )->toArray();

				switch ( $wheel->list_provider ) {
					default :
						$response = MailChimp_Service::add_to_list( $wheel->list, $email, $fieldsForProvider );
						break;
				}
			}

			if(is_string($response))
				wp_send_json_error(__($response, Config_Manager::$slug));

			if(Settings_Manager::get_setting('log') === true)
				Log_Service::log($email.' opted into wheel '.$wheel->id.(!$should_optin ? '. They chose not to opt in to your list': ''));

			Log_Service::add_to_db_log($email,$wheel->id);

			$this->play($wheel);
		}

		public function add_wheels() {
			$model = new Wheels_VM();
			$model->wheels = $this->get_active_wheels();
			if(count($model->wheels) > 0 && !wp_style_is(Config_Manager::$slug,'enqueued'))
				wp_enqueue_style(Config_Manager::$slug);
			foreach($model->wheels as $wheel) {
				$handle = 'wof-theme-'.$wheel->theme;
				if(!wp_style_is($handle,'enqueued'))
					wp_enqueue_style($handle);
			}

			Html::partial('code/views/wheels', $model);
		}

		private function get_segment_title(Wheel_Model $wheel, $segment) {
			if($segment->type == 0)
				return $wheel->has_setting('losing_title') ? $wheel->losing_title : __('Uh oh!', Config_Manager::$slug);

			return str_replace(
				'{x}',
				'<em>'.$segment->label.'</em>',
				$wheel->has_setting('winning_title')? $wheel->winning_title : __('Hurray!', Config_Manager::$slug)
			);
		}

		private function get_segment_text(Wheel_Model $wheel, $segment) {
			switch($segment->type){
				case 0: return $wheel->losing_text;
				case 1: return $wheel->winning_text_coupon;
				case 2: return $wheel->winning_text_link;
			}
			return $wheel->losing_text;
		}

		private function get_active_wheels() {
			$wheels = Wheel_service::get_all_wheels();
			$allowed_wheels = [];

			foreach($wheels as $wheel) {
				if($wheel->active != 1)
					continue;

				array_push($allowed_wheels,$wheel);
			}

			return $allowed_wheels;
		}
	}
}