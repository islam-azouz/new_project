<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Traits\SubscriptionsHelperTrait;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\Settings;
use App\Models\CustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CRUDController;

class UsersController extends CRUDController
{

    public function __construct()
    {
        $this->baseData = [
            'TABLE_NAME' => 'users',
            'MODEL' => User::class,
            'VIEW_FOLDER' => 'users',
            'MODULE_SLUG' => 'users',
            'DATATABLE_QUERY' => 'model',
            'MODAL_SIZE' => '750px',
            'MODULE_NAME' => __('Users'),
            'MODULE_ICON' => 'flaticon-customer',
            'BUTTON_ADD_NAME' => __('Add User'),
            'MODAL_HEAD_ADD' => __('Add New User'),
            'MODAL_HEAD_EDIT' => __('Edit User'),
            'MODAL_HEAD_SHOW' => __('Show User'),
            'TABLE_FIELDS' => [
                'name' => __('Name'),
                'email' => __('Email'),
                'phone' => __('Phone'),
                'created_at' => __('Created At'),
            ],
            'JOIN_TABLE_FIELDS' => [],
            'TABLE_RENDERS'    => [],
            'TABLE_FILTERS' => [
                'date_range' => true,
                'type' => 'Multiple', // Set To (Multiple), If You Want To Filter By Multiple Values
            ],
        ];
    }

