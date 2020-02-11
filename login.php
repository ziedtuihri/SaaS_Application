<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Login</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">

</head>
<body class="gray-bg">
<div class="middle-box text-center loginscreen animated fadeInDown">
    <div>
        <div>
            <h1 class="logo-name">
                <img src="login_img.png" style="width : 20%; margin-top: 30px;">
            </h1>
        </div>
        <h2>Welcome to tunisyndic</h2>
        </p>
        <form action="#">
            <center>
            <div class="alert alert-danger" role="alert" id="Authentification" style="display:none; margin-top: 30px;margin-bottom: 20px;width: 200px;">
                <span>Authentification échouée</span>
            </div>
            </center>
            <center>
            <div class="form-group" style="margin-top: 30px;margin-bottom: 20px;width: 200px;">
                <input id="email_id" name="email" type="email" class="form-control" placeholder="Username" required="">
            </div>
            </center>
            <center>
            <div class="s2-example" style="margin-top: 30px;margin-bottom: 20px;width: 200px;">
                <select id="select_tenant" class="js-example-basic-single js-states form-control"
                        style="margin-bottom: 30px; display:none"><option value="0">Select Instance</option></select>
            </div>
            </center>
            <button onclick="Send_Login()" type="button" value="Login" class="btn btn-primary block full-width m-b">
                Login
            </button>
        </form>
        <p class="m-t"><small>Zied Tuihri &copy; 2019</small></p>
    </div>
</div>
<!-- Mainly scripts -->

<script>


    function Send_Login() {

        username = $("#email_id").val();
        console.log("testing htis value " + username);
        key = "kBZNJGnc5khJzydxGxsyNOwXyjcaAtdytLno5g7G";

        $(document).ready(function () {
            $.ajax({
                url: 'http://tunisyndic.tn/tenant_api/initSession',
                type: "POST",
                data: ({root: "root"})
            });
            $.ajax({
                url: 'http://tunisyndic.tn/tenant_api/ExistTenant',
                type: "POST",
                data: ({name: username}),
                success: function (data) {
                    console.log("fdfs"+data);
                    obj = JSON.parse(data);
                    console.log("this your data  ***." + obj[0]);
                    if (obj[0] == 1) {
                        window.location.replace("http://tunisyndic.tn/signin?email="+username);
                    } else if (obj[0] == 0) {
                        document.getElementById("email_id").style.color = 'red';
                        $('#Authentification').show();
                    } else {
                        $('#select_tenant').show();
                        var items = obj[1];
                        for (var i = 0; i < items.length; i++) {
                            $('#select_tenant').append($('<option>', {
                                value: items[i],
                                text: items[i]
                            }, '</option>'));
                        }

                        $("#select_tenant").change(function(){

                           var selectedTenant = $(this).children("option:selected").val();
                            console.log("this your tenant  ." + selectedTenant);

                            $.ajax({
                                url: 'http://tunisyndic.tn/tenant_api/selectedTenant',
                                type: "POST",
                                data: ({Tenant: selectedTenant}),
                                success: function (data) {
                                    console.log("this your tenant  ." + selectedTenant);
                                    window.location.replace("http://tunisyndic.tn/signin?email="+username);
                                }
                                });
                        });
                    }
                }
            });
        });
    }


</script>


<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.js"></script>

</body>
</html>

