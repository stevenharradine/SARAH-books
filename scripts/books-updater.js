console.log ("Books updater");
console.log ("*************");
console.log ();

String.prototype.endsWith = function (suffix) {
	return this.indexOf(suffix, this.length - suffix.length) !== -1;
};

var request = require('request');

var url = "http://localhost/media/Music/Library/Books/";
var url = "http://localhost/media/Music/Library/Books/william%20shakespeare/William%20Shakespeare/Hamlet%20(1891)/";
var books = new Array ();
var books_index = 0;

function getTitle (url) {
	url = "http://localhost/SARAH/apps/books/api/getTitle/?myusername=books_user&mypassword=books_password&book_url=" + url;
	request(url, function (error, response, body) {
		console.log (body);
	});
}

function getBooks (url) {
	request(url, function (error, response, body) {
		//if (!error && response.statusCode == 200) {
		body_peices = body.split ('<a href="');

		for (
			i = 3;							// skip the headers
			i < body_peices.length - 1;		// loop through the rest of the elements
			i += 2							// because we pivit on eachside of
											// the anchor tag we need to skip
		) {									// every other element
			secondary_fragments = body_peices[i + 1].split ('">');
			link = secondary_fragments[0];

			if (link.endsWith(".epub")) {							// if supported book format
				book_url = url + link;
				console.log (books[books_index++] = book_url);

				getTitle (book_url);
			} else if (link.endsWith ("/") && link.length >= 2) {	// if folder
				getBooks (url + link);
			}
		}
	});
}

getBooks (url);