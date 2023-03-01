@extends('admin.layouts.pc.dashboard-theme')
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

#acViewBody a{
    text-decoration: none !important;
    color: #000 !important;
    cursor: default !important;
}

#acViewBody a:hover{
    text-decoration: none !important;
    color: #000 !important;
    cursor: default !important;
}

  </style>

  <div class="loader" style="display:none;"></div>


<section class="statistics color-grey pt-4 pb-2">






<div class="container-fluid">
  <div class="row">
  <div class="col-md-9 pull-left">
   <h4>AC wise Turnout</h4>
  </div>

   <div class="col-md-3  pull-right text-right">

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

    

           <div class="table-responsive">
      
            <table id="acViewBody" class="table table-striped table-bordered" style="width:100%"><thead>
       <tr>
        <th > State </th>
        <th > PC No </th>
        <th > PC Name </th>
        <th > AC No </th>
        <th > AC Name </th>

        <th > Total Elector</th>
        <th > Latest Total </th>
        <th > Percentage </th>

       </tr>
    </thead>
        <tbody>

        @foreach($results as $result)
            <tr>
            @foreach($result as $value)
                <td>
                    {{ $value }}
                </td>
            @endforeach
        </tr>
        @endforeach
      
       </tbody></table>


         </div><!-- End Of  table responsive -->  
      </div><!-- End Of intra-table Div -->   
        
         
      </div><!-- End Of random-area Div -->
      
    </div><!-- End OF page-contant Div -->
    </div>      
  </div><!-- End Of parent-wrap Div -->
  </div> 


<script type="text/javascript">
setTimeout(function(e){
    referesh_page();
},300000);

function referesh_page(){
    location.reload();
}

</script>
@endsection