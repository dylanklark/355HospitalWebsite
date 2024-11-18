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
