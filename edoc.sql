-- Fixed & Improved edoc Database
DROP DATABASE IF EXISTS edoc;
CREATE DATABASE edoc;
USE edoc;

-- Table: admin
-- Table: admin
CREATE TABLE admin(
  admin_id INT AUTO_INCREMENT PRIMARY KEY,
  fname VARCHAR(100) NOT NULL,
  lname VARCHAR(100) NOT NULL,
  address VARCHAR(255) NOT NULL,
  age INT NOT NULL,
  gender ENUM('Male', 'Female', 'Other') NOT NULL,
  dob DATE NOT NULL,
  qualification VARCHAR(100) NOT NULL,
  grad_year YEAR NOT NULL,
  aemail VARCHAR(150) UNIQUE,
  apassword VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  tele VARCHAR(20) NOT NULL
);

INSERT INTO admin (fname,lname ,address,age,gender,dob,qualification,grad_year,aemail, apassword,created_at,tele) VALUES
('Aishwarya','Kadam','Belgaum','22','Female','2003-01-26','BDS','2024','aishwarya@gmail.com','Aish@27','CURRENT_TIMESTAMP','9876543210');

-- Table: doctor
CREATE TABLE doctor (
    doctor_id INT AUTO_INCREMENT PRIMARY KEY,
    dfname VARCHAR(100) NOT NULL,
    dlname VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    age INT CHECK (age >= 22),
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    dob DATE NOT NULL,
    qualification VARCHAR(150) NOT NULL,
    specialties VARCHAR(150) NOT NULL,
    grad_year YEAR NOT NULL,
    experience INT CHECK (experience >= 0),
    docemail VARCHAR(150) UNIQUE,
    docpassword VARCHAR(255),
    doctel INT(13) NOT NULL,
    date_registered DATE DEFAULT (CURRENT_DATE)
);

INSERT INTO doctor (docemail, dfname,dlname, docpassword, doctel, specialties) VALUES
('doctor@edoc.com', 'Test', 'Doctor', 'Test@1234', '56794210389', 'Gynecology'),
('dr.smith@hospital.com', 'Dr. John',' Smith', 'smith@12345', '9876543210', 'Cardiology'),
('dr.lee@hospital.com', 'Dr. Alice', 'Lee', 'lee@1234', '9123456789', 'Dermatology'),
('dr.raj@hospital.com', 'Dr. Rajesh','Kumar', 'raj@1234', '9012345678', 'Gastroenterology');

-- Table: patient
CREATE TABLE patient (
    patient_id INT AUTO_INCREMENT PRIMARY KEY,
    pfname VARCHAR(100) NOT NULL,
    plname VARCHAR(100) NOT NULL,
    paddress VARCHAR(255) NOT NULL,
    age INT CHECK (age >= 1 AND age <= 120),
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    pdob DATE NOT NULL,
    ptel INT(13) NOT NULL,
    pemail VARCHAR(150) UNIQUE,
    ppassword VARCHAR(255),
    reg_date DATE DEFAULT (CURRENT_DATE)
);


INSERT INTO patient (pemail, pfname,plname, ppassword, paddress, pdob, ptel) VALUES
('patient@edoc.com', 'Test',' Patient', '123', 'Sri Lanka', '2000-01-01', '0120000000'),
('emhashenudara@gmail.com', 'Hashen',' Udara', '123', 'Sri Lanka', '2002-06-03', '0700000000'),
('aish@mail.com', 'Aish',' Kad', 'Aish@27', 'Belgaum', '2003-01-26', '9876543210');

-- Table: specialties
CREATE TABLE specialties (
  id INT PRIMARY KEY,
  sname VARCHAR(50)
);

INSERT INTO specialties (id, sname) VALUES
(1, 'Accident and emergency medicine'), 
(2, 'Allergology'), 
(3, 'Anaesthetics'), 
(4, 'Biological hematology'), (5, 'Cardiology'), (6, 'Child psychiatry'), (7, 'Clinical biology'), (8, 'Clinical chemistry'), (9, 'Clinical neurophysiology'), (10, 'Clinical radiology'), (11, 'Dental, oral and maxillo-facial surgery'), (12, 'Dermato-venerology'), (13, 'Dermatology'), (14, 'Endocrinology'), (15, 'Gastro-enterologic surgery'), (16, 'Gastroenterology'), (17, 'General hematology'), (18, 'General Practice'), (19, 'General surgery'), (20, 'Geriatrics'), (21, 'Immunology'), (22, 'Infectious diseases'), (23, 'Internal medicine'), (24, 'Laboratory medicine'), (25, 'Maxillo-facial surgery'), (26, 'Microbiology'), (27, 'Nephrology'), (28, 'Neuro-psychiatry'), (29, 'Neurology'), (30, 'Neurosurgery'), (31, 'Nuclear medicine'), (32, 'Obstetrics and gynecology'), (33, 'Occupational medicine'), (34, 'Ophthalmology'), (35, 'Orthopaedics'), (36, 'Otorhinolaryngology'), (37, 'Paediatric surgery'), (38, 'Paediatrics'), (39, 'Pathology'), (40, 'Pharmacology'), (41, 'Physical medicine and rehabilitation'), (42, 'Plastic surgery'), (43, 'Podiatric Medicine'), (44, 'Podiatric Surgery'), (45, 'Psychiatry'), (46, 'Public health and Preventive Medicine'), (47, 'Radiology'), (48, 'Radiotherapy'), (49, 'Respiratory medicine'), (50, 'Rheumatology'), (51, 'Stomatology'), (52, 'Thoracic surgery'), (53, 'Tropical medicine'), (54, 'Urology'), (55, 'Vascular surgery'), (56, 'Venereology');
-- Table: schedule
CREATE TABLE schedule (
  scheduleid INT AUTO_INCREMENT PRIMARY KEY,
  docid INT,
  title VARCHAR(255),
  scheduledate DATE,
  scheduletime TIME,
  nop INT,
  FOREIGN KEY (docid) REFERENCES doctor(doctor_id)
);

INSERT INTO schedule (docid, title, scheduledate, scheduletime, nop) VALUES
(1, 'Checkup', '2022-06-10', '20:36:00', 1),
(1, 'Consultation', '2022-06-10', '13:33:00', 1);

-- Table: appointment
CREATE TABLE appointment (
  appoid INT AUTO_INCREMENT PRIMARY KEY,
  pid INT,
  apponum INT,
  scheduleid INT,
  appodate DATE,
  appotime TIME,
  docid INT,
  transaction_id VARCHAR(50),
  payment_date datetime,
  payment_status VARCHAR(20),
  status enum('Expired', 'Cancelled', 'Completed','Painding') DEFAULT 'Painding',
  status_updated_at datetime,
  FOREIGN KEY (pid) REFERENCES patient( patient_id),
  FOREIGN KEY (docid) REFERENCES doctor(doctor_id ),
  FOREIGN KEY (scheduleid) REFERENCES schedule(scheduleid)
);

INSERT INTO appointment (pid, apponum, scheduleid, appodate, appotime, docid) VALUES
(1, 1, 1, '2022-06-03', '10:00:00', 1);

-- Table: webuser
CREATE TABLE webuser (
  email VARCHAR(255) PRIMARY KEY,
  usertype CHAR(1)
);

INSERT INTO webuser (email, usertype) VALUES
('admin@edoc.com', 'a'),
('doctor@edoc.com', 'd'),
('patient@edoc.com', 'p'),
('emhashenudara@gmail.com', 'p');

CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_id INT,
    card_last4 VARCHAR(4),
    card_name VARCHAR(80),
    expiry VARCHAR(5),
    status VARCHAR(20)
);

-- Add this table to your database

CREATE TABLE IF NOT EXISTS doctor_payment_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    payment_name VARCHAR(255) NOT NULL,
    payment_mobile VARCHAR(20) NOT NULL,
    upi_id VARCHAR(255) NOT NULL,
    qr_code_path VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctor(doctor_id) ON DELETE CASCADE,
    UNIQUE KEY unique_doctor (doctor_id)
);