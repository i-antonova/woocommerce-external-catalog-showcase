/*
========================================
ext_catalog CART ENGINE (FINAL)
========================================
*/

// =========================
// NORMALIZE CODE
// =========================
if(!function_exists('ext_catalog_normalize_code')){
    function ext_catalog_normalize_code($code){
        $code = trim($code);
        $code = ltrim($code, '0');
        $code = preg_replace('/[^0-9]/', '', $code);
        return $code;
    }
}

// =========================
// ADD DATA TO CART
// =========================
add_filter('woocommerce_add_cart_item_data','ext_catalog_add_cart_item_data',10,3);

function ext_catalog_add_cart_item_data($cart_item_data,$product_id,$variation_id){

    if($product_id != EXTERNAL_CATALOG_VIRTUAL_PRODUCT_ID) return $cart_item_data;

    if(empty($_REQUEST['api_code'])) return $cart_item_data;

    // Load products safely
    $products = get_transient('ext_catalog_products');

    if(empty($products)){
        $products = wpgetapi_endpoint('ext_catalog_api','active_products',['debug'=>false]);

        if(!empty($products)){
            set_transient('ext_catalog_products',$products,600);
        }
    }

    if(empty($products)) return $cart_item_data;

    $code = sanitize_text_field($_REQUEST['api_code']);

    foreach($products as $item){

        if(ext_catalog_normalize_code($item['code']) == ext_catalog_normalize_code($code)){

            // Prevent adding unavailable
            if(intval($item['availability']) <= 0){
                wc_add_notice('Product not available','error');
                return $cart_item_data;
            }

            $cart_item_data['api_code']  = $code;
            $cart_item_data['api_name']  = $item['description'];
            $cart_item_data['api_image'] = $item['image'];
            $leaflet = ext_catalog_get_leaflet_prices();
$norm = ext_catalog_normalize_code($code);

if(isset($leaflet[$norm])){
    // Leaflet price is gross; convert to net for cart
    $cart_item_data['api_price'] = $leaflet[$norm] / 1.24;
}else{
    $cart_item_data['api_price'] = floatval($item['rtlprice']);
}

            break;
        }
    }

    if(isset($cart_item_data['api_code'])){
        $cart_item_data['unique_key'] = md5($cart_item_data['api_code']);
    }

    return $cart_item_data;
}

// =========================
// RESTORE CART DATA
// =========================
add_filter('woocommerce_get_cart_item_from_session','ext_catalog_restore_cart_item',20,2);

function ext_catalog_restore_cart_item($cart_item,$values){

    foreach(['api_price','api_name','api_image','api_code'] as $key){
        if(isset($values[$key])){
            $cart_item[$key] = $values[$key];
        }
    }

    if(isset($cart_item['api_price'])){
        $cart_item['data']->set_price($cart_item['api_price']);
    }

    return $cart_item;
}

// =========================
// SET FINAL PRICE
// =========================
add_action('woocommerce_before_calculate_totals','ext_catalog_set_cart_price',20);

function ext_catalog_set_cart_price($cart){

    if(is_admin() && !defined('DOING_AJAX')) return;

    foreach($cart->get_cart() as $cart_item){
        if(isset($cart_item['api_price'])){
            $cart_item['data']->set_price($cart_item['api_price']);
        }
    }
}

// =========================
// DISPLAY NAME
// =========================
add_filter('woocommerce_cart_item_name','ext_catalog_cart_item_name',10,3);

function ext_catalog_cart_item_name($name,$cart_item,$cart_item_key){
    return $cart_item['api_name'] ?? $name;
}

// =========================
// DISPLAY IMAGE
// =========================
add_filter('woocommerce_cart_item_thumbnail','ext_catalog_cart_item_thumbnail',10,3);

function ext_catalog_cart_item_thumbnail($thumbnail,$cart_item,$cart_item_key){

    if(!empty($cart_item['api_image'])){
        return '<img src="'.esc_url($cart_item['api_image']).'" width="60">';
    }

    return $thumbnail;
}

// =========================
// DISPLAY CODE IN CART
// =========================
add_filter('woocommerce_get_item_data','ext_catalog_display_code',10,2);

function ext_catalog_display_code($item_data,$cart_item){

    if(isset($cart_item['api_code'])){
        $item_data[] = [
            'name' => 'Code',
            'value' => $cart_item['api_code']
        ];
    }

    return $item_data;
}

// =========================
// HIDE API PRODUCT FROM SHOP
// =========================
add_action('pre_get_posts', function($query){

    if(!is_admin() && $query->is_main_query() && (is_shop() || is_product_category())){

        $exclude = $query->get('post__not_in');

        if(!is_array($exclude)) $exclude = [];

        $exclude[] = EXTERNAL_CATALOG_VIRTUAL_PRODUCT_ID;

        $query->set('post__not_in',$exclude);
    }
});
