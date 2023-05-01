<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8"/>
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"/>
        <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
        <title>Demo - Payday</title>
        <link rel="shortcut icon" href="https://payday.gainhq.com/images/icon.png"/>
        <link rel="apple-touch-icon" href="https://payday.gainhq.com/images/icon.png"/>
        <link rel="apple-touch-icon-precomposed" href="https://payday.gainhq.com/images/icon.png"/>

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <style>
            body{
                background:#062148;
            }
            main{
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }
            .demo-btn {
                background: #329af7;
                color:#ffffff;

            }
            
            .logo-container{
                margin-bottom: 30px;
                width: 60%;
                margin-left: 20%;
            }
            
            .demo-btn-container{
                display:flex;
                flex-direction: column;
                border-radius:5px !important;
                background: #37529e;
                padding: 10PX 10PX;
            }
        </style>

    </head>

    <body>
        <main>
            <div class="logo-container">
                <img src="https://payday.gainhq.com/images/logo.png" class="img-fluid" alt="payday logo">
            </div>
            <div class="demo-btn-container">
                <a  class="btn btn-default demo-btn" href="https://payday.gainhq.com/" target="new">Click to view demo</a>
            </div>
        </main>


        <!-- JavaScript -->
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    </body>
</html>
