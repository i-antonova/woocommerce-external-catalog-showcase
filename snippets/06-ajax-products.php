/*
========================================
EXTERNAL CATALOG - AJAX PRODUCTS ENGINE
========================================
*/

if (!function_exists('ext_catalog_normalize_search_text')) {
    function ext_catalog_normalize_search_text($text) {
        $text = trim((string) $text);
        if ($text === '') {
            return '';
        }
        if (function_exists('mb_strtolower')) {
            $text = mb_strtolower($text, 'UTF-8');
        } else {
            $text = strtolower($text);
        }
        if (class_exists('Normalizer')) {
            $decomposed = Normalizer::normalize($text, Normalizer::FORM_D);
            if (is_string($decomposed)) {
                $text = $decomposed;
            }
        }
        return preg_replace('/\p{M}+/u', '', $text);
    }
}

if (!function_exists('ext_catalog_ajax_products')) {

    add_action('wp_ajax_ext_catalog_get_products', 'ext_catalog_ajax_products');
    add_action('wp_ajax_nopriv_ext_catalog_get_products', 'ext_catalog_ajax_products');

    function ext_catalog_ajax_products() {

        $products = ext_catalog_get_products();
        $leaflet  = ext_catalog_get_leaflet_prices();

        $search = ext_catalog_normalize_search_text($_POST['search'] ?? '');
        $min    = floatval($_POST['min'] ?? 0);
        $max    = floatval($_POST['max'] ?? 0);
        $offer  = $_POST['offer'] ?? false;
        $sort   = $_POST['sort'] ?? '';
        $page   = intval($_POST['page'] ?? 1);

        $filtered = [];

        foreach ($products as $p) {

            $name = ext_catalog_normalize_search_text($p['description']);
            $code = ext_catalog_normalize_search_text($p['code']);

            $base  = floatval($p['rtlprice']) * 1.24;
            $final = $leaflet[$p['code']] ?? $base;

            if (
                $search !== '' &&
                mb_strpos($name, $search, 0, 'UTF-8') === false &&
                mb_strpos($code, $search, 0, 'UTF-8') === false
            ) {
                continue;
            }

            if ($min && $final < $min) {
                continue;
            }
            if ($max && $final > $max) {
                continue;
            }
            if ($offer && !isset($leaflet[$p['code']])) {
                continue;
            }

            $p['final_price'] = $final;
            $filtered[] = $p;
        }

        if ($sort === 'asc') {
            usort($filtered, function ($a, $b) {
                return $a['final_price'] <=> $b['final_price'];
            });
        }

        if ($sort === 'desc') {
            usort($filtered, function ($a, $b) {
                return $b['final_price'] <=> $a['final_price'];
            });
        }

        $perPage = 25;
        $offset  = ($page - 1) * $perPage;
        $slice   = array_slice($filtered, $offset, $perPage);

        wp_send_json([
            'products' => $slice,
            'total'    => count($filtered),
        ]);
    }
}
