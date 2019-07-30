<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientCoreController;
use Illuminate\Support\Facades\Input;
use App\Models\Funnel;

class FunnelController extends ClientCoreController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Listing leads
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user_id = \Auth::id();
        $funnels = Funnel::select('ukey as value', 'name as text')->where('user_id', $user_id)->get()->toArray();

        return view('client.funnel.index', ['funnels' => $funnels]);
    }

    /**
     * Manage all ajax calls
     *
     * @param  string  $method
     * @return JSON object
     */
    public function apiaccess($method)
    {
        $return_data = ['status' => 0];
        $method = $this->secure_data($method);

        switch ($method) {
            case 'fetchdata':
                $return_data = $this->fetchdata();
                break;

            case 'updatefunnel':
                $return_data = $this->updatefunnel();
                break;
        }

        return json_encode($return_data);
    }

    /**
     * Fetch funnel content
     *
     * @return array
     */
    public function fetchdata()
    {
        $return_data = ['status' => 0];

        $user_id = \Auth::id();
        $ukey = $this->secure_data(Input::get('ukey'));
        $data = Funnel::where('ukey', $ukey)->where('user_id', $user_id)->first()->toArray();
        if($data) {
            $data['content'] = json_decode($data['content'], true);
            $return_data['status'] = 1;
            $return_data['data'] = $data;
            $return_data['message'] = 'Funnel data fetch successfully.';
        }
        else {
            $return_data['message'] = 'This funnel is not available.';
        }

        return $return_data;
    }

    /**
     * Add / Update funnel
     *
     * @return array
     */
    public function updatefunnel()
    {
        $return_data = ['status' => 0];
        $user_id = \Auth::id();
        $ukey = md5(rand().'-'.time().'-'.rand());

        $rules = \Config::get('validation.funnelupdate');
        $messages = [];
        $attributeNames = [];
        $validator = \Validator::make(Input::all(), $rules, $messages);
        $validator->setAttributeNames($attributeNames);

        if (!$validator->fails()) {
            $data = ['user_id' => $user_id];
            $fields = ['name', 'subject'];
            foreach ($fields as $form_field => $field) {
                $data[$field] = $this->secure_data(Input::get($field));
            }
            $data['content'] = json_encode(Input::get('content'));

            $ukey_id = $this->secure_data(Input::get('ukey', ''));
            if ($ukey_id == '' && !$ukey_id) {
                $data['ukey'] = $ukey;
                $funnel_data = Funnel::create($data);
                $msg = 'Funnel inserted successfully.';
                $type = 'insert';
            } else {
                // Update
                Funnel::where('ukey', $ukey_id)->update($data);
                $funnel_data = Funnel::where('ukey', $ukey_id)->first();
                $msg = 'Funnel updated successfully.';
                $type = 'update';
            }

            $return_data['status'] = 1;
            $return_data['type'] = $type;
            $return_data['data'] = ['value' => $funnel_data['ukey'], 'text' => $funnel_data['name']];
            $return_data['message'] = $msg;
        }
        else {
            $return_data['message'] = implode("<br>", $validator->errors()->all());
        }

        return $return_data;
    }

    /**
     * Delete funnel
     *
     * @param  string  $ukey
     * @return JSON object
     */
    public function destroy($ukey)
    {
        $return_data = ['status' => 0];
        $user_id = \Auth::id();
        $ukey = $this->secure_data($ukey);

        $affectedRows = Funnel::where('user_id', $user_id)->where('ukey', $ukey)->delete();
        if($affectedRows > 0) {
            $return_data['status'] = 1;
            $return_data['message'] = 'Funnel deleted successfully.';
        }
        else {
            $return_data['message'] = "This funnel is not exists.";
        }

        return json_encode($return_data);
    }

}
