<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url() ?>assets/style.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/login.css">
    <link rel="icon" href="<?= base_url() ?>assets/img/WASET.png">
    <title>الرسائل</title>
</head>

<body class="login">

    <div class="messages-content">
        <div class="main">

            <div class="container b-container" id="b-container">
                <form class="form" id="login-form">
                    <h2 class="form_title title">تسجيل الدخول</h2>
                    <span class="form__span">قم بتسجيل الدخول لحسابك :</span>
                    <span id="error-text" style="color: red;"></span>
                    <input class="form__input" type="text" placeholder="معرف الدخول" id="username" name="username">
                    <input class="form__input" type="password" placeholder="رمز الدخول" id="password" name="password">
                    <button class="form__button button submit">سجل الدخول</button>
                </form>
            </div>

            <div class="switch" id="switch-cnt">
                <div class="switch__circle"></div>
                <div class="switch__circle switch__circle--t"></div>
                <div class="switch__container" id="switch-c1">
                    <h2 class="switch__title title">اهلا بعودتك!</h2>
                    <p class="switch__description description">للبقاء على اتصال معنا، يرجى تسجيل الدخول باستخدام معلوماتك الشخصية</p>
                </div>

            </div>
        </div>

        <div class="message-footer">
            <a href="https://maps.app.goo.gl/Q3h7s56bWB8hJ4wQ6" class="footer-icon">
                <img src="<?= base_url() ?>assets/img/icon/location.svg" alt="">
            </a>
            <a href="mailto:info@al-waseet.com" class="footer-icon">
                <img src="<?= base_url() ?>assets/img/icon/email.svg" alt="">
            </a>
            <a href="https://www.instagram.com/alwaseetcompany1" class="footer-icon">
                <img src="<?= base_url() ?>assets/img/icon/instagram.svg" alt="">
            </a>
            <a href="https://web.facebook.com/alwaseetcompany1?_rdc=1&_rdr" class="footer-icon">
                <img src="<?= base_url() ?>assets/img/icon/facebook.svg" alt="">
            </a>
        </div>
    </div>

    <script src="<?= base_url() ?>assets/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $("#login-form").submit(function(e) {
            e.preventDefault();
            let username = $("#login-form input[type=text]").val();
            let password = $("#login-form input[type=password]").val();

            if (username == "" || password == "") {
                $("#error-text").text("يجب ادخال اسم المستخدم وكلمة المرور");
                return false;
            }
            let timerInterval;
            Swal.fire({
                title: "جاري تسجيل الدخول!",
                html: "سيتم تحويلك مباشرتا بعد <b></b>.",
                timer: 5000,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                    const timer = Swal.getPopup().querySelector("b");
                    timerInterval = setInterval(() => {
                        timer.textContent = `${Swal.getTimerLeft()}`;
                    }, 100);
                },
                willClose: () => {
                    clearInterval(timerInterval);
                }
            }).then((result) => {
                /* Read more about handling dismissals below */
                if (result.dismiss === Swal.DismissReason.timer) {
                    console.log("I was closed by the timer");
                }
            });
            $.ajax({
                url: "<?= base_url() ?>login",
                type: "POST",
                data: new FormData($("#login-form")[0]),
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.status == true) {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'تم تسجيل الدخول بنجاح',
                            showConfirmButton: false,
                            timer: 1500
                        })
                        window.location.href = "<?= base_url() ?>";
                    } else {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: data.msg,
                        })
                        $("#error-text").text(data.msg);

                    }
                },
                error: function(error) {
                    let res = JSON.parse(error.responseText);
                    
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: res.msg,
                    })
                    $("#error-text").text(res.msg);
                }
            })
        })
    </script>

</body>

</html>