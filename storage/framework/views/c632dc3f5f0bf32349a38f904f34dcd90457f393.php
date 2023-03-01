<?php
  $setting = \App\models\Admin\SettingModel::get_setting_cache(); 
  $is_two_step = 0;
  if(!empty($setting['two_step'])){
    $is_two_step = $setting['two_step'];
  }

  $auto_logout_after = 0;
  if(!empty($setting['auto_logout_after']) && $setting['auto_logout_after']>0){
    $auto_logout_after = $setting['auto_logout_after'];
  }

?>

<?php if($auto_logout_after > 0 && Auth::user() && $user_data->role_id != 7){ ?>
<div class="modal fade" id="auto_logout_after" tabindex="-1" role="dialog" aria-labelledby="auto_logout_after" aria-hidden="false" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="two_step_form" action="return false;">
        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
      <div class="modal-header">
        Auto Logout Warning...
      </div>
      <div class="modal-body">
        <div class="form-group row text-center">
          <p id="reset_logout_countdown_div" class="reset_logout_countdown_div text-center text-danger" style="width: 100%;"></p>
        </div>
        <div class="form-group text-center">
          <button type="button" class="btn btn-primary stay_login_extend" id="auto_logout_extend">Stay Login</button>
          <button type="button" class="btn" id="auto_logout_cancel">Logout</button>
        </div>
       
      </div>
      <div class="modal-footer">
        
      </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
var auto_logout_after = "<?php echo e($auto_logout_after); ?>";
var is_logout = 0;
var timeoutHandle;
function countdown(minutes,stat) {
    var seconds = 60;
    var mins = minutes;
     
    if(getCookie("minutes") && getCookie("seconds") && stat){
      var seconds = getCookie("seconds");
      var mins = getCookie("minutes");
    }
   
    function tick() {
  
        var counter = document.getElementById("reset_logout_countdown_div");
        setCookie("minutes",mins,10)
        setCookie("seconds",seconds,10)
        var current_minutes = mins-1
        seconds--;
        if($('#reset_logout_countdown_div').length>0){
          $('#reset_logout_countdown_div').html("Auto Logout in " + current_minutes.toString() + ":" + (seconds < 10 ? "0" : "") + String(seconds));
        }
		
		//save the time in cookie
        if( seconds > 0 ) {
            timeoutHandle = setTimeout(tick, 1000);
        } else {
            if(mins > 1){
              // countdown(mins-1);   never reach “00″ issue solved:Contributed by Victor Streithorst    
              setTimeout(function () { 
                countdown(parseInt(mins)-1,false); }, 1000
              );
                     
            }
        }

        if(seconds==0 && mins==1){
          clear_all_timeout();
          $('#auto_logout_after').modal();
          if(is_logout == 0){
            is_logout = 1;
            countdown(1,false);
          }else{
            is_logout = 0;
            $('#auto_logout_cancel').click();
          }
        }
        console.log(mins+' '+seconds);
    }
    tick();

}


function setCookie(cname,cvalue,exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires=" + d.toGMTString();
    document.cookie = cname+"="+cvalue+"; "+expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

$(document).ready(function(e){

  $('#auto_logout_extend').click(function(e){
    clear_all_timeout();
    countdown(auto_logout_after,false);
    $('#auto_logout_after').modal('hide');
    is_logout = 0;
  });
  countdown(auto_logout_after,false);

  $('#auto_logout_cancel').click(function(e){
    window.location.href = "<?php echo url('/logout'); ?>"
  });


    $('button, input, a, select, textarea').on('keyup change keydown click', function(e){
      
        clear_all_timeout();
        countdown("<?php echo e($auto_logout_after); ?>",false);
        is_logout = 0;
      
    });
});

  function clear_all_timeout(){
    var id = window.setTimeout(function() {}, 0);
    while (id--) {
        window.clearTimeout(id); // will do nothing if no timeout with id is present
    }
  }
</script>

<?php } ?>


<?php if($is_two_step==1 && Auth::user() && (trim(Auth::user()->two_step_pin) == "" || Auth::user()->two_step_pin_flag == 0)){ ?>
<div class="modal fade animated zoomIn" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="false" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="two_step_form" action="return false;">
        <input type="hidden" name="_token" class="token" value="<?php echo csrf_token(); ?>">
      <div class="modal-header">
        Please Setup 2 step verification pin
      </div>
      <div class="modal-body">
        <div class="form-group row">
          <label class="col-md-3">Pin</label>
          <div class="col-md-9">
          <input type="password" name="pin" value="" id="pin" class="form-control">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-md-3">Confirm Pin</label>
          <div class="col-md-9">
          <input type="password" name="pin_confirmation" value="" id="pin_confirmation" class="form-control">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="pin_submit">Update</button>
      </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
function afterModalTransition(e) {
  e.setAttribute("style", "display: none !important;");
}

$(document).ready(function(e){
  


  $('#pin_submit').click(function(e){
      $.ajax({
        url: "<?php echo url('/profile/pin/update'); ?>",
        type: 'POST',
        data: '_token='+$('#two_step_form .token').val()+'&pin='+$('#two_step_form #pin').val()+'&pin_confirmation='+$('#two_step_form #pin_confirmation').val(),
        dataType: 'json', 
        beforeSend: function() {
          $('.modal').removeClass('animated shake');
          $('#two_step_form .text-danger').remove();
          $('#two_step_form input').removeClass('input-error');
          $('#pin_submit').prop('disabled',true);
          $('#pin_submit').text("Validating...");
          $('#pin_submit').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
        },  
        complete: function() {

        },        
        success: function(json) {
          $('.modal').addClass('animated shake');
          $('.jq-toast-wrap').remove();

          if(json['status'] == true){
            $('#exampleModal').modal('hide');
            success_messages(json['message']);
          }

          if(json['status'] == false){
            if(json['login_required']){
              error_messages(json['message']);
            }
            if(json['errors']['pin']){
              $("#two_step_form input[name='pin']").addClass("input-error");
              $("#two_step_form input[name='pin']").after("<span class='text-danger'>"+json['errors']['pin'][0]+"</span>");
            }
            if(json['errors']['pin_confirmation']){
              $("#two_step_form input[name='pin_confirmation']").addClass("input-error");
              $("#two_step_form input[name='pin_confirmation']").after("<span class='text-danger'>"+json['errors']['pin_confirmation'][0]+"</span>");
            }
          }

          $('#pin_submit').prop('disabled',false);
          $('#pin_submit').text("Submit");
          $('.loading_spinner').remove();
        },
        error: function(data) {
          var errors = data.responseJSON;
          $('#pin_submit').prop('disabled',false);
          $('#pin_submit').text("Submit");
          $('.loading_spinner').remove();
        }
      }); 
  });

  $('#exampleModal').modal();

});

</script>
<?php } ?>

<?php 
  
  $auto_logout_after = 0;
  if(!empty($setting['auto_logout_after']) && $setting['auto_logout_after']>0){
    $auto_logout_after = $setting['auto_logout_after'];
  }

?><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/admin/common/supporting-header.blade.php ENDPATH**/ ?>