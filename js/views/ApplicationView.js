ContentView = Backbone.View.extend({
	el: $('#content'),
	constructor: function (options) {
		this.configure(options || {});
		Backbone.View.prototype.constructor.apply(this, arguments);
	},
	configure: function (options) {
		if (this.options) {
			options = _.extend({}, _.result(this, 'options'), options);
		}
		this.options = options;
	},
	initialize: function () {
		this.render();
	},
	onLoaded: function () {
	},
	render: function () {
		var that = this;
		$.get(this.getPageTemplate(), function (data) {
			template = _.template(data);
			var vars = {
				data: that.getPageContent(),
				config: config,
				ViewUtils: ViewUtils
			};
			that.$el.html(template(vars));
			that.onLoaded();
		}, 'html');
	},
	getPageTemplate: function () {
		Debug.log('Error: Called abstract method!');
		return null;
	},
	close: function () {
		this.$el.html(''); // Remove content from page
		// Remove callbacks, events, listeners etc.
		this.stopListening();
		this.undelegateEvents();
		this.unbind();
		this.off();
//		this.remove(); // Remove view from DOM
//		Backbone.View.prototype.remove.call(this);
	},
	getPageContent: function () {
		return {};
	}
});
// Statics
ContentView.active = null;
ContentView.register = function (view) {
	if (ContentView.active !== null) {
		ContentView.active.close();
		ContentView.active = null;
	}
	ContentView.active = view;
};

ModalView = ContentView.extend({
	el: $('#modal'),
	// Called after modal is loaded (and not opened yet)
	onLoaded: function () {
		this.modal();
		this.showProgress();
		this.onOpened();
	},
	// Called after modal is opened
	onOpened: function () {

	},
	modal: function () {
		$('#modal').find('.modal').modal('show');
	},
	showProgress: function () {
		Progress.show('.modal-progress');
	}
});

MapView = ContentView.extend({
	map: null,
	polySource: new ol.source.Vector(),
	vectorlayer: null,
	parser: new ol.format.WKT(),
	onLoaded: function () {
		var view = new ol.View({
			center: [0, 0],
			zoom: 2
		});

		// set the style of the vector geometries
		var polyStyle = new ol.style.Style({
			fill: new ol.style.Fill({
				color: 'rgba(0,139,0,0.1)'
			}),
			stroke: new ol.style.Stroke({
				color: 'rgba(0,139,0,1)',
				width: 2
			})
		});

		this.map = new ol.Map({
			layers: [new ol.layer.Tile({
					source: new ol.source.OSM()
				}),
				new ol.layer.Vector({
					source: this.polySource,
					style: polyStyle
				})
			],
			target: 'map',
			controls: ol.control.defaults({
				attributionOptions: /** @type {olx.control.AttributionOptions} */({
					collapsible: false
				})
			}),
			view: view
		});

		// gets the geolocation
		var geolocation = new ol.Geolocation({
			projection: view.getProjection(),
			tracking: true
		});
		// zooms the map to the users location
		geolocation.once('change:position', function () {
			view.setCenter(geolocation.getPosition());
			view.setZoom(5);
		});

		$('#spatialFilter').barrating({
			showValues: true,
			showSelectedRating: false,
			onSelect: executeSearch,
			onClear: executeSearch
		});
		$('#ratingFilter').barrating({
			showSelectedRating: false,
			onSelect: executeSearch,
			onClear: executeSearch
		});

		this.doSearch();
	},
	doSearch: function () {
		this.polySource.clear();
		geodataShowController();
	},
	resetSearch: function (form) {
		form.reset();
		// Remove visible feedback of barrating.
		$('#spatialFilter').barrating('clear');
		$('#ratingFilter').barrating('clear');
		this.doSearch();
	},
	getServerCrs: function () {
		return 'EPSG:4326';
	},
	getMapCrs: function () {
		return 'EPSG:3857';
	},
	/*
	 * calculates the current bounding box of the map and returns it as an WKt String
	 */
	getBoundingBox: function () {
		var mapbbox = this.map.getView().calculateExtent(this.map.getSize());
		mapbbox = ol.extent.applyTransform(mapbbox, ol.proj.getTransform(this.getMapCrs(), this.getServerCrs()));
		var geom = new ol.geom.Polygon([[new ol.extent.getBottomLeft(mapbbox), new ol.extent.getBottomRight(mapbbox), new ol.extent.getTopRight(mapbbox), new ol.extent.getTopLeft(mapbbox), new ol.extent.getBottomLeft(mapbbox)]]);
		return this.parser.writeGeometry(geom);
	},
	/*
	 * add the bboxes from the Geodata to the map
	 */
	addGeodataToMap: function (data) {
		var polygeom;
		// gets each bbox(wkt format), transforms it into a geometry and adds it to the vector source 
		for (var index = 0; index < data.geodata.length; index++) {
			polygeom = this.parser.readGeometry(data.geodata[index].metadata.bbox, this.getServerCrs());
			polygeom.transform(this.getServerCrs(), this.getMapCrs());
			this.polySource.addFeature(new ol.Feature({
				geometry: new ol.geom.Polygon(polygeom.getCoordinates()),
				projection: this.getMapCrs()
			}));
		}
	},
	getPageTemplate: function () {
		return '/api/internal/doc/map';
	}

});

AboutView = ContentView.extend({
	getPageTemplate: function () {
		return 'api/internal/doc/about';
	}
});

HelpView = ContentView.extend({
	getPageTemplate: function () {
		return 'api/internal/doc/help';
	}
});