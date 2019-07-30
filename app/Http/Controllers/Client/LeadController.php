<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientCoreController;
use Illuminate\Support\Facades\Input;
use App\Models\Lead;

class LeadController extends ClientCoreController
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
        return view('client.lead.index');
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
            case 'chgstatus':
                $return_data = $this->chgstatus();
                break;

            case 'fetchrecords':
                $return_data = $this->fetchrecords();
                break;

            case 'leadinfo':
                $return_data = $this->leadinfo();
                break;

            case 'updatelead':
                $return_data = $this->updatelead();
                break;

            case 'emailmanager':
                $return_data = $this->emailmanager();
                break;
        }

        return json_encode($return_data);
    }

    /**
     * Fetch lead records
     *
     * @return array
     */
    public function fetchrecords()
    {
        $return_data = ['status' => 0];

        $user_id = \Auth::id();
        $current_page = $this->secure_data(Input::get('currentPage'));
        if($current_page === '') {$current_page = 0;}
        $limit = 10;
        $from = $current_page * $limit;

        $arrfields = array(
            0=>'title', 1=>'status', 2=>'activity', 3=>'name',
            4=>'company_name', 5=>'city', 6=>'contact'
        );
        $numericfields = array();
        $datefields = array();
        $jsonParams = [
            0 => ['title', 'leads.other_info'],
            5 => ['city', 'leads.other_info']
        ];
        $csortColumn = $this->secure_data(Input::get('sortBy'));
        $csortType = $this->secure_data(Input::get('sort'));

        $where = 'user_id = "'.$user_id.'"';
        $orderby = "";

        // TODO: Move where condition in TFC
        $tfc = new \App\Libraries\TFC();
        $tfc->init('responses', $arrfields, $numericfields, $datefields);
        $tfc->jsonparamsInit(1, $jsonParams);

        if($csortColumn !== '' && !is_null($csortColumn) && in_array($csortColumn, $arrfields)) {
            $orderArr = [];
            if($csortColumn == 'name') {
                $orderArr[] = ['field' => 'first_name', 'column' => '', 'type' => $csortType, 'chk' => 0];
                $orderArr[] = ['field' => 'last_name', 'column' => '', 'type' => $csortType, 'chk' => 0];
            }
            else {
                $field_key = array_search ($csortColumn, $arrfields);
                $orderArr[] = ['column' => $field_key, 'type' => $csortType, 'chk' => 1];
            }
            $orderby = $tfc->init_order($orderby, $orderArr);
            $orderby .= ', leads.id desc';
        }
        else {
            $orderby .= ' leads.id desc';
        }

        $records = Lead::whereRaw($where)
            ->orderByRaw($orderby)
            ->limit($limit)->offset($from)
            ->get()->toArray();

        foreach($records as $k=>$record) {
            $records[$k]['other_info'] = ($record['other_info'] !== '') ? json_decode($record['other_info'], true) : [];
        }

        $return_data['status'] = 1;
        $return_data['items'] = $records;
        $return_data['limit'] = $limit;

        return $return_data;
    }

    /**
     * Change lead status
     *
     * @return array
     */
    public function chgstatus()
    {
        $return_data = ['status' => 0];

        $user_id = \Auth::id();
        $status = $this->secure_data(Input::get('status'));
        $ukey = $this->secure_data(Input::get('ukey'));
        if(in_array($status, [0,1,2,3]))
        {
            $record = Lead::where('ukey', $ukey)->where('user_id', $user_id)->first();
            if($record)
            {
                $record->status = $status;
                $record->save();

                $record_arr = $record->toArray();
                $record_arr['other_info'] = ($record_arr['other_info'] !== '') ? json_decode($record_arr['other_info'], true) : [];

                $return_data['status'] = 1;
                $return_data['record'] = $record_arr;
            }
            else {
                $return_data['message'] = "An error occurred, please try again.";
            }
        }
        else {
            $return_data['message'] = "An error occurred, please try again.";
        }

        return $return_data;
    }

    /**
     * Fetch email sending information
     *
     * @return array
     */
    public function leadinfo()
    {
        $return_data = ['status' => 0];

        $user_id = \Auth::id();
        $lead_id = $this->secure_data(Input::get('id'));
        if($lead_id)
        {
            $record = Lead::where('id', $lead_id)->where('user_id', $user_id)->first();
            if($record)
            {
                $email_info = \App\Models\Emailinfo::select('emailinfo.*', 'funnels.name')->join('funnels', 'funnels.id', '=', 'emailinfo.funnel_id')
                    ->where('lead_id', $lead_id)->orderBy('emailinfo.id', 'desc')->get()->toArray();

                $return_data['status'] = 1;
                $return_data['records'] = $email_info;
            }
            else {
                $return_data['message'] = "An error occurred, please try again.";
            }
        }
        else {
            $return_data['message'] = "An error occurred, please try again.";
        }

        return $return_data;
    }

    /**
     * Add / Update lead
     *
     * @return array
     */
    public function updatelead()
    {
        $return_data = ['status' => 0];
        $user_id = \Auth::id();
        $ukey = md5(rand().'-'.time().'-'.rand());

        $rules = \Config::get('validation.leadupdate');
        $messages = [];
        $attributeNames = [
            'other_info.title' => 'Lead Title',
            'other_info.city' => 'City'
        ];
        $validator = \Validator::make(Input::all(), $rules, $messages);
        $validator->setAttributeNames($attributeNames);

        if (!$validator->fails()) {
            $data = ['user_id' => $user_id, 'status' => 0, 'other_info' => []];
            $fields = ['first_name', 'last_name', 'email', 'phone', 'other_info.city' => 'city', 'other_info.title' => "title", 'company_name'];
            $other_fields = ['city' => 'other_info', 'title' => 'other_info'];
            foreach ($fields as $form_field => $field) {
                if (array_key_exists($field, $other_fields)) {
                    $data[$other_fields[$field]][$field] = $this->secure_data(Input::get($form_field));
                } else {
                    $data[$field] = $this->secure_data(Input::get($field));
                }
            }

            $data['other_info'] = json_encode($data['other_info']);

            $id = $this->secure_data(Input::get('id', ''));
            if ($id == '' && !$id) {
                $data['ukey'] = $ukey;
                $lead_data = Lead::create($data);
                $msg = 'Lead inserted successfully.';
                $type = 'insert';
            } else {
                // Update
                Lead::where('id', $id)->update($data);
                $lead_data = Lead::where('id', $id)->first();
                $msg = 'Lead updated successfully.';
                $type = 'update';
            }

            $lead_data['other_info'] = ($lead_data['other_info'] !== '') ? json_decode($lead_data['other_info'], true) : [];
            $return_data['status'] = 1;
            $return_data['type'] = $type;
            $return_data['lead_data'] = $lead_data;
            $return_data['message'] = $msg;
        }
        else {
            $return_data['message'] = implode("<br>", $validator->errors()->all());
        }

        return $return_data;
    }

    /**
     * Delete lead
     *
     * @param  string  $ukey
     * @return JSON object
     */
    public function destroy($ukey)
    {
        $return_data = ['status' => 0];
        $user_id = \Auth::id();
        $ukey = $this->secure_data($ukey);

        $affectedRows = Lead::where('user_id', $user_id)->where('ukey', $ukey)->delete();
        if($affectedRows > 0) {
            $return_data['status'] = 1;
            $return_data['message'] = 'Lead deleted successfully.';
        }
        else {
            $return_data['message'] = "This lead is not exists.";
        }

        return json_encode($return_data);
    }

    /**
     * Add leads to emailmanager
     *
     * @return array
     */
    public function emailmanager()
    {
        $return_data = ['status' => 0];
        $user_id = \Auth::id();
        $ids = Input::get('ids');

        if (count($ids) > 0) {
            $data = [
                'user_id' => $user_id,
                'lead_ids' => json_encode($ids)
            ];

            \App\Models\Emailmanager::create($data);

            $return_data['status'] = 1;
            $return_data['message'] = 'Redirecting to Email manager, please wait for a moment.';
        }
        else {
            $return_data['message'] = 'Please select at least one lead.';
        }

        return $return_data;
    }
}
