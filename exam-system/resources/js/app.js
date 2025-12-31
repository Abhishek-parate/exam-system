// resources/js/app.js
import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Global AJAX Polling Helper
window.ExamPoller = {
    intervals: {},
    
    start(key, callback, interval = 5000) {
        this.stop(key);
        this.intervals[key] = setInterval(callback, interval);
    },
    
    stop(key) {
        if (this.intervals[key]) {
            clearInterval(this.intervals[key]);
            delete this.intervals[key];
        }
    },
    
    stopAll() {
        Object.keys(this.intervals).forEach(key => this.stop(key));
    }
};

// Prevent back button during exam
window.preventBack = function() {
    window.history.pushState(null, "", window.location.href);
    window.onpopstate = function() {
        window.history.pushState(null, "", window.location.href);
    };
};
