<?php if(isset($form_filters) && count($form_filters)>0){ ?>
<section class="color-white statistics dashboard p-2" style="border-bottom:1px solid #eee;">
  <div class="container-fluid">
    <form id="generate_report_id"  class="row" method="get" onsubmit="return false;">
      @foreach($form_filters as $iterate_filter)
      <?php $id = $iterate_filter['id']; ?>
      <div class="form-group col-md-2"> <label>{{$iterate_filter['name']}}</label> 
       <select name="{{$iterate_filter['id']}}" id="{{$iterate_filter['id']}}" class="form-control filter-form" onchange ="filter('<?php echo $id; ?>')">
            <option value="">Select {{$iterate_filter['name']}}</option>
            @foreach($iterate_filter['results'] as $result)
              @if($result['active'])
                <option value="{{$result['id']}}" selected="selected">{{$result['name']}}</option> 
              @else 
                <option value="{{$result['id']}}" >{{$result['name']}}</option> 
              @endif  
            @endforeach
        
            </select>
          </div>
      @endforeach
     </form>   
  </div>
</section>
<script type="text/javascript">
  function filter(type_code){
    if(type_code == 'st_code'){
      if($('#ac_no').length>0){
        $('#ac_no').val('');
      }
      if($('#ps_no').length>0){
        $('#ps_no').val('');
      }
    }else if(type_code == 'ac_no'){
      if($('#ps_no').length>0){
        $('#ps_no').val('');
      }
    }
    var url = "<?php echo $filter_action ?>";
    var query = '';
    $('.filter-form').each(function(index,object){
      if($(object).val().trim() != ''){
        query += "&"+$(object).attr('id')+"="+$(object).val();
      }
    });
    window.location.href = url+'?'+query.substring(1);
  }
</script>
<?php } ?>