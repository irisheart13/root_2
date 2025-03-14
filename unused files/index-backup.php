<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">

    <link href="dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <title>Login</title>
</head>
<style>
    /* Poppins Regular */
    @font-face {
        font-family: 'Poppins';
        src: url('/Root_1/Poppins/Poppins-Regular.ttf') format('truetype');
        font-weight: 400;
        font-style: normal;
    }

    /* Poppins Bold */
    @font-face {
        font-family: 'Poppins';
        src: url('/Root_1/Poppins/Poppins-Bold.ttf') format('truetype');
        font-weight: 700;
        font-style: normal;
    }

    /* Poppins Italic */
    @font-face {
        font-family: 'Poppins';
        src: url('/Root_1/Poppins/Poppins-Italic.ttf') format('truetype');
        font-weight: 400;
        font-style: italic;
    }

    /* Poppins Light */
    @font-face {
        font-family: 'Poppins';
        src: url('/Root_1/Poppins/Poppins-Light.ttf') format('truetype');
        font-weight: 300;
        font-style: normal;
    }
    * {
	    margin: 0;
	    padding: 0;
	}
    body{
    	overflow-y: hidden;
    }
    .container-fluid{
        padding:0;
    }
    /*Nav Bar Design START*/
    .nav-bar{
        position: absolute;
        top: 0;
        left: 0;

        width: 100vw;
        height: 40px;

        margin: 0;
        padding: 0;

        background-color: #212529;
    }
    .email{
    	display: flex;
        justify-content: end; 
        align-items: center;
    }
    .img-email{
    	width: 20px;
    	height: 20px;

    	filter: brightness(0) saturate(100%) invert(75%) sepia(92%) saturate(400%) hue-rotate(-10deg);
    }
    .txt-email{
    	color:#fdc500;
    }
    /*Nav Bar Design END*/

	/*content design START*/
    .overlay{
        border-image-source: linear-gradient(hsl(150 100% 0% / 0.8),  
        hsl(120 100% 0% / 0.8));
        border-image-slice: fill 1;
        
    }
	.contentSection{
        background-image: url('img/plmun_rlrc.jpg');
        background-size: cover;
        background-repeat: no-repeat;

        height:100vh;
    }
	.php-login{
		padding: 0px;
		margin-top: 60px;

		display: flex;
	    justify-content: center;
	}

    .login-box{
        width: 400px;
        height: 450px;
        margin: 0;

        background-color: #dce1de; /* Dark color with 40% opacity */
        border-radius: 10px;
    }
    .custom-input{
        border-radius: 15px;
        color: white;
    }
    .btn-login{
        width:100%;
        height:35px;

        border-width: 1px;
        border-radius: 20px;
    }
    .plmun_logo_login{
        margin-top: 60px;
        width: 120px;
        height: 120px;
    }
	/*content design END*/
</style>
<body>
	<div class="container-fluid">
		<!--Nav Section START-->
        <section class="navBarSection">
            <div class="container-fluid">
                <div class="row nav-bar">
                    <div class="col-12 nav_btn">
                        <div class="d-flex flex-row-reverse">
                            <div class="p-2 txt-email">
                                plmuncomm@plmun.edu.ph
                            </div>
                            <div class="p-2">
                                <img src="img/email.png" alt="email" class="img-email">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Nav Section END-->

        <!--Content Section START-->
        <section class="contentSection overlay">
            <div class="container-fluid">
                <div class="row g-0">
                    <div class="col-12 col-md-6 p-3 php-login d-flex">
                        <?php 
                            include 'login.php';
                        ?>
                    </div>
                    <div class="col-12 col-md-6 p-2">
                        <!--LIST OF ABSTRACT-->
                    </div>
                </div>
            </div>
        </section>
        <!--Content Section END-->
	</div>
</body>
</html>
