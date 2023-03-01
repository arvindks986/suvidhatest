@extends('admin.layouts.pc.report-theme')
@section('content')
<?php
$st = getstatebystatecode($user_data->st_code);
$distname = getdistrictbydistrictno($user_data->st_code, $user_data->dist_no);
$pcdetails = getpcbypcno($user_data->st_code, $user_data->pc_no);
$namePrefix = \Route::current()->action['prefix'];
//dd($filedData);
?>
<style>
    span.help-block strong.user {
        color: red;
    }
    .mis_gap {
        margin: 18px;
        background: #b1287a;
        color: #fff;
        padding: 10px;
    }
    .final_action_btn{
        background-color: #bb4292;
        color: #fff;
        margin-left: 29px;
        border-radius: 2px;
        padding: 5px 10px 8px 10px;
    }

    a.final_action_btn:hover{
        color:#fff !important;
        text-decoration: none;
    }
    #form_new_mis{
        padding-bottom: 25px;
    }
    input[type="date"], input[type="time"], input[type="datetime-local"], input[type="month"] {
        -webkit-appearance: listbox;
    }
    .ecrpregistraion{
        color:green;
        font-weight:600;
        font-size:16px;

    }
    .ecrpassign{
        color:green;
        font-weight:600;
        font-size:16px;            
    }

</style>

<button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();" style="float: right;margin-right: 91px; margin-top: 7px;">Back</button>

<main role="main" class="inner cover mb-3">
    <section class="mt-5">
        <div class="container-fluid">             
            <div class="row">
                <div class="card text-left" style="width:100%; margin:0 auto;"> 
                    <div class="card-body">
                        <div class="clearfix"></div> 
                        <section class="tab_order">             
                            <ul class="tabs">
                                <li>
                                    <a href="#tab1" id="ActiveTab1">Register ECRP</a>
                                </li>
                                <li>
                                    <a href="#tab2" id="ActiveTab2">Assign ECRP</a>
                                </li>
                                <li>
                                    <a href="#tab3" id="ActiveTab3">Filed Statement</a>
                                </li>   
                            </ul>

                            <div id="tab1" class="tabContainer">
                                <form method="post"   id="registrationData">
                                    {{ csrf_field() }} 
                                    <div class=" row">
                                        <div style="" class="col-sm-12 mt-2 mb-4 text-center">
                                            <p class="h6 text-center">Registration Form for ECRP</p>
                                        </div>
                                        <div  class="col-sm-12 mt-2 mb-4 text-center">
                                            <p class="h6 text-center ecrpregistraion"></p>
                                        </div>

                                        <div class="col-sm-6" id="">
                                            <label for="">Name<sup>*</sup></label>
                                            <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name">
                                        </div>
                                        <div class="col-sm-6" id="">
                                            <label for="">Mobile No.</label>
                                            <input type="text" name="mobile" maxlength="10" minlegth="10" id="mobile" class="form-control" placeholder="Enter Mobile Number">
                                        </div>
                                        <div class="col-sm-6 mt-3" id="">
                                            <label for="">Email id<sup>*</sup></label>
                                            <input type="text" class="form-control" name="email" id="email" placeholder="Enter Email ID">
                                        </div>
                                        <div class="col-sm-6 mt-3" id="">
                                            <label for="">State</label>
                                            <select name="ST_CODE" id="ST_CODE" class="form-control">
                                                <option value="" selected="selected">All States</option>
                                                @php $statelist = getallstate(); @endphp
                                                @foreach ($statelist as $state_List ))                                                
                                                <option value="{{ $state_List->ST_CODE }}">{{$state_List->ST_NAME}}</option>                                                
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-6 mt-3" id="">
                                            <label for="">Select District</label>
                                            <select name="district_no" id="district_no" class="form-control" >
                                                <option value="">Select District</option>                                                
                                            </select>
                                        </div>
                                        <div class="col-sm-6 mt-3" id="">
                                            <label for="">Election Type</label>
                                            <select name="election_type" id="election_type" class="form-control" >
                                                <option value="">Select Election Type</option>
