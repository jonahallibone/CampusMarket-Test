<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
<link href='https://fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>
<title>{{ $title }}</title>
	<style>

		::-webkit-input-placeholder {
			font-size: 40px;
			font-weight: 300;
			color: #8cc63f;
			text-align: center;
		}

		:-moz-placeholder { /* Firefox 18- */
			font-size: 40px;
			font-weight: 300;
			color: #8cc63f;
			opacity: 1;
		}

		::-moz-placeholder {  /* Firefox 19+ */
	 		font-size: 40px;
			font-weight: 300;
			color: #8cc63f;
		}

		:-ms-input-placeholder {
			font-size: 50px;
			font-weight: 300;
			color: #8cc63f;
		}


		select {
   		-webkit-appearance: none;
   		-moz-appearance: none;
   		appearance: none;
			font-size: 35px;
			line-height: 50px;
			border: 1px solid #8cc63f;
			color: #8cc63f;
			padding: 10px 5px 10px 10px;
			background-color: transparent;
			max-width: 600px;
			width: 600px;
			display: inline-block;
			font-weight: 300;
			outline: none;
			text-align: center;
			transition: .1s all linear;
			cursor: pointer;
	}

	select:hover {
		background-color: #8cc63f;
		color: white;
	}

		html,body {
			margin: 0;
			padding: 0;
			width: 100%;
			height: 100%;
			color: #FFF;
			background-color: #ecf0f1;
			display:block;
			font-weight: 100;
			font-family: 'Roboto', sans-serif;
			background-image: url('{{ URL::asset("assets/images/background-image.png") }}');
			background-size:cover;
			background-repeat: no-repeat;
		}

		.wrapper {
			max-width: 800px;
			margin: 0px auto;
			padding-bottom: 25px;
		}

		#main-frame {
			min-height: 100%;
			width: 100%;
			display: inline-block;
			text-align: left;
			padding-left: 20px;
			padding-right: 20px;
			box-sizing:border-box;
			background-color: rgba(44, 62, 80,0.7)
		}

		#middle-center {
			position: relative;
		}

		#logo {
			width: 100%;
			font-size: 0;
			padding: 20px 0 80px 0;
			display: block;
			clear: both;
			height: 29px;
		}

		.the-logo {
			width: 100px;
			float: left;
			display: inline;
			clear: both;
		}

		span.the-runner {
			font-size: 40px;
			font-weight: 300;
			color: #00aeef;
			line-height: 80px;
			clear: both;
			display: block;
			padding-bottom: 50px;
		}

		input.input-inline {
			border: none;
			border-bottom: dashed 1px #8cc63f;
			background-color: transparent;
			height: 75px;
			outline: none;
			display: inline-block;
			vertical-align: top;
			font-size: 40px;
			font-weight: 300;
			min-width: 300px;
			font-family: 'Roboto', sans-serif;
			color: #8cc63f;
		}

		.college-name {
			width: 340px;
		}

		.email {
			width: 400px;
		}

		.submission {
			height: 80px;
			border: 1px solid #8cc63f;
			border-radius: 2px;
			padding: 10px 20px 10px 20px;
			outline: none;
			background-color: transparent;
			color: #8cc63f;
			font-size: 28px;
			display: inline-block;
			font-family: 'Roboto', sans-serif;
			cursor: pointer;
			transition: .1s all linear;
		}

		.submission:hover, .working, .done {
			color: #FFF;
			background-color: #8cc63f;
			border-color: #8cc63f;
		}

	.working {
		cursor: wait;
	}

	.done {
		cursor: default;
	}

	@media(max-width:480px) {

			select {
   			font-size: 28px;
				padding: 10px;
				text-align: center;
				line-height: 30px;
			}

			html,body {
				background-image: none;
				min-height: 100%;
				text-align: left;
				width: 100%;
				background-color: #FFF;
			}

			#main-frame {
				background-color: #FFF;
			}

			.wrapper {
				margin: 0px auto;
			}

			span.the-runner {
				line-height: 60px;
			}

			span.the-runner, input.input-inline {
				font-size: 28px;
			}

			::-webkit-input-placeholder {
				font-size: 28px;
				text-align: left;
			}

			:-moz-placeholder { /* Firefox 18- */
				font-size: 28px;
				text-align: left;
			}

			::-moz-placeholder {  /* Firefox 19+ */
		 		font-size: 28px;
				text-align: left;
			}

			:-ms-input-placeholder {
				font-size: 28px;
				text-align: left;
			}


			input.input-inline {
				height: 60px;
				vertical-align: middle;
				text-align: left;
				display: block;
				/*width: 250px;*/
				padding-left: 0;
				min-width: 0px;
				border-radius: 0;
			}

			input.email {
				width: 270px;
			}

			.college-name {
				text-align: center;
				padding-right: 0;
				width: 270px;
			}

			#logo {
				padding-bottom: 50px;
			}


		}

	</style>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<script src="https://use.typekit.net/jmx2tat.js"></script>
	<script>try{Typekit.load({ async: true });}catch(e){}</script>
	<script type="text/javascript">
		$(document).ready(function() {
			$(".submission").click(function(){
				//Add Regex for white space and .edu ending..

				var collegeName = $(".college-name").val();
				var collegeEmail = $(".email").val();

				var collegeVerified = $.trim(collegeEmail).split("@");
				if (collegeVerified.length == 2) {
					collegeVerified = collegeVerified[1].split(".");
					if (collegeVerified[collegeVerified.length-1] == "edu") {
						isVerified = true;
					}
				}

				else {
					var isVerified = false;
					$(".email").css("color", "red");
					$(".email").css("border-color", "red");
				}

				if ($.trim($(".college-name").val()).length != 0 && $.trim($(".email").val()).length != 0 && isVerified ) {

					var token = $("input[name='_token']").val();

					$(this).addClass("working");
					$(this).html("Please wait...");
					$(this).attr('disabled','disabled');

					$.post("/waitinglist", { college: collegeName, email: collegeEmail, _token: token }, function(data) {
						$(".submission").removeClass("working").addClass("done");
						$(".submission").html(data);
						setTimeout(function(){ location.reload(); }, 5000);
					});
				}
			});
		});
	</script>
	<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-69205313-1', 'auto');
  ga('send', 'pageview');

</script>
</head>
	<body>
		<section id="main-frame">
			<div class="wrapper">
				<div id="logo">
					BlackMarket U
					<img src="{{ asset('assets/images/logo-1.png') }}" alt="BlackMarket U" class="the-logo" />
				</div>
			</div>
			<div id="middle-center">
				<div class="wrapper">
					<span class="the-runner">Join a BlackMarket at
					<select class="input-inline college-name" class="college-name">
						<option selected disabled>your school</option>
						<option value="champlain college">Champlain College</option>
						<option value="uvm">University of Vermont</option>
					</select>
					when we launch by signing up with
					<input type="text" class="input-inline email" placeholder="your college email">
					{{ Form::token(); }}
					</span>
					<button class="submission">Get Notified!</button>
				</div>
			</div>
		</section>
	</body>
</html>
