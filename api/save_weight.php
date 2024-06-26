<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();
$post = json_decode(file_get_contents('php://input'), true);

if(isset($post['status'], $post['doNo'], $post['vehicleNumber'], $post['driverName'], $post['product'], $post['totalCagesWeight']
, $post['totalCagesCount'], $post['totalBirdWeight'], $post['totalBirdCount'], $post['timestampData'], $post['capturedData']
, $post['averageBird'], $post['farm'], $post['startTime'], $post['weightDetails'], $post['endTime'], $post['assignedTo'], $post['company'])){

	$status = $post['status'];
	$doNo = $post['doNo'];
	$vehicleNumber = $post['vehicleNumber'];
	$driverName = $post['driverName'];
	$product = $post['product'];
	$totalCagesWeight = $post['totalCagesWeight'];
	$totalCagesCount = $post['totalCagesCount'];
	$totalBirdWeight = $post['totalBirdWeight'];
	$totalBirdCount = $post['totalBirdCount'];
	$timestampData = $post['timestampData'];
	$capturedData = $post['capturedData'];
	$averageBird = $post['averageBird'];
	$farmId = $post['farm'];
	$weightDetails = $post['weightDetails'];
	$startTime = $post['startTime'];
	$endTime = $post['endTime'];
	$assignedTo = $post['assignedTo'];
	$company = $post['company'];

	$remark = null;
	$customerName = null;
	$supplierName = null;
	$insert = true;
	$serialNo = "";
	$today = date("Y-m-d 00:00:00");

	if(isset($post['customerName']) && $post['customerName'] != null && $post['customerName'] != ''){
		$customerName = $post['customerName'];
	}
	
	if(isset($post['supplierName']) && $post['supplierName'] != null && $post['supplierName'] != ''){
		$supplierName = $post['supplierName'];
	}
	
	if(isset($post['remark']) && $post['remark'] != null && $post['remark'] != ''){
		$remark = $post['remark'];
	}

	if(isset($post['serialNo']) && $post['serialNo'] == null || $post['serialNo'] == ''){
	    if($status == 'Sales'){
	        $serialNo = 'S'.date("Ymd");
	    }
	    else{
	        $serialNo = 'P'.date("Ymd");
	    }

		if ($select_stmt = $db->prepare("SELECT COUNT(*) FROM weighing WHERE created_datetime >= ? AND status = ?")) {
            $select_stmt->bind_param('ss', $today, $status);
            
            // Execute the prepared query.
            if (! $select_stmt->execute()) {
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Failed to get latest count"
                    )); 
            }
            else{
                $result = $select_stmt->get_result();
                $count = 1;
                
                if ($row = $result->fetch_assoc()) {
                    $count = (int)$row['COUNT(*)'] + 1;
                    $select_stmt->close();
                }

                $charSize = strlen(strval($count));

                for($i=0; $i<(4-(int)$charSize); $i++){
                    $serialNo.='0';  // S0000
                }
        
                $serialNo .= strval($count);  //S00009
			}
		}
	}
	
	if ($select_stmt = $db->prepare("SELECT COUNT(*) FROM weighing WHERE start_time = ? AND weighted_by = ? AND farm_id = ?")) {
	    $select_stmt->bind_param('sss', $startTime, $assignedTo, $farmId);
        // Execute the prepared query.
        if (! $select_stmt->execute()) {
            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Failed to get result"
                )); 
        }
        else{
            $result = $select_stmt->get_result();
            $count = 1;
            
            if ($row = $result->fetch_assoc()) {
                if((int)$row['COUNT(*)'] > 0){
                    $insert = false;
                }
            }
		}
	}
	
	$select_stmt->close();

    if($insert){
        if ($insert_stmt = $db->prepare("INSERT INTO weighing (serial_no, po_no, customer, supplier, product, driver_name, lorry_no, 
	farm_id, remark, average_bird, weight_data, total_cages_weight, total_cages_count, total_bird_weight, total_bird_count, status, 
	start_time, end_time, company, weighted_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")){	
    	    $data = json_encode($weightDetails);
    		$insert_stmt->bind_param('ssssssssssssssssssss', $serialNo, $doNo, $customerName, $supplierName, $product, $driverName, 
    		$vehicleNumber, $farmId, $remark, $averageBird, $data, $totalCagesWeight, $totalCagesCount, $totalBirdWeight, $totalBirdCount, 
    		$status, $startTime, $endTime, $company, $assignedTo);		
    		// Execute the prepared query.
    		if (! $insert_stmt->execute()){
    			echo json_encode(
    				array(
    					"status"=> "failed", 
    					"message"=> $insert_stmt->error
    				)
    			);
    		} 
    		else{
    		    $id = $insert_stmt->insert_id;
    			$insert_stmt->close();
    			
    			echo json_encode(
    				array(
    					"status"=> "success", 
    					"message"=> "Added Successfully!!",
    					"serialNo"=> $serialNo,
    					"id" => $id
    				)
    			);
    		}
    
    		$db->close();
    	}
    }
	else{
	    if ($select_stmt = $db->prepare("SELECT * FROM weighing WHERE start_time = ? AND weighted_by = ? AND farm_id = ?")) {
    	    $select_stmt->bind_param('sss', $startTime, $assignedTo, $farmId);
            // Execute the prepared query.
            $id = 0;
            if (! $select_stmt->execute()) {
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Failed to get result"
                    )); 
            }
            else{
                $result = $select_stmt->get_result();
                echo json_encode(
        				array(
        					"status"=> "failed", 
        					"message"=> "In here"
        				)
        			);
                
                if ($row = $result->fetch_assoc()) {
                    $serialNo = $row['serial_no'];
                    $id = $row['id'];
                }
    		}
    		
    		echo json_encode(
				array(
					"status"=> "success", 
					"message"=> "Updated Successfully!!",
					"serialNo"=> $serialNo,
					"id" => $id
				)
			);
    	}
	}
} 
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );     
}
?>