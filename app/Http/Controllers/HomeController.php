<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectResource;
use App\Models\Resource;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::where('status', 1)->get();
        $roles = Role::all();
        return view('welcome', ['projects'=>$projects, 'roles'=>$roles]);
    }

    public function resources(Request $request)
    {
        $resources = Resource::where('role_id', $request->id)->get();
        return response()->json($resources);
    }

    public function allocation(Request $request)
    {
        $allocation_fulfilled = ProjectResource::where([
            'resource_id'=>$request->resource,
            'allocation_start_date'=>$request->allocation_start_date
        ])->sum('allocation');
        return response()->json($allocation_fulfilled);
    }

    public function assign(Request $request)
    {
        ProjectResource::create($request->all());
        return redirect()->back()->with('message', 'Project assigned to resource successfully!');
    }

    public function detail()
    {
        return view('result');
    }

    public function result(Request $request)
    {
        $month_last_date = [
            '01' => 31,
            '02' => 28,
            '03' => 31,
            '04' => 30,
            '05' => 31,
            '06' => 30,
            '07' => 31,
            '08' => 31,
            '09' => 30,
            '10' => 31,
            '11' => 30,
            '12' => 31,
        ];
        $date_string = $request->year.'-'.$request->month.'-'.$month_last_date[$request->month];
        $date = Carbon::parse($date_string);

        $range_condition = [
            ['allocation_start_date', '<=', $date],
            ['allocation_end_date', '>=', $date],
        ];

        $projects = ProjectResource::select('project_id')->where($range_condition)->distinct()->get();

        $project_resources = [];
        foreach ($projects as $project) {
            $project_data = ProjectResource::with('resource')->select('resource_id', 'allocation')
                ->where('project_id', $project->project_id)->where($range_condition)->get();
            $project_resources['each'][] = [
                'project_name' => $project->project->name,
                'resources' => $project_data
            ];
        }
        $project_resources['total_allocation'] = ProjectResource::select('resource_id', DB::raw('SUM(allocation) AS total_allocation'))
            ->where($range_condition)->groupBy('resource_id')->get();
        $project_resources['resources'] = Resource::where('joined_date', '<=', "$request->year-$request->month-31")->get();

        return response()->json($project_resources);
    }
}
