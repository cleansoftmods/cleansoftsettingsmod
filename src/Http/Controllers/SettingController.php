<?php namespace WebEd\Base\Settings\Http\Controllers;

use WebEd\Base\Http\Controllers\BaseAdminController;

use WebEd\Base\Settings\Repositories\Contracts\SettingContract;

class SettingController extends BaseAdminController
{
    protected $module = 'webed-settings';

    /**
     * @var \WebEd\Base\Settings\Repositories\SettingRepository
     */
    protected $repository;

    public function __construct(SettingContract $settingRepository)
    {
        parent::__construct();

        $this->repository = $settingRepository;

        $this->breadcrumbs->addLink('Settings', route('admin::settings.index.get'));

        $this->getDashboardMenu($this->module);

        $this->assets
            ->addStylesheets('bootstrap-tagsinput')
            ->addJavascripts('bootstrap-tagsinput');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $this->setPageTitle('Settings');

        return do_filter('settings.index.get', $this)->viewAdmin('index');
    }

    /**
     * Update settings
     * @method POST
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $data = $this->request->except([
            '_token',
            '_tab',
        ]);

        /**
         * Filter
         */
        $data = do_filter('settings.before-edit.post', $data, $this);

        $result = $this->repository->updateSettings($data);

        $msgType = $result['error'] ? 'danger' : 'success';

        flash_messages()
            ->addMessages($result['messages'], $msgType)
            ->showMessagesOnSession();

        do_action('settings.after-edit.post', $result, $this);

        return redirect()->back();
    }
}
