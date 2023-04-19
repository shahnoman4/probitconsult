<?php

namespace App\Http\Controllers;

use App\Vessel;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VesselController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $moduleUtil;
    protected $commonUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil,Util $commonUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->commonUtil = $commonUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('vessel.view') && ! auth()->user()->can('vessel.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $vessels = Vessel::where('business_id', $business_id)
                        ->select(['name','expected_date','arrival_date','shipping_line_agent','selling_date','id']);
                        // @can("vessel.update")
                        // <button data-href="{{action(\'App\Http\Controllers\VesselController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_vessel_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        //     &nbsp;
                        // @endcan
                return Datatables::of($vessels)
                ->addColumn(
                    'action',
                    '
                    @can("vessel.delete")
                        <button data-href="{{action(\'App\Http\Controllers\VesselController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_vessel_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
                ->editColumn('expected_date', function ($row) {
                    $expected_date = ! empty($row->expected_date) ? $row->expected_date->format('Y-m-d') : "";
                    return $expected_date;
                })
                ->editColumn('arrival_date', function ($row) {
                    $arrival_date = ! empty($row->arrival_date) ? $row->arrival_date->format('Y-m-d') : "";
                    return $arrival_date;
                })
                ->editColumn('selling_date', function ($row) {
                    $selling_date = ! empty($row->selling_date) ? $row->selling_date->format('Y-m-d') : "";
                    return $selling_date;
                })
                ->removeColumn('id')
                ->rawColumns([5])
                ->make(false);
        }

        return view('vessel.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('vessel.create')) {
            abort(403, 'Unauthorized action.');
        }

        $quick_add = false;
        if (! empty(request()->input('quick_add'))) {
            $quick_add = true;
        }

        $is_repair_installed = $this->moduleUtil->isModuleInstalled('Repair');

        return view('vessel.create')
                ->with(compact('quick_add', 'is_repair_installed'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('vessel.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name','expected_date','arrival_date','shipping_line_agent','selling_date']);
            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;
            $input['created_by'] = $request->session()->get('user.id');
            //$input['expected_date'] = $request->has('expected_date') ? $this->commonUtil->uf_date($request->input('expected_date'), true) : null;
             //dd($input);
            if ($this->moduleUtil->isModuleInstalled('Repair')) {
                $input['use_for_repair'] = ! empty($request->input('use_for_repair')) ? 1 : 0;
            }

            $vessel = Vessel::create($input);
            $output = ['success' => true,
                'data' => $vessel,
                'msg' => __('vessel.added_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('vessel.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $vessel = Vessel::where('business_id', $business_id)->find($id);

            $is_repair_installed = $this->moduleUtil->isModuleInstalled('Repair');

            return view('vessel.edit')
                ->with(compact('vessel', 'is_repair_installed'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('vessel.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name','expected_date','arrival_date','shipping_line_agent','selling_date']);
                $business_id = $request->session()->get('user.business_id');

                $vessel = Vessel::where('business_id', $business_id)->findOrFail($id);
                $vessel->name = $input['name'];
                $vessel->expected_date = $input['expected_date'];
                $vessel->arrival_date = $input['arrival_date'];
                $vessel->shipping_line_agent = $input['shipping_line_agent'];
                $vessel->selling_date = $input['selling_date'];

                if ($this->moduleUtil->isModuleInstalled('Repair')) {
                    $vessel->use_for_repair = ! empty($request->input('use_for_repair')) ? 1 : 0;
                }

                $vessel->save();

                $output = ['success' => true,
                    'msg' => __('vessel.updated_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('vessel.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                $vessel = Vessel::where('business_id', $business_id)->findOrFail($id);
                $vessel->delete();

                $output = ['success' => true,
                    'msg' => __('vessel.deleted_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    public function getVesselApi()
    {
        try {
            $api_token = request()->header('API-TOKEN');

            $api_settings = $this->moduleUtil->getApiSettings($api_token);

            $vessel = Vessel::where('business_id', $api_settings->business_id)
                                ->get();
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            return $this->respondWentWrong($e);
        }

        return $this->respond($vessel);
    }
}
