<?php
    session_start();
?>

<div class="row login-box">
            <div id="login" >
                <form method="POST" action="login_process.php" class="form_login row">
                    <div class="col-12 d-flex justify-content-center align-items-center pt-2">
                        <img src="img/plmun_logo.png" alt="plmun_logo_login" class="plmun_logo_login">
                    </div>
                    <div class="col-8 p-2 mx-auto">
                        <input type="text" name="username" placeholder="USERNAME" class="form-control text-center custom-input" required>
                    </div>
                    <div class="col-8 p-2 mx-auto">
                        <input type="password" name="pin" placeholder="PIN" class="form-control text-center custom-input" required>
                    </div>
                    <div class="col-5 mx-auto p-2">
                        <button type="submit" class="btn-login">LOGIN</button>
                    </div>
                </form>
            </div>
            <?php
            if (!empty($error_message)) {
                echo "<p style='color: red;'>$error_message</p>";
            }
            ?>
        </div>
    </div>