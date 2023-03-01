<?php //echo "<pre>"; print_r($data); echo "</pre>"; //die;?>
<!DOCTYPE html>
<html>
<head>
    <title></title>

    <link href="assets/css/icons.css" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
    <link href="/assets/toastr/css/toastr.min.css" rel="stylesheet">
</head>

<body style="background: #ffffff;">
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2" style="margin-top: 50px;">
                <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">Select Election</h3>
          </div>
          <div class="panel-body">
            <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Select Election</th>
                    <th>State Name</th>
                    <th>Constituency Type</th>
                    <th>Month</th>
                    <th>Year</th>
                    <th>Election Type</th>
                </tr>
            </thead>
            <tbody>
                <form method="POST" action="javascript:void(0);" name="election_selection">
                    @csrf
                @foreach($data as $key => $election_detail)
                <tr>
                    <td class="dev">
                        <input type="radio" name="selection" value="{{$key}}">
                        
                    </td>
                    <td>
                        {{ $election_detail['st_name'] }}
                        <input type="hidden" name="st_code" value="{{ $election_detail['st_code'] }}">
                        <input type="hidden" name="st_name" value="{{ $election_detail['st_name'] }}">
                        <input type="hidden" name="ScheduleID" value="{{ $election_detail['ScheduleID'] }}">
                    </td>
                    <td>
                        {{ $election_detail['constituency_type'] }}
                        <input type="hidden" name="constituency_type" value="{{ $election_detail['constituency_type'] }}">
                    </td>

                    <td>
                        {{ $election_detail['month'] }}
                        <input type="hidden" name="constituency_type" value="{{ $election_detail['constituency_type'] }}">
                    </td>
                    <td>
                        {{ $election_detail['year'] }}
                        <input type="hidden" name="year" value="{{ $election_detail['year'] }}">
                    </td>
                    <td>
                        {{ $election_detail['election_type'] }} 
                        <input type="hidden" name="election_type" value="{{ $election_detail['election_type'] }}">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
          </div>
          <div class="panel-footer" style="height: 50px !important; width: 100%;">
            <div class=" pull-right">
                <div class="form-group">
                    <div class="button-group">
                        <button href="javascript:void(0)" type="submit" class="btn btn-primary" id="submit">Select</button>
                        <a href="{{ route('signout') }}" class="btn btn-primary">Exit</a>
                    </div>
                </div>
            </div>
            </form>
          </div>
        </div>
        </div>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/toastr/js/toastr.min.js"></script>
    <script type="text/javascript">
        $(document).on('click','#submit',function(){
            var selectedId = $('input[name="selection"]:checked').val();
            // console.log(selectedId)
            if(selectedId == undefined){
                $('form[name="election_selection"]').removeAttr('action');
                toastr.error('No Election Selected!', 'Error', {timeOut: 5000});
                return false;
            }else{
                $('form[name="election_selection"]').attr('action','createSession');
                $('form[name="election_selection"]').submit();
            }
        })
    </script>
</body>

</html>