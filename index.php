<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">

    <link rel="stylesheet" href="./index.css">
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <title>Login</title>
</head>
<body>
	<div class="container-fluid main">
		<!--Nav Section START-->
        <section class="navBarSection">
            <div class="row">
                <div class="col-2 col-md-1 p-0 d-flex align-items-center justify-content-center justify-content-md-end logo">
                    <img src="img/plmun_logo.png" alt="logo" class="img-logo">
                </div>
                <div class="col-4 col-md-2 welcome p-0 ps-md-2 d-flex align-items-center">
                    <span class="txt-welcome">WELCOME</span>
                </div>
                <div class="col-6 col-md-3 offset-md-6 d-flex align-items-center justify-content-end">
                    <span class="txt-email align-items-center">plmuncomm@plmun.edu.ph</span>
                </div>
            </div>
        </section>
        <!--Nav Section END-->

        <!-- Login and list of abstract START -->
        <section class="lnlSection"> <!--lnl means login and list-->
            <div class=row>
                <div class="col-12 col-md-4 login d-flex justify-content-center">
                        <?php 
                            include 'login.php';
                        ?>
                </div>
                <div class="col-12 col-md-8">
                        
                </div>
            </div>
        </section>
        <!-- Login and list of abstract END -->

       
	</div>
</body>
</html>
