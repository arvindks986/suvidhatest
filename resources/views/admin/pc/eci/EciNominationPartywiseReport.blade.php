@extends('admin.layouts.pc.report-theme')
@section('content')

<style type="text/css">
      th, td { white-space: normal;}
        .dataTables_wrapper .row:nth-child(2) .col-sm-12 { overflow: scroll;}
        
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
   <!--FILTER STARTS FROM HERE-->
 <div class=" card-header">
      <div class=" row">
            <div class="col">
              <form method="post" action="{{url('/eci/Ecipartywise-report')}}" id="EciNominationType">
                 {{ csrf_field() }}
                 <!--Nomination Type DROPDOWN STARTS-->
                 <input type="hidden" name="partid" value="">
                  <select name="NominationType" id="NominationType">
                   <!-- <option value="">Select Nomination Type</option>-->
                    <option value="All" >All Nomination</option>
                    <option value="validly">Validly Nomination</option>
                  </select>
                   <!--Nomination Type DROPDOWN ENDS-->
                   
                   <!--STATE LIST DROPDOWN STARTS-->
                  <select name="state" id="state" onChange="getschedule(this.value);" >
                      <option value="All">All States</option>
                     @php $statelist = getallstate(); @endphp

                      @foreach ($statelist as $state_List ))

                        @if (old('state') == $state_List->ST_CODE)
                              <option value="{{ $state_List->ST_CODE }}" selected>{{$state_List->ST_NAME}}</option>
                        @else
                              <option value="{{ $state_List->ST_CODE }}">{{$state_List->ST_NAME}}</option>
                        @endif
                      
                      @endforeach

                      @if ($errors->has('state'))
                      <span class="help-block">
                          <strong class="user">{{ $errors->first('state') }}</strong>
                      </span>
                      @endif
                  
                  </select>
                 <!--STATE LIST DROPDOWN ENDS-->
                  <!--PHASE LIST DROPDOWN STARTS-->
                  <select name="scheduleid" id="scheduleid">
                  <option value="All">All Phase</option>
                  <option value="1">Phase 1</option>
                  <option value="2">Phase 2</option>
                  <option value="3">Phase 3</option>
                  <option value="4">Phase 4</option>
                  <option value="5">Phase 5</option>
                  <option value="6">Phase 6</option>
                  <option value="7">Phase 7</option>
                  </select>
                   <!--PHASE LIST DROPDOWN ENDS-->
                  <input type="submit" value="Filter" class="btn btn-primary">
                  <!--<input type="reset" value="Reset Filter" name="Cancel" class="btn">-->
              </form>
            </div> 
            <div class="col"></div>
      </div>
</div>

 <!--FILTER ENDS HERE-->
 
<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> Party Wise Report</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">Eci</span> &nbsp;&nbsp; <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp; 
              <a href="{{url('/eci/partywise-report-pdf')}}/{{ base64_encode($st_code) }}/{{ base64_encode($nomtype) }}/{{ base64_encode($scheduleid) }}" class="btn btn-info" role="button">Export PDF</a>
              <a href="{{url('/eci/partywise-report-excel')}}/{{ base64_encode($st_code) }}/{{ base64_encode($nomtype) }}/{{ base64_encode($scheduleid) }}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
              </p>
              </div>
            </div>
      </div>
   
 <div class="card-body">  
 <div class="table-responsive">
      <table id="list-table2" class="table table-striped table-bordered table-hover" style="width:100%">  
         <thead>
         <tr>
          <th>S.No</th>
           <th>Party Abbreviation</th>
           <th>Party name</th> 
           <th>Party type</th> 
           <th>Total Nominations applied</th> 
           <th>Total Validly Nominated Candidates</th> 
        </tr>
        </thead>
        <tbody>
        <?php 
            $j=0;  
            $total_applied=0;
            $total_accepted=0;
            $total_rejected=0;
            $total_withdrowl=0;
            $total_validnom=0;
          if(count($EciNominationReport)>0){
            ?>
            @foreach($EciNominationReport as $partywiseDetailList) 

          <?php
         $pcDetails=getpcbypcno($partywiseDetailList->st_code,$partywiseDetailList->pc_no); 
         //$acDetails =getacbyacno($partywiseDetailList->st_code,$officerDetailsList->ac_no);
         $st=getstatebystatecode($partywiseDetailList->st_code);
        // $partyDetails=getById('m_party','CCODE',$partywiseDetailList->party_id);
         $j++; 
    //dd($partywiseDetailList);  
    $totapplied=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$partywiseDetailList->st_code])->where(['application_status' =>'1'])->where('party_id', '=' ,$partywiseDetailList->party_id)->get()->count();
    $totrej=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$partywiseDetailList->st_code])->where(['application_status' =>'4'])->where('party_id', '=' ,$partywiseDetailList->party_id)->get()->count();
    $totalwith= \app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$partywiseDetailList->st_code])->where(['application_status' =>'5'])->where('party_id', '=' ,$partywiseDetailList->party_id)->get()->count() ;
    $totaccepted=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$partywiseDetailList->st_code])->where(['application_status' =>'6'])->where('party_id', '=' ,$partywiseDetailList->party_id)->get()->count();
    $total=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$partywiseDetailList->st_code])->where('party_id', '!=' ,'1180')->where('application_status','!=','11')->get()->count();
    $totalvelidcand=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$partywiseDetailList->st_code])->where(['application_status' =>'6'])->where('party_id', '=' ,$partywiseDetailList->party_id)->where(['finalaccepted'=>1])->get()->count();

    if($partywiseDetailList->PARTYTYPE=='N') { $parttype='National Party';} 
      elseif($partywiseDetailList->PARTYTYPE=='S'){ $parttype='State Party';} 
      elseif($partywiseDetailList->PARTYTYPE=='Z'){ $parttype='Independent Party';} 
      elseif($partywiseDetailList->PARTYTYPE=='U'){ $parttype='Unrecognized Party';} 
      elseif($partywiseDetailList->PARTYTYPE=='0'){ $parttype='Unrecognized Party';}  

        ?>
          <tr>
          <td >{{ $j }}</td>
            <td >@if(isset($partywiseDetailList->PARTYABBRE)) {{$partywiseDetailList->PARTYABBRE}}@endif</td>
            <td><a target="" href="{{url('/eci/candidatelist-partywise')}}/{{base64_encode($partywiseDetailList->CCODE)}}/{{ base64_encode($st_code) }}/{{ base64_encode($nomtype) }}/{{ base64_encode($scheduleid) }}">@if(isset($partywiseDetailList->PARTYNAME)) {{$partywiseDetailList->PARTYNAME}}@endif</a></td>
            <td>@if(isset($partywiseDetailList->PARTYTYPE)) {{ $parttype }}@endif</td>
            <td >@if(isset($partywiseDetailList->totalnomination)) {{$partywiseDetailList->totalnomination}}@endif</td>
            <td >@if(isset($totalvelidcand)) {{$totalvelidcand}}@endif</td> 
            <!-- <td >@if(isset($totaccepted)) {{$totaccepted}}@endif</td>
            <td >@if(isset($totrej)) {{$totrej}}@endif</td>
            <td >@if(isset($totalwith)) {{$totalwith}}@endif</td>-->
          </tr>
            @endforeach 
          <?php } else {?>
            <tr>
            <td class="col-md-6" colspan='6'> <p>No Records  Founds </p></td>
             </tr>  
             <?php } ?>
             </tbody>
            </table>
            {{ $EciNominationReport->links() }}
           </div> <!-- end reponcive-->
          </div>
        </div>
  </div>
  </div>
  </section>
  </main>
@endsection


