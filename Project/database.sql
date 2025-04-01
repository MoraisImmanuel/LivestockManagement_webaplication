use livestock_monitoring;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'client') NOT NULL
);

CREATE TABLE diseases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    symptoms TEXT,
    treatment TEXT
);

CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    disease_id INT,
    symptoms TEXT,
    location VARCHAR(100),
    date_reported TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (disease_id) REFERENCES diseases(id)
);


USE livestock_monitoring;

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('unread', 'read') DEFAULT 'unread',
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id) -- Assuming notifications are tied to users
);
select*from users
INSERT INTO notifications (message, user_id)
VALUES ('New disease report received for review', 5);  -- Replace `1` with the actual user_id




CREATE TABLE IF NOT EXISTS animals (
    id VARCHAR(10) PRIMARY KEY,  -- NAM001 format
    user_id INT,
    type ENUM('cattle', 'sheep', 'goat', 'chicken') NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    date_of_birth DATE NOT NULL,
    last_vaccination DATE,
    notes TEXT,
    health_status ENUM('Healthy', 'Vaccination Due Soon', 'Vaccination Overdue', 'Sick') NOT NULL DEFAULT 'Healthy',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Create disease_outbreaks table
CREATE TABLE IF NOT EXISTS disease_outbreaks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    location VARCHAR(100) NOT NULL,
    disease VARCHAR(100) NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    status ENUM('active', 'resolved') DEFAULT 'active',
    reported_by INT,
    reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    FOREIGN KEY (reported_by) REFERENCES users(id)
);

-- Create vaccinations table
CREATE TABLE IF NOT EXISTS vaccinations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    animal_id VARCHAR(10),
    vaccine_type VARCHAR(100) NOT NULL,
    vaccination_date DATE NOT NULL,
    next_due_date DATE NOT NULL,
    administered_by INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE,
    FOREIGN KEY (administered_by) REFERENCES users(id)
);

-- Create health_records table
CREATE TABLE IF NOT EXISTS health_records (
    id INT PRIMARY KEY AUTO_INCREMENT,
    animal_id VARCHAR(10),
    condition_type VARCHAR(100) NOT NULL,
    symptoms TEXT,
    diagnosis TEXT,
    treatment TEXT,
    recorded_by INT,
    record_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES users(id)
);

-- Create diagnostic_symptoms table
CREATE TABLE IF NOT EXISTS diagnostic_symptoms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    animal_type ENUM('cattle', 'sheep', 'goat', 'chicken') NOT NULL,
    symptom VARCHAR(100) NOT NULL,
    UNIQUE KEY unique_symptom (animal_type, symptom)
);

-- Create diagnostic_diseases table
CREATE TABLE IF NOT EXISTS diagnostic_diseases (
    id INT PRIMARY KEY AUTO_INCREMENT,
    animal_type ENUM('cattle', 'sheep', 'goat', 'chicken') NOT NULL,
    symptom_id INT,
    disease VARCHAR(100) NOT NULL,
    FOREIGN KEY (symptom_id) REFERENCES diagnostic_symptoms(id),
    UNIQUE KEY unique_disease (animal_type, symptom_id, disease)
);

-- Insert initial diagnostic data
INSERT INTO diagnostic_symptoms (animal_type, symptom) VALUES
('cattle', 'Fever'),
('cattle', 'Lameness'),
('cattle', 'Loss of appetite'),
('cattle', 'Respiratory issues'),
('sheep', 'Fever'),
('sheep', 'Wool loss'),
('sheep', 'Lameness'),
('sheep', 'Coughing'),
('goat', 'Fever'),
('goat', 'Weight loss'),
('goat', 'Diarrhea'),
('goat', 'Coughing'),
('chicken', 'Respiratory issues'),
('chicken', 'Reduced egg production'),
('chicken', 'Diarrhea'),
('chicken', 'Loss of appetite');




CREATE TABLE symptoms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    animal_type VARCHAR(50) NOT NULL,
    symptom_name VARCHAR(100) NOT NULL
);

CREATE TABLE diagnoses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    animal_type VARCHAR(50) NOT NULL,
    symptom VARCHAR(100) NOT NULL,
    disease VARCHAR(100) NOT NULL
);

-- Insert sample symptoms
INSERT INTO symptoms (animal_type, symptom_name) VALUES
('cattle', 'Fever'),
('cattle', 'Lameness'),
('cattle', 'Loss of appetite'),
('cattle', 'Respiratory issues'),
('sheep', 'Fever'),
('sheep', 'Wool loss'),
('sheep', 'Lameness'),
('sheep', 'Coughing');

-- Insert sample diagnoses
INSERT INTO diagnoses (animal_type, symptom, disease) VALUES
('cattle', 'Fever', 'Foot and Mouth Disease'),
('cattle', 'Fever', 'Anthrax'),
('cattle', 'Lameness', 'Foot and Mouth Disease'),
('cattle', 'Loss of appetite', 'Bovine Tuberculosis'),
('cattle', 'Respiratory issues', 'Bovine Tuberculosis');