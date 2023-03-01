@extends('admin.layouts.pc.theme')
@section('title', 'Officer update profile')
@section('content')
 <?php  $st=getstatebystatecode($user_data->st_code); 
        
    ?>
  <style type="text/css">
      th, td { white-space: nowrap;}
        <!-- .dataTables_wrapper .row:nth-child(2) .col-sm-12 { overflow: scroll;} -->
        
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
<section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h2 class="mr-auto">Officer Details Report</h2></div> 
             <div class="col"><p class="mb-0 text-right">
              <b>State Name:</b> 
              <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
              </div>
            </div>
            </div>
<div class="card-body">  
              form enctype="multipart/form-data" id="election_form" method="POST"  action="{{url('ropc/createnomination') }}" autocomplete='off' enctype="x-www-urlencoded">
    {{ csrf_field() }}
 
  <div class="container">
  <div class="row">
  
  <div class="card text-left mt-3" style="width:100%; margin:0 auto 10px auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h4>Candidate Nomintion Details</h4></div> 
          <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">PC Name:</b> 
            <span class="badge badge-info">{{$pc->PC_NAME}}</span>&nbsp;&nbsp;  </p></div>
         
                </div>
                </div>
      <div class="row">
      @if (session('success_mes'))
          <div class="alert alert-success"> {{session('success_mes') }}</div>
        @endif
        
  </div>
    <div class="card-body">  
    <div class="row d-flex  align-items-center">
      <div class="col-md-2">
      <div class="avatar-upload">
                <div class="avatar-edit">
                  <input type='file' id="imageUpload" name="profileimg" accept=".png, .jpg, .jpeg" />
                    <label for="imageUpload"><sup>*</sup><img src="{{ asset('admintheme/img/icon/tab-icon-002.png')}}"/></label>
                </div>
            @if ($errors->has('profileimg'))
                <span style="color:red;">{{ $errors->first('profileimg') }}</span>
            @endif
            <div class="avatar-preview"><div id="imagePreview"></div>
            </div>
            <div class="profileerrormsg errormsg errorred"></div>
           </div>
      </div>
             
  <div class="col"> 
  
<div class="form-group row">

<div class="col">
<label class="">Party Name <sup>*</sup></label>
     
      <?php 
        $partyd=getallpartylist();
        $symb=getsymbollist();
        $symb1=getsymboltypelist('T');
        $newst=old('state');
        $newdist=old('district');
        $newac=old('ac');
        if($newst!='' and $newdist!='')
          {
            $all_dist=getalldistrictbystate($newst);
            $all_ac=getacbystate($newst); 
          }
         
      ?>

      <select name="party_id" class="form-control party_id">
        <option value="">-- Select Party --</option>
           
          @foreach($partyd as $Party)
          <option value="{{ $Party->CCODE }}" @if($Party->CCODE==old('party_id')) selected="selected" @endif > {{$Party->PARTYABBRE}}-{{$Party->PARTYNAME}} </option>
          @endforeach
           
      </select>
        @if ($errors->has('party'))
                        <span style="color:red;">{{ $errors->first('party') }}</span>
                    @endif
      <div class="perrormsg errormsg errorred"></div>
  
