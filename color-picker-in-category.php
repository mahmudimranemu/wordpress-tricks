/**
 * Add new colorpicker field to "Add new Category" screen
 * - https://developer.wordpress.org/reference/hooks/taxonomy_add_form_fields/
 *
 * @param String $taxonomy
 *
 * @return void
 */
function colorpicker_field_add_new_category( $taxonomy ) {

  ?>

    <div class="form-field term-colorpicker-wrap">
        <label for="term-colorpicker">Category Color</label>
        <input name="_category_color" value="#ffffff" class="colorpicker" id="term-colorpicker" />
        <p>This is the field description where you can tell the user how the color is used in the theme.</p>
    </div>

  <?php

}
add_action( 'category_add_form_fields', 'colorpicker_field_add_new_category' );  // Variable Hook Name

/**
 * Add new colopicker field to "Edit Category" screen
 * - https://developer.wordpress.org/reference/hooks/taxonomy_add_form_fields/
 *
 * @param WP_Term_Object $term
 *
 * @return void
 */
function colorpicker_field_edit_category( $term ) {

    $color = get_term_meta( $term->term_id, '_category_color', true );
    $color = ( ! empty( $color ) ) ? "#{$color}" : '#ffffff';

  ?>

    <tr class="form-field term-colorpicker-wrap">
        <th scope="row"><label for="term-colorpicker">Select Color</label></th>
        <td>
            <input name="_category_color" value="<?php echo $color; ?>" class="colorpicker" id="term-colorpicker" />
            <p class="description">This is the field description where you can tell the user how the color is used in the theme.</p>
        </td>
    </tr>

  <?php


}
add_action( 'category_edit_form_fields', 'colorpicker_field_edit_category' );   // Variable Hook Name

/**
 * Term Metadata - Save Created and Edited Term Metadata
 * - https://developer.wordpress.org/reference/hooks/created_taxonomy/
 * - https://developer.wordpress.org/reference/hooks/edited_taxonomy/
 *
 * @param Integer $term_id
 *
 * @return void
 */
function save_termmeta( $term_id ) {

    // Save term color if possible
    if( isset( $_POST['_category_color'] ) && ! empty( $_POST['_category_color'] ) ) {
        update_term_meta( $term_id, '_category_color', sanitize_hex_color_no_hash( $_POST['_category_color'] ) );
    } else {
        delete_term_meta( $term_id, '_category_color' );
    }

}
add_action( 'created_category', 'save_termmeta' );  // Variable Hook Name
add_action( 'edited_category',  'save_termmeta' );  // Variable Hook Name


/* enqueue wp-color-picker*/
function category_colorpicker_enqueue( $taxonomy ) {

    if( null !== ( $screen = get_current_screen() ) && 'edit-category' !== $screen->id ) {
        return;
    }

    // Colorpicker Scripts
    wp_enqueue_script( 'wp-color-picker' );

    // Colorpicker Styles
    wp_enqueue_style( 'wp-color-picker' );

}
add_action( 'admin_enqueue_scripts', 'category_colorpicker_enqueue' );


/**
 * Print javascript to initialize the colorpicker
 * - https://developer.wordpress.org/reference/hooks/admin_print_scripts/
 *
 * @return void
 */
function colorpicker_init_inline() {

    if( null !== ( $screen = get_current_screen() ) && 'edit-category' !== $screen->id ) {
        return;
    }

  ?>

    <script>
        jQuery( document ).ready( function( $ ) {

            $( '.colorpicker' ).wpColorPicker();

        } ); // End Document Ready JQuery
    </script>

  <?php

}
add_action( 'admin_print_scripts', 'colorpicker_init_inline', 20 );


function the_category_colors() {

  $categories = get_the_category();

	$color = get_term_meta( $category->term_id, '_category_color', true );
	$color = ( ! empty( $color ) ) ? "#{$color}" : '#000000';

  $separator = '';
  $output = '';
  if($categories){

      foreach($categories as $category) {
          $output .= '<span class="post-category" style="background-color: #'.  get_term_meta( $category->term_id, '_category_color', true ) . '; "><a href="'.get_category_link($category->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ) ) . '">'.$category->cat_name.'</a></span>'.$separator;
      }
      echo trim($output, $separator);
  }
}