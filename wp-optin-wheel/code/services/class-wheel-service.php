<?php

namespace MABEL_WOF_LITE\Code\Services
{

	use MABEL_WOF_LITE\Code\Models\Wheel_Model;
	use MABEL_WOF_LITE\Core\Common\Linq\Enumerable;

	class Wheel_service
	{
		private static $post_type = 'wof_lite_wheel';
		private static $allow_html_minimal = [
			'a' => [
				'href' => [],
				'title' => []
			],
			'b' => [],
			'em' => [],
			'strong' => [],
			'i' => [],
			'span' => [ 'style' ],
			'ul' => [],
			'li' => []
		];

		public static function get_sequence(Wheel_Model $w, $play = 1) {
			return Helper_Service::encrypt(join('::', [
				$w->id,
				$play,
				time()-1505392740
			]));
		}

		public static function validate_sequence(Wheel_Model $wheel,$sequence, $psequence) {
			$seq = explode('::',Helper_Service::decrypt($sequence));
			$pseq = explode('::',Helper_Service::decrypt($psequence));
			$current_play = intval($seq[1]);
			$previous_play = intval($pseq[1]);

			if(sizeof($seq) != 3 || sizeof($pseq) != 3) return false;
			if($wheel->id != $seq[0] || $wheel->id != $pseq[0]) return false;
			if($current_play > 1 && $sequence === $psequence) return false;
			if($previous_play > $current_play) return false;
			if($current_play === $previous_play && $current_play != 1) return false;

			if($previous_play != $current_play) {
				$time = intval($seq[2]);
				if((time()-1505392740) - $time > 1200) return false;
			}

			return $current_play;
		}

		public static function calculate_segment_hit(Wheel_Model $wheel) {
			$will_win = mt_rand(0,100) <= $wheel->winning_chance;

			if(!$will_win){
				$losing_segments = Enumerable::from($wheel->slices)->where(function($x){return $x->type == 0;})->toArray();
				$losing_segment = $losing_segments[array_rand($losing_segments)];
				return $losing_segment;
			}else{
				$winning_segments = Enumerable::from($wheel->slices)->where(function($x){return $x->type != 0 && $x->chance != 0;})->toArray();

				usort($winning_segments, function($a,$b){
					if($a->chance === $b->chance) return 0;
					return (intval($a->chance) < intval($b->chance)) ? -1 : 1;
				});

				$rand = mt_rand(0, 100);

				$cumul = 0;
				foreach($winning_segments as $segment) {
					$cumul += intval($segment->chance);
					if($rand < $cumul)
						return $segment;
				}

				return null;

			}
		}

		public static function raw_to_wheel($raw) {
			$wheel = new Wheel_Model();
			$options = $raw['options'];

			$wheel->id = $raw['id'];
			$wheel->active = $raw['active'];
			$wheel->theme = $options->theme;
			$wheel->slices = $options->slices;
			$wheel->winning_chance = $options->winning_chance;

			if(!empty($options->title))
				$wheel->title = wp_kses($options->title, self::$allow_html_minimal);

			$wheel->bgpattern = $options->bgpattern;

			if(!empty($options->explainer))
				$wheel->explainer = wp_kses($options->explainer, self::$allow_html_minimal);
			if(!empty($options->disclaimer))
				$wheel->disclaimer = wp_kses($options->disclaimer, self::$allow_html_minimal);
			if(!empty($options->email_placeholder))
				$wheel->email_placeholder = wp_strip_all_tags($options->email_placeholder);
			if(!empty($options->button_text))
				$wheel->button_text = wp_strip_all_tags($options->button_text);
			if(!empty($options->close_text))
				$wheel->close_text = wp_strip_all_tags($options->close_text);

			if(!empty($options->list_provider))
				$wheel->list_provider = $options->list_provider;
			if(!empty($options->list))
				$wheel->list = $options->list;
			else{
				if(!empty($options->mailchimp_list))
					$wheel->list = $options->mailchimp_list;
				if(!empty($options->cm_list))
					$wheel->list = $options->cm_list;
				if(!empty($options->ac_list))
					$wheel->list = $options->ac_list;
			}


			if(!empty($options->losing_title))
				$wheel->losing_title = $options->losing_title;
			if(!empty($options->winning_title))
				$wheel->winning_title = $options->winning_title;
			if(!empty($options->losing_text))
				$wheel->losing_text = $options->losing_text;
			if(!empty($options->winning_text_coupon))
				$wheel->winning_text_coupon = $options->winning_text_coupon;
			if(!empty($options->winning_text_link))
				$wheel->winning_text_link = $options->winning_text_link;
			if(!empty($options->button_done))
				$wheel->button_done = $options->button_done;

			if(!empty($options->appeartype))
				$wheel->appeartype = $options->appeartype;
			if(!empty($options->appeardelay))
				$wheel->appeardelay = $options->appeardelay;
			if(!empty($options->occurance))
				$wheel->occurance = $options->occurance;
			else $wheel->occurance = 'delay';
			if(isset($options->occurancedelay))
				$wheel->occurancedelay = $options->occurancedelay;

			if(!empty($options->fields))
				$wheel->fields = $options->fields;
			if(!empty($options->optin_if_checked))
				$wheel->optin_if_checked = $options->optin_if_checked;

			return $wheel;
		}

		public static function toggle_activation($id, $toggle) {
			wp_update_post( [
				'ID' => $id,
				'post_status' => $toggle == 1 ? 'publish' : 'draft'
			] );
		}

		public static function get_wheel($id) {
			$post = get_post($id);

			$wheel = [
				'id' => $post->ID,
				'options'  => json_decode(get_post_meta($post->ID,'options',true)),
				'active' => $post->post_status === 'publish' ? 1 : 0
			];
			return self::raw_to_wheel($wheel);
		}

		public static function get_all_wheels() {

			$post_ids = new \WP_Query( [
				'post_type' => self::$post_type,
				'fields' => 'ids',
				'posts_per_page' => 3,
			] );

			$wheels = [];

			foreach ($post_ids->posts as $id) {
				$obj = [
					'id' => $id,
					'options' => json_decode(get_post_meta($id, 'options', true)),
					'active' =>  get_post_status($id) === 'publish' ? 1 : 0
				];
				array_push($wheels,self::raw_to_wheel($obj));
			}

			wp_reset_postdata();
			return $wheels;
		}

		public static function delete_wheel($id) {

            if( ! empty( $id ) ) {

                $id = intval( trim ( $id ) );

                if( ! empty( $id ) ) {
                    $post_type = get_post_type( $id );
                    if( $post_type ===  self::$post_type ) {
                        wp_delete_post( $id, true );
                    }
                }
            }
		}

		public static function edit_wheel($id,$data) {
			update_post_meta($id,'options', $data);
		}

		public static function add_wheel($data) {

			$id = wp_insert_post( [
				'post_type' => self::$post_type,
				'post_status' => 'publish'
			], true);

			if(!is_wp_error( $id ) && $id > 0){
				add_post_meta($id,'options', $data);

				return $id;
			}
			return null;
		}
	}
}