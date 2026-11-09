<?php
// Start session and check authentication
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Database connection
require_once 'connection.php';

// Get user information if needed
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Engineering Categories - Job Agency">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <title>Engineering Categories | Job Agency Website</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/owl.css">

    <style>
        :root {
            --primary: #007bff;
            --secondary: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --light: #f8f9fa;
            --dark: #343a40;
        }

        .category-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
            text-align: center;
        }

        .category-tabs-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 40px;
        }

        .category-tabs {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 0;
        }

        .category-tabs .nav-link {
            border: none;
            padding: 20px 30px;
            font-weight: 600;
            color: #555;
            border-radius: 0;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .category-tabs .nav-link i {
            font-size: 1.2rem;
            width: 25px;
            text-align: center;
        }

        .category-tabs .nav-link.active {
            background: white;
            color: var(--primary);
            border-left: 4px solid var(--primary);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .category-tabs .nav-link:hover:not(.active) {
            background: rgba(255,255,255,0.7);
            color: var(--primary);
        }

        .category-content {
            padding: 40px;
            min-height: 600px;
            background: white;
        }

        .category-section {
            margin-bottom: 40px;
        }

        .section-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .section-header i {
            font-size: 1.5rem;
        }

        .skills-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .skill-category {
            background: var(--light);
            border-radius: 10px;
            padding: 20px;
            border-left: 4px solid var(--primary);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .skill-category:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .skill-category h5 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .skill-category h5 i {
            color: var(--success);
        }

        .skills-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .skills-list li {
            padding: 8px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 10px;
            transition: color 0.3s ease;
        }

        .skills-list li:before {
            content: "▸";
            color: var(--primary);
            font-weight: bold;
        }

        .skills-list li:hover {
            color: var(--primary);
        }

        .skills-list li:last-child {
            border-bottom: none;
        }

        .tech-badge {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin: 2px;
            transition: all 0.3s ease;
        }

        .tech-badge:hover {
            background: var(--dark);
            transform: scale(1.05);
        }

        .search-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-top: 4px solid var(--primary);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .stat-label {
            color: var(--secondary);
            font-weight: 500;
        }

        .category-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), #0056b3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .user-welcome {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .category-tabs .nav-link {
                padding: 15px 20px;
                font-size: 0.9rem;
            }
            
            .category-content {
                padding: 20px;
            }
            
            .skills-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .tab-pane {
            animation: fadeIn 0.3s ease-in;
        }
    </style>
</head>

<body>

    <!-- ***** Preloader Start ***** -->
    <div id="preloader">
        <div class="jumper">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>  

    <!-- Include Header -->
    <?php include 'header.php'; ?>

    <!-- Page Header -->
    <div class="category-header">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <h1>Engineering Domains & Skills</h1>
            <p class="lead">Explore comprehensive technology stacks and specialized skills across various engineering fields</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- User Welcome Message -->
        <div class="user-welcome">
            <h4><i class="fas fa-user-circle"></i> Welcome, <?php echo htmlspecialchars($username); ?>!</h4>
            <p class="mb-0">Explore engineering categories and enhance your skills</p>
        </div>

        <div class="row">
            <!-- Quick Stats -->
            <div class="col-12 mb-4">
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-number">200+</div>
                        <div class="stat-label">Technologies</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">5</div>
                        <div class="stat-label">Domains</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">Specializations</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">1000+</div>
                        <div class="stat-label">Skills</div>
                    </div>
                </div>
            </div>

            <!-- Search Box -->
            <div class="col-12 mb-4">
                <div class="search-box">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-lg" placeholder="Search technologies, skills, or tools...">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="category-tabs-container">
            <div class="row">
                <!-- Vertical Tabs -->
                <div class="col-lg-3">
                    <div class="category-tabs">
                        <ul class="nav flex-column" id="categoryTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="cs-tab" data-toggle="tab" href="#cs" role="tab">
                                    <i class="fas fa-laptop-code"></i>
                                    Computer Science
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="it-tab" data-toggle="tab" href="#it" role="tab">
                                    <i class="fas fa-server"></i>
                                    Information Technology
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="extc-tab" data-toggle="tab" href="#extc" role="tab">
                                    <i class="fas fa-satellite-dish"></i>
                                    Electronics & Telecom
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="mech-tab" data-toggle="tab" href="#mech" role="tab">
                                    <i class="fas fa-cogs"></i>
                                    Mechanical Engineering
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="mfg-tab" data-toggle="tab" href="#mfg" role="tab">
                                    <i class="fas fa-industry"></i>
                                    Manufacturing
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="col-lg-9">
                    <div class="tab-content category-content" id="categoryTabsContent">
                        
                        <!-- CS Category -->
                        <div class="tab-pane fade show active" id="cs" role="tabpanel">
                            <div class="category-section">
                                <div class="section-header">
                                    <i class="fas fa-laptop-code"></i>
                                    <h3 class="mb-0">Computer Science & Engineering</h3>
                                </div>
                                
                                <div class="skills-grid">
                                    <div class="skill-category">
                                        <h5><i class="fas fa-code"></i> Core Programming</h5>
                                        <ul class="skills-list">
                                            <li>Java</li>
                                            <li>Python</li>
                                            <li>C++</li>
                                            <li>JavaScript</li>
                                            <li>C#</li>
                                            <li>Go</li>
                                            <li>Rust</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-globe"></i> Web Technologies</h5>
                                        <ul class="skills-list">
                                            <li>React.js</li>
                                            <li>Node.js</li>
                                            <li>Angular</li>
                                            <li>Vue.js</li>
                                            <li>Django</li>
                                            <li>Spring Boot</li>
                                            <li>Express.js</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-database"></i> Databases</h5>
                                        <ul class="skills-list">
                                            <li>MySQL</li>
                                            <li>PostgreSQL</li>
                                            <li>MongoDB</li>
                                            <li>Redis</li>
                                            <li>Oracle</li>
                                            <li>SQL Server</li>
                                            <li>Cassandra</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-cloud"></i> Cloud & DevOps</h5>
                                        <ul class="skills-list">
                                            <li>AWS</li>
                                            <li>Azure</li>
                                            <li>Google Cloud</li>
                                            <li>Docker</li>
                                            <li>Kubernetes</li>
                                            <li>Jenkins</li>
                                            <li>Terraform</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-mobile"></i> Mobile Development</h5>
                                        <ul class="skills-list">
                                            <li>React Native</li>
                                            <li>Flutter</li>
                                            <li>Swift</li>
                                            <li>Kotlin</li>
                                            <li>Android</li>
                                            <li>iOS</li>
                                            <li>Xamarin</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-brain"></i> AI & Data Science</h5>
                                        <ul class="skills-list">
                                            <li>TensorFlow</li>
                                            <li>PyTorch</li>
                                            <li>Scikit-learn</li>
                                            <li>Pandas</li>
                                            <li>NumPy</li>
                                            <li>Hadoop</li>
                                            <li>Spark</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- IT Category -->
                        <div class="tab-pane fade" id="it" role="tabpanel">
                            <div class="category-section">
                                <div class="section-header">
                                    <i class="fas fa-server"></i>
                                    <h3 class="mb-0">Information Technology</h3>
                                </div>
                                
                                <div class="skills-grid">
                                    <div class="skill-category">
                                        <h5><i class="fas fa-shield-alt"></i> Cybersecurity</h5>
                                        <ul class="skills-list">
                                            <li>Network Security</li>
                                            <li>Ethical Hacking</li>
                                            <li>Security Operations</li>
                                            <li>Compliance</li>
                                            <li>Risk Management</li>
                                            <li>Incident Response</li>
                                            <li>Vulnerability Assessment</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-network-wired"></i> Infrastructure</h5>
                                        <ul class="skills-list">
                                            <li>System Administration</li>
                                            <li>Network Management</li>
                                            <li>Cloud Infrastructure</li>
                                            <li>Virtualization</li>
                                            <li>Storage Systems</li>
                                            <li>Backup Solutions</li>
                                            <li>Disaster Recovery</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-cogs"></i> Enterprise Systems</h5>
                                        <ul class="skills-list">
                                            <li>SAP Implementation</li>
                                            <li>Oracle ERP</li>
                                            <li>Microsoft Dynamics</li>
                                            <li>CRM Systems</li>
                                            <li>Business Intelligence</li>
                                            <li>Data Warehousing</li>
                                            <li>ETL Processes</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-project-diagram"></i> IT Management</h5>
                                        <ul class="skills-list">
                                            <li>ITIL Framework</li>
                                            <li>Agile Methodology</li>
                                            <li>Project Management</li>
                                            <li>Service Delivery</li>
                                            <li>Vendor Management</li>
                                            <li>Budget Planning</li>
                                            <li>Team Leadership</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-tools"></i> IT Operations</h5>
                                        <ul class="skills-list">
                                            <li>Help Desk Support</li>
                                            <li>Technical Support</li>
                                            <li>System Monitoring</li>
                                            <li>Performance Tuning</li>
                                            <li>Automation Scripts</li>
                                            <li>Documentation</li>
                                            <li>Training & Development</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-robot"></i> Emerging Technologies</h5>
                                        <ul class="skills-list">
                                            <li>Blockchain</li>
                                            <li>IoT Solutions</li>
                                            <li>AI Integration</li>
                                            <li>RPA Tools</li>
                                            <li>Low-Code Platforms</li>
                                            <li>AR/VR Development</li>
                                            <li>Edge Computing</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- EXTC Category -->
                        <div class="tab-pane fade" id="extc" role="tabpanel">
                            <div class="category-section">
                                <div class="section-header">
                                    <i class="fas fa-satellite-dish"></i>
                                    <h3 class="mb-0">Electronics & Telecommunication</h3>
                                </div>
                                
                                <div class="skills-grid">
                                    <div class="skill-category">
                                        <h5><i class="fas fa-microchip"></i> Hardware Design</h5>
                                        <ul class="skills-list">
                                            <li>PCB Design</li>
                                            <li>VLSI Design</li>
                                            <li>FPGA Development</li>
                                            <li>Embedded Systems</li>
                                            <li>Circuit Design</li>
                                            <li>Signal Processing</li>
                                            <li>Power Electronics</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-broadcast-tower"></i> Communication Systems</h5>
                                        <ul class="skills-list">
                                            <li>5G Technology</li>
                                            <li>Wireless Networks</li>
                                            <li>Satellite Communication</li>
                                            <li>Optical Fiber</li>
                                            <li>RF Engineering</li>
                                            <li>Network Protocols</li>
                                            <li>Telecom Standards</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-wifi"></i> Networking</h5>
                                        <ul class="skills-list">
                                            <li>TCP/IP Protocols</li>
                                            <li>Network Security</li>
                                            <li>VoIP Systems</li>
                                            <li>MPLS Networks</li>
                                            <li>SDN/NFV</li>
                                            <li>Network Optimization</li>
                                            <li>Quality of Service</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-tools"></i> Testing & Measurement</h5>
                                        <ul class="skills-list">
                                            <li>Oscilloscopes</li>
                                            <li>Spectrum Analyzers</li>
                                            <li>Signal Generators</li>
                                            <li>Network Analyzers</li>
                                            <li>EMC/EMI Testing</li>
                                            <li>Protocol Testing</li>
                                            <li>Quality Assurance</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-cube"></i> IoT & Embedded</h5>
                                        <ul class="skills-list">
                                            <li>Embedded C/C++</li>
                                            <li>RTOS</li>
                                            <li>IoT Protocols</li>
                                            <li>Sensor Networks</li>
                                            <li>Edge Computing</li>
                                            <li>Firmware Development</li>
                                            <li>Hardware Security</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-desktop"></i> Design Tools</h5>
                                        <ul class="skills-list">
                                            <li>MATLAB/Simulink</li>
                                            <li>Cadence Tools</li>
                                            <li>Altium Designer</li>
                                            <li>LabVIEW</li>
                                            <li>PSPICE</li>
                                            <li>VHDL/Verilog</li>
                                            <li>ANSYS HFSS</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mech Category -->
                        <div class="tab-pane fade" id="mech" role="tabpanel">
                            <div class="category-section">
                                <div class="section-header">
                                    <i class="fas fa-cogs"></i>
                                    <h3 class="mb-0">Mechanical Engineering</h3>
                                </div>
                                
                                <div class="skills-grid">
                                    <div class="skill-category">
                                        <h5><i class="fas fa-drafting-compass"></i> Design & CAD</h5>
                                        <ul class="skills-list">
                                            <li>SolidWorks</li>
                                            <li>AutoCAD</li>
                                            <li>CATIA</li>
                                            <li>Creo Parametric</li>
                                            <li>NX CAD</li>
                                            <li>Inventor</li>
                                            <li>GD&T</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-chart-line"></i> Analysis & Simulation</h5>
                                        <ul class="skills-list">
                                            <li>Finite Element Analysis</li>
                                            <li>Computational Fluid Dynamics</li>
                                            <li>Thermal Analysis</li>
                                            <li>Stress Analysis</li>
                                            <li>Vibration Analysis</li>
                                            <li>Multibody Dynamics</li>
                                            <li>Optimization</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-industry"></i> Manufacturing</h5>
                                        <ul class="skills-list">
                                            <li>CNC Programming</li>
                                            <li>Additive Manufacturing</li>
                                            <li>Lean Manufacturing</li>
                                            <li>Six Sigma</li>
                                            <li>Quality Control</li>
                                            <li>Process Planning</li>
                                            <li>Tool Design</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-car"></i> Automotive Systems</h5>
                                        <ul class="skills-list">
                                            <li>Engine Design</li>
                                            <li>Vehicle Dynamics</li>
                                            <li>Transmission Systems</li>
                                            <li>Braking Systems</li>
                                            <li>Suspension Design</li>
                                            <li>HVAC Systems</li>
                                            <li>NVH Analysis</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-wind"></i> Energy Systems</h5>
                                        <ul class="skills-list">
                                            <li>Thermodynamics</li>
                                            <li>Heat Transfer</li>
                                            <li>Fluid Mechanics</li>
                                            <li>Renewable Energy</li>
                                            <li>Power Plants</li>
                                            <li>HVAC Design</li>
                                            <li>Energy Efficiency</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-tools"></i> Materials & Testing</h5>
                                        <ul class="skills-list">
                                            <li>Material Science</li>
                                            <li>Metallurgy</li>
                                            <li>Composite Materials</li>
                                            <li>Non-Destructive Testing</li>
                                            <li>Failure Analysis</li>
                                            <li>Corrosion Engineering</li>
                                            <li>Standards Compliance</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mfg Category -->
                        <div class="tab-pane fade" id="mfg" role="tabpanel">
                            <div class="category-section">
                                <div class="section-header">
                                    <i class="fas fa-industry"></i>
                                    <h3 class="mb-0">Manufacturing Engineering</h3>
                                </div>
                                
                                <div class="skills-grid">
                                    <div class="skill-category">
                                        <h5><i class="fas fa-cogs"></i> Production Systems</h5>
                                        <ul class="skills-list">
                                            <li>Production Planning</li>
                                            <li>Lean Manufacturing</li>
                                            <li>Six Sigma</li>
                                            <li>Quality Management</li>
                                            <li>Supply Chain</li>
                                            <li>Inventory Control</li>
                                            <li>Cost Reduction</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-robot"></i> Automation</h5>
                                        <ul class="skills-list">
                                            <li>PLC Programming</li>
                                            <li>Robotics</li>
                                            <li>SCADA Systems</li>
                                            <li>Industrial IoT</li>
                                            <li>Machine Vision</li>
                                            <li>Process Control</li>
                                            <li>Digital Twin</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-tools"></i> Manufacturing Processes</h5>
                                        <ul class="skills-list">
                                            <li>Machining</li>
                                            <li>Welding</li>
                                            <li>Casting</li>
                                            <li>Forming</li>
                                            <li>Additive Manufacturing</li>
                                            <li>Assembly Lines</li>
                                            <li>Surface Treatment</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-chart-bar"></i> Quality Systems</h5>
                                        <ul class="skills-list">
                                            <li>Statistical Process Control</li>
                                            <li>ISO Standards</li>
                                            <li>Root Cause Analysis</li>
                                            <li>Metrology</li>
                                            <li>Calibration</li>
                                            <li>Audit Processes</li>
                                            <li>Continuous Improvement</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-database"></i> Manufacturing Software</h5>
                                        <ul class="skills-list">
                                            <li>SAP PP/MM</li>
                                            <li>MES Systems</li>
                                            <li>ERP Implementation</li>
                                            <li>CAD/CAM</li>
                                            <li>PLM Systems</li>
                                            <li>Simulation Software</li>
                                            <li>Data Analytics</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="skill-category">
                                        <h5><i class="fas fa-sync-alt"></i> Operations Management</h5>
                                        <ul class="skills-list">
                                            <li>Plant Management</li>
                                            <li>Maintenance Systems</li>
                                            <li>Safety Management</li>
                                            <li>Environmental Compliance</li>
                                            <li>Team Leadership</li>
                                            <li>Budget Management</li>
                                            <li>Project Management</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br><br><br><br>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Additional Scripts -->
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/owl.js"></script>
    <script src="assets/js/slick.js"></script>
    <script src="assets/js/accordions.js"></script>

    <script>
        $(document).ready(function() {
            // Smooth animations
            $('.nav-link').on('click', function() {
                $('.tab-pane').addClass('fade-in');
            });

            // Search functionality
            $('.search-box input').on('keyup', function() {
                var searchText = $(this).val().toLowerCase();
                if (searchText.length > 2) {
                    $('.skills-list li').each(function() {
                        var text = $(this).text().toLowerCase();
                        if (text.indexOf(searchText) !== -1) {
                            $(this).closest('.skill-category').show();
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                } else {
                    $('.skills-list li').show();
                    $('.skill-category').show();
                }
            });

            // Add hover effects
            $('.skill-category').hover(
                function() {
                    $(this).css('transform', 'translateY(-5px)');
                },
                function() {
                    $(this).css('transform', 'translateY(0)');
                }
            );
        });
    </script>

</body>
</html>