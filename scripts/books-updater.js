var request      = require('request'),
    Cheerio      = require('cheerio'),
    url          = "http://192.168.1.2/media/Music/Library/Books/",
    books        = new Array (),
    books_index  = 0,
    agentOptions = {
    	  url: url,
    	  strictSSL: false	// allow self-signed
    }

console.log ("Books updater");
console.log ("*************");
console.log ();

String.prototype.endsWith = function (suffix) {
	return this.indexOf(suffix, this.length - suffix.length) !== -1
}

var i = 0;

request(agentOptions, function (error, response, body) {
	if (error) {
		return console.log (error)
	}
  if (!error && response.statusCode == 200) {
    dom = Cheerio.load(body);

    
		dom("table").find("tr").each(function(i, elem) {
			dom(this).find("td").each (function(j, elem) {
				if (j == 1) {
					console.log (i++ + "|" + dom(this).find("a").attr("href"))
				}
			})
		})
  }
})