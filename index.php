<?php
/**
 * Created by PhpStorm.
 * User: ahmet
 * Date: 15.11.2017
 * Time: 23:24
 */
error_reporting(0);


require "classes/Karnaugh.php";

?>
<html>
    <head>
        <title>Karnaugh Map | Ahmet Çotur</title>
        <style type="text/css">
            body{
                margin: 0;
                padding: 0;
            }
            span.head{
                display: block;
                font-size: 25px;
                font-weight: bold;
                padding: 10px;
                text-align: center;
            }
            table.TruthTable{
                font-size:20px;
                font-weight:bold;
            }
            a{
                color:dimgrey;
                text-decoration: none;
            }
            a:hover{
                text-decoration: underline;
            }
        </style>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script type="text/javascript">
            $(function () {
                var data = { 'numbs[]' : []};
                $("input:checked").each(function() {
                    data['numbs[]'].push($(this).val());
                });
                $.post("post.php", data).done(function( ret ) {
                    $("#changeArea").html(ret);
                    $(".numbsCheck").attr("disabled",null);
                });
                $('.numbsCheck').change(function() {
                    $(".numbsCheck").attr("disabled","disabled");
                    if(this.checked) {
                        $(".fNumbs"+ this.value).html("1");
                    }else {
                        $(".fNumbs"+ this.value).html("0");
                    }


                    var data = { 'numbs[]' : []};
                    $("input:checked").each(function() {
                        data['numbs[]'].push($(this).val());
                    });
                    $.post("post.php", data).done(function( ret ) {
                        $("#changeArea").html(ret);
                        $(".numbsCheck").attr("disabled",null);
                    });


                });
            });
        </script>
    </head>
    <body>
        <center><h1>Karnaugh Map <h6 style="color: dimgrey"><a style="color: dimgrey" href="https://en.wikipedia.org/wiki/Karnaugh_map" target="_blank">What is it?</a></h6></h1></center>
    <hr>
    <?php
        $kTable = new Karnaugh(4,"min");


    ?>
    <center><div style="width: 500px;display: inline-block">
            <div style="float: left;">
                <form action="post.php" method="post" id="truthTable">
                    <?php
                    $kTable->printTruthTable();
                    ?>
                </form>

            </div>
            <div id="changeArea"  style="float: right;margin-right: 50px">



            </div>

        </div></center>
        <script src="js/malsup.js" type="text/javascript"></script>
    </body>
</html>