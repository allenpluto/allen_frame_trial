<html>
<head>
    <title>EML File Reader</title>
    <meta name="robots" content="noindex, nofollow">
    <script type="text/javascript" src="/content/js/jquery-1.11.3.js"></script>
</head>
<body>
<div id="controller_container">
    <a href="javascript:void(0)" type="button" id="start">Start</a>
    <a href="javascript:void(0)" type="button" id="stop" style="display: none;">Stop</a>
</div>
<div id="timer_container"></div>
<div id="result_container">
    Loading...
</div>
<style>
    #controller_container
    {
        width:800px;
        margin:20px auto;
        text-align: center;
    }
    #controller_container a {
        display: inline-block;
        width: 10em;
        padding: 5px 15px;

        background: #666666;
        border:2px outset #666666;
        border-radius:5px;

        color: white;
        font-size: 2em;
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        text-transform: uppercase;
    }
    #controller_container a:active {
        background: #444444;
        border-style: inset;

    }
    #timer_container
    {
        width:800px;
        margin:20px auto;
        padding:10px 25px;

        background:#eeeeee;

        border:2px solid #cccccc;
        border-radius:5px;

        color:#333333;
        font-size:16px;
        line-height:1.2;

        -webkit-transition: height 500ms linear;
        transition: height 500ms linear;
    }
    #timer_container.complete
    {
        color:#33cc33;
    }
    #result_container
    {
        display:block;
        width:800px;
        margin:20px auto;
        padding:10px 25px;

        background:#eeeeee;

        border:2px solid #cccccc;
        border-radius:5px;

        color:#333333;
        font-size:16px;
        line-height:1.2;

        -webkit-transition: height 500ms linear;
        transition: height 500ms linear;
    }
    #result_container > div
    {
        padding-bottom:10px;
        margin-bottom:10px;

        border-bottom:2px dashed #cccccc;
    }
    #result_container > div:last-child
    {
        margin-bottom:0;

        border-bottom:none;
    }
    #result_container .error
    {
        color:#cc3333;
    }
</style>
<script>

    $(document).ready(function(){
        var java_timer_start = Date.now();
        var java_time_seconds = 0;
        var java_time_start = 0;
        var success_counter = 0;
        var counter = 0;

        var java_timer = null;
        <?php
        $option = [
            'limit'=>50,
        ];
        if (isset($_GET['limit']))
        {
            $option['limit'] = $_GET['limit'];
        }
        ?>
        var query_limit = 250000;
        var limit = <?=$option['limit']?>;
        var max_count = Math.floor(query_limit / limit);


        var content_container = $('#result_container');
        function call_handler(){
            java_time_seconds = (Date.now() - java_timer_start)/1000;
            java_time_start = java_time_seconds;
            var post_value = {
                'limit': limit,
                'start_time': Date.now()
            };
            // console.log('post_value',post_value);
            $.ajax({
                'type': 'POST',
                'url': '/developer/file_meta/file_meta_handler.php',
                'data': post_value,
                'timeout': 600000,
                'success': function(result_string) {
                    var test_split_result = result_string.split('\n');
                    // if (test_split_result.length > 0) {
                    //     console.log('Last Line: ',test_split_result[test_split_result.length-1],(test_split_result[test_split_result.length-1]?'yes':'no'));
                    // }

                    if (counter == 0)
                    {
                        content_container.html('');
                    }
                    // var regex = /\n[^\n]*$/;
                    // var result = result_string.replace(regex, "");
                    var result = test_split_result[test_split_result.length-1];
                    try {
                        result = $.parseJSON(result);
                        if (result['success'] === true)
                        {
                            success_counter++;

                            var new_div = $('<div />',{
                                'class': 'success'
                            });
                            $.each(result['updated_data'], function( row_index, row ) {
                                var html_row = $('<div />',{
                                    'class':'success_row'
                                }).html(row.status+' - '+row.message);
                                if (row.status != 'OK')
                                {
                                    html_row.addClass('error')
                                }
                                new_div.append(html_row);
                            });
                            new_div.append('<br><br>Execution Time: '+result['execution_time']+' seconds');
                            new_div.append('<br><br>Process Time: '+Math.round((Date.now() - result['start_time'])/10)/100+' seconds');
                            content_container.prepend(new_div);
                        }
                        else
                        {
                            // counter = max_count;         // Force stop on error message returned
                            var new_div = $('<div />',{
                                'class': 'error'
                            });
                            $.each(result['error_message'], function( row_index, row ) {
                                new_div.append('<div class="success_row">'+row+'</div>');
                            });
                            new_div.append('<br><br>Execution Time: '+result['execution_time']+' seconds');
                            new_div.append('<br><br>Process Time: '+Math.round((Date.now() - result['start_time'])/10)/100+' seconds');
                            new_div.append('<br><br>Result:<br> '+result['error_status']+' seconds');
                            content_container.prepend(new_div);
                        }
                    } catch (e) {
                        if (result && typeof result === 'string') {
                            content_container.prepend('<div class="error"><p>'+result+'</p></div>');
                        } else{
                            content_container.prepend('<div class="error"><p>Unexpected result</p></div>');
                        }
                    }

                },
                'error': function(request, status, error) {
                    // counter = max_count;         // Force stop on other error - e.g. timeout
                    if (status == 'timeout')
                    {
                        content_container.prepend('<div class="error"><p>Timeout, requested server not responding</p></div>');
                    }
                    else
                    {
                        content_container.prepend('<div class="error"><p>Unknown Error, Submit Changes Failed</p></div>');
                    }
                },
                'complete': function()
                {
                    counter++;
                    if (counter >= max_count)
                    {
                        clearInterval(java_timer);
                        // clearInterval(api_ajax_timer);
                        $('#timer_container').addClass('complete');
                    } else {
                        call_handler();
                    }
                }
            });
        }
        // var api_ajax_timer = setInterval(call_handler, 180000);
        // console.log(call_handler);
        // call_handler();

        $('#start').click(function (){
            java_timer_start = Date.now();
            $('#timer_container').removeClass('complete');
            max_count = Math.floor(query_limit / limit);
            java_timer = setInterval(function(){
                java_time_seconds = Math.round((Date.now() - java_timer_start)/1000);
                $('#timer_container').html(java_time_seconds + ' seconds. Calls made: '+success_counter+' / '+counter+'.');
            }, 10);
            call_handler();
            $('#start').hide();
            $('#stop').show();
        });

        $('#stop').click(function (){
            max_count = 1;
            $('#start').show();
            $('#stop').hide();
        });
    });
</script>
</body>
</html>