<!--                                                <option value="AC">AC</option>-->
                                                <option value="PC">PC</option>
                                                <option value="Bye-Election">Bye-Election</option>
                                            </select>
                                        </div>
                                        <div style="" class="col-sm-12 mt-5 text-center">
                                            <button type="button" class="btn btn-primary" id="saveregistraion">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div id="tab2" class="tabContainer">
                                <form method="post"   id="assignData">
                                    {{ csrf_field() }} 

                                    <div class=" row">
                                        <div style="" class="col-sm-12 mt-2 mb-4 text-center">
                                            <p class="h6 text-center">Assign Form for ECRP</p>
                                        </div>
                                        <div  class="col-sm-12 mt-2 mb-4 text-center">
                                            <p class="h6 text-center ecrpassign"></p>
                                        </div>
                                        <div class="col-sm-6 mt-3" id="">
                                            <label for="">State</label>
                                            <select name="stateAssign" id="stateAssign" class="form-control">
                                                <option value="" selected="selected">Select State</option>
                                                @php $statelist = getallstate(); @endphp
                                                @foreach ($statelist as $state_List ))                                                
                                                <option value="{{ $state_List->ST_CODE }}" >{{$state_List->ST_NAME}}</option>                                                
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-6 mt-3" id="">
                                            <label for="">Select District</label>
                                            <select name="districtassign" id="districtassign" class="form-control" >
                                                
                                            </select>
                                        </div>
                                        <div class="col-sm-6 mt-3" id="">
                                            <label for="">Election Type</label>
                                            <select name="election_typeassign" id="election_typeassign" class="form-control" >
                                                <option value="">Select Election Type</option>
                                                <!--<option value="AC">AC</option>-->
                                                <option value="PC">PC</option>
                                                <option value="Bye-Election">Bye-Election</option>
                                            </select>
                                        </div>
                                         <div class="col-sm-6 mt-3" id="">
                                            <label for="">ECRP</label>                                            
                                             <div id="ecrp_list"></div>
                                        </div>
                                         <div class="col-sm-6 mt-3" id="">  
                                            <label for="">Political Party</label>
                                            <div id="party_list"></div>
                                            
                                        </div>
                                        <div class="col-sm-6 mt-3" id="">
                                            <label for="">Candidate Name</label>
                                             <div id="candidate_list"></div>
                                        </div>
<!--                                         <div class="col-sm-6 mt-3" id="">
                                            <label for="">Candidate Name</label>
                                            <select name="candidate_id" id="candidate_id" class="form-control" >
                                                
                                            </select>
                                        </div>-->
                                       
                                        <div style="" class="col-sm-12 mt-5 text-center">
                                            <button type="button" class="btn btn-primary" id="assignbutton">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div id="tab3" class="tabContainer">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>                                                
                                            <tr>
                                                <th>Candidate Name</th>
                                                <th>Party Name</th>
                                                <th>State</th>
                                                <th>District</th>
                                                <th>Election Type</th>
                                                <th>ECRP Name</th>
                                                <th>ECRP ID</th>
                                                <th class="width-120">Action</th>
                                            </tr>    
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Suman Yadav</td>
                                                <td>BJP</td>
                                                <td>UP</td>
                                                <td>Lucknow</td>
                                                <td>AC</td>
                                                <td>ECRP Name</td>
                                                <td>ECRP ID</td>
                                                <td><a href="" class="btn btn-info btn-sm width-100" target="_blank">Report Verify</a></td>
                                            </tr>
                                            <tr>
                                                <td>Suman Yadav</td>
                                                <td>BJP</td>
                                                <td>UP</td>
                                                <td>Lucknow</td>
                                                <td>AC</td>
                                                <td>ECRP Name</td>
                                                <td>ECRP ID</td>
                                                <td><a href="" class="btn btn-info btn-sm width-100" target="_blank">Report Verify</a></td>
                                            </tr>
                                            <tr>
                                                <td>Suman Yadav</td>
                                                <td>BJP</td>
                                                <td>UP</td>
                                                <td>Lucknow</td>
                                                <td>AC</td>
                                                <td>ECRP Name</td>
                                                <td>ECRP ID</td>
                                                <td><a href="" class="btn btn-info btn-sm width-100" target="_blank">Report Verify</a></td>
                                            </tr>
                                            <tr>
                                                <td>Suman Yadav</td>
                                                <td>BJP</td>
                                                <td>UP</td>
                                                <td>Lucknow</td>
                                                <td>AC</td>
                                                <td>ECRP Name</td>
                                                <td>ECRP ID</td>
                                                <td><a href="" class="btn btn-info btn-sm width-100" target="_blank">Report Verify</a></td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="8" class="text-center" align="center">  <button type="button" class="btn btn-primary mt-3 mb-3" id="">Submit</button>                                    
                                                </td>                                       
                                            </tr>
                                        </tfoot>         
                                    </table>

                                </div>
                            </div>

                        </section>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>    


