@extends('admin.layouts.pc.theme')
@section('title', 'Suvidha')
@section('bradcome', 'Estimate Turnout Entry')
@section('content')
 <?php    $st=getstatebystatecode($ele_details->ST_CODE);  
          $ac=getacbyacno($ele_details->ST_CODE,$user_data->ac_no); 
          $pc=getpcbypcno($ele_details->ST_CODE,$user_data->pc_no);
          $url = URL::to("/"); $j=0; 
          $current_date=date("Y-m-d H:i:s");   
          $poll_date="2019-04-16"; // $seched->DATE_POLL;    
          $p1=$poll_date." 00:10:00"; $p2=$poll_date." 00:30:00"; $p3=$poll_date." 23:30:00"; $p4=$poll_date." 23:30:00";  $p5=$poll_date." 24:00:00";
          $pt1=$poll_date." 00:00:00"; $pt2=$poll_date." 00:00:00"; $pt3=$poll_date." 23:00:00"; $pt4=$poll_date." 23:00:00";  $pt5=$poll_date." 23:00:00";
          echo $current_date; echo $p1;
         ?>
 
   <main role="main" class="inner cover mb-3">
<section class="mt-3">
  <div class="container jumborton card">
  <div class=" row ">

            <div class="col-md-6">
              <table>
              <tr>
                <th colspan="2"><h3>Details</h3></th>
              </tr>
               <tr>
                  <td><b>State:</b></td>
                  <td>{{$st->ST_NAME}}</td>
                </tr>
                <tr>
                  <td><b>PC Name:</b></td>
                  <td>{{$pc->PC_NAME}}</td>
                </tr>
                <tr>
                  <td><b>AC Name:</b></td>
                  <td>{{$ac->AC_NAME}}</td>
                </tr>
                
               
                
              </table>
              
            </div>
            <div class="col-md-6  text-right totalPercentage p-4">
            
            <p>Estimated Turnout</p>
            <h1 class="display-1 m-0 p-0" style="line-height: 73px;">
                 {{$totalturnout_per}}<small style="font-size: 67%;">%</small>
            </h1>
            <small>Last Updated <span class="badge badge-success">{{date("d-m-Y H:i:S",strtotime($lists->updated_at))}}</span></small>
            </div>
          
          </div>
          
          
    <div class="row">
    <div class="card col p-0 m-0">
       
              <table class="table-bordered card-body" cellpadding="0" cellspacing="0" style="width:100%;">
              <thead>
                <tr>
                  <th>Time</th>
                  <th>Estimated Poll Turnout %</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>09:00 AM  @if( $current_date>=$pt1 and $current_date<=$p1) @if($lists->est_turnout_round1==0) <p id="demo"> </p> @endif @endif </td>
                  <td> 
                     @if( $current_date>=$pt1 and $current_date<=$p1)  
                    <form class="form-horizontal" id="election_form" method="post" action="{{url('aro/voting/estimated-entry')}}"   autocomplete='off'>  {{csrf_field()}}   <input type="hidden" name="id" value="{{$lists->id}}"> 
                       <div class="PollEdit">
                          <label for="PercenTage" class="mt-2">Enter Total Percentage here</label>
                          <input type="text" name="est_turnout_round1" id="est_turnout_round1" class="PoLLInput" placeholder="Estimated Poll Turnout%"  value="@if($lists->est_turnout_round1>0) {{$lists->est_turnout_round1}} @endif" maxlength="5"  />
                           <span id="errmsg" class="text-danger"></span> 
                            @if ($errors->has('est_turnout_round1'))
                                <span style="color:red;">{{ $errors->first('est_turnout_round1') }}</span>
                           @endif  
                            <button type="submit" id="saverec" name="saverec" value="1" class="btn buttonActive">Update</button>                   
                          
                        </div>   
                            <input type="hidden" name="field_name" value="est_turnout_round1">  
                    </form>
                  @endif
                
                 @if($lists->est_turnout_round1>0)
                 @if($current_date>$p1)
                        <div class="Pollcompleted">
                        <p class="PollText display-2">{{$lists->est_turnout_round1}} %</p>
                        <small class="text-white text-center">Last Updated on  {{date("M d, Y H:i:s",strtotime($lists->update_at_round1))}}   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Updated Device:- {{$lists->update_device_round1}}</small>
                        </div>
                  @endif
                      @elseif($current_date>=$p1) 
                        <div class="PollMissed">
                          <p class="PollText display-2">Missed</p>
                        </div>
                     @else
                        <div class="PollDeactive">  <p class="PollText display-2">Not Open</p>   </div>
                    @endif

                  </td>
                </tr>
                
                <tr>
                  <td>11:00 AM @if( $current_date>=$pt2 and $current_date<=$p2) @if($lists->est_turnout_round2==0) <p id="demo1"> </p> @endif @endif</td>
                  <td> @if( $current_date>=$pt2 and $current_date<=$p2)  
                   
                       <div class="PollEdit">
                         <form class="form-horizontal" id="election_form" method="post" action="{{url('aro/voting/estimated-entry')}}"  autocomplete='off'>  {{csrf_field()}}   <input type="hidden" name="id" value="{{$lists->id}}"> 
                          <label for="PercenTage" class="mt-2">Enter Total Percentage here</label>
                          <input type="text" name="est_turnout_round2" id="est_turnout_round2" class="PoLLInput" placeholder="Estimated Poll Turnout%"  value="" maxlength="5"  />
                           <span id="errmsg" class="text-danger"></span> 
                            @if ($errors->has('est_turnout_round2'))
                                <span style="color:red;">{{ $errors->first('est_turnout_round2') }}</span>
                           @endif  
                            <button type="submit" id="saverec2" name="saverec" value="2" class="btn buttonActive">Update</button>                   
                           <input type="hidden" name="field_name" value="est_turnout_round2">  
                          </form>
                        </div>   
                           
                  @endif
                    @if($lists->est_turnout_round2>0)
                        <div class="Pollcompleted">
                        <p class="PollText display-2">{{$lists->est_turnout_round2}} %</p>
                        <small class="text-white text-center">Last Updated on  {{date("M d, Y H:i:s",strtotime($lists->update_at_round2))}}   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Updated Device:- {{$lists->update_device_round2}}</small>
                        </div>
                      @elseif($current_date>=$p2) 
                        <div class="PollMissed">
                          <p class="PollText display-2">Missed</p>
                        </div>
                     @else
                        <div class="PollDeactive">  <p class="PollText display-2">Not Open</p>   </div>
                    @endif
                  </td>
                </tr>
                
                <tr>
                  <td>01:00 PM  @if( $current_date>=$pt3 and $current_date<=$p3) @if($lists->est_turnout_round3==0) <p id="demo2"> </p> @endif @endif </td>
                   <td> @if( $current_date>=$pt3 and $current_date<=$p3)  
                    <form class="form-horizontal" id="election_form" method="post" action="{{url('aro/voting/estimated-entry')}}" enctype="multipart/form-data" autocomplete='off'>  {{csrf_field()}}   <input type="hidden" name="id" value="{{$lists->id}}"> 
                       <div class="PollEdit">
                          <label for="PercenTage" class="mt-2">Enter Total Percentage here</label>
                          <input type="text" name="est_turnout_round3" id="est_turnout_round3" class="PoLLInput" placeholder="Estimated Poll Turnout%"  value="" maxlength="5"  />
                           <span id="errmsg" class="text-danger"></span> 
                            @if ($errors->has('est_turnout_round3'))
                                <span style="color:red;">{{ $errors->first('est_turnout_round3') }}</span>
                           @endif  
                            <button type="submit" id="saverec3" name="saverec" value="3" class="btn buttonActive">Update</button>                   
                          
                        </div>   
                            <input type="hidden" name="field_name" value="est_turnout_round3">  
                    </form>
                  @endif
                    @if($lists->est_turnout_round3>0)
                        <div class="Pollcompleted">
                        <p class="PollText display-2">{{$lists->est_turnout_round3}} %</p>
                        <small class="text-white text-center">Last Updated on  {{date("M d, Y H:i:s",strtotime($lists->update_at_round3))}}   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Updated Device:- {{$lists->update_device_round3}}</small>
                        </div>
                      @elseif($current_date>=$p3) 
                        <div class="PollMissed">
                          <p class="PollText display-2">Missed</p>
                        </div>
                     @else
                        <div class="PollDeactive">  <p class="PollText display-2">Not Open</p>   </div>
                    @endif
                  </td>
                </tr>
                
                <tr>
                  <td>03:00 PM  @if( $current_date>=$pt4 and $current_date<=$p4) @if($lists->est_turnout_round4==0) <p id="demo3"> </p> @endif @endif</td>
                  <td> @if( $current_date>=$pt4 and $current_date<=$p4)  
                    <form class="form-horizontal" id="election_form" method="post" action="{{url('aro/voting/estimated-entry')}}" enctype="multipart/form-data" autocomplete='off'>  {{csrf_field()}}   <input type="hidden" name="id" value="{{$lists->id}}"> 
                       <div class="PollEdit">
                          <label for="PercenTage" class="mt-2">Enter Total Percentage here</label>
                          <input type="text" name="est_turnout_round4" id="est_turnout_round4" class="PoLLInput" placeholder="Estimated Poll Turnout%"  value="" maxlength="5"  />
                           <span id="errmsg" class="text-danger"></span> 
                            @if ($errors->has('est_turnout_round4'))
                                <span style="color:red;">{{ $errors->first('est_turnout_round4') }}</span>
                           @endif  
                            <button type="submit" id="saverec4" name="saverec" value="4" class="btn buttonActive">Update</button>                   
                          
                        </div>   
                            <input type="hidden" name="field_name" value="est_turnout_round4">  
                    </form>
                  @endif
                    @if($lists->est_turnout_round4>0)
                        <div class="Pollcompleted">
                        <p class="PollText display-2">{{$lists->est_turnout_round4}} %</p>
                        <small class="text-white text-center">Last Updated on  {{date("M d, Y H:i:s",strtotime($lists->update_at_round4))}}   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Updated Device:- {{$lists->update_device_round4}}</small>
                        </div>
                      @elseif($current_date>=$p4) 
                        <div class="PollMissed">
                          <p class="PollText display-2">Missed</p>
                        </div>
                     @else
                        <div class="PollDeactive">  <p class="PollText display-2">Not Open</p>   </div>
                    @endif
                  </td>
                </tr>
                 <tr>
                  <td>05:00 PM @if( $current_date>=$pt5 and $current_date<=$p5) @if($lists->est_turnout_round1==0) <p id="demo5"> </p> @endif @endif</td>
                  <td> @if( $current_date>=$pt5 and $current_date<=$p5)  
                    <form class="form-horizontal" id="election_form" method="post" action="{{url('aro/voting/estimated-entry')}}" enctype="multipart/form-data" autocomplete='off'>  {{csrf_field()}}   <input type="hidden" name="id" value="{{$lists->id}}"> 
                       <div class="PollEdit">
                          <label for="PercenTage" class="mt-2">Enter Total Percentage here</label>
                          <input type="text" name="est_turnout_round5" id="est_turnout_round5" class="PoLLInput" placeholder="Estimated Poll Turnout%"  value="" maxlength="5"  />
                           <span id="errmsg" class="text-danger"></span> 
                            @if ($errors->has('est_turnout_round5'))
                                <span style="color:red;">{{ $errors->first('est_turnout_round5') }}</span>
                           @endif  
                            <button type="submit" id="saverec5" name="saverec" value="5" class="btn buttonActive">Update</button>                   
                          
                        </div>   
                            <input type="hidden" name="field_name" value="est_turnout_round3">  
                    </form>
                  @endif
                    @if($lists->est_turnout_round5>0)
                        <div class="Pollcompleted">
                        <p class="PollText display-2">{{$lists->est_turnout_round5}} %</p>
                        <small class="text-white text-center">Last Updated on  {{date("M d, Y H:i:s",strtotime($lists->update_at_round5))}}   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Updated Device:- {{$lists->update_device_round5}}</small>
                        </div>
                      @elseif($current_date>=$p5) 
                        <div class="PollMissed">
                          <p class="PollText display-2">Missed</p>
                        </div>
                     @else
                        <div class="PollDeactive">  <p class="PollText display-2">Not Open</p>   </div>
                    @endif
                  </td>
                </tr>
                <tr>
                <td>&nbsp;</td>
                  <td><h1 class="display-1" style="font-size: 45px;  text-transform: uppercase;  padding: 14px 14px 0;"><a href="#">End of Poll</a></h1></td>
                  
                </tr>
              </tbody>
                  
              </table>
              
          
          
      
  
  </div>    
    </div>
  </div>
  </section>
  <!--<section class="mt-3">
  <div class="container">
