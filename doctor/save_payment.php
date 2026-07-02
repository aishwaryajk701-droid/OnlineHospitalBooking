<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION["user"]) || $_SESSION['usertype']!='d'){
    header("location: ../login.php");
    exit();
}

include("../connection.php");

if($_POST){
    $doctor_id = $_POST['doctor_id'];
    $payment_name = $_POST['payment_name'];
    $payment_mobile = $_POST['payment_mobile'];
    $upi_id = $_POST['upi_id'];
    
    // Handle QR code upload
    $qr_code_path = "";
    $upload_new_qr = false;
    
    if(isset($_FILES['qr_code']) && $_FILES['qr_code']['error'] == 0){
        $upload_new_qr = true;
        $target_dir = "../uploads/qr_codes/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["qr_code"]["name"], PATHINFO_EXTENSION));
        $new_filename = "qr_" . $doctor_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Check if image file is actual image
        $check = getimagesize($_FILES["qr_code"]["tmp_name"]);
        if($check !== false) {
            // Allow certain file formats
            if($file_extension == "jpg" || $file_extension == "png" || $file_extension == "jpeg" || $file_extension == "gif") {
                // Check file size (limit to 5MB)
                if ($_FILES["qr_code"]["size"] <= 5000000) {
                    if (move_uploaded_file($_FILES["qr_code"]["tmp_name"], $target_file)) {
                        $qr_code_path = $target_file;
                        
                        // Delete old QR code if exists
                        $old_qr = $database->query("SELECT qr_code_path FROM doctor_payment_details WHERE doctor_id='$doctor_id'");
                        if($old_qr->num_rows > 0){
                            $old_data = $old_qr->fetch_assoc();
                            if(!empty($old_data['qr_code_path']) && file_exists($old_data['qr_code_path'])){
                                unlink($old_data['qr_code_path']);
                            }
                        }
                    } else {
                        header("location: payment_settings.php?action=error");
                        exit();
                    }
                } else {
                    header("location: payment_settings.php?action=error");
                    exit();
                }
            } else {
                header("location: payment_settings.php?action=error");
                exit();
            }
        } else {
            header("location: payment_settings.php?action=error");
            exit();
        }
    }
    
    // Check if payment details already exist
    $check_query = $database->query("SELECT * FROM doctor_payment_details WHERE doctor_id='$doctor_id'");
    
    if($check_query->num_rows > 0){
        // Update existing record
        if($upload_new_qr){
            $sql = "UPDATE doctor_payment_details SET 
                    payment_name='$payment_name', 
                    payment_mobile='$payment_mobile', 
                    upi_id='$upi_id', 
                    qr_code_path='$qr_code_path',
                    updated_at=NOW()
                    WHERE doctor_id='$doctor_id'";
        } else {
            $sql = "UPDATE doctor_payment_details SET 
                    payment_name='$payment_name', 
                    payment_mobile='$payment_mobile', 
                    upi_id='$upi_id',
                    updated_at=NOW()
                    WHERE doctor_id='$doctor_id'";
        }
    } else {
        // Insert new record
        $sql = "INSERT INTO doctor_payment_details 
                (doctor_id, payment_name, payment_mobile, upi_id, qr_code_path, created_at) 
                VALUES 
                ('$doctor_id', '$payment_name', '$payment_mobile', '$upi_id', '$qr_code_path', NOW())";
    }
    
    if($database->query($sql)){
        header("location: payment_settings.php?action=success");
    } else {
        header("location: payment_settings.php?action=error");
    }
    
} else {
    header("location: payment_settings.php");
}
?>