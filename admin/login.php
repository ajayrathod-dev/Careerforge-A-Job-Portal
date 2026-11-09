<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
include("connection.php");
$username = $_POST['uname'] ?? '';
$password = $_POST['password'] ?? '';

if (isset($_POST["login"])) {
    $sql = "SELECT * FROM admin WHERE user_id='$username' AND password='$password' LIMIT 1";
    
    $result = mysqli_query($link, $sql);
    if ($result->num_rows > 0) {
        while ($usr = $result->fetch_assoc()) {
            $uuid = uniqid();
            setcookie("uuid", $uuid, time() + (10 * 365 * 24 * 60 * 60));
            $my_otp = rand(111111, 999999);
            mysqli_query($link, "INSERT INTO ods (ots, user_id, uuid, status) VALUES ('$my_otp', '$username', '$uuid', 'verified')");

            // Set session variables for admin
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user_id'] = $username;

            echo "<script>window.location.href='dashboard.php?success=User Login Successfully'</script>";
        }
    } else {
        header("Location:login.php?error=13");
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="egal-removebg-preview.png" type="image/png" />
    <!--plugins-->
    <link href="assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/plugins/notifications/css/lobibox.min.css" />

    <link href="assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
    <link href="assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
    <!-- loader-->
    <link href="assets/css/pace.min.css" rel="stylesheet" />
    <script src="assets/js/pace.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="assets/css/app.css" rel="stylesheet">
    <link href="assets/css/icons.css" rel="stylesheet">
    <link href="assets/css/splash.css" rel="stylesheet">
    <title>Job Portal Admin</title>
</head>

<body style="background: #c21500;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #ffc500, #c21500);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #ffc500, #c21500); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
">

    <!--wrapper--> 
    <div class="wrapper valign-wrapper">
        <div class="section-authentication-signin d-flex align-items-center justify-content-center my-5 my-lg-0">
            <div class="container-fluid">
                <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
                    <div class="col mx-auto">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-4 text-center">
                                    <img src="egal-removebg-preview.png" style="width:100px;">
                                </div>

                                <div class="border p-4 rounded">
                                    <div class="text-center">
                                        <h3 class="">Login</h3>
                                    </div>

                                    <div class="login-separater text-center mb-4"> <span>LOGIN WITH Admin ID</span>
                                        <hr />
                                    </div>
                                    <div class="form-body">
                                        <form class="row g-3" method="post" action="">
                                            <div class="col-12">
                                                <label for="inputEmailAddress" class="form-label">Admin Id</label>
                                                <input type="text" name="uname" class="form-control" id="inputEmailAddress" value="<?php if (isset($_COOKIE['uname'])) {
                                                                                                                                        echo $_COOKIE['uname'];
                                                                                                                                    } ?>" placeholder="User Id" required>
                                            </div>
                                            <div class="col-12">
                                                <label for="inputChoosePassword" class="form-label">Password</label>
                                                <div class="input-group" id="show_hide_password">
                                                    <input type="password" class="form-control border-end-0" id="inputChoosePassword" name="password" placeholder="Enter Password" required=""> <a href="javascript:;" class="input-group-text bg-transparent" value="<?php if (isset($_COOKIE['password'])) {                         			echo $_COOKIE['password'];
                                                                        } ?>"><i class='bx bx-hide'></i></a>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked <?php if (isset($_COOKIE['uname'])) { ?> checked <?php } ?>>
                                                    <label class="form-check-label" for="flexSwitchCheckChecked">Remember Me</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-end"> <a href="forgot.php">Forgot Password ?</a>
                                            </div>
                                            <div class="col-12">
                                                <div class="d-grid">
                                                    <input type="submit" class="btn btn-primary" name="login" Value="Login">
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end row-->

                <!--start switcher-->
                <div class="switcher-wrapper">
                    <div class="switcher-btn"> <i class='bx bx-cog bx-spin'></i>
                    </div>
                    <div class="switcher-body">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 text-uppercase">Theme Customizer</h5>
                            <button type="button" class="btn-close ms-auto close-switcher" aria-label="Close"></button>
                        </div>
                        <hr />
                        <h6 class="mb-0">Theme Styles</h6>
                        <hr />
                        <div class="d-flex align-items-center justify-content-between">
                            <script type="text/javascript">
                                window.onload = function() {
                                    <?php
                                    $time = date("H");
                                    $timezone = date("e");
                                    if ($time < "7" || $time > "19") {
                                    ?>
                                        document.getElementById("darkmode").click();
                                    <?php
                                    } else {
                                    ?>
                                        document.getElementById("lightmode").click();
                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if ($_GET['error'] == 13 && $_GET['error'] != '') {
                                    ?>
                                        Lobibox.notify('error', {
                                            pauseDelayOnHover: true,
                                            continueDelayOnInactiveTab: false,
                                            position: 'top right',
                                            icon: 'bx bx-x-circle',
                                            msg: 'Invalid User ID or Password.'
                                        });
                                    <?php
                                    }
                                    ?>
                                };
                            </script>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="lightmode">
                                <label class="form-check-label" for="lightmode">Light</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="darkmode">
                                <label class="form-check-label" for="darkmode">Dark</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="semidark">
                                <label class="form-check-label" for="semidark">Semi Dark</label>
                            </div>
                        </div>
                        <hr />
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="minimaltheme" name="flexRadioDefault">
                            <label class="form-check-label" for="minimaltheme">Minimal Theme</label>
                        </div>
                        <hr />
                        <h6 class="mb-0">Header Colors</h6>
                        <hr />
                        <div class="header-colors-indigators">
                            <div class="row row-cols-auto g-3">
                                <div class="col">
                                    <div class="indigator headercolor1" id="headercolor1"></div>
                                </div>
                                <div class="col">
                                    <div class="indigator headercolor2" id="headercolor2"></div>
                                </div>
                                <div class="col">
                                    <div class="indigator headercolor3" id="headercolor3"></div>
                                </div>
                                <div class="col">
                                    <div class="indigator headercolor4" id="headercolor4"></div>
                                </div>
                                <div class="col">
                                    <div class="indigator headercolor5" id="headercolor5"></div>
                                </div>
                                <div class="col">
                                    <div class="indigator headercolor6" id="headercolor6"></div>
                                </div>
                                <div class="col">
                                    <div class="indigator headercolor7" id="headercolor7"></div>
                                </div>
                                <div class="col">
                                    <div class="indigator headercolor8" id="headercolor8"></div>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <h6 class="mb-0">Sidebar Colors</h6>
                        <hr />
                        <div class="header-colors-indigators">
                            <div class="row row-cols-auto g-3">
                                <div class="col">
                                    <div class="indigator sidebarcolor1" id="sidebarcolor1"></div>
                                </div>
                                <div class="col">
                                    <div class="indigator sidebarcolor2" id="sidebarcolor2"></div>
                                </div>
                                <div class="col">
                                    <div class="indigator sidebarcolor3" id="sidebarcolor3"></div>
                                </div>
                                <div class="col">
                                    <div class="indigator sidebarcolor4" id="sidebarcolor4"></div>
                                </div>
                                <div class="col">
                                    <div class="indigator sidebarcolor5" id="sidebarcolor5"></div>
                                </div>
                                <div class="col">
                                    <div class="indigator sidebarcolor6" id="sidebarcolor6"></div>
                                </div>
                                <div class="col">
                                    <div class="indigator sidebarcolor7" id="sidebarcolor7"></div>
                                </div>
                                <div class="col">
                                    <div class="indigator sidebarcolor8" id="sidebarcolor8"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end switcher-->

                <!-- Bootstrap JS -->
                <script src="assets/js/bootstrap.bundle.min.js"></script>
                <!--plugins-->
                <script src="assets/js/jquery.min.js"></script>
                <script src="assets/plugins/simplebar/js/simplebar.min.js"></script>
                <script src="assets/plugins/metismenu/js/metisMenu.min.js"></script>
                <script src="assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
                <script src="assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js"></script>
                <script src="assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js"></script>
                <script src="assets/plugins/chartjs/js/Chart.min.js"></script>
                <script src="assets/plugins/chartjs/js/Chart.extension.js"></script>
                <script src="assets/js/index.js"></script>
                <!--app JS-->
                <script src="assets/js/app.js"></script>
                <!--notification js -->
                <script src="assets/plugins/notifications/js/lobibox.min.js"></script>
                <script src="assets/plugins/notifications/js/notifications.min.js"></script>
                <script src="assets/plugins/notifications/js/notification-custom-script.js"></script>
                <!--app JS-->
                <script src="assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
                <script src="assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
                <!--app JS-->

</body>

<script>
    $(document).ready(function() {
        $('#example').DataTable();
    });
</script>
<script>
    $(document).ready(function() {
        var table = $('#example2').DataTable({
            lengthChange: false,
            buttons: ['copy', 'excel', 'pdf', 'print']
        });

        table.buttons().container()
            .appendTo('#example2_wrapper .col-md-6:eq(0)');
    });
</script>
<!--Password show & hide js -->
<script>
    $(document).ready(function() {
        $("#show_hide_password a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_password input').attr("type") == "text") {
                $('#show_hide_password input').attr('type', 'password');
                $('#show_hide_password i').addClass("bx-hide");
                $('#show_hide_password i').removeClass("bx-show");
            } else if ($('#show_hide_password input').attr("type") == "password") {
                $('#show_hide_password input').attr('type', 'text');
                $('#show_hide_password i').removeClass("bx-hide");
                $('#show_hide_password i').addClass("bx-show");
            }
        });
    });
</script>
<!--app JS-->

</body>

</html>