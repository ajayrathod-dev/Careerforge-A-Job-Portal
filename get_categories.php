<?php
require_once 'connection.php';

header('Content-Type: application/json');

if (isset($_GET['industry'])) {
    $industry = $_GET['industry'];
    
    $categories = [];
    
    switch ($industry) {
        case 'CS':
            $categories = [
                ['id' => 'cs_programming', 'name' => 'Programming Languages'],
                ['id' => 'cs_tech_stacks', 'name' => 'Tech Stacks'],
            ];
            break;
        case 'IT':
            $categories = [
                ['id' => 'it_specializations', 'name' => 'IT Specializations / Roles'],
                ['id' => 'it_technical_skills', 'name' => 'IT Technical Skills / Tools'],
            ];
            break;
        case 'EXTC':
            $categories = [
                ['id' => 'extc_specializations', 'name' => 'Electronics & Telecommunication Specializations'],
                ['id' => 'extc_technical_skills', 'name' => 'Electronics & Telecommunication Technical Skills / Tools'],
            ];
            break;
        case 'Mechanical':
            $categories = [
                ['id' => 'mech_specializations', 'name' => 'Mechanical Engineering Specializations'],
                ['id' => 'mech_technical_skills', 'name' => 'Mechanical Engineering Technical Skills / Tools'],
            ];
            break;
        case 'MFG':
            $categories = [
                ['id' => 'mfg_specializations', 'name' => 'Manufacturing Specializations'],
                ['id' => 'mfg_technical_skills', 'name' => 'Manufacturing Technical Skills & Tools'],
            ];
            break;
        default:
            $categories = [];
    }
    
    echo json_encode(['categories' => $categories]);
} else {
    echo json_encode(['categories' => []]);
}
?>