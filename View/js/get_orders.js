$(document).ready(function(){

    $("#start").click(function(){
        $.ajax({
            url: "getNonCOmpleteOrders.php",
            dataType: "json",
            success: function(data){
                $.each( data, function( key, val ) {
                    $.ajax({
                        url: "checkOrderInfo.php",
                        data: {oid:val.$oid},
                        type: 'POST',
                        success: function(data) {

                            $("#div1").append(data);
                        }
                    });

                });
        }});
    });

});