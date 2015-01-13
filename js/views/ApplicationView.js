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
<<<<<<< HEAD
        polySource: new ol.source.Vector(),
        vectorlayer: null,
        parser: new ol.format.WKT(),
        
=======
	polySource: new ol.source.Vector(),
	vectorlayer: null,
>>>>>>> 8e850bf738094c478eae7e74e61ea5d54f558cc6
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
		geodataShowController(new GeodataShow(), this);
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
	getBoundingBox: function () {
		// TODO: Return the current bounding box of the map
<<<<<<< HEAD
                console.log(this.map.getView().calculateExtent(this.map.getSize()));
                var mapbbox = this.map.getView().calculateExtent(this.map.getSize());
                mapbbox = this.parser.writeFeature(mapbbox);
                return mapbbox;
=======
		var ViewPort = this.map.getViewport();

		return null;
>>>>>>> 8e850bf738094c478eae7e74e61ea5d54f558cc6
	},
	/*
	 * add the bboxes from the Geodata to the map
	 */
	addGeodataToMap: function (data) {
<<<<<<< HEAD
                var polygeom;
                
                
                // gets each bbox(wkt format), transforms it into a geometry and adds it to the vector source 
                for(var index = 0; index < data.geodata.length; index++) {
                    polygeom = this.parser.readGeometry(data.geodata[index].metadata.bbox, 'EPSG: 4326');
                    polygeom.transform('EPSG:4326', 'EPSG:3857');
                    this.polySource.addFeature(new ol.Feature({
                        geometry: new ol.geom.Polygon(polygeom.getCoordinates()),
                        projection: 'EPSG: 3857'
                    }));
                }
                     
=======
		var parser = new ol.format.WKT();
		var polygeom;


		// gets each bbox(wkt format), transforms it into a geometry and adds it to the vector source 
		for (var index = 0; index < data.geodata.length; index++) {
			polygeom = parser.readGeometry(data.geodata[index].metadata.bbox, 'EPSG: 4326');
			polygeom.transform('EPSG:4326', 'EPSG:3857');
			this.polySource.addFeature(new ol.Feature({
				geometry: new ol.geom.Polygon(polygeom.getCoordinates()),
				projection: 'EPSG: 3857'
			}));
		}

>>>>>>> 8e850bf738094c478eae7e74e61ea5d54f558cc6
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