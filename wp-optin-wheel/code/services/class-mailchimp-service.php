<?php

namespace MABEL_WOF_LITE\Code\Services
{

	use MABEL_WOF_LITE\Core\Common\Linq\Enumerable;
	use MABEL_WOF_LITE\Core\Common\Managers\Settings_Manager;

	class MailChimp_Service
	{
		public static function add_to_list($list_id, $email, $fields) {

			if(self::is_in_list($email,$list_id))
				return "This email address is already used.";

			$data = [
				'email_address' => $email,
				'status' =>  'subscribed'
			];

			if(!empty($fields)){
				$merge_fields = [];
				foreach($fields as $field){
					$merge_fields[$field->id] = $field->value;
				}
				$data['merge_fields'] = $merge_fields;
			}

			$response = self::request('lists/'.$list_id.'/members/'.md5(strtolower($email)),$data);

			if(isset($response->status) && $response->status == 400)
				return $response->detail;

			return true;
		}

		public static function get_email_lists() {
			$payload = self::request('lists?count=100', null, 'get');
			if($payload === null) return [];
			$list_objects = Enumerable::from($payload->lists)->select(function($x){
				return  ['id' => $x->id, 'name' => $x->name];
			})->toArray();

			$lists = [];

			foreach($list_objects as $list) {
				array_push($lists, [ 'id' => $list['id'], 'title' => $list['name'] ] );
			}

			return $lists;
		}

		public static function is_in_list($email,$listId) {
			$hash = md5(strtolower($email));
			$response = self::request('lists/'.$listId.'/members/'.$hash,null,'get');
			if($response->status === 'subscribed' || $response->status === 'pending') return true;
			return false;
		}

		public static function get_fields_from_list($listId){
			$response = self::request('lists/'.$listId.'/merge-fields',null,'get');
			return Enumerable::from($response->merge_fields)->where(function($x){return $x->type === 'text';})->select(function($x){
				return [ 'id' => $x->tag, 'title' => $x->name ];
			})->toArray();
		}

		private static function request($type, array $body = null, $method = 'post') {
			$api_key = Settings_Manager::get_setting('mailchimp_api');
			if($api_key === null) return null;

			$data_center = explode('-',$api_key)[1];
			$url = 'https://' . $data_center . '.api.mailchimp.com/3.0/'.$type.'/';

			$headers = [
				'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
				'Content-Type' => 'application/json'
			];

			$options = [
				'timeout' => 15,
				'headers' => $headers
			];

			if($body != null) {
				$options['body'] = json_encode( $body );
				$options['method'] = 'PUT';
			}

			$response = $method === 'post' ? wp_remote_post( $url, $options) : wp_remote_get($url,$options);

			if(is_wp_error($response)) return null;

			return json_decode(wp_remote_retrieve_body($response ));
		}

	}
}