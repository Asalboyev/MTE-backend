<?php

namespace App\Http\Controllers;

use App\Models\Application;

class ApplicationsController extends Controller
{
    public $title = 'Запросы';
    public $route_name = 'applications';
    public $route_parameter = 'application';

    public function index()
    {
        $applications = Application::latest()
            ->paginate(12);

            return view('app.applications.index', [
                'title' => $this->title,
                'route_name' => $this->route_name,
                'route_parameter' => $this->route_parameter,
                'applications' => $applications
            ]);
    }

    public function destroy(Application $application)
    {
        $application->delete();

        return back()->with([
            'success' => true,
            'message' => 'Успешно удален'
        ]);
    }
}
