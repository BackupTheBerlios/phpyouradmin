/* initialisations diverses de jQuery
//	$(".dateinput").datepicker();
// 	$(".dateinput").datepicker({ showOn: 'focus' });
*/
var tabRteZone = new Array();

if (typeof rteWidth == 'undefined') var rteWidth = 400;
if (typeof rteHeight == 'undefined') var rteHeight = 300;


$(document).ready(function() {
	$(".dateinput").datepicker();
/*	$('.rte-zone').rte("", "/php_inc/jQuery/rteImgs/");*/

	$('.jqrte2').rte({
                css: ['default.css'],
/*                base_url: 'http://mysite.com',*/
                frame_class: 'frameBody',
                width: rteWidth,
                height: rteHeight,
                controls_rte: rte_toolbar,
                controls_html: html_toolbar
        });
});

