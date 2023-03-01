@extends('admin.layouts.pc.dashboard-theme')
@section('content')
<section class="dashboard-header pt-3 pb-3">
  <div class="container-fluid">
  
        
      <form id="generate_report_id" class="row" method="get" onsubmit="return false;">
  

          
         
        </form>   
  
    
  </div>
</section>

<main role="main" class="inner cover mb-3 mt-3">
<section>  

  <div class="container-fluid">
  <div class="row">   


  @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
          @endif
          @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
          @endif   




<div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h4>{!! $heading_title !!}</h4></div> 
                  <div class="col"><p class="mb-0 text-right">

                    @if(isset($filter_buttons) && count($filter_buttons)>0)
                            @foreach($filter_buttons as $button)
                                <?php $but = explode(':',$button); ?>
                                <b>{!! $but[0] !!}:</b>
                                <span class="badge badge-info">{!! $but[1] !!}</span>
                            @endforeach  
                    @endif
                



                    &nbsp;&nbsp; 
                  <b></b> 
                   <span class="badge badge-info"></span>&nbsp;&nbsp;  </p></div>
                </div> <!-- end col-->
                </div><!-- end row-->
              
            <div class="card-body"> 

    

           <div class="table-responsive">
          <table class="table table-bordered ">
           <thead class="capatlize">


            <tr> 
              
             <th>AC </th>

              <th>Finalized By RO</th>
              <th>Finalized By CEO</th>
            </tr>

          </thead>
          @if(count($results)>0)

          <tbody>   
            <?php $i = 0; ?>
            @foreach($results as $result)
              <tr id="{{$result['id']}}" data-id="{!! $result['id'] !!}">
                <td>{!! $result['pc_no'] !!}-{!! $result['pc_name'] !!}</td>
     
                <td> 
                  <?php if($result['finalize_by_ro']){ ?>
                    Yes 
                    <?php if(!$result['finalize_by_ceo']){ ?>
                    <br>
                    <button class="btn btn-success ceo-definalize">De-finalize </button>
                    <?php } ?>
                  <?php }else{ ?>
                    No
                  <?php } ?>
                </td>
                <td>
                  <?php if($result['finalize_by_ceo']){ ?>
                    Yes
                  <?php }else if($result['finalize_by_ro']){ ?>
                    <button class="btn btn-success ceo-finalize">Finalize</button>
                  <?php }else{ ?>
                    <small class="alert aleart-warning">Finalize button will appear once RO will finalize the indexcard.</small>
                  <?php } ?> 
                </td>

              </tr>
              <?php $i++; ?>
            @endforeach

          </tbody>

         
          @else
          <tbody>
          <tr>
            <td colspan="15" cellpadding='5' align="center">
              Please Select a AC.
            </td>
          </tr>
          </tbody>
          @endif

           </table>
         </div><!-- End Of  table responsive -->  
       </div>
     </div>
      </div><!-- End Of intra-table Div -->   
        
         
      </div><!-- End Of random-area Div -->
      
</section>
</main>
@endsection

@section('script')

<script type="text/javascript">
function filter(){
  var url = "<?php echo $action ?>";
  var query = '';
    if($("#pc_no").val() != ''){
      query += '&pc_no='+$("#pc_no").val();
    }
    if($("#year").val() != ''){
      query += '&year='+$("#year").val();
    }
    window.location.href = url+'?'+query.substring(1);
}
$(document).ready(function(e){
  $('.ceo-finalize').click(function(e){
    if(confirm("Are you sure you want to finalize.")){
      id = $(this).parent('td').parent('tr').attr('data-id');
        $.ajax({
          url: "{!! url('/pcceo/indexcard/finalize/post') !!}",
          type: 'POST',
          data: '_token={!! csrf_token() !!}&finalized=1&id='+id+'&year='+$('#year').val(),
          dataType: 'json', 
          beforeSend: function() {
            $('#'+id+' button').prop('disabled',true);
            $('#'+id+' button').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
          },  
          complete: function() {
            $('.loading_spinner').remove();
            $('#'+id+' button').prop('disabled',false);
          },        
          success: function(json) {
            if(json['status'] == true){
              location.reload();
            }
            if(json['status'] == false){
              error_messages(json['message']);
            }
            $('.loading_spinner').remove();
            $('#'+id+' button').prop('disabled',false);
          },
          error: function(data) {
            var errors = data.responseJSON;
            $('.loading_spinner').remove();
            $('#'+id+' button').prop('disabled',false);
          }
        });
      } 
  });


  $('.ceo-definalize').click(function(e){
    if(confirm("Are you sure you want to De-finalize.")){
      id = $(this).parent('td').parent('tr').attr('data-id');
        $.ajax({
          url: "{!! url('/pcceo/indexcard/finalize/post') !!}",
          type: 'POST',
          data: '_token={!! csrf_token() !!}&finalized=0&id='+id+'&year='+$('#year').val(),
          dataType: 'json', 
          beforeSend: function() {
            $('#'+id+' button').prop('disabled',true);
            $('#'+id+' button').append(" <i class='fa fa-circle-o-notch loading_spinner fa-spin load' aria-hidden='true'></i>");
          },  
          complete: function() {
            $('.loading_spinner').remove();
            $('#'+id+' button').prop('disabled',false);
          },        
          success: function(json) {
            if(json['status'] == true){
              location.reload();
            }
            if(json['status'] == false){
              error_messages(json['message']);
            }
            $('.loading_spinner').remove();
            $('#'+id+' button').prop('disabled',false);
          },
          error: function(data) {
            var errors = data.responseJSON;
            $('.loading_spinner').remove();
            $('#'+id+' button').prop('disabled',false);
          }
        });
      } 
  });


  
});
</script>
@endsection