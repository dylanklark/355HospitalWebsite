CREATE TABLE Hospital (
    Hospital_ID INT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Street VARCHAR(255),
    State VARCHAR(50),
    ZIP VARCHAR(10)
);


CREATE TABLE Visit (
    Visit_Number INT PRIMARY KEY,
    Hospital_ID INT NOT NULL,
    Date DATE,
    Reason_for_visit VARCHAR(255),
    Diagnosis TEXT,
    Time TIME,
    FOREIGN KEY (Hospital_ID) REFERENCES Hospital(Hospital_ID)
);


CREATE TABLE Health_Insurance (
    Policy_Number INT PRIMARY KEY,
    Name VARCHAR(255),
    Deductible DECIMAL(10, 2),
    Street VARCHAR(255),
    City VARCHAR(100),
    ZIP VARCHAR(10),
    Copay DECIMAL(10, 2)
);


CREATE TABLE Patient (
    SSN VARCHAR(11) PRIMARY KEY,
    Phone VARCHAR(15),
    Patient_FN VARCHAR(50),
    Patient_LN VARCHAR(50),
    Street VARCHAR(255),
    State VARCHAR(50),
    ZIP VARCHAR(10),
    Policy_Number INT,
    FOREIGN KEY (Policy_Number) REFERENCES Health_Insurance(Policy_Number)
);


CREATE TABLE Health_Care_Provider (
    Employee_ID INT PRIMARY KEY,
    EmpFN VARCHAR(50),
    EmpLN VARCHAR(50),
    Phone VARCHAR(15),
    Specialty VARCHAR(255),
    Hospital_ID INT,
    FOREIGN KEY (Hospital_ID) REFERENCES Hospital(Hospital_ID)
);


CREATE TABLE Bills (
    Policy_Number INT,
    Hospital_ID INT,
    Cost DECIMAL(10, 2),
    PRIMARY KEY (Policy_Number, Hospital_ID),
    FOREIGN KEY (Policy_Number) REFERENCES Health_Insurance(Policy_Number),
    FOREIGN KEY (Hospital_ID) REFERENCES Hospital(Hospital_ID)
);


CREATE TABLE Medications (
    Medication_ID INT PRIMARY KEY,
    Name VARCHAR(255),
    Dose VARCHAR(50),
    Side_Effects TEXT,
    Employee_ID INT,
    FOREIGN KEY (Employee_ID) REFERENCES Health_Care_Provider(Employee_ID)
);


CREATE TABLE Prescribed (
    SSN VARCHAR(11),
    Medication_ID INT,
    PRIMARY KEY (SSN, Medication_ID),
    FOREIGN KEY (SSN) REFERENCES Patient(SSN),
    FOREIGN KEY (Medication_ID) REFERENCES Medications(Medication_ID)
);


CREATE TABLE Contraindications (
    Medication_ID INT,
    Contraindication TEXT,
    PRIMARY KEY (Medication_ID, Contraindication),
    FOREIGN KEY (Medication_ID) REFERENCES Medications(Medication_ID)
);

CREATE TABLE Prescribes (
    Medication_ID INT,
	Employee_ID INT,
    PRIMARY KEY(Medication_ID, Employee_ID),
    FOREIGN KEY (Medication_ID) REFERENCES Medications(Medication_ID),
    FOREIGN KEY (Employee_ID) REFERENCES Health_Care_Provider(Employee_ID)
);

–2 TABLE JOIN: “Employees and the hospital they work at”


SELECT 
    Health_Care_Provider.Employee_ID,
    Health_Care_Provider.EmpFN AS First_Name,
    Health_Care_Provider.EmpLN AS Last_Name,
    Hospital.Name AS Hospital_Name
FROM Health_Care_Provider
JOIN Hospital ON Health_Care_Provider.Hospital_ID = Hospital.Hospital_ID
ORDER BY Hospital_Name;



–3 TABLE JOIN: “Patients with the name of their medications”

SELECT Patient.SSN,
    Patient.Patient_FN AS First_Name,
    Patient.Policy_Number,
    Medications.Name AS Medication
FROM Patient
JOIN Prescribed ON Patient.SSN = Prescribed.SSN
JOIN Medications ON Prescribed.Medication_ID = Medications.Medication_ID;


–SELF JOIN: “Every state that has multiple hospitals”

SELECT 
    h1.State AS State,
    h1.Name AS Hospital1_Name,
    h2.Name AS Hospital2_Name
FROM Hospital h1
JOIN Hospital h2 ON h1.State = h2.State AND h1.Hospital_ID < h2.Hospital_ID
ORDER BY h1.State, h1.Name, h2.Name;

–AGGREGATE FUNCTION: “Insurance policies and amount of patients with each one”

SELECT 
    Health_Insurance.Policy_Number,
    Health_Insurance.Name AS Policy_Name,
    COUNT(Patient.SSN) AS Total_Patients
FROM Health_Insurance
LEFT JOIN Patient ON Health_Insurance.Policy_Number = Patient.Policy_Number
GROUP BY Health_Insurance.Policy_Number, Health_Insurance.Name
ORDER BY Total_Patients DESC;

–AGG. FUNC. W/ GROUP BY & HAVING: “Hospitals with 3+ Health care providers”

SELECT 
    Hospital.Name AS Hospital_Name,
    Hospital.State,
    COUNT(Health_Care_Provider.Employee_ID) AS Total_Providers
FROM Hospital
JOIN Health_Care_Provider ON Hospital.Hospital_ID = Health_Care_Provider.Hospital_ID
GROUP BY Hospital.Hospital_ID, Hospital.Name, Hospital.State
HAVING COUNT(Health_Care_Provider.Employee_ID) > 3
ORDER BY Total_Providers DESC;




