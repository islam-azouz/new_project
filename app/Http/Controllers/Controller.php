<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param mixed $data the success state of response or the message to flush if the type omitted
     * @param string $type the type of the operation (store, update, delete)
     */
    public static function checkResponse($data, $type = 'default')
    {
        # Check Data And Set Flash Sessin.
        switch ($type) {
            case 'store':
                if ($data) {
                    session()->flash('success_message', __('Data Saved Successfully'));
                    $response = response()->json(['success' => 'success', 'data' => $data], 200);
                } else {
                    session()->flash('error_message', __('Error Cannot Save Data'));
                    $response = response()->json(['error' => 'invalid'], 422);
                }
                break;
            case 'update':
                if ($data) {
                    session()->flash('success_message', __('Data Updated Successfully'));
                    $response = response()->json(['success' => 'success'], 200);
                } else {
                    session()->flash('error_message', __('Error Cannot Updated Data'));
                    $response = response()->json(['error' => 'invalid'], 422);
                }
                break;
            case 'delete':
                if ($data) {
                    session()->flash('warning_message', __('Successfully Deleted Selected Rows'));
                    $response = response()->json(['success' => 'success'], 200);
                } else {
                    session()->flash('error_message', __('Error Cannot Delete Selected Rows'));
                    $response = response()->json(['error' => 'invalid'], 422);
                }
                break;
            default:
                session()->flash('error_message', $data);
                $response = response()->json(['error' => 'invalid'], 422);
                break;
        }
        # Return Response.
        return $response;
    }
}
