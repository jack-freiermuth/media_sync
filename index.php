<!DOCTYPE HTML>
<html>

<head>
	<TITLE>Media Sync</TITLE>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<link rel="stylesheet" href="css/main.css">
<link rel="stylesheet" href="css/bootstrap.min.css">
<div class="container">
	<div class="row">
		<div class="col-sm-3">
		<?php include 'side_navbar.php'; ?>
		</div>
		<div class="col-sm-9">

			<div class="row"><!-- File Path Input -->
				<div class="col-lg-12 vertical-buffer">
					<div class="input-group">
						<span class="input-group-btn">
							<button class="btn btn-secondary directory_path_button" type="button">Go!</button>
						</span>
						<input id="directory_path" type="text" class="form-control" placeholder="File Path ex: I:\Movies\" value="I:\Movies\">
					</div>
				</div>
			</div><!-- End File Path Input -->

			<div class="row">
				<div class="col-lg-12 name_change_list_html"></div>
			</div>


		</div>
	</div>
</div>

</html>


<script type="text/javascript">

$('.directory_path_button').on('click', function() {
    $.ajax({
        url: 'movie_script.php',
        type: 'post',
        dataType: 'json',
        data: {
            'directory_path':$('#directory_path').val(),
        },
        success: function (data) {
        	$('.name_change_list_html').html(data.name_change_list_html);
            console.log('data: ', data.name_change_list_html);
        },
        error: function(jqXHR, textStatus, errorThrown) {
        	console.log('jqXHR: ',jqXHR);
            console.log('textStatus: ',textStatus);
            console.log('errorThrown: ',errorThrown);
        }
    });	
});
</script>