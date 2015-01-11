<h1>Kommentar erstellen</h1>
											
<form id="form-comment-secondStep" class="column col-md-5" onsubmit="return false">
	<h2 class="row">Daten eingeben</h2>

	<div class="row form-group">
		<label for="url">URL*</label>
		<input class="form-control" name="url" id="inputURL" type="text" readonly="readonly" value="<%= _.escape(data.url) %>">
		<div class="error-message"></div>
	</div>

	<div class="row form-group">
		<label for="datatype">Datenformat*</label>
		<input class="form-control" name="datatype" id="inputDataType" type="text" readonly="readonly" value="<%= _.escape(data.metadata.datatype) %>"> <!-- //TODO: Output proper service name -->
		<div class="error-message"></div>
	</div>

	<% if (!_.isEmpty(data.layer)) { %>
	<div class="row form-group">
		<label for="title">Layer</label>
		<select class="form-control" name="layer" id="inputLayer">
			<option value="">Kommentar keinem Layer zuordnen</option>
			<% _.each(data.layer, function(l) { %>
			<option value="<%= _.escape(l.id) %>"><%= _.escape(l.id) %>: <%= _.escape(l.title) %></option>
			<% }); %>
		</select>
		<div class="error-message"></div>
	</div>
	<% } %>

	<% if (data.isNew) { %>
	<div class="row form-group">
		<label for="title">Titel*</label>
		<input class="form-control" name="title" id="inputTitle" type="text" value="<%= _.escape(data.metadata.title) %>">
		<div class="error-message"></div>
	</div>
	<% } %>

	<div class="row form-group">
		<label for="text">Freitext*</label>
		<textarea class="form-control" rows="3" name="text" id="inputText"></textarea>
		<div class="error-message"></div>
	</div>

	<div class="row form-group">
		<label for="startDate">Zeitraum</label>
			<div class="input-group">
				<span id="show-datepicker-startComment" class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
				<input class="form-control" name="startDate" id="inputStartDate" type="text"placeholder="Startzeitpunkt" datepicker data-trigger="#show-datepicker-startComment">
			</div>
			<span class="error-message"></span>
	</div>

	<div class="row form-group">
		<div class="input-group">
			<span id="show-datepicker-endComment" class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
			<input class="form-control" name="endDate" id="inputEndDate" type="text"placeholder="Endzeitpunkt" datepicker data-trigger="#show-datepicker-endComment">
		</div>
		<div class="error-message"></div>
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
		<div class="error-message"></div>
	</div>

	<div class="row form-group text-right">
		<button type="submit" class="btn btn-primary" id="addCommentSecondBtn">Erstellen</button>
	</div>

</form>
<div id="form-comment-metadata" class="column col-md-7">
	<h2 class="row">Zusätzliche Metadaten</h2>

	<dl class="dl-horizontal metadata-list">
	<% if (!data.isNew) { %>
		<dt>Titel</dt>
		<dd><%= _.escape(data.metadata.title) %></dd>
	<% } %>
		<dt>Karte</dt>
		<dd><%= _.escape(data.metadata.bbox) %></dd>
	<% if (!_.isEmpty(data.metadata.language)) { %>
		<dt>Sprache</dt>
		<dd><%= _.escape(data.metadata.language) %></dd>
	<% } if (!_.isEmpty(data.metadata.abstract)) { %>
		<dt>Beschreibung</dt>
		<dd><pre><%= _.escape(data.metadata.abstract) %></pre></dd>
	<% } if (!_.isEmpty(data.metadata.keywords)) { %>
		<dt>Tags</dt>
		<dd>
			<% _.each(data.metadata.keywords, function(word) { %>
			<span class="label label-default"><%= _.escape(word) %></span>
			<% }); %>
		</dd>
	<% } if (!_.isEmpty(data.metadata.beginTime) || !_.isEmpty(data.metadata.endTime)) { %>
		<dt>Zeitraum</dt>
		<dd>
			Anfangsdatum: <%= data.metadata.beginTime ? _.escape(data.metadata.beginTime) : 'Unbekannt' %><br />
			Enddatum: <%= data.metadata.endTime ? _.escape(data.metadata.endTime) : 'Unbekannt' %>
		</dd>
	<% } if (!_.isEmpty(data.metadata.author)) { %>
		<dt>Autor</dt>
		<dd><pre><%= _.escape(data.metadata.author) %></pre></dd>
	<% } if (!_.isEmpty(data.metadata.copyright)) { %>
		<dt>Copyright</dt>
		<dd><pre><%= _.escape(data.metadata.copyright) %></pre></dd>
	<% } if (!_.isEmpty(data.metadata.license)) { %>
		<dt>Lizenz</dt>
		<dd><pre><%= _.escape(data.metadata.license) %></pre></dd>
	<% } %>
	</dl>

</div>
			
 <!-- For barRating-plugin; loaded in header, otherwise it doesnt works -->
 <script type="text/javascript" src="/js/plugins/barRating/jquery.barrating.min.js"></script>
		
<!-- For the datePicker-plugin -->
<script type="text/javascript" src="/js/plugins/datePicker/datepicker.min.js"></script>
<script type="text/javascript" src="/js/plugins/datePicker/datePicker-views.js"></script>