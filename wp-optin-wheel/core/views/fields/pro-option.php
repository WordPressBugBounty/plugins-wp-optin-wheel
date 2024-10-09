<?php
/** @var \MABEL_WOF_LITE\Core\Models\Pro_option $option */
?>

<div class="pro-option-teaser">
	<?php echo $option->value? $option->value : __("This option is available in the premium version.", \MABEL_WOF_LITE\Core\Common\Managers\Config_Manager::$slug); ?>
</div>