<!--**********FORM VALIDATION STARTS**********-->
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>

<!--**********FORM VALIDATIONS SCRIPT**********-->
<script type="text/javascript">
//*******************EXTRA VALIDATION METHODS STARTS********************//
//maxsize
    $.validator.addMethod('maxSize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param)
    });
//minsize
    $.validator.addMethod('minSize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size >= param)
    });
//alphanumeric
    $.validator.addMethod("alphnumericregex", function (value, element) {
        return this.optional(element) || /^[a-z0-9\._\s]+$/i.test(value);
    });
//alphaonly
    $.validator.addMethod("onlyalphregex", function (value, element) {
        return this.optional(element) || /^[a-z\.\s]+$/i.test(value);
    });
//without space
    $.validator.addMethod("noSpace", function (value, element) {
        return value.indexOf(" ") < 0 && value != "";
    }, "No space please and don't leave it empty");
//*******************EXTRA VALIDATION METHODS ENDS********************//
//*******************ECI FILTER FORM VALIDATION STARTS********************//
    $("#EciCustomReportFilter").validate({
        rules: {
            state: {required: true, noSpace: true},
            ScheduleList: {number: true},
        },
        messages: {
            state: {
                required: "Select state name.",
                noSpace: "State name must be without space.",
            },
            ScheduleList: {
                number: "Scedule ID should be numbers only.",
            },
        },
        errorElement: 'div',
        errorPlacement: function (error, element) {
            var placement = $(element).data('error');
            if (placement) {
                $(placement).append(error)
            } else {
                error.insertAfter(element);
            }
        }
    });
