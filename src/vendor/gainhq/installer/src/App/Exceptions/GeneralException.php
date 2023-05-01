<?php

namespace Gainhq\Installer\App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class GeneralException extends Exception
{
    public function __construct($message = '', $code = 403, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function report()
    {
        //
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  Request $request
     * @return Response
     */
    public function render($request)
    {
        if ($request->expectsJson())
            return response(['status' => false, 'message' => $this->getMessage()], $this->getCode());

        if(view()->exists('custom_errors.'.$this->getCode()))
            return response()->view('custom_errors.'.$this->getCode(), ['message' => $this->getMessage()]);

        return redirect()
            ->back()
            ->withInput()
            ->withFlashDanger($this->message);
    }
}
