<div class="wrap about-wrap">

	<h1>Welcome to AJAX File Upload v. 0.1!</h1>

	<p class="about-text">Thank you for using AJAX File Upload to quickly process your uploads on the go!</p>
	
	<div style="display: block; background: #fff; text-align: center; padding: 1em 0;"> <img src="http://i.imgur.com/qVPdWkb.png" alt="preview" style=" display: table; margin: 0 auto;"> <h2 style="font-style: italic;display: inline-block;">A small preview</h2><em style=" color: #C1C1C1; margin-left: 3px; font-size: 90%;"> WpChats 3.0 profile edit</em> </div>

	<p>This plugin will help you add file upload feature to your site, set maximum upload size, allowed file extensions, and much more through a simple shortcode or a custom function.</p>

	<p>Totally AJAX, your uploads will be processed faster and an elegant way. All you need to do is to add the shortcode to your content, or call the plugin's custom function whithin your code and that's it.</p>

	<p><a href="options-general.php?page=ajax-file-upload" class="button button-primary">Go to settings</a> or carry on with the useful documentation</p>

	<h2 id="afu-shortcode">The Shortcode</h2>

	<p>You can use <code>[ajax-file-upload]</code> to output the AJAX file uploader, set its settings, markup, and much more through the shortcode attributes.</p>

	<h3>Shortcode attributes</h3>

	<div style="display: inline-block; max-width: 30%; vertical-align: top;">
	
		<ol>
							<li><a href="#unique_identifier"><code>unique_identifier</code></a></li>
							<li><a href="#max_size"><code>max_size</code></a></li>
							<li><a href="#allowed_extensions"><code>allowed_extensions</code></a></li>
							<li><a href="#permissions"><code>permissions</code></a></li>
							<li><a href="#on_success_alert"><code>on_success_alert</code></a></li>
							<li><a href="#on_fail_alert"><code>on_fail_alert</code></a></li>
							<li><a href="#on_fail_alert_error_message"><code>on_fail_alert_error_message</code></a></li>
							<li><a href="#on_success_set_input_value"><code>on_success_set_input_value</code></a></li>
							<li><a href="#set_background_image"><code>set_background_image</code></a></li>
							<li><a href="#set_image_source"><code>set_image_source</code></a></li>
							<li><a href="#disallow_remove_button"><code>disallow_remove_button</code></a></li>
							<li><a href="#disallow_reupload"><code>disallow_reupload</code></a></li>
							<li><a href="#upload_button_value"><code>upload_button_value</code></a></li>
							<li><a href="#select_file_button_value"><code>select_file_button_value</code></a></li>
							<li><a href="#remove_file_button_value"><code>remove_file_button_value</code></a></li>
							<li><a href="#show_preloader"><code>show_preloader</code></a></li>
							<li><a href="#default_loading_text"><code>default_loading_text</code></a></li>
							<li><a href="#on_success_dialog_prompt_value"><code>on_success_dialog_prompt_value</code></a></li>
					</ol>
	
	</div>

	<div style="display: inline-block; max-width: 65%; vertical-align: top; margin-left: 7px;">

		<h3 id="unique_identifier">1. unique_identifier <span style="font-size:80%;font-weight:normal;">— required</span></h3>
		<p>This is a required attribute. Insert anything random that makes the uploader unique, used to store the uploader settings in the database and get them while processing a file for the shortcode in use.<br>Example use: <code>unique_identifier="my_contact_form"</code></p>

		<h3 id="max_size">2. max_size <span style="font-size:80%;font-weight:normal;">— optional</span></h3>
		<p>Allows you to set the maximum file size to upload in KB. 1 MB is equal to 1000 KB.<br>Example use: <code>max_size=3000</code> for 3 MB upload</p>

		<h3 id="allowed_extensions">3. allowed_extensions <span style="font-size:80%;font-weight:normal;">— optional</span></h3>
		<p>With this attribute you can set the allowed file extensions to upload. Please separate the extensions with commas.<br>Example use: <code>allowed_extensions="pdf,txt"</code></p>

		<h3 id="permissions">4. permissions <span style="font-size:80%;font-weight:normal;">— optional</span></h3>
		<p>Set the required permission for a user to upload a file. You can set <code>all</code> for all users (even logged-out ones), or <code>logged_in</code> for logged-in users only, or a custom user role e.g <code>author</code>. If you choose to set a role, the current user is required to have that role in order to process an upload. </p>

		<h3 id="on_success_alert">5. on_success_alert <span style="font-size:80%;font-weight:normal;">— optional</span></h3>
		<p>Alert the user that their upload is done. You can set custom messages to output within the dialog. It is optional, and no default alert is there if you don't set this attribute.<br>Example use: <code>on_success_alert="Your file was successfully uploaded"</code></p>

		<h3 id="on_fail_alert">6. on_fail_alert <span style="font-size:80%;font-weight:normal;">— optional</span></h3>
		<p>When a file is not processed successfully, you can use this attribute to alert the user with a custom message.<br>Example use: <code>on_fail_alert="We couldn't have your file uploaded. Try again?"</code></p>

		<h3 id="on_fail_alert_error_message">7. on_fail_alert_error_message <span style="font-size:80%;font-weight:normal;">— optional</span></h3>
		<p>An error message is always included in the AJAX response while a file was not processed with success. To alert this message to the user, you just need to add this attribute with some value that makes it true.<br>Example use: <code>on_fail_alert_error_message="true"</code></p>

		<h3 id="on_success_set_input_value">8. on_success_set_input_value <span style="font-size:80%;font-weight:normal;">— optional</span></h3>
		<p>You might have a hidden or visible input within your form where you want to put the uploaded media URL. If so, then use this attribute and put that field selector as the attribute value. If this field was not found in the DOM, an error will be added to the console.<br>Example use: <code>on_success_set_input_value="#my_hidden_input"</code></p>

		<h3 id="set_background_image">9. set_background_image <span style="font-size:80%;font-weight:normal;">— optional</span></h3>
		<p>When uploading an image, you probably want to use this attribute to set an element's background-image property with the returned image upload URL. The value has to be a valid element selector.<br>Example use: <code>set_background_image=".cover-photo-container"</code></p>

		<h3 id="set_image_source">10. set_image_source <span style="font-size:80%;font-weight:normal;">— optional</span></h3>
		<p>Same here, when uploading an image you would probably want to set an image source attribute with the returned image upload URL. If so then provide this image selector in the DOM.<br>Example use: <code>set_image_source="img.profile-pic"</code></p>

		<h3 id="disallow_remove_button">11. disallow_remove_button <span style="font-size:80%;font-weight:normal;">— optional</span></h3>
		<p>This attribute allows you to not provide a file delete button with the shortcode output. It can be handy when you don't want a user to multiple files everytime as they hit this button. Set its value and the button won't be there.<br>Example use: <code>disallow_remove_button="1"</code></p>

		<h3 id="disallow_reupload">12. disallow_reupload <span style="font-size:80%;font-weight:normal;">— optional</span></h3>
		<p>This can be handy to prevent users to upload multiple files. Set its value to something that makes it true, as you do, when a user uploads a file, the upload and remove button will both disappear, and the select one will become disabled.<br>Example use: <code>disallow_reupload=""</code></p>

		<h3 id="upload_button_value">13 upload_button_value <span style="font-size:80%;font-weight:normal;">— optional</span></h3>
		<p>The default value is "upload" and translatable through the admin settings. Lets you edit the upload button text.<br>Example use: <code>upload_button_value="upload file"</code></p>

		<h3 id="select_file_button_value">14. select_file_button_value <span style="font-size:80%;font-weight:normal;">— optional</span></h3>
		<p>The default value is "choose file" and translatable through the admin settings. Lets you edit the select button text..<br>Example use: <code>select_file_button_value="choisir un fichier"</code></p>

		<h3 id="remove_file_button_value">15. remove_file_button_value <span style="font-size:80%;font-weight:normal;">— optional</span></h3>
		<p>The default value is "remove" and translatable through the admin settings. Lets you edit the remove button text..<br>Example use: <code>remove_file_button_value="click to remove this file"</code></p>

		<h3 id="show_preloader">16. show_preloader <span style="font-size:80%;font-weight:normal;">— optional</span></h3>
		<p>If you have a hidden preloader which you want to show while the upload is being processed, provide its selector in the attribute value.<br>Example use: <code>show_preloader=".loading"</code></p>

		<h3 id="default_loading_text">17. default_loading_text <span style="font-size:80%;font-weight:normal;">— optional</span></h3>
		<p>The default value is "uploading.." and it is translatable. When you don't have <code>show_preloader</code> set, the select file button's text will change to this attribute value while an upload is processing, and the icon will change to a spin icon (icons by fontello.com)<br>Example use: <code>default_loading_text="Processing your file. please wait.."</code></p>

		<h3 id="on_success_dialog_prompt_value">18. on_success_dialog_prompt_value <span style="font-size:80%;font-weight:normal;">— optional</span></h3>
		<p>This will let your uses get the media URI of what they have uploaded. A dialog box will prompt with acustom message and an input where they can copy the file URL. in the attribute value, you can set the message to show above the input in the dialog box.<br>Example use: <code>on_success_dialog_prompt_value="Upload done! make sure to copy your URL below and save it!"</code></p>

	</div>

	<h2 id="the-function">The function</h2>

	<p>In your PHP template, you could call the shortcode with <code>do_shortcode('[ajax-file-upload ..]')</code> WordPress's native shortcode parser function. Or, there's this function <code>ajax_file_upload()</code> you can use instead, carry on.</p>

	<p><code>ajax_file_upload( $args )</code> accepts an array of arguments, these arguments are basically the shortcode attributes! So, keep consulting the above list if you need an element to add to your function..</p>

	<p>Here's an example use:</p>

	<pre>$args = array(
    "unique_identifier" =&gt; "my_subscription_form_file_upload",
    "allowed_extensions" =&gt; "jpg, png, bmp, gif",
    "on_success_alert" =&gt; "Your file was uploaded. Please continue with your subscription operation."
);
echo ajax_file_upload( $args );
	</pre>

	<h2>Further usage</h2>

	<h3>Child theme</h3>

	<p>This plugin supports child theme. You can copy the entire plugin file to your active theme, and there you can modifications and changes to all the plugin files except the <code>ajax-file-upload.php</code> which is the core file. Neat huh? useful when you want to customize the shortcode template more or edit a little bit of jQuery and CSS..</p>

	<h3 id="js-events">JavaScript events</h3>

	<p>This plugin creates DOM events that you can hook into to perform your required actions. Here are the events created by far:</p>

	<h3>afu_got_response</h3>

	<p><code>afu</code>? that's the plugin's prefix. This event is initiated when an upload is processed or not, mainly when the ajax request is completed successfully, and it has data attached to it which you can access through <code>event.data</code> method:</p>

	<pre>window.addEventListener( "afu_got_response", function(e){
    var data = e.data; // full data object
    if( data.response.success ) { // success
        console.log( data.response.media_uri ); // the uploaded media URL
    }
}, false);
	</pre>

	<p>Do a <code>console.log(e.data)</code> there's much more useful data included with the event's attached data.</p>

	<p><img src="http://i.imgur.com/s6v6BFz.png"></p>

	<h3>afu_file_uploaded</h3>

	<p>Same as the previous one, but <code>afu_file_uploaded</code> is only called when the file was successfully uploaded. You can use it to get the media URI with <code>e.data.response.media_uri</code> as you set the event listener</p>

	<pre>window.addEventListener( "afu_file_uploaded", function(e){
    if( "undefined" !== typeof e.data.response.media_uri ) {
        console.log( e.data.response.media_uri ); // the uploaded media URL
    }
}, false);
	</pre>	

	<h3>afu_error_uploading</h3>

	<p>Runs when a file was not uploaded. Useful data are included about the file, the applied settings and the response.</p>

	<pre>window.addEventListener( "afu_error_uploading", function(e){
    console.log( e.data ); // debugging
}, false);
	</pre>			

	<h3>afu_file_removed</h3>

	<p>Runs when a file was not uploaded. Useful data are included about the file, the applied settings and the response.</p>

	<pre>window.addEventListener( "afu_file_removed", function(e){
    console.log( e.data.this ); // the clicked button
    console.log( e.data.container ); // the container div of this button if you want to apply changes to its chilren or so
}, false);
	</pre>

	<h2>Thank you!</h2>

	<p>Thanks for reading about this plugin. This plugin is totally free and open-source, that means you can contribute to it to add many more cool features and improve it better. Interested? hit me up at samelh.com/contact/. AFU is available on the free open source software repository <a href="https://github.com/elhardoum/AJAX-File-Upload" target="_blank">Github</a>.</p>

	<p>If you like it by far, please take some time to leave us a ☆☆☆☆☆ rating on WordPress, a star ☆ on the Github repository, and thank you for your time!</p>

	<p><a href="options-general.php?page=ajax-file-upload" class="button button-primary">Go to settings</a></p>

	<p style="text-align:center">
		<a href="https://github.com/elhardoum/AJAX-File-Upload" target="_blank">Star on Github</a>
		- <a href="https://twitter.com/intent/tweet?text=Check+out+AJAX+File+Upload+%23free+%23WordPress+plugin+https%3a%2f%2fprofiles.wordpress.org%2felhardoum%23content-plugins+via+%40samuel_elh" target="_blank">Share on Twitter</a>
		- <a href="https://twitter.com/samuel_elh" target="_blank">Follow @Samuel_Elh on Twitter</a>
		- <a href="https://profiles.wordpress.org/elhardoum#content-plugins">Find support</a>
		- <a href="http://samelh.com">samelh.com</a>
	</p>

</div>