//********************ECI FILTER FORM VALIDATION ENDS********************//
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });


    
// end state here
</script>
<script type="text/javascript">
 


    $(document).ready(function () {
        $('.dropdown-menu a.dropdown-toggle').on('click', function (e) {
            var $el = $(this);
            $el.toggleClass('active-dropdown');
            var $parent = $(this).offsetParent(".dropdown-menu");
            if (!$(this).next().hasClass('show')) {
                $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
            }
            var $subMenu = $(this).next(".dropdown-menu");
            $subMenu.toggleClass('show');

            $(this).parent("li").toggleClass('show');

            $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function (e) {
                $('.dropdown-menu .show').removeClass("show");
                $el.removeClass('active-dropdown');
            });

            if (!$parent.parent().hasClass('navbar-nav')) {
                $el.next().css({"top": $el[0].offsetTop, "left": $parent.outerWidth() - 4});
            }

            return false;
        });
    });
    jQuery('ul.tabs').each(function () {
        var $active, $content, $links = jQuery(this).find('a');
        $active = jQuery($links.filter('[href="' + location.hash + '"]')[0] || $links[0]);
        $active.addClass('active');
        $content = jQuery($active[0].hash);
        $links.not($active).each(function () {
            jQuery(this.hash).hide();
        });
        jQuery(this).on('click', 'a', function (e) {
            $active.removeClass('active');
            $content.hide();
            $active = jQuery(this);
            $content = jQuery(this.hash);
            $active.addClass('active');
            $content.show();
            e.preventDefault();
        });
    });
    jQuery(document).ready(function () {
        jQuery("select[name='ST_CODE']").change(function () {
            var state = jQuery(this).val();
            jQuery.ajax({
                url: '<?php echo url('/') ?>/ropc/getdistrictsbystate',
                type: 'GET',
                data: {"state": state, "_token": "{{ csrf_token() }}"},
                success: function (response) {
                    var stateselect = jQuery('form select[name=district_no]');
                    stateselect.empty();
                    var pchtml = '';
                    pchtml = pchtml + '<option value="">-- All district --</option> ';
                    jQuery.each(response, function (key, value) {
                        pchtml = pchtml + '<option value="' + value.DIST_NO + '">' + value.DIST_NO + ' - ' + value.DIST_NAME + '</option>';
                        jQuery("select[name='district_no']").html(pchtml);
                    });
                    var pchtml_end = '';
                    jQuery("select[name='district_no']").append(pchtml_end)
                }
            });
        });


    });
    jQuery(document).ready(function () {
        jQuery("select[name='stateAssign']").change(function () {
            var state = jQuery(this).val();
            getCandidateList(state);
            jQuery.ajax({
                url: '<?php echo url('/') ?>/ropc/getdistrictsbystate',
                type: 'GET',
                data: {"state": state, "_token": "{{ csrf_token() }}"},
                success: function (response) {
                    var stateselect = jQuery('form select[name=districtassign]');
                    stateselect.empty();
                    var pchtml = '';
                    pchtml = pchtml + '<option value="">-- All district --</option> ';
                    jQuery.each(response, function (key, value) {
                        pchtml = pchtml + '<option value="' + value.DIST_NO + '">' + value.DIST_NO + ' - ' + value.DIST_NAME + '</option>';
                        jQuery("select[name='districtassign']").html(pchtml);
                    });
                    var pchtml_end = '';
                    jQuery("select[name='districtassign']").append(pchtml_end)
                    
                }
            });
            
        });


    });
    function getCandidateList(stcode){
    console.log("stcode"+stcode);
     jQuery.ajax({
                url: '<?php echo url('/') ?>/ropc/getECRPCandidateList/'+stcode,
                type: 'GET',
                data: {"stcode": stcode},
                success: function (response) {
                    $('#candidate_list').html(response);
     
                    
                }
            });
    }
 
    
// End of Tab Javascript
    jQuery(document).on('click', '#saveregistraion', function (e) {
        var data = jQuery("#registrationData").serialize();
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        var mobilefilter = /[1-9]{1}[0-9]{9}/;
        var name = $('#name').val();
        var mobile = $('#mobile').val();
        var email = $('#email').val();
        var ST_CODE = $('#ST_CODE').val();
        var district_no = $('#district_no').val();
        var election_type = $('#election_type').val();

        if (name == "") {
            $('#name').css('border', '2px solid red');
            $('#name').focus();
        } else if (mobile == "") {
            $('#name').css('border', '');
            $('#mobile').css('border', '2px solid red');
            $('#mobile').focus();
        } else if (!mobilefilter.test(mobile) && !mobile.length == 10) {
            $('#name').css('border', '');
            $('#mobile').css('border', '2px solid red');
            $('#mobile').focus();
        } else if (email == "") {
            $('#name,#mobile').css('border', '');
            $('#email').css('border', '2px solid red');
            $('#email').focus();
        } else if (!emailReg.test(email)) {
            $('#name,#mobile').css('border', '');
            $('#email').css('border', '2px solid red');
            $('#email').focus();
        } else if (ST_CODE == "") {
            $('#name,#mobile,#email').css('border', '');
            $('#ST_CODE').css('border', '2px solid red');
            $('#ST_CODE').focus();
        } else if (district_no == "") {
            $('#name,#mobile,#email,#ST_CODE').css('border', '');
            $('#district_no').css('border', '2px solid red');
            $('#district_no').focus();
        } else if (election_type == "") {
            $('#name,#mobile,#email,#ST_CODE,#district_no').css('border', '');
            $('#election_type').css('border', '2px solid red');
            $('#election_type').focus();
        } else {
            $('#name,#mobile,#email,#ST_CODE,#district_no,#election_type').css('border', '');
            $.ajax({
                data: data,
                type: "POST",
                dataType: "json",
                url: "{{url('/ropc/saveEcrpRegistration')}}",
                beforeSend: function (data) {
                    console.log("before send");
                },
                success: function (response) {
                    $('.ecrpregistraion').text('ECRP Registration Successfully.');
                    $('#name,#mobile,#email,#ST_CODE,#district_no,#election_type').val();
                    $("#registrationData")[0].reset();
                },
                error: function (error) {
                    console.log(error);

                }
            });
        }
    });
