<style type="text/css">
    .row.mis_gap {
        padding: 10px;
        border: 1px solid #6666;
    }
</style>
<?php 
  
 
$pc_no=!empty($profileData[0]) ? $profileData[0]->pc_no:'';
$st_code=!empty($profileData[0]) ? $profileData[0]->st_code:'';
$party_id=!empty($profileData[0]) ? $profileData[0]->party_id:'';
 
 $candiatePcName = getpcbypcno($st_code, $pc_no);       
 $candiatePcName =  !empty($candiatePcName)? $candiatePcName->PC_NAME:'---';
 $stateName= getstatebystatecode($st_code); 
 $stateName =  !empty($stateName)? $stateName->ST_NAME:'---';  
 $partyname = getpartybyid($party_id);
 $partyname =  !empty($partyname)? $partyname->PARTYNAME:'---';  
  
?>
<div class=" text-left" style="width:100%;">
    <!--SELECT CANDIDATE-->
 
    <div  class="collapse show">  
        <table   class="table table-striped table-bordered table-hover" style="width:100%">
            <tbody>
                @if(count($profileData)>0)
                <tr>
                    <td>Name</td>
                    <td>{{!empty($profileData[0]) ? $profileData[0]->cand_name:'--'}}</td>
                </tr>
                <tr>
                    <td>Phone</td>
                    <td>{{!empty($profileData[0]) ? $profileData[0]->cand_mobile:'--'}}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>{{!empty($profileData[0]) ? $profileData[0]->cand_email:'--'}} </td>
                </tr>
                <tr>
                    <td>PC Name</td>
                    <td>{{$candiatePcName}}</td>
                </tr>
                <tr>
                    <td>Party Name</td>
                    <td>{{$partyname}}</td>
                </tr>
                <tr>
                    <td>Residence Address</td>
                    <td>{{!empty($profileData[0]) ? $profileData[0]->candidate_residence_address:'--'}}</td>
                </tr>
                <tr>
                    <td>Election Type</td>
                    <td>{{!empty($profileData[0]) ? $profileData[0]->ELECTION_TYPE:'--'}}</td>
                </tr>                
                <tr>
                    <td>State</td>
                    <td>{{$stateName}}</td>
                </tr>
                
                @else
                <tr>
                    <td colspan="2">No Record Available</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>


</div>
