 <?php



 function updateEvmById($data)
    {
     
         $data = ["add_evm_vote"=>1000, "total_vote"=>60000, "nom_id"=>3021, "st_code"=>"S01", "pc_no"=>7];
		$url ="https://resultapi.eci.gov.in/v1/apiRoutes/updateEvmById";
    //$url ="http://localhost:3000/v1/apiRoutes/updateEvmById";
     //Andhra Pradesh, Amalapuram, MORTHA SIVA RAMA KRISHNA   
		
		$fields = $data;
		
		$headers = array(
				"x-access-token: 4AAQSkZJRgABAQAAAQABAAD2wBDAAEBAQEBAQeweEBAQEBAQEBAQsdfsdfaEBAQEBAQEBAQEBAQEBAQEBPmDG9y8aiU3Hv2Fkx8AHEQcSw3G2DyZW1FelRLieg3RlNYhrQswWGRBsodFHE58CMPrrrrHNrIIutQI8SU7HZhV4YgJHhwOK23tYEwHk92HAbjx3PrbXSt5ktNB858o2RDbYpqDxV4f2SE0N2bYLcOxGMS3zux6Lsio1tjmin9EoymydOZfd1TFJD6EyKI",
				"content-type: application/json"
		);
		
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, $url );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
         $result = curl_exec( $ch );
         print_r($result);
         return true;
  
    }

    function updatePostalById($data)
    {
         $data = ["add_postal_vote"=>100, "total_vote"=>500, "nom_id"=>3021, "st_code"=>"S01", "pc_no"=>7];
         // echo json_encode($data);
         // exit;

		$url ="https://resultapi.eci.gov.in/v1/apiRoutes/updatePostalById";
    //$url ="http://localhost:3000/v1/apiRoutes/updatePostalById";
		
		$fields = $data;
		
		$headers = array(
				"x-access-token: 4AAQSkZJRgABAQAAAQABAAD2wBDAAEBAQEBAQeweEBAQEBAQEBAQsdfsdfaEBAQEBAQEBAQEBAQEBAQEBPmDG9y8aiU3Hv2Fkx8AHEQcSw3G2DyZW1FelRLieg3RlNYhrQswWGRBsodFHE58CMPrrrrHNrIIutQI8SU7HZhV4YgJHhwOK23tYEwHk92HAbjx3PrbXSt5ktNB858o2RDbYpqDxV4f2SE0N2bYLcOxGMS3zux6Lsio1tjmin9EoymydOZfd1TFJD6EyKI",
				"content-type: application/json"
		);
		
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, $url );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
         $result = curl_exec( $ch );
         print_r($result);
         return true;
  
    }

      function updateWinningLeading($data)
    {
        
    //  $data = [  "st_code" => "S19",
  
    //     "pc_no" => 3,
    //     "pc_name" => "Khadoor Sahibddddddddddddddddddttttttttttttttttt",
    //     "pc_hname" => "खडूर साहिब",
    //     "election_id" => "1",
    //     "constituency_type" => "PC",
    //     "candidate_id" => 2,
    //     "nomination_id" => 0,
    //     "lead_cand_name" => "fffffffffffffffffffff",
    //     "lead_cand_hname" => "करुि",
    //     "lead_cand_partyid" => 743,
    //     "lead_cand_party" => "Independent",
    //     "lead_cand_hparty" => "निर्दलीय",
    //     "lead_party_type" => "Z",
    //     "lead_party_abbre" => "IND",
    //     "lead_hpartyabbre" => "आईएनडी",
    //     "trail_candidate_id" => 63,
    //     "trail_nomination_id" => 71,
    //     "trail_cand_name" => "POLAO",
    //     "trail_cand_hname" => "िुपुल",
    //     "trail_cand_partyid" => "2094",
    //     "trail_cand_party" => "Aajad Bharat Party (Democratic)",
    //     "trail_cand_hparty" => "आजाद भारत पार्टी(डेमोक्रेटिक",
    //     "trail_party_type" => "U",
    //     "trail_party_abbre" => "AABHAP",
    //     "trail_hpartyabbre" => "हभप  ",
    //     "lead_total_vote" => 126928,
    //     "trail_total_vote" => 2281831,
    //     "margin" =>2154903
    // ];
		$url ="https://resultapi.eci.gov.in/v1/apiRoutes/updateWinningLeading";
   // $url ="http://localhost:3000/v1/apiRoutes/updateWinningLeading";
		
		$fields = $data;
		
		$headers = array(
        "x-access-token: 4AAQSkZJRgABAQAAAQABAAD2wBDAAEBAQEBAQeweEBAQEBAQEBAQsdfsdfaEBAQEBAQEBAQEBAQEBAQEBPmDG9y8aiU3Hv2Fkx8AHEQcSw3G2DyZW1FelRLieg3RlNYhrQswWGRBsodFHE58CMPrrrrHNrIIutQI8SU7HZhV4YgJHhwOK23tYEwHk92HAbjx3PrbXSt5ktNB858o2RDbYpqDxV4f2SE0N2bYLcOxGMS3zux6Lsio1tjmin9EoymydOZfd1TFJD6EyKI",
        "content-type: application/json"
    );
		
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, $url );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
         $result = curl_exec( $ch );
         print_r($result);
        return true;
  
    }

     function updateWinningLeadingStatus($data)
    {

        
     $data = [  
          "st_code" => "S19",
          "pc_no" => 3,
          //"nomination_id" => 0,
          "status" => 1
          ];
   
    
		$url ="https://resultapi.eci.gov.in/v1/apiRoutes/updateWinningLeadingStatus";
       //$url ="http://localhost:3000/v1/apiRoutes/updateWinningLeadingStatus";
		
		$fields = $data;
		
		$headers = array(
				"x-access-token: 4AAQSkZJRgABAQAAAQABAAD2wBDAAEBAQEBAQeweEBAQEBAQEBAQsdfsdfaEBAQEBAQEBAQEBAQEBAQEBPmDG9y8aiU3Hv2Fkx8AHEQcSw3G2DyZW1FelRLieg3RlNYhrQswWGRBsodFHE58CMPrrrrHNrIIutQI8SU7HZhV4YgJHhwOK23tYEwHk92HAbjx3PrbXSt5ktNB858o2RDbYpqDxV4f2SE0N2bYLcOxGMS3zux6Lsio1tjmin9EoymydOZfd1TFJD6EyKI",
				"content-type: application/json"
		);
		
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, $url );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
         $result = curl_exec( $ch );
          print_r($result);
         return true;
          
        
  
    }
    $data = [  
          "st_code" => "S19",
          "pc_no" => 3,
          "nomination_id" => 0,
          "status" => 1
          ];
    updateEvmById($data);

    