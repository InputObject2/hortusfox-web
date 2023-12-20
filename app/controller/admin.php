<?php

/**
 * Admin controller
 */
class AdminController extends BaseController {
    /**
	 * Perform base initialization
	 * 
	 * @return void
	 */
    public function __construct()
    {
        parent::__construct();

        if (!UserModel::isCurrentlyAdmin()) {
            header('Location: /');
            exit();
        }
    }

    /**
	 * Handles URL: /admin
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\ViewHandler
	 */
	public function index($request)
	{
		$user = UserModel::getAuthUser();
		$locs = LocationsModel::getAll(false);
        $user_accounts = UserModel::getAll();

		$new_version = null;
		$current_version = null;

		$check_version = $request->params()->query('cv', false);

		if ($check_version) {
			$new_version = VersionModule::getVersion();
			$current_version = safe_config('version');
		}

		return parent::view(['content', 'admin'], [
			'user' => $user,
			'locations' => $locs,
            'user_accounts' => $user_accounts,
			'new_version' => $new_version,
			'current_version' => $current_version
		]);
	}

	/**
	 * Handles URL: /admin/environment/save
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\RedirectHandler
	 */
	public function save_environment($request)
	{
		try {
			$workspace = $request->params()->query('workspace', env('APP_WORKSPACE'));
			$lang = $request->params()->query('lang', env('APP_LANG'));
			$scroller = (bool)$request->params()->query('scroller', 0);
			$enablechat = (bool)$request->params()->query('enablechat', 0);
			$onlinetimelimit = (int)$request->params()->query('onlinetimelimit', env('APP_ONLINEMINUTELIMIT'));
			$chatonlineusers = (bool)$request->params()->query('chatonlineusers', 0);
			$chattypingindicator = (bool)$request->params()->query('chattypingindicator', 0);
			$cronpw = $request->params()->query('cronpw', env('APP_CRONPW'));
			
			UtilsModule::saveEnvironment($workspace, $lang, $scroller, $enablechat, $onlinetimelimit, $chatonlineusers, $chattypingindicator, $cronpw);

			FlashMessage::setMsg('success', __('app.environment_settings_saved'));

			return redirect('/admin');
		} catch (\Exception $e) {
			FlashMessage::setMsg('error', $e->getMessage());
			return back();
		}
	}

	/**
	 * Handles URL: /admin/user/create
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\RedirectHandler
	 */
	public function create_user($request)
	{
		try {
			$name = $request->params()->query('name', null);
			$email = $request->params()->query('email', null);
			
			UserModel::createUser($name, $email);

			FlashMessage::setMsg('success', __('app.user_created_successfully'));

			return redirect('/admin');
		} catch (\Exception $e) {
			FlashMessage::setMsg('error', $e->getMessage());
			return back();
		}
	}

	/**
	 * Handles URL: /admin/user/update
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\RedirectHandler
	 */
	public function update_user($request)
	{
		try {
			$id = $request->params()->query('id');
			$name = $request->params()->query('name', null);
			$email = $request->params()->query('email', null);
			$admin = $request->params()->query('admin', 0);
			
			UserModel::updateUser($id, $name, $email, (int)$admin);

			FlashMessage::setMsg('success', __('app.user_updated_successfully'));

			return redirect('/admin');
		} catch (\Exception $e) {
			FlashMessage::setMsg('error', $e->getMessage());
			return back();
		}
	}

	/**
	 * Handles URL: /admin/user/remove
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\RedirectHandler
	 */
	public function remove_user($request)
	{
		try {
			$id = $request->params()->query('id');
			
			UserModel::removeUser($id);

			FlashMessage::setMsg('success', __('app.user_removed_successfully'));

			return redirect('/admin');
		} catch (\Exception $e) {
			FlashMessage::setMsg('error', $e->getMessage());
			return back();
		}
	}

	/**
	 * Handles URL: /admin/location/add
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\RedirectHandler
	 */
	public function add_location($request)
	{
		try {
			$name = $request->params()->query('name', null);
			$icon = $request->params()->query('icon', null);
			
			LocationsModel::addLocation($name, $icon);

			FlashMessage::setMsg('success', __('app.location_added_successfully'));

			return redirect('/admin');
		} catch (\Exception $e) {
			FlashMessage::setMsg('error', $e->getMessage());
			return back();
		}
	}

	/**
	 * Handles URL: /admin/location/update
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\RedirectHandler
	 */
	public function update_location($request)
	{
		try {
			$id = $request->params()->query('id');
			$name = $request->params()->query('name', null);
			$icon = $request->params()->query('icon', null);
			$active = $request->params()->query('active', 0);
			
			LocationsModel::editLocation($id, $name, $icon, (int)$active);

			FlashMessage::setMsg('success', __('app.location_updated_successfully'));

			return redirect('/admin');
		} catch (\Exception $e) {
			FlashMessage::setMsg('error', $e->getMessage());
			return back();
		}
	}

	/**
	 * Handles URL: /admin/location/remove
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\RedirectHandler
	 */
	public function remove_location($request)
	{
		try {
			$id = $request->params()->query('id');
			$target = $request->params()->query('target');
			
			LocationsModel::removeLocation($id, $target);

			FlashMessage::setMsg('success', __('app.location_removed_successfully'));

			return redirect('/admin');
		} catch (\Exception $e) {
			FlashMessage::setMsg('error', $e->getMessage());
			return back();
		}
	}
}
