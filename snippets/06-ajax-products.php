/*
========================================
ext_catalog AJAX PRODUCTS ENGINE
========================================
*/

add_action('wp_ajax_ext_catalog_get_products','ext_catalog_ajax_products');
add_action('wp_ajax_nopriv_ext_catalog_get_products','ext_catalog_ajax_products');

function ext_catalog_ajax_products(){

    $products = ext_catalog_get_products();
    $leaflet  = ext_catalog_get_leaflet_prices();

    $search = strtolower($_POST['search'] ?? '');
    $min = floatval($_POST['min'] ?? 0);
    $max = floatval($_POST['max'] ?? 0);
//     $available = $_POST['available'] ?? false;
	$offer = $_POST['offer'] ?? false;
    $sort = $_POST['sort'] ?? '';
    $page = intval($_POST['page'] ?? 1);

    $filtered = [];

    foreach($products as $p){

        $name = mb_strtolower($p['description']);
        $code = mb_strtolower($p['code']);

        $base = floatval($p['rtlprice']) * 1.24;
        $final = $leaflet[$p['code']] ?? $base;

        if($search && strpos($name,$search)===false && strpos($code,$search)===false) continue;
        if($min && $final < $min) continue;
        if($max && $final > $max) continue;
//         if($available && intval($p['availability'])<=0) continue;
		if($offer && (!isset($leaflet[$p['code']]))) continue;

        $p['final_price'] = $final;
        $filtered[] = $p;
    }

    if($sort === 'asc'){
        usort($filtered,function($a,$b){ return $a['final_price'] <=> $b['final_price']; });
    }

    if($sort === 'desc'){
        usort($filtered,function($a,$b){ return $b['final_price'] <=> $a['final_price']; });
    }

    $perPage = 25;
    $offset = ($page-1)*$perPage;

    $slice = array_slice($filtered,$offset,$perPage);

    wp_send_json([
        'products' => $slice,
        'total' => count($filtered)
    ]);
}
