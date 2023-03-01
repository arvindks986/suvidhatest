<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title><?php echo $heading_title; ?></title>
  <style type="text/css">
    @page  {
      header: page-header;
      footer: page-footer;
    }

    .table-strip {
      border-collapse: collapse;
    }

    .table-strip th,
    .table-strip td {
      text-align: center;
    }

    .header_section {
      height: 300px !important;
      width: 100%;
      float: left;
    }

    .table-strip {
      border-collapse: collapse;
    }

    .table-strip th,
    .table-strip td {
      text-align: center;
    }
  </style>
</head>

<body>
  <?php
  if ($caddata->cand_name == $caddata->nomination_submittedby) {
    $applied_by = '(candidate)';
  } else {
    $applied_by = '(proposer)';
  }
  ?>
  <htmlpageheader name="page-header">
    <div class="header_section">
      <p align="right" class="text-right"> <small style="font-size:10px;"> Encore Audit Ref.:- <?php echo $ref_no; ?> </small></p>
      <!--HEADER STARTS HERE-->
      <table style="width:100%;" border="0" align="center" cellpadding="5">
        <thead>
          <tr>
            <th style="width:100%; font-size:27px;" align="center" colspan="2"><img src="<?php echo e(public_path('/theme/img/logo/eci-logo.png')); ?>" alt="" width="100" border="0" /></th>
          </tr>
          <tr>
            <th style="width:100%; font-size:27px;" align="center" colspan="2">
              PART VI
            </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="width: 100%; text-align: center;font-size:14px;" colspan="2">
              <strong>Receipt for Nomination Paper and Notice of Scrutiny </strong>
              <br />
              (To be handed over to the person presenting the Nomination Paper)
            </td>
          </tr>
          <tr>
            <td style="width: 100%; text-align: center;font-size:12px;" colspan="2"> State: <b><?php echo $st_name; ?></b> Parliament Constituency: <b><?php echo e($ac_no); ?>-<?php echo e($ac_name); ?></b></td>
          </tr>
        </tbody>
      </table>

      <!--HEADER ENDS HERE-->

    </div>
  </htmlpageheader>



  <table class="table-strip" style="width: 100%;" border="0" align="center" cellpadding="10">
    <tr>
      <td style="width: 100%; text-align:justify;font-size:12px; line-height:30px;">
        Serial No. of nomination paper <b><u><?php echo e($caddata->nomination_papersrno); ?> </u></b>
      </td>
    </tr>
    <tr>
      <td style="width: 100%; text-align:justify;font-size:12px; line-height:30px;">
        The nomination paper of <strong><u><?php echo e(strtoupper($caddata->cand_name)); ?> </u></strong> a candidate for election from the

        <?php if(!empty($ac)): ?> <strong> <strong><u><?php echo e(strtoupper($ac->PC_NAME)); ?></u> </strong> </strong> Parliament constituency <?php endif; ?>
        was delivered to me at my office at <strong><u><?php echo e($caddata->rosubmit_time); ?></u> </strong> (hour) on <strong><u><?php echo e(date("d-m-Y",strtotime($caddata->rosubmit_date))); ?> </u></strong> (date) by the <strong><u><?php echo e($caddata->nomination_submittedby); ?></u></strong> <?php echo e($applied_by); ?>. All nomination papers will be taken up for scrutiny at <strong><u><?php echo e($caddata->scrutiny_time); ?> </u></strong> (hour) on <strong><u><?php echo e(date("d-m-Y",strtotime($caddata->scrutiny_date))); ?> </u></strong> (date) at <strong><u><?php echo e(strtoupper($caddata->place)); ?></u> </strong> Place.
      </td>

    </tr>
  </table>


  <table style="width: 100%; margin-bottom: 5px; margin-top:30px;">
    <tr>
      <td>Date:- <b><?php echo e($caddata->fdate); ?></b></td>
      <td align="right">Returning Officer <br><br> <b><?php echo e($ac_no); ?>-<?php echo e($ac_name); ?></b></td>
    </tr>
  </table>


</body>

</html><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/admin/candform/downloadprintreceipt.blade.php ENDPATH**/ ?>