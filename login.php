<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
?>

<div class="row login-box p-0">
    <div class="col-3 col-md-4 img-section p-0 m-0">
        <img src="img/plmun_rlrc.jpg" alt="plmun_rlrc" class="img-plmun_rlrc">
    </div>
    <div class="col-9 col-md-8  d-flex justify-content-center align-items-center">
        <form method="POST" action="login_process.php" class="form_login row">
            <div class="col-12">
                <div class="row">
                    <div class="col-12 text-center">
                        <span class="txt-plmun">PLMun</span>
                    </div>
                    <div class="col-12 text-center">
                        <span class="txt-wVM">Where Values Matter</span>
                    </div>
                </div>
            </div>
            <div class="col-10 col-md-10 p-2 mx-auto">
                <input type="text" name="username" placeholder="USERNAME" class="form-control text-center custom-input" required>
            </div>
            <div class="col-10 col-md-10 p-2 mx-auto">
                <input type="password" name="pin" placeholder="PIN" class="form-control text-center custom-input" required>
            </div>
            <div class="col-5 col-md-7 mx-auto p-2">
                <button type="submit" class="btn-login">LOGIN</button>
            </div>
            <div class="col-12 text-center mt-3">
                <a href="#" class="text-decoration-none register-link" data-bs-toggle="modal" data-bs-target="#registerModal">
                    Don't have an account? Register
                </a>
            </div>
        </form>
    </div>
</div>