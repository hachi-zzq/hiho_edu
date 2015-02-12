/* To avoid CSS expressions while still supporting IE 7 and IE 6, use this script */
/* The script tag referring to this file must be placed before the ending body tag. */

/* Use conditional comments in order to target IE 7 and older:
	<!--[if lt IE 8]><!-->
	<script src="ie7/ie7.js"></script>
	<!--<![endif]-->
*/

(function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'hihoedu-icon\'">' + entity + '</span>' + html;
	}
	var icons = {
		'icon-note': '&#xe013;',
		'icon-video': '&#xe03a;',
		'icon-uniE05E': '&#xe05e;',
		'icon-play': '&#xe071;',
		'icon-group': '&#xe001;',
		'icon-smile-c': '&#xe021;',
		'icon-love': '&#xe02a;',
		'icon-dislike': '&#xe02b;',
		'icon-picture': '&#xe02e;',
		'icon-pause-c': '&#xe045;',
		'icon-play-c': '&#xe046;',
		'icon-stop-c': '&#xe049;',
		'icon-plus-c': '&#xe053;',
		'icon-share': '&#xe05b;',
		'icon-help': '&#xe05d;',
		'icon-trash': '&#xe072;',
		'icon-up-c': '&#xe078;',
		'icon-right-c': '&#xe079;',
		'icon-left-c': '&#xe07a;',
		'icon-down-c': '&#xe07b;',
		'icon-ok-c': '&#xe080;',
		'icon-time2-c': '&#xe081;',
		'icon-del-c': '&#xe082;',
		'icon-preview': '&#xe087;',
		'icon-love2': '&#xe08a;',
		'icon-info-c': '&#xe08b;',
		'icon-time-c': '&#xe094;',
		'icon-add-c': '&#xe095;',
		'icon-loading': '&#xe098;',
		'icon-reload': '&#xe099;',
		'icon-settings': '&#xe09a;',
		'icon-edot-c': '&#xe600;',
		'icon-love-c': '&#xe601;',
		'icon-search': '&#xf002;',
		'icon-star': '&#xf005;',
		'icon-star-o': '&#xf006;',
		'icon-check': '&#xf00c;',
		'icon-times': '&#xf00d;',
		'icon-navicon': '&#xf0c9;',
		'icon-caret-down': '&#xf0d7;',
		'icon-angle-up': '&#xf106;',
		'icon-angle-down': '&#xf107;',
		'icon-ellipsis-h': '&#xf141;',
		'0': 0
		},
		els = document.getElementsByTagName('*'),
		i, c, el;
	for (i = 0; ; i += 1) {
		el = els[i];
		if(!el) {
			break;
		}
		c = el.className;
		c = c.match(/[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
}());
