<?php
$css_import = "
/* ------------------- Allgemeine Style-Definitionen ------------------- */

/* Angaben der Schriftdefinition für die gesamte Seite */
BODY {
	font-family: {body_font_face};
	font-size: {body_font_size};
	color: {body_font_color};
}


/* Allgemeine Definition der Links */
A {
	font-family : {body_font_face};
	font-size : 11px;
	color : #000000;
}

A:link {
	font-family: {body_font_face};
	font-size: 11px;
	text-decoration: underline;
	color: #000000;

}

A:hover {
	font-family: {body_font_face};
	font-size: 11px;
	color: #4665B5;
	text-decoration: none;
}

P, TD {
	font-family : {body_font_face};
	font-size : 11px;
}

HR {
	color : #666666;
	height : 1px;
}

LEGEND {
	font-family : {body_font_face};
	font-size : 11px;
	padding-bottom : 5px;
	padding-top : 5px;
	padding-right : 5px;
	padding-left : 5px;
}

LABEL {
	font-family : {body_font_face};
	font-size : 11px;
	color : Black;
}

/* ------------------- Styleing des oberen Engine-Bereichs ------------------- */
/* ------------------- Styleing der Willkommensleiste ------------------- */


/*  Styling der Leiste oben, die Willkommensdaten und Buttons für Suche, Mitglieder und Uploads zeigt */
.info_row_top {
	font-size: {row_top_font_size};
    color : {row_top_font_color};
}

.main_row_top {
	background-image : url(images/bg_nav.gif);
	background-repeat : repeat-x;	
}


/* Definition der Kategorienleiste zeigt die Navigation bzw. die UR-here Leiste */
.catrow {
	font-family: {body_font_face};
	font-size: {breadcrumb_font_size};
	color: {breadcrumb_font_color};
}

A.catrow, A.catrow:LINK, A.catrow:VISITED, A.catrow:ACTIVE {
	font-family: {body_font_face};
	font-size: {breadcrumb_font_size};
	color: {breadcrumb_font_color};
	text-decoration : underline;
}

A.catrow:HOVER {
	font-family: {body_font_face};
	font-size: {breadcrumb_font_size};
	color: {breadcrumb_font_color_hover};
	text-decoration : none;
}


