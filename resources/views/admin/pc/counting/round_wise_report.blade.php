  @extends('admin.layouts.pc.theme')
  @section('content')

<section class="mt-1">
  <div class="container-fluid">  
  <div class="row">  
    <div class="col-md-12 row">
    <form id="generate_report_id" method="get" onsubmit="return false;" style="width: 100%;">
 
      <?php if(isset($rounds) && $rounds >0){ ?>
          <div class="form-group"> <label>Round </label> 
            <select name="round" id="round" class="form-control" onchange ="filter()">
            <?php for($i = 1; $i <= $rounds; $i++){ ?>
              @if($i == $round)
                <option value="{{$i}}" selected="selected" >Round {{$i}}</option> 
              @else 
                <option value="{{$i}}" >Round {{$i}}</option> 
              @endif  
            <?php } ?>
        
            </select>
          </div>
        <?php }else{ ?>
         <input type="hidden" id="round" name="round" value="{!! $round !!}">
        <?php } ?>
    </form>
  </div>
  <div class="col-md-12 row">
    @if($acs_not_filled)
    <p>AC's that are not filled or not in this round {!! $acs_not_filled !!}</p>
    @endif
  </div>
  </div>
  </div>


    <div class="container-fluid">
      <div class="row">

        <div class="card text-left" style="width:100%; margin:0 auto;">
          <div class=" card-header">
            <div class=" row">
              <div class="col form-inline"><h6 class="mr-auto">{!! $heading_title !!}</h6><p class="mb-0 text-right">

                @foreach($buttons as $button)
                <span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Export" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
                @endforeach


                @if(isset($filter_buttons) && count($filter_buttons)>0)
                  @foreach($filter_buttons as $button)
                      <?php $but = explode(':',$button); ?>
                      <b class="bolt">{!! $but[0] !!}:</b> 
                      <span class="badge badge-info">{!! $but[1] !!}</span>            
                  @endforeach
                @endif
                </p>
              </div>
              </div>
            </div>
            <div class="card-body">
              @if(isset($results) && count($results)>0)
              <table class="table table-bordered table-hover table-dot">
                <thead>
                  <tr>
                   <th>Sr. no.</th>
                   <th>Candidate Name</th>
                   <th>Party</th>
                   <th>Votes brought from Previous rounds</th>
                   <th>Votes from current round</th>
                   <th>Total Cumulative Votes</th>
                 </tr>
               </thead>
               <tbody>
                <?php foreach ($results as $result) { ?>
                  <tr>
                    <td>{!! $result['sr_no'] !!}</td>
                    <td>{!! $result['candidate_name'] !!}</td>
                    <td>{!! $result['party_name'] !!}</td>
                    <td>{!! $result['previous_total'] !!}</td>
                    <td>{!! $result['current_total'] !!}</td>
                    <td>{!! $result['total'] !!}</td>
                  </tr>
                <?php } ?>

              </tbody>
            </table>
            @else
            <p>No Record Found.</p>
            @endif 
            <!-- end reponcive-->

          </div>
        </div>


      </div>
    </div>
  </section>
  @endsection

@section('script')
<script type="text/javascript">

function filter(){
  var url = "<?php echo $action ?>";
  var query = '';
  if(jQuery("#round").val() != ''){
      query += '&round='+jQuery("#round").val();
    }
  window.location.href = url+'?'+query.substring(1);
}
</script>

@endsection