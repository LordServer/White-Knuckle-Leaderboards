import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ['status', 'banDays']

    connect() {
        this.toggle();
    }

    toggle() {
        this.banDaysTarget.hidden =
            this.statusTarget.value !== 'suspended';
    }
}
