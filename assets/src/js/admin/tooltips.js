/**
 * Tooltips functionality
 *
 * @package Bonaire
 * @since 1.0.0
 */

import 'tooltipster';

(function ($) {
    'use strict';

    $(document).ready(function () {
        // Initialize tooltipster for elements with .bonaire-tooltip class
        if (typeof $.fn.tooltipster === 'function') {
            $('.bonaire-tooltip').tooltipster({
                theme: 'tooltipster-shadow',
                animation: 'fade',
                delay: 200,
                maxWidth: 400,
                contentAsHTML: true
            });

            // Initialize tooltips with data attributes
            $('[data-tooltip]').tooltipster({
                theme: 'tooltipster-shadow',
                animation: 'fade',
                delay: 200,
                maxWidth: 400,
                content: function () {
                    return $(this).data('tooltip');
                }
            });
        }
    });
})(jQuery);
