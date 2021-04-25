<?php
class ControllerExtensionModuleOCNElfinder extends Controller {
    private $error = [];
    private $user_token;

    public function __construct($registry) {
        parent::__construct($registry);

        $this->user_token = 'user_token=' . $this->session->data['user_token'];
    }

    public function index() {
        $this->load->language('extension/module/ocn_elfinder');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_ocn_elfinder', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            if (isset($this->request->post['apply'])) {
                $this->response->redirect($this->url->link('extension/module/ocn_elfinder', $this->user_token, true));
            }

            $this->response->redirect($this->url->link('marketplace/extension', $this->user_token . '&type=module', true));
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $data['error_warning'] = isset($this->error['warning'])
            ? $this->error['warning']
            : '';

        $data['breadcrumbs'] = [
            [
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', $this->user_token, true),
            ],
            [
                'text' => $this->language->get('text_extension'),
                'href' => $this->url->link('marketplace/extension', $this->user_token . '&type=module', true),
            ],
            [
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/ocn_elfinder', $this->user_token, true),
            ],
        ];

        $data['url_action'] = $this->url->link('extension/module/ocn_elfinder', $this->user_token, true);
        $data['url_cancel'] = $this->url->link('marketplace/extension', $this->user_token . '&type=module', true);
        $data['url_connector'] = $this->url->link('extension/module/ocn_elfinder/connector', $this->user_token);

