@extends('admin.layouts.pc.dashboard-theme')
@section('content')
<style type="text/css">
  .heading th{
    text-transform: capitalize;
    text-align: left;
  }
  .complain-heading-main{
    text-transform: capitalize;
    text-align: center;
  }
</style>
<section class="dashboard-header pt-3 pb-3">
  <div class="container-fluid">
  
        
      <form id="generate_report_id" class="row" method="get" onsubmit="return false;">
  

          <div class="form-group col-md-3"> <label>State</label> 
          
            <select name="st_code" id="st_code" class="form-control" onchange ="filter()">
              <option value="">Select State</option>
            @foreach($states as $iterate_state)
              @if($st_code == $iterate_state['st_code'])
                <option value="{{$iterate_state['st_code']}}" selected="selected" >{{$iterate_state['st_name']}}</option> 
              @else 
                <option value="{{$iterate_state['st_code']}}">{{$iterate_state['st_name']}}</option> 
              @endif  
            @endforeach
        
            </select>
          </div>

          <div class="form-group col-md-3"> <label>PC </label> 
          
            <select name="pc_no" id="pc_no" class="form-control" onchange ="filter()">
            <option value="">Select PC</option>
            @foreach($pcs as $result)
              @if($pc_no == $result['pc_no'])
                <option value="{{$result['pc_no']}}" selected="selected" >{{$result['pc_no']}}-{{$result['pc_name']}}</option> 
              @else 
                <option value="{{$result['pc_no']}}" >{{$result['pc_no']}}-{{$result['pc_name']}}</option> 
              @endif  
            @endforeach
        
            </select>
          </div>
         
        </form>   
  
    
  </div>
</section>

<main role="main" class="inner cover mb-3 mt-3">
<section>  

  <div class="container-fluid">
  <div class="row">   


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
      <div class="alert <?php echo $class; ?>">
        {{ Session::get('flash-message') }}
      </div>
    @endif  


<div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h4>De-Finalize Constituency</h4></div> 
                  <div class="col"><p class="mb-0 text-right">			
						<label class="mr-3"><b>Report: </b></label>
						<a href="{{url('eci-index/indexcard/de-finalize-pcs/pdf')}}" target="_blank"><button type="button" class="btn btn-primary">Export PDF</button></a>
						<a href="{{url('eci-index/indexcard/de-finalize-pcs/excel')}}" target="_blank"><button type="button" class="btn btn-success">Export CSV</button></a>
						</p>
				  </div>
                </div> <!-- end col-->
                </div><!-- end row-->
              
            <div class="card-body"> 

    

           <div class="table-responsive">
          <table class="table table-bordered " id="list-table">
            <tr>
              <th>State Name</th>
              <th>PC Name</th>
              <!-- <th>Required definalization</th>
              <th>Required ECI Approval</th>
              <th>Date</th> 
              <th>Complain</th>-->
       
              <th>Action</th>
       
            </tr>
          @if( count($results)>0)
            
            @foreach($results as $result)
              <tr>
                <td>{!! $result['st_name'] !!}</td>
                <td>{!! $result['pc_name'] !!}</td>
                <?php /*<td><b>@if($result['no_need_approval']) Yes @else No @endif</b>
                  @if($result['definalize_access'] && $result['no_need_approval'] > 0)
                  <form action="{{$result['definalize_action']}}" method="post">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="pc_no" value="{{$result['pc_no']}}">
                    <input type="hidden" name="st_code" value="{{$result['st_code']}}">
                  <button type="submit" class="btn btn-success">De-finalize</button>
                  </form>
                  @endif
                </td>
                <td><b>{!! $result['need_approval'] !!}</b></td>
                
                <td>{!! $result['date'] !!}</td>
                <td>
                  <a href="javascript:void(0)" class="btn btn-link btn_more_info">Click here for more info</a>
                  <div class="complain_info" style="display: none;">
                  {!! $result['complain'] !!}
                  </div>
                </td>*/?>
                
                <td>
				<?php /*@if(verifyreport('777') == '0') */?>
				
				
                  @if($result['definalize_access'])
                  <form action="{{$result['definalize_action']}}" method="post" style="float:left;">
                    <input type="hidden" name="_token" value="{{csrf_token()}}" >
                    <input type="hidden" name="pc_no" value="{{$result['pc_no']}}">
                    <input type="hidden" name="st_code" value="{{$result['st_code']}}">
                  <button type="submit" class="btn btn-success">De-Finalize-IndexCard</button>
                  </form>
				 &nbsp;
				  <form action="{{$definalize_action_nomination}}" method="post" style="float:left; margin-left: 10px;">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="pc_no" value="{{$result['pc_no']}}">
                    <input type="hidden" name="st_code" value="{{$result['st_code']}}">
                  <button type="submit" class="btn btn-success">De-Finalize-Nomination</button>
                  </form>
				 &nbsp;
				  <form action="{{$definalize_action_counting}}" method="post" style="float:left;margin-left: 10px;">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="pc_no" value="{{$result['pc_no']}}">
                    <input type="hidden" name="st_code" value="{{$result['st_code']}}">
                  <button type="submit" class="btn btn-success">De-Finalize-Counting</button>
                  </form>
				  
				  
                  @else
                  --
                  @endif
			
                </td>
                
                

              </tr>
            @endforeach
          @else
          <tbody>
          <tr>
            <td colspan="6" cellpadding='5' align="center">
              No Record Found.
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


<!-- Modal for finalised Cheack -->
<div class="modal fade complain_info_modal" id="complain_info_modal" tabindex="-1" role="dialog"  aria-hidden="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
      <h4 class="modal-title">List of Changes</h4>
      <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">

    </div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Modal for finalised -->



<script type="text/javascript">
$(document).ready(function(e){      
  $('.btn_more_info').click(function(e){
    var html = $(this).next('.complain_info').html();
    $('#complain_info_modal .modal-body').html(html);
    $('#complain_info_modal').modal('show');
  });
});
function filter(){
  var url = "<?php echo $current_page ?>";
  var query = '';
    if($("#pc_no").val() != ''){
      query += '&pc_no='+$("#pc_no").val();
    }
    if($("#st_code").val() != ''){
      query += '&st_code='+$("#st_code").val();
    }
    window.location.href = url+'?'+query.substring(1);
}
</script>
@endsection