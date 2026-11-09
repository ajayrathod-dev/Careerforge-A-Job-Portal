<?php
require_once 'connection.php';

header('Content-Type: application/json');

if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    
    $subcategories = [];
    
    switch ($category_id) {
        // CS - Programming Languages
        case 'cs_programming':
            $subcategories = [
                ['id' => 'cs_core_general', 'name' => 'Core / General Purpose'],
                ['id' => 'cs_web_scripting', 'name' => 'Web / Scripting'],
                ['id' => 'cs_mobile_dev', 'name' => 'Mobile Development'],
                ['id' => 'cs_data_science', 'name' => 'Data Science / Analytics / Academic'],
                ['id' => 'cs_database', 'name' => 'Database / Query Languages'],
                ['id' => 'cs_functional', 'name' => 'Functional / Specialised'],
            ];
            break;
            
        // CS - Tech Stacks
        case 'cs_tech_stacks':
            $subcategories = [
                ['id' => 'cs_frontend', 'name' => 'Front-End Frameworks / Libraries'],
                ['id' => 'cs_backend', 'name' => 'Back-End Frameworks'],
                ['id' => 'cs_mobile_frameworks', 'name' => 'Mobile Frameworks'],
                ['id' => 'cs_databases', 'name' => 'Databases'],
                ['id' => 'cs_cloud_devops', 'name' => 'Cloud / DevOps Platforms'],
                ['id' => 'cs_project_management', 'name' => 'Project Management / Collaboration Tools'],
                ['id' => 'cs_data_ai', 'name' => 'Data / AI Tools'],
            ];
            break;
            
        // IT - Specializations
        case 'it_specializations':
            $subcategories = [
                ['id' => 'it_software_dev', 'name' => 'Software Development & Programming'],
                ['id' => 'it_data_analytics', 'name' => 'Data & Analytics'],
                ['id' => 'it_systems_infra', 'name' => 'Systems & Infrastructure'],
                ['id' => 'it_cybersecurity', 'name' => 'Cybersecurity'],
                ['id' => 'it_enterprise', 'name' => 'Enterprise Solutions'],
                ['id' => 'it_project_management', 'name' => 'Project / Product / People Management'],
                ['id' => 'it_emerging_tech', 'name' => 'Emerging Technologies'],
            ];
            break;
            
        // IT - Technical Skills
        case 'it_technical_skills':
            $subcategories = [
                ['id' => 'it_programming_lang', 'name' => 'Programming Languages'],
                ['id' => 'it_databases', 'name' => 'Databases'],
                ['id' => 'it_cloud_platforms', 'name' => 'Cloud Platforms'],
                ['id' => 'it_devops_tools', 'name' => 'DevOps / CI-CD Tools'],
                ['id' => 'it_data_analytics_tools', 'name' => 'Data & Analytics Tools'],
                ['id' => 'it_cybersecurity_tools', 'name' => 'Cybersecurity Tools'],
                ['id' => 'it_collaboration_tools', 'name' => 'Collaboration & PM Tools'],
                ['id' => 'it_testing_tools', 'name' => 'Testing / QA Tools'],
                ['id' => 'it_lowcode_tools', 'name' => 'Low-Code / RPA Tools'],
                ['id' => 'it_emerging_stacks', 'name' => 'Emerging Tech Stacks'],
            ];
            break;
            
        // EXTC - Specializations
        case 'extc_specializations':
            $subcategories = [
                ['id' => 'extc_design_dev', 'name' => 'Design & Development'],
                ['id' => 'extc_testing', 'name' => 'Testing & Validation'],
                ['id' => 'extc_communications', 'name' => 'Communications & Networking'],
                ['id' => 'extc_manufacturing', 'name' => 'Manufacturing & Production'],
                ['id' => 'extc_standards', 'name' => 'Standards & Compliance'],
                ['id' => 'extc_project_leadership', 'name' => 'Project / Leadership'],
            ];
            break;
            
        // EXTC - Technical Skills
        case 'extc_technical_skills':
            $subcategories = [
                ['id' => 'extc_design_software', 'name' => 'Design & Simulation Software'],
                ['id' => 'extc_embedded_systems', 'name' => 'Embedded Systems / Programming Languages'],
                ['id' => 'extc_test_tools', 'name' => 'Test & Measurement Tools'],
                ['id' => 'extc_communication_tech', 'name' => 'Communication / Networking Technologies'],
                ['id' => 'extc_iot_cloud', 'name' => 'IoT / Cloud Integration'],
                ['id' => 'extc_project_tools', 'name' => 'Project / Collaboration Tools'],
            ];
            break;
            
        // Mechanical - Specializations
        case 'mech_specializations':
            $subcategories = [
                ['id' => 'mech_design_dev', 'name' => 'Design & Development'],
                ['id' => 'mech_analysis_sim', 'name' => 'Analysis & Simulation'],
                ['id' => 'mech_manufacturing', 'name' => 'Manufacturing / Production'],
                ['id' => 'mech_quality', 'name' => 'Quality & Standards'],
                ['id' => 'mech_maintenance', 'name' => 'Maintenance & Operations'],
                ['id' => 'mech_materials', 'name' => 'Materials & Supply Chain'],
                ['id' => 'mech_sector_expertise', 'name' => 'Sector-Specific Expertise'],
                ['id' => 'mech_leadership', 'name' => 'Leadership / Project Management'],
            ];
            break;
            
        // Mechanical - Technical Skills
        case 'mech_technical_skills':
            $subcategories = [
                ['id' => 'mech_cad_software', 'name' => 'Design / CAD Software'],
                ['id' => 'mech_analysis_software', 'name' => 'Analysis / Simulation Software'],
                ['id' => 'mech_manufacturing_tools', 'name' => 'Manufacturing & CNC Tools'],
                ['id' => 'mech_plm_erp', 'name' => 'PLM / ERP / MES Systems'],
                ['id' => 'mech_project_tools', 'name' => 'Project & Collaboration Tools'],
                ['id' => 'mech_measurement_tools', 'name' => 'Measurement / Quality Tools'],
                ['id' => 'mech_emerging_tech', 'name' => 'Emerging Tech'],
            ];
            break;
            
        // Manufacturing - Specializations
        case 'mfg_specializations':
            $subcategories = [
                ['id' => 'mfg_production', 'name' => 'Production & Operations'],
                ['id' => 'mfg_process_quality', 'name' => 'Process & Quality'],
                ['id' => 'mfg_design_eng', 'name' => 'Design & Engineering'],
                ['id' => 'mfg_maintenance', 'name' => 'Maintenance & Reliability'],
                ['id' => 'mfg_materials', 'name' => 'Materials & Supply Chain'],
                ['id' => 'mfg_safety', 'name' => 'Safety & Compliance'],
                ['id' => 'mfg_digital', 'name' => 'Technology & Digital Manufacturing'],
                ['id' => 'mfg_sector_expertise', 'name' => 'Sector-Specific Expertise'],
                ['id' => 'mfg_leadership', 'name' => 'Leadership & Management'],
            ];
            break;
            
        // Manufacturing - Technical Skills
        case 'mfg_technical_skills':
            $subcategories = [
                ['id' => 'mfg_design_software', 'name' => 'Design / Engineering Software'],
                ['id' => 'mfg_erp_mes', 'name' => 'ERP / MRP / MES Systems'],
                ['id' => 'mfg_plc_automation', 'name' => 'PLC / Automation Platforms'],
                ['id' => 'mfg_quality_tools', 'name' => 'Quality & Data Analysis Tools'],
                ['id' => 'mfg_project_tools', 'name' => 'Project / Collaboration Tools'],
                ['id' => 'mfg_iot_industry4', 'name' => 'IoT / Industry 4.0'],
            ];
            break;
            
        default:
            $subcategories = [];
    }
    
    echo json_encode(['subcategories' => $subcategories]);
} else {
    echo json_encode(['subcategories' => []]);
}
?>