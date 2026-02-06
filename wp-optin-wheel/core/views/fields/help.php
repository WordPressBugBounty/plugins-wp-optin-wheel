<?php
	use MABEL_WOF_LITE\Core\Common\Managers\Config_Manager;
	/** @var \MABEL_WOF_LITE\Core\Models\Help $help */

?>
<div class="p-t-1">
	<div style="display: none;" id="help-<?php echo esc_attr( $help->id ); ?>">
		<div style="padding:20px;">
			<?php include Config_Manager::$dir . 'admin/views/' . $help->template; ?>
		</div>
	</div>
	<a title="<?php echo esc_attr( $help->title ); ?>" href="#TB_inline?width=600&height=550&inlineId=help-<?php echo esc_attr( $help->id ); ?>" class="primary thickbox">
		<?php echo esc_html( $help->link_title == null ?
                __( 'More info', 'wp-optin-wheel' ) :
                $help->link_title
            )
		?>
	</a>
</div>