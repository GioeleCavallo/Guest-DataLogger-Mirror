<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Guest Data Logger</title>

        <link href="{{asset('logo.png')}}" type="image/png" rel="icon" >
        <link href="{{asset('css/font.css')}}" rel="stylesheet">
        <link href="{{asset('css/home.css')}}" rel="stylesheet">
        
        <script src="{{asset('js/jquery.js')}}"></script>
        <script src="{{asset('js/Chart.js')}}"></script>
        <script src="{{asset('js/canva.js')}}"></script>
    
    </head>
    <body>
        <a id="return-href" href="/"><img src="{{asset('logo.png')}}" height="60px" width="60px" ></a>
        <canvas id="line-chart"></canvas>
    </body>
   
    <script>
    $( document ).ready(function() {
        $chart = new Canva("line-chart");
        $lastTime = new Date();
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function fetchData(callback){
            var data;
            $.ajax({
                type: "GET",
                url: '{{ url("/") }}/api/fetchData',
                data: { _token: "{{ csrf_token() }}"  },
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                cache: false,
                success: function (data) {

                    $data = data["data"];
                    
                    callback($data);
                },

                error: function (msg) {

                    console.log("failed GET");
                    console.log(msg.responseText);
                }
            });
        }

        
        function initializeData(){
            console.log("initalized!");
            fetchData(function(data){
                $labels = [];
                $values = [];

                $.each(data,function(index){
                    $labels.push($(this)[0]);
                    $values.push($(this)[1]);
                    $lastTime = new Date($(this)[0]);
                });
                
                $chart.showChart($labels,$values);

            });
        }

        window.setInterval(function(){
            fetchData(function(data){
                console.log("interval");
                $.each(data,function(index){
                    $date = new Date($(this)[0]);
                    if($date > $lastTime){
                        console.log("added element");
                        $chart.addValueChart($(this)[0],$(this)[1]);
                        $lastTime = $date;
                    }
                });
                

            });
        }, 1000); 

        initializeData();
        /*
        let chart = new Canva("line-chart");
        chart.showChart(["prova","aaa"],[12,2]);
        chart.addValueChart("1",6);
        chart.addValueChart("2",6);
        chart.addValueChart("3",6);
        chart.addValueChart("4",6);
        chart.addValueChart("5",6);*/
    }); 
    </script>
</html>