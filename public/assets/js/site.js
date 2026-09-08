'use strict';
document.addEventListener('DOMContentLoaded', () => {
    const nav = document.getElementById('mainNav');
    if (nav) nav.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
        const instance = bootstrap.Collapse.getInstance(nav);
        if (instance) instance.hide();
    }));

    const product = new URLSearchParams(window.location.search).get('product');
    const productInput = document.getElementById('product_name');
    if (product && productInput && !productInput.value) {
        productInput.value = product.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }
});

