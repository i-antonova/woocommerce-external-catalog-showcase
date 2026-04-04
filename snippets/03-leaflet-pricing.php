/*
========================================
ext_catalog LEAFLET PRICING ENGINE
========================================
*/

// =========================
// GET LEAFLET PRICES
// =========================
function ext_catalog_get_leaflet_prices(){

    $cache = get_transient('ext_catalog_leaflet_prices');
    if($cache !== false) return $cache;

    $url = EXTERNAL_CATALOG_LEAFLET_CSV_URL;

    $prices = [];

    if(($handle = fopen($url,'r')) !== false){

        while(($data = fgetcsv($handle,0,';')) !== false){

            if(count($data) < 2) continue;
            if(strtolower($data[0]) === 'code') continue;

            $code  = ext_catalog_normalize_code($data[0]);
            $price = floatval(str_replace(',','.',$data[1])); // GROSS

            $prices[$code] = $price;
        }

        fclose($handle);
    }

    set_transient('ext_catalog_leaflet_prices',$prices,3600);

    return $prices;
}
