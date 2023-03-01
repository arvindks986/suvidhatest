<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>{!! $heading_title !!}</title>
        <style type="text/css">
        @page {
        header: page-header;
        footer: page-footer;
      }
      body, p, td, div { font-family: freesans; }
      .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          
          .header_section{
            height:300px !important;
            width: 100%;
            float: left;
          }
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
           
      </style>
    </head>
    <body>
      <?php
        if($caddata->cand_name == $caddata->nomination_submittedby){
          $applied_by = '(उम्मीदवार)';
        }else {
          $applied_by = '(प्रस्तावक)';
        }  
      ?>
       <htmlpageheader name="page-header" >
  <div class="header_section">
        <p align="right" class="text-right"> <small style="font-size:10px;"> एनकॉर ऑडिट रेफ.:-  {!!$ref_no!!} </small></p>
         <!--HEADER STARTS HERE-->
            <table style="width:100%;" border="0" align="center" cellpadding="5">
               <thead>
                <tr>
                <th style="width:100%; font-size:27px;" align="center" colspan="2" ><img src="{{ public_path('/theme/img/logo/eci-logo.png') }}" alt=""  width="100" border="0"/></th>
                     
                </tr>
                <tr> <th style="width:100%; font-size:27px;" align="center" colspan="2" >
                        (भाग VI) 
                    </th>
                </tr>
                <tr>
                  <th  style="width: 100%; text-align: center;font-weight: bold;font-size:14px;" colspan="2">
                       <strong>नामांकन पत्र और संवीक्षा की सूचना के लिए रसीद</strong>
                       <br/>
                      (नामांकन पत्र प्रस्तुत करने वाले व्यक्ति को सौंपा जाना) 
                  </th>
                </tr>
                 <tr>
                   <th  style="width: 100%; text-align: center;font-weight: bold;font-size:12px;" colspan="2"> राज्य: <b>{!! $st_name !!}</b> 
                    संसद क्षेत्र: {{$pc_no}}-{{$pc_name}}</td>
                </tr>
              </thead>
            </table>
   
        <!--HEADER ENDS HERE-->
       
        </div>
</htmlpageheader>   


 
    <table class="table-strip" style="width: 100%;" border="0" align="center" cellpadding="10">
      <tr>
        <th  style="width: 100%; text-align:justify;font-size:12px; line-height:30px;">
         
            नामांकन पत्र की क्रम संख्या <b><u>{{$caddata->nomination_papersrno}} </u></b> का नामांकन पत्र <strong><u>{{strtoupper($caddata->cand_name)}} </u></strong>   से चुनाव के लिए एक उम्मीदवार 
                 
                @if(!empty($ac)) <strong> <strong><u>{{strtoupper($ac->AC_NAME) }}</u> </strong>  </strong> 
                विधानसभा क्षेत्र @endif
                
            मेरे कार्यालय में मुझे दिया गया था <strong><u>{{$caddata->rosubmit_time}}</u> </strong> (घंटा) पर <strong><u>{{date("d-m-Y",strtotime($caddata->rosubmit_date))}} </u></strong> (तारीख) द्वारा <strong><u>{{ $caddata->nomination_submittedby }}</u></strong> {{$applied_by}}. सभी नामांकन पत्रों की जांच के लिए लिया जाएगा <strong><u>{{$caddata->scrutiny_time}} </u></strong>  (घंटा) पर <strong><u>{{date("d-m-Y",strtotime($caddata->scrutiny_date))}} </u></strong> (तारीख) पर <strong><u>{{strtoupper($caddata->place)}}</u> </strong> जगह.  
                 
          </td></tr>
    </table>
     

 <table style="width: 100%; margin-bottom: 5px; margin-top:30px;">
     <tr>   <td>तारीख:- {{$caddata->fdate}}</td> 
            <td align="right">
                रिटर्निंग ऑफिसर <br><br> {{$pc_no}}-{{$pc_name}}</td>
    </tr>
</table>


    </body>
</html>