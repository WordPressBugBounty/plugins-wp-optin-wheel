<?php

namespace MABEL_WOF_LITE\Core\Common
{
	use MABEL_WOF_LITE\Core\Common\Managers\Config_Manager;
	use MABEL_WOF_LITE\Core\Common\Managers\Options_Manager;
	use MABEL_WOF_LITE\Core\Models\Start_VM;


	abstract class Admin extends Presentation_Base
	{
		public $options_manager;
		public $add_mediamanager_scripts;
		private static $notices = [];
		public $capability;

		public function __construct(Options_Manager $options_manager)
		{
			parent::__construct();

			$this->capability = 'manage_options';

			$this->add_mediamanager_scripts = false;
			$this->options_manager = $options_manager;

			$this->add_script_dependencies('jquery');

			$this->add_script(Config_Manager::$slug,'admin/js/admin.min.js');
			$this->add_style(Config_Manager::$slug,'admin/css/admin.min.css');

			$this->loader->add_action('admin_menu', $this, 'add_menu');
			$this->loader->add_filter('plugin_action_links_' . Config_Manager::$plugin_base, $this, 'add_settings_link');

			$this->loader->add_action( 'admin_init', $this, 'init_settings');
			if(isset($_GET['page']) && $_GET['page'] === Config_Manager::$slug)
			{
				$this->loader->add_action( 'admin_enqueue_scripts', $this, 'register_styles' );
				$this->loader->add_action( 'admin_enqueue_scripts', $this, 'register_scripts' );
				$this->loader->add_action('admin_init',$this,'init_admin_page');
				$this->loader->add_action('admin_notices', $this,'show_admin_notices');
			}
		}

		public function show_admin_notices() {
			$notices = self::$notices;

			foreach( $notices as $notice ) {
				echo '<div class="notice is-dismissible notice-'.$notice['class'].'"><p>'.$notice['message'].'</p></div>';
			}

		}

		public abstract function init_admin_page();

		public function add_settings_link( $links )
		{
			$my_links = [
				'<a href="' . admin_url( 'options-general.php?page=' .Config_Manager::$slug ) . '">' .__('Settings' , Config_Manager::$slug). '</a>',
			];
			return array_merge( $links, $my_links );
		}

		public function add_menu()
		{
			$page = add_options_page('', Config_Manager::$name, $this->capability, Config_Manager::$slug, [ $this,'display_settings'] );
		}

		public function init_settings() {
            register_setting(
                Config_Manager::$slug,
                Config_Manager::$settings_key,
                [
                    'sanitize_callback' => function ( $settings ) {
                    
                        if ( isset( $settings['mailchimp_api'] ) && ! preg_match('/^[a-f0-9]{32}-us\d+$/i', $settings['mailchimp_api'])) {
                            return get_option( Config_Manager::$settings_key ); // Prevent saving
                        }
                        
                        return $settings; // Valid, save
                        
                    }
                ]
            );
            
			//register_setting( Config_Manager::$slug , Config_Manager::$settings_key );
		}

		public function display_settings()
		{
			$model = new Start_VM();
			$model->settings_key = Config_Manager::$settings_key;
			$model->sections = $this->options_manager->get_sections();
			$model->hidden_settings = $this->options_manager->get_hidden_settings();
			$model->slug = Config_Manager::$slug;

			ob_start();
			include Config_Manager::$dir . 'core/views/start.php';
			echo ob_get_clean();
		}

		public function register_styles() {
			if(isset($_GET['page']) && $_GET['page'] == Config_Manager::$slug)
				parent::register_styles();
		}

		public function register_scripts()
		{
			if(isset($_GET['page']) && $_GET['page'] == Config_Manager::$slug)
				parent::register_scripts();
			if($this->add_mediamanager_scripts){
				wp_enqueue_media();
			}
		}
	}
}