<div class="row">
  				
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                <div class="col"> <h4>Estimated Voter Turnout Entry </h4> </div> 
				<div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span>&nbsp;&nbsp; <b>PC Name:</b> 
            <span class="badge badge-info">{{$pc->PC_NAME}}</span>&nbsp;&nbsp;  <b class="bolt">AC Name:</b>  <span class="badge badge-info">{{$ac->AC_NAME}}</span>&nbsp;&nbsp;  
            </p></div>
         
                </div>
                </div>
     <div class="section mt-4">
  <div class="container">
    <div class="row text-center mb-3">
   <div class="col">
   <span class="">
   <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">{{$totalturnout_per}}%</span>
   <br>
         <span type="text" style="color: #28a745;  text-transform: uppercase;  letter-spacing: 3px;" class=" ">Estimated Voter Turn Out</span></span>
  </div></div>
  
  </div>
  </div>
   <div class="row">
    <div class="col">
        
        @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
         @if (session('error_mes'))
          <div class="alert alert-danger"> {{session('error_mes') }}</div>
        @endif
         @if (\Session::has('success'))
			<div class="alert alert-success">
				<ul>
					<li>{!! \Session::get('success') !!}</li>
				</ul>
			</div>
		@endif
      
   </div>
   </div>
     
 





      <div class="card-border">  
       <div class="row">
        <div class="col-md-12">
         
         <table   class="table table-striped table-bordered" style="width:100%">
        
        <tbody> 
                <form class="form-horizontal" id="election_form" method="post" action="{{url('aro/voting/estimated-entry')}}" enctype="multipart/form-data" autocomplete='off'>  {{csrf_field()}}   <input type="hidden" name="id" value="{{$lists->id}}">    
                <tr>  <td>
                 
               <div class="section mt-4">
  <div class="container">
    <div class="row text-center mb-3">
   <div class="col">
     <span type="text" style="color: #28a745;  text-transform: uppercase;  letter-spacing: 3px;" class=" ">10:00 AM</span><br>
              @if( $current_date>=$pt1 and $current_date<=$p1)   <p id="demo"></p> 
                  <input type="text" name="est_turnout_round1" id="est_turnout_round1" value="" maxlength="5">
                 <span id="errmsg" class="text-danger"></span> 
                   @if ($errors->has('est_turnout_round1'))
                                <span style="color:red;">{{ $errors->first('est_turnout_round1') }}</span>
                           @endif 
                 <br>
                 <button type="submit" id="saverec" name="saverec" value="1" class="btn btn-primary custombtn">Update</button><br>
                @endif 
   <span class="">
   
   @if($lists->est_turnout_round1>0)
   <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">{{$lists->est_turnout_round1}}%</span>
   @elseif($current_date>=$p1) 
       <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">MISSED</span>
      @else
      <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">NOT OPEN</span>
   @endif
         </span>
    @if($lists->update_at_round1!=0)
     <br><span type="text" class=" ">Lsate Updated Time:- {{date("d-m-Y H:i:s",strtotime($lists->update_at_round1))}}</span> <span  class=" ">Updated Device:- {{$lists->update_device_round1}}</span>
     @endif
  </div></div>
  
  </div>
  </div></td> </tr> 
                <input type="hidden" name="field_name" value="est_turnout_round1">  
             </form>


             <form class="form-horizontal" id="election_form1" method="post" action="{{url('aro/voting/estimated-entry')}}" enctype="multipart/form-data" autocomplete='off'>  {{csrf_field()}} <input type="hidden" name="id" value="{{$lists->id}}">
                <tr>   <td>
            
  <div class="container">
    <div class="row text-center mb-3">
   <div class="col">
      
    
                 <span type="text" style="color: #28a745;  text-transform: uppercase;  letter-spacing: 3px;" class=" ">12:00 PM</span><br>
                @if( $current_date>=$pt2 and $current_date<=$p2)    <p id="demo1"></p>
                  <input type="text" name="est_turnout_round2" id="est_turnout_round2" value=""  maxlength="5">
                 <span id="errmsg1" class="text-danger"></span>   
                   @if ($errors->has('est_turnout_round2'))
                                <span style="color:red;">{{ $errors->first('est_turnout_round2') }}</span>
                           @endif 
                 <button type="submit" id="saverec1" name="saverec" value="2" class="btn btn-primary custombtn">Update</button> <br>
                 <p id="demo"> </p> <br>   
      @endif
   <span class="">
  
    
   @if($lists->est_turnout_round2>0)
   <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">{{$lists->est_turnout_round2}}%</span>
   @elseif($current_date>=$p2) 
       <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">MISSED</span>
      @else
      <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">NOT OPEN</span>
   @endif
   
         </span>
      @if($lists->update_at_round2!=0)
     <br><span type="text" class=" ">Lsate Updated Time:- {{date("d-m-Y H:i:s",strtotime($lists->update_at_round2))}}</span> <span  class=" ">Updated Device:- {{$lists->update_device_round2}}</span>
     @endif
  </div></div>
  
  </div>
  </div></td>


               </tr> 
                <input type="hidden" name="field_name" value="est_turnout_round2">
             </form>
             <form class="form-horizontal" id="election_form2" method="post" action="{{url('aro/voting/estimated-entry')}}" enctype="multipart/form-data" autocomplete='off'>  {{csrf_field()}} <input type="hidden" name="id" value="{{$lists->id}}">
                <tr>   <td>
               

                 <div class="section mt-4">
  <div class="container">
    <div class="row text-center mb-3">
   <div class="col">
      <span type="text" style="color: #28a745;  text-transform: uppercase;  letter-spacing: 3px;" class=" ">02:00 PM</span><br>
    @if( $current_date>=$pt3 and $current_date<=$p3)    <p id="demo2"></p> 
                 <input type="text" name="est_turnout_round3" id="est_turnout_round3" value=""  maxlength="5">
                 <span id="errmsg2" class="text-danger"></span>  
                   @if ($errors->has('est_turnout_round3'))
                                <span style="color:red;">{{ $errors->first('est_turnout_round3') }}</span>
                           @endif 
                 <button type="submit" id="saverec2"  name="saverec" value="3"class="btn btn-primary custombtn">Update</button><br> 
                  <p id="demo1"> </p> <br>    
      @endif
   <span class="">
    
    @if($lists->est_turnout_round3>0)
   <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">{{$lists->est_turnout_round3}}%</span>
   @elseif($current_date>=$p3) 
       <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">MISSED</span>
      @else
      <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">NOT OPEN</span>
   @endif
   
         </span>
       @if($lists->update_at_round3!=0)
     <br><span type="text" class=" ">Lsate Updated Time:- {{date("d-m-Y H:i:s",strtotime($lists->update_at_round3))}}</span> <span  class=" ">Updated Device:- {{$lists->update_device_round3}}</span>
     @endif
  </div></div>
  
  </div>
  </div></td>
               </tr>
                <input type="hidden" name="field_name" value="est_turnout_round3"> 
           </form>

             <form class="form-horizontal" id="election_form3" method="post" action="{{url('aro/voting/estimated-entry')}}" enctype="multipart/form-data" autocomplete='off'>  {{csrf_field()}} <input type="hidden" name="id" value="{{$lists->id}}">
                <tr>   <td>
                  
      <div class="section mt-4">
  <div class="container">
    <div class="row text-center mb-3">
   <div class="col">
    <span type="text" style="color: #28a745;  text-transform: uppercase;  letter-spacing: 3px;" class=" ">04:00 PM</span><br>

    @if( $current_date>=$pt4 and $current_date<=$p4)   <p id="demo3"></p>
                  <input type="text" name="est_turnout_round4" id="est_turnout_round4" value=""  maxlength="5">
                 <span id="errmsg3" class="text-danger"></span>  
                   @if ($errors->has('est_turnout_round4'))
                                <span style="color:red;">{{ $errors->first('est_turnout_round4') }}</span>
                           @endif 
                  <button type="submit" id="saverec3" name="saverec" value="4"class="btn btn-primary custombtn">Update</button><br> 
                    
      @endif
   <span class="">
    <br>
   @if($lists->est_turnout_round4>0)
   <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">{{$lists->est_turnout_round4}}%</span>
   @elseif($current_date>=$p4) 
       <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">MISSED</span>
      @else
      <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">NOT OPEN</span>
   @endif
   
         </span>
          @if($lists->update_at_round4!=0)
     <br><span type="text" class=" ">Lsate Updated Time:- {{date("d-m-Y H:i:s",strtotime($lists->update_at_round4))}}</span> <span  class=" ">Updated Device:- {{$lists->update_device_round4}}</span>
     @endif
  </div></div>
  
  </div>
  </div></td>

               </tr>
                <input type="hidden" name="field_name" value="est_turnout_round4">
             </form>

               <tr>   <td>
             <form class="form-horizontal" id="election_form4" method="post" action="{{url('aro/voting/estimated-entry')}}" enctype="multipart/form-data" autocomplete='off'>  {{csrf_field()}} <input type="hidden" name="id" value="{{$lists->id}}">
       <div class="section mt-4">
          <div class="container">
            <div class="row text-center mb-3">
              <div class="col">
                <span type="text" style="color: #28a745;  text-transform: uppercase;  letter-spacing: 3px;" class=" ">05:00 PM</span><br>
               @if( $current_date>=$pt5 and $current_date<=$p5)  
                
                <input type="text" name="est_turnout_round5" id="est_turnout_round5" value=""  maxlength="5">
                 <span id="errmsg4" class="text-danger"></span> 
                   @if ($errors->has('est_turnout_round5'))
                                <span style="color:red;">{{ $errors->first('est_turnout_round5') }}</span>
                           @endif 
                 <button type="submit" id="saverec4" name="saverec" value="5" class="btn btn-primary custombtn">Update</button><br>  
              @endif 
              <span class="">
            <br>
            @if($lists->est_turnout_round5>0)
           <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">{{$lists->est_turnout_round5}}%</span>
           @elseif($current_date>=$p5) 
               <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">MISSED</span>
              @else
              <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">NOT OPEN</span>
           @endif
           
                 </span>
         @if($lists->update_at_round5!=0)
     <br><span type="text" class=" ">Lsate Updated Time:- {{date("d-m-Y H:i:s",strtotime($lists->update_at_round5))}}</span> <span  class=" ">Updated Device:- {{$lists->update_device_round5}}</span>
     @endif
        </div></div>
  
  </div>
  </div>
                <input type="hidden" name="field_name" value="est_turnout_round5">
             </form>
          </td>
               </tr>  
        <tr>   <td>
             
       <div class="section mt-4">
          <div class="container">
            <div class="row text-center mb-3">
              <div class="col">
               <span class=""><span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;"><a href="{{url('aro/voting/schedule-entry')}}">
                 Enter Turnout Details
               </a></span>
            
              </div>
            </div>
  
        </div>
  </div>
                <input type="hidden" name="field_name" value="est_turnout_round5">
             </form>
          </td>
               </tr> 
        </tbody>
      </table>
          
          
          </div>
          </div>
           
    
  
        

    </div>
  
 
  </div>
  </div>
  </section>-->

  
  <!--
   <section class="mt-3">
  <div class="container">
