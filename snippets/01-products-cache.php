/*
========================================
ext_catalog API PRODUCTS CACHE (TEST MODE)
========================================
*/

// =========================
// GET PRODUCTS FROM API
// =========================
function ext_catalog_get_products(){

    $products = get_transient('ext_catalog_products');

    if($products !== false){
        return $products;
    }

    $products = wpgetapi_endpoint('ext_catalog_api','active_products',['debug'=>false]);

    if(empty($products) || !is_array($products)){
        return [];
    }

    set_transient('ext_catalog_products', $products, 600);

    return $products;
}

// =========================
// DIRECT OUTPUT (FOR WPCode)
// =========================
// $products = ext_catalog_get_products();

// echo '<div style="background:#fff;padding:15px;border:2px solid red;">';
// echo '<strong>ext_catalog TEST</strong><br>';
// echo 'Products loaded: '.count($products);
// echo '</div>';
