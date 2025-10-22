/**
 * Admin JavaScript - Main Entry Point
 *
 * @package Bonaire
 * @since 1.0.0
 */

import alertify from 'alertifyjs';
import { BonaireAdmin } from './BonaireAdmin';

(function ($) {
    'use strict';

    $(document).ready(function () {
        // Make alertify available globally for WordPress compatibility
        window.alertify = alertify;

        // Initialize admin interface
        const admin = new BonaireAdmin($, alertify);
        admin.init();
    });
})(jQuery);
