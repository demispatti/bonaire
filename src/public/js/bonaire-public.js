/**
 * The javascript for the public part.
 *
 * @since      1.0.0
 * @package    Bonaire
 * @subpackage Bonaire/public
 * @author     Demis Patti <demispatti@gmail.com>
 */
"use strict";
(function ( $ ){

    function BonairePublic (){

    }

    BonairePublic.prototype = {

        init: function (){
            this.fillFields();
        },

        fillFields: function (){
            $('.cf-your-name').val('Demis Patti');
            $('.cf-your-email').val('demispatti@gmail.com');
            $('.cf-your-subject').val('Test ');
            $('.cf-your-message').val('Testnachricht. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.');
        }
    };

    $(document).one('ready', function (){

        var bonairePublic = new BonairePublic();
        bonairePublic.init();
    });

})(jQuery);
