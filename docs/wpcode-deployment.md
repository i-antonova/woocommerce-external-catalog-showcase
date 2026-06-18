# WPCode deployment notes

## Do not duplicate snippets

Copying a snippet into a new WPCode entry while leaving the original enabled can cause:

- `Cannot redeclare function ...` (PHP fatal error)
- Duplicate `wp_ajax_*` handlers
- Site-wide breakage (shortcodes like `[product_table]` may print as literal text)

**Prefer:** paste into the **existing** snippet and disable/delete any duplicate.

## Suggested load order

1. `01-products-cache.php` — API transient
2. `02-cart-engine.php` — cart metadata + pricing
3. `03-leaflet-pricing.php` — seasonal CSV prices
4. `05-ajax-add-to-cart.php` — add to cart AJAX
5. `06-ajax-products.php` — list/filter AJAX (depends on 01, 03)
6. `04-ui-rendering.php` — **catalog page only** (HTML/JS)

## Guards

Snippet 06 uses `function_exists()` around shared handlers so a accidental double-include is less likely to fatal the site — still avoid duplicate active snippets.