<div class="row">
          
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                <div class="col"> <h4>Estimated Voter Turnout Entry Details </h4> </div> 
       <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">AC Name:</b>  <span class="badge badge-info">{{$ac->AC_NAME}}</span>&nbsp;&nbsp;  
            </p></div>
        
                </div>
                </div>
    
       
    <div class="card-border">  
       <div class="row">
        <div class="col-md-12">
         
         <table   class="table table-striped table-bordered" style="width:100%">
        <thead> <tr> <th>Slots</th><th>Time Slot</th><th>Estimated Poll Turnout Should be entered by  </th> <th>Estimated Turnout %</th>   </tr>
        </thead>
        <tbody>     
                <tr> <td>Slots-1</td><td>Start to 10:00 AM</td><td>10:30 AM </td> <td>{{$lists->est_turnout_round1}}</td></tr> 
                <tr> <td>Slots-2</td><td>Start to 12:00 PM</td><td>12:30 PM </td> <td>{{$lists->est_turnout_round2}}</td></tr> 
                <tr> <td>Slots-3</td><td>Start to 02:00 PM</td><td>02:30 PM </td> <td>{{$lists->est_turnout_round3}}</td></tr> 
                <tr> <td>Slots-4</td><td>Start to 04:00 PM</td><td>04:30 PM </td> <td>{{$lists->est_turnout_round4}}</td></tr> 
                <tr> <td>Slots-5</td><td>Start to 05:00 PM</td><td>05:30 PM</td> <td>{{$lists->est_turnout_round5}}</td></tr> 
           
        </tbody>
      </table>
          
          
          </div>
          </div>
           
    
  
        

    </div>
    </div>
  
  
  </div>
  </div>
  </section>-->
  </main>
 
