@extends('admin.layouts.pc.dashboard-theme')
@section('content')
<style type="text/css">
  .fullwidth{
    float: left;
    width: 100%;
  }
</style>
<section class="statistics color-grey pt-4 pb-2">
<div class="container-fluid">
  <div class="row">
  <div class="col-md-7 pull-left">
   <h4>{!! $heading_title !!}</h4>
  </div>

   <div class="col-md-5  pull-right text-right">

@foreach($buttons as $button)
<span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
@endforeach
      
    </div> 

  </div>
</div>  
</section>

@if(isset($filter_buttons) && count($filter_buttons)>0)
<section class="statistics pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        @foreach($filter_buttons as $button)
            <?php $but = explode(':',$button); ?>
            <span class="pull-right" style="margin-right: 10px;">
            <span><b>{!! $but[0] !!}:</b></span>
            <span class="badge badge-info">{!! $but[1] !!}</span>

            </span>
            
        @endforeach
      </div>
    </div>
  </div>
</section>
@endif





<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
     <div class="page-contant">
     <div class="random-area">
<br>

      <form class="form-horizontal" method="post" action="{!! $action !!}">
       
            <input type="hidden" value="{!! csrf_token() !!}" name="_token">
            <div class="form-group fullwidth">
                <label class="col-md-3 pull-left">Pin Setup Popup after login</label>
                <select class="form-control col-md-9 pull-left" name="two_step" id="two_step">
                  <?php if(isset($two_step) && $two_step==1){ ?>
                  <option value="1" selected="selected">Enable</option>
                  <option value="0">Disable</option>
                  <?php }else{ ?>
                  <option value="1">Enable</option>
                  <option value="0" selected="selected">Disable</option>
                  <?php } ?>
                </select>

                @if(isset($errors))
                 <span class="text-error text-danger text-right pull-right">{!! $errors->first('two_step') !!}</span>
                 @endif 
           
                </div>

                <div class="form-group fullwidth">
                <label class="col-md-3 pull-left">2 Step login with PIN</label>
                <select class="form-control col-md-9 pull-left" name="two_step_login" id="two_step_login">
                  <?php if(isset($two_step_login) && $two_step_login==1){ ?>
                  <option value="1" selected="selected">Enable</option>
                  <option value="0">Disable</option>
                  <?php }else{ ?>
                  <option value="1">Enable</option>
                  <option value="0" selected="selected">Disable</option>
                  <?php } ?>
                </select>

                @if(isset($errors))
                 <span class="text-error text-danger text-right pull-right">{!! $errors->first('two_step_login') !!}</span>
                 @endif 
           
                </div>


                <div class="form-group fullwidth">
                <label class="col-md-3 pull-left">Skip Login password on same netowrk</label>
                <select class="form-control col-md-9 pull-left" name="skip_password_network" id="skip_password_network">
                  <?php if(isset($skip_password_network) && $skip_password_network==1){ ?>
                  <option value="1" selected="selected">Enable</option>
                  <option value="0">Disable</option>
                  <?php }else{ ?>
                  <option value="1">Enable</option>
                  <option value="0" selected="selected">Disable</option>
                  <?php } ?>
                </select>

                @if(isset($errors))
                 <span class="text-error text-danger text-right pull-right">{!! $errors->first('skip_password_network') !!}</span>
                 @endif 
           
                </div>


                <div class="form-group fullwidth">
                <label class="col-md-3 pull-left">Disable Concurrent login</label>
                <select class="form-control col-md-9 pull-left" name="concurrent_login" id="concurrent_login">
                  <?php if(isset($concurrent_login) && $concurrent_login==1){ ?>
                  <option value="1" selected="selected">Enable</option>
                  <option value="0">Disable</option>
                  <?php }else{ ?>
                  <option value="1">Enable</option>
                  <option value="0" selected="selected">Disable</option>
                  <?php } ?>
                </select>

                @if(isset($errors))
                 <span class="text-error text-danger text-right pull-right">{!! $errors->first('concurrent_login') !!}</span>
                 @endif 
           
                </div>


                <div class="form-group fullwidth">
                <label class="col-md-3 pull-left">Auto Logout After<small>(in min)</small></label>
                <input type="text" class="form-control col-md-9 pull-left" name="auto_logout_after" id="auto_logout_after" value="{{$auto_logout_after}}">
                @if(isset($errors))
                 <span class="text-error text-danger text-right pull-right">{!! $errors->first('auto_logout_after') !!}</span>
                 @endif 
                <small class="text-error text-warning text-right pull-right">enter 0 if you want to keep Auto Logout disabled.</small>
                </div>


                


          <div class="form-group fullwidth">
            <button type="submit" class="pull-right btn btn-primary" style="margin-top: 30px;">Save</button>
          </div>


     </form>        
      
        


         </div><!-- End Of  table responsive -->  
      </div><!-- End Of intra-table Div -->   
        
         
      </div><!-- End Of random-area Div -->
      
    </div><!-- End OF page-contant Div -->
    </div>      
@endsection


@section('script')
@if (session('success_mes'))
<script type="text/javascript">
 success_messages("{{session('success_mes') }}");
 </script>
@endif
@if (session('error_mes'))
  <script type="text/javascript">
  error_messages("{{session('error_mes') }}");
</script>
@endif
@endsection