    public function dynamicInputs()
    {
        $this->addInput([
            'label' => __('Name'),
            'name' => 'name',
            'width' => 'col-lg-6',
            'type' => 'text',
            'required' => true,
            'placeholder' => __('Enter Name'),
            'validation' => 'required',

        ]);

        $this->addInput([
            'label' => __('Email'),
            'name' => 'email',
            'width' => 'col-lg-6',
            'type' => 'email',
            'required' => true,
            'placeholder' => __('Enter Email'),
            'validation' => 'required|unique:users,email',
        ]);

        $this->addInput([
            'label' => __('Phone'),
            'name' => 'phone',
            'width' => 'col-lg-6',
            'type' => 'text',
            'placeholder' => __('Enter Phone'),
            'validation' => 'nullable|unique:users,phone',
            'class' => 'phone-input',
        ]);

        return $this->INPUTS;
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

        $data = $data->with(['subscriptionAddons' => function($query) {
            $query->where('is_paid', '0');
        }]);

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

    public function customRules(): array
    {
        return [

        ];
    }

    public function setBaseQueries()
    {

    }

    public function customInputs($user, $request)
    {
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        $user->branch_id = $request->branch_id;
        $user->register_id = $request->register_id;
        $user->pin_code = $request->pin_code;

        return $user;
    }

    public function store(Request $request)
    {
        if($this->checkCurrentSubscriptionIsTrailForStore())
        {
            return response()->json(['errors' => [__("Can't add user in your trial period")]], 422);
        }

        $this->authorize('create', $this->baseData('MODEL'));

        # Get Base Inputs
        $INPUTS = $this->baseData('DYNAMIC_INPUTS');

        # Valdiation Rules
        $rules = [
            'roles' => 'required',
        ];

        # Valdiation Messages
        $messages = [];

        $customRules = $this->customRules() ?? [];

        # Dinamic Inputs
        foreach ($INPUTS as $Input) {
            if (isset($Input->validation)) {
                $rules[$Input->name] = $Input->validation . (isset($customRules[$Input->name]) ? '|' . $customRules[$Input->name] : '');
                unset($customRules[$Input->name]);
            }
        }

        $rules = $rules + $customRules;

        # Return Validation
        $this->validate($request, $rules, $messages);

        # Check Email is existed in main database
        $tenant_id = tenant()->id;
        $email_check = tenancy()->central(function () use ($request, $tenant_id) {
            return user::where('email', $request->email)->where('tenant_id', '!=', $tenant_id)->first();
        });

        if ($email_check) {
            return response()->json(['errors' => [__('A user with this email address already exists')]], 422);
        }

        # Check Phone is existed in main database
        if ($request->phone) {
            $phone_check = tenancy()->central(function () use ($request, $tenant_id) {
                return user::where('phone', $request->phone)->where('tenant_id', '!=', $tenant_id)->first();
            });

            if ($phone_check) {
                return response()->json(['errors' => [__('A user with this phone number already exists')]], 422);
            }
        }

        try {

            # Save To Database
            $st = new ($this->baseData('MODEL'));

            # Dinamic inputs
            foreach ($INPUTS as $Input) {
                if (isset($Input->name) && isset($request->{$Input->name}) && !$Input->custom) {
                    if ($Input->type == 'file') {
                        $st->addMediaFromRequest($Input->name)->toMediaCollection($this->baseData('TABLE_NAME'));
                        continue;
                    }
                    $st->{$Input->name} = $request->{$Input->name};
                }
            }

            # Custom inputs
            $st = $this->customInputs($st, $request);

            $st->save();


            $this->afterSave($st, $request);

            return self::checkResponse(true, 'store');
        } catch (\Throwable $th) {
            $st->delete();

            session()->flash('error_message', __('Error Cannot Save Data') . ':' . $th->getMessage());
            return response()->json(['error' => 'invalid' . ':' . $th->getMessage()], 422);
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update', $this->baseData('MODEL'));

        # Get Base Inputs
        $INPUTS = $this->baseData('DYNAMIC_INPUTS');

        # Valdiation Rules
        $rules = [
            'roles' => 'required',
        ];

        # Valdiation Messages
        $messages = [];

        $customRules = $this->customRules();

        # Dinamic Inputs
        foreach ($INPUTS as $Input) {
            if (isset($Input->validation)) {
                $rules[$Input->name] = $Input->validation . (isset($customRules[$Input->name]) ? '|' . $customRules[$Input->name] : '');
                unset($customRules[$Input->name]);
            }
        }

        $rules = $rules + $customRules;

        # Special Rules Handling
        foreach ($rules as $key => $rule) {
            $ruleValidation = explode('|', $rule);
            $fieldType = collect($INPUTS)->where('name', $key)->first()?->type;
            foreach ($ruleValidation as &$inputRule) {
                if (str_contains($inputRule, 'unique')) {
                    $inputRule = explode(',', $inputRule);
                    $inputRule[2] = $id;
                    $inputRule = implode(',', $inputRule);
                    break;
                }

                if ($fieldType == 'file' && $inputRule == 'required') {
                    $inputRule = null;
                }
            }
            $rules[$key] = implode('|', array_filter($ruleValidation));
        }

        # Return Validation
        $this->validate($request, $rules, $messages);

        # Check Email is existed in main database
        $tenant_id = tenant()->id;

        $email_check = tenancy()->central(function () use ($request, $tenant_id) {
            return user::where('email', $request->email)->where('tenant_id', '!=', $tenant_id)->first();
        });

        if ($email_check) {
            return response()->json(['errors' => [__('A user with this email address already exists')]], 422);
        }

        # Check Phone is existed in main database
        if ($request->phone) {
            $phone_check = tenancy()->central(function () use ($request, $tenant_id) {
                return user::where('phone', $request->phone)->where('tenant_id', '!=', $tenant_id)->first();
            });

            if ($phone_check) {
                return response()->json(['errors' => [__('A user with this phone number already exists')]], 422);
            }
        }



        try {

            # Save To Database
            $st = $this->baseData('MODEL')::find($id);

            # Dinamic inputs
            foreach ($INPUTS as $Input) {

                if ($Input->type == 'select2-ajax' && !$Input->custom) {
                    $st->{$Input->name} = $request->{$Input->name};
                    continue;
                }

                if (isset($Input->name) && isset($request->{$Input->name}) && !$Input->custom) {
                    if ($Input->type == 'file') {
                        $st->clearMediaCollection();
                        if ($st->hasMedia($this->baseData('TABLE_NAME'))) {
                            $st->clearMediaCollection($this->baseData('TABLE_NAME'));
                        }
                        $st->addMediaFromRequest($Input->name)->toMediaCollection($this->baseData('TABLE_NAME'));
                        continue;
                    }

                    $st->{$Input->name} = $request->{$Input->name};
                }
            }
           if(isset($st->role)){
            $role = Role::whereIn('id',$request->roles)->first();

            $numUserIsSuperAdmin = User::whereHas(
             'roles', function($q){
                 $q->where('is_super_admin', 1);
             }
             )->count();

            if($st->role->is_super_admin == 0 || ($st->role->is_super_admin == 1 && $role->is_super_admin == 1)
             || ($role->is_super_admin == 0 && $numUserIsSuperAdmin > 1 ) )
            {
                 # Custom inputs
                 $st = $this->customInputs($st, $request);

                 $st->save();

                 $this->afterSave($st, $request);

                 return self::checkResponse(true, 'update');
            }
            else{
             session()->flash('error_message', __('Error Cannot Updated Data') . ': '. __('Must user one least has super admin permission'));

            }
           }
           else{
                # Custom inputs
                $st = $this->customInputs($st, $request);

                $st->save();

                $this->afterSave($st, $request);

                return self::checkResponse(true, 'update');
           }



        } catch (\Throwable $th) {

            session()->flash('error_message', __('Error Cannot Updated Data') . ':' . $th->getMessage());
            return response()->json(['error' => 'invalid' . ':' . $th->getMessage()], 422);
        }
    }

    public function afterSave($user, $request)
    {
        $user->syncRoles($request->roles);
        $user->shifts()->sync($request->shifts);

        # Custom Fields
        $this->saveCustomFieldsData($user, $request, 'User');

        # Addons Subscription
        if(!app('sharedData')->lastSubscription->is_free && !$this->checkSubscriptionLimit('User') && $request->method() != 'PUT')
        {
            $this->createSubscriptionAddons('User',$user);
        }
    }

    public function destroy($ids)
    {
        $this->authorize('delete', $this->baseData('MODEL'));

        $IdsToDelete = explode(',', $ids);

        $deleted = true;

        $models = $this->baseData('MODEL')::whereIn('id', $IdsToDelete)->get();

        $numOfSuberAdminForDelete = $models->filter(
            function($q){
                if($q->role->is_super_admin == 1){
                    return $q;
                }
            }
        )->count();

        $numUserIsSuperAdmin = User::whereHas(
            'roles', function($q){
                $q->where('is_super_admin', 1);
            }
        )->count();


        if($numOfSuberAdminForDelete < $numUserIsSuperAdmin){
            foreach ($models as $model) {
                if (auth()->id() == $model->id) {
                    $deleted = false;
                    continue;
                }

                try {

                    $model->delete();
                } catch (\Throwable $th) {

                    $deleted = false;
                }
            }
        }
        else{
          return self::checkResponse(__('Must be find user has super admin role at least one'));
        }

        # Return To Response.
        return $deleted ? self::checkResponse($deleted, 'delete') : self::checkResponse(__('Some of the selected Items are used somewhere in the system and can\'t be deleted'));
    }

    public function usersForSelect($roles = null)
    {
        $result = User::select('id', 'name as text')
        ->when($roles,function($q) use($roles){
            $q->whereHas('roles.permissions', function ($query) use($roles) {

                $roles = explode(',', $roles);
                $query->whereIn('name', $roles);
            });
        })
        ->when(auth()->user()->branch_id , function($query){
            $query->where('branch_id',auth()->user()->branch_id);
        });

        if (request()->q) {
            $result = $result->where('name', 'like', '%' . request()->q . '%');
        }

        return $result->orderBy('id')->cursorPaginate(10);
    }
}