</div>
    
    <div class="col">
    <label class="">Symbol <sup>*</sup></label>
        <select name="symbol_id" class="form-control">
          <option value="">-- Select Symbol --</option>
          @foreach($symb as $symbolDetails)
          <option value="{{ $symbolDetails->SYMBOL_NO }}" @if($symbolDetails->SYMBOL_NO==old('symbol_id')) selected="selected" @endif> {{$symbolDetails->SYMBOL_NO}}-{{$symbolDetails->SYMBOL_DES}}</option>
          @endforeach
        </select>
        @if ($errors->has('symbol_id'))
                <span style="color:red;">{{ $errors->first('symbol_id') }}</span>
            @endif
        <div class="serrormsg errormsg errorred"></div>
        <div id="mysysDiv" style="display: none;"> <input type="checkbox" name="nosymb" id="nosymb" value="200" checked="checked"> Symbole Not Alloted</div>
    </div>
      
    
    
    
           
  </div><!-- end COL-->
  </div><!-- end row-->
          </div>
        </div>
        </div>
      </div>
    </div>    
    </section>
    <section>
    <div class="container p-0">
      <div class="row">
      
        <div class="col-md-12">
        <div class="card">
                <div class="card-header d-flex align-items-center">
                  <h4>Candidate Personal Details</h4>
                </div>
                <div class="card-body">
        <div class="row">
        
          <div class="col">                  
                  
                     
                    <div class="form-group row">
                      <label class="col-sm-3">Name<sup>*</sup></label>
                      <div class="col">
              {!! Form::text('name', null, ['class' => 'form-control', 'id' => 'name', 'placeholder' => 'Name in English','']) !!}
            @if ($errors->has('name'))
                    <span style="color:red;">{{ $errors->first('name') }}</span>
                  @endif 
              <div class="nameerrormsg errormsg errorred"></div>
                      </div>  
            <div class="col">
             {!! Form::text('hname', null, ['class' => 'form-control', 'id' => 'hname', 'placeholder' => 'Name in Hindi','']) !!}
             @if ($errors->has('hname'))
                    <span style="color:red;">{{ $errors->first('hname') }}</span>
                  @endif 
             <div class="nhindierrormsg errormsg errorred"></div>
                      </div>
                      <div class="col">
             {!! Form::text('cand_vname', null, ['class' => 'form-control', 'id' => 'cand_vname', 'placeholder' => 'Name in Vernacular','']) !!}
             @if ($errors->has('cand_vname'))
                    <span style="color:red;">{{ $errors->first('cand_vname') }}</span>
                  @endif 
              
                      </div>
                    </div>
        <div class="form-group row">
                      <label class="col-sm-3">Candidate Alias Name </label>
                      <div class="col">
              {!! Form::text('aliasname', null, ['class' => 'form-control', 'id' => 'aliasname', 'placeholder' => 'Alias  Name English','']) !!}
            @if ($errors->has('aliasname'))
                    <span style="color:red;">{{ $errors->first('aliasname') }}</span>
                  @endif 
               
                      </div>  
            <div class="col">
             {!! Form::text('aliashname', null, ['class' => 'form-control', 'id' => 'aliashname', 'placeholder' => 'Alias Name In Hindi','']) !!}
             @if ($errors->has('aliashname'))
                    <span style="color:red;">{{ $errors->first('aliashname') }}</span>
                  @endif 
              
                      </div>
                    </div>
          
          <div class="form-group row">
                      <label class="col-sm-3">Father's / Husband's Name <sup>*</sup></label>
                      <div class="col">
             {!! Form::text('fname', null, ['class' => 'form-control', 'id' => 'fname', 'placeholder' => 'In English','']) !!}
             @if ($errors->has('fname'))
                    <span style="color:red;">{{ $errors->first('fname') }}</span>
                  @endif 
             <div class="ferrormsg errormsg errorred"></div> 
                      </div>  
            <div class="col">
             {!! Form::text('fhname', null, ['class' => 'form-control', 'id' => 'fhname', 'placeholder' => 'In Hindi','']) !!}
             @if ($errors->has('fhname'))
                    <span style="color:red;">{{ $errors->first('fhname') }}</span>
                  @endif 
             <div class="fhindierrormsg errormsg errorred"></div>
                      </div>
                    </div>
                    <div class="form-group row">
                    <label class="col-sm-3">Category <sup>*</sup></label> 
            <div class="col"> 
              <select name="cand_category" class="form-control">
                <option value="">--Select Category--</option>
                <option value="general"  @if("general"==old('cand_category')) selected="selected" @endif>General</option>
                <option value="sc" @if("sc"==old('cand_category')) selected="selected" @endif>SC</option>
                <option value="st" @if("st"==old('cand_category')) selected="selected" @endif>ST</option>
                <option value="obc" @if("obc"==old('cand_category')) selected="selected" @endif>OBC</option>
                </select>
            @if ($errors->has('cand_category'))
                        <span style="color:red;">{{ $errors->first('cand_category') }}</span>
                    @endif
              <div class="caterrormsg errormsg errorred"></div>
             
          </div>
          <div class="col"> 

          </div> 
        </div>
          <div class="line"></div>
          
          <div class="form-group row">
            <label class="col-sm-2">Email <sup>*</sup></label>
                        <div class="col">
              {!! Form::text('email', null, ['class' => 'form-control', 'id' => 'email','']) !!}
              @if ($errors->has('email'))
                    <span style="color:red;">{{ $errors->first('email') }}</span>
                  @endif 
              <div class="eerrormsg errormsg errorred"></div>
                        </div>  
              <label class="col-sm-2">Mobile No <sup>*</sup></label>
            <div class="col">
              {!! Form::text('cand_mobile', null, ['class' => 'form-control', 'id' => 'cand_mobile','','maxlength' => 10]) !!}
          @if ($errors->has('cand_mobile'))
                    <span style="color:red;">{{ $errors->first('cand_mobile') }}</span>
                  @endif 
                 
              <div class="merrormsg errormsg errorred"></div> 
            </div>
                    </div>
          
          
          <div class="form-group row">
                      <label class="col-sm-2">Gender <sup>*</sup></label>
       
                      <div class="col">
               <div class="custom-control custom-radio">
              <input type="radio" name="gender" class="custom-control-input" id="customControlValidation2" value="female" 
              @if("female"==old('gender')) checked="checked" @endif>
              <label class="custom-control-label" for="customControlValidation2">Female</label>
              </div>
              <div class="custom-control custom-radio ">
                <input type="radio" class="custom-control-input" id="customControlValidation3" name="gender" value="male" id="radio2"@if("male"==old('gender')) checked="checked" @endif> 
              <label class="custom-control-label" for="customControlValidation3">Male</label>
               
              </div><div class="custom-control custom-radio mb-3">
              <input type="radio" class="custom-control-input" id="customControlValidation4" name="gender" value="third" @if("third"==old('gender')) checked="checked" @endif>  
              <label class="custom-control-label" for="customControlValidation4">Others</label>
              </div>
              <div class="gerrormsg errormsg errorred"></div>
              </div> 
              <label class="col-sm-2">PAN Number  </label>
              <div class="col">
                {!! Form::text('panno', null, ['class' => 'form-control', 'id' => 'panno','maxlength' => 10]) !!}
                @if ($errors->has('panno'))
                    <span style="color:red;">{{ $errors->first('panno') }}</span>
                  @endif 
                <div class="pannoerrormsg errormsg errorred"></div>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-2">Date of Birth <sup>*</sup></label>
              <div class="col">
                 <input name="dob" type="text" class="form-control" placeholder="" id='dob' value="{{old('dob')}}">
                                <span class="text-danger">{{ $errors->error->first('dob') }}</span>
                <div class="doberrormsg errormsg errorred"></div>
              </div>  
              <label class="col-sm-2">Age <sup>*</sup></label>
              <div class="col">
                {!! Form::text('age', null, ['class' => 'form-control', 'maxlength'=>'2', 'id' => 'age','']) !!}
                <div class="ageerrormsg errormsg errorred"></div>
              </div>
            </div>
                    <div class="line"></div>  
          
            <div class="form-group row">
                      <label class="col-sm-2">Address Line1<sup>*</sup></label>
                       <div class="col">
              {!! Form::text('addressline1', null, ['class' => 'form-control', 'id' => 'addressline1','placeholder'=>'In English']) !!}
            @if ($errors->has('addressline1'))
                            <span style="color:red;">{{ $errors->first('addressline1') }}</span>
                        @endif 
              <div class="addressline1errormsg errormsg errorred"></div>
                      </div>  
            <div class="col">
              {!! Form::text('addresshline1', null, ['class' => 'form-control', 'id' => 'addresshline1','placeholder'=>'In Hindi']) !!}
            @if ($errors->has('addresshline1'))
                            <span style="color:red;">{{ $errors->first('addresshline1') }}</span>
                        @endif 
              <div class="addresshline1errormsg errormsg errorred"></div>
             </div>  
                    </div>
          
          <div class="line"></div>
          
          <div class="form-group row">
                      <label class="col-sm-2">Address Line2<sup></sup></label>
                       <div class="col">
              {!! Form::text('addressline2', null, ['class' => 'form-control', 'id' => 'addressline2','placeholder'=>'In English']) !!}
              <div class="addressline2errormsg errormsg errorred"></div>
                      </div>  
            <div class="col">
              {!! Form::text('addresshline2', null, ['class' => 'form-control', 'id' => 'addresshline2','placeholder'=>'In Hindi']) !!}
              <div class="addresshline2errormsg errormsg errorred"></div>
             </div>  
                    </div>
          <div class="line"></div>
           
          <div class="form-group row">
          <div class="col-sm-2"><label for="statename">State Name <sup>*</sup></label></div>
          <div class="col"><div class="" style="width:100%;">
            <select name="state" class="form-control" >
              <option value="">-- Select States --</option>
               @if(isset($all_state)) @foreach($all_state as $st)
                <option value="{{ $st->ST_CODE }}" @if($st->ST_CODE==old('state')) selected="selected" @endif > {{ $st->ST_NAME }}</option>
              @endforeach
              @endif
             </select>
              @if ($errors->has('state'))
                        <span style="color:red;">{{ $errors->first('state') }}</span>
                    @endif 
            <div class="stateerrormsg errormsg errorred"></div>         </div>
          </div>  
          <div class="col-sm-2"><label for="statename">District <sup>*</sup></label></div>
          <div class="col"><div class="" style="width:100%;">
            <select name="district" class="form-control" >
              <option value="">-- Select Ditricts --</option>
               
              @foreach($all_dist as $district)
                <option value="{{$district->DIST_NO}}" @if($district->DIST_NO==old('district')) selected="selected" @endif > 
                  {{$district->DIST_NO}} - {{$district->DIST_NAME }} - {{$district->DIST_NAME_HI}}
                </option>
              @endforeach  
               
            </select>
            @if ($errors->has('district'))
                        <span style="color:red;">{{ $errors->first('district') }}</span>
                    @endif 
            <div class="districterrormsg errormsg errorred"></div>
          </div>
            </div> 
          </div> 
          <div class="form-group row">
            
          
          <div class="col-sm-2"><label for="statename">AC <sup>*</sup></label></div>
           <div class="col">
            <div class="" style="width:100%;">
              <select name="ac" class="consttype form-control" >
                <option value="">-- Select AC --</option>
                @foreach($all_ac as $getAc)
                  <option value="{{$getAc->AC_NO}}"  @if($getAc->AC_NO==old('ac')) selected="selected" @endif> 
                  {{$getAc->AC_NO }} - {{$getAc->AC_NAME }} - {{$getAc->AC_NAME_HI}}
                  </option>
                @endforeach 
              </select>
              @if ($errors->has('ac'))
                        <span style="color:red;">{{ $errors->first('ac') }}</span>
                    @endif
              <div class="consterrormsg errormsg errorred"></div>
            </div>
          </div>
           
          
          </div> 
         <div class="form-group row float-right">       
            <div class="col">
            <button type="submit" id="candnomination" class="btn btn-primary">Submit</button>
            </div>
         </div>
                
          </div>
        </div>
                </div>
              </div>
        </div>
      </div>
    </div>    
    </section>
    </form>
          </div>
        </div>
  </div>
  </div>
  </section>
  </main>

@endsection