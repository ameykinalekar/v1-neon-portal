<?php
 
namespace App\Exceptions;
 
use Exception;
 
class ApiException extends Exception
{
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
    }
 
    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json(['data' => [], 'status' => 500, 'message' => [$this->getMessage()]]);
    }
}