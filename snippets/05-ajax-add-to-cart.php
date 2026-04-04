add_action('wp_ajax_ext_catalog_add_to_cart','ext_catalog_ajax_add_to_cart');
add_action('wp_ajax_nopriv_ext_catalog_add_to_cart','ext_catalog_ajax_add_to_cart');

function ext_catalog_ajax_add_to_cart(){

    if(empty($_POST['api_code'])){
        wp_send_json_error();
    }

    $code = sanitize_text_field($_POST['api_code']);

    WC()->cart->add_to_cart(EXTERNAL_CATALOG_VIRTUAL_PRODUCT_ID, 1, 0, [], [
        'api_code' => $code
    ]);

    wp_send_json_success();
}
