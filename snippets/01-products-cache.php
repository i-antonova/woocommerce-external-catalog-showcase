/*
========================================
EXTERNAL CATALOG – API PRODUCTS CACHE
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
