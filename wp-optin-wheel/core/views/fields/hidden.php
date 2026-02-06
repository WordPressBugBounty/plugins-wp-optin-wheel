<?php
/** @var \MABEL_WOF_LITE\Core\Models\Hidden_Option $option */
?>

<input 
    class="mabel-formm-element" 
    type="hidden" 
    name="<?php echo esc_attr( $option->name === null ? $option->id : $option->name ) ?>" 
    value="<?php echo esc_attr( $option->value ) ?>" 
    <?php
    // Output is already properly escaped within the function.
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo $option->get_extra_data_attributes(); 
    ?> 
/>