import './bootstrap.js';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your guest_base.html.twig.
 */
import './styles/app.scss';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

document.addEventListener("DOMContentLoaded", function() {
    window.addEventListener('scroll', function () {
        if (window.scrollY > 50) {
            document.getElementById('navbar').classList.add('opaque-navbar');
        } else {
            document.getElementById('navbar').classList.remove('opaque-navbar');
        }
    });
});
