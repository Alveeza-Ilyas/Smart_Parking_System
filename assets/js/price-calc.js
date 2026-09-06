/* ============================================================
 * Shared pricing helper.
 *
 * Used by the static (LocalStorage) version and by the PHP
 * booking page, so the price shown on screen always matches the
 * price the backend stores. The mirror of this logic in PHP is
 * parkingPrice() in config/config.php - keep both in sync.
 * ============================================================ */
(function (global) {
    'use strict';

    var DEFAULTS = {
        first: 5.0,      // first hour
        extra: 3.0,      // every additional hour
        dailyMax: 50.0,  // cap per started 24h block
        currency: '$',
        maxHours: 24
    };

    function clampHours(hours, maxHours) {
        var value = parseInt(hours, 10);
        if (isNaN(value) || value < 1) {
            return 1;
        }
        return Math.min(maxHours || DEFAULTS.maxHours, value);
    }

    function calculate(hours, options) {
        var opts = options || {};
        var first = typeof opts.first === 'number' ? opts.first : DEFAULTS.first;
        var extra = typeof opts.extra === 'number' ? opts.extra : DEFAULTS.extra;
        var dailyMax = typeof opts.dailyMax === 'number' ? opts.dailyMax : DEFAULTS.dailyMax;
        var maxHours = typeof opts.maxHours === 'number' ? opts.maxHours : DEFAULTS.maxHours;

        var h = clampHours(hours, maxHours);
        var price = first + (h - 1) * extra;
        var days = Math.ceil(h / 24);

        return Math.round(Math.min(price, days * dailyMax) * 100) / 100;
    }

    function format(amount, currency) {
        var sign = currency || DEFAULTS.currency;
        return sign + Number(amount).toFixed(2);
    }

    function optionsFromNode(node) {
        var data = node.dataset || {};
        return {
            first: parseFloat(data.first),
            extra: parseFloat(data.extra),
            dailyMax: parseFloat(data.dailyMax),
            currency: data.currency || DEFAULTS.currency
        };
    }

    /**
     * Wire a price block to a duration <input>.
     * Expected markup (see booking.php / views/booking.html):
     *   <div data-price-calculator data-first="5" data-extra="3"
     *        data-daily-max="50" data-duration-input="duration">
     *       <span data-price-output>$5.00</span>
     *   </div>
     */
    function bind(node, durationInput) {
        var output = node.querySelector('[data-price-output]');
        if (!output || node.dataset.priceBound === '1') {
            return;
        }
        node.dataset.priceBound = '1';

        var opts = optionsFromNode(node);
        var input = durationInput ||
            document.getElementById(node.dataset.durationInput || 'duration');

        function update() {
            var hours = clampHours(input ? input.value : 1);
            output.textContent = format(calculate(hours, opts), opts.currency);
            output.dataset.hours = String(hours);
        }

        if (input) {
            input.addEventListener('input', update);
            input.addEventListener('change', update);
        }
        update();
    }

    function bindAll(root) {
        (root || document).querySelectorAll('[data-price-calculator]').forEach(function (node) {
            bind(node);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            bindAll();
        });
    } else {
        bindAll();
    }

    global.ParkingPrice = {
        DEFAULTS: DEFAULTS,
        calculate: calculate,
        format: format,
        clampHours: clampHours,
        bind: bind,
        bindAll: bindAll
    };
})(window);
