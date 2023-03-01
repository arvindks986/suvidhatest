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
			.table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
          .header_section{
          	height: 500px !important;
          	width: 100%;
          	float: left;
          }
          .small{
          	display: none;
          }
  		</style>
    </head>
    <body>

<htmlpageheader name="page-header" >
	<div class="header_section">

         <!--HEADER STARTS HERE-->
			<p align="center" class="text-center"> <span style="font-size:20px;font-weight:bold;color:blue;"> (Preview)</span></p>
            <table style="width:100%;padding: 5px 0;" border="0" align="center" cellpadding="5">
               <thead>
                <tr>
                   <th style="width:100%; font-size: 30px;" align="center" >
                        Election Commission of India 
                    </th>
                </tr>
                <tr>
                  <th  style="width: 100%; text-align: center;font-weight: bold;font-size: 20px;">
                    Round Declaration Form
                  </th>
                </tr>
              </thead>
            </table>
        <!--HEADER ENDS HERE-->

         <table style="width:100%;" border="0" align="center">  
          
                <tr>
                 <td  style="width:100%;">
                    <table  style="width:100%;padding: 15px 0;">
                      <tbody>
                          <tr>
                          <td>
                            <br>
                            State: <b>{!! $st_name !!}</b><br>
                            
                            <br>
                          </td>
                          <td align="right">Date <u>{{ date('d-M-Y h:i a') }}</u></td>
                        </tr>
                        <tr>
                          <td>Election: <b>{!! $election !!}</b></td>
                          <td align="right">
                              Round Number <u style="min-width: 250px;">{!! $round !!}</u>
                              </td>

                        </tr>
                        

                          <?php if(isset($ac_no)){ ?>
                            <tr> 
                                <td>Number & Name of the PC - <u style="min-width: 250px;">{!! $pc_no !!}-{!! $pc_name !!}</u> 
                             </td>

                              </tr> 
                                <tr>
                             <td>
                            Number & Name of the AC- <u style="min-width: 250px;"> {!! $ac_no !!}-{!! $ac_name !!}</u>
                            </td>
                          </tr>
                          
                          <?php }else{ ?>
                            <tr> 
                                <td>Number & Name of the AC <u style="min-width: 250px;">{!! $pc_no !!}-{!! $pc_name !!}</u> 
                              </td>
                            </tr>
                           
                           
                          <?php } ?>

                           
                       
                      </tbody>
                    </table>  
                 </td>

               </tr>

              
              
            </table> 
  
       
        </div>
</htmlpageheader>

<div class="body_section">

                      
        <table class="table-strip" style="width: 100%;" border="1" align="center" cellpadding="5">
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
</div>


	<div class="footer_section">
      <table style="width:100%; border-collapse: collapse;" align="center" border="0" cellpadding="15">
          <tbody>
            <tr>
              <td align="left" colspan="2"><br>RO/ARO_________________________<br>  <br>  <br>               Observer___________________________
                <br>  
              </td>


            </tr>
          </tbody>
      </table>
  </div>


    </body>
</html>
