<?php
/**
 *  Shortcodes documentation page
 */

?>

<br/><br/>
<h2>Global Shortcodes</h2>
<hr/>
	<h3>[global_alumni_number <em>divisor=int decimals=int</em>]</h3>
	<p>Outputs value of global variable: alumni</p>
	<strong>divisor</strong> (int) (Optional) Divide output by a certain number.
	<strong>decimals</strong> (int) (Optional) Display a given amount of decimal points.

	<em>Examples:</em>
	<pre>[global_alumni_number]</pre> => <?php echo do_shortcode( '[global_alumni_number]' ); ?>
	<pre>[global_alumni_number divisor=1000] thousand students</pre> => <?php echo do_shortcode( '[global_alumni_number divisor=1000]' ); ?> thousand students
	<pre>[global_alumni_number divisor=1000 decimals=2]</pre> => <?php echo do_shortcode( '[global_alumni_number divisor=1000 decimals=1]' ); ?> thousand students

<hr/>
	<h3>[global_degrees_number]</h3>
	<p>Outputs value of global variable: degrees</p>
	<strong>divisor</strong> (int) (Optional) Divide output by a certain number.
	<strong>decimals</strong> (int) (Optional) Display a given amount of decimal points.

	<em>Examples:</em>
	<pre>[global_degrees_number]</pre> => <?php echo do_shortcode( '[global_degrees_number]' ); ?>

<hr/>

<h3>[global_contact_phone <em>link(bool)</em>]</h3>
	<p>Outputs value of global variable: degrees</p>
	<strong>link</strong> (boolean) Show output as a clickable tel: href? Default false

	<em>Examples:</em>
	<pre>[global_contact_phone]</pre> => <?php echo do_shortcode( '[global_contact_phone]' ); ?>
	<pre>[global_contact_phone link]</pre> => <?php echo do_shortcode( '[global_contact_phone link=true]' ); ?>

<hr/>