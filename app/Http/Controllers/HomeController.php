<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectResource;
use App\Models\Resource;
use App\Models\Role;
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
        $projects = ProjectResource::select('project_id')
            ->whereYear('allocation_start_date', $request->year)->whereMonth('allocation_start_date', $request->month)
            ->distinct()->get();
        $project_resources = [];
        foreach ($projects as $project) {
            $project_data = ProjectResource::with('resource')->where('project_id', $project->project_id)
            ->whereYear('allocation_start_date', $request->year)->whereMonth('allocation_start_date', $request->month)
                ->select('resource_id', 'allocation')->get();
            $project_resources['each'][] = [
                'project_name' => $project->project->name,
                'resources' => $project_data
            ];
        }
        $project_resources['total_allocation'] = ProjectResource::with('resource')->select('resource_id', DB::raw('SUM(allocation) AS total_allocation'))
            ->whereYear('allocation_start_date', $request->year)->whereMonth('allocation_start_date', $request->month)
            ->groupBy('resource_id')->get();
        $project_resources['resources'] = Resource::where('joined_date', '<=', "$request->year-$request->month-31")->get();

        return response()->json($project_resources);
    }
}
