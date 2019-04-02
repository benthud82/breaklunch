<!DOCTYPE html>
<html>

    <head>
        <title>Break/Lunch Kiosk</title>
        <?php include_once '../printvis/headerincludes.php'; ?>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <script src="js/jquery.scannerdetection.js" type="text/javascript"></script>
        <script src="js/jquery.scannerdetection.compatibility.js" type="text/javascript"></script>
    </head>

    <body style="">

        <section id="content"> 
            <section class="main padder"> 

                <!--Input Container-->
                <div id="container_input">
                    <div class="row" style="padding-top: 75px;">
                        <div class="col-lg-2 col-md-4 col-sm-6 col-xs-6 col-lg-offset-5 col-md-offset-4 col-sm-offset-3 col-xs-offset-3 ">
                            <div class="form-signin" >
                                <h2 class="form-signin-heading text-center">Scan TSM#</h2>
                                <div style="margin: 10px; ">
                                    <label for="username" class="sr-only">Scan TSM#</label>
                                    <input type="text" id="tsmnum" name="tsmnum" class="form-control" placeholder="Scan TSM#" required="" autofocus="" autocomplete="off" >
                                </div>
                                <!--                       //         <button class="btn btn-lg btn-primary btn-block" type="submit" id="verifytsm">Sign in</button>-->
                            </div>
                        </div>
                    </div>
                </div
                <!--Error Modal-->

                <!--Break/Lunch Container-->
                <div id="container_breaklunch" class="hidden">
                    <div class="row" style="padding-top: 75px;  margin: 0px; width: 100%; text-align: center;">

                        <button style="display: inline-block; width: 150px; margin: 20px;"  class="btn btn-lg btn-primary btn-block click_breaklunch" type="submit" id="click_break" data-whse="<?php echo $whse ?>" data-type="BREAK" >Break - 15 Min</button>
                        <button style="display: inline-block; width: 150px; margin: 20px;" class="btn btn-lg btn-danger btn-block click_breaklunch" type="submit" id="click_lunch" data-whse="<?php echo $whse ?>" data-type="LUNCH"  >Lunch - 30 Min</button>


                    </div>
                </div

                <!--Error Modal-->



                <!-- Modal content-->
                <div id="modal_tsm_num_error" class="modal fade " role="dialog">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">TSM Not Found</h4>
                            </div>

                            <div class="modal-body" id="" style="margin: 50px;">
                                <div class="alert alert-danger " style="font-size: 100%;">  <i class="fa fa-info-circle fa-lg"></i><span> No TSM found.  Please try again.</span></div>
                            </div>

                        </div>
                    </div>
                </div>

            </section>
        </section>


        <script>
            $("body").tooltip({selector: '[data-toggle="tooltip"]'});

            function verifytsm(barcode) {
                debugger;
                var tsmnum = barcode;

                $.ajax({
                    data: {tsmnum: tsmnum},
                    url: 'post/verifytsm.php',
                    type: 'POST',
                    dataType: 'json',
                    success: function (ajaxresult) {
                        var tsmname = (ajaxresult[0]);
                        var test_error = (ajaxresult[1]);
                        //error control
                        if (test_error === 1) {
                            showerrormodal();
                            clear_tsm_num();
                        } else {
                            //TSM found
                            //Hide input text
                            hide_input_container();
                            //show break lunch insert
                            show_breaklunch_container();
                        }
                    }
                });
            }

            //break or lunch button clicked
            $(document).on("click touchstart", ".click_breaklunch", function (e) {
                e.preventDefault();
                var whse = $(this).attr('data-whse');
                var tsmnum = $('#tsmnum').val();
                var posttype = $(this).attr('data-type');
                $.ajax({
                    data: {tsmnum: tsmnum, whse: whse, posttype: posttype},
                    url: 'post/breaklunch.php',
                    type: 'POST',
                    dataType: 'html',
                    success: function () {
                        clear_tsm_num();
                        show_input_container();
                        hide_breaklunch_container();
                    }
                });

            });


            function showerrormodal() {
                $('#modal_tsm_num_error').modal('toggle');
            }
            function clear_tsm_num() {
                $('#tsmnum').val('');
            }
            function hide_input_container() {
                $('#container_input').addClass('hidden');
            }
            function show_breaklunch_container() {
                $('#container_breaklunch').removeClass('hidden');
            }
            function show_input_container() {
                $('#container_input').removeClass('hidden');
            }
            function hide_breaklunch_container() {
                $('#container_breaklunch').addClass('hidden');
            }




        </script>

        <script type="text/javascript">
            $(document).scannerDetection({
                //https://github.com/kabachello/jQuery-Scanner-Detection
                timeBeforeScanTest: 200, // wait for the next character for upto 200ms
                avgTimeByChar: 40, // it's not a barcode if a character takes longer than 100ms
                preventDefault: true,
                endChar: [13],
                onComplete: function (barcode, qty) {
                    debugger;
                    var barcode = barcode;
                    $('#tsmnum').val (barcode);
                    verifytsm(barcode);
                },
                onError: function (string, qty) {

                }
            });
        </script>

    </body>
</html>


