@extends('admin.layouts.pc.theme')
@section('title', 'Suvidha')
@section('bradcome', 'Polling Station Details')
@section('content')

 
@if($errors->any())
        <div class="alert alert-info">{{$errors->first()}}</div>
@endif

@if (session('error'))
           <div class="alert alert-info">{{ session('error') }}</div>
@endif
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

<section class="dashboard-header section-padding">
  <div class="container-fluid">
  
        
    <form class="row"  method="post" action="{{url('#')}}" id="pswisedataform">

{{ csrf_field() }}
           <!---STATE FILTER-->
          
         <div class="form-group col-md-3"> <label>State </label>
           <select id="state" name="state" class="form-control" onchange ="return get_pc_list();">
                <option value="">--- Select State ---</option> 
                <@foreach (getallstate() as $statelist)

                 @if (old('state_id') == $statelist['ST_CODE'])
                      <option value="{{ $statelist['ST_CODE'] }}" selected>{{ $statelist['ST_NAME'] }}</option>
                @else
                      <option value="{{ $statelist['ST_CODE'] }}">{{ $statelist['ST_NAME'] }}</option>
                @endif

                @endforeach
                </select>
                @if ($errors->has('state_id'))
                    <span class="help-block">
                        <strong>{{ $errors->first('state_id') }}</strong>
                    </span>
                @endif
         </div>
      

           <!---PC FILTER-->
          <div class="form-group col-md-3"> <label>PC Constituency </label> 
          
            <select id="pc_id" onchange="return get_ac_list();" name="pc_id" class="form-control">
              <option value="">--- Select PC ---</option> 
            </select>
          </div>

           <!---AC FILTER-->
          <div class="form-group col-md-3"> <label>PC Constituency </label> 
          
            <select id="ac_id" name="ac_id" class="form-control">
              <option value="">--- Select Assembly ---</option> 
            </select>
          </div>

        <div class="form-group col-md-3">
        <div class="text-center">
          <input type="submit" value="Submit" class="btn btn-primary">
          <input type="reset" value="Reset" name="Cancel" class="btn">   
        </div>
      </div>


        </form>   
  
    
  </div>
</section>



<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
     <div class="page-contant">
     <div class="random-area">
  <br>

    

           <div class="table-responsive">
      
            <table id="data_table_table" class="table table-striped table-bordered" style="width:100%"><thead>

      <tr><th colspan="12" class="text-center">{!! $heading_title_with_all !!}</th></tr>


       <tr>
        <th>Serial No</th>
          <th>PS No</th>
          <th>PS Name</th> 
          <th>PS Type</th> 
          <th>Electors Male</th> 
          <th>Electors Female</th> 
          <th>Electors Other</th> 
          <th>Electors Total</th> 
          <th>Voter Male</th> 
          <th>Voter Female</th> 
          <th>Voter Other</th> 
          <th>Voter Total</th> 
          <th>Action</th>
         
       </tr>


    </thead>
        <tbody>
      @php  
        $count = 1;

        @endphp

         @forelse ($PsWiseDetails as $key=>$listdata)


          <tr>
             <td>{{ $count }}</td>
            <td>{{$listdata->PS_NO }}</td>
            <td>{{$listdata->PS_NAME_EN }}</td>
            <td>{{$listdata->PS_TYPE }}</td>
            <td>{{$listdata->electors_male }}</td>
            <td>{{$listdata->electors_female }}</td>
            <td>{{$listdata->electors_other }}</td>
            <td>{{$listdata->electors_total }}</td>
            <td>{{$listdata->voter_male }}</td>
            <td>{{$listdata->voter_female }}</td>
            <td>{{$listdata->voter_other }}</td>
            <td>{{$listdata->voter_total }}</td>
            <td><button type="button" class="btn btn-primary PsWiseDetailspopup" data-toggle="modal" data-target="#myModal" data-emale="{{$listdata->electors_male }}" data-efemale="{{$listdata->electors_female }}" data-eother="{{$listdata->electors_other }}" data-etotal="{{$listdata->electors_total }}" data-vmale="{{$listdata->voter_male }}" data-vfemale="{{$listdata->voter_female }}" data-vother="{{$listdata->voter_other }}" data-vtotal="{{$listdata->voter_total }}" data-psname="{{$listdata->PS_NAME_EN }}" data-psno="{{$listdata->PS_NO }}">Edit</button></td>
         
          </tr>
       
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="5">No Data Found For Election Nomination Data</td>                 
              </tr>
          @endforelse        
       </tbody></table>

         </div><!-- End Of  table responsive -->  
      </div><!-- End Of intra-table Div -->   
        
         
      </div><!-- End Of random-area Div -->
      
    </div><!-- End OF page-contant Div -->
    </div>      
  </div><!-- End Of parent-wrap Div -->
  </div> 


<script type="text/javascript">

//STATE ONCHAGE FUNCTION STARTS
    function get_pc_list() {
        
        var id = jQuery('#state').val();
        jQuery.ajax({
             url: APP_URL +'/get_pc_list',
             type: 'get',
             data: {'id' : id, '_token': '{{csrf_token()}}'},
             success: function(data){   
                jQuery('#pc_id').html(data);
                return false;
                    
            }
        });
        
    }
//STATE ONCHANGE FUNCTION ENDS   

//DISTRICT ONCHAGE FUNCTION STARTS
    function get_ac_list() {
        
        var state = jQuery('#state').val();
        var pc_id = jQuery('#pc_id').val();
        jQuery.ajax({
             url: APP_URL+"/get_assembly/"+state+'/'+pc_id,
             type: 'GET',
             data:  "state="+state+"pc_id="+pc_id,
             success: function(data){   
                jQuery('#ac_id').html(data);
                return false;
                    
            }
        });
        
    }
//DISTRICT ONCHANGE FUNCTION ENDS 
</script>
@endsection