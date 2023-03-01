@extends('admin.layouts.pc.theme')
@section('title', 'List Candidate')
@section('content') 
<style>
    .form-check-input{
     -ms-transform: scale(2); /* IE */
 -moz-transform: scale(2); /* FF */
 -webkit-transform: scale(2); /* Safari and Chrome */
 -o-transform: scale(2); /* Opera */
  padding: 10px;
    }
 .statistics div[class*=col-] .card {
    padding: 20px 7px;
 
    }
    .card.income b {
    font-size: 16px;
}

</style>

<main role="main" class="inner cover mb-3 mb-auto">
  <section class="statistics">
        <div class="container-fluid mt-5 mb-5">
          <div class="row d-flex">
	<div class="col-lg-3">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Authority Type</b> &nbsp; <div class="btn-group float-right">
		  <a type="button" href="{{url('/pcceo/viewauthority')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/pcceo/addauthority')}}" class="btn btn-sm btn-primary">Add</a>
				  </div>				</div>
              </div>
            </div>
          <div class="col-lg-3">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Permission</b> &nbsp; <div class="btn-group float-right">
<!--					<button type="button" class="btn btn-sm btn-outline-primary">View</button>-->
                  <a type="button" href="{{url('/pcceo/viewpermsn')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/pcceo/addpermission')}}" class="btn btn-sm btn-primary">Add</a>
				  </div></div>
              </div>
            </div> 
              <div class="col-lg-3">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Authority</b> &nbsp; <div class="btn-group float-right">
		  <a type="button" href="{{url('/pcceo/viewnodals')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/pcceo/addnodals')}}" class="btn btn-sm btn-primary">Add</a>
				  </div>				</div>
              </div>
            </div>
              
               <div class="col-lg-3">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Permission Date Restriction</b> &nbsp; <div class="btn-group float-right">
<!--					<button type="button" class="btn btn-sm btn-outline-primary">View</button>-->
                  <a type="button" href="{{url('/pcceo/EditRestriction')}}" class="btn btn-sm btn-outline-primary">View</a>
<!--                  <a type="button" href="{{url('/pcceo/addpermission')}}" class="btn btn-sm btn-primary">Update</a>-->
				  </div></div>
              </div>
            </div> 
		
          </div>
        </div>
      </section>
    @if (session('chckmessage'))
    <div class="alert alert-danger">
        {{ session('chckmessage') }}
    </div>
    @endif
    <section class="mt-5" id="wrapper">
<div class="container">
<div class="row">
<div class="col-lg-12 p-0">
<div class="sidebar__inner">
              <div class="card"><!--  style="max-width:700px; margin:0 auto;" -->
                <div class="card-header d-flex align-items-center">
                  <h2>Edit Permission Date Restriction</h2>
                </div>
                   @if (Session::has('message'))
                    <div class="alert alert-success">
                        {{ session()->get('message') }}
                    </div>
                   @endif
             <div class="card-body getpermission">
			
			 
			 
                      <form class="form-horizontal" method="POST" action="{{url('/pcceo/updatedaterestriction')}}">
                          {{csrf_field()}}
                       		
			<div class="form-group row">
                          <label class="col-sm-4 form-control-label">Check if you want to enable date restriction in online mode <sup>*</sup></label>
                          @if(!empty($restrictdata))
                          <div class="form-check">
                              @if($restrictdata->restriction_status == 1)
                              <input class="form-check-input" type="checkbox" name="daterestriction" value="1" id="inlineFormCheck" checked="checked">
                                @else
                                <input class="form-check-input" type="checkbox" name="daterestriction" value="1" id="inlineFormCheck">
                                @endif
                           </div>
                          @endif
                        </div>	  
                      
                    </div>
					<div class="card-footer">
						     <div class="form-group row">
                         
                          <div class="col">
                           <button class="btn btn-success float-right" name="submit" value="ADD">Update</button>
                          </div>
                        </div>
					</div>
                   </form>
					
              </div>
           </div>
            </div>  
            </div>
</div>

</section>
</main>
@endsection