        $data['module_ocn_elfinder_status'] = isset($this->request->post['module_ocn_elfinder_status'])
            ? $this->request->post['module_ocn_elfinder_status']
            : $this->config->get('module_ocn_elfinder_status');
        $data['module_ocn_elfinder_product_status'] = isset($this->request->post['module_ocn_elfinder_product_status'])
            ? $this->request->post['module_ocn_elfinder_product_status']
            : $this->config->get('module_ocn_elfinder_product_status');
        $data['module_ocn_elfinder_category_status'] = isset($this->request->post['module_ocn_elfinder_category_status'])
            ? $this->request->post['module_ocn_elfinder_category_status']
            : $this->config->get('module_ocn_elfinder_category_status');
        $data['module_ocn_elfinder_information_status'] = isset($this->request->post['module_ocn_elfinder_information_status'])
            ? $this->request->post['module_ocn_elfinder_information_status']
            : $this->config->get('module_ocn_elfinder_information_status');
        $data['module_ocn_elfinder_html_status'] = isset($this->request->post['module_ocn_elfinder_html_status'])
            ? $this->request->post['module_ocn_elfinder_html_status']
            : $this->config->get('module_ocn_elfinder_html_status');
        $data['module_ocn_elfinder_marketing_status'] = isset($this->request->post['module_ocn_elfinder_marketing_status'])
            ? $this->request->post['module_ocn_elfinder_marketing_status']
            : $this->config->get('module_ocn_elfinder_marketing_status');


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/ocn_elfinder', $data));
    }

    public function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/ocn_elfinder')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function manager()
    {
        $this->load->language('extension/module/ocn_elfinder');
        $this->document->setTitle($this->language->get('heading_title'));

        // Find which protocol to use to pass the full image link back
        if ($this->request->server['HTTPS']) {
            $server = HTTPS_CATALOG;
        } else {
            $server = HTTP_CATALOG;
        }

        if (isset($this->request->get['target'])) {
            $data['target'] = $this->request->get['target'];
        } else {
            $data['target'] = '';
        }
        if (isset($this->request->get['thumb'])) {
            $data['thumb'] = $this->request->get['thumb'];
        } else {
            $data['thumb'] = '';
        }
        if (isset($this->request->get['textarea'])) {
            $data['textarea'] = $this->request->get['textarea'];
        } else {
            $data['textarea'] = '';
        }

        $data['url_connector'] = $this->url->link('extension/module/ocn_elfinder/connector', $this->user_token);
        $data['base_url'] = 'view/javascript/summernote/elfinder/';

        $this->response->setOutput($this->load->view('extension/module/ocn_elfinder_manager', $data));
    }

    /**
     * Simple function to demonstrate how to control file access using "accessControl" callback.
     * This method will disable accessing files/folders starting from '.' (dot)
     *
     * @param  string    $attr    attribute name (read|write|locked|hidden)
     * @param  string    $path    absolute file path
     * @param  string    $data    value of volume option `accessControlData`
     * @param  object    $volume  elFinder volume driver object
     * @param  bool|null $isDir   path is directory (true: directory, false: file, null: unknown)
     * @param  string    $relpath file path relative to volume root directory started with directory separator
     * @return bool|null
     **/
    function access($attr, $path, $data, $volume, $isDir, $relpath) {
        $basename = basename($path);
        return $basename[0] === '.'                  // if file/folder begins with '.' (dot)
        && strlen($relpath) !== 1           // but with out volume root
            ? !($attr == 'read' || $attr == 'write') // set read+write to false, other (locked+hidden) set to true
            :  null;                                 // else elFinder decide it itself
    }

    /**
     * Simple debug function
     * Usage: debug($anyVal[, $anyVal2 ...]);
     */
    function debug() {
        $arg = func_get_args();
        ob_start();
        foreach($arg as $v) {
            var_dump($v);
        }
        $o = ob_get_contents();
        ob_end_clean();
        file_put_contents('.debug.txt', $o, FILE_APPEND);
    }

    public function connector()
    {
        // // load composer autoload before load elFinder autoload If you need composer
        // // You need to run the composer command in the php directory.
        is_readable(DIR_STORAGE . 'vendor/studio-42/elfinder/php/autoload.php') && require DIR_STORAGE . 'vendor/studio-42/elfinder/php/autoload.php';

        // // elFinder autoload
//        require DIR_STORAGE . 'vendor/studio-42/elfinder/php/autoload.php';
        // ===============================================

        // // Enable FTP connector netmount
        elFinder::$netDrivers['ftp'] = 'FTP';
        // ===============================================

        // Documentation for connector options:
        // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options
        $opts = [
            // 'debug' => true,
            'roots' => [
                // Items volume
                [
                    'driver'        => 'LocalFileSystem', // driver for accessing file system (REQUIRED)
                    'path'          => DIR_IMAGE, // path to files (REQUIRED)
                    'URL'           => HTTPS_CATALOG . 'image/', // URL to files (REQUIRED)
                    'tmbURL'        => HTTPS_CATALOG . 'image/cache/elfinder/tmb/',
                    'tmbPath'       => DIR_IMAGE . 'cache/elfinder/tmb/',
                    'quarantine'    => DIR_IMAGE . 'cache/elfinder/quarantine/',
                    'copyJoin'      => false,
                    'trashHash'     => 't1_Lw', // elFinder's hash of trash folder
                    'winHashFix'    => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
                    'uploadDeny'    => ['all'], // All Mimetypes not allowed to upload
                    'uploadAllow'   => [
                        'image/x-ms-bmp',
                        'image/gif',
                        'image/jpeg',
                        'image/png',
                        'image/x-icon',
                        'text/plain'
                    ], // Mimetype `image` and `text/plain` allowed to upload
                    'uploadOrder'   => ['deny', 'allow'], // allowed Mimetype `image` and `text/plain` only
                    'accessControl' => 'access', // disable and hide dot starting files (OPTIONAL)
                    'tmbGcMaxlifeHour' => 1,  // 1 hour
                    'tmbGcPercentage'  => 10, // 10 execute / 100 tmb querys
                ],
                // Trash volume
                [
                    'id'            => 't1',
                    'driver'        => 'Trash',
                    'path'          => DIR_IMAGE . 'cache/elfinder/.trash/',
                    'URL'           => HTTPS_CATALOG . 'image/cache/elfinder/.trash/',
                    'tmbPath'       => DIR_IMAGE . 'cache/elfinder/.trash/tmb/',
                    'tmbURL'        => HTTPS_CATALOG . 'image/cache/elfinder/.trash/tmb/',
                    'winHashFix'    => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
                    'uploadDeny'    => ['all'], // Recomend the same settings as the original volume that uses the trash
                    'uploadAllow'   => [
                        'image/x-ms-bmp',
                        'image/gif',
                        'image/jpeg',
                        'image/png',
                        'image/x-icon',
                        'text/plain'
                    ], // Same as above
                    'uploadOrder'   => ['deny', 'allow'], // Same as above
                    'accessControl' => null, // Same as above
                ],
            ],
            // some bind functions
//            'bind' => [
//                // enable logger
//                // '*' => [logger, 'log'),
//                'mkdir mkfile rename duplicate upload rm paste' => [logger, 'log'],
//                // enable plugins
//                'archive.pre ls.pre mkdir.pre mkfile.pre rename.pre upload.pre' => [
//                    'Plugin.Normalizer.cmdPreprocess',
//                    'Plugin.Sanitizer.cmdPreprocess'
//                ],
//                'upload.presave' => [
//                    'Plugin.AutoRotate.onUpLoadPreSave',
//                    'Plugin.AutoResize.onUpLoadPreSave',
//                    'Plugin.Watermark.onUpLoadPreSave',
//                    'Plugin.Normalizer.onUpLoadPreSave',
//                    'Plugin.Sanitizer.onUpLoadPreSave',
//                ],
//            ],
        ];

        // run elFinder
        $connector = new elFinderConnector(new elFinder($opts));
        $connector->run();
    }
}
