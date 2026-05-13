import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import '@tailwindplus/elements';

document.addEventListener('turbo:load', function() {
    const selector = document.getElementById('form-control');

    selector.addEventListener('change', function() {
        const url = this.value;
        if (url) {
            window.location.href = url; // Redirects to the selected route
        }
    });
});
