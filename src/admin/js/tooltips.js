/**
 * The javascript for the settings page tooltips.
 *
 * @since      0.9.0
 * @package    Bonaire
 * @subpackage Bonaire/admin
 * @author     Demis Patti <demispatti@gmail.com>
 */
"use strict";
jQuery(function ( $ ){
    "use strict";

    function BonaireTooltips (){

        this.bonaireTooltips = BonaireTooltipsObject;
        this.body = $('body,html');
        this.form = $('#bonaire_settings_form');
    }

    BonaireTooltips.prototype = {

        init: function (){
            this.addHtmlContainer();
            this.addSymbols();
            this.initTooltips();
        },
        addSymbols: function (){

            $.each(this.bonaireTooltips.option_keys, function ( i ){
                var tooltip = '<span class="bonaire-tooltip-symbol" data-option="' + i + '" data-tooltip-content="#' + i + '_tooltip_content"><i class="dashicons dashicons-info" aria-hidden="true"></i></span>';
                $(tooltip).insertAfter($('[name="bonaire_options[' + i + ']"]'));
            });
        },
        addHtmlContainer: function (){

            var tooltips = this.bonaireTooltips.tooltips;

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

        var bonaireTooltips = new BonaireTooltips();
        bonaireTooltips.init();
    });

});