// assign ecrp
    jQuery(document).on('click', '#assignbutton', function (e) {
        var data = jQuery("#assignData").serialize();
        //stateAssign districtassign election_typeassign party_id candidate_id ecrp_id

        var stateAssign = $('#stateAssign').val();
        var districtassign = $('#districtassign').val();
        var election_typeassign = $('#election_typeassign').val();
        var party_id = $('#party_id').val();
        var candidate_id = $('#candidate_id').val();
        var ecrp_id = $('#ecrp_id').val();
        if (stateAssign == "") {
            $('#stateAssign').css('border', '2px solid red');
            $('#stateAssign').focus();
        } else if (districtassign == "") {
            $('#stateAssign').css('border', '');
            $('#districtassign').css('border', '2px solid red');
            $('#districtassign').focus();
        } else if (election_typeassign == "") {
            $('#stateAssign,#districtassign').css('border', '');
            $('#election_type').css('border', '2px solid red');
            $('#election_type').focus();
        } else {
            $('#stateAssign,#districtassign,#email,#ST_CODE,#district_no,#election_type').css('border', '');
            $.ajax({
                data: data,
                type: "POST",
                dataType: "json",
                url: "{{url('/ropc/assignEcrpRegistration')}}",
                beforeSend: function (data) {
                    console.log("before send");
                },
                success: function (response) {
                   //alert(response);
                    $('.ecrpassign').text('ECRP Assign Successfully.');
                  //  $('#ST_CODE,#district_no,#election_type').val();
                    $("#assignData")[0].reset();
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }
    });
</script>
 <script type="text/javascript">
            // jQuery wait till the page is fullt loaded
            $(document).ready(function () {
                // keyup function looks at the keys typed on the search box
              //  $('#party_id').on('keyup',function() {
                    // the text typed in the input field is assigned to a variable 
                   // var query = $(this).val();
                   var query='';
                    // call to an ajax function
                    $.ajax({
                        // assign a controller function to perform search action - route name is search
                       url: '<?php echo url('/') ?>/ropc/getParty',
                        // since we are getting data methos is assigned as GET
                        type:"GET",
                        // data are sent the server
                        data:{'partyName':query},
                        // if search is succcessfully done, this callback function is called
                        success:function (data) {
                            // print the search results in the div called country_list(id)
                            $('#party_list').html(data);
                        }
                    })
                    // end of ajax call
               // });

                // initiate a click function on each search result
//                $(document).on('click', '.party', function(){
//                    // declare the value in the input field to a variable
//                    var value = $(this).text();
//                    // assign the value to the search box
//                    $('#party_id').val(value);
//                    // after click is done, search results segment is made empty
//                    $('#party_list').html("");
//                });
            });
            // jQuery wait till the page is fullt loaded
            $(document).ready(function () {
                // keyup function looks at the keys typed on the search box
              //  $('#party_id').on('keyup',function() {
                    // the text typed in the input field is assigned to a variable 
                   // var query = $(this).val();
                   var query='test';
                    // call to an ajax function
                    $.ajax({
                        // assign a controller function to perform search action - route name is search
                       url: '<?php echo url('/') ?>/ropc/getEcrpList',
                        // since we are getting data methos is assigned as GET
                        type:"GET",
                        // data are sent the server
                        data:{'stcode':query},
                        // if search is succcessfully done, this callback function is called
                        success:function (data) {
                             
                            $('#ecrp_list').html(data);
                        }
                    })
                  
            });
        </script>

@endsection
