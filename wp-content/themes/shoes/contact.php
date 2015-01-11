<?php
/*
Template Name: Liên Hệ
*/
?>

<?php get_header();?>

<!--- start-content---->
	<div class="content contact-main">
		<!----start-contact---->
		<div class="contact-info">
				<div class="map">
					<iframe width="100%" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps?f=q&amp;source=s_q&amp;hl=vi&amp;geocode=&amp;q=229+ph%E1%BB%91+V%E1%BB%8Dng,+%C4%90%E1%BB%93ng+T%C3%A2m,+H%C3%A0+N%E1%BB%99i,+Vi%E1%BB%87t+Nam&amp;aq=0&amp;oq=229+Ph%C3%B4&amp;sll=37.0625,-95.677068&amp;sspn=41.903538,79.013672&amp;t=h&amp;ie=UTF8&amp;hq=&amp;hnear=229+ph%E1%BB%91+V%E1%BB%8Dng,+%C4%90%E1%BB%93ng+T%C3%A2m,+Hai+B%C3%A0+Tr%C6%B0ng,+H%C3%A0+N%E1%BB%99i,+Vi%E1%BB%87t+Nam&amp;z=14&amp;iwloc=A&amp;ll=20.99569,105.842752&amp;output=embed"></iframe><br><small><a href="https://www.google.com/maps?f=q&amp;source=s_q&amp;hl=vi&amp;geocode=&amp;q=229+ph%E1%BB%91+V%E1%BB%8Dng,+%C4%90%E1%BB%93ng+T%C3%A2m,+H%C3%A0+N%E1%BB%99i,+Vi%E1%BB%87t+Nam&amp;aq=0&amp;oq=229+Ph%C3%B4&amp;sll=37.0625,-95.677068&amp;sspn=41.903538,79.013672&amp;t=h&amp;ie=UTF8&amp;hq=&amp;hnear=229+ph%E1%BB%91+V%E1%BB%8Dng,+%C4%90%E1%BB%93ng+T%C3%A2m,+Hai+B%C3%A0+Tr%C6%B0ng,+H%C3%A0+N%E1%BB%99i,+Vi%E1%BB%87t+Nam&amp;z=14&amp;iwloc=A&amp;ll=20.99569,105.842752&amp;output=embed" style="color:#666;text-align:left;font-size:12px"></a></small>
				 </div>
				 <div class="wrap">
				 <div class="contact-grids">
						 <div class="col_1_of_bottom span_1_of_first1">
								<h5>Address</h5>
								<ul class="list3">
									<li>
										<img src="<?php echo bloginfo('template_directory');?>/images/home.png" alt="">
										<div class="extra-wrap">
										 <p>P1603, chung cư A2, <br> 229 phố Vọng, Hà Nội</p>
										</div>
									</li>
								</ul>
							</div>
							<div class="col_1_of_bottom span_1_of_first1">
								<h5>Phones</h5>
								<ul class="list3">
									<li>
										   <img src="<?php echo bloginfo('template_directory');?>/images/phone.png" alt="">
										<div class="extra-wrap">
											<p><span>Telephone:</span>+84 999888999</p>
										</div>
											<img src="<?php echo bloginfo('template_directory');?>/images/fax.png" alt="">
										<div class="extra-wrap">
											<p><span>FAX:</span>+1 800 589 2587</p>
										</div>
									</li>
								</ul>
							</div>
							<div class="col_1_of_bottom span_1_of_first1">
								 <h5>Email</h5>
								<ul class="list3">
									<li>
										<img src="<?php echo bloginfo('template_directory');?>/images/email.png" alt="">
										<div class="extra-wrap">
										  <p><span class="mail"><a href="mailto:yoursite.com">allblue@gmail.com</a></span></p>
										</div>
									</li>
								</ul>
							</div>
							<div class="clear"></div>
				 </div>
					<form method="post" action="contact-post.html">
						  <div class="contact-form">
							<div class="contact-to">
								<input type="text" class="text" value="Name..." onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Name...';}">
								<input type="text" class="text" value="Email..." onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Email...';}">
								<input type="text" class="text" value="Subject..." onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Subject...';}">
							</div>
							<div class="text2">
							   <textarea value="Message:" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Message';}">Message..</textarea>
							</div>
						   <span><input type="submit" class="" value="Submit"></span>
							<div class="clear"></div>
						   </div>
					   </form>
						</div>
		</div>
		<!----//End-contact---->
	</div>

<?php get_footer();?>