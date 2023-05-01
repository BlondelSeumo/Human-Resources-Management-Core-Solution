<?php


namespace Gainhq\Installer\App\Controllers\Setup;

use Gainhq\Installer\App\Controllers\Controller;
use Gainhq\Installer\App\Services\PurchaseCodeService;
use Illuminate\Http\Request;

class PurchaseCodeController extends Controller
{
    public function __construct(PurchaseCodeService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('installer::purchase_code');
    }

    public function purchaseCodeStore(Request $request)
    {
        $request->validate([
            'code' => 'required|min:3|regex:/^[^#]+$/U',
        ]);
        $this->service->savePurchaseCode($request);
        return response()->json(['status' => true, 'message' => trans('default.purchase_code_verified')]);
    }

}