<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>

<div>
    Hi,
    <br>
    {{$user->name}} has added you to a savings plan on GroupBank! You'll be required to renumerate the sum of N{{$plan->amount}} monthly. 
    <br> Please click on the link below or copy it into the address bar of your browser to register:
    <br>
    <a href="{{ url('plan/{plan}/register')}}">Register</a>
    <br/>
</div>

</body>
</html>