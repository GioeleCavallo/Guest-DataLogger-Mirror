<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Login</title>
        <link href="{{asset('logo.png')}}" type="image/png" rel="icon" >
        
        <link href="{{asset('css/font.css')}}" rel="stylesheet">
        <script src="{{asset('js/jquery.js')}}"></script>

        <link href="{{asset('css/bootstrap/bootstrap.min.css')}}" rel="stylesheet">
        <link href="{{asset('css/home.css')}}" rel="stylesheet">
        <link href="{{asset('css/login.css')}}" rel="stylesheet">
    
    </head>
    <body> 
        <a id="return-href" href="/"><img src="{{asset('logo.png')}}" height="60px" width="60px" ></a>
        @if (count($errors) > 0)
            <?php $finalErrors = ""; ?>
                @foreach ($errors->all() as $error)
                    <?php $finalErrors .= $error . " "  ?>
                @endforeach
                <script>alert("{{$finalErrors}}");</script>
        @endif        
        @if ($message = Session::get('failed'))
        <script>alert("{{$message}}");</script>
        @endif
        <main class="form-signin w-100 m-auto">
  
            <form  id="form" method="POST" action="/checkLogin">
            @csrf
                <h1 class="mb-3 fw-normal">Login</h1>
            
                <div class="form-floating">
                    <input type="text" class="form-control" id="floatingInput" placeholder="username" name="username" >
                    
                </div>
                <div class="form-floating">
                    <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password">

                </div>
            
                <button class="w-100 btn btn-primary btn-lg" type="submit">Login</button>
                
                </form>
            </main>
        </div>
    </body>
</html>