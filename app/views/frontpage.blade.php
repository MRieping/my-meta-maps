<!DOCTYPE html>
<html lang="de">

	<head>

		<title>My Meta Maps</title>
    
	    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>	
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    
	    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.0.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="/css/style.css" type="text/css">
	    
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
		<link rel="icon" href="/favicon.ico" type="image/x-icon">

	    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

		<!-- For the Map -->
		<link rel="stylesheet" href="http://openlayers.org/en/v3.0.0/css/ol.css" type="text/css">
		<link rel="stylesheet" href="/css/mapstyle.css" type="text/css">
		<script src="http://openlayers.org/en/v3.0.0/build/ol.js" type="text/javascript"></script>
		<script src="/js/mapscript.js" type="text/javascript"></script>

		<!-- For formValidator-plugin -->
		<link rel="stylesheet" href="/js/plugins/formValidator/css/formValidator.css" type="text/css"/>
		
		<!-- For the datePicker-plugin -->
	    <link rel="stylesheet" href="/js/plugins/datePicker/datepicker.min.css" type="text/css"/>
	    
	    <!-- For barRating-plugin -->
	    <link rel="stylesheet" href="/js/plugins/barRating/css/rating-plugin.css" type="text/css"/>
	    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700" type="text/css"/>
	    
	    <!-- For barRating-plugin; loaded in header, otherwise it doesnt works -->
	    <script type="text/javascript" src="/js/plugins/barRating/jquery.barrating.min.js"></script>
    	<script type="text/javascript" src="/js/plugins/barRating/rating-views.js"></script>

	</head>

	<body onload="drawmap()">

		<!-- Navbar - beginning -->
		<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
			<!-- Div/row for navbar-header - beginning -->
			<div class="row clearfix">
				<div class="col-md-12 column">
					<!-- Start: Logo -->
					<div class="navbar-header">
						<a class="navbar-brand logo" href="#">
							<img src="/img/logo.png" alt="My Meta Maps">
						</a>
					</div>
					<!-- End: Logo -->
					<!-- Div/row for navbar-collapse - beginning -->
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<!-- Start: Language Chooser -->
						<ul class="nav navbar-nav navbar-left">
							<div class="navbar-form btn-group" role="group">
								<a href="#" class="btn btn-default active" role="button"><img src="/img/flags/en.png" alt="English"></a>
								<a href="#" class="btn btn-default" role="button"><img src="/img/flags/de.png" alt="Deutsch"></a>
								<a href="#" class="btn btn-default" role="button"><img src="/img/flags/nl.png" alt="Nederlands"></a>
							</div>
						</ul>
						<!-- End: Language Chooser -->
						<ul class="nav navbar-nav navbar-right">
							<!-- Start: Add geodata/comment -->
							<form class="navbar-form navbar-left">
								<button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#ModalAddComment" id="commentBtn"> Kommentar erstellen &nbsp;
									<span class="glyphicon glyphicon-plus-sign"></span>
								</button>
							</form>
							<!-- End: Add geodata/comment -->
							<!-- Start: Account navigation -->
							<form class="navbar-form navbar-left">
								<div class="btn-group" role="group">
									<button type="submit" class="btn btn-default disabled"> <span class="glyphicon glyphicon-user"></span>&nbsp; Account </button>&nbsp;
									<button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#ModalLogin" id="loginBtn"> Anmelden &nbsp;
										<span class="glyphicon glyphicon-log-in"></span>
									</button>
									<button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#ModalRegister" id="registerBtn"> Registrieren&nbsp;
										<span class="glyphicon glyphicon-edit"></span>
									</button>
								</div>
							</form>	
							<!-- End: Account navigation -->
							<!-- Start: Help navigation -->
							<form class="navbar-form navbar-right">
								<button type="submit" class="btn btn-danger" data-toggle="modal" data-target="#ModalHelp" id="helpBtn"><span class="glyphicon glyphicon-question-sign"></span></button>
							</form>
							<!-- End Help navigation -->
						</ul>
					</div>	
					<!-- Div/row for navbar-collapse - ending -->
				</div>
			</div>
			<!-- Div/row for navbar-header - ending -->
		</nav>
		<!-- Navbar - ending -->

		<!-- Spacing to the navbar -->
		<div id="spacing" style="height: 70px;">&nbsp;</div>
		
		<div class="buttons">
            <button class="blue-pill deactivated rating-enable" style="display: none;">enable</button>
        </div>
	
		<!-- Header - beginning --> 
		<!-- Müsste später durch ein template ersetzt werden, um Benutzerhilfe nur beim ersten Start anzuzeigen 
			und Einstellungen der Filter speichern zu können -->
		<header class="row clearfix" style="margin-left: 30px; margin-right: 30px">

			<!-- Div for the alert for user-help - beginning -->
			<div class="alert alert-warning alert-dismissible">
				<button type="button" class="close" data-dismiss="alert">
					<span aria-hidden="true">&times;</span><span class="sr-only">Schließen</span>
				</button>
				<strong>Benutzerhilfe</strong> &nbsp; 
				Klicke oben auf <button type="submit" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#ModalHelp" id="helpBtn"><span class="glyphicon glyphicon-question-sign"></span></button> für weitere Informationen
			</div>
			<!-- Div for the alert for user-help - ending -->

			<!-- Div for the filters - beginning -->
			<div class="row clearfix">
				<div class="col-md-3 column">
					<div class="input select rating-underline">
						<select id="spatialFilter">
					 	   <option value="" selected="selected"></option>
					 	   <option value="5">5</option>
						   <option value="10">10</option>
						   <option value="20">20</option>
						   <option value="50">50</option>
						   <option value="100">100</option>
						   <option value="200">200</option>
						   <option value="500">500</option>
						</select>
				    </div>
				</div>
				<div class="col-md-2 column">
					<div class="input select rating-stars" style="margin-left: 12px">
			            <select id="ratingFilter">
			                <option value="" selected="selected"></option>
			                <option value="1">1</option>
			                <option value="2">2</option>
			                <option value="3">3</option>
			                <option value="4">4</option>
			                <option value="5">5</option>
			            </select>
			        </div>
				</div>
				<div class="col-md-2 column">
			    	<div class="form-group">
			        	<div class="input-group">
			            	<input class="form-control" type="text" placeholder="Startzeitpunkt" datepicker data-trigger="#show-datepicker-start">
								<span id="show-datepicker-start" class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
			            </div>
			        </div> 
			    </div>
			    <div class="col-md-2 column">
			    	<div class="form-group">
			        	<div class="input-group">
			            	<input class="form-control" type="text" placeholder="Endzeitpunkt" datepicker data-trigger="#show-datepicker-end">
								<span id="show-datepicker-end" class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
			            </div>
			        </div> 
			    </div>
				<div class="col-md-3 column">
					<div class="input-group">
						<input type="text" class="form-control" placeholder="Stichwortsuche">
						<div class="input-group-btn">
							<button type="submit" class="btn btn-primary"> <span class="glyphicon glyphicon-search"></span> </button>&nbsp;
						</div>
					</div>
				</div>
			<!-- Div for the filters - beginning -->
			</div>
			<hr>		
		</header>
		<!-- Header - ending -->

		<!-- Section - beginning -->
		<!-- Müsste später durch ein template ersetzt werden -->
		<section class="row clearfix" style="margin-left: 30px; margin-right: 30px">	
			<!-- Div for map/comments - beginning -->
			<div class="row clearfix" id="mapComments">
				<!-- Div for map - beginning -->
				<div class="col-md-8 column">
					<p>
						<div id="map"></div>
					</p>
				</div>
				<!-- Div for map - ending -->
				
				<!-- Div for comments - beginning -->
				<div class="col-md-4 column">
					<h3 class="text-info">
						Kommentare ...
					</h3>
					<br>
					
					<!-- Tabs for comments with and woithout spatial reference -->
					<ul class="nav nav-tabs nav-justified">
					    <li class="active"><a href="#commentWithGeo" data-toggle="tab">... mit räumlichem Bezug</a></li>
					    <li><a href="#commentWithOutGeo" data-toggle="tab">... ohne räumlichen Bezug</a></li>
					</ul>
					<div class="tab-content commentScrollBox">
						
						<!-- Tab for comments with spatial reference -->		
						<div role="tabpanel" class="tab-pane active list-group" id="commentWithGeo">
							<!-- Unordered list for comments with spatial reference-->
							<!-- Durch template ersetzen -->
							<a href="#" class="list-group-item">Geodatensatz ???</a>
							<a href="#" class="list-group-item">Geodatensatz ???</a>
							<a href="#" class="list-group-item">Geodatensatz ???</a>
							<a href="#" class="list-group-item">Geodatensatz ???</a>
							<a href="#" class="list-group-item">Geodatensatz ???</a>
							<a href="#" class="list-group-item">Geodatensatz ???</a>
							<a href="#" class="list-group-item">Geodatensatz ???</a>
							<a href="#" class="list-group-item">Geodatensatz ???</a>
							<a href="#" class="list-group-item">Geodatensatz ???</a>
							<a href="#" class="list-group-item">Geodatensatz ???</a>
							<a href="#" class="list-group-item">Geodatensatz ???</a>
							<a href="#" class="list-group-item">Geodatensatz ???</a>
							<a href="#" class="list-group-item">Geodatensatz ???</a>
						</div>
						
						<!-- Tab for comments without spatial reference -->
						<div role="tabpanel" class="tab-pane list-group" id="commentWithOutGeo">
							<!-- Unordered list for comments -->
							<!-- Durch template ersetzen -->
							<a href="#" class="list-group-item">Geodatensatz xyz</a>
							<a href="#" class="list-group-item">Geodatensatz xyz</a>
							<a href="#" class="list-group-item">Geodatensatz xyz</a>
							<a href="#" class="list-group-item">Geodatensatz xyz</a>
							<a href="#" class="list-group-item">Geodatensatz xyz</a>
							<a href="#" class="list-group-item">Geodatensatz xyz</a>
							<a href="#" class="list-group-item">Geodatensatz xyz</a>
							<a href="#" class="list-group-item">Geodatensatz xyz</a>
							<a href="#" class="list-group-item">Geodatensatz xyz</a>
							<a href="#" class="list-group-item">Geodatensatz xyz</a>
							<a href="#" class="list-group-item">Geodatensatz xyz</a>
							<a href="#" class="list-group-item">Geodatensatz xyz</a>
						</div>
						
					</div>
				</div>
				<!-- Div for comments - ending -->
			</div>
			<!-- Div for map/comments - ending -->
		</section>
		<!-- Section - ending -->

		<br> <!-- Spacing to the footer/navbar on the bottom -->

		<footer>
			<nav class="navbar navbar-default" style="margin-bottom: 0px" role="navigation">
				<ul class="nav navbar-nav navbar-right">
					<form class="navbar-form navbar-right">
						<button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#ModalInfo"> Impressum&nbsp;
							<span class="glyphicon glyphicon-info-sign"></span>
						</button>
					</form>
				</ul>
			</nav>
		</footer>


		<!-- Modals; will shown if a certain button is clicked -->
		<!-- Manche müssen wir noch zu templates ändern oder durch andere Möglichkeiten ersetzen -->
		<!-- Vielleicht kann man auch nur ein Modal für alle 'bereit stellen' -->
		
		<div id="addCommentContainer">
			<!-- Has to be empty -->
		</div>
		
		<!-- Lässt sich dies irgendwie extrahieren ??? -->
		<script type="text/template" id="addCommentTemplate">
	 		 <!-- Modal for comments -->
			<div class="modal fade" id="ModalAddComment" tabindex="-1" role="dialog" aria-labelledby="meinModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Schließen</span></button>
							<h4 class="modal-title" id="meinModalLabel">Kommentar erstellen</h4>
						</div>
						
						<div class="modal-body">
											
							<form id="form-comment" onsubmit="return false">
								
								<div class="row form-group">
								<label for="titleInput">Titel</label>
								<input class="form-control" name="titleInput" id="inputTitle" type="text" data-bvStrict="true" data-bvTransform="noSpaces">
								</div>
								
								<div class="row form-group">
									<label for="URLInput">URL*</label>
									<input class="form-control" name="URLInput" id="inputURL" type="text" data-bvStrict="URL" data-bvSwitch="">
									<div class="help-block error-message">Bitte füge eine (valide) URL eines Geodatensatzes hinzu</div>
								</div>
								
								<div class="row form-group">
									<label for="textInput">Freitext*</label>
									<textarea class="form-control" rows="3" name="textInput" id="inputText" type="text" data-bvStrict="notEmpty" data-bvSwitch=""></textarea>
									<div class="help-block error-message">Bitte füge einen Freitext hinzu</div>
								</div>
								
								<div class="row form-group">
									<label for="startDateInput">Zeitraum</label>
									<div class="input-group">
						            	<input class="form-control" readonly name="startDateInput" id="inputStartDate" type="text" data-bvStrict="datesRelation:endDateInput" placeholder="Startzeitpunkt" data-date-format="dd/mm/yyyy" datepicker data-trigger="#show-datepicker-startComment">
											<span id="show-datepicker-startComment" class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
						            </div>
						            <span class="help-block error-message">Überprüfe bitte noch mal beide Daten</span>
								</div>
								
								<div class="row form-group">
									<div class="input-group">
						            	<input class="form-control" readonly name="endDateInput" id="inputEndDate" type="text" data-bvStrict="date:dd/mm/yyyy|empty" placeholder="Endzeitpunkt" data-date-format="dd/mm/yyyy" datepicker data-trigger="#show-datepicker-endComment">
											<span id="show-datepicker-endComment" class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
						            </div>
						            <span class="help-block error-message">Übeprüfe bitte noch mal dieses Datum</span>
								</div>
								
								<div class="row form-group">
									<div class="input select rating-stars">
										<label for="ratingComment">Bewertung</label>
										<select id="ratingComment" name="rating">
											<option value="" selected="selected"></option>
									    	<option value="1">1</option>
									        <option value="2">2</option>
									        <option value="3">3</option>
									        <option value="4">4</option>
									        <option value="5">5</option>
										</select>
									</div>
								</div>
								
								<button type="submit" class="btn btn-primary" id="addCommentBtn">Erstellen</button>
					
							</form>
							
						</div>
						<div class="modal-footer">
							<div class="row clearfix">
								<div class="col-md-2 column">
									<label>*Verpflichtend</label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</script>

		<!-- Modal for login -->
		<div class="modal fade" id="ModalLogin" tabindex="-1" role="dialog" aria-labelledby="meinModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Schließen</span></button>
						<h4 class="modal-title" id="meinModalLabel">Anmelden</h4>
					</div>
					<div class="modal-body">
						
						<form id="form-login">
							
							<div class="row form-group">
								<label for="usernameInput">E-Mail-Adresse / Benuzername</label>
								<input class="form-control" rows="3" name="usernameInput" id="inputUsername" type="text" data-bvStrict="notEmpty" data-bvSwitch=""></textarea>
								<div class="help-block error-message">Bitte füge deine E-Mail-Adresse oder deine Benutzernamen hinzu</div>
							</div>
								
							<div class="row form-group">
								<label for="passwordLoginInput">Passwort</label>
								<input class="form-control" name="passwordLoginInput" id="inputPasswordLogin" type="password" data-bvStrict="reg:^.{5,}">
								<span class="help-block error-message">Passwort muss mindestens aus 5 Zeichen bestehen</span>
							</div>
							
							<button type="submit" class="btn btn-primary">Anmelden</button>
							
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal for register -->
		<!-- Erfolgsmeldung fehlt noch -->
		<div class="modal fade" id="ModalRegister" tabindex="-1" role="dialog" aria-labelledby="meinModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Schließen</span></button>
						<h4 class="modal-title" id="meinModalLabel">Registrieren</h4>
					</div>
					<div class="modal-body">
			
						<form id="form-register">
							
							<div class="row form-group">
								<label for="nameInput">Name</label>
								<input class="form-control" name="nameInput" id="inputName" type="text" data-bvStrict="true" data-bvTransform="noSpaces">
							</div>
							
							<div class="row form-group">
								<label for="mailInput">E-Mail-Adresse</label>
								<input class="form-control" name="mailInput" id="inputName" type="text" data-bvStrict="email" data-bvEmpty="@">
								<div class="help-block error-message">Dies ist keine E-Mail-Adresse</div>
							</div>
							
							<div class="row form-group">
								<label for="passwordRegisterInput">Passwort</label>
								<input class="form-control" name="passwordRegisterInput" id="inputPasswordRegister" type="password" data-bvStrict="reg:^.{5,}">
								<span class="help-block error-message">Passwort muss mindestens aus 5 Zeichen bestehen</span>
							</div>
							
							<div class="row form-group">
								<label for="passRepeatInput">Passwort wiederholen</label>
								<input class="form-control" name="passRepeatInput" id="inputPassRepeat" type="password" data-bvStrict="same:passwordRegisterInput">
								<span class="help-block error-message">Passwörter stimmen nicht überein</span>
							</div>
							
							<button type="submit" class="btn btn-primary">Registrieren</button>
							
						</form>
						
					</div>
				</div>
			</div>
		</div>

		<!-- Modal for info-site -->
		<div class="modal fade" id="ModalInfo" tabindex="-1" role="dialog" aria-labelledby="meinModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Schließen</span></button>
						<h4 class="modal-title" id="meinModalLabel">Impressum</h4>
					</div>
					<div class="modal-body">
						@include('pages.about')
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Schließen</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal for user-help -->
		<div class="modal fade" id="ModalHelp" tabindex="-1" role="dialog" aria-labelledby="meinModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Schließen</span></button>
						<h4 class="modal-title" id="meinModalLabel">Benutzerhilfe</h4>
					</div>
					<div class="modal-body">
						<form role="form">
							<div class="form-group">
								@include('pages.help')
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Schließen</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal for Users -->
		<div id="ModalUser">

		</div>

		<!-- Modals ending -->


		<!-- Load at the end to load the site faster -->
		
		<!-- Libaries (jQuery in head) -->
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.7.0/underscore-min.js"></script>	
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/backbone.js/1.1.2/backbone-min.js"></script>
		<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.0.1/js/bootstrap.min.js"></script>
		
		<!-- For formValidator-plugin -->
		<script type="text/javascript" src="/js/plugins/formValidator/bvalidator.jquery.js"></script>
		<script type="text/javascript" src="/js/plugins/formValidator/validator-views.js"></script>
		
		<!-- For the datePicker-plugin -->
	    <script type="text/javascript" src="/js/plugins/datePicker/datepicker.min.js"></script>
	    <script type="text/javascript" src="/js/plugins/datePicker/datePicker-views.js"></script>
		
		<!-- Comment-MVC's -->
		<script type="text/javascript" src="/js/models/commentModel.js"></script>
		<script type="text/javascript" src="/js/views/commentView.js"></script>
		<script type="text/javascript" src="/js/views/commentAddView.js"></script>
		<script type="text/javascript" src="/js/controllers/commentController.js"></script>
		<script type="text/javascript" src="/js/controllers/commentAddController.js"></script>
		
		<script type="text/javascript" src="/js/helpers.js"></script>

	</body>

</html>