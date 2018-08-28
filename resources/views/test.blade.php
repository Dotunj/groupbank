<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
    
    </head>
    <body>
   
        <form >
            <script src="https://js.paystack.co/v1/inline.js"></script>
            <button type="button" onclick="payWithPaystack()"> Pay </button> 
          </form>
           
          <script>
            function payWithPaystack(){
              var handler = PaystackPop.setup({
                key: 'pk_test_013035a27d6c9deb86f35de3549951cc3d5e8919',
                email: 'customer@email.com',
                amount: 10000,
              //  ref: ''+Math.floor((Math.random() * 1000000000) + 1), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
                metadata: {
                   custom_fields: [
                      {
                          display_name: "Mobile Number",
                          variable_name: "mobile_number",
                          value: "+2348012345678"
                      }
                   ]
                },
                callback: function(response){
                    alert('success. transaction ref is ' + response.reference);
                },
                onClose: function(){
                    alert('window closed');
                }
              });
              handler.openIframe();
            }
          </script>
    </body>
</html>
