@extends('layouts.theme')
@section('content') 
<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
   <div class="nw-crte-usr">
         <div class="head-title">
          <h3><i><img src="{{ asset('theme/images/icons/tab-icon-010.png')}}" /></i>Candidate Information</h3>
         </div>
         
            
            <form class="form-horizontal" id="election_form" method="POST"  action="{{url('ro/candidateupdate') }}" >
                {{ csrf_field() }}   
                <input type="hidden" name="candidate_id" value="{{ $candate->candidate_id}}">
                <input type="hidden" name="CCODE" value="{{ $candate->CCODE}}">
            <div class="col-sm-12" > 
              <div class="form-group col-sm-6 {{ $errors->has('totalschedule') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Candidare Name:</label>
                    <div class="col-sm-6">
                  <input type="text" class="form-control" name="candname" id="candname" value="{{$candate->cand_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                   </div>
                  </div><!-- End Of form-group Div -->
               <div class="form-group col-sm-6 {{ $errors->has('pressdate') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Candidate Father's Name:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->candidate_father_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div>
                
                <div class="col-sm-12" > 
              <div class="form-group col-sm-6 {{ $errors->has('totalschedule') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Candidare Completed Age:</label>
                    <div class="col-sm-6">
                  <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->cand_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                   </div>
                  </div><!-- End Of form-group Div -->
               <div class="form-group col-sm-6 {{ $errors->has('pressdate') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Candidate Gender:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->candidate_father_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div>

                <div class="col-sm-12" > 
              <div class="form-group col-sm-6 {{ $errors->has('totalschedule') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Candidare Qualification:</label>
                    <div class="col-sm-6">
                  <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->cand_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                   </div>
                  </div><!-- End Of form-group Div -->
               <div class="form-group col-sm-6 {{ $errors->has('pressdate') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Candidate category:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->candidate_father_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div>

                <div class="col-sm-12" > 
              <div class="form-group col-sm-6 {{ $errors->has('totalschedule') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Candidare Residence Address:</label>
                    <div class="col-sm-6">
                  <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->cand_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                   </div>
                  </div><!-- End Of form-group Div -->
               <div class="form-group col-sm-6 {{ $errors->has('pressdate') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Candidate Temporary Address:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->candidate_father_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div>

           <div class="col-sm-12" > 
              <div class="form-group col-sm-6 {{ $errors->has('totalschedule') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Candidare Residence AC:</label>
                    <div class="col-sm-6">
                  <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->cand_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                   </div>
                  </div><!-- End Of form-group Div -->
               <div class="form-group col-sm-6 {{ $errors->has('pressdate') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Candidate Temporary AC:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->candidate_father_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div>

               <div class="col-sm-12" > 
              <div class="form-group col-sm-6 {{ $errors->has('totalschedule') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Candidare Residence PC:</label>
                    <div class="col-sm-6">
                  <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->cand_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                   </div>
                  </div><!-- End Of form-group Div -->
               <div class="form-group col-sm-6 {{ $errors->has('pressdate') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Candidate Temporary PC:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->candidate_father_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div>
              
               <div class="col-sm-12" > 
              <div class="form-group col-sm-6 {{ $errors->has('totalschedule') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Candidare Residence District :</label>
                    <div class="col-sm-6">
                  <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->cand_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                   </div>
                  </div><!-- End Of form-group Div -->
               <div class="form-group col-sm-6 {{ $errors->has('pressdate') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Candidate Temporary District:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->candidate_father_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div>
               
               <div class="col-sm-12" > 
              <div class="form-group col-sm-6 {{ $errors->has('totalschedule') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Candidare Residence State :</label>
                    <div class="col-sm-6">
                  <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->cand_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                   </div>
                  </div><!-- End Of form-group Div -->
               <div class="form-group col-sm-6 {{ $errors->has('pressdate') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Candidate Temporary State:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->candidate_father_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div>

                <div class="col-sm-12" > 
              <div class="form-group col-sm-6 {{ $errors->has('totalschedule') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Candidare Residence Pin Code :</label>
                    <div class="col-sm-6">
                  <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->cand_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                   </div>
                  </div><!-- End Of form-group Div -->
               <div class="form-group col-sm-6 {{ $errors->has('pressdate') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Candidate Temporary Pin Code:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->candidate_father_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div>

                 <div class="col-sm-12" > 
              <div class="form-group col-sm-6 {{ $errors->has('totalschedule') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Candidare Electoral Part No:</label>
                    <div class="col-sm-6">
                  <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->cand_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                   </div>
                  </div><!-- End Of form-group Div -->
               <div class="form-group col-sm-6 {{ $errors->has('pressdate') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Candidate Electoral Serial No:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->candidate_father_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div>

                 <div class="col-sm-12" > 
              <div class="form-group col-sm-6 {{ $errors->has('totalschedule') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Candidare Electoral Epic NO:</label>
                    <div class="col-sm-6">
                  <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->cand_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                   </div>
                  </div><!-- End Of form-group Div -->
               <div class="form-group col-sm-6 {{ $errors->has('pressdate') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Candidate Nick Name:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->candidate_father_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div>

            <div class="col-sm-12" > 
              <div class="form-group col-sm-6 {{ $errors->has('totalschedule') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Candidare Proposer Name :</label>
                    <div class="col-sm-6">
                  <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->cand_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                   </div>
                  </div><!-- End Of form-group Div -->
               <div class="form-group col-sm-6 {{ $errors->has('pressdate') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Candidate Proposer Electoral Srno:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->candidate_father_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div>

               <div class="col-sm-12" > 
              <div class="form-group col-sm-6 {{ $errors->has('totalschedule') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Candidare Proposer Partno :</label>
                    <div class="col-sm-6">
                  <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->cand_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                   </div>
                  </div><!-- End Of form-group Div -->
               <div class="form-group col-sm-6 {{ $errors->has('pressdate') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Candidate Proposer Assembly:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->candidate_father_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div>
               
                <div class="col-sm-12" > 
              <div class="form-group col-sm-6 {{ $errors->has('totalschedule') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Candidare Proposer PC :</label>
                    <div class="col-sm-6">
                  <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->cand_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                   </div>
                  </div><!-- End Of form-group Div -->
               <div class="form-group col-sm-6 {{ $errors->has('pressdate') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Candidate Proposer State:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{$candate->candidate_father_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div>
          <div class="col-sm-12" > 
              <div class="form-group col-sm-6 {{ $errors->has('totalschedule') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Political Party :</label>
                    <div class="col-sm-6">
                  <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{ $candate->cand_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                   </div>
                  </div><!-- End Of form-group Div -->
               <div class="form-group col-sm-6 {{ $errors->has('pressdate') ? ' has-error' : '' }}">
                     <label class="control-label col-sm-6" for="">Paty Type:</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" name="pressdate" id="pressdate" value="{{ $candate->candidate_father_name}}">
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                    </div>
                  </div><!-- End Of form-group Div -->
                </div>
              <div class="col-sm-12" > 
              <div class="form-group col-sm-6 {{ $errors->has('totalschedule') ? ' has-error' : '' }}" >
                     <label class="control-label col-sm-6" for="">Candidate has been convicted:</label>
                    <div class="col-sm-6">
                  <INPUT TYPE="Radio" Name="convicted" Value="1">Yes
<INPUT TYPE="Radio" Name="convicted" Value="0">No
                           @if ($errors->has('pressdate'))
                                <span class="help-block" <strong>{{ $errors->first('pressdate') }}</strong></span>
                           @endif
                   </div>
                  </div><!-- End Of form-group Div -->
               
                </div>
              <div class="btns-actn">
                  <input type="submit" value="Update Candidate"> <input type="submit" value="Next">
              </div>
            </form>  
          </div><!-- End Of nw-crte-usr Div -->
   
       <!--    Listing -->

    </div> <!-- End Of child-area Div -->     
  </div><!-- End Of parent-wrap Div -->
  </div> 

@endsection