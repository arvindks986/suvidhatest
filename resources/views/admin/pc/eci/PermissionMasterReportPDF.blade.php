<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Permission Report</title>
       
    </head>
    <body>
         <!--HEADER STARTS HERE-->
            <table style="width:100%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/></th>
                    <th  style="width:50%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
                        SECRETARIAT OF THE<br>
                        ELECTION COMMISSION OF INDIA<br>
                        Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>  
                    </th>
                </tr>
              </thead>
            </table>
        <!--HEADER ENDS HERE-->
      <style type="text/css">
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
      </style>
        <table style="width:100%; border: 1px solid #000;" border="0" align="center">  
                <tr>
                 <td  style="width:50%;">
               
                 </td>
                 <td  style="width:50%">
                  <table style="width:100%">
                      <tbody>
                         <tr>
                           <td align="right"><strong>Date of Print:</strong>{{ date('d-M-Y h:i a') }}</td>
                         </tr>
                     
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>
            </table>
 
		<table class="table-strip" style="width: 100%;" border="1" align="center">
		<tbody>
		<tr>
		<td align="center" ><strong>Permission Report</strong></td>
		</tr>
		</tbody>
		</table>
                
        <table class="table-strip" style="width: 100%;" border="1" align="center">
           <thead>
            <tr>  
                <th>State Name</th>
                <th>Permission name</th>
                <th>Document details</th>
                <th>Permission Level</th>
                <th>Authority Type</th>
                <th>Required Status</th>
            </tr>
          </thead>
          <tbody id="oneTimetab">   
		     @if(!empty($report))
                                @php $state="";$p_id=0;$i=1; @endphp
                                @foreach($report as $key => $data)
                               <?php
                                $authname="";
                                $required_status="";
                                
                                $file_name = $data->file_name;
                                if($data->auth_name != 'undefined' && $data->auth_name != 'null')
                                {
                                     $authname = $data->auth_name.' ';
                                }
                                else
                                {
                                      $authname = "";
                                }
                                
                                if($data->canddoc_name != 'undefined' && $data->canddoc_name != '')
                                {
                                    $authname .= $data->canddoc_name;
                                }
                               if($data->required_status == '1')
                               {
                               $required_status = 'Mandatory';
                               }
                               else
                               {
                                    $required_status = 'Not Mandatory';
                               }
                               $file_name = "uploads/permission-document/" . $data->st_code . "/" . $data->file_name;
                             /* $fileserver = $data->fileserver_dir;
                              if ($fileserver == 'uploads')
                                {
                                  $file_name = "uploads/permission-document/" . $data->st_code . "/" . $data->file_name;
                              }
                              else
                              {
                                  $file_name = "/".$data->file_name;
                              }*/
                               ?>
                                <?php $state1=$data->st_code;$p_id1 = $data->permission_id;?>
                               
                                    <?php if(($state == $state1) && ($p_id == $p_id1)){?>
                                 <tr>
<!--                                    <td>{{''}}</td>-->
                                    <td>{{''}}</td>
                                    <td>{{''}}</td>
                                    <td>{{$data->doc_name}} <a href="{{asset($file_name)}}" download="">Download Format</a></td>
                                    <td>{{$data->role_name}}</td>
                                    <td>{{$authname}}</td>
                                    <td>{{$required_status}}</td>
                                </tr>
                                    <?php } else { ?>
                                 <tr>
<!--                                    <td>{{$i}}</td>-->
                                    <td>{{$data->st_code}}</td>
                                    <td>{{$data->pname}}</td>
                                    <td>{{$data->doc_name}} <a href="{{asset($file_name)}}" download="">Download Format</a></td>
                                    <td>{{$data->role_name}}</td>
                                    <td>{{$authname}}</td>
                                    <td>{{$required_status}}</td>
                                </tr>
                                
                                    <?php $i++; }?>
                                 
                                <?php $state=$data->st_code;$p_id = $data->permission_id;?>
                                
                                @endforeach
                                @endif
			  
			
              
          </tbody>
        </table>
      <table style="width:100%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
          <tbody>
            <tr>
              <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>  
            </tr>
          </tbody>
      </table>
    </body>
</html>