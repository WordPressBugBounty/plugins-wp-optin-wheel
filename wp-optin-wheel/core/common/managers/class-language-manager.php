<?php

namespace MABEL_WOF_LITE\Core\Common\Managers
{

	use MABEL_WOF_LITE\Core\Common\Registry;

	if(!defined('ABSPATH')){die;}

	/**
	 * Loads the translations in 'languages' folder.
	 * Class Language
	 * @package MABEL_WOF_LITE\Includes\Core
	 */
	class Language_Manager
	{
		protected $language_folder = 'languages';

		public function __construct()
		{
			Registry::get_loader()->add_action('plugins_loaded', $this, 'load_text_domain');
		}

		public function load_text_domain()
		{
			load_plugin_textdomain(
				Config_Manager::$slug,
				false,
				plugin_basename(Config_Manager::$slug) .'/'. $this->language_folder . '/'
			);
		}
	}
}
