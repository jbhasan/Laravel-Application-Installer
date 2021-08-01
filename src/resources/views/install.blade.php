<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Installer</title>
    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <!-- Font-->
    <link rel="stylesheet" type="text/css" href="{{ ('/vendor/sayeed/application_installer/css/opensans-font.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ ('/vendor/sayeed/application_installer/css/montserrat-font.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ ('/vendor/sayeed/application_installer/fonts/material-design-iconic-font/css/material-design-iconic-font.min.css') }}">
    <!-- Main Style Css -->
    <link rel="stylesheet" href="{{ ('/vendor/sayeed/application_installer/css/style.css') }}"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
</head>
<body>
<div class="page-content">
    <div class="wizard-heading">Application Installer</div>
    <div class="wizard-v7-content">
        <div class="wizard-form">
            <form class="form-register" action="#" method="post">
                <div id="form-total">
                    <!-- SECTION 1 -->
                    <div class="steps">
                        <p class="step-icon"><span>1</span></p>
                        <div class="step-text">
                            <span class="step-inner-1">Check Requirements</span>
                            <span class="step-inner-2">Check System Requirements</span>
                        </div>
                    </div>
                    <section>
                        <div class="inner">
                            <div class="wizard-header">
                                <h3 class="heading">Check Requirements</h3>
                            </div>
                            <div class="wizard-body" id="checkRequirements">

                            </div>
                        </div>
                    </section>
                    <!-- SECTION 2 -->
                    <div class="steps">
                        <p class="step-icon"><span>2</span></p>
                        <div class="step-text">
                            <span class="step-inner-1">Application Setup</span>
                            <span class="step-inner-2">Application Details</span>
                        </div>
                    </div>
                    <section>
                        <div class="inner">
                            <div class="wizard-header">
                                <h3 class="heading">Application Setup</h3>
                            </div>
                            <div class="wizard-body" id="applicationSetup">
                                @include('application_installer::partials.application-setup')
                            </div>
                        </div>
                    </section>
                    <!-- SECTION 3 -->
                    <div class="steps">
                        <p class="step-icon"><span>3</span></p>
                        <div class="step-text">
                            <span class="step-inner-1">Confirm & Finalize</span>
                            <span class="step-inner-2">Confirm All Setup</span>
                        </div>
                    </div>
                    <section>
                        <div class="inner">
                            <div class="wizard-header">
                                <h3 class="heading">Confirm & Finalize</h3>
                            </div>
                            <div class="wizard-body" id="confirmFinalize">
                                @include('application_installer::partials.confirm-finalize')
                            </div>
                        </div>
                    </section>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="{{ ('/vendor/sayeed/application_installer/js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ ('/vendor/sayeed/application_installer/js/jquery.steps.js') }}"></script>
<script src="{{ ('/vendor/sayeed/application_installer/js/main.js') }}"></script>
<script>
    $(document).ready(function () {
        checkRequirements();
    });

    function checkRequirements() {
        $.ajax({
            url: '/install/check-requirements',
            method: 'post',
            async: true,
            data: {_token: '{{ @csrf_token() }}'},
            success: function (response) {
                $("#checkRequirements").html(response);
            }
        })
    }

    $(document).on('click', "#btnCheckConnection", function () {
        var element = $(this);
        element.html('Checking...');
        $.ajax({
            url: '/install/check-connection',
            method: 'post',
            async: true,
            data: {
                _token: '{{ @csrf_token() }}',
                db_host: $("#db_host").val(),
                db_port: $("#db_port").val(),
                db_database: $("#db_database").val(),
                db_username: $("#db_username").val(),
                db_password: $("#db_password").val(),
            },
            success: function (response) {
                if (response.status === 'success') {
                    element.html(response.message).removeClass('button-danger').addClass('button-default');
                } else if (response.status === 'error2') {
                    element.html(response.message).removeClass('button-default').addClass('button-danger');
                } else {
                    element.html('Database credentials is not correct').removeClass('button-default').addClass('button-danger');
                }
            }
        })
    });

    $(document).on('click', "a[href='#finish']", function () {
        var element = $(this);
        var originHtml = element.html();
        element.html('Processing, please wait...');
        $.ajax({
            url: '/install/process',
            method: 'post',
            async: true,
            headers: {
                'X-CSRF-TOKEN': '{{ @csrf_token() }}'
            },
            data: $(".form-register").serialize(),
            success: function (response) {
                if (response.status === 'success') {
                    element.html(response.message).removeClass('button-danger').addClass('button-default');
                    setTimeout(function () {
                        $.ajax({
                            url: '/install/migrate',
                            method: 'post',
                            async: true,
                            headers: {
                                'X-CSRF-TOKEN': '{{ @csrf_token() }}'
                            },
                            data: {},
                            success: function (response) {
                                if (response.status === 'success') {
                                    $(".wizard-form").html('<div class="wizard-header"><h3 class="heading" style="text-align: center;font-size: 45px;font-family: cursive;">Congratulations!</h3></div><div class="wizard-body" id="congratulations"><div class="box-body" style="text-align: center;font-size: 20px;font-family: cursive;">Your application setup is successfully done.<br>You can enjoy your application now, <a href="/home">click here</a></div></div>');
                                } else {
                                    element.html(response.message).removeClass('button-default').addClass('button-danger');
                                }
                            }
                        });
                    }, 5000);
                } else if (response.status === 'error2') {
                    element.html(response.message).removeClass('button-default').addClass('button-danger');
                } else {
                    element.html('Database credentials is not correct').removeClass('button-default').addClass('button-danger');
                }
            }
        })
    });
    $(document).on('change', 'input', function () {
        $('#finalAppName').text($("#application_name").val());
        $('#finalDbHost').text($("#db_host").val());
        $('#finalDbPort').text($("#db_port").val());
        $('#finalDbUser').text($("#db_username").val());
        $('#finalDbPassword').text($("#db_password").val());
        $('#finalDbName').text($("#db_database").val());
    })
</script>
</body>
</html>