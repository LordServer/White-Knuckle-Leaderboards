import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        document.cookie =
            'timezone=' +
            encodeURIComponent(
                Intl.DateTimeFormat().resolvedOptions().timeZone
            ) +
            '; path=/; max-age=31536000';
    }
}
