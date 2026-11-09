<?php
require_once 'connection.php';

header('Content-Type: application/json');

if (isset($_GET['subcategory_id'])) {
    $subcategory_id = $_GET['subcategory_id'];
    
    $skills = [];
    
    switch ($subcategory_id) {
        // CS - Programming Languages - Core / General Purpose
        case 'cs_core_general':
            $skills = [
                ['id' => 1, 'name' => 'C'],
                ['id' => 2, 'name' => 'C++'],
                ['id' => 3, 'name' => 'C#'],
                ['id' => 4, 'name' => 'Java'],
                ['id' => 5, 'name' => 'Python'],
                ['id' => 6, 'name' => 'Go (Golang)'],
                ['id' => 7, 'name' => 'Rust'],
            ];
            break;
            
        // CS - Programming Languages - Web / Scripting
        case 'cs_web_scripting':
            $skills = [
                ['id' => 8, 'name' => 'JavaScript'],
                ['id' => 9, 'name' => 'TypeScript'],
                ['id' => 10, 'name' => 'PHP'],
                ['id' => 11, 'name' => 'Ruby'],
                ['id' => 12, 'name' => 'Bash / Shell'],
                ['id' => 13, 'name' => 'PowerShell'],
                ['id' => 14, 'name' => 'Perl'],
            ];
            break;
            
        // CS - Programming Languages - Mobile Development
        case 'cs_mobile_dev':
            $skills = [
                ['id' => 15, 'name' => 'Swift'],
                ['id' => 16, 'name' => 'Objective-C'],
                ['id' => 17, 'name' => 'Kotlin'],
                ['id' => 18, 'name' => 'Dart'],
            ];
            break;
            
        // CS - Programming Languages - Data Science
        case 'cs_data_science':
            $skills = [
                ['id' => 19, 'name' => 'R'],
                ['id' => 20, 'name' => 'MATLAB'],
                ['id' => 21, 'name' => 'Julia'],
                ['id' => 22, 'name' => 'Scala'],
            ];
            break;
            
        // CS - Programming Languages - Database
        case 'cs_database':
            $skills = [
                ['id' => 23, 'name' => 'SQL (MySQL, PostgreSQL etc.)'],
                ['id' => 24, 'name' => 'PL/SQL'],
                ['id' => 25, 'name' => 'GraphQL'],
            ];
            break;
            
        // CS - Programming Languages - Functional
        case 'cs_functional':
            $skills = [
                ['id' => 26, 'name' => 'Haskell'],
                ['id' => 27, 'name' => 'F#'],
                ['id' => 28, 'name' => 'Lisp / Clojure'],
                ['id' => 29, 'name' => 'Erlang / Elixir'],
                ['id' => 30, 'name' => 'Assembly Language'],
            ];
            break;
            
        // CS - Tech Stacks - Frontend
        case 'cs_frontend':
            $skills = [
                ['id' => 31, 'name' => 'React.js'],
                ['id' => 32, 'name' => 'Angular'],
                ['id' => 33, 'name' => 'Vue.js'],
                ['id' => 34, 'name' => 'Svelte'],
                ['id' => 35, 'name' => 'jQuery'],
                ['id' => 36, 'name' => 'Bootstrap / Tailwind CSS'],
            ];
            break;
            
        // CS - Tech Stacks - Backend
        case 'cs_backend':
            $skills = [
                ['id' => 37, 'name' => 'Node.js'],
                ['id' => 38, 'name' => 'Express.js'],
                ['id' => 39, 'name' => 'Django (Python)'],
                ['id' => 40, 'name' => 'Flask (Python)'],
                ['id' => 41, 'name' => 'Spring / Spring Boot (Java)'],
                ['id' => 42, 'name' => '.NET Core / ASP.NET (C#)'],
                ['id' => 43, 'name' => 'Ruby on Rails (Ruby)'],
                ['id' => 44, 'name' => 'Laravel (PHP)'],
                ['id' => 45, 'name' => 'Phoenix (Elixir)'],
                ['id' => 46, 'name' => 'FastAPI (Python)'],
            ];
            break;
            
        // CS - Tech Stacks - Mobile Frameworks
        case 'cs_mobile_frameworks':
            $skills = [
                ['id' => 47, 'name' => 'React Native'],
                ['id' => 48, 'name' => 'Flutter'],
                ['id' => 49, 'name' => 'Xamarin'],
            ];
            break;
            
        // CS - Tech Stacks - Databases
        case 'cs_databases':
            $skills = [
                ['id' => 50, 'name' => 'MySQL'],
                ['id' => 51, 'name' => 'PostgreSQL'],
                ['id' => 52, 'name' => 'Oracle Database'],
                ['id' => 53, 'name' => 'Microsoft SQL Server'],
                ['id' => 54, 'name' => 'MongoDB'],
                ['id' => 55, 'name' => 'Cassandra'],
                ['id' => 56, 'name' => 'Redis'],
                ['id' => 57, 'name' => 'Firebase'],
            ];
            break;
            
        // CS - Tech Stacks - Cloud/DevOps
        case 'cs_cloud_devops':
            $skills = [
                ['id' => 58, 'name' => 'AWS (Amazon Web Services)'],
                ['id' => 59, 'name' => 'Microsoft Azure'],
                ['id' => 60, 'name' => 'Google Cloud Platform (GCP)'],
                ['id' => 61, 'name' => 'Docker'],
                ['id' => 62, 'name' => 'Kubernetes'],
                ['id' => 63, 'name' => 'Jenkins'],
                ['id' => 64, 'name' => 'Git / GitHub / GitLab'],
                ['id' => 65, 'name' => 'Terraform'],
            ];
            break;
            
        // CS - Tech Stacks - Project Management
        case 'cs_project_management':
            $skills = [
                ['id' => 66, 'name' => 'Jira'],
                ['id' => 67, 'name' => 'Trello'],
                ['id' => 68, 'name' => 'Asana'],
                ['id' => 69, 'name' => 'Confluence'],
                ['id' => 70, 'name' => 'Slack'],
                ['id' => 71, 'name' => 'Microsoft Teams'],
            ];
            break;
            
        // CS - Tech Stacks - Data/AI
        case 'cs_data_ai':
            $skills = [
                ['id' => 72, 'name' => 'TensorFlow'],
                ['id' => 73, 'name' => 'PyTorch'],
                ['id' => 74, 'name' => 'Scikit-learn'],
                ['id' => 75, 'name' => 'Hadoop / Spark'],
                ['id' => 76, 'name' => 'Power BI / Tableau'],
            ];
            break;
            
        // IT - Specializations - Software Development
        case 'it_software_dev':
            $skills = [
                ['id' => 77, 'name' => 'Web Development (Frontend, Backend, Full-Stack)'],
                ['id' => 78, 'name' => 'Mobile App Development (Android, iOS, Cross-platform)'],
                ['id' => 79, 'name' => 'Desktop / Enterprise Software Development'],
                ['id' => 80, 'name' => 'Embedded Systems / IoT Development'],
                ['id' => 81, 'name' => 'API & Integration Development'],
                ['id' => 82, 'name' => 'Cloud-Native Application Development'],
            ];
            break;
            
        // IT - Specializations - Data & Analytics
        case 'it_data_analytics':
            $skills = [
                ['id' => 83, 'name' => 'Data Engineering'],
                ['id' => 84, 'name' => 'Data Analysis & Visualization'],
                ['id' => 85, 'name' => 'Business Intelligence (BI)'],
                ['id' => 86, 'name' => 'Big Data Engineering'],
                ['id' => 87, 'name' => 'Data Science & Machine Learning'],
                ['id' => 88, 'name' => 'AI / Deep Learning Engineering'],
            ];
            break;
            
        // IT - Specializations - Systems & Infrastructure
        case 'it_systems_infra':
            $skills = [
                ['id' => 89, 'name' => 'IT Infrastructure / System Administration'],
                ['id' => 90, 'name' => 'Network Administration'],
                ['id' => 91, 'name' => 'DevOps / Site Reliability Engineering (SRE)'],
                ['id' => 92, 'name' => 'Cloud Infrastructure Engineering'],
                ['id' => 93, 'name' => 'Database Administration'],
                ['id' => 94, 'name' => 'Virtualization & Containerization (VMware, Docker, Kubernetes)'],
            ];
            break;
            
        // IT - Specializations - Cybersecurity
        case 'it_cybersecurity':
            $skills = [
                ['id' => 95, 'name' => 'Security Operations (SOC)'],
                ['id' => 96, 'name' => 'Network Security'],
                ['id' => 97, 'name' => 'Application Security'],
                ['id' => 98, 'name' => 'Cloud Security'],
                ['id' => 99, 'name' => 'Threat Hunting / Incident Response'],
                ['id' => 100, 'name' => 'Ethical Hacking / Penetration Testing'],
                ['id' => 101, 'name' => 'Security Compliance & Risk Management'],
            ];
            break;
            
        // IT - Specializations - Enterprise Solutions
        case 'it_enterprise':
            $skills = [
                ['id' => 102, 'name' => 'ERP / CRM Implementation & Support'],
                ['id' => 103, 'name' => 'SAP Functional / Technical (various modules)'],
                ['id' => 104, 'name' => 'Oracle / Microsoft Dynamics / Salesforce'],
                ['id' => 105, 'name' => 'Business Process Automation (RPA)'],
                ['id' => 106, 'name' => 'Low-Code / No-Code Development'],
            ];
            break;
            
        // IT - Specializations - Project Management
        case 'it_project_management':
            $skills = [
                ['id' => 107, 'name' => 'IT Project Management (Agile / Scrum / Waterfall)'],
                ['id' => 108, 'name' => 'Product Ownership / Product Management'],
                ['id' => 109, 'name' => 'Release Management'],
                ['id' => 110, 'name' => 'ITIL Service Management'],
            ];
            break;
            
        // IT - Specializations - Emerging Technologies
        case 'it_emerging_tech':
            $skills = [
                ['id' => 111, 'name' => 'Cloud & Edge Computing'],
                ['id' => 112, 'name' => 'Blockchain Development'],
                ['id' => 113, 'name' => 'AR / VR / Metaverse Solutions'],
                ['id' => 114, 'name' => 'Quantum Computing Basics'],
                ['id' => 115, 'name' => 'Chatbots & Conversational AI'],
            ];
            break;
            
        // IT - Technical Skills - Programming Languages
        case 'it_programming_lang':
            $skills = [
                ['id' => 116, 'name' => 'C, C++, Java'],
                ['id' => 117, 'name' => 'Python, R'],
                ['id' => 118, 'name' => 'JavaScript (Node.js, React, Angular, Vue)'],
                ['id' => 119, 'name' => 'PHP, Ruby, Go, Rust'],
                ['id' => 120, 'name' => '.NET / C#'],
                ['id' => 121, 'name' => 'Swift, Kotlin, Dart/Flutter'],
            ];
            break;
            
        // IT - Technical Skills - Databases
        case 'it_databases':
            $skills = [
                ['id' => 122, 'name' => 'SQL (MySQL, PostgreSQL, MS SQL Server, Oracle)'],
                ['id' => 123, 'name' => 'NoSQL (MongoDB, Cassandra, DynamoDB)'],
                ['id' => 124, 'name' => 'Graph Databases (Neo4j)'],
                ['id' => 125, 'name' => 'Data Warehousing (Snowflake, Redshift)'],
            ];
            break;
            
        // IT - Technical Skills - Cloud Platforms
        case 'it_cloud_platforms':
            $skills = [
                ['id' => 126, 'name' => 'AWS'],
                ['id' => 127, 'name' => 'Microsoft Azure'],
                ['id' => 128, 'name' => 'Google Cloud Platform (GCP)'],
                ['id' => 129, 'name' => 'IBM Cloud'],
                ['id' => 130, 'name' => 'Oracle Cloud'],
            ];
            break;
            
        // IT - Technical Skills - DevOps Tools
        case 'it_devops_tools':
            $skills = [
                ['id' => 131, 'name' => 'Git, GitHub, GitLab'],
                ['id' => 132, 'name' => 'Jenkins'],
                ['id' => 133, 'name' => 'Docker, Kubernetes'],
                ['id' => 134, 'name' => 'Terraform, Ansible'],
                ['id' => 135, 'name' => 'Helm, ArgoCD'],
            ];
            break;
            
        // IT - Technical Skills - Data & Analytics Tools
        case 'it_data_analytics_tools':
            $skills = [
                ['id' => 136, 'name' => 'Power BI, Tableau, Qlik'],
                ['id' => 137, 'name' => 'Hadoop Ecosystem (Hive, Spark)'],
                ['id' => 138, 'name' => 'Apache Kafka'],
                ['id' => 139, 'name' => 'Airflow'],
                ['id' => 140, 'name' => 'Scikit-Learn, TensorFlow, PyTorch'],
            ];
            break;
            
        // IT - Technical Skills - Cybersecurity Tools
        case 'it_cybersecurity_tools':
            $skills = [
                ['id' => 141, 'name' => 'SIEM (Splunk, QRadar)'],
                ['id' => 142, 'name' => 'Firewalls / IDS / IPS'],
                ['id' => 143, 'name' => 'Vulnerability Scanners (Nessus, Qualys)'],
                ['id' => 144, 'name' => 'Pen-Testing Tools (Metasploit, Burp Suite)'],
                ['id' => 145, 'name' => 'Endpoint Security (CrowdStrike, SentinelOne)'],
            ];
            break;
            
        // IT - Technical Skills - Collaboration Tools
        case 'it_collaboration_tools':
            $skills = [
                ['id' => 146, 'name' => 'Jira / Confluence'],
                ['id' => 147, 'name' => 'Microsoft Project'],
                ['id' => 148, 'name' => 'Trello / Asana'],
                ['id' => 149, 'name' => 'Slack / Teams'],
            ];
            break;
            
        // IT - Technical Skills - Testing Tools
        case 'it_testing_tools':
            $skills = [
                ['id' => 150, 'name' => 'Selenium'],
                ['id' => 151, 'name' => 'JMeter'],
                ['id' => 152, 'name' => 'Postman'],
                ['id' => 153, 'name' => 'Cypress'],
            ];
            break;
            
        // IT - Technical Skills - Low-Code Tools
        case 'it_lowcode_tools':
            $skills = [
                ['id' => 154, 'name' => 'UiPath'],
                ['id' => 155, 'name' => 'Automation Anywhere'],
                ['id' => 156, 'name' => 'PowerApps'],
                ['id' => 157, 'name' => 'Mendix / OutSystems'],
            ];
            break;
            
        // IT - Technical Skills - Emerging Tech
        case 'it_emerging_stacks':
            $skills = [
                ['id' => 158, 'name' => 'Blockchain (Ethereum / Hyperledger)'],
                ['id' => 159, 'name' => 'AR / VR SDKs (Unity, Unreal Engine)'],
                ['id' => 160, 'name' => 'Edge / IoT Platforms'],
            ];
            break;
            
        // EXTC - Specializations - Design & Development
        case 'extc_design_dev':
            $skills = [
                ['id' => 161, 'name' => 'Circuit Design (Analog / Digital)'],
                ['id' => 162, 'name' => 'PCB Design & Layout'],
                ['id' => 163, 'name' => 'Embedded Systems Development'],
                ['id' => 164, 'name' => 'FPGA / ASIC / VLSI Design'],
                ['id' => 165, 'name' => 'Microcontroller / Microprocessor Programming'],
                ['id' => 166, 'name' => 'IoT Device Development'],
                ['id' => 167, 'name' => 'Wireless Sensor Networks'],
                ['id' => 168, 'name' => 'RF / Microwave Design'],
                ['id' => 169, 'name' => 'Power Electronics'],
            ];
            break;
            
        // EXTC - Specializations - Testing & Validation
        case 'extc_testing':
            $skills = [
                ['id' => 170, 'name' => 'Hardware Testing & Debugging'],
                ['id' => 171, 'name' => 'Compliance Testing (EMC/EMI)'],
                ['id' => 172, 'name' => 'Signal Integrity Analysis'],
                ['id' => 173, 'name' => 'Environmental / Reliability Testing'],
            ];
            break;
            
        // EXTC - Specializations - Communications & Networking
        case 'extc_communications':
            $skills = [
                ['id' => 174, 'name' => 'Telecom Network Planning'],
                ['id' => 175, 'name' => 'RF Planning & Optimization'],
                ['id' => 176, 'name' => 'Cellular Technologies (2G / 3G / 4G / 5G)'],
                ['id' => 177, 'name' => 'Satellite Communication Systems'],
                ['id' => 178, 'name' => 'Optical Fiber Communication'],
                ['id' => 179, 'name' => 'Network Security'],
                ['id' => 180, 'name' => 'Protocol Development (TCP/IP, SIP, VoIP)'],
            ];
            break;
            
        // EXTC - Specializations - Manufacturing & Production
        case 'extc_manufacturing':
            $skills = [
                ['id' => 181, 'name' => 'Electronics Manufacturing (SMT / PCB Assembly)'],
                ['id' => 182, 'name' => 'Quality Assurance / Quality Control in Electronics'],
                ['id' => 183, 'name' => 'EHS for Electronics Manufacturing'],
                ['id' => 184, 'name' => 'Supply Chain Management for Electronics'],
            ];
            break;
            
        // EXTC - Specializations - Standards & Compliance
        case 'extc_standards':
            $skills = [
                ['id' => 185, 'name' => 'Regulatory Compliance (FCC, CE, RoHS)'],
                ['id' => 186, 'name' => 'ISO / IEC Standards Implementation'],
                ['id' => 187, 'name' => 'Safety & Reliability Engineering'],
            ];
            break;
            
        // EXTC - Specializations - Project / Leadership
        case 'extc_project_leadership':
            $skills = [
                ['id' => 188, 'name' => 'R&D Project Management'],
                ['id' => 189, 'name' => 'Product Lifecycle Management'],
                ['id' => 190, 'name' => 'Vendor / Supplier Development'],
                ['id' => 191, 'name' => 'Cost Optimization'],
            ];
            break;
            
        // EXTC - Technical Skills - Design Software
        case 'extc_design_software':
            $skills = [
                ['id' => 192, 'name' => 'Altium Designer'],
                ['id' => 193, 'name' => 'KiCad'],
                ['id' => 194, 'name' => 'OrCAD / Cadence Allegro'],
                ['id' => 195, 'name' => 'Mentor Graphics (PADS, Xpedition)'],
                ['id' => 196, 'name' => 'Eagle CAD'],
                ['id' => 197, 'name' => 'Proteus'],
                ['id' => 198, 'name' => 'MATLAB / Simulink'],
                ['id' => 199, 'name' => 'LabVIEW'],
                ['id' => 200, 'name' => 'PSPICE'],
                ['id' => 201, 'name' => 'HFSS (Ansys)'],
            ];
            break;
            
        // EXTC - Technical Skills - Embedded Systems
        case 'extc_embedded_systems':
            $skills = [
                ['id' => 202, 'name' => 'C / C++ for Embedded'],
                ['id' => 203, 'name' => 'Embedded Linux'],
                ['id' => 204, 'name' => 'RTOS (FreeRTOS, VxWorks)'],
                ['id' => 205, 'name' => 'Python for Embedded / IoT'],
                ['id' => 206, 'name' => 'Assembly Language'],
                ['id' => 207, 'name' => 'Verilog / VHDL'],
            ];
            break;
            
        // EXTC - Technical Skills - Test Tools
        case 'extc_test_tools':
            $skills = [
                ['id' => 208, 'name' => 'Oscilloscope'],
                ['id' => 209, 'name' => 'Logic Analyzer'],
                ['id' => 210, 'name' => 'Spectrum Analyzer'],
                ['id' => 211, 'name' => 'Network Analyzer'],
                ['id' => 212, 'name' => 'Signal Generators'],
                ['id' => 213, 'name' => 'JTAG / Boundary Scan Tools'],
            ];
            break;
            
        // EXTC - Technical Skills - Communication Technologies
        case 'extc_communication_tech':
            $skills = [
                ['id' => 214, 'name' => '5G / LTE / NB-IoT'],
                ['id' => 215, 'name' => 'Wi-Fi / Bluetooth / ZigBee'],
                ['id' => 216, 'name' => 'LoRa / LoRaWAN'],
                ['id' => 217, 'name' => 'Ethernet / TCP/IP'],
                ['id' => 218, 'name' => 'MPLS / SDN / NFV'],
            ];
            break;
            
        // EXTC - Technical Skills - IoT Integration
        case 'extc_iot_cloud':
            $skills = [
                ['id' => 219, 'name' => 'AWS IoT Core'],
                ['id' => 220, 'name' => 'Azure IoT Hub'],
                ['id' => 221, 'name' => 'Google Cloud IoT'],
                ['id' => 222, 'name' => 'MQTT / CoAP Protocols'],
            ];
            break;
            
        // EXTC - Technical Skills - Project Tools
        case 'extc_project_tools':
            $skills = [
                ['id' => 223, 'name' => 'Jira / Trello'],
                ['id' => 224, 'name' => 'Confluence'],
                ['id' => 225, 'name' => 'Microsoft Project / Primavera'],
                ['id' => 226, 'name' => 'Git / GitHub / GitLab'],
            ];
            break;
            
        // Mechanical - Specializations - Design & Development
        case 'mech_design_dev':
            $skills = [
                ['id' => 227, 'name' => 'Product Design & Development'],
                ['id' => 228, 'name' => 'CAD / 3D Modelling'],
                ['id' => 229, 'name' => 'Mechanical Component Design'],
                ['id' => 230, 'name' => 'Tooling & Fixture Design'],
                ['id' => 231, 'name' => 'Prototype Development'],
                ['id' => 232, 'name' => 'Reverse Engineering'],
            ];
            break;
            
        // Mechanical - Specializations - Analysis & Simulation
        case 'mech_analysis_sim':
            $skills = [
                ['id' => 233, 'name' => 'Finite Element Analysis (FEA)'],
                ['id' => 234, 'name' => 'Computational Fluid Dynamics (CFD)'],
                ['id' => 235, 'name' => 'Thermal / Heat Transfer Analysis'],
                ['id' => 236, 'name' => 'Vibration Analysis'],
                ['id' => 237, 'name' => 'Stress & Fatigue Analysis'],
            ];
            break;
            
        // Mechanical - Specializations - Manufacturing
        case 'mech_manufacturing':
            $skills = [
                ['id' => 238, 'name' => 'Production Planning & Control'],
                ['id' => 239, 'name' => 'CNC Programming & Machining'],
                ['id' => 240, 'name' => 'Process Planning & Optimization'],
                ['id' => 241, 'name' => 'Lean Manufacturing / Six Sigma'],
                ['id' => 242, 'name' => 'Casting / Forging / Welding Processes'],
                ['id' => 243, 'name' => 'Additive Manufacturing / 3D Printing'],
            ];
            break;
            
        // Mechanical - Specializations - Quality
        case 'mech_quality':
            $skills = [
                ['id' => 244, 'name' => 'Quality Assurance / Quality Control'],
                ['id' => 245, 'name' => 'ISO / ASME / ASTM Standards'],
                ['id' => 246, 'name' => 'Non-Destructive Testing (NDT)'],
                ['id' => 247, 'name' => 'Root Cause Analysis / CAPA'],
                ['id' => 248, 'name' => 'Reliability Engineering'],
            ];
            break;
            
        // Mechanical - Specializations - Maintenance
        case 'mech_maintenance':
            $skills = [
                ['id' => 249, 'name' => 'Preventive & Predictive Maintenance'],
                ['id' => 250, 'name' => 'Plant / Asset Management'],
                ['id' => 251, 'name' => 'MTBF / MTTR Tracking'],
                ['id' => 252, 'name' => 'Industrial Safety'],
            ];
            break;
            
        // Mechanical - Specializations - Materials
        case 'mech_materials':
            $skills = [
                ['id' => 253, 'name' => 'Material Selection & Testing'],
                ['id' => 254, 'name' => 'Procurement & Vendor Development'],
                ['id' => 255, 'name' => 'Supply Chain Management'],
            ];
            break;
            
        // Mechanical - Specializations - Sector Expertise
        case 'mech_sector_expertise':
            $skills = [
                ['id' => 256, 'name' => 'Automotive Engineering'],
                ['id' => 257, 'name' => 'Aerospace Engineering'],
                ['id' => 258, 'name' => 'HVAC / Refrigeration'],
                ['id' => 259, 'name' => 'Power Plants / Energy Systems'],
                ['id' => 260, 'name' => 'Oil & Gas / Petrochemical'],
                ['id' => 261, 'name' => 'Heavy Machinery / Industrial Equipment'],
            ];
            break;
            
        // Mechanical - Specializations - Leadership
        case 'mech_leadership':
            $skills = [
                ['id' => 262, 'name' => 'Mechanical Project Management'],
                ['id' => 263, 'name' => 'Cost Reduction / Value Engineering'],
                ['id' => 264, 'name' => 'Change Management'],
                ['id' => 265, 'name' => 'Sustainability / Green Engineering'],
            ];
            break;
            
        // Mechanical - Technical Skills - CAD Software
        case 'mech_cad_software':
            $skills = [
                ['id' => 266, 'name' => 'AutoCAD'],
                ['id' => 267, 'name' => 'SolidWorks'],
                ['id' => 268, 'name' => 'CATIA'],
                ['id' => 269, 'name' => 'Creo (Pro/ENGINEER)'],
                ['id' => 270, 'name' => 'Siemens NX'],
                ['id' => 271, 'name' => 'Inventor'],
                ['id' => 272, 'name' => 'Rhino 3D'],
            ];
            break;
            
        // Mechanical - Technical Skills - Analysis Software
        case 'mech_analysis_software':
            $skills = [
                ['id' => 273, 'name' => 'ANSYS Mechanical'],
                ['id' => 274, 'name' => 'Abaqus'],
                ['id' => 275, 'name' => 'HyperMesh'],
                ['id' => 276, 'name' => 'NASTRAN / PATRAN'],
                ['id' => 277, 'name' => 'STAR-CCM+'],
                ['id' => 278, 'name' => 'COMSOL Multiphysics'],
            ];
            break;
            
        // Mechanical - Technical Skills - Manufacturing Tools
        case 'mech_manufacturing_tools':
            $skills = [
                ['id' => 279, 'name' => 'Mastercam'],
                ['id' => 280, 'name' => 'Delcam / PowerMILL'],
                ['id' => 281, 'name' => 'Edgecam'],
                ['id' => 282, 'name' => 'G-code Programming'],
                ['id' => 283, 'name' => 'CAMWorks'],
            ];
            break;
            
        // Mechanical - Technical Skills - PLM/ERP
        case 'mech_plm_erp':
            $skills = [
                ['id' => 284, 'name' => 'SAP (PP, PM modules)'],
                ['id' => 285, 'name' => 'Teamcenter (Siemens PLM)'],
                ['id' => 286, 'name' => 'Windchill (PTC)'],
                ['id' => 287, 'name' => 'Oracle E-Business Suite'],
            ];
            break;
            
        // Mechanical - Technical Skills - Project Tools
        case 'mech_project_tools':
            $skills = [
                ['id' => 288, 'name' => 'Microsoft Project'],
                ['id' => 289, 'name' => 'Primavera P6'],
                ['id' => 290, 'name' => 'Jira / Trello'],
                ['id' => 291, 'name' => 'Confluence'],
                ['id' => 292, 'name' => 'Git / GitHub (for documentation / scripts)'],
            ];
            break;
            
        // Mechanical - Technical Skills - Measurement Tools
        case 'mech_measurement_tools':
            $skills = [
                ['id' => 293, 'name' => 'Coordinate Measuring Machine (CMM)'],
                ['id' => 294, 'name' => 'GD&T Tools'],
                ['id' => 295, 'name' => 'SPC / Minitab'],
            ];
            break;
            
        // Mechanical - Technical Skills - Emerging Tech
        case 'mech_emerging_tech':
            $skills = [
                ['id' => 296, 'name' => 'Digital Twin'],
                ['id' => 297, 'name' => 'IoT in Mechanical Systems'],
                ['id' => 298, 'name' => 'Additive Manufacturing (Metal 3D Printing)'],
            ];
            break;
            
        // Manufacturing - Specializations - Production
        case 'mfg_production':
            $skills = [
                ['id' => 299, 'name' => 'Production Planning & Control (PPC)'],
                ['id' => 300, 'name' => 'Shop Floor Management'],
                ['id' => 301, 'name' => 'Assembly Line Management'],
                ['id' => 302, 'name' => 'Lean Manufacturing'],
                ['id' => 303, 'name' => 'Six Sigma / Continuous Improvement'],
                ['id' => 304, 'name' => '5S / Kaizen Implementation'],
                ['id' => 305, 'name' => 'TPM (Total Productive Maintenance)'],
                ['id' => 306, 'name' => 'OEE / Productivity Improvement'],
            ];
            break;
            
        // Manufacturing - Specializations - Process & Quality
        case 'mfg_process_quality':
            $skills = [
                ['id' => 307, 'name' => 'Process Engineering'],
                ['id' => 308, 'name' => 'Quality Assurance (QA)'],
                ['id' => 309, 'name' => 'Quality Control (QC)'],
                ['id' => 310, 'name' => 'ISO Standards Implementation (ISO 9001, 14001 etc.)'],
                ['id' => 311, 'name' => 'Statistical Process Control (SPC)'],
                ['id' => 312, 'name' => 'Non-Destructive Testing (NDT)'],
                ['id' => 313, 'name' => 'Root Cause Analysis / CAPA'],
            ];
            break;
            
        // Manufacturing - Specializations - Design & Engineering
        case 'mfg_design_eng':
            $skills = [
                ['id' => 314, 'name' => 'Product Design & Development'],
                ['id' => 315, 'name' => 'CAD/CAM Design'],
                ['id' => 316, 'name' => 'Tooling / Die Design'],
                ['id' => 317, 'name' => 'R&D (Research & Development)'],
                ['id' => 318, 'name' => 'Prototyping'],
            ];
            break;
            
        // Manufacturing - Specializations - Maintenance
        case 'mfg_maintenance':
            $skills = [
                ['id' => 319, 'name' => 'Preventive Maintenance'],
                ['id' => 320, 'name' => 'Predictive Maintenance'],
                ['id' => 321, 'name' => 'Reliability Engineering'],
                ['id' => 322, 'name' => 'Maintenance Planning (MTBF / MTTR tracking)'],
            ];
            break;
            
        // Manufacturing - Specializations - Materials
        case 'mfg_materials':
            $skills = [
                ['id' => 323, 'name' => 'Material Planning & Procurement'],
                ['id' => 324, 'name' => 'Inventory Management'],
                ['id' => 325, 'name' => 'Supply Chain Management'],
                ['id' => 326, 'name' => 'Vendor Development'],
                ['id' => 327, 'name' => 'Logistics Coordination'],
            ];
            break;
            
        // Manufacturing - Specializations - Safety
        case 'mfg_safety':
            $skills = [
                ['id' => 328, 'name' => 'EHS (Environment, Health & Safety)'],
                ['id' => 329, 'name' => 'Industrial Safety'],
                ['id' => 330, 'name' => 'Regulatory Compliance'],
                ['id' => 331, 'name' => 'Sustainability / Green Manufacturing'],
            ];
            break;
            
        // Manufacturing - Specializations - Digital Manufacturing
        case 'mfg_digital':
            $skills = [
                ['id' => 332, 'name' => 'Industrial Automation'],
                ['id' => 333, 'name' => 'PLC / SCADA Programming'],
                ['id' => 334, 'name' => 'IoT in Manufacturing'],
                ['id' => 335, 'name' => 'Manufacturing Execution Systems (MES)'],
                ['id' => 336, 'name' => 'ERP Implementation (SAP, Oracle, etc.)'],
            ];
            break;
            
        // Manufacturing - Specializations - Sector Expertise
        case 'mfg_sector_expertise':
            $skills = [
                ['id' => 337, 'name' => 'Automotive Manufacturing'],
                ['id' => 338, 'name' => 'Aerospace Manufacturing'],
                ['id' => 339, 'name' => 'Heavy Engineering / Capital Goods'],
                ['id' => 340, 'name' => 'Electronics / Semiconductor Manufacturing'],
                ['id' => 341, 'name' => 'Textile / Apparel Manufacturing'],
                ['id' => 342, 'name' => 'FMCG / Food Processing'],
                ['id' => 343, 'name' => 'Pharma / Chemical Manufacturing'],
            ];
            break;
            
        // Manufacturing - Specializations - Leadership
        case 'mfg_leadership':
            $skills = [
                ['id' => 344, 'name' => 'Plant Management'],
                ['id' => 345, 'name' => 'Project Management in Manufacturing'],
                ['id' => 346, 'name' => 'Change Management'],
                ['id' => 347, 'name' => 'Cost Reduction / Value Engineering'],
            ];
            break;
            
        // Manufacturing - Technical Skills - Design Software
        case 'mfg_design_software':
            $skills = [
                ['id' => 348, 'name' => 'AutoCAD'],
                ['id' => 349, 'name' => 'SolidWorks'],
                ['id' => 350, 'name' => 'CATIA'],
                ['id' => 351, 'name' => 'Creo (Pro/ENGINEER)'],
                ['id' => 352, 'name' => 'NX (Siemens)'],
                ['id' => 353, 'name' => 'ANSYS'],
                ['id' => 354, 'name' => 'CAMWorks'],
                ['id' => 355, 'name' => 'Mastercam'],
            ];
            break;
            
        // Manufacturing - Technical Skills - ERP/MES
        case 'mfg_erp_mes':
            $skills = [
                ['id' => 356, 'name' => 'SAP (PP, MM, QM, PM modules)'],
                ['id' => 357, 'name' => 'Oracle E-Business Suite'],
                ['id' => 358, 'name' => 'Microsoft Dynamics'],
                ['id' => 359, 'name' => 'Infor LN'],
                ['id' => 360, 'name' => 'Epicor'],
                ['id' => 361, 'name' => 'Siemens Opcenter (MES)'],
                ['id' => 362, 'name' => 'Rockwell FactoryTalk'],
            ];
            break;
            
        // Manufacturing - Technical Skills - PLC/Automation
        case 'mfg_plc_automation':
            $skills = [
                ['id' => 363, 'name' => 'Siemens PLC'],
                ['id' => 364, 'name' => 'Allen-Bradley / Rockwell'],
                ['id' => 365, 'name' => 'Mitsubishi PLC'],
                ['id' => 366, 'name' => 'Schneider Electric PLC'],
                ['id' => 367, 'name' => 'ABB PLC'],
                ['id' => 368, 'name' => 'Omron PLC'],
                ['id' => 369, 'name' => 'SCADA Systems (Wonderware, WinCC, iFIX)'],
            ];
            break;
            
        // Manufacturing - Technical Skills - Quality Tools
        case 'mfg_quality_tools':
            $skills = [
                ['id' => 370, 'name' => 'Minitab'],
                ['id' => 371, 'name' => 'SPC Software'],
                ['id' => 372, 'name' => 'QMS Systems'],
                ['id' => 373, 'name' => 'FMEA Tools'],
                ['id' => 374, 'name' => '8D Problem Solving'],
            ];
            break;
            
        // Manufacturing - Technical Skills - Project Tools
        case 'mfg_project_tools':
            $skills = [
                ['id' => 375, 'name' => 'Microsoft Project'],
                ['id' => 376, 'name' => 'Primavera P6'],
                ['id' => 377, 'name' => 'Jira / Trello'],
                ['id' => 378, 'name' => 'Confluence'],
            ];
            break;
            
        // Manufacturing - Technical Skills - IoT/Industry4
        case 'mfg_iot_industry4':
            $skills = [
                ['id' => 379, 'name' => 'ThingWorx'],
                ['id' => 380, 'name' => 'Azure IoT'],
                ['id' => 381, 'name' => 'AWS IoT Core'],
                ['id' => 382, 'name' => 'Ignition (Inductive Automation)'],
            ];
            break;
            
        default:
            $skills = [];
    }
    
    echo json_encode(['skills' => $skills]);
} else {
    echo json_encode(['skills' => []]);
}
?>