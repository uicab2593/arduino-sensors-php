<!DOCTYPE html>
<html>
<head>
	<meta charset='utf-8'>
	<title>Sensores</title>
	<!-- Latest compiled and minified CSS -->
	<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css" integrity="sha384-aUGj/X2zp5rLCbBxumKTCw2Z50WgIr1vs/PFN4praOTvYXWlVyh2UtNUU0KAUhAX" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>
</head>
<body>
	<div class="container">
		<div class="row text-center">
			<div class="col-md-4">
				<h2>Boton</h2>
				<h4 class='text-danger'><i id='sensor0' class='fa fa-circle-o fa-3x'></i></h4>
				<form class='sensorConfig'>
					<input type='hidden' name='sensor' value='0'>
					<input type='checkbox' name='value' checked='checked'>
					<button type='submit' class='btn btn-default btn-xs'>Cambiar</button>
				</form>				
				<form class='sensorConfig'>
					<input type='hidden' name='sensor' value='0'>
					<input type='hidden' name='value' value='1000'>
					<button type='submit' class='btn btn-default btn-xs'>ID</button>
				</form>				
			</div>
			<div class="col-md-4">
				<h2>Movimiento</h2>
				<h4 class='text-danger'><i id='sensor1' class='fa fa-circle-o fa-3x'></i></h4>
				<form class='sensorConfig'>
					<input type='hidden' name='sensor' value='1000'>
					<input type='checkbox' name='value' checked='checked'>
					<button type='submit' class='btn btn-default btn-xs'>Cambiar</button>
				</form>				
				<form class='sensorConfig'>
					<input type='hidden' name='sensor' value='1'>
					<input type='hidden' name='value' value='1000'>
					<button type='submit' class='btn btn-default btn-xs'>ID</button>
				</form>				
			</div>
			<div class="col-md-4">
				<h2>Luz</h2>
				<h4 class='text-danger'><i id='sensor2' class='fa fa-circle-o fa-3x'></i></h4>
				<p id='lightValue'>100</p>
				<form class='sensorConfig'>
					<input type='hidden' name='sensor' value='2'>
					<input id='lightRange' type='range' name='value' value='100' min='0' max='255'>
					<button type='submit' class='btn btn-default btn-xs'>Cambiar</button>
				</form>
				<form class='sensorConfig'>
					<input type='hidden' name='sensor' value='2'>
					<input type='hidden' name='value' value='1000'>
					<button type='submit' class='btn btn-default btn-xs'>ID</button>
				</form>								
			</div>
		</div>
	</div>
<script src="http://autobahn.s3.amazonaws.com/js/autobahn.min.js"></script>
<script>
    var conn = new ab.Session('ws://sensors:8080',
        function() {
        	console.log('conexion lista');
            conn.subscribe('all',function(topic,data) {
            	var r = JSON.parse(data);
            	if(r.state=='1'){
            		$("#sensor"+r.sensor).removeClass("fa-circle-o");
            		$("#sensor"+r.sensor).addClass("fa-circle");
            	}else{
            		$("#sensor"+r.sensor).removeClass('fa-circle');
            		$("#sensor"+r.sensor).addClass("fa-circle-o");
            	}
            });
        },
        function() {
            console.warn('conexion terminada');
        },
        {'skipSubprotocolCheck': true}
    );

    $(document).ready(function(){
	    $(".sensorConfig").submit(function(e){
	    	var obj = $(this);
	    	e.preventDefault();
	    	var valueInput = obj.find("[name='value']"); 
	    	$.post('/writingPort.php',{
	    		sensor: obj.find("[name='sensor']").val(),
	    		value: valueInput.attr('type')=='checkbox'?(valueInput.prop('checked')?1:0):valueInput.val()
	    	});
	    });
    	$("#lightRange").change(function(){
    		$("#lightValue").text($(this).val());
    	});
    });
</script>
</body>
</html>