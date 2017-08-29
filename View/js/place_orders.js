$(document).ready(function(){

    $("#start").click(function(){
        $.ajax({
            url: "placeBuyOrders.php",
            success: function(data) {

                $("#div1").append(data);
            }
        });
    });

});