@endsection
 @section('script')
<script type="text/javascript">
   $(document).ready(function(){  
    $("#est_turnout_round1").keypress(function (e) {
       //if the letter is not digit then display error and don't type anything
        $(this).val($(this).val().replace(/[^0-9\.]/g,''));
            if ((e.which != 46 || $(this).val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)) {
          //display error message
          $("#errmsg").html("Digits Only").show().fadeOut("slow");
          return false;
      }
     });
     $("#est_turnout_round2").keypress(function (e) {
       //if the letter is not digit then display error and don't type anything
       $(this).val($(this).val().replace(/[^0-9\.]/g,''));
            if ((e.which != 46 || $(this).val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)) {
          //display error message
          $("#errmsg1").html("Digits Only").show().fadeOut("slow");
          return false;
      }
     });

     $("#est_turnout_round3").keypress(function (e) {
       //if the letter is not digit then display error and don't type anything
        $(this).val($(this).val().replace(/[^0-9\.]/g,''));
            if ((e.which != 46 || $(this).val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)) {
          //display error message
          $("#errmsg2").html("Digits Only").show().fadeOut("slow");
          return false;
      }
     });
     $("#est_turnout_round4").keypress(function (e) {
       //if the letter is not digit then display error and don't type anything
       //if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
       $(this).val($(this).val().replace(/[^0-9\.]/g,''));
            if ((e.which != 46 || $(this).val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)) {
          //display error message
          $("#errmsg3").html("Digits Only").show().fadeOut("slow");
          return false;
       }
     });
       $("#est_turnout_round5").keypress(function (e) {
       //if the letter is not digit then display error and don't type anything
          $(this).val($(this).val().replace(/[^0-9\.]/g,''));
        if ((e.which != 46 || $(this).val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)) {
          //display error message
          $("#errmsg4").html("Digits Only").show().fadeOut("slow");
          return false;
      }
     });   


    

    $('#saverec').click(function(){
    var est = $('input[name="est_turnout_round1"]').val();
      error = false;
     if(est.trim() == ''){
      $('#errmsg').html('');
      $('#errmsg').html('Please enter voters turnout');
      $( "input[name='est_turnout_round1']" ).focus();
       error = true;
      }

    if(error){
      return false;
    }

       }) // 

     $('#saverec1').click(function(){
          var est = $('input[name="est_turnout_round2"]').val();
            error = false;
           if(est.trim() == ''){
            $('#errmsg1').html('');
            $('#errmsg1').html('Please enter voters turnout');
            $( "input[name='est_turnout_round2']" ).focus();
             error = true;
            }

          if(error){
            return false;
          }

             }) // 
        $('#saverec2').click(function(){
          var est = $('input[name="est_turnout_round3"]').val();
            error = false;
           if(est.trim() == ''){
            $('#errmsg2').html('');
            $('#errmsg2').html('Please enter voters turnout');
            $( "input[name='est_turnout_round3']" ).focus();
             error = true;
            }

          if(error){
            return false;
          }

             }) // 
        $('#saverec3').click(function(){
          var est = $('input[name="est_turnout_round4"]').val();
            error = false;
           if(est.trim() == ''){
            $('#errmsg3').html('');
            $('#errmsg3').html('Please enter voters turnout');
            $( "input[name='est_turnout_round4']" ).focus();
             error = true;
            }

          if(error){
            return false;
          }

             }) // 
       $('#saverec4').click(function(){
          var est = $('input[name="est_turnout_round5"]').val();
            error = false;
           if(est.trim() == ''){
            $('#errmsg4').html('');
            $('#errmsg4').html('Please enter voters turnout');
            $( "input[name='est_turnout_round5']" ).focus();
             error = true;
            }

          if(error){
            return false;
          }

             }) // 

    }) // end function        
