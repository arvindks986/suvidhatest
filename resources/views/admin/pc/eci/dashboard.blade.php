@extends('admin.layouts.pc.dashboard-theme')
<link rel="stylesheet" href="{{ asset('/theme/css/dashboard.css') }}">
@section('content')
@section('title', 'ECI ')
@section('bradcome', 'DASHBOARD')
<main>
  <style>
    span.non-num,
    span.num {
      padding: 0;
      min-width: auto;
      min-height: auto;
      line-height: 34px;
      font-size: 1rem;
    }

    .head-title {
      color: #d34c89;
      border-bottom: 1px dashed #d34c89;
      padding: 0.85rem 0;
      border-top: 1px dashed #d34c89;
    }

    .table-td-nowrap td {
      white-space: nowrap;
    }
  </style>
  <section class="statistics color-grey" style="border-bottom:1px solid #eee;">
    <div class="container-fluid pt-2">
      <table id="" class="table table-striped table-bordered table-hover table-td-nowrap" style="width:100%">
        <thead>
          <tr>
            <th>Serial No</th>
            <th>Poll Events (Phase)</th>
            <th>State</th>
            <th>Total PCs in Phase</th>
            <th>Date of Issue of Gazette Notification</th>
            <th>Last Date For Making Nominations</th>
            <th>Date for Scrutiny of Nominations</th>
            <th>Last Date For Withdrawl of Candidature</th>
            <th>Date Of Poll</th>
            <th>Date Of Counting</th>
            <th>Date Of Completion</th>
          </tr>
        </thead>
        <tbody>
          @php $count = 1; @endphp
          @forelse ($results as $result)
          <tr>
            <td>{{ $count }}</td>
            <td><a style="color:#000000">Phase - {{$result['sid'] }}</a></td>

            <td><a style="color:#000000">{!! $result['label'] !!}</a></td>

            <td><a style="color:#000000">{{$result['acs'] }}</a></td>

            <td><a style="<?php echo $result['start_nomi_class'] ?>">{{GetReadableDateFormat($result['start_nomi_date']) }}</a></td>

            <td><a style="<?php echo $result['last_nomi_class'] ?>">{{GetReadableDateFormat($result['last_nomi_date']) }}</a></td>


            <td><a style="<?php echo $result['nomi_scr_class'] ?>">{{GetReadableDateFormat($result['dt_nomi_scr']) }}</a></td>

            <td><a style="<?php echo $result['last_wid_class'] ?>">{{GetReadableDateFormat($result['last_wid_date']) }}</a></td>

            <td><a style="<?php echo $result['poll_date_class'] ?>">{{GetReadableDateFormat($result['poll_date']) }}</a></td>

            <td><a style="<?php echo $result['count_date_class'] ?>">{{GetReadableDateFormat($result['count_date']) }}</a></td>

            <td><a style="<?php echo $result['complete_date_class'] ?>">{{GetReadableDateFormat($result['complete_date']) }}</a></td>

          </tr>
          @php $count++; @endphp
          @empty
          <tr>
            <td colspan="4">No Data Found For Election Schedule</td>
          </tr>
          @endforelse
        </tbody>
      </table>


  </section>
</main>


@endsection