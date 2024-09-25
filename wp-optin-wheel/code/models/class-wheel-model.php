<?php

namespace MABEL_WOF_LITE\Code\Models {

	use MABEL_WOF_LITE\Core\Common\Linq\Enumerable;
	use MABEL_WOF_LITE\Core\Common\Managers\Config_Manager;

	class Wheel_Model {

		public $id;
		public $active = '1';
		public $theme = 'vintage';

		public $winning_chance = 70;
		public $slices = [];

		public $bgpattern = 'hearts';

		public $title;
		public $explainer;
		public $disclaimer;
		public $email_placeholder;
		public $button_text;
		public $close_text;

		public $appeartype = 'delay';
		public $appeardelay = 5;
		public $occurance = 'delay';
		public $occurancedelay = 14;

		public $list_provider;
		public $list;

		public $losing_title;
		public $losing_text;
		public $winning_title;
		public $winning_text_coupon;
		public $winning_text_link;
		public $button_done;

		public $optin_if_checked;
		public $fields = [];

		public function get_options_for_frontend() {
			$options = [];

			switch($this->appeartype){
				case 'immediately':
					$options['appear'] = 'time';
					$options['appearData'] = 0;
					break;
				case 'delay':
					$options['appear'] = 'time';
					$options['appearData'] = $this->appeardelay;
					break;
			}

			switch($this->occurance) {
				case 'delay':
					$options['occurance'] = 'time';
					$options['occuranceData'] = $this->occurancedelay;
					break;
			}

			return htmlspecialchars(json_encode($options), ENT_QUOTES, 'UTF-8');
		}

		public function classes() {
			$classes = [
				'wof-wheel',
				'wof-theme-'.$this->theme,
			];

			return join(' ', $classes);
		}

		public function data_attributes() {

			$elements = [
				'id' => $this->id,
				'options' => esc_attr($this->get_options_for_frontend()),
				'fields' => esc_attr(json_encode($this->fields))
			];

			return join(' ', Enumerable::from($elements)->select(function($v,$k){
				return 'data-'.$k .'="'.esc_attr($v) .'"';
			})->toArray());
		}

		public function has_setting($key) {
			return !empty($this->{$key});
		}

		public function setting_or_default($key,$default){
			return $this->has_setting($key) ? $this->{$key} : $default;
		}

		public function get_background(){
			$url = Config_Manager::$url . 'public/img/';
			switch($this->bgpattern){
				case 'hearts':
					return 'background-image:url(\''.$url.'bg-hearts.png\');opacity:.085;background-size:11%;';
				default: return '';
			}
		}

	}
}