<?php
require_once 'db_connect.php';

$post = json_decode(file_get_contents('php://input'), true);

$staffId = $post['userId'];
$userId = $post['uid'];

//$lots = $db->query("SELECT * FROM lots WHERE deleted = '0'");
$products = $db->query("SELECT * FROM products WHERE deleted = '0' AND customer='".$staffId."'");
$customers = $db->query("SELECT * FROM customers WHERE deleted = '0' AND customer='".$staffId."'");
$suppliers = $db->query("SELECT * FROM supplies WHERE deleted = '0' AND customer='".$staffId."'");
$grades = $db->query("SELECT * FROM grades WHERE deleted = '0' AND customer='".$staffId."'");
$transporters = $db->query("SELECT * FROM transporters WHERE deleted = '0' AND customer='".$staffId."'");
$staff = $db->query("SELECT * FROM staff WHERE deleted = '0' AND customer='".$staffId."'");
$indicators = $db->query("SELECT * FROM indicators WHERE users='".$userId."'");
$printers = $db->query("SELECT * FROM printers WHERE users='".$userId."'"); 

$data1 = array();
$data2 = array();
$data3 = array();
$data4 = array();
$data5 = array();
$data6 = array();
$data7 = array();
$data8 = array();

while($row1=mysqli_fetch_assoc($products)){
    $data1[] = array( 
        'id'=>$row1['id'],
        'product_name'=>$row1['product_name']
    );
}

while($row2=mysqli_fetch_assoc($customers)){
    $data2[] = array( 
        'id'=>$row2['id'],
        'customer_name'=>$row2['customer_name'],
        'regNo'=>$row2['reg_no'],
        'address'=>$row2['customer_address'],
        'address2'=>$row2['customer_address2'],
        'address3'=>$row2['customer_address3'],
        'address4'=>$row2['customer_address4'],
        'phone'=>$row2['customer_phone'],
        'email'=>$row2['pic']
    );
}

while($row3=mysqli_fetch_assoc($suppliers)){
    $data3[] = array( 
        'id'=>$row3['id'],
        'supplier_name'=>$row3['supplier_name'],
        'regNo'=>$row3['reg_no'],
        'address'=>$row3['supplier_address'],
        'address2'=>$row3['supplier_address2'],
        'address3'=>$row3['supplier_address3'],
        'address4'=>$row3['supplier_address4'],
        'phone'=>$row3['supplier_phone'],
        'email'=>$row3['pic']
    );
}

while($row4=mysqli_fetch_assoc($grades)){
    $data4[] = array( 
        'id'=>$row4['id'],
        'grades'=>$row4['grades']
    );
}

while($row5=mysqli_fetch_assoc($transporters)){
    $data5[] = array( 
        'id'=>$row5['id'],
        'transporter_name'=>$row5['transporter_name']
    );
}

while($row6=mysqli_fetch_assoc($staff)){
    $data6[] = array( 
        'id'=>$row6['id'],
        'staff_name'=>$row6['staff_name']
    );
}

while($row7=mysqli_fetch_assoc($indicators)){
    $data7[] = array( 
        'id'=>$row7['id'],
        'name'=>$row7['name'],
        'mac_address'=>$row7['mac_address'],
        'udid'=>$row7['udid']
    );
}

while($row8=mysqli_fetch_assoc($printers)){
    $data8[] = array( 
        'id'=>$row8['id'],
        'name'=>$row8['name'],
        'mac_address'=>$row8['mac_address'],
        'udid'=>$row8['udid']
    );
}

$db->close();

echo json_encode(
    array(
        "status"=> "success", 
        "products"=> $data1, 
        "customers"=> $data2, 
        "suppliers"=> $data3, 
        "grades"=> $data4, 
        "transporter"=> $data5,
        "staff"=> $data6, 
        "indicators"=> $data7,
        "printers"=> $data8
    )
);
?>