</script>
<script>
  var po = "{{date("Y-m-d H:i:s",strtotime($p1))}}" ;
   var countDownDate = new Date(po).getTime();
  var x = setInterval(function() {
  // alert("-countDownDate);
  // Get todays date and time
  var now = new Date().getTime();
  //  alert(now);
  // Find the distance between now and the count down date
  var distance = countDownDate - now;
   // alert(distance);
  // Time calculations for days, hours, minutes and seconds
  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
  // Output the result in an element with id="demo"
    document.getElementById("demo").innerHTML = hours + "- "
  + minutes + "-  " + seconds;
    
  // If the count down is over, write some text 
  if (distance < 0) {
    clearInterval(x);
    document.getElementById("demo").innerHTML = "EXPIRED";
  }
}, 1000);
</script>
 <script>
   var po2 = "{{date("Y-m-d H:i:s",strtotime($p2))}}" ;
 
  var countDownDate2 = new Date(po2).getTime();
  var x = setInterval(function() {

  // Get todays date and time
  var now = new Date().getTime();
    
  // Find the distance between now and the count down date
  var distance2 = countDownDate2 - now;
    
  // Time calculations for days, hours, minutes and seconds
  var days2 = Math.floor(distance2 / (1000 * 60 * 60 * 24));
  var hours2 = Math.floor((distance2 % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  var minutes2 = Math.floor((distance2 % (1000 * 60 * 60)) / (1000 * 60));
  var seconds2 = Math.floor((distance2 % (1000 * 60)) / 1000);
    
  // Output the result in an element with id="demo"
    document.getElementById("demo1").innerHTML =  hours2 + "- "
  + minutes2 + "-  " + seconds2;
    
  // If the count down is over, write some text 
  if (distance2 < 0) {
    clearInterval(x);
    document.getElementById("demo1").innerHTML = "EXPIRED";
  }
}, 1000);
</script>
<script>
   var po3 = "{{date("Y-m-d H:i:s",strtotime($p3))}}" ;
 
  var countDownDate3 = new Date(po3).getTime();
  var x = setInterval(function() {

  // Get todays date and time
  var now = new Date().getTime();
    
  // Find the distance between now and the count down date
  var distance3 = countDownDate3 - now;
    
  // Time calculations for days, hours, minutes and seconds
  var days3 = Math.floor(distance3 / (1000 * 60 * 60 * 24));
  var hours3 = Math.floor((distance3 % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  var minutes3 = Math.floor((distance3 % (1000 * 60 * 60)) / (1000 * 60));
  var seconds3 = Math.floor((distance3 % (1000 * 60)) / 1000);
    
  // Output the result in an element with id="demo"
    document.getElementById("demo2").innerHTML =  hours3 + "- "
  + minutes3 + "-  " + seconds3;
    
  // If the count down is over, write some text 
  if (distance3 < 0) {
    clearInterval(x);
    document.getElementById("demo2").innerHTML = "EXPIRED";
  }
}, 1000);
</script>
<script>
   var po4 = "{{date("Y-m-d H:i:s",strtotime($p4))}}" ;
 
  var countDownDate4 = new Date(po4).getTime();
  var x = setInterval(function() {

  // Get todays date and time
  var now = new Date().getTime();
    
  // Find the distance between now and the count down date
  var distance4 = countDownDate4 - now;
    
  // Time calculations for days, hours, minutes and seconds
  var days4 = Math.floor(distance4 / (1000 * 60 * 60 * 24));
  var hours4 = Math.floor((distance4 % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  var minutes4 = Math.floor((distance4 % (1000 * 60 * 60)) / (1000 * 60));
  var seconds4 = Math.floor((distance4 % (1000 * 60)) / 1000);
    
  // Output the result in an element with id="demo"
    document.getElementById("demo3").innerHTML = hours4 + "- "
  + minutes4 + "-  " + seconds4;
    
  // If the count down is over, write some text 
  if (distance4 < 0) {
    clearInterval(x);
    document.getElementById("demo3").innerHTML = "EXPIRED";
  }
}, 1000);
</script>
<script>
   var po5 = "{{date("Y-m-d H:i:s",strtotime($p5))}}" ;
 
  var countDownDate5 = new Date(po5).getTime();  
  var x = setInterval(function() {   

  // Get todays date and time
  var now = new Date().getTime();
    
  // Find the distance between now and the count down date
  var distance5 = countDownDate5 - now;
    
  // Time calculations for days, hours, minutes and seconds
  var days5 = Math.floor(distance5 / (1000 * 60 * 60 * 24));
  var hours5 = Math.floor((distance5 % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  var minutes5 = Math.floor((distance5 % (1000 * 60 * 60)) / (1000 * 60));
  var seconds5 = Math.floor((distance5 % (1000 * 60)) / 1000);
    
  // Output the result in an element with id="demo"
    document.getElementById("demo5").innerHTML = hours5 + "- "
  + minutes5 + "-  " + seconds5;
    
  // If the count down is over, write some text 
  if (distance5 < 0) {
    clearInterval(x);
    document.getElementById("demo5").innerHTML = "EXPIRED";
  }
}, 1000);
</script>
 <script>
   var po5 = "{{date("Y-m-d H:i:s",strtotime($p5))}}" ;
 
  var countDownDate5 = new Date(po5).getTime();  
  var x = setInterval(function() {   

  // Get todays date and time
  var now = new Date().getTime();
    
  // Find the distance between now and the count down date
  var distance5 = countDownDate5 - now;
    
  // Time calculations for days, hours, minutes and seconds
  var days5 = Math.floor(distance5 / (1000 * 60 * 60 * 24));
  var hours5 = Math.floor((distance5 % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  var minutes5 = Math.floor((distance5 % (1000 * 60 * 60)) / (1000 * 60));
  var seconds5 = Math.floor((distance5 % (1000 * 60)) / 1000);
    
  // Output the result in an element with id="demo"
    document.getElementById("demo5").innerHTML =  hours5 + "- "
  + minutes5 + "-  " + seconds5;
    
  // If the count down is over, write some text 
  if (distance5 < 0) {
    clearInterval(x);
    document.getElementById("demo5").innerHTML = "EXPIRED";
  }
}, 1000);
</script>
@endsection 