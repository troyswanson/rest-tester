<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>The REST Tester</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/global.css"/>
<link rel="stylesheet" type="text/css" href="css/pictos.css"/>
<script src="js/jquery-1.7.1.js"></script>
<script src="js/index.js"></script>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-20896597-2']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<script type="text/javascript" src="//use.typekit.net/zrf2poe.js"></script>
<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
</head>

<body>

<div id="container">
	<div id="header">
		<h1>The REST Tester</h1>
		<p>The <strong>REST Tester</strong> is a simple utility designed to help web developers debug RESTful API calls.</p>
		<p><strong>Just enter a URL to get started.</strong></p>
	</div>
	<div id="content">
		<div class="outer">
			<div class="inner">
			
				<form id="request_form">
					<div id="input_box">
						<h2>URL</h2>
						<input name="url" id="url" type="text" />
						<div id="add_boxes">
							<a id="add_auth" title="Add HTTP Authentication" href="#"></a>
							<a id="add_req_val" title="Add a Request Value" href="#"></a>
						</div>
						<div id="options">
							<div id="auth">
								<h3>Authentication</h3>
								<dl>
									<dt>Username</dt>
									<dd><input name="auth_un" type="text" /></dd>
									<dt>Password</dt>
									<dd><input name="auth_pw" type="text" /></dd>
								</dl>
								<div class="clear_l"></div>
							</div>
							<div id="req_vals">
								<h3>Request Values</h3>
								<div class="req_val">
									<div class="req_val_type_chooser">
										<ul>
											<li class="selected"><a href="#">Off</a></li>
											<li><a href="#">GET</a></li>
											<li><a href="#">POST</a></li>
										</ul>
									</div>
									<input name="req[type][]" type="hidden" class="req_val_type" value="Off" />
									<input name="req[name][]" type="text" />
									<input name="req[value][]" type="text" />
									<a class="req_val_remove pictos" href="#">D</a>
									<div class="clear_l"></div>
								</div>
							</div>
						</div>
					</div>
					<div id="execute_box">
						<a id="execute" href="#">Execute HTTP Request</a>
					</div>
					<div class="clear_l"></div>
				</form>
				
				<div id="results">
					<h3>Request</h3>
					<div id="request">
						<div class="request_line">
							<span class="method"></span>&nbsp;<span class="uri"></span>&nbsp;<span class="http_version"></span>
						</div>
						<div class="headers">
						</div>
						<div class="body">
						</div>
					</div>
					<h3>Response</h3>
					<div id="response">
						<div class="status_line">
							<span class="http_version"></span>&nbsp;<span class="code"></span>&nbsp;<span class="phrase"></span>
						</div>
						<div class="headers">
						</div>
						<div class="body">
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</div>
	<div id="footer">&copy;<?=date("Y")?> <a href="http://troy.im">Troy Swanson</a></div>
</div>

</body>
</html>