<html>
<head>
	<title>Google Location Retriever</title>
	<meta name="robots" content="noindex, nofollow">
    <script type="text/javascript" src="/allen_frame_trial/content/js/jquery-1.11.3.js"></script>
</head>
<body>
<div id="timer_container"></div>
<div id="result_container">
    Loading...
</div>
<style>
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
	var java_timer = setInterval(function(){
    java_time_seconds = Math.round((Date.now() - java_timer_start)/1000);
    $('#timer_container').html(java_time_seconds + ' seconds');
}, 10);
<?php
	$option = [
        'limit'=>5,
        'query_limit'=>1500
    ];
	if (isset($_GET['query_limit']))
    {
        // sync listing cost 2 queries per require (one address decoder, one geo reverse coding)
        $option['query_limit'] = $_GET['query_limit']/2;
    }
	if (isset($_GET['limit']))
    {
        $option['limit'] = $_GET['limit'];
    }
?>
var limit = <?=$option['limit']?>;
var counter = 0;
var query_limit = <?=$option['query_limit']?>;
var max_count = Math.floor(query_limit / limit);

var content_container = $('#result_container');
var api_ajax_timer = setInterval(function(){
java_time_seconds = (Date.now() - java_timer_start)/1000;
java_time_start = java_time_seconds;
var post_value = {
'limit': limit,
'start_time': Date.now()
};
$.ajax({
'type': 'POST',
'url': '/allen_frame_trial/developer/hierarchy_to_rel_handler.php',
'data': post_value,
'timeout': 30000,
'success': function(result_string) {
    console.log(result_string);
if (counter == 0)
{
content_container.html('');
}
var result = $.parseJSON(result_string);
if (result['success'] === true)
{
var new_div = $('<div />',{
'class': 'success'
});
$.each(result['updated_data'], function( row_index, row ) {
new_div.append('<div class="success_row">'+row+'</div>');
});
new_div.append('<br><br>Execution Time: '+result['execution_time']+' seconds');
new_div.append('<br><br>Process Time: '+Math.round((Date.now() - result['start_time'])/10)/100+' seconds');
content_container.prepend(new_div);
}
else
{
counter = max_count;
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
},
'error': function(request, status, error) {
counter = max_count;
if (status == 'timeout')
{
content_container.append('<div class="error"><p>Timeout, requested server not responding</p></div>');
}
else
{
content_container.append('<div class="error"><p>Unknown Error, Submit Changes Failed</p></div>');
}
},
'complete': function()
{
counter++;
if (counter >= max_count)
{
clearInterval(java_timer);
clearInterval(api_ajax_timer);
$('#timer_container').addClass('complete');
}
}
});
}, 5000);
});
</script>
</body>
</html>