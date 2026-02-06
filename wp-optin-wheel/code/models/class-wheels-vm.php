<?php

namespace MABEL_WOF_LITE\Code\Models {

	class Wheels_VM
	{
		/**
		 * @var Wheel_Model[]
		 */
		public $wheels;

		public function __construct() {
			$this->wheels = [];
		}

	}
}