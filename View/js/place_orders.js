$(document).ready(function(){
    var checkStillHaveOrders = setInterval(function(){
       if ( $("#checkStart").val() == 1) {
           $("#checkStart").val(0);
           $.ajax({
               url: "placeBuyOrders.php",
               success: function (data) {
                   $("#div1").append(data);

                   var checkOrdersExist = setInterval(function () {

                       $.ajax({
                           url: "getNonCompleteOrders.php",
                           dataType: "json",
                           success: function (data) {
                               if (data == '') {
                                   $("#checkStart").val(1);
                                   clearInterval(checkOrdersExist);
                               }
                               $.each(data, function (key, val) {

                                   $.ajax({
                                       url: "checkOrderInfo.php",
                                       data: {oid: val.$oid},
                                       type: 'POST',
                                       success: function (data) {

                                           $("#div2").prepend(data);
                                       }
                                   });

                               });
                           }
                       });


                   }, 5000);
               }
           });
       }
    },100);
});