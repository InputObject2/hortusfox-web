<?php

/*
    Asatru PHP - Example controller

    Add here all your needed routes implementations related to 'index'.
*/

/**
 * Example index controller
 */
class IndexController extends BaseController {
	const INDEX_LAYOUT = 'layout';

	/**
	 * Perform base initialization
	 * 
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct(self::INDEX_LAYOUT);
	}

	/**
	 * Handles URL: /
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\ViewHandler
	 */
	public function index($request)
	{
		$locs = LocationsModel::getAll();
		
		return parent::view(['content', 'index'], [
			'locations' => $locs
		]);
	}

	/**
	 * Handles URL: /plants/location/{id}
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\ViewHandler
	 */
	public function plants_from_location($request)
	{
		$location = $request->arg('id');

		$plants = PlantsModel::getAll($location);
		
		return parent::view(['content', 'plants'], [
			'plants' => $plants,
			'location' => $location
		]);
	}

	/**
	 * Handles URL: /plants/details/{id}
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\ViewHandler
	 */
	public function view_plant_details($request)
	{
		$plant_id = $request->arg('id');

		$plant_data = PlantsModel::getDetails($plant_id);
		
		return parent::view(['content', 'details'], [
			'plant' => $plant_data
		]);
	}

	/**
	 * Handles URL: /plants/add
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\RedirectHandler
	 */
	public function add_plant($request)
	{
		$validator = new Asatru\Controller\PostValidator([
			'name' => 'required',
			'location' => 'required',
			'perennial' => 'required',
			'cutting_month' => 'required',
			'date_of_purchase' => 'required',
			'humidity' => 'required',
			'light_level' => 'required'
		]);

		if (!$validator->isValid()) {
			FlashMessage::setMsg('error', 'Invalid data given');
			return back();
		}

		$name = $request->params()->query('name', null);
		$location = $request->params()->query('location', null);
		$perennial = $request->params()->query('perennial', false);
		$cutting_month = $request->params()->query('cutting_month', null);
		$date_of_purchase = $request->params()->query('date_of_purchase', null);
		$humidity = $request->params()->query('humidity', 0);
		$light_level = $request->params()->query('light_level', '');

		$plant_id = PlantsModel::addPlant($name, $location, $perennial, $cutting_month, $date_of_purchase, $humidity, $light_level);

		return redirect('/plants/details/' . $plant_id);
	}
}
