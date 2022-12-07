<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Admin</title>

        <link href="{{asset('logo.png')}}" type="image/png" rel="icon" >

        <link href="{{asset('css/font.css')}}" rel="stylesheet">
        
        <script src="{{asset('js/jquery.js')}}"></script>

        <link href="{{asset('css/bootstrap/bootstrap.min.css')}}" rel="stylesheet">
        <link href="{{asset('css/home.css')}}" rel="stylesheet">
        <link href="{{asset('css/admin.css')}}" rel="stylesheet">
    
    </head>
    <body> 
        <a id="return-href" href="/"><img src="{{asset('logo.png')}}" height="60px" width="60px" ></a>
        <main>
        <div id="container">
            <h1>ADMIN</h1>
            <form id="form" method="POST" action="/changeSettings">
            @csrf
            <div class="row ">
                <div class="col-sm-6">
                    <label for="minInterval" class="form-label">Min interval</label>
                    <div class="input-group has-validation">
                        <span  style="border-top-right-radius: 0px; border-bottom-right-radius: 0px; margin-right: -3px;"  
                         class="input-group-text cartoon-input"><input type="checkbox" id="checkMin" name="checkMin" ></span>
                        <input type="datetime-local" class="form-control cartoon-input" id="minInterval" name="minInterval">
                    </div>
                </div>
                <div class="col-sm-6">
                    <label for="maxInterval" class="form-label">Max interval</label>
                    <div class="input-group has-validation">
                        <span style=" border-top-right-radius: 0px; border-bottom-right-radius: 0px; margin-right: -3px;"
                        class="input-group-text cartoon-input"><input type="checkbox" id="checkMax" name="checkMax" ></span>
                        <input type="datetime-local" class="form-control cartoon-input" id="maxInterval" name="maxInterval" >
                    </div>
                </div>
            </div>
            <br>
            <div class="row ">
                <div class="col-md-6">
                    <label for="time" class="form-label">Pick up time:</label>
                    <div class="input-group has-validation">
                        <span  style="border-top-right-radius: 0px; border-bottom-right-radius: 0px; margin-right: -3px;"  
                        class="input-group-text cartoon-input"><input type="checkbox" id="checkTime" name="checkTime" ></span>
                        <input type="number" id="time" class="form-control cartoon-input" name="time" min = "1" required=""/>
                    </div>
                    <!--<label for="time">Pick up time:</label>
                    <div class="form-outline input-group has-validation">
                        <input type="number" id="time" class="form-control cartoon-input" name="time" min = "1" required=""/>
                    </div>-->
                </div>

                <div class="col-md-6">
                    <label for="files-list">File</label>
                    <select class="custom-select d-block w-100 cartoon-input" id="files-list" name="file" required=""></select>
                </div>
                
            </div>
            <br>
            <br>
            <button class="w-100 btn btn-primary btn-lg " type="submit">Save settings</button>
            </form> 
        </div>
</main>
    </body>
    <script>
        $( document ).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function fetchSettings(callback){
                var data;
                $.ajax({
                    type: "GET",
                    url: '{{ url("/") }}/api/fetchSettings',
                    data: { _token: "{{ csrf_token() }}"  },
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    cache: false,
                    success: function (data) {
                        callback(data);
                    },

                    error: function (msg) {

                        console.log("failed GET");
                        console.log(msg.responseText);
                    }
                });
            }

            function setAttributes(){
                $('[name^=check]').change(function() { 
                    $("#minInterval").attr('disabled', !$('#checkMin').is(":checked"));
                    $("#maxInterval").attr('disabled', !$('#checkMax').is(":checked"));
                    $("#time").attr('disabled', !$('#checkTime').is(":checked"));
                });
                $( "#buttons" ).click(function() {
                    $("#form").submit();
                    
                });
            }

            function dateIsValid(date) {
                return date instanceof Date && !isNaN(date);
            }

            function initializeSettings(){
                console.log("initalized!");
                fetchSettings(function(data){
                    console.log(data);
                    $("#checkMin").prop("checked", data["minInterval"] != null);
                    $("#checkMax").prop("checked", data["maxInterval"] != null);
                    $("#checkTime").prop("checked", data["time"] != null);

                    $("#minInterval").val(data["minInterval"]);
                    $("#maxInterval").val(data["maxInterval"]);
                    $("#time").val(data["time"]);

                    $("#minInterval").attr('disabled', !$('#checkMin').is(":checked"));
                    $("#maxInterval").attr('disabled', !$('#checkMax').is(":checked"));
                    $("#time").attr('disabled', !$('#checkTime').is(":checked"));
                    console.log(data["file"]);
                    $.each(data["files"], function(index){
                        $('#files-list').append(`<option value="${data["files"][index]}">${data["files"][index]}</option>`);
                    });
                    $("#files-list").val(data["file"]);
                    
                    console.log(data["files"]);
                });

                
            }
            setInterval(function () {
                console.log($("#minInterval").val());
            },2000);
            setAttributes();
            initializeSettings();
        });
    </script>
</html>