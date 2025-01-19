<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\Lang;
use App\Models\SiteInfo;
use Illuminate\Http\Request;

class SiteInfoController extends Controller
{
    public $title = 'Общие данные';
    public $route_name = 'site_infos';
    public $route_parameter = 'site_info';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $site_info = SiteInfo::latest()
            ->first();
        $langs = Lang::all();

        return view('app.site_infos.index', [
            'title' => $this->title,
            'route_name' => $this->route_name,
            'route_parameter' => $this->route_parameter,
            'site_info' => $site_info,
            'langs' => $langs
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'title.' . $this->main_lang->code => 'required'
        ]);
        if ($validator->fails()) {
            return back()->withInput()->with([
                'success' => false,
                'message' => 'Ошибка валидации'
            ]);
        }

        $data['logo'] = isset($data['logo'][0]) ? $data['logo'][0] : null;
        $data['logo_dark'] = isset($data['logo_dark'][0]) ? $data['logo_dark'][0] : null;
        $data['favicon'] = isset($data['favicon'][0]) ? $data['favicon'][0] : null;

        $site_info = SiteInfo::first();
        
        SiteInfo::updateOrCreate(
            [
                'id' => $site_info->id ?? null
            ],
            [
                'title' => $data['title'],
                'desc' => $data['desc'],
                'logo' => $data['logo'],
                'logo_dark' => $data['logo_dark'],
                'address' => $data['address'],
                'phone_number' => $data['phone_number'],
                'email' => $data['email'],
                'work_time' => $data['work_time'],
                'map' => $data['map'],
                'favicon' => $data['favicon'],
                'telegram' => $data['telegram'],
                'instagram' => $data['instagram'],
                'facebook' => $data['facebook'],
                'youtube' => $data['youtube'],
            ]
        );

        return redirect()->route('site_infos.index')->with([
            'success' => true,
            'message' => 'Успешно сохранен'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SiteInfo  $siteInfo
     * @return \Illuminate\Http\Response
     */
    public function show(SiteInfo $siteInfo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SiteInfo  $siteInfo
     * @return \Illuminate\Http\Response
     */
    public function edit(SiteInfo $siteInfo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SiteInfo  $siteInfo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SiteInfo $siteInfo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SiteInfo  $siteInfo
     * @return \Illuminate\Http\Response
     */
    public function destroy(SiteInfo $siteInfo)
    {
        //
    }
}
