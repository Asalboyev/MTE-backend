<?php

namespace App\Http\Controllers;

use App\Models\AdvantageCategory;
use App\Models\Application;
use App\Models\Member;
use App\Models\Partner;
use App\Models\Product;
use App\Models\ProductsCategory;
use App\Models\Question;
use App\Models\Service;
use App\Models\Development;
use Illuminate\Http\Request;

class WebController extends Controller
{

    public function application(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'page' => 'required|integer'
        ]);

        Application::create($request->all());

        return back()->with([
            'success' => true,
            'message' => 'Ваша заявка принята!'
        ]);
    }
}
