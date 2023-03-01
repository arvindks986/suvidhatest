@extends('admin.layouts.pc.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Prepare Counting Records')
@section('content') 
  <?php   $st=getstatebystatecode($st_code);  
          $pc=getpcbypcno($st_code,$pc_no); 
          $url = URL::to("/"); $j=0;
    ?>
 <style type="text/css">
      
        
        html {
              overflow: scroll;
              overflow-x: hidden;
             }
              ::-webkit-scrollbar {    width: 0px; 
              background: transparent;  /* optional: just make scrollbar invisible */
              }

              ::-webkit-scrollbar-thumb {
                background: #ff9800;
                }
              div.dataTables_wrapper {margin:0 auto;} 
  </style>
 <main role="main" class="inner cover mb-3">
  
  <div class="container mt-5">
  <div class="row">
  
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"> <h4>Prepare Counting Records</h4> </div> 
          <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">PC Name:</b> 
            <span class="badge badge-info">{{$pc->PC_NAME}}</span>&nbsp;&nbsp;  
            </p></div>
         
                </div>
                </div>
   <div class="row">
    <div class="col">
          @if(Session::has('success_admin'))
          <div class="alert alert-success"><strong> {{ nl2br(Session::get('success_admin')) }}</strong> </div>
       @endif  
       
      @if(Session::has('error_mes'))
        <div class="alert alert-danger"><strong> {{ nl2br(Session::get('error_mes')) }}</strong></div>
      @endif
       
       @if(Session::has('unsuccess_insert'))
        <div class="alert alert-danger"><strong> {{ nl2br(Session::get('unsuccess_insert')) }}</strong></div>
      @endif
      <div class="form-group float-right ml-4"> 
        @if($cand_finalize_ro==1 )
           @if($pc_counting->isEmpty())
            <span class="input-group-btn"><a href="{{url('ropc/counting/activate_allac') }}" class="btn btn-primary btn-sm mt-3 mr-3">Activate All AC for Counting</a></span>
            @endif
        @endif
      </div> 
         
    </div>
    </div>
    
    <div class="card-body">  
      @if($cand_finalize_ro==1)  
      @if(!$pc_counting->isEmpty()) 
   <form class="form-horizontal" id="" method="POST"  action="" >       
   <table   class="table table-striped table-bordered" style="width:100%">
        <thead> <tr><th>Sr.No</th><th>Candidate Name</th><th>Party</th>   </tr></thead>
        <tbody> <?php $j=0; ?>   
            @foreach($pc_counting as $md)  <?php $j++;  ?>
             <tr><td>{{$j}}</td> <td>{{$md->candidate_name}}</td> <td>{{$md->party_name}}</td>  
                 
                 
                </tr>
           
            @endforeach 
            
        </tbody>
     
    </table>
     </form> 
     @endif      
    @else
       <h6>Candidate Nominations details has not been finalized. Please finalize the accepted list of candidates and then start counting process.</h6>
     @endif
    </div>
    </div>
  
  
  </div>
  </div>
  </section>
  </main>
 
@endsection