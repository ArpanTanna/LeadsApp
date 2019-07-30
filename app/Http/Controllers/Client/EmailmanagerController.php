<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientCoreController;
use Illuminate\Support\Facades\Input;
use App\Models\Lead;
use App\Models\Funnel;
use App\Models\Emailmanager;
use App\Models\Emailinfo;

class EmailmanagerController extends ClientCoreController
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
        $leads = [];

        $email_manager = Emailmanager::where('user_id', $user_id)->latest()->first();
        if($email_manager) {
            $lead_ids = ($email_manager->lead_ids !== '') ? json_decode($email_manager->lead_ids, true) : [];
            $leads = Lead::select('id', 'first_name', 'last_name', 'company_name', 'email')->whereIn('id', $lead_ids)->get()->toArray();
        }

        return view('client.emailmanager.index', ['funnels' => $funnels, 'leads' => $leads]);
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
            case 'sendemail':
                $return_data = $this->sendemail();
                break;
        }

        return json_encode($return_data);
    }

    /**
     * Send emails
     *
     * @return array
     */
    public function sendemail()
    {
        $return_data = ['status' => 0];
        $user_id = \Auth::id();

        $rules = \Config::get('validation.sendemail');
        $messages = [
            'leads.required' => 'Please select at least one lead.'
        ];
        $attributeNames = [];
        $validator = \Validator::make(Input::all(), $rules, $messages);
        $validator->setAttributeNames($attributeNames);

        if (!$validator->fails()) {
            $funnel_id = $this->secure_data(Input::get('funnel'));
            $funnel_data = Funnel::where('ukey', $funnel_id)->where('user_id', $user_id)->first();
            if($funnel_data) {
                $lead_ids = $this->secure_data(Input::get('leads'));
                $leads = Lead::where('user_id', $user_id)->whereIn('id', $lead_ids)->get()->toArray();
                $content_pre = ($funnel_data->content !== '') ? json_decode($funnel_data->content, true) : "";

                $emailsend = new \App\Libraries\Emailsend();
                $total = count($leads); $total_email_sent = 0;

                foreach($leads as $lead) {
                    $content = replace_dynamic_content($content_pre, $lead);

                    $ukey = md5(rand().'-'.time().'-'.rand());
                    $emailinfo_insert = ['user_id' => $user_id, 'lead_id' => $lead['id'], 'funnel_id' => $funnel_data['id'], 'ukey' => $ukey];
                    $emailinfo_data = Emailinfo::create($emailinfo_insert);

                    $email_content = $emailsend->getcontent($lead, $emailinfo_data, $content, 'emails/common');
                    $is_send = $emailsend->send($funnel_data->subject, $email_content, $lead['email']);

                    $emailinfo_data->is_sent = $is_send;
                    $emailinfo_data->save();

                    if($is_send == 1) {
                        $total_email_sent++;
                    }
                }

                $return_data['status'] = 1;
                $return_data['message'] = 'Total email sent: '.$total_email_sent.' out of '.$total;
            }
            else {
                $return_data['message'] = 'This funnel is not exists.';
            }
        }
        else {
            $return_data['message'] = implode("<br>", $validator->errors()->all());
        }

        return $return_data;
    }
}
