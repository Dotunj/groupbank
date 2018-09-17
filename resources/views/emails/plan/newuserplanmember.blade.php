<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8">
</head>

<body>
    @php
    $id = $plan->identifier;
    @endphp

    <div>
        Hi,
        <br>
        {{$user->name}} has added you to a savings plan on GroupBank! You'll be required to renumerate the sum of
        N{{number_format($plan->amount, 2, '.', ',')}} monthly.
        <br>Please click on the link below to register:
        <br>
        <a href="http://localhost:8080/register/{{$plan->identifier}}">Register</a>
        <br />
    </div>

</body>

</html>
