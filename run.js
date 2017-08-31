var Andy = require('webpage').create();
var Bobby = require('webpage').create();
var Sam = require('webpage').create();

/*Andy.onLoadFinished = function (status) {
console.log('Running Andy');
Andy.release();
};

Bobby.onLoadFinished = function (status) {
console.log('Running Bobby');
Bobby.release();
};

Sam.onLoadFinished = function (status) {
console.log('Running Sam');
Sam.release();
};
*/
var A = setInterval(function(){
      Andy.open('http://localhost/btc/Andy.php', function(status){
		   console.log('Andy Open');
		   //console.log(Andy.content);
	  }) ;
    }, 3000);

var B = setInterval(function(){
	Bobby.open('http://localhost/btc/Bobby.php', function(status){
		console.log('Bobby Open');
		//console.log(Bobby.content);
	  }) ;
}, 100);

var S = setInterval(function(){
	Sam.open('http://localhost/btc/Sam.php', function(status){
		console.log('Sam Open');
		//console.log(Sam.content);
	  }) ;
}, 100);

