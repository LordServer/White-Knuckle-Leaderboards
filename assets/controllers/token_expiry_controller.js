import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['checkbox', 'expiresAt'];

    connect() {
        console.log('token-expiry controller loaded');
        this.toggle();
    }

    toggle() {
        const disabled = this.checkboxTarget.checked;

        this.expiresAtTarget.disabled = disabled;

        if (disabled) {
            this.expiresAtTarget.value = '';
        }
    }
}
