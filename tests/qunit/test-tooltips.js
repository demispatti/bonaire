/**
 * The script for the settings menu tooltips.
 *
 * @link
    * @since             0.1.0
 * @package           bonaire
 * @subpackage        bonaire/admin/menu/includes/js
 * Author:            Demis Patti <demispatti@gmail.com>
 * Author URI:        https://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

/**
 * Help
 *
 * Author:            Demis Patti <demispatti@gmail.com>
 * Author URI:        https://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
jQuery(function ( $ ){
    "use strict";

    function BonaireTooltipsTest (){

        this.BonaireTooltipsTest = BonaireTooltipsObject;
        this.body = $('body,html');
        this.form = $('#bonaire_settings_form');
    }

    BonaireTooltipsTest.prototype = {

        init: function (){
            this.addHtmlContainer();
            this.addSymbols();
            this.initTooltips();
        },
        addSymbols: function (){

            $.each(this.BonaireTooltipsTest.option_keys, function ( i ){
                var tooltip = '<span class="bonaire-tooltip-symbol" data-option="' + i + '" data-tooltip-content="#' + i + '_tooltip_content"><i class="dashicons dashicons-info" aria-hidden="true"></i></span>';
                $(tooltip).insertAfter($('[name="bonaire_options[' + i + ']"]'));
            });
        },
        addHtmlContainer: function (){

            var tooltips = this.BonaireTooltipsTest.tooltips;

            var html = '<div class="tooltip_templates">';
            $.each(tooltips, function ( i ){
                var content = tooltips[i];

                html += '<span id="' + i + '_tooltip_content" class="bonaire-the-tooltip" >' + content + '</span>';
            });
            html += '</div>';

            this.body.append(html);
            $('.tooltip_templates').css('display', 'none');
        },
        initTooltips: function (){

            $('.bonaire-tooltip-symbol').tooltipster({
                animation: 'grow',
                delay: 200,
                theme: 'tooltipster-shadow',
                trigger: 'click',
                position: 'right',
                interactive: true,
                minWidth: '360px',
                maxWidth: '760px'
            });
        }
    };

    $(document).one('ready', function (){

        if (true === false){
            var bonaireTooltipsTest = new BonaireTooltipsTest();
            bonaireTooltipsTest.init();
        }

    });

});
