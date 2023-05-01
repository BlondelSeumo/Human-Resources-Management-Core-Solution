<?php


namespace Gainhq\Installer\App\Managers;

use Gainhq\Installer\App\Managers\Helper\RequestSender;
use Gainhq\Installer\App\Managers\Helper\UrlHelper;
use Gainhq\Installer\App\Managers\Validator\PurchaseCodeValidator;

class UpdateManager
{
    use UrlHelper;

    protected $purchaseCodeManager;
    protected $purchaseCodeValidator;
    protected $request;
    protected $downloadManager;

    public function __construct(
        PurchaseCodeManager $purchaseCodeManager,
        PurchaseCodeValidator $purchaseCodeValidator,
        RequestSender $request,
        DownloadManager $downloadManager
    )
    {
        $this->purchaseCodeManager = $purchaseCodeManager;
        $this->purchaseCodeValidator = $purchaseCodeValidator;
        $this->request = $request;
        $this->downloadManager = $downloadManager;
    }

    public function updates()
    {
        if ($code = $this->purchaseCodeManager->getCode()) {
            $url = $this->url($code, 'update/list');
            $result = $this->request->get($url)->data;
            $this->downloadManager->download($result, $code);
            return (object)['status' => true, 'result' => $result];
        }
        return (object)['status' => false, 'message' => trans('default.invalid_purchase_code')];
    }
}
