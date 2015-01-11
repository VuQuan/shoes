		<!---start-footer---->
		<div class="footer">
			<div class="wrap">
				<div class="footer-left">
					<p> &copyCopyright Made in by Allblue - 2014 </p>
				</div>
				<div class="footer-right">
					<script type="text/javascript">
						$(document).ready(function() {
							/*
							var defaults = {
					  			containerID: 'toTop', // fading element id
								containerHoverID: 'toTopHover', // fading element hover id
								scrollSpeed: 1200,
								easingType: 'linear' 
					 		};
							*/
							
							$().UItoTop({ easingType: 'easeOutQuart' });
							
						});
					</script>
			    <a href="#" id="toTop" style="display: block;"><span id="toTopHover" style="opacity: 1;"></span></a>
				</div>
				<div class="clear"> </div>
			</div>
		</div>
        <!---//End-wrap---->
        <!---//End-footer---->


	<?php wp_footer(); ?>
	
	<!-- Don't forget analytics -->
	
</body>

</html>
