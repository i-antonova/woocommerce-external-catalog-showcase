/*
========================================
EXTERNAL CATALOG UI PACK (FINAL)
UI + PREFETCH + ANIMATIONS
========================================
*/

echo '<script>
window.ext_catalog_AJAX = "'.admin_url('admin-ajax.php').'";
</script>';
?>

<style>

/* BASE */
body{
    font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;
}

/* GRID */
.ext_catalog-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(220px,1fr));
    gap:20px;
}

/* CARD */
.ext_catalog-card{
    display:flex;
    flex-direction:column; /* KEY for equal height */
    border:1px solid #eee;
    border-radius:12px;
    padding:15px;
    background:#fff;
    position:relative;
    transition:all .25s ease;
    opacity:0;
    transform:translateY(10px);
    animation:fadeIn .3s ease forwards;
}

.ext_catalog-card:hover{
    box-shadow:0 15px 35px rgba(0,0,0,0.12);
    transform:translateY(-6px) scale(1.01);
}

@keyframes fadeIn{
    to{opacity:1; transform:translateY(0);}
}

/* IMAGE WRAPPER */
.ext_catalog-img-wrap{
    overflow:hidden;
    border-radius:8px;
    z-index:1;
}

/* IMAGE */
.ext_catalog-img-wrap img{
    width:100%;
    height:180px;
    object-fit:contain;
    transition:transform .35s ease;
}

/* ZOOM */
.ext_catalog-card:hover .ext_catalog-img-wrap img{
    transform:scale(1.08);
    filter:brightness(1.05);
}

/* TITLE */
.ext_catalog-title{
    font-size:14px;
    line-height:1.2em;
    margin:8px 0;
}

/* DESKTOP TITLE CONTROL (5 lines) */
@media (min-width: 768px){

    .ext_catalog-title{
        display:-webkit-box;
        -webkit-line-clamp:5;
        -webkit-box-orient:vertical;
        overflow:hidden;
        min-height:6em; /* keeps alignment */
    }

}

/* CONTENT PUSH (align buttons nicely) */
.ext_catalog-price{
    font-size:18px;
    font-weight:bold;
    margin-top:auto; /* pushes price + button to bottom */
}

/* BUTTON */
.ext_catalog-btn{
    width:100%;
    padding:10px;
    border:none;
    border-radius:8px;
    background:#000;
    color:#fff;
    cursor:pointer;
    transition:.2s;
    margin-top:8px;
}

.ext_catalog-btn:hover{
    background:#333;
}

.ext_catalog-btn:disabled{
    background:#ccc;
}

/* BADGE */
.ext_catalog-badge{
    position:absolute;
    top:10px;
    left:10px;
    background:#e60023;
    color:#fff;
    font-size:12px;
    padding:4px 6px;
    border-radius:4px;
    z-index:20;
    transform:translateZ(0);
}

/* OLD PRICE */
.ext_catalog-old{
    text-decoration:line-through;
    color:#999;
}

/*RESPONSIVE GRID IMPROVEMENT */

/* Tablet */
@media (max-width: 1024px){
    .ext_catalog-grid{
        grid-template-columns:repeat(auto-fill,minmax(200px,1fr));
    }
}

/* Small tablets / large phones */
@media (max-width: 768px){
    .ext_catalog-grid{
        grid-template-columns:repeat(auto-fill,minmax(180px,1fr));
        gap:16px;
    }
}

/* MOBILE (1 COLUMN OPTIMIZED) */
@media (max-width: 480px){

    .ext_catalog-grid{
        grid-template-columns:1fr; /* force single column */
        gap:14px;
        padding:0 10px;
    }

    .ext_catalog-card{
        padding:12px;
        border-radius:10px;
    }

    .ext_catalog-img-wrap img{
        height:140px;
    }

    .ext_catalog-title{
        font-size:13px;
        min-height:auto;
        display:block;
    }

    .ext_catalog-price{
        font-size:16px;
    }

    .ext_catalog-btn{
        padding:9px;
        font-size:14px;
    }
}

