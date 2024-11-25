<?php
ini_set('display_errors', 1);

// Database connection
require_once('../pdo_connect.php');

// Initialize variables
$searchTerm = '';
$searchType = '';
$result = [];
$error = '';
$searchTypes = [
    // Hospital Searches
    'hospital_name' => 'Search Hospitals by Name',
    'hospital_location' => 'Search Hospitals by Location (State/ZIP)',
    'hospital_specialty' => 'Search Hospitals by Healthcare Provider Specialty',
    
    // Patient Searches
    'patient_name' => 'Search Patients by Name',
    'patient_location' => 'Search Patients by Location (State/ZIP)',
    'patient_phone' => 'Search Patients by Phone Number',
    
    // Healthcare Provider Searches
    'provider_name' => 'Search Healthcare Providers by Name',
    'provider_specialty' => 'Search Healthcare Providers by Specialty',
    
    // Medication Searches
    'medication_name' => 'Search Medications by Name',
    'medication_side_effects' => 'Search Medications by Side Effects',
    
    // Visit Searches
    'visit_reason' => 'Search Visits by Reason',
    'visit_diagnosis' => 'Search Visits by Diagnosis',
    
    // Insurance Searches
    'insurance_name' => 'Search Insurance by Name',
    'insurance_location' => 'Search Insurance by Location (City/ZIP)'
];

// Handle form submission
if(isset($_GET['submit'])) {
    try {
        // Sanitize and validate input
        $searchTerm = trim($_GET['search_term']);
        $searchType = $_GET['search_type'];

        // Prepare dynamic SQL based on search type
        switch($searchType) {
            // Hospital Searches
            case 'hospital_name':
                $sql = "SELECT * FROM Hospital WHERE Name LIKE :searchTerm";
                $searchTerm = '%' . $searchTerm . '%';
                break;
            case 'hospital_location':
                $sql = "SELECT * FROM Hospital WHERE State LIKE :searchTerm OR ZIP LIKE :searchTerm";
                $searchTerm = '%' . $searchTerm . '%';
                break;
            case 'hospital_specialty':
                $sql = "SELECT DISTINCT h.* FROM Hospital h 
                        JOIN Health_Care_Provider hcp ON h.Hospital_ID = hcp.Hospital_ID 
                        WHERE hcp.Specialty LIKE :searchTerm";
                $searchTerm = '%' . $searchTerm . '%';
                break;
            
            // Patient Searches
            case 'patient_name':
                $sql = "SELECT * FROM Patient WHERE Patient_FN LIKE :searchTerm OR Patient_LN LIKE :searchTerm";
                $searchTerm = '%' . $searchTerm . '%';
                break;
            case 'patient_location':
                $sql = "SELECT * FROM Patient WHERE State LIKE :searchTerm OR ZIP LIKE :searchTerm";
                $searchTerm = '%' . $searchTerm . '%';
                break;
            case 'patient_phone':
                $sql = "SELECT * FROM Patient WHERE Phone LIKE :searchTerm";
                $searchTerm = '%' . $searchTerm . '%';
                break;
            
            // Healthcare Provider Searches
            case 'provider_name':
                $sql = "SELECT * FROM Health_Care_Provider WHERE EmpFN LIKE :searchTerm OR EmpLN LIKE :searchTerm";
                $searchTerm = '%' . $searchTerm . '%';
                break;
            case 'provider_specialty':
                $sql = "SELECT * FROM Health_Care_Provider WHERE Specialty LIKE :searchTerm";
                $searchTerm = '%' . $searchTerm . '%';
                break;
            
            // Medication Searches
            case 'medication_name':
                $sql = "SELECT * FROM Medications WHERE Name LIKE :searchTerm";
                $searchTerm = '%' . $searchTerm . '%';
                break;
            case 'medication_side_effects':
                $sql = "SELECT * FROM Medications WHERE Side_Effects LIKE :searchTerm";
                $searchTerm = '%' . $searchTerm . '%';
                break;
            
            // Visit Searches
            case 'visit_reason':
                $sql = "SELECT v.*, h.Name AS Hospital_Name FROM Visit v 
                        JOIN Hospital h ON v.Hospital_ID = h.Hospital_ID 
                        WHERE v.Reason_for_visit LIKE :searchTerm";
                $searchTerm = '%' . $searchTerm . '%';
                break;
            case 'visit_diagnosis':
                $sql = "SELECT v.*, h.Name AS Hospital_Name FROM Visit v 
                        JOIN Hospital h ON v.Hospital_ID = h.Hospital_ID 
                        WHERE v.Diagnosis LIKE :searchTerm";
                $searchTerm = '%' . $searchTerm . '%';
                break;
            
            // Insurance Searches
            case 'insurance_name':
                $sql = "SELECT * FROM Health_Insurance WHERE Name LIKE :searchTerm";
                $searchTerm = '%' . $searchTerm . '%';
                break;
            case 'insurance_location':
                $sql = "SELECT * FROM Health_Insurance WHERE City LIKE :searchTerm OR ZIP LIKE :searchTerm";
                $searchTerm = '%' . $searchTerm . '%';
                break;
            
            default:
                throw new Exception("Invalid search type");
        }

        // Prepare and execute statement
        $stmt = $dbc->prepare($sql);
        $stmt->bindParam(':searchTerm', $searchTerm);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Check if results found
        if (empty($result)) {
            $error = "No results found matching your search criteria.";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Comprehensive Healthcare Search</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-blue-50">
    <div class="container mx-auto p-6">
        <h2 class="text-3xl font-bold text-blue-800 mb-6">Healthcare Database Search</h2>
        
        <form method="get" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="search_term">
                    Search Term
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                       id="search_term" name="search_term" type="text" 
                       value="<?php echo htmlspecialchars($searchTerm); ?>" 
                       placeholder="Enter search term">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="search_type">
                    Search Type
                </label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                        id="search_type" name="search_type">
                    <?php foreach($searchTypes as $type => $label): ?>
                        <option value="<?php echo $type; ?>" 
                                <?php echo ($searchType === $type ? 'selected' : ''); ?>>
                            <?php echo htmlspecialchars($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                        type="submit" name="submit">
                    Search
                </button>
            </div>
        </form>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($result)): ?>
            <div class="overflow-x-auto">
                <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
                    <thead class="bg-blue-100">
                        <tr>
                            <?php 
                            // Dynamically generate table headers based on first result
                            $headers = array_keys($result[0]);
                            foreach($headers as $header): 
                            ?>
                                <th class="p-3 text-left text-blue-900"><?php echo htmlspecialchars(str_replace('_', ' ', $header)); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($result as $row): ?>
                            <tr class="border-b border-blue-200 hover:bg-blue-50">
                                <?php foreach($row as $value): ?>
                                    <td class="p-3"><?php echo htmlspecialchars($value); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>