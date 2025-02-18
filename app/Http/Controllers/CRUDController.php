<?php

namespace App\Http\Controllers;

use App\Http\Helpers\SMSHelper;
use App\Mail\BaseMailable;
use Carbon\Carbon;
use App\Models\CustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class CRUDController extends Controller
{
    use SMSHelper;

    # Controller Base Data
    protected $baseData = null;

    protected $INPUTS = [];

    protected $defaults = [
        'VIEW_SIZE' => 6
    ];

    public function baseData($key = null)
    {
        $this->baseData = array_merge($this->defaults, $this->baseData);

        if ((!$key || $key == 'DYNAMIC_INPUTS') && !isset($this->baseData['DYNAMIC_INPUTS'])) {
            $this->baseData['DYNAMIC_INPUTS'] = $this->dynamicInputs();
        }

        return $key ? ($this->baseData[$key] ?? null) : $this->baseData;
    }

    public function baseQueries($key = null)
    {
        if (!isset($this->baseData['BASE_QUERIES'])) {
            $this->setBaseQueries();
        }

        return $key ? $this->baseData['BASE_QUERIES'][$key] : $this->baseData['BASE_QUERIES'];
    }

    public function setBaseQueries()
    {
        $this->baseData['BASE_QUERIES'] = [];
    }

    /**
     * add an input to the crud controller
     *
     * @param array $inputData
     * @return void
     */
    public function addInput($data)
    {
        $this->INPUTS[] = (object) array_merge([
            'label'        =>  '',
            'name'         => '',
            'help_text'    => '',
            'width'        => 'col-lg-12',
            'type'         => '', // number, password, text, email, color, hidden, date, time, date-time, textarea, checkbox, switch, radio, file, select, select2, select2-ajax
            'id'           =>  '',
            'class'        =>  '',
            'parent_class' =>  'mb-5',
            'required'     =>  false,
            'disabled'     =>  '',
            'readonly'     =>  '',
            'defaultValue' =>  '',
            'autocomplete' =>  'off',
            'validation'   =>  '',
            'min'          =>  '', //for number, date fields
            'max'          =>  '', //for number, date fields
            'maxlength'    =>  '', //for text fields
            'minlength'    =>  '', //for text fields
            'multiple'     =>  false, // for select2
            'rows'         =>  5, //for textarea
            'size'         => 'md', // for checkbox, switch, select2  (md, lg, xl, sm)
            'placeholder'  => '',
            'jsFunction'   => '', // for select,
            'options'      => [], // for select, select2, radio  (array) ['value' => 'label']
            'custom'       => false, //if true exlude this input from crud store and handle it in the child controller of the module,
            'multipleEdit' => false //if true this input will be used in multiple edit
        ], $data);
    }

    public function index()
    {
        $this->authorize('read', $this->baseData('MODEL')); //was viewAny

        $this->baseQueries();

        $data = $this->baseData();

        $VIEW_FOLDER = $this->baseData('VIEW_FOLDER');

        return View::first(["tenant.{$VIEW_FOLDER}.index", 'tenant.crud_base.index'], $data);
    }

    public function quickAdd()
    {

        $this->authorize('read', $this->baseData('MODEL')); //was viewAny

        $this->baseQueries();

        $data = $this->baseData();

        $VIEW_FOLDER = $this->baseData('VIEW_FOLDER');

        return View::first(["tenant.{$VIEW_FOLDER}.quick_add", 'tenant.crud_base.quick_add'], $data);
    }

    public function datatable(Request $request)
    {
        # Get Table Name From Base Data.
        $table_name        = $this->baseData['TABLE_NAME'];
        $table_fields      = array_keys($this->baseData['TABLE_FIELDS']);
        $TABLE_FILTERS     = $this->baseData['TABLE_FILTERS'];
        $JOIN_TABLE_FIELDS = $this->baseData['JOIN_TABLE_FIELDS'];
        $goTo              = $request->goTo ?? null;

        $selectedFields = ["{$table_name}.id"];
        foreach ($table_fields as $field) {
            $selectedFields[] = "{$table_name}.{$field}";
        }
        foreach ($JOIN_TABLE_FIELDS as $table => $fileds) {
            $table = explode('|', $table);
            foreach ($fileds as $fieldKey => $fieldValue) {
                $selectedFields[] = "{$table[0]}.{$fieldKey} as {$table[0]}_{$fieldKey}";
            }
        }


        # Get Datatable Data From Database
        if (isset($this->baseData['DATATABLE_QUERY']) && $this->baseData['DATATABLE_QUERY'] == 'model') {
            $data = $this->baseData['MODEL']::query();
            # Custom Fields On Datatable
            $customFields =  CustomField::where('module', ucfirst($table_name))->orderBy('order', 'ASC')->where('activated', 1)->where('show_on_table', 1)->get();
            if ($customFields->count()) {
                $data->with('customFieldsData');
            }
        } else {
            $data  = DB::table($table_name);
        }

        $data->when($goTo, function ($query) use ($goTo, $table_name) {
            $query->where($table_name . '.id', $goTo);
        });

        $data->when($JOIN_TABLE_FIELDS, function ($query) use ($JOIN_TABLE_FIELDS, $table_name) {
            // Join Tables
            foreach ($JOIN_TABLE_FIELDS as $table => $fileds) {
                $table = explode('|', $table);
                $query->leftJoin($table[0], $table[0] . '.id', '=', $table_name . '.' . $table[1]);
            }
        })
            ->when($TABLE_FILTERS, function ($query) use ($TABLE_FILTERS, $request, $table_name) {
                if ($TABLE_FILTERS['date_range']) {
                    $dateRange = $request->date_range;
                    $query->when($dateRange, function ($query) use ($dateRange, $table_name) {
                        $dateRange = explode('to', $dateRange);
                        $fromDate  = Carbon::parse($dateRange[0] . " 00:00:00", app('sharedData')->timezone)->setTimezone('UTC');
                        $toDate    = Carbon::parse(($dateRange[1] ?? $dateRange[0]) . " 23:59:59", app('sharedData')->timezone)->setTimezone('UTC');
                        $query->whereBetween($table_name . '.created_at', [$fromDate, $toDate]);
                    });
                }
                foreach ($TABLE_FILTERS as $filterKey => $filterValue) {
                    if (!$filterValue) continue;
                    if ($filterKey == 'date_range') continue;
                    $query->when($request->{$filterKey}, function ($query) use ($filterKey, $filterValue, $request, $table_name) {
                        if ($filterValue === 'Multiple') {
                            $query->whereIn($table_name . '.' . $filterKey, $request->{$filterKey});
                        } else {
                            $query->where($table_name . '.' . $filterKey, $request->{$filterKey});
                        }
                    });
                }
            })->when(method_exists($this, 'modifyDatatables'), function ($query) {
                $this->modifyDatatables($query);
            })
            ->addSelect($selectedFields);

        # Datatable
        if (!empty($this->baseData['DATATABLE_QUERY']) && $this->baseData['DATATABLE_QUERY'] == 'model') {
            $data   =  Datatables()->eloquent($data);
            # Custom Fields On Datatable
            if ($customFields->count()) {
                foreach ($customFields as $customField) {
                    $data->addColumn('custom_field_' . $customField->id, function ($row) use ($customField) {
                        $value = $row->customFieldsData->where('custom_field_id', $customField->id)->value('value');
                        if ($value) {
                            return $value;
                        }
                        return null;
                    });
                }
            }
        } else {
            $data   =  Datatables()->query($data);
        }

        $table = $data->editColumn('created_at', function ($row) {
            return Carbon::createFromDate($row->created_at)->setTimezone(app('sharedData')->timezone)->format('Y-m-d h:i A');
        });

        return   $table->rawColumns($this->baseData('RAW_COLUMNS') ?? [])->toJson();
    }

    public function store(Request $request)
    {
        $this->authorize('create', $this->baseData('MODEL'));

        # Get Base Inputs
        $INPUTS  = $this->baseData('DYNAMIC_INPUTS');

        # Valdiation Rules
        $rules    = [];

        # Valdiation Messages
        $messages = $this->customValidationMessages();

        $customRules = $this->customRules() ?? [];

        # Dinamic Inputs
        foreach ($INPUTS as $Input) {
            if (isset($Input->validation)) {
                $rules[$Input->name] =  array_merge(
                    (function () use ($Input) {
                        if (!empty($Input->validation)) {
                            return is_array($Input->validation) ? $Input->validation : explode('|', $Input->validation);
                        }

                        return [];
                    })(),
                    (function () use ($customRules, $Input) {
                        if (isset($customRules[$Input->name])) {
                            return is_array($customRules[$Input->name]) ? $customRules[$Input->name] : explode('|', $customRules[$Input->name]);
                        } else {
                            return [];
                        }
                    })()
                );
                unset($customRules[$Input->name]);
            }
        }

        $rules = $rules + $customRules;

        # Return Validation
        $this->validate($request, $rules, $messages);

        try {
            DB::beginTransaction();

            # Save To Database
            $st = new ($this->baseData('MODEL'));

            # Dinamic inputs
            foreach ($INPUTS as $Input) {
                if (isset($Input->name) && isset($request->{$Input->name}) && !$Input->custom) {

                    switch ($Input->type) {
                        case 'file':
                            if (isset($request->{$Input->name})) {
                                $st->addMediaFromRequest($Input->name)->toMediaCollection($this->baseData('TABLE_NAME'));
                            }
                            break;
                        case 'date-time':
                            $st->{$Input->name} = Carbon::parse($request->{$Input->name}, app('sharedData')->timezone)->setTimezone('UTC');
                            break;

                        default:

                            $st->{$Input->name} = $request->{$Input->name};
                            break;
                    }
                }
            }

            # Custom inputs
            $st = $this->customInputs($st, $request);

            $this->save($st, $request);

            $this->afterSave($st, $request);

            DB::commit();

            return self::checkResponse($st, 'store');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            session()->flash('error_message', __('Error Cannot Save Data') . ':' . $th->getMessage());
            return response()->json(['error' => 'invalid' . ':' . $th->getMessage()], 422);
        }
    }

    public function save(&$model, $request)
    {
        $model->save();
    }

    public function customInputs($model, Request $request)
    {
        return $model;
    }

    public function customRules(): array
    {
        return [];
    }

    public function customValidationMessages(): array
    {
        return [];
    }

    public function afterSave($model, Request $request)
    {
    }

    public function show($id)
    {
        $this->authorize('view', $this->baseData('MODEL'));

        $this->baseQueries();

        # Get Base Data
        $data         = $this->baseData();
        $VIEW_FOLDER  = $data['VIEW_FOLDER'];
        $MODEL     = $data['MODEL']::find($id);

        # Edit Base Data And Pass To View
        $data['MODAL_HEAD_SHOW'] .= " " . ($MODEL->name ?? '#' . $MODEL->id);
        $data['MODEL'] = $MODEL;

        return View::first(["tenant.{$VIEW_FOLDER}.show", 'tenant.crud_base.show'], $data);
    }

    public function edit($id)
    {
        $editData = $this->baseData['MODEL']::find($id);

        $this->authorize('update', $editData);

        $this->baseQueries();

        # Get Base Data
        $data         = $this->baseData();
        $VIEW_FOLDER  = $data['VIEW_FOLDER'];

        # Edit Base Data And Pass To View
        $data['MODAL_HEAD_EDIT'] .= " " . ($editData->name ?? '');
        $data['editData'] = $editData;

        return View::first(["tenant.{$VIEW_FOLDER}.modal_edit", 'tenant.crud_base.modal_edit'], $data);
    }

    public function update(Request $request, $id)
    {
        if ($id == '0' && $request->multiple_edit) {
            return $this->multipleUpdate($request);
        } else {
            $st = $this->baseData('MODEL')::find($id);
        }

        $st = $this->baseData('MODEL')::find($id);

        $this->authorize('update', $st);

        # Get Base Inputs
        $INPUTS  = $this->baseData('DYNAMIC_INPUTS');

        # Valdiation Rules
        $rules    = [];

        # Valdiation Messages
        $messages = $this->customValidationMessages();

        $customRules = $this->customRules();

        # Dinamic Inputs
        foreach ($INPUTS as $Input) {
            if (isset($Input->validation)) {
                $rules[$Input->name] =  array_merge(
                    (function () use ($Input) {
                        if (!empty($Input->validation)) {
                            return is_array($Input->validation) ? $Input->validation : explode('|', $Input->validation);
                        }

                        return [];
                    })(),
                    (function () use ($customRules, $Input) {
                        if (isset($customRules[$Input->name])) {
                            return is_array($customRules[$Input->name]) ? $customRules[$Input->name] : explode('|', $customRules[$Input->name]);
                        } else {
                            return [];
                        }
                    })()
                );
                unset($customRules[$Input->name]);
            }
        }

        $rules = $rules + $customRules;

        # Special Rules Handling
        foreach ($rules as $key => $rule) {
            $inputRules = is_array($rule) ? $rule : explode('|', $rule);
            $fieldType = collect($INPUTS)->where('name', $key)->first()?->type;
            foreach ($inputRules as &$inputRule) {
                if (is_string($inputRule) && str_contains($inputRule, 'unique')) {
                    $inputRule = explode(',', $inputRule);
                    $inputRule[2] = $id;
                    $inputRule  = implode(',', $inputRule);
                    break;
                }

                if ($fieldType == 'file' && $inputRule == 'required') {
                    $inputRule = null;
                }
            }
            $rules[$key] = array_filter($inputRules);
        }

        # Return Validation
        $this->validate($request, $rules, $messages);

        try {
            DB::beginTransaction();

            # Dinamic inputs
            foreach ($INPUTS as $Input) {

                if ($Input->type == 'select2-ajax' && !$Input->custom) {
                    $st->{$Input->name} = $request->{$Input->name};
                    continue;
                }

                if (isset($Input->name) && isset($request->{$Input->name}) && !$Input->custom) {

                    switch ($Input->type) {
                        case 'file':
                            $st->clearMediaCollection();
                            if ($st->hasMedia($this->baseData('TABLE_NAME'))) {
                                $st->clearMediaCollection($this->baseData('TABLE_NAME'));
                            }
                            if (isset($request->{$Input->name})) {
                                $st->addMediaFromRequest($Input->name)->toMediaCollection($this->baseData('TABLE_NAME'));
                            }
                            break;
                        case 'date-time':
                            $st->{$Input->name} = Carbon::parse($request->{$Input->name}, app('sharedData')->timezone)->setTimezone('UTC');
                            break;

                        default:

                            $st->{$Input->name} = $request->{$Input->name};
                            break;
                    }
                }
            }

            # Custom inputs
            $st = $this->customInputs($st, $request);

            $this->baseData['original'] = $st->getOriginal();

            $this->save($st, $request);

            $this->afterSave($st, $request);

            DB::commit();

            return self::checkResponse(true, 'update');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            session()->flash('error_message', __('Error Cannot Updated Data') . ':' . $th->getMessage());
            return response()->json(['error' => 'invalid' . ':' . $th->getMessage()], 422);
        }
    }

    public function multipleUpdate(Request $request)
    {
        $ids = explode(',', $request->multiple_edit);
        $stMulti = $this->baseData('MODEL')::whereIn('id', $ids)->get();

        foreach ($stMulti as $st) {
            $this->authorize('update', $st);
        }

        # Get Base Inputs
        $INPUTS  = collect($this->baseData('DYNAMIC_INPUTS'))->where('multipleEdit', true);

        # Valdiation Rules
        $rules    = [];

        # Valdiation Messages
        $messages = $this->customValidationMessages();

        $customRules = $this->customRules();

        # Dinamic Inputs
        foreach ($INPUTS as $Input) {
            if (isset($Input->validation) && $request->{$Input->name}) {
                $rules[$Input->name] =  array_merge(
                    (function () use ($Input) {
                        if (!empty($Input->validation)) {
                            return is_array($Input->validation) ? $Input->validation : explode('|', $Input->validation);
                        }

                        return [];
                    })(),
                    (function () use ($customRules, $Input) {
                        if (isset($customRules[$Input->name])) {
                            return is_array($customRules[$Input->name]) ? $customRules[$Input->name] : explode('|', $customRules[$Input->name]);
                        } else {
                            return [];
                        }
                    })()
                );
                unset($customRules[$Input->name]);
            }
        }

        $rules = $rules + $customRules;

        # Special Rules Handling
        foreach ($rules as $key => $rule) {
            $inputRules = is_array($rule) ? $rule : explode('|', $rule);
            $fieldType = collect($INPUTS)->where('name', $key)->first()?->type;
            foreach ($inputRules as &$inputRule) {
                if (is_string($inputRule) && str_contains($inputRule, 'unique')) {
                    $inputRule = explode(',', $inputRule);
                    $inputRule[2] = 0;
                    $inputRule  = implode(',', $inputRule);
                    break;
                }

                if ($fieldType == 'file' && $inputRule == 'required') {
                    $inputRule = null;
                }
            }
            $rules[$key] = array_filter($inputRules);
        }

        # Return Validation
        $this->validate($request, $rules, $messages);

        try {
            DB::beginTransaction();

            foreach ($stMulti as $st) {

                # Dinamic inputs
                foreach ($INPUTS as $Input) {
                    if (!$request->{$Input->name} && $request->{$Input->name} != "0") continue;

                    if ($Input->type == 'select2-ajax' && !$Input->custom) {
                        $st->{$Input->name} = $request->{$Input->name};
                        continue;
                    }

                    if (isset($Input->name) && isset($request->{$Input->name}) && !$Input->custom) {
                        switch ($Input->type) {
                            case 'file':
                                $st->clearMediaCollection();
                                if ($st->hasMedia($this->baseData('TABLE_NAME'))) {
                                    $st->clearMediaCollection($this->baseData('TABLE_NAME'));
                                }
                                if (isset($request->{$Input->name})) {
                                    $st->addMediaFromRequest($Input->name)->toMediaCollection($this->baseData('TABLE_NAME'));
                                }
                                break;
                            case 'date-time':
                                $st->{$Input->name} = Carbon::parse($request->{$Input->name}, app('sharedData')->timezone)->setTimezone('UTC');
                                break;

                            default:

                                $st->{$Input->name} = $request->{$Input->name};
                                break;
                        }
                    }
                }

                # Custom inputs
                $st = $this->customInputs($st, $request);

                $this->baseData['original'] = $st->getOriginal();

                $this->save($st, $request);

                $this->afterSave($st, $request);
            }

            DB::commit();

            return self::checkResponse(true, 'update');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            session()->flash('error_message', __('Error Cannot Updated Data') . ':' . $th->getMessage());
            return response()->json(['error' => 'invalid' . ':' . $th->getMessage()], 422);
        }
    }

    public function destroy($ids)
    {
        $this->authorize('delete', $this->baseData('MODEL'));

        $IdsToDelete = explode(',', $ids);
        $deleted = true;

        foreach ($this->baseData('MODEL')::whereIn('id', $IdsToDelete)->get() as $model) {
            try {
                DB::beginTransaction();
                $this->authorize('delete', $model);
                $model->delete();

                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();

                $deleted = false;
            }
        }

        # Return To Response.
        return $deleted ? self::checkResponse($deleted, 'delete') : self::checkResponse(__('Some of the selected Items are used somewhere in the system and can\'t be deleted'));
    }

    public function itemsForSelect($data)
    {
        $data = explode(',', $data);

        $table = array_shift($data);

        $result = DB::table($table);

        if (Schema::hasColumn($table, 'deleted_at')) {
           $result = $result->whereNull('deleted_at');
        }

        if (request()->q) {
            $result = $result->where($data[0], 'like', '%' . request()->q . '%');
        }
        //dd(request()->where);
        if (request()->where) {
            // example: ?where[]=id,!=,1&where[]=is_customer,=,1
            if (is_array(request()->where)) {
                foreach (request()->where as $where) {

                    $columns = explode(',', $where);

                    if($columns[1]=='NULL'){
                        $result = $result->whereNull($columns[0]);
                    }
                    elseif(count($columns)>2){
                        $result = $result->where($columns[0], $columns[1],$columns[2]);
                    }
                    else{
                        $result = $result->where(...explode(',', $where));
                    }

                }
            } else {
                $where = explode(',', request()->where);
                $result = $result->where(...$where);
            }
        }

        if (request()->orWhere) {
            // example: ?where[]=id,!=,1&where[]=is_customer,=,1
            if (is_array($orWhere = request()->orWhere)) {

                $firstOrWhere = array_shift($orWhere);

                if (request()->where) {
                    $result = $result->orWhere(...explode(',', $firstOrWhere));
                } else {
                    $result = $result->where(...explode(',', $firstOrWhere));
                }

                foreach ($orWhere as $where) {
                    $result = $result->orWhere(...explode(',', $where));
                }
            }else{
                $orWhere = explode(',', request()->orWhere);
                $result = $result->orWhere(...$orWhere);

            }
        }



        return $result->select("$data[0] AS text", $data[1], ...array_slice($data, 3))->simplePaginate($data[2] ?? 10);
    }

    public function saveCustomFieldsData($model, $request, $className)
    {
        if (!$request->custom_fields) return;
        # Custom Fields
        $className        = "App\Models\\" . $className;
        $customFields     = $request->custom_fields;
        $customFieldsData = [];
        foreach ($customFields as $customFieldId => $customFieldValue) {
            if ($customFieldValue == null) continue;
            // check if array
            if (is_array($customFieldValue)) {
                $customFieldValue = implode(',', $customFieldValue);
            }
            $row = [];
            $row['valuable_id']     = $model->id;
            $row['valuable_type']   = $className;
            $row['custom_field_id'] = $customFieldId;
            $row['value']           = $customFieldValue;
            $customFieldsData[] = $row;
        }
        # Check if method is update then delete all old custom fields
        if ($request->method() == 'PUT') {
            DB::table('custom_fields_data')->where('valuable_id', $model->id)->where('valuable_type', $className)->delete();
        }
        if (count($customFieldsData)) {
            DB::table('custom_fields_data')->insert($customFieldsData);
        }
    }

    public function getCustomFieldsOnDatatableWithData($data, $moduleName)
    {
        # Custom Fields On Datatable
        $customFields =  CustomField::where('module', $moduleName)
            ->orderBy('order', 'ASC')
            ->where('activated', 1)
            ->where('show_on_table', 1)
            ->get();
        if ($customFields->count()) {
            $data->with('customFieldsData');
        }
        return $customFields;
    }

    public function addCustomFieldsColumnsOnDatatable($data, $customFields)
    {
        # Custom Fields On Datatable
        if ($customFields->count()) {
            foreach ($customFields as $customField) {
                $data->addColumn('custom_field_' . $customField->id, function ($row) use ($customField) {
                    $value = $row->customFieldsData->where('custom_field_id', $customField->id)->value('value');
                    if ($value) {
                        return $value;
                    }
                    return null;
                });
            }
        }
        return $data;
    }

    public function shareView($modelId)
    {

        $MODEL = $this->baseData('MODEL')::find($modelId);

        $printTemplate = $this->baseQueries('printTemplate');

        $VIEW_FOLDER = $this->baseData('VIEW_FOLDER');

        return View::first(["tenant.{$VIEW_FOLDER}.share", 'tenant.crud_base.share'], compact('MODEL', 'printTemplate'));
    }

    public function getsharingData($model)
    {
        switch (get_class($model)) {
            case 'App\Models\SalesQuotation':
                return [
                    'subject' => 'Quotation_#' . $model->id,
                    'link' => route('customer.share-quotation', $model->id)
                ];
                break;
            case 'App\Models\SalesOrder':
                return [
                    'subject' => 'Order_#' . $model->id,
                    'link' => route('customer.share-order', $model->id)
                ];
                break;
            case 'App\Models\SalesInvoice':
                return [
                    'subject' => 'Invoice_#' . $model->id,
                    'link' => route('customer.share-invoice', $model->id)
                ];
                break;
            case 'App\Models\PurchasesRequestForQuotation':
                return [
                    'subject' => 'RFQ_#' . $model->id,
                    'link' => route('customer.share-rfq', $model->id)
                ];
                break;
            case 'App\Models\PurchasesOrder':
                return [
                    'subject' => 'Purchase_Order_#' . $model->id,
                    'link' => route('customer.share-purchase-order', $model->id)
                ];
                break;
            case 'App\Models\PurchasesBill':
                return [
                    'subject' => 'Bill_#' . $model->id,
                    'link' => route('customer.share-bill', $model->id)
                ];
                break;
        }
    }

    public function emailShare($modelId)
    {
        $model = $this->baseData('MODEL')::find($modelId);

        try {

            $VIEW_FOLDER = $this->baseData('VIEW_FOLDER');

            Mail::to($model->contact ?? $model->suppliers)->send(new BaseMailable($model, "tenant.{$VIEW_FOLDER}.email", ...$this->getsharingData($model)));
        } catch (\Throwable $th) {

            return response()->json(['message' => $th->getMessage()], 422);
        }

        return response()->json(['message' => 'email sent successfully']);
    }

    public function smsShare($modelId)
    {
        $model = $this->baseData('MODEL')::find($modelId);

        try {

            $this->sendMsg($model->contact->mobile, $this->getsharingData($model)['link']);
        } catch (\Throwable $th) {

            return response()->json(['message' => $th->getMessage()], 422);
        }

        return response()->json(['message' => 'Sms sent successfully']);
    }
}
