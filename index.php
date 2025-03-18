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

        <!-- Registration Modal START -->
        <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel">Register</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="register.php" method="POST" onsubmit="return validatePIN()">
                        <!-- Last Name -->
                        <div class="mb-3">
                            <label for="lastname" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter Last Name" required>
                        </div>

                        <!-- First Name -->
                        <div class="mb-3">
                            <label for="firstname" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter First Name" required>
                        </div>

                        <!-- Middle Initial -->
                        <div class="mb-3">
                            <label for="middleinitial" class="form-label">Middle Initial (Optional)</label>
                            <input type="text" class="form-control" id="middle_initial" name="middle_initial" placeholder="Enter Middle Initial" maxlength="1">
                        </div>

                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label">Institutional Email</label>
                            <input type="email" class="form-control" id="username" name="username" 
                                pattern="^[a-zA-Z0-9._%+-]+@plmun.edu.ph$" required 
                                placeholder="example@plmun.edu.ph">
                            <small class="text-danger d-none" id="emailError">Only @plmun.edu.ph emails are allowed.</small>
                        </div>

                        <!-- PIN -->
                        <div class="mb-3">
                            <label for="pin" class="form-label">PIN</label>
                            <input type="password" class="form-control" id="pin" name="pin" pattern="\d{4}" required maxlength="4">
                            <small class="text-danger d-none" id="pinError">PIN must be exactly 4 digits.</small>
                        </div>

                        <!-- Confirmation PIN -->
                        <div class="mb-3">
                            <label for="pin" class="form-label">PIN</label>
                            <input type="password" id="confirm_pin" name="confirm_pin" placeholder="Confirm 4-digit PIN" required>
                            <small class="text-danger d-none" id="pinError">PIN must be exactly 4 digits.</small>
                        </div>

                        <!-- College Department Dropdown -->
                        <div class="mb-3">
                            <label for="department" class="form-label">College Department</label>
                            <select class="form-select" id="department" name="department" required>
                                <option value="" selected disabled>Select Department</option>
                                <option value="CITCS">CITCS</option>
                                <option value="CBA">CBA</option>
                                <option value="COA">COA</option>
                                <option value="CTE">CTE</option>
                                <option value="CAS">CAS</option>
                                <option value="CCJ">CCJ</option>
                                <option value="IPPG">IPPG</option>
                            </select>
                        </div>

                        <!-- Program Dropdown (Dynamic) -->
                        <div class="mb-3">
                            <label for="program" class="form-label">Program</label>
                            <select class="form-select" id="program" name="program" required>
                                <option value="" selected disabled>Select Program</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Register</button>
                    </form>
                </div>
                </div>
            </div>
        </div>
        <!-- Registration Modal END -->      
	</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const departmentSelect = document.getElementById("department");
    const programSelect = document.getElementById("program");
    
    // Program options based on department
    const programs = {
        "CITCS": ["BSCS", "BSIT", "ACT"],
        "CBA": ["BSBA-HR", "BSBA-MM", "BSBA-OM"],
        "CTE": ["BEED-ECED", "BEED-SNED", "BEED-GENED", "BSED-ENG", "BSED-FIL", "BSED-PE", "BSED-SCI", "BSED-MATH"],
        "CAS": ["BSPSY", "BACOMM"],
        "COA": ["BSA"],
        "CCJ": ["BSCRIM"],
        "IPPG": ["BAPOL","BPA"]
    };

    departmentSelect.addEventListener("change", function () {
        const selectedDept = this.value;
        programSelect.innerHTML = `<option value="" disabled selected>Select Program</option>`; // Reset options

        if (programs[selectedDept]) {
            programs[selectedDept].forEach(program => {
                let option = document.createElement("option");
                option.value = program;
                option.textContent = program;
                programSelect.appendChild(option);
            });
        }
    });

    // Email validation
    document.getElementById("username").addEventListener("input", function () {
        const emailError = document.getElementById("emailError");
        if (!this.value.endsWith("@plmun.edu.ph")) {
            emailError.classList.remove("d-none");
        } else {
            emailError.classList.add("d-none");
        }
    });

    // PIN validation
    document.getElementById("pin").addEventListener("input", function () {
        const pinError = document.getElementById("pinError");
        if (!/^\d{4}$/.test(this.value)) {
            pinError.classList.remove("d-none");
        } else {
            pinError.classList.add("d-none");
        }
    });

    // Confirmation of the pin
    function validatePIN() {
        const pin = document.getElementById('pin').value;
        const confirmPin = document.getElementById('confirm_pin').value;

        if (pin !== confirmPin) {
            alert('PIN and Confirm PIN do not match!');
            return false;
        }
        return true;
    }
});
</script>
</body>
</html>
