require (['../../js/jquery-1.6.2.min'], function ($) {
	require({
		baseUrl: '../../js/'
	}, [
		"navigation",
		'js/jszip.min.js',
		'js/jszip-utils.min.js',
		'getUrlVars',
		'xpath'
	], function( 
		nav,
		JSZip,
		JSZipUtils,
		urlVars,
		xpath
	) {

		var book_url = getUrlVars()['book'];

		// on page load check if a bookmark exists and jump
		// to that location
		function jumpToBookmark () {
			jQuery.ajax({
				url: "index.php?getbookmark=" + book_url,
			}).done(function(xPath) {
				var el = document.evaluate(xPath, document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
				jQuery (el).css("color", "red");

				// scroll to bookmark location and account for the header
				window.scrollTo (0, el.offsetTop - 90);
			});
		}

		function pageSettings (id, units, getURL) {
			jQuery ("#" + id).bind ("change", function () {
				var value = jQuery (this).val();
				jQuery ("#content").css (id, value + units);
				jumpToBookmark ();

				jQuery.ajax({
					url: "index.php?" + getURL + "=" + value,
					context: document.body
				}).done(function(data) {});
			});

			jQuery.ajax({
				url: "index.php?getpagesettings=" + id,
				context: document.body
			}).done(function(data) {
				jQuery ("#content").css (id, jQuery ("#" + id).val(parseInt(data)).val() + units);
			});

			jumpToBookmark ();
		}

		if (book_url != undefined && book_url != null) {
			JSZipUtils.getBinaryContent(book_url, function(err, data) {
				var elt = document.getElementById('jszip_utils');

				if ( err ) {
					console.log (elt, err);
					return;
				}

				try {
					var zip = new JSZip(data);
					var valid_paths = ["", "OEBPS/"];
					var path = "";

					// find toc location and save its path
					var toc_zip;
					for (var i = 0; i < valid_paths.length; i++) {
						toc_zip = zip.file(valid_paths[i] + "toc.ncx");

						if (toc_zip != null) {
							path = valid_paths[i];

							break;
						}
					}

					var toc = toc_zip.asText();
					var toc_dom = new DOMParser().parseFromString(toc,'text/xml');
					var toc_result = toc_dom.evaluate('/', toc_dom, null, XPathResult.FIRST_ORDERED_NODE_TYPE , null).singleNodeValue;

					var title = toc_result.getElementsByTagName("docTitle")[0].getElementsByTagName("text")[0].innerHTML;
					var navigation_items = toc_result.getElementsByTagName("navPoint");

					var output_toc = "<h1>" + title + "</h1><ul>";
					var output_chapters = '';

					for (var i = 0; i < navigation_items.length; i++) {
						var current_item = navigation_items[i];

						var list_title = current_item.getElementsByTagName("navLabel")[0].getElementsByTagName("text")[0].innerHTML;
						var list_link = current_item.getElementsByTagName("content")[0].getAttributeNode("src").value.replace(/%20/g, ' ').replace(/%2C/g, ',').split('#')[0];

						var chapter = zip.file(path + list_link).asText();
						var chapter_dom = new DOMParser().parseFromString(chapter,'text/xml');
						var chapter_body = chapter_dom.evaluate('/', chapter_dom,  null, XPathResult.FIRST_ORDERED_NODE_TYPE , null).singleNodeValue.getElementsByTagName("body")[0].innerHTML;
						var deep_link_id = "deep-link" + i;

						output_toc += "<li><a href=\"#" + deep_link_id + "\">" + list_title + "</a></li>";
						output_chapters += "<article class=\"chapter\" id=" + deep_link_id + "><h2>" + list_title + "</h2>" + chapter_body + "</article>";
					}
					output_toc += "</ul>";

					document.getElementById("content").innerHTML += output_toc + output_chapters;

					// when clicking a passage in the book
					// save the xpath location of that paragragh
					// to the database and then turn it red
					jQuery ("#content").delegate ("p", "click", function () {
						var el = this;
						var xPath = getElementXPath (el);

						jQuery (this).parent().find("p").removeAttr("style");

						jQuery.ajax({
							url: "index.php?bookmark=" + xPath + "&book=" + book_url,
							context: document.body
						}).done(function() {
							jQuery (el).css("color", "red");
						});
					});

					jumpToBookmark ();
				} catch(e) {
					console.log (elt, e);
				}
			});

			jQuery.ajax({
				url: "index.php?getpagesettings=font-size",
				context: document.body
			}).done(function(data) {
				jQuery ("#content").css ("font-size", parseInt(data) + "px");
			});

			jQuery.ajax({
				url: "index.php?getpagesettings=line-height",
				context: document.body
			}).done(function(data) {
				jQuery ("#content").css ("line-height", parseInt(data) + "em");
			});

			jQuery (".page-settings").bind ("click", function (clickEvent) {
				clickEvent.preventDefault();

				if (jQuery (".page-settings-panel").length == 0) {
					jQuery ("#content").prepend("<div class='page-settings-panel'> \
						<h2>Font size</h2>\
						<input id='font-size' type='range' max='30' min='8' step='0.5' value='15' />\
						<br />\
						<h2>Line height</h2>\
						<input id='line-height' type='range' max='2.5' min='.5' step='0.25' value='.5' />\
					</div>");

					pageSettings ('font-size', 'px', 'saveFontSize');
					pageSettings ('line-height', 'em', 'saveLineHeight');
				} else {
					jQuery (".page-settings-panel").toggle();
				}
			});
		}
	});
});