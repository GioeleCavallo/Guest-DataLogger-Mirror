<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Home</title>

        <link href="{{asset('logo.png')}}" type="image/png" rel="icon" >
        <link href="{{asset('css/font.css')}}" rel="stylesheet">
        
        <link href="{{asset('css/bootstrap/bootstrap.min.css.css')}}" rel="stylesheet">
        <link href="{{asset('css/home.css')}}" rel="stylesheet">

    </head>
    <body>
        <h1 style="text-align:center">Benvenuto in {{ config('app.name') }}<h1>
        <div class="center" id="button-container" >
            <div class="center inner" ><a href="{{ config('app.url') }}:8000/chart">  CHART  </a></div>
            <div class="center inner" ><a href="{{ config('app.url') }}:8000/admin">  ADMIN  </a></div>

        </div>
    </body>
  
</html>