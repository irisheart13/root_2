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
    .container{
    	padding: 0;
    	margin: 0;
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
	.content{
		padding: 0px;
		height: 100vh;
		background-color: #283618;
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

        background-color: #f5f5f5;
        border-radius: 10px;
    }
    .custom-input{
        border-radius: 15px;
        color: black;
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

    @media(min-width: 576px) {
    
    }
</style>
<body>
	<div class="container">
		<!--Nav Section START-->
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
		<div>
        <!--Nav Section END-->

        <!--Content Section START-->
        
    	<div class="row content">
        	<div class="col-12 col-md-6 p-3 php-login d-flex">
                <?php 
                    include 'login.php';
                ?>
        	</div>

        	<div class="col-12 col-md-6 p-2">
        		
        	</div>
        </div>
        
        <!--Content Section END-->
	</div>
</body>
</html>
