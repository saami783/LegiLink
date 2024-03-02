import '../../bootstrap.js';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import '../../styles/user/app.scss';
import 'tw-elements';

import './sidenav';
import './navbar';

document.addEventListener("DOMContentLoaded", function() {
    window.addEventListener('scroll', function () {
        if (window.scrollY > 50) {
            document.getElementById('navbar').classList.add('opaque-navbar');
        } else {
            document.getElementById('navbar').classList.remove('opaque-navbar');
        }
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const sidenavElement = document.getElementById('sidenav-4');
    const instance = new Sidenav(sidenavElement);
});