/* Styling aller Input und Textare Felder innerhalb der Engine */
.select {
	font-size : 10px;
	color :#4665B5;
	font-size : 10px;
	border-color: #517196; 
	border-colordark: #517196; 
	border-colorlight: #CCCCCC; 
	background-image: url(\'images/input.gif\');
	border : 1px solid #000000;	
}


/* Styling der Input-Felder in der Quick-Leiste */
INPUT.quick, TEXTAREA.quick {
	color :#4665B5;
	font-size : 10px;
	border-color: #517196; 
	border-colordark: #517196; 
	border-colorlight: #CCCCCC; 
	background-image: url(\'images/input.gif\');
	border : 1px solid #000000;
	font-family : {body_font_face};
}


/* Styling der Input-Felder und Textarea allgemein */
INPUT.input, TEXTAREA.input, SELECT.input {
	color :#4665B5;
	font-size : 10px;
	font-family : {body_font_face};
}


/* ------------------- Style der Linken Boxen ------------------- */


/* Überschrift für die Linken Boxen */
.left_box_headline {
	font-size : 10px;
	color : {content_highlight_font_color};
	background-color : {content_highlight_background_color};
	background-image : url(images/bg.gif);
	background-repeat : repeat-x;	
}


/* Inhalt der linken Boxen */
.left_box_content {
	font-size : 10px;
    background-color : {content_background_color_even};    
}


/* Links in den linken Boxen */
A.left_box_content, A.left_box_content:LINK, A.left_box_content:VISITED, A.left_box_content:ACTIVE {
	font-size : 10px;
	text-decoration : underline;
}

A.left_box_content:HOVER {
	font-size : 10px;
	text-decoration : none;
	color : #0055A8;
}


/* Linien in den linken Boxen */
HR.left_box_content {
	color : #000000;
	height : 1px;
}


/* ------------------- Style der rechten Boxen ------------------- */
/* ------------------- Boxen im Mittelteil ------------------- */


/* Überschrift für die rechte Box */
.pop_box_headline {
	font-size : 10px;
	color : #FFFFFF;
	background-color : {content_highlight_background_color};
	background-image : url(images/bg.gif);
	background-repeat : repeat-x;		
}


/* Inhalt der rechten Box */
.pop_box_content {
	font-size : 10px;
	color : #696969;
    background : {content_background_color_even};
}


/* Links in der rechten Box */
A.pop_box_content, A.pop_box_content:LINK, A.pop_box_content:VISITED, A.pop_box_content:ACTIVE {
	font-size : 10px;
	text-decoration : underline;
	color : #696969;
}

A.pop_box_content:HOVER {
	font-size : 10px;
	text-decoration : none;
	color : #000000;
}


/* ------------------- Style des Hauptteils Mitte ------------------- */
/* ------------------- Kategorien, Unterkategorien ------------------- */
/* ------------------- Inhaltsansicht, Mittelbox ------------------- */


/* Überschrift der Kategorie */
.cat_main_headline {
	font-size : 14px;
	font-weight : bold;
    padding-bottom : 41px;
}


/* Überschrift der angezeigten Unterkategorien */
.cat_headline {
	font-size : 12px;
	font-weight : bold;
}

A.cat_headline, A.cat_headline:LINK, A.cat_headline:VISITED, A.cat_headline:ACTIVE {
	font-size : 12px;
	font-weight : bold;
	text-decoration : underline;
}

A.cat_headline:HOVER {
	font-size : 12px;
	font-weight : bold;
	text-decoration : none;
    color: #FF3300;
	/*color : #0055A8;*/
}


/* Anzeige Kategorien und Anzahl Files in der Kategorie */
.cat_subcat_info {
	color : Gray;
	font-size : 10px;
}


/* Inhalt Kategorie */
.cat_content {
	font-size : 11px;
}


.page_step {
	font-family : {body_font_face};
	font-size : 11px;
	color : Black;
}

A.page_step {
	font-family : {body_font_face};
	font-size : 11px;
	color : Black;
	text-decoration : underline;
}

A.page_step:HOVER {
	font-family : {body_font_face};
	font-size : 11px;
	color : #0055A8;
	text-decoration : none;
}

.highlight_text {
	font-size: 11px;
	color: #FF0000;
}

.message_area {
	font-family: {body_font_face};
	font-size: 11px;
}

/* ------------------- Style abgesetzter Listen (Kommentare, Gästebuch) ------------------- */


/* Abgesetzte Einträge-Überschrift Gross */
.entry_headline_big {
	font-family : {body_font_face};
	font-size : 14px;
	color : Black;
	font-weight : bold;
	font-style : italic;
}


/* Abgesetzte Einträge-Überschrift Klein */
.entry_headline_small {
	font-family : {body_font_face};
	font-size : 10px;
	color : Black;
}


/* Username in der Profilansicht */
.entry_headline_big_light {
	font-family : {body_font_face};
	font-size : 14px;
	color : White;
	font-weight : bold;
	font-style : italic;
}


/* Gruppenbezeichnung in der Profilansicht */
.entry_headline_small_light {
	font-family : {body_font_face};
	font-size : 10px;
	color : White;
}


/* Abgesetzte Einträge Inhalt */
.entry_content {
	font-family : {body_font_face};
	font-size : 11px;
	color : Black;
}


/* ------------------- Style aller Listen ------------------- */


/* Listenüberschriften */
.list_headline {
	font-family : {body_font_face};
	font-size : {content_font_size};
	background-color : {content_highlight_background_color};
	background-image : url(images/bg.gif);
	background-repeat : repeat-x;	
	font-weight : bold;
	color : {content_highlight_font_color};
	padding : 3px 3px 3px 3px;
}

A.list_headline {
	font-family : {body_font_face};
	font-size : {content_font_size};
	background-color : {content_highlight_background_color};
	font-weight : bold;
	color : {content_highlight_font_color};
}

A.list_headline:HOVER {
	font-family : {body_font_face};
	font-size : {content_font_size};
	background-color : {content_highlight_background_color};
	font-weight : bold;
	color : {content_highlight_font_color_hover};
}


/* Helle Zeilen in Listen */
.list_light {
	font-family : {body_font_face};
	font-size : {content_font_size};
	background-color : {content_background_color_odd};
	color : {content_font_color};
	padding : 3px 3px 3px 3px;
}

A.list_light {
	font-family : {body_font_face};
	font-size : {content_font_size};
	background-color : {content_background_color_odd};
	color : {content_font_color};
}

A.list_light:HOVER {
	font-family : {body_font_face};
	font-size : {content_font_size};
	background-color : {content_background_color_odd};
	color : {content_font_color_hover};
}


/* Dunkle Zeilen in Listen */
.list_dark {
	font-family : {body_font_face};
	font-size : {content_font_size};
	background-color : {content_background_color_even};
	color : {content_font_color};
	padding : 3px 3px 3px 3px;
}

A.list_dark {
	font-family : {body_font_face};
	font-size : {content_font_size};
	background-color : {content_background_color_even};
	color : {content_font_color};
}

A.list_dark:HOVER {
	font-family : {body_font_face};
	font-size : {content_font_size};
	background-color : {content_background_color_even};
	color : {content_font_color_hover};
}


/* ------------------- Styling der unteren Leiste ------------------- */


/* Styling der Leiste unten, die den Quick-Search und die Navigaiton zwischen den Kategorien zeigt */
.info_row_bottom {
	font-size: {row_bottom_font_size};
    color : {row_bottom_font_color};
}

.main_row_bottom {
	background-image : url(images/bg_nav.gif);
	background-repeat : repeat-x;	
}


/* ------------------- Styling des Copyright Vermerk ------------------- */


/* Copyright-Vermerk
*/
.copyright {
	font-size : 10px;
}

A.copyright, A.copyright:LINK, A.copyright:VISITED, A.copyright:ACTIVE {
	font-size : 10px;
	text-decoration : none;
}

A.copyright:HOVER {
	font-size : 10px;
	text-decoration : underline;
}";
?>
