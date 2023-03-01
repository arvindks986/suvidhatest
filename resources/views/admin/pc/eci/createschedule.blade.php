@extends('admin.layouts.theme')
@section('title', 'Create Schedule')
@section('content') 
 <link href="{{ asset('css/daterangepicker.css')}}" rel="stylesheet"/>
<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
   <div class="nw-crte-usr">
         <div class="head-title">
          <h3><i><img src="{{ asset('admintheme/images/icons/tab-icon-010.png')}}" /></i>Create Election Schedule</h3>
         </div>
         @if(Session::has('success_admin'))
                  <script type="text/javascript" >
                    alert(' {{Session::get('success_admin')}}');
                  </script>
             <div class="alert alert-success">
                <strong> {{ nl2br(Session::get('success_admin')) }}</strong> 
              </div>
          @endif   
          @if(Session::has('unsuccess_insert'))
             <div class="alert alert-danger">
                <strong> {{ nl2br(Session::get('unsuccess_insert')) }}</strong> 
              </div>
          @endif 
         
            <div class="btns-actn"> <center>
                 <input type="submit" name="btnadd" value="Add Schedule" onClick="showadd();" id="btnadd" class="btns-actn">
                 <input type="submit" name="btncancel" value="Cancel Schedule" onClick="canceladd();" id="btncancel" style="display:none;" class="btns-actn"></center>
          </div> 
          
         <div id="add_menu" style="display:none;">   
            <form class="form-horizontal" id="election_form" method="POST"  action="{{url('eci/createschedule') }}" >
                {{ csrf_field() }}   
            <div class="col-sm-12" > 
              <div class="form-group col-sm-6 {{ $errors->has('totalschedule') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Schedule / Phase of Election :</label>
                    <div class="col-sm-6">
                           <!--{{ Form::select('size', array('L' => 'Large', 'S' => 'Small')) }} -->
                      <select name="totalschedule" id="totalschedule" >
                       <option value=""> Select Schedule</option><option value="1">1</option><option value="2">2</option>
                <option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option>
              <option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option>
                      </select> 
                           @if ($errors->has('totalschedule'))
                                <span class="help-block" <strong>{{ $errors->first('totalschedule') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
               
                 
                <div class="form-group col-sm-6 {{ $errors->has('pressdate') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Press Announcement Date:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{ old('pressdate') }}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div>
                <div class="col-sm-12" > 
               <div class="form-group col-sm-6 {{ $errors->has('st_date_nom') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Start Date of Nominations:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="st_date_nom" id="st_date_nom" value="{{ old('st_date_nom') }}">
                           @if ($errors->has('st_date_nom'))
                                <span class="help-block" <strong>{{ $errors->first('st_date_nom') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->


                  <div class="form-group col-sm-6 {{ $errors->has('lt_date_nom') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Last Date of Nominations:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="lt_date_nom" id="lt_date_nom" value="{{ old('lt_date_nom') }}">
                           @if ($errors->has('lt_date_nom'))
                                <span class="help-block" <strong>{{ $errors->first('lt_date_nom') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div><div class="col-sm-12" > 
               <div class="form-group col-sm-6 {{ $errors->has('scut_date_nom') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Scrutiny of Nomination Date:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="scut_date_nom" id="scut_date_nom" value="{{ old('scut_date_nom') }}">
                           @if ($errors->has('scut_date_nom'))
                                <span class="help-block" <strong>{{ $errors->first('scut_date_nom') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->


                  <div class="form-group col-sm-6 {{ $errors->has('with_date_nom') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Last Date of Withdrawal:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="with_date_nom" id="with_date_nom" value="{{ old('with_date_nom') }}">
                           @if ($errors->has('with_date_nom'))
                                <span class="help-block" <strong>{{ $errors->first('with_date_nom') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div><div class="col-sm-12" > 
               <div class="form-group col-sm-6 {{ $errors->has('date_ofpoll') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Date Of Poll:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="date_ofpoll" id="date_ofpoll" value="{{ old('date_ofpoll') }}">
                           @if ($errors->has('date_ofpoll'))
                                <span class="help-block" <strong>{{ $errors->first('date_ofpoll') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->


                  <div class="form-group col-sm-6 {{ $errors->has('date_of_counting') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Counting Of Votes Date:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="date_of_counting" id="date_of_counting" value="{{ old('date_of_counting') }}">
                           @if ($errors->has('date_of_counting'))
                                <span class="help-block" <strong>{{ $errors->first('date_of_counting') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div><div class="col-sm-12" > 
               <div class="form-group col-sm-6 {{ $errors->has('completion_date') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Election Completion Date:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control"  name="completion_date" id="completion_date"value="{{ old('completion_date') }}">
                           @if ($errors->has('completion_date'))
                                <span class="help-block" <strong>{{ $errors->first('completion_date') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                  <!--<div class="form-group col-sm-6 {{ $errors->has('election') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Select Election</label>
                    <div class="col-sm-6">
                     <select class="form-control" name="election">
                          <option value="">Select Election</option>
                    @foreach($list_election as $list)
                          <option value="{{ $list->election_id }}">{{ $list->election_sort_name."-".$list->election_type }}</option>
                    @endforeach
                    </select>
                      @if ($errors->has('election'))
                                <span class="help-block" <strong>{{ $errors->first('election') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
               </div>
              <div class="btns-actn">
                 <input type="submit" value="Save Schedule">
              </div>
            </form> 
            </div> <!-- End Of  add div Div -->
          </div><!-- End Of nw-crte-usr Div -->
   
       <!--    Listing -->

<div class="page-contant">
      <div class="head-title">
              <h3><i><img src="{{ asset('admintheme/images/icons/tab-icon-002.png')}}" /></i>Election Schedule </h3>
      </div>
            
      <!-- Start Of Page Sub Setion Div --> 
       <div class="page-sub-setion"> 
      <!-- Start Of Intra section Div -->
          <div class="intra-section">
          
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
           <!-- Start Table Here -->  
            <div class="table-responsive">
              <table class="table table-bordered">
               <thead>
                 <tr><th>Action</th><th>Phase No</th><th>Date of Press Announcement</th><th>St. Date of Nomination</th><th>Last Date of Nomination</th> 
                 <th>Date for Scrutiny</th><th>Last Date of Withdrawal </th><th>Date of Poll</th><th>Date of Counting</th>
                  <th>Election Completed Date</th> 
                 </tr>
               </thead>
               @foreach($list_schedule as $list)
                   
               <tr><td><a href="{{url('eci/updateschedule/'.$list->SCHEDULEID) }}">Edit</a></td>
                <td>{{ $list->SCHEDULENO }}</td><td>{{ date("d-m-Y",strtotime($list->DT_PRESS_ANNC)) }}</td> 
               <td>{{ date("d-m-Y",strtotime($list->DT_ISS_NOM)) }}</td><td>{{ date("d-m-Y",strtotime($list->LDT_IS_NOM)) }}</td><td>{{ date("d-m-Y",strtotime($list->DT_SCR_NOM)) }}</td><td>{{ date("d-m-Y",strtotime($list->LDT_WD_CAN)) }}</td>
                <td>{{ date("d-m-Y",strtotime($list->DATE_POLL)) }}</td><td>{{ date("d-m-Y",strtotime($list->DATE_COUNT)) }}</td><td>{{ date("d-m-Y",strtotime($list->DTB_EL_COM)) }}</td> 
                
              
               </tr>
                @endforeach
              </table>
            </div><!-- End Of table-responsive Div -->
          </div>
           
          </div>  
        </div><!-- End Of intra-section Div -->   
        </div><!-- End Of page-sub-setion Div -->
      
    </div><!-- End OF page-contant Div -->


       <!-- end list-->
    
    </div> <!-- End Of child-area Div -->     
  </div><!-- End Of parent-wrap Div -->
  </div>
   
<script language="javascript">
function showadd()
  {
  document.getElementById('add_menu').style.display="block";
  document.getElementById('btnadd').style.display="none";
  document.getElementById('btncancel').style.display="block";
  }
function canceladd()
  { 
  document.getElementById('add_menu').style.display="none";
  document.getElementById('btncancel').style.display="none";
  document.getElementById('btnadd').style.display="block";
  }
  
</script>
@endsection
<script src="{{ asset('js/jquery.js') }} "></script>
  
<script type="text/javascript" src="{{ asset('js/moment.min.js') }}"></script>
<script src="{{ asset('js/bootstrap-datetimepicker.js') }} "></script>
<script type="text/javascript">
  
jQuery(document).ready(function() { 
        jQuery.noConflict();
    jQuery('#pressdate').datetimepicker({
          weekStart:0,
          todayBtn:  1,
          autoclose: 1,
          todayHighlight: 1,
          startView: 2,
          minView: 2,
          forceParse: 0
      });
});

</script>