<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\ConfigGroup;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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

    public function upload_from_dropzone(Request $request)
    {
        try {
            $img = $request->file('file');
            $img_name = Str::random(12) . '.' . $img->extension();
            $saved_img = $img->move(public_path('upload/images'), $img_name);

            if (!File::exists('upload/images/200')) {
                File::makeDirectory(public_path('upload/images/200'), 0700, true, true);
            }
            Image::make($saved_img)->resize(200, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save(public_path('upload/images/200/' . $img_name), 60);

            if (!File::exists('upload/images/600')) {
                File::makeDirectory(public_path('upload/images/600'), 0700, true, true);
            }
            Image::make($saved_img)->resize(600, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save(public_path('upload/images/600/' . $img_name), 80);

            return response()->json([
                'file_name' => $img_name
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // upload image for CKEditor
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $fileName = time() . '.' . $request->file('upload')->getClientOriginalExtension();

            $request->file('upload')->move(public_path('upload'), $fileName);

            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $url = asset('upload/' . $fileName);
            $msg = 'Image upload successfully!';
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";

            @header('Content-type: text/html; charset=utf-8');
            echo $response;
        }
    }

    public function config()
    {
        $config = Config::all();
        $config_groups = ConfigGroup::all();

        return view('app.config.index', compact(
            'config',
            'config_groups'
        ));
    }

    public function config_update(Request $request)
    {
        $data = $request->all();

        foreach (ConfigGroup::all() as $item) {
            $item->update([
                'is_active' => 0
            ]);
        }
        if (isset($data['config_groups'])) {
            foreach ($data['config_groups'] as $key => $item) {
                $config = ConfigGroup::find($key);
                $config->update([
                    'is_active' => 1
                ]);
            }
        }

        foreach (Config::all() as $item) {
            $item->update([
                'is_active' => 0
            ]);
        }
        foreach ($data['config'] as $key => $item) {
            $config = Config::find($key);
            $config->update([
                'is_active' => 1
            ]);
        }

        return back()->with([
            'success' => true,
            'message' => 'Успешно сохранен'
        ]);
    }
}
