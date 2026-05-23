/*
========================================
EXTERNAL CATALOG – LEAFLET PRICING ENGINE
========================================
*/

// =========================
// GET LEAFLET PRICES
// =========================

function ext_catalog_get_leaflet_prices() {

    static $prices = null;

    if ($prices !== null) {
        return $prices;
    }

    delete_transient('ext_catalog_leaflet_prices');

    $cache = get_transient('ext_catalog_leaflet_prices');

    if ($cache !== false) {
        return $cache;
    }

    $url = EXTERNAL_CATALOG_LEAFLET_CSV_URL;

    $response = wp_remote_get($url);

    if (is_wp_error($response)) {
        return [];
    }

    $body = wp_remote_retrieve_body($response);

    if (empty($body)) {
        return [];
    }

    $lines = explode("\n", $body);

    $prices = [];

    foreach ($lines as $line) {

        $line = trim($line);

        if (empty($line)) {
            continue;
        }

        $delimiter = (substr_count($line, ';') > substr_count($line, ',')) ? ';' : ',';

        $data = str_getcsv($line, $delimiter);

        if (count($data) < 2) {
            continue;
        }

        if (strtolower(trim($data[0])) === 'code') {
            continue;
        }

        $code = ext_catalog_normalize_code($data[0]);

        $price = floatval(
            str_replace(',', '.', trim($data[1]))
        );

        $prices[$code] = $price;
    }

    set_transient('ext_catalog_leaflet_prices', $prices, 3600);

    return $prices;
}