/* FILTERS */
.ext_catalog-filters{
    position:sticky;
    top:80px;
    z-index:1000;
    background:#fff;
    padding:12px;
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    border-bottom:1px solid #eee;
}

.ext_catalog-filters input,
.ext_catalog-filters select{
    padding:8px 10px;
    border:1px solid #ddd;
    border-radius:8px;
}

.ext_catalog-check{
    display:flex;
    align-items:center;
    gap:6px;
    font-size:14px;
}

.ext_catalog-check input{
    vertical-align:middle;
}

/* TOAST */
.ext_catalog-toast{
    position:fixed;
    bottom:20px;
    right:20px;
    background:#000;
    color:#fff;
    padding:12px 18px;
    border-radius:8px;
    opacity:0;
    transform:translateY(20px);
    transition:.3s;
    z-index:9999;
}

.ext_catalog-toast.show{
    opacity:1;
    transform:translateY(0);
}

/* LOAD MORE */
.ext_catalog-load-wrap{
    text-align:center;
    margin:30px 0;
}

.ext_catalog-load-btn{
    padding:10px 20px;
    border:none;
    border-radius:10px;
    background:#000;
    color:#fff;
    cursor:pointer;
}

/*  SKELETON */
.skeleton{
    height:250px;
    background:linear-gradient(90deg,#eee,#f5f5f5,#eee);
    animation:shine 1.2s infinite;
    border-radius:10px;
}

@keyframes shine{
    0%{background-position:-200px;}
    100%{background-position:200px;}
}

/* CART ANIMATION */
@keyframes ext_catalog-bounce{
    0%{transform:scale(1);}
    30%{transform:scale(1.25);}
    60%{transform:scale(0.9);}
    100%{transform:scale(1);}
}

.ext_catalog-cart-bounce{
    animation:ext_catalog-bounce .4s ease;
}

mark{
    background:yellow;
}

</style>

<div id="toast" class="ext_catalog-toast"></div>

<div class="ext_catalog-filters">
    <input id="search" placeholder="Search...">
    <input id="min" type="number" placeholder="Min €">
    <input id="max" type="number" placeholder="Max €">
    <label class="ext_catalog-check">
    <input type="checkbox" id="offer">
    <span>Only offers</span>
	</label>
    <select id="sort">
        <option value="">Sort</option>
        <option value="asc">Price ↑</option>
        <option value="desc">Price ↓</option>
    </select>
    <button onclick="resetFilters()">Reset</button>
</div>

<div id="results-count"></div>
<div id="ext_catalog-container"></div>

<script>

let page = 1;
let prefetched = null;

/*  HIGHLIGHT */
function highlight(text, search){
    if(!search) return text;
    let i = text.toLowerCase().indexOf(search.toLowerCase());
    if(i === -1) return text;
    return text.substring(0,i)+"<mark>"+text.substring(i,i+search.length)+"</mark>"+text.substring(i+search.length);
}

/* TOAST */
function toast(msg, ok=true){
    let t=document.getElementById("toast");
    t.innerHTML = ok
        ? `✔ ${msg} <a href="/cart" style="color:#fff;margin-left:10px;">View cart</a>`
        : `⚠ ${msg}`;
    t.style.background = ok ? "#0a7d00" : "#b00020";
    t.classList.add("show");
    setTimeout(()=>t.classList.remove("show"),3000);
}

/* SKELETON */
function showSkeleton(){
    let c=document.getElementById("ext_catalog-container");
    c.innerHTML='<div class="ext_catalog-grid">'+Array(8).fill('<div class="skeleton"></div>').join('')+'</div>';
}

/* FETCH */
function fetchProducts(reset=false){

    if(reset){
        page=1;
        prefetched=null;
        showSkeleton();
    }

    if(prefetched && !reset){
        render(prefetched);
        prefetched=null;
        prefetchNext();
        return;
    }

    fetch(window.ext_catalog_AJAX,{
        method:"POST",
        body:new URLSearchParams({
            action:"ext_catalog_get_products",
            search:search.value,
            min:min.value,
            max:max.value,
            offer:offer.checked?1:0,
            sort:sort.value,
            page:page
        })
    })
    .then(r=>r.json())
    .then(res=>{
        render(res);
        prefetchNext();
    });
}

/* RENDER */
function render(res){

    let container=document.getElementById("ext_catalog-container");

    if(page===1){
        container.innerHTML='<div class="ext_catalog-grid"></div>';
        document.getElementById("results-count").innerHTML=res.total+" products found";
    }

    let grid=container.querySelector(".ext_catalog-grid");
    let html="";

    res.products.forEach(p=>{

        let base=parseFloat(p.rtlprice||0)*1.24;
        let final=parseFloat(p.final_price||0);
        let isOffer=Math.abs(final-base)>0.01 && final<base;

        html+=`
<div class="ext_catalog-card">

${isOffer?`<div class="ext_catalog-badge">-${Math.round((1-final/base)*100)}%</div>`:""}

<div class="ext_catalog-img-wrap">
    <img src="${p.image}" loading="lazy">
</div>

<div class="ext_catalog-title">${highlight(p.description,search.value)}</div>

<div><strong>Code:</strong> ${p.code}</div>

<div class="ext_catalog-price">
${isOffer?`<div class="ext_catalog-old">€${base.toFixed(2)}</div>`:""}
€${final.toFixed(2)} (incl. VAT)
</div>

<div>${p.availability>0 ? "Available: "+p.availability : "Out of stock"}</div>

<button class="ext_catalog-btn"
onclick="animateBtn(this); addToCart('${p.code}'); flyToCart(this);">
Add to cart
</button>

</div>`;
    });

    grid.insertAdjacentHTML("beforeend",html);

    let old=document.getElementById("load");
    if(old) old.remove();

    if(res.products.length===25){
        container.insertAdjacentHTML("beforeend",
        `<div class="ext_catalog-load-wrap">
        <button id="load" class="ext_catalog-load-btn" onclick="page++;fetchProducts()">Load more</button>
        </div>`);
    }
}

/* PREFETCH */
function prefetchNext(){
    fetch(window.ext_catalog_AJAX,{
        method:"POST",
        body:new URLSearchParams({
            action:"ext_catalog_get_products",
            search:search.value,
            min:min.value,
            max:max.value,
            offer:offer.checked?1:0,
            sort:sort.value,
            page:page+1
        })
    })
    .then(r=>r.json())
    .then(res=>{ prefetched=res; });
}

/* CART */
function addToCart(code){
    fetch(window.ext_catalog_AJAX,{
        method:"POST",
        body:new URLSearchParams({
            action:"ext_catalog_add_to_cart",
            api_code:code
        })
    })
    .then(r=>r.json())
    .then(res=>{
        if(res.success){
            toast("Product added",true);
            refreshCart();
            updateCartCount();
            animateCart();
        }else{
            toast("Error",false);
        }
    });
}

function refreshCart(){
    if(window.jQuery){
        jQuery(document.body).trigger("wc_fragment_refresh");
    }
}

function animateBtn(btn){
    btn.style.transform="scale(.95)";
    setTimeout(()=>btn.style.transform="scale(1)",150);
}

function animateCart(){
    let c=document.querySelector('.et-cart-info');
    if(!c) return;
    c.classList.remove('ext_catalog-cart-bounce');
    void c.offsetWidth;
    c.classList.add('ext_catalog-cart-bounce');
}

/* FILTERS */
function resetFilters(){
    search.value="";
    min.value="";
    max.value="";
    offer.checked=false;
    sort.value="";
    fetchProducts(true);
}

search.oninput=()=>fetchProducts(true);
min.oninput=()=>fetchProducts(true);
max.oninput=()=>fetchProducts(true);
offer.onchange=()=>fetchProducts(true);
sort.onchange=()=>fetchProducts(true);

fetchProducts(true);

</script>
