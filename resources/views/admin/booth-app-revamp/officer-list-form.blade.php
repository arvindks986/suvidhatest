@extends('admin.layouts.ac.theme')
@section('content')
<style type="text/css">
  .loader {
   position: fixed;
   left: 50%;
   right: 50%;
   border: 16px solid #f3f3f3; /* Light grey */
   border-top: 16px solid #3498db; /* Blue */
   border-radius: 50%;
   width: 120px;
   height: 120px;
   animation: spin 2s linear infinite;
   z-index: 99999;
 }
 @keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>

<div class="loader" style="display:none;"></div>

<section class="statistics color-grey pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col pull-left">
       <h4>{!! $heading_title !!}</h4>
     </div>

     <div class="col  pull-right  text-right">
	 
	 @if(isset($filter_buttons) && count($filter_buttons)>0)

        @foreach($filter_buttons as $button)
        <?php $but = explode(':',$button); ?>
        <span class="" style="margin-right: 10px;">
          <span><b>{!! $but[0] !!}:</b></span>
          <span class="badge badge-info">{!! $but[1] !!}</span>

        </span>

        @endforeach 
		
      @if(count($buttons)>0)
      @foreach($buttons as $button)
      <span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="{{ $button['name'] }}" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
      @endforeach
      @endif  


   
@endif	  
    </div>
  </div>
</div>  
</section>







<div class="container-fluid">

  <!-- Start parent-wrap div --> 
  <!-- Start parent-wrap div -->  
  <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
      @if(Session::has('flash-message'))
@if(Session::has('status'))
<?php
$status = Session::get('status');
if($status==1){
 $class = 'alert-success';
}
else{
  $class = 'alert-danger';
}
?>
@endif
<div class="alert <?php echo $class; ?> in">
  <a href="#" class="close" data-dismiss="alert">&times;</a>
  {{ Session::get('flash-message') }}
</div>
@endif
     <div class="page-contant card">
      <div class="random-area card-body">
        <br>

       
        
        <form class="form-horizontal" method="post" action="{!! $action !!}">

          <input type="hidden" value="{!! csrf_token() !!}" name="_token">
          <?php if(isset($encrpt_id)){ ?>
            <input type="hidden" value="{!! $encrpt_id !!}" name="id">
          <?php } ?>

          <div class="form-group mb-1">
            <label class="col-md-3  pull-left">Polling Station </label>
            <select class="form-control col-md-9 " name="ps_no" id="ps_no">
              @foreach($polling_stations as $iterate_ps)
              <?php if($ps_no == $iterate_ps['ps_no']){ ?>
                <option value="{{$iterate_ps['ps_no']}}" selected="selected">{{$iterate_ps['ps_no']}}-{{$iterate_ps['ps_name']}}</option>
              <?php } ?>
              @endforeach
            </select>

            @if(isset($errors))
            <span class="text-error text-danger text-right pull-right">{!! $errors->first('ps_no') !!}</span>
            @endif 

          </div>

          <div class="form-group mb-1">
            <label class="col-md-3 pull-left">Name</label>
            <input type="text" class="form-control col-md-9 " name="name" id="name" value="{{$name}}" placeholder="Name">
            @if(isset($errors))
            <span class="text-error text-danger text-right pull-right">{!! $errors->first('name') !!}</span>
            @endif 

          </div>


          <div class="form-group mb-1">
            <label class="col-md-3 pull-left">Mobile</label>
            <input type="text" class="form-control col-md-9 " name="mobile" id="name" value="{{$mobile}}" placeholder="Mobile" size="10" maxlength="10">
            @if(isset($errors))
            <span class="text-error text-danger text-right pull-right">{!! $errors->first('mobile') !!}</span>
            @endif 
          </div>

          <div class="form-group mb-1">
            <label class="col-md-3  pull-left">Status</label>
            <select class="form-control col-md-9 " name="status" id="status">
              <?php if($status == 1){ ?>
                <option value="1" selected="selected">Enable</option>
                <option value="0">Disable</option>
              <?php }else{ ?>
                <option value="1">Enable</option>
                <option value="0" selected="selected">Disable</option>
              <?php } ?>
            </select>

            @if(isset($errors))
            <span class="text-error text-danger text-right pull-right">{!! $errors->first('status') !!}</span>
            @endif 

          </div>

          <?php /* <div class="form-group mb-1">
            <label class="col-md-3  pull-left">Role</label>
            <select class="form-control col-md-9 " name="role_id" id="role_id" onchange="required_pin(this.value)">
              @foreach($roles as $iterate_role)
              <?php if($role_id == $iterate_role['role_id']){ ?>
                <option value="{{$iterate_role['role_id']}}" selected="selected">{{$iterate_role['name']}}</option>
              <?php }else{ ?>
                <option value="{{$iterate_role['role_id']}}">{{$iterate_role['name']}}</option>
              <?php } ?>
              @endforeach
            </select>

            @if(isset($errors))
            <span class="text-error text-danger text-right pull-right">{!! $errors->first('role_id') !!}</span>
            @endif 

          </div>  */ ?>

            <input type="hidden" class="form-control col-md-9 " name="pin" id="pin" value="1234" placeholder="Please enter 4 digit pin">
           <input type="hidden" class="form-control col-md-9 " name="pin_confirmation" id="pin_confirmation" value="1234" placeholder="Confirmed Pin">
 


         

          <input type="hidden" name="role_id" value="{!! $role_id !!}" id="role_id">
          <input type="hidden" name="role_level" value="{!! $role_level !!}" id="role_level">

          

          <div class="form-group mb-1 is_pro_right display_none">
            <label class="col-md-3  pull-left">Authorized this PO for PRO Rights</label>
            <select class="form-control col-md-9 " name="is_pro_right" id="is_pro_right">
              <?php if($is_pro_right == 1){ ?>
                <option value="1" selected="selected">Yes</option>
                <option value="0">No</option>
              <?php }else{ ?>
                <option value="1">Yes</option>
                <option value="0" selected="selected">No</option>
              <?php } ?>
            </select>

            @if(isset($errors))
            <span class="text-error text-danger text-right pull-right">{!! $errors->first('is_pro_right') !!}</span>
            @endif 

          </div>


          <div class="form-group mb-1 is_testing">
            <label class="col-md-3  pull-left">Testing User</label>
            <select class="form-control col-md-9 " name="is_testing" id="is_testing">
              <?php if($is_testing == 1){ ?>
                <option value="1" selected="selected">Yes</option>
                <option value="0">No</option>
              <?php }else{ ?>
                <option value="1">Yes</option>
                <option value="0" selected="selected">No</option>
              <?php } ?>
            </select>

            @if(isset($errors))
            <span class="text-error text-danger text-right pull-right">{!! $errors->first('is_testing') !!}</span>
            @endif 

          </div>



          <div class="form-group mb-1">
            <label class="col-md-3 pull-left" style="visibility: hidden;"></label>
            <button type="submit" class="btn btn-large btn-primary">Submit</button>
          </div>

        </form>
      </div>
    </div>
  </div> 
</div>

</div><!-- End Of parent-wrap Div -->
</div> 
@endsection

@section('script')
<script type="text/javascript">
  function required_pin(id){
    if(id == '3423'){
      $('.is_pro_right').removeClass('display_none');
    }else{
      $('.is_pro_right').addClass('display_none');
    }
  }

  $(document).ready(function(e){
    var role_id = "<?php echo $role_id ?>";
    required_pin(role_id);
  });
</script>

@endsection