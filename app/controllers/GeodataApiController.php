<?php
/* 
 * Copyright 2014/15 Matthias Mohr
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use \Carbon\Carbon;
use \GeoMetadata\GmRegistry;
use \GeoMetadata\GeoMetadata;

/**
 * This controller handles the internal API requests related to geodata/comment tasks, like adding and retrieving comments, parsing metadata, storing permalinks for searches etc.
 * Request is always a GET or POST request with JSON based parameters. Reponse is always JSON based, too.
 * 
 * @see https://github.com/m-mohr/my-meta-maps/wiki/Client-Server-Protocol
 */
class GeodataApiController extends BaseApiController {
	
	/**
	 * Builds a JSON response for a sinlge geo data set.
	 * 
	 * @param Geodata $geodata Geo dataset to use
	 * @param array $layers List of layers to add
	 * @return array
	 */
	protected function buildSingleGeodata(Geodata $geodata, $layers = array()) {
		return array('geodata' => $this->buildGeodataEntry($geodata, $layers));
	}
	
	/**
	 * Builds a JSON response with multiple geo data sets included.
	 * 
	 * @param array $list List of geo datasets.
	 * @return array
	 */
	protected function buildMultipleGeodata(array $list) {
		$data = array('geodata' => array());
		foreach ($list as $geodata) {
			$data['geodata'][] = $this->buildGeodataEntry($geodata, null);
		}
		return $data;
	}
	
	/**
	 * Builds the geodata response, which might contain additional data, depending on the request and the data given.
	 * 
	 * @param Geodata $geodata Geodata object to use for creation.
	 * @param array $layers Layers to add to the response.
	 * @return array
	 */
	protected function buildGeodataEntry(Geodata $geodata, $layers = null) {
		$data =  array(
			'id' => $geodata->id,
			'permalink' => $geodata->createPermalink(),
			'url' => $geodata->url,
			'metadata' => array(
				'datatype' => $geodata->datatype,
				'title' => $geodata->title,
				'bbox' => $geodata->bbox,
				'keywords' => $geodata->getKeywords(),
				'language' => $geodata->language,
				'copyright' => $geodata->copyright,
				'author' => $geodata->author,
				'time' => $this->buildTime($geodata),
				'abstract' => $geodata->abstract,
				'license' => $geodata->license
			)
		);
		if (isset($geodata->ratingAvg)) {
			$data['ratingAvg'] = $geodata->ratingAvg;
		}
		if (isset($geodata->commentCount)) {
			$data['commentCount'] = $geodata->commentCount;
		}
		if (isset($geodata->comments)) {
			if (is_array($geodata->comments)) { // Add comment entries
				$data['comments'] = $this->buildCommentList($geodata->comments);
			}
			else if (is_numeric($geodata->comments)) { // Add comment count
				$data['comments'] = $geodata->comments;
			}
		}
		if ($layers !== null) {
			$data['layer'] = array();
			foreach($layers as $layer) {
				$layerJson = array(
					'id' => $layer->name,
					'title' => $layer->title,
					'bbox' => $layer->bbox
				);
				if (isset($layer->comments)) {
					if (is_array($layer->comments)) { // Add comment entries
						$layerJson['comments'] = $this->buildCommentList($layer->comments);
					}
					else if (is_numeric($layer->comments)) { // Add comment count
						$layerJson['comments'] = $layer->comments;
					}
				}
				$data['layer'][] = $layerJson;
			}
		}
		
		return $data;
	}

	/**
	 * Builds the comments part of the JSON responses.
	 * 
	 * @param array $comments
	 * @return array
	 */
	protected function buildCommentList(array $comments) {
		$data = array();
		foreach($comments as $comment) {
			$entry = array(
				'id' => $comment->id,
				'permalink' => $comment->createPermalink(),
				'text' => $comment->text,
				'rating' => $comment->rating,
				'geometry' => $comment->geom,
				'time' => $this->buildTime($comment),
				'user' => null
			);
			if ($comment->user_id > 0) {
				$entry['user'] = array(
					'id' => $comment->user_id,
					'name' => $comment->user_name
				);
			}
			$data[] = $entry;
		}
		return $data;
	}
	
	/**
	 * Builds the time part of the JSON responses.
	 * 
	 * @param Eloquent $model
	 * @return array
	 */
	protected function buildTime(Eloquent $model) {
		return array(
			'start' => ($model->start !== null) ? $this->toDate($model->start) : null,
			'end' => ($model->end !== null) ? $this->toDate($model->end) : null
		);
	}
	
	/**
	 * Parses the metadata at the specified URL with the parser represented by the service code.
	 * 
	 * @param string $url URL to parse
	 * @param string $code Service code
	 * @param \GeoMetadata\Model\Metadata $model Model to store the metadata in
	 * @return \GeoMetadata\Model\Metadata New instance of the model containing the metadata
	 */
	protected function parseMetadata($url, $code, $model = null) {
		$geo = GeoMetadata::withUrl($url, $code);
		if ($geo != null) {
			$geo->setModel($model);
			return $geo->parse();
		}
		return null;
	}

	/**
	 * Handles a POST requests that requests to add (a geo data set including) a comment to the database.
	 * 
	 * @return Response
	 */
	public function postAdd() {
		$data = Input::only('url', 'datatype', 'layer', 'text', 'geometry', 'start', 'end', 'rating', 'title');
		
		$geodata = null;
		$service = GmRegistry::getService($data['datatype']);
		if ($service !== null) {
			$serviceUrl = $service->getServiceUrl($data['url']);
			$geodata = Geodata::where('url', '=', $serviceUrl)->first();
		}
		
		$validator = Validator::make($data,
			array(
				'url' => 'required|url',
				'datatype' => empty($geodata) ? 'required|' : '' . 'in:'.implode(',', GmRegistry::getServiceCodes()),
				'layer' => '',
				'title' => empty($geodata) ? 'required|' : '' . 'min:3|max:200',
				'text' => 'required|min:3|max:100000',
				'geometry' => 'geometry',
				'start' => 'date8601',
				'end' => 'date8601',
				'rating' => 'integer|between:1,5'
			)
		);
		
		if ($validator->fails()) {
			return $this->getConflictResponse($validator->messages());
		}
		
		if (empty($geodata)) {
			// Add geodata from remote service
			$geodata = $this->parseMetadata($data['url'], $data['datatype'], new GmGeodata());
			if ($geodata == null) {
				return $this->getConflictResponse();
			}
			// Add geodata from user (user may override the title for new URLs)
			$geodata->title = $data['title'];
			if (!$geodata->save()) {
				return $this->getConflictResponse();
			}
			else {
				// Parser added the layers, but hasn't stored them so far...
				$layers = $geodata->getLayers();
				if (is_array($layers)) {
					foreach($layers as $layer) {
						$layer->geodata()->associate($geodata);
						$layer->save();
					}
				}
			}
		}
		
		$foundLayer = null;
		if (!empty($data['layer'])) {
			foreach($geodata->layers as $layer) {
				if ($layer->name == $data['layer']) {
					$foundLayer = $layer;
					break;
				}
			}
		}
		
		$comment = new Comment();
		$comment->geodata()->associate($geodata);
		if (Auth::check()) {
			$comment->user()->associate(Auth::user());
		}
		if ($foundLayer !== null) {
			$comment->layer()->associate($foundLayer);
		}
		$comment->text = $data['text'];
		$comment->rating = empty($data['rating']) ? null : $data['rating'];
		$comment->start = empty($data['start']) ? null : new Carbon($data['start']);
		$comment->end = empty($data['end']) ? null : new Carbon($data['end']);
		$comment->geom = empty($data['geometry']) ? null : $data['geometry'];
		if (!$comment->save()) {
			return $this->getConflictResponse();
		}
		
		return $this->getJsonResponse($this->buildSingleGeodata($geodata, null));
	}
	
	/**
	 * Handles a POST request that parses the data from a given URL and returns the metadata for it.
	 * 
	 * @return Response
	 */
	public function postMetadata() {
		$data = Input::only('url', 'datatype');
		
		$validator = Validator::make($data,
			array(
				'url' => 'required|url',
				'datatype' => 'required|in:'.implode(',', GmRegistry::getServiceCodes())
			)
		);
		if ($validator->fails()) {
			return $this->getConflictResponse($validator->messages());
		}

		
		// Try to get existing metadata for the URL
		$serviceUrl = 	GmRegistry::getService($data['datatype'])->getServiceUrl($data['url']);
		$geodata = Geodata::where('url', '=', $serviceUrl)->first();
		if ($geodata != null) {
			$json = $this->buildSingleGeodata($geodata, $geodata->layers->all()); // Get layers from DB
			$json['geodata']['id'] = $geodata->id;
			$json['geodata']['isNew'] = false;
			return $this->getJsonResponse($json);
		}
		else {
			// No metadata found in DB, parse them from the URL
			$geodata = $this->parseMetadata($data['url'], $data['datatype'], new GmGeodata());
			if ($geodata != null) {
				$json = $this->buildSingleGeodata($geodata, $geodata->getLayers()); // Get layers from GeoMetadata
				$json['geodata']['id'] = 0;
				$json['geodata']['isNew'] = true;
				return $this->getJsonResponse($json);
			}
		}

		$error = Lang::get('validation.url', array('attribute' => 'URL'));
		return $this->getConflictResponse(array('url' => $error));
	}
	
	/**
	 * Handles a POST request that returns auto complete suggestions for the keyword based search.
	 * 
	 * Implementation is delayed. Method is not working yet and returns a 404 Not Found so far.
	 * 
	 * @return Response
	 */
	public function postKeywords() {
		$q = Input::get('q');
		$metadata = Input::get('metadata');
		if (strlen($q) >= 3) {
			// TODO: Implement auto suggestion for keywords.
		}
		return $this->getNotFoundResponse();
	}
	
	/**
	 * Handles a POST request thet stores the search settings and creates a permalink for it.
	 * 
	 * @return Response
	 */
	public function postSearchSave() {
		$data = $this->getFilterInput(array('bbox'));
		if (empty($data['bbox'])) {
			return $this->getConflictResponse();
		}
		$search = new SavedSearch();
		$search->id = SavedSearch::generateId();
		$search->keywords = $data['q'];
		$search->metadata = $data['metadata'];
		$search->rating = $data['minrating'];
		$search->start = $data['start'];
		$search->end = $data['end'];
		$search->bbox = $data['bbox'];
		$search->radius = $data['radius'];
		if ($search->save()) {
			return $this->getJsonResponse(array(
				'permalink' => Config::get('app.url') . '/geodata/search/' . $search->id
			));
		}
		else {
			return $this->getConflictResponse();
		}
	}
	
	/**
	 * Handles a POST request thet returns the search settings that were saved for the specified permalink.
	 * 
	 * @param string $id ID of the permalink
	 * @return Response
	 */
	public function getSearchLoad($id) {
		$search = SavedSearch::selectBbox()->find($id);
		if ($search !== null) {
			$json = array(
				'permalink' => array(
					'q' => $search->keywords,
					'metadata' => $search->metadata,
					'bbox' => $search->bbox,
					'radius' => $search->radius,
					'time' => $this->buildTime($search),
					'minrating' => $search->rating
				)
			);
			return $this->getJsonResponse($json);
		}
		else {
			return $this->getNotFoundResponse();
		}
	}
	
	/**
	 * Handles a POST request that returns a list of geodata sets that suits the filter.
	 * 
	 * @return Response
	 */
	public function postList() {
		$filter = $this->getFilterInput();
		$geodata = Geodata::filter($filter)->get();
		return $this->getJsonResponse($this->buildMultipleGeodata($geodata->all()));
	}
	
	/**
	 * Handles a POST request that returns the (filtered) comments.
	 * 
	 * @param int $id ID of the geo dataset,
	 * @return Response
	 */
	public function postComments($id) {
		$filter = $this->getFilterInput();
		// Get geodata for the specified id
		$geodata = Geodata::find($id);
		// Get all suitable comments for the filter and group them by layer (no layer = 0)
		$comments = Comment::filter($filter, $id)->get();
		$groupedComments = array(0 => array()); // Make sure the geodata entry is always a valid array
		$ratingSumFiltered = 0;
		$ratingCountFiltered = 0;
		foreach($comments as $comment) {
			if ($comment->rating > 0) {
				$ratingSumFiltered += $comment->rating;
				$ratingCountFiltered++;
			}
			if (empty($comment->layer_id)) {
				$groupedComments[0][] = $comment;
			}
			else {
				$groupedComments[$comment->layer_id][] = $comment;
			}
		}
		$count = count($comments);
		// Compute the count and averages
		$geodata->ratingAvg = array(
			'all' => round(Comment::where('geodata_id', $id)->avg('rating'), 1),
			'filtered' => round(($ratingCountFiltered > 0 ? $ratingSumFiltered / $ratingCountFiltered : 0), 1)
		);
		$geodata->commentCount = array(
			'all' => Comment::where('geodata_id', $id)->count(),
			'filtered' => $count
		);
		// Get all layers
		$layers = $geodata->layers()->orderBy('title')->orderBy('id')->get();
		// Append the comment data to the layers and the geodata
		$geodata->comments = $groupedComments[0];
		foreach($layers as $layer) {
			if (isset($groupedComments[$layer->id])) {
				$layer->comments = $groupedComments[$layer->id];
			}
			else {
				$layer->comments = array();
			}
		}
		// Build the response array from the collected data
		$json = $this->buildSingleGeodata($geodata, $layers);
		return $this->getJsonResponse($json);
	}
	
	/**
	 * Reads the request data from the query and validates the data. Returns the valid data.
	 * 
	 * @param array $required Fields that should be marked as required for validation.
	 * @return array Validated data
	 */
	protected function getFilterInput(array $required = array()) {
		$input = Input::only('q', 'bbox', 'radius', 'start', 'end', 'minrating', 'metadata', 'comment');
		$rules = array(
			'q' => '',
			'bbox' => 'geometry:wkt,Polygon',
			'radius' => 'integer|between:1,500',
			'start' => 'date8601',
			'end' => 'date8601',
			'minrating' => 'integer|between:1,5',
			'metadata' => 'boolean',
			'comment' => 'integer',
		);
		foreach ($required as $field) {
			$rules[$field] = empty($rules[$field]) ? 'required' : 'required|' . $rules[$field];
		}
		$validator = Validator::make($input, $rules);
		$data = $validator->valid();
		// Set default values when not existant
		$data['q'] = !empty($data['q']) ? $data['q'] : '';
		$data['bbox'] = !empty($data['bbox']) ? $data['bbox'] : null;
		$data['radius'] = !empty($data['radius']) ? $data['radius'] : null;
		$data['minrating'] = !empty($data['minrating']) ? $data['minrating'] : null;
		$data['start'] = !empty($data['start']) ? new Carbon($data['start']) : null;
		$data['end'] = !empty($data['end']) ? new Carbon($data['end']) : null;
		$data['metadata'] = ($data['metadata'] !== null) ? $data['metadata'] : false;
		$data['comment'] = !empty($data['comment']) ? $data['comment'] : 0;
		return $data;
	}
	
}
