@extends('layouts.theme')
@section('content') 
<style>
.accordion {
    background-color: #eee;
    color: #444;
    cursor: pointer;
    padding: 18px;
    width: 100%;
    border: none;
    text-align: left;
    outline: none;
    font-size: 15px;
    transition: 0.4s;
}

.active, .accordion:hover {
    background-color: #ccc;
}

.accordion:after {
    content: '\002B';
    color: #777;
    font-weight: bold;
    float: right;
    margin-left: 5px;
}

.active:after {
    content: "\2212";
}

.panel {
    padding: 0 18px;
    background-color: white;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.2s ease-out;
}
</style>
<link href="{{ asset('theme/main.css') }}" rel="stylesheet">
<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
   <div class="nw-crte-usr">
         <div class="head-title">
          <h3><i><img src="{{ asset('theme/images/icons/tab-icon-010.png')}}" /></i>Candidate Information</h3>
         </div>
  <div class="col-lg-12">
                <!-- Nomination Part 1 -->
        <div class="nomination-parts1">
           <div class="nomination-heading">
                    <!--FORM 2A<br/>
                    <em>(See rule 4)</em><br/>-->
                            NOMINATION PAPER<br/>
                    <em>Election to the House of the People</em>
           </div>
                
        <div class="col-lg-12">
                    <div class="col-lg-11"></div>
                    <div class="col-lg-1"><input type='file' onchange="readURL(this);" id="profileimg"/></div>
                  </div>
                  <div class="nomination-profilepic">
                    <div class="profilepic" id="profilepic"/>
                      <span class="profiletext">Recent stamp size (2cm X 2.5cm) photograph in white/off white background with full face view to be attached.</span>
                      <input type="file" name="profile" class="upload_profile">
                    </div>
                  </div>
              </div>        

<button class="accordion">PART I &nbsp; &nbsp; &nbsp; (STRIKE OFF PART I OR PART II BELOW WHICHEVER IS NOT APPLICABLE) </button>
<div class="panel">
    <div class="nomination-parts box recognised">
                  
                    <div class="nomination-form-heading">
                     <!-- STRIKE OFF PART I OR PART II BELOW WHICHEVER IS NOT APPLICABLE<br/>
                      <strong>PART I</strong><br/>-->
                      (To be used by candidate set up by recognised political party)
                    </div>
                    
                    <div class="nomination-detail">
                       <p>I nominate as a candidate for election to the House of the People from the <input type="text" name="parliamentry_constituency" class="nomination-field-3"/>Parliamentary constituency.<br/>
                       
                      Candidate's name<input type="text" name="candidate_name" class="nomination-field-3" placeholder="Candidate Name"/>
                      
                      <span class="father_name spouse">Father's</span>/<span class="mother_name spouse">mother's</span>/<span class="husband_name spouse">husband's name</span> <!--<span class="clickhere">(Click here)</span>-->
                      
                      <span id="spouse_name"><input type="text" name="spouse_name" placeholder="Please select spouse" class="nomination-field-3" readonly/></span>
                      
                      <span id="father_name" style="display:none"><input type="text" name="father_name" placeholder="Father's Name" class="nomination-field-3"/></span>
                      <span id="mother_name" style="display:none"><input type="text" name="mother_name" placeholder="Mother's Name" class="nomination-field-3"/></span>
                      <span id="husband_name" style="display:none"><input type="text" name="husband_name" placeholder="Husband's Name" class="nomination-field-3"/></span>
                      
                      His postal address <input type="text" name="candidate_name" class="nomination-field-3"/>His name is entered at S.No<input type="text" name="candidate_name" class="nomination-field-2"/>in Part No<input type="text" name="candidate_name" placeholder="" class="nomination-field-2"/> of the electoral roll for <input type="text" name="candidate_name" class="nomination-field-2"/>(Assembly constituency comprised within)<input type="text" name="candidate_name" class="nomination-field-2"/> Parliamentary Constituency.<br/>
                      
                      My name is <input type="text" name="candidate_name" class="nomination-field-3"/> and it is entered at S.No<input type="text" name="candidate_name" class="nomination-field-2"/>in Part No<input type="text" name="candidate_name" class="nomination-field-2"/>of the electoral roll for <input type="text" name="candidate_name" class="nomination-field-3"/> *(Assembly constituency comprised within)<input type="text" name="candidate_name" class="nomination-field-3"/> Parliamentary constituency.</p>
                    </div>
                  
                  
                    <div class="nomination-signature">
                      <span class="nomination-date left">Date<input type="text" name="candidate_name" class="nomination-field-4"/></span>
                      <span class="nomination-sign right">Signature of Proposer <input type="text" name="candidate_name" class="nomination-field-4"/></span>
                    </div>
                  </div>
</div>

<button class="accordion">PART II</button>
<div class="panel">
  <p><div class="nomination-parts box not-recognised">
                  <div class="nomination-form-heading">
                    <strong>PART II</strong><br/>
                    (To be used by candidate NOT set up by recognised political party) 
                  </div>
                  
                  <div class="nomination-detail">
                     <p>We hereby nominate as candidate for election to the House of the People from the<input type="text" name="candidate_name" placeholder="" class="nomination-field-3"/>Parliamentary Constituency<br/>
                     
                     Candidate's name<input type="text" name="candidate_name" placeholder="" class="nomination-field-2"/>Father's/mother's/husband's name.<input type="text" name="candidate_name" placeholder="" class="nomination-field-2"/>His postal address<input type="text" name="candidate_name" placeholder="" class="nomination-field-2"/><br/>
                     
                     His name is entered at S.No <input type="text" name="candidate_name" placeholder="" class="nomination-field-2"/>in Part No <input type="text" name="candidate_name" placeholder="" class="nomination-field-2"/>of the electoral roll for <input type="text" name="candidate_name" placeholder="" class="nomination-field-2"/>(Assembly constituency comprised within) <input type="text" name="candidate_name" placeholder="" class="nomination-field-2"/> Parliamentary constituency.<br/><br/>
                     
                     We declare that we are electors of the above Parliamentary Constituency and our names are entered in the electoral roll for that Parliamentary Constituency as indicated below and we append our signatures below in token of subscribing to this nomination:â€”</p>
                     
                     <div class="table-heading">Particulars of the proposers and their signatures</div>
                     <table class="table table-bordered proposers-table">
                        <thead>
                          <tr>
                          <th>Sr No.</th>
                          <th>Name of component Assembly Constituency</th>
                          <th colspan="2">Elector Roll No. of Proposer</th>
                          <th>Full Name</th>
                          <th>Signature</th>
                          <th>Date</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>Part No. of Electoral Roll</td>
                          <td>S.No. in that part</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          </tr>
                           <tr>
                          <td>1</td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          </tr>
                           <tr>
                          <td>2</td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          </tr>
                          <tr>
                          <td>3</td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          </tr>
                          <tr>
                          <td>4</td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          </tr>
                          <tr>
                          <td>5</td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          </tr>
                          <tr>
                          <td>6</td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          </tr>
                          <tr>
                          <td>7</td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          </tr>
                          <tr>
                          <td>8</td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          </tr>
                          <tr>
                          <td>9</td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          </tr>
                          <tr>
                          <td>10</td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          <td><input type="text" name="candidate_name" placeholder="" class="particulars-field-12"/></td>
                          </tr>
                        </tbody>
                      </table>
                  </div>
                </div></p>
</div>

<button class="accordion">PART III</button>
<div class="panel">
  <p<div class="nomination-parts">
                 
                
                <div class="nomination-detail">
                  <p>I, the candidate mentioned in Part I/Part II (Strike out which is not applicable) assent to this nomination and hereby declareâ€”</p>
                  <ul>
                    <li>(a) that I am a citizen of India and have not acquired the citizenship of any foreign State/country.</li>
                    <li>(b) that I have completed<input type="text" name="candidate_name" class="nomination-field-2"/>years of age;<br/>
                    
                    [STRIKE OUT c(i) or c(ii) BELOW WHICHEVER IS NOT APPLICABLE]</li>
                    
                <div class="nomination-options strikeout">
                  <div class="checkbox">
                    <label><input type="radio" class="strikeout-applicable" name="applicable" value="applicable">(c) (i) that I am set up at this election by the<input type="text" name="candidate_name" class="nomination-field-2"/> party, which is recognised National Party/State Party in this State and that the symbol reserved for the above party be allotted to me.</label>
                  </div>
                  <div class="or">OR</div>
                  <div class="checkbox">
                    <label><input type="radio" class="strikeout-applicable" name="applicable" value="not-applicable">(c) (ii) that I am set up at this election by the <input type="text" name="candidate_name" class="nomination-field-2"/> party, which is a registered-unrecognised political party/that I am contesting this election as an independent candidate. (Strike out which is not applicable) and that the symbols I have chosen, in order of preference, are:â€”
                  (i)<input type="text" name="candidate_name" class="nomination-field-2"/>(ii)<input type="text" name="candidate_name" class="nomination-field-2"/>(iii)<input type="text" name="candidate_name" class="nomination-field-2"/></label>
                  </div>
                </div>
                
                  <li>(d) that my name and my father's/mother's/husband's name have been correctly spelt out above in <input type="text" name="candidate_name" class="nomination-field-2"/> (name of the language);</li>
                  <li>(e) that to the best of my knowledge and belief, I am qualified and not also disqualified for being chosen to fill the seat in the House of the People.</li></ul>
                </div>
                
                <div class="nomination-detail">
                  <p>*I further declare that I am a member of the <input type="text" name="candidate_name" class="nomination-field-2"/>**Caste/tribe which is a scheduled **caste/tribe of the State of <input type="text" name="candidate_name" class="nomination-field-2"/> in relation to <input type="text" name="candidate_name" class="nomination-field-2"/>(area) in that State. 
                
                  I also declare that I have not been, and shall not be nominated as a candidate at the present general election/the bye-elections being held simultaneously, to the House of the People from more than two Parliamentary Constituencies. </p>
                </div>
                
                <div class="nomination-signature">
                  <span class="nomination-date left">Date <input type="text" name="candidate_name" class="nomination-field-4"/> </span>
                  <span class="nomination-sign right">Signature of Proposer <input type="text" name="candidate_name" class="nomination-field-4"/></span>
                </div>
                
                <div class="nomination-note">
                  Î³Score out the words "assembly constituency comprised within" in the case of Jammu and Kashmir, Andaman and Nicobar Islands, Chandigarh, Dadra and Nagar Haveli, Daman and Diu and Lakshadweep.<br/>

                  *Score out this paragraph, if not applicable.<br/>

                  **Score out the words not applicable. N.B.â€”A "recognised political party" means a political party recognised by the Election Commission under the Election Symbols (Reservation and Allotment) Order, 1968 in the State concerned. 
                </div>
              </div></p>
</div>
  
          </div><!-- End Of nw-crte-usr Div -->
   
        
    
    </div> <!-- End Of child-area Div -->     
  </div><!-- End Of parent-wrap Div -->
  </div> 
<script>
var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.maxHeight){
      panel.style.maxHeight = null;
    } else {
      panel.style.maxHeight = panel.scrollHeight + "px";
    } 
  });
}
</script>
 
@endsection