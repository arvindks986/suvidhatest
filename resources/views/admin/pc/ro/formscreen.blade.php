@extends('admin.layouts.theme')
@section('content') 
<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
   <div class="nw-crte-usr">
         <div class="head-title">
          <h3><i><img src="{{ asset('theme/images/icons/tab-icon-010.png')}}" /></i>QR code </h3>
         </div>
         @if(Session::has('success_admin'))
             <div class="alert alert-success">
                <strong> {{ nl2br(Session::get('success_admin')) }}</strong> 
              </div>
          @endif   
          @if(Session::has('unsuccess_insert'))
             <div class="alert alert-danger">
                <strong> {{ nl2br(Session::get('unsuccess_insert')) }}</strong> 
              </div>
          @endif  
            
            <form class="form-horizontal" id="election_form" method="POST"  action="{{url('ro/viewform') }}" >
                {{ csrf_field() }}   
             
               <textarea name="qrcode" id="qrcode" rows="5" cols="25"> 123456</textarea>

              <div class="btns-actn">
                 <input type="submit" value="Next">
              </div>
            </form>  
          </div><!-- End Of nw-crte-usr Div -->
   
        
    
    </div> <!-- End Of child-area Div -->     
  </div><!-- End Of parent-wrap Div -->
  </div> 

@endsection