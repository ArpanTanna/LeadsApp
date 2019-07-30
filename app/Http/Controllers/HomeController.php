<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Set flag email_open = 1 for emailinfo table
     *
     * @return 1.png Image File
     */
    public function emailopen($eikey, $leadkey)
    {
        $eikey = $this->secure_data($eikey);
        $leadkey = $this->secure_data($leadkey);

        if($eikey && $leadkey) {
            $lead_data = \App\Models\Lead::where('ukey', $leadkey)->first();
            if($lead_data) {
                $ei_data = \App\Models\Emailinfo::where('ukey', $eikey)->where('lead_id', $lead_data->id)->first();
                if($ei_data) {
                    $ei_data->is_open = 1;
                    $ei_data->save();
                }
            }
        }

        $img_path = public_path('images/1.png');
        $img = \File::get($img_path);
        return response($img)
            ->header('Content-type','image/png')
            ->header('content-length', filesize($img_path))
            ->header('Content-Disposition','attachment; filename=1.png')
            ->header('Pragma','no-cache')
            ->header('Expires',0);
    }
}
