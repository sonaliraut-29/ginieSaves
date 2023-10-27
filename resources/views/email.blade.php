<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <style>
        p {
            font-size: 12px;
        }

        .signature {
            font-style: italic;
        }
    </style>
</head>
<body>
<div>
    <p>Hey {{ $user->Name }},</p>
    <p>Please find the below temprary password for you account. </p>
    <p>Password : Test1234 </p>
    <p>Please change your password , Once you are able to login.</p>
    <p>Thanks,</p>
    <p class="signature">Genie Saves</p>
</div>
</body>
</html>