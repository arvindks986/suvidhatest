@extends('admin.layouts.pc.theme')
@section('title', 'Candidate and Counting Section')
@section('bradcome', 'List of candidate finalize')
@section('content') 
   <?php  $st=getstatebystatecode($user_data->st_code);   ?> 

   
<section class="">
  <div class="container">
  <div class="row">
  <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> End of PollFinalize</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b></b> 
               </p>
              </div>
            </div>
      </div>
  
 <div class="card-body">
       @if(Session::has('success_mes'))
             <div class="alert alert-success">
                <strong> {{ nl2br(Session::get('success_mes')) }}</strong> 
              </div>
          @endif
      @if(Session::has('error_messsage'))
             <div class="alert alert-danger">
                <strong> {{ nl2br(Session::get('error_messsage')) }}</strong> 
              </div>
          @endif    
   <div class="modal-body">
    @if(isset($newdetails))  Finalized :- 
         @foreach($newdetails as $details) 
          @if($details->end_of_poll_finalize==1)  Phase-{{$details->scheduleid}}  @endif
        @endforeach 
        @endif
    <form class="form-horizontal" id="election_form" method="POST"  action="{{url('pcceo/veryfyend-of-poll-finalize') }}" >
                {{ csrf_field() }}   
            
               
     
    
    
       <div class="mb-3">
       <p>Select Phase Number: <sup>*</sup></p>
        <select name="phasenumber" class="form-control party_id" required="required">
        <option value="">-- Select Phase --</option>
        @if(isset($newdetails))
         @foreach($newdetails as $details) 
          <option value="{{$details->scheduleid}}"> Phase-{{$details->scheduleid}} </option>  
        @endforeach 
        @endif
      </select>
     
    <span id="err" class="text-danger"></span>
                           @if ($errors->has('phasenumber'))
                                 <span style="color:red;">{{ $errors->first('phasenumber') }}</span>
                           @endif  
      
  </div>
  
   
 
   
  <div class="modal-footer">
       
        <button type="submit" class="btn btn-primary">Finalize By CEO</button>
      </div>
    </form>
      </div>
    </div>
    </div>
  </div>
  </div>
  </section>

 
@endsection
@section('script')
<script type="text/javascript">
           jQuery(document).ready(function(){
          
          $("#election_form").submit(function(){
             if($("#phasenumber").val()=='')
                    {  
                    $("#err").text("");
                    $("#err").text("Pleaseselect phase");
                    $("#phasenumber").focus();
                    return false;
                    }
               });
        });
  
  
    
</script>
 
@endsection