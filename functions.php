<?php

function redirection($page)
{
	echo "
	<script type='text/javascript'> 
	 window.location = '$page'
	</script>
	";
}

function start_lightbox($lightbox)
{
	echo "
	<script type='text/javascript'> 
	window.location.href = 'javascript:void(0)'
	document.getElementById('$lightbox').style.display='block';document.getElementById('fade').style.display='block'
	</script>
	";
}
?>


