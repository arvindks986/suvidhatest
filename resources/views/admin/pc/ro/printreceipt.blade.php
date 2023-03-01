@extends('admin.layouts.theme')
@section('content') 
       
<link href="{{ asset('admintheme/main.css') }}" rel="stylesheet">
<div class="container-fluid">
  <!-- Start Parent Wrap div -->  
    <div class="parent-wrap">
    <!-- Start Child Area Div --> 
    <div class="child-area">
     <!-- Start Page Content Div -->  
    <div class="page-contant">
      <div class="head-title">
        <h3><i><img src="{{ asset('admintheme/images/icons/tab-icon-010.png')}}" /></i>Print Receipt</h3>
      </div>
                <ul class="steps" id="progressbar">
                  <li class="step">QR SCAN</li>
                  <li class="step">Verify Nomination </li>
                  <li class="step">Decision by RO</li>
                  <li class="step">Final Receipt</li>
                  <li class="step">Finalize</li>
                  <li class="step active">Print Receipt</li>
                </ul>
    <div class="row">
     <div class="nomination-fieldset">
            <div id="printTable">
                  <link href="{{ asset('admintheme/main.css') }}" rel="stylesheet">
             <div class="nomination-form-heading">
                <img src="{{ asset('admintheme/images/logo/foot-logo-eci.png') }}"><br/>
                <br/><br/>
                  <strong>PART VI </strong><br/><br/>
                <strong>Receipt for Nomination Paper and Notice of Scrutiny </strong>
                <br/>
                  (To be handed over to the person presenting the Nomination Paper) 
                </div>
                  
                <div class="nomination-detail">
                Serial No. of nomination paper &nbsp;&nbsp;&nbsp; <strong>{{$nomination->nomination_papersrno}} </strong> &nbsp;&nbsp;&nbsp; The nomination paper of &nbsp;&nbsp;&nbsp; <strong>{{strtoupper($caddata->cand_name)}} </strong> &nbsp;&nbsp;&nbsp;   a candidate for election from the 
                @if(!empty($pc))
                  &nbsp;&nbsp;&nbsp; <strong> {{strtoupper($pc->PC_NAME)}} </strong> &nbsp;&nbsp;&nbsp;  Parliamentary constituency  @endif  
                @if(!empty($ac)) &nbsp;&nbsp;&nbsp; <strong> &nbsp;&nbsp;&nbsp; <strong>{{strtoupper($ac->AC_NAME) }} </strong> &nbsp;&nbsp;&nbsp;  </strong> &nbsp;&nbsp;&nbsp; Assembly constituency @endif 

                 

                was delivered to me at my office  at &nbsp;&nbsp;&nbsp; <strong>{{$nomination->rosubmit_time}} </strong> &nbsp;&nbsp;&nbsp; (hour) on &nbsp;&nbsp;&nbsp; <strong>{{$nomination->rosubmit_date}} </strong> &nbsp;&nbsp;&nbsp; (date) by the *candidate/proposer. All nomination papers will be taken up for scrutiny at &nbsp;&nbsp;&nbsp; <strong>{{$nomination->scrutiny_time}} </strong> &nbsp;&nbsp;&nbsp;  (hour) on &nbsp;&nbsp;&nbsp; <strong>{{$nomination->scrutiny_date}} </strong> &nbsp;&nbsp;&nbsp; (date) at &nbsp;&nbsp;&nbsp; <strong>{{strtoupper($nomination->place)}} </strong> &nbsp;&nbsp;&nbsp; Place.  
                </div><!--Nomination Details-->
                <div class="nomination-signature" style="mirgin-top:100px;">
                  <span class="nomination-date left">Date: &nbsp;&nbsp;&nbsp; <strong>{{$nomination->fdate}} </strong> &nbsp;&nbsp;&nbsp;</span>
                  <span class="nomination-sign right">Returning Officer </span>
                </div><!--Nomination Signature-->
                <!--<div class="score">*Score out the word not applicable.</div>-->
                </div> <!--  printTable-->
                 <div class="btns-actn">
                      <input type="submit" value="Print" onclick="printContent('printTable')"  target="_blank"> 
                      <!--<input type="submit" value="Close">-->
                    </div>
              </div><!--Nomination Parts-->
            </div><!--Row-->
 
         
      </div>
     
  
     </div><!-- End Of nw-crte-usr Div -->
    </div> <!-- End Of child-area Div -->     
  </div><!-- End Of parent-wrap Div -->
    
@endsection

<script type="text/javascript">
<!--
function printContent(id){
str=document.getElementById(id).innerHTML
newwin=window.open('','printwin','left=100,top=100,width=2480,height=3508')
newwin.document.write('<HTML>\n<HEAD>\n')
newwin.document.write('<TITLE>Print Receipt</TITLE>\n')
newwin.document.write('<script>\n')
newwin.document.write('function chkstate(){\n')
newwin.document.write('if(document.readyState=="complete"){\n')
newwin.document.write('window.close()\n')
newwin.document.write('}\n')
newwin.document.write('else{\n')
newwin.document.write('setTimeout("chkstate()",2000)\n')
newwin.document.write('}\n')
newwin.document.write('}\n')
newwin.document.write('function print_win(){\n')
newwin.document.write('window.print();\n')
newwin.document.write('chkstate();\n')
newwin.document.write('}\n')
newwin.document.write('<\/script>\n')
newwin.document.write('</HEAD>\n')
newwin.document.write('<BODY onload="print_win()">\n')
newwin.document.write(str)
newwin.document.write('</BODY>\n')
newwin.document.write('</HTML>\n')
newwin.document.close()
}
//-->
</script>