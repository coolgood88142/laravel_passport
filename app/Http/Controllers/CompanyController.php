<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\CompanyPermission;
use App\Models\CompanyPermissionLog;
use App\Models\Product;
use App\User;
use App\Models\UserPermission;
use App\Models\UserPermissionLog;
use App\Http\Controllers\GoogleSheetsController;
use App\Http\Controllers\MailController;
use Carbon\Carbon;
use DB;
class CompanyController extends Controller
{
    protected $product;
    protected $permission;
    protected $company;
    protected $companyPermission;
    protected $companyPermissionGroup;
    protected $permissionAmount;

    public function __construct()
    {
        $this->queryCompanyId = '';
        $this->queryCompanyName = '';
        $this->companyPermission = [];
        $this->companyPermissionGroup = [];
        $this->companyId = '';
        $this->productId = '';
        $this->amount = '';
        $this->company = [];
        $this->permissionAmount = [];
    }

    public function editCompany(Request $request){
        $queryCompany = $request->queryCompany == null ? '' : $request->queryCompany;
        $queryCompanyPermission = $request->queryCompanyPermission == null ? '' : $request->queryCompanyPermission;
        $addCompanyPermission = $request->addCompanyPermission == null ? '' : $request->addCompanyPermission;
        $updateCompanyPermission = $request->updateCompanyPermission == null ? '' : $request->updateCompanyPermission;
        $deleteCompanyPermission = $request->deleteCompanyPermission == null ? '' : $request->deleteCompanyPermission;
        $deleteExpiredData = $request->deleteExpiredData == null ? '' : $request->deleteExpiredData;
        $now = Carbon::now('Asia/Taipei')->toDateTimeString();
        $this->productId = $request->productId == null ? '' : $request->productId;
        $errorMsg = '';

        if($queryCompany != '' && $queryCompanyPermission == 'Y'){
            $this->companyId = $queryCompany;
            $company = Company::where('id', $queryCompany)->first();
            $this->queryCompanyName = $company->name;
            $companyPermission = CompanyPermission::where('company_id', $this->companyId)->get();

            foreach($companyPermission as $data){
                $company = Company::where('id', $data->company_id)->first();
                $product = Product::where('id', $data->product_id)->first();
                $userData = User::select('id')->where('company_id', $data->company_id)->get();
                $startDatetime = $data->start_datetime;
                $endDatetime = $data->end_datetime;

                $companyPermission = [
                    'company_permission_id' => $data->id,
                    'company_id' =>  $company->id,
                    'company_name' =>  $company->name,
                    'product_id' =>  $product->id,
                    'product_name' =>  $product->name,
                    'amount' =>  $data->amount,
                    'start_datetime' =>  $startDatetime,
                    'end_datetime' =>  $endDatetime,
                ];

                array_push($this->permissionAmount, $companyPermission);
            }
        }else if($queryCompany != '' && $addCompanyPermission == 'Y'){
            $this->companyId = $request->companyId == null ? '' : $request->companyId;
            $this->productId = $request->productId == null ? '' : $request->productId;
            $this->amount = $request->amount == null ? '' : $request->amount;
            $startDateTime = $request->startDateTime == null ? '' : strtotime($request->startDateTime);
            $endDateTime = $request->endDateTime == null ? '' : strtotime($request->endDateTime);

            if($this->amount > 0){
                $companyPermission = CompanyPermission::where('company_id', $this->companyId)
                                ->where('product_id', $this->productId)
                                ->first();
                if($companyPermission->amount > $this->amount){
                    $productName = Product::where('id', $this->productId)->first();
                    $errorMsg = $productName->name . '數量不能小於' . $companyPermission->amount . ',';
                }else if($startDateTime > $endDateTime){
                    $errorMsg = $errorMsg . '使用時段的起始時間不能大於截止時間' . ',';
                }else if($startDateTime == $endDateTime){
                    $errorMsg = $errorMsg . '使用時段的起始時間不能等於截止時間' . ',';
                }

                if($errorMsg == ''){
                    $amount = $this->amount;
                    $companyId = $this->companyId;
                    $productId = $this->productId;

                    $data = new CompanyPermission();
                    $data->company_id = $companyId;
                    $data->product_id = $productId;
                    $data->amount = $amount;
                    $data->start_datetime = $request->startDateTime;
                    $data->end_datetime = $request->endDateTime;
                    $data->save();

                    return redirect(route('queryPermissionAmount'));
                }else{
                    $errorMsg = substr($errorMsg, 0, -1);
                }
            }
        }else if($queryCompany != '' && $updateCompanyPermission == 'Y'){
            $editCompanyPermissionId = $request->editCompanyPermissionId == null ? '' : $request->editCompanyPermissionId;
            $this->companyId = $request->companyId == null ? '' : $request->companyId;
            $this->productId = $request->productId == null ? '' : $request->productId;
            $this->amount = $request->amount == null ? '' : $request->amount;
            $startDateTime = $request->startDateTime == null ? '' : $request->startDateTime;
            $endDateTime = $request->endDateTime == null ? '' : $request->endDateTime;


            if($editCompanyPermissionId != ''){
                $data = CompanyPermission::where('id', '=', $editCompanyPermissionId)->first();
                $data->company_id = $this->companyId;
                $data->product_id = $this->productId;
                $data->amount = $this->amount;
                $data->start_datetime = $startDateTime;
                $data->end_datetime = $endDateTime;
                $data->save();
            }

            $company = Company::where('id', $this->companyId)->first();
            $this->queryCompanyName = $company->name;
            $companyPermission = CompanyPermission::where('company_id', $this->companyId)->get();

            foreach($companyPermission as $data){
                $company = Company::where('id', $data->company_id)->first();
                $product = Product::where('id', $data->product_id)->first();
                $userData = User::select('id')->where('company_id', $data->company_id)->get();
                $startDatetime = $data->start_datetime;
                $endDatetime = $data->end_datetime;

                $companyPermission = [
                    'company_permission_id' => $data->id,
                    'company_id' =>  $company->id,
                    'company_name' =>  $company->name,
                    'product_id' =>  $product->id,
                    'product_name' =>  $product->name,
                    'amount' =>  $data->amount,
                    'start_datetime' =>  $startDatetime,
                    'end_datetime' =>  $endDatetime,
                ];

                array_push($this->permissionAmount, $companyPermission);
            }

            $this->productId = '';
            $this->amount = '';
            $request->startDateTime = '';
            $request->endDateTime = '';
        }else if($queryCompany != '' && $deleteCompanyPermission == 'Y'){
            $editCompanyPermissionId = $request->editCompanyPermissionId == null ? '' : $request->editCompanyPermissionId;
            $this->companyId = $request->companyId == null ? '' : $request->companyId;
            $this->productId = $request->productId == null ? '' : $request->productId;
            $this->amount = $request->amount == null ? '' : $request->amount;
            $startDateTime = $request->startDateTime == null ? '' : $request->startDateTime;
            $endDateTime = $request->endDateTime == null ? '' : $request->endDateTime;


            if($editCompanyPermissionId != ''){
                $data = CompanyPermission::where('id', '=', $editCompanyPermissionId)->first();
                $data->delete();

                $companyPermission = new CompanyPermissionLog();
                $companyPermission->company_id = $this->companyId;
                $companyPermission->product_id = $this->productId;
                $companyPermission->amount = $this->amount;
                $companyPermission->start_datetime = $startDateTime;
                $companyPermission->end_datetime = $endDateTime;
                $companyPermission->save();
            }

            $company = Company::where('id', $this->companyId)->first();
            $this->queryCompanyName = $company->name;
            $companyPermission = CompanyPermission::where('company_id', $this->companyId)->get();

            foreach($companyPermission as $data){
                $company = Company::where('id', $data->company_id)->first();
                $product = Product::where('id', $data->product_id)->first();
                $userData = User::select('id')->where('company_id', $data->company_id)->get();
                $startDatetime = $data->start_datetime;
                $endDatetime = $data->end_datetime;

                $companyPermission = [
                    'company_permission_id' => $data->id,
                    'company_id' =>  $company->id,
                    'company_name' =>  $company->name,
                    'product_id' =>  $product->id,
                    'product_name' =>  $product->name,
                    'amount' =>  $data->amount,
                    'start_datetime' =>  $startDatetime,
                    'end_datetime' =>  $endDatetime,
                ];

                array_push($this->permissionAmount, $companyPermission);
            }

            $this->productId = '';
            $this->amount = '';
            $request->startDateTime = '';
            $request->endDateTime = '';
        }else if($queryCompany != '' && $deleteExpiredData == 'Y' && $this->productId != ''){
            $companyId = $request->companyId == null ? '' : $request->companyId;
            $companyPermissionData = CompanyPermission::where('company_id', $companyId)
                                    ->where('product_id', '=', $this->productId)
                                    ->where('end_datetime', '<', $now)
                                    ->get();

            if($companyPermissionData->count() > 0){
                foreach($companyPermissionData as $companyPermission){
                    $this->deleteCompanyPermission($companyPermission->id);
                    $this->saveCompanyPermissionLog($companyPermission);
                }

                $userPermissionData = DB::table('users')
                    ->join('user_permission', 'users.id' , '=' , 'user_permission.user_id')
                    ->join('company', 'users.company_id', '=', 'company.id')
                    ->select('user_permission.id', 'user_permission.user_id',
                        'user_permission.product_id', 'user_permission.start_datetime',
                        'user_permission.end_datetime', 'user_permission.created_at',
                        'user_permission.updated_at'
                    )
                    ->where('user_permission.end_datetime', '<', $now)
                    ->where('company.id', '=', $companyId)
                    ->get();

                if($userPermissionData->count() > 0){
                    foreach($userPermissionData as $userPermission){
                        $this->deleteUserPermission($userPermission->id);
                        $this->saveUserPermissionLog($userPermission);
                    }

                }
            }

            $editCompanyPermissionId = $request->editCompanyPermissionId == null ? '' : $request->editCompanyPermissionId;
            $this->companyId = $request->companyId == null ? '' : $request->companyId;
            $this->productId = $request->productId == null ? '' : $request->productId;
            $this->amount = $request->amount == null ? '' : $request->amount;
            $startDateTime = $request->startDateTime == null ? '' : $request->startDateTime;
            $endDateTime = $request->endDateTime == null ? '' : $request->endDateTime;


            if($editCompanyPermissionId != ''){
                $data = CompanyPermission::where('id', '=', $editCompanyPermissionId)->first();
                $data->company_id = $this->companyId;
                $data->product_id = $this->productId;
                $data->amount = $this->amount;
                $data->start_datetime = $startDateTime;
                $data->end_datetime = $endDateTime;
                $data->save();
            }

            $company = Company::where('id', $this->companyId)->first();
            $this->queryCompanyName = $company->name;
            $companyPermission = CompanyPermission::where('company_id', $this->companyId)->get();

            foreach($companyPermission as $data){
                $company = Company::where('id', $data->company_id)->first();
                $product = Product::where('id', $data->product_id)->first();
                $userData = User::select('id')->where('company_id', $data->company_id)->get();
                $startDatetime = $data->start_datetime;
                $endDatetime = $data->end_datetime;

                $companyPermission = [
                    'company_permission_id' => $data->id,
                    'company_id' =>  $company->id,
                    'company_name' =>  $company->name,
                    'product_id' =>  $product->id,
                    'product_name' =>  $product->name,
                    'amount' =>  $data->amount,
                    'start_datetime' =>  $startDatetime,
                    'end_datetime' =>  $endDatetime,
                ];

                array_push($this->permissionAmount, $companyPermission);
            }

            $this->productId = '';
            $this->amount = '';
            $request->startDateTime = '';
            $request->endDateTime = '';
        }

        return view('editCompany', [
            'company' => Company::all(),
            'product' => Product::all(),
            'permissionAmount' => $this->permissionAmount,
            'errorMsg' => $errorMsg,
            'companyId' => $this->companyId,
            'productId' => $this->productId,
            'amount' => $this->amount,
            'startDateTime' => $request->startDateTime,
            'endDateTime' => $request->endDateTime,
        ]);
    }

    public function deleteCompanyPermission($id){
        CompanyPermission::where('id', '=', $id)->delete();
    }

    public function saveCompanyPermissionLog($companyPermission){
        $companyPermissionLog = new CompanyPermissionLog();
        $companyPermissionLog->company_id = $companyPermission->company_id;
        $companyPermissionLog->product_id = $companyPermission->product_id;
        $companyPermissionLog->amount = $companyPermission->amount;
        $companyPermissionLog->start_datetime = $companyPermission->start_datetime;
        $companyPermissionLog->end_datetime = $companyPermission->end_datetime;
        $companyPermissionLog->save();
    }

    public function deleteUserPermission($id){
        UserPermission::where('id', '=', $id)->delete();
    }

    public function saveUserPermissionLog($userPermission){
        $userPermissionLog = new UserPermissionLog();
        $userPermissionLog->user_id = $userPermission->user_id;
        $userPermissionLog->product_id = $userPermission->product_id;
        $userPermissionLog->start_datetime = $userPermission->start_datetime;
        $userPermissionLog->end_datetime = $userPermission->end_datetime;
        $userPermissionLog->save();
    }

    public function queryCompany(Request $request){
        return view('queryCompany', [
            'company' => Company::all(),
        ]);
    }

    public function createCompanyData($queryCompany){
        $companyPermission = [];
        $companyPermissionLog = [];
        $userPermission = [];
        $productData = [];
        $userPermissionLog = [];
        $userPermissionLogGroup = [];

        if($queryCompany != ''){
            $companyPermissionList = DB::table('company_permission')
                ->join('company', 'company_permission.company_id' , '=' , 'company.id')
                ->join('product', 'company_permission.product_id' , '=' , 'product.id')
                ->select('company_permission.company_id', 'company.name as company_name',
                    'company_permission.product_id', 'product.name as product_name',
                )
                ->where('company_id', $queryCompany)->distinct()->get();

            foreach($companyPermissionList as $data){
                $userData = User::select('id')->where('company_id', $data->company_id)->get();
                $userArray = [];
                $datetimeArray = [];
                foreach($userData as $user){
                    array_push($userArray, $user->id);
                }
                $userIdData = join(',', $userArray);


                $companyPermissionData = DB::table('company_permission')
                            ->where('company_id', $data->company_id)
                            ->where('product_id', $data->product_id)
                            ->get();

                foreach($companyPermissionData as $permission){
                    $datetime = $permission->start_datetime . ' ~ ' . $permission->end_datetime;
                    array_push($datetimeArray, $datetime);
                }

                $datetimeData = implode('
                ', $datetimeArray);

                $amount = CompanyPermission::where('company_id', $data->company_id)
                        ->where('product_id', $data->product_id)
                        ->sum('amount');

                $companyPermissionArray = [
                    'company_id' => $data->company_id,
                    'company_name' => $data->company_name,
                    'company_name' => $data->company_name,
                    'product_id' => $data->product_id,
                    'product_name' => $data->product_name,
                    'amount' => $amount,
                    'date_time' => $datetimeData,
                    'users_id' => $userIdData,
                    'created_at' => $companyPermissionData->last()->created_at,
                    'updated_at' => $companyPermissionData->last()->updated_at,
                ];

                array_push($companyPermission, $companyPermissionArray);

                $permission = DB::table('user_permission')
                    ->join('users', 'user_permission.user_id' , '=' , 'users.id')
                    ->join('product', 'user_permission.product_id' , '=' , 'product.id')
                    ->select('user_permission.user_id', 'users.name as user_name',
                        'user_permission.product_id', 'product.name as product_name',
                        // 'user_permission.start_datetime', 'user_permission.end_datetime',
                        // 'user_permission.created_at', 'user_permission.updated_at'
                    )
                    ->where('product_id', $data->product_id)
                    // ->whereIn('user_id', $userArray)
                    ->distinct()
                    ->get();

                if($permission->count() > 0){
                    foreach($permission as $data){
                        $userPermision = DB::table('user_permission')
                            ->where('product_id', $data->product_id)
                            ->where('user_id', $data->user_id)
                            ->get();

                        // dd($userPermision);
                        $datetimeArray = [];
                        foreach($userPermision as $permission){
                            $datetime = $permission->start_datetime . ' ~ ' . $permission->end_datetime;
                            array_push($datetimeArray, $datetime);
                        }

                        $datetimeData = implode('
', $datetimeArray);

                        $permissionDataArray = [
                            'user_id' => $data->user_id,
                            'user_name' => $data->user_name,
                            'product_id' => $data->product_id,
                            'product_name' => $data->product_name,
                            'date_time' => $datetimeData,
                            'created_at' => $userPermision->last()->created_at,
                            'updated_at' => $userPermision->last()->updated_at,
                        ];
                        array_push($userPermission, $permissionDataArray);
                    }
                }

                $permissionLog = DB::table('user_permission_log')
                    ->join('users', 'user_permission_log.user_id' , '=' , 'users.id')
                    ->join('product', 'user_permission_log.product_id' , '=' , 'product.id')
                    ->select('user_permission_log.user_id', 'users.name as user_name',
                        'user_permission_log.product_id', 'product.name as product_name',
                    )
                    ->where('product_id', $data->product_id)
                    ->distinct()
                    ->get();

                if($permissionLog->count() > 0){
                    foreach($permissionLog as $data){
                        $userPermisionLog = DB::table('user_permission_log')
                            ->where('product_id', $data->product_id)
                            ->where('user_id', $data->user_id)
                            ->get();

                        $datetimeArray = [];
                        foreach($userPermisionLog as $permission){
                            $datetime = $permission->start_datetime . ' ~ ' . $permission->end_datetime;
                            array_push($datetimeArray, $datetime);
                        }

                        $datetimeData = implode('
                        ', $datetimeArray);

                        $permissionDataLogArray = [
                            'user_id' => $data->user_id,
                            'user_name' => $data->user_name,
                            'product_id' => $data->product_id,
                            'product_name' => $data->product_name,
                            'date_time' => $datetimeData,
                            'created_at' => $userPermisionLog->last()->created_at,
                            'updated_at' => $userPermisionLog->last()->updated_at,
                        ];
                        array_push($userPermissionLog, $permissionDataLogArray);
                        array_push($userPermissionLogGroup, $userPermisionLog->last()->created_at);
                    }
                }
            }

            $companyPermissionLogList = DB::table('company_permission_log')
                ->join('company', 'company_permission_log.company_id' , '=' , 'company.id')
                ->join('product', 'company_permission_log.product_id' , '=' , 'product.id')
                ->select('company_permission_log.company_id', 'company.name as company_name',
                    'company_permission_log.product_id', 'product.name as product_name',
                )
                ->where('company_id', $queryCompany)->distinct()->get();

            foreach($companyPermissionLogList as $data){
                $userData = User::select('id')->where('company_id', $data->company_id)->get();
                $userArray = [];
                $datetimeArray = [];
                foreach($userData as $user){
                    array_push($userArray, $user->id);
                }
                $userIdData = join(',', $userArray);


                $companyPermissionLogData = DB::table('company_permission_log')
                            ->where('company_id', $data->company_id)
                            ->where('product_id', $data->product_id)
                            ->get();

                foreach($companyPermissionLogData as $permission){
                    $datetime = $permission->start_datetime . ' ~ ' . $permission->end_datetime;
                    array_push($datetimeArray, $datetime);
                }

                $datetimeData = implode('
                ', $datetimeArray);

                $amount = CompanyPermissionLog::where('company_id', $data->company_id)
                        ->where('product_id', $data->product_id)
                        ->sum('amount');

                $companyPermissionLogArray = [
                    'company_id' => $data->company_id,
                    'company_name' => $data->company_name,
                    'company_name' => $data->company_name,
                    'product_id' => $data->product_id,
                    'product_name' => $data->product_name,
                    'amount' => $amount,
                    'date_time' => $datetimeData,
                    'users_id' => $userIdData,
                    'created_at' => $companyPermissionLogData->last()->created_at,
                    'updated_at' => $companyPermissionLogData->last()->updated_at,
                ];

                array_push($companyPermissionLog, $companyPermissionLogArray);
            }
        }

        $userPermissionLogGroupArray = array_unique($userPermissionLogGroup);

        return [
            'companyPermission' => $companyPermission,
            'companyPermissionLog' => $companyPermissionLog,
            'userPermission' => $userPermission,
            'userPermissionLog' => $userPermissionLog,
            'userPermissionLogGroup' => $userPermissionLogGroupArray
        ];
    }

    public function sendCompanyData(Request $request){
        if($request->sendEmail == 'Y'){
            (new MailController())->sendEmailCompanyData($request);
        }else if($request->sendSpreadsheets == 'Y'){
            (new GoogleSheetsController())->sendSpreadsheetsCompanyData($request);
        }else if($request->get('sendAppScript') == 'Y'){
            return $this->createCompanyData($request->get('queryCompany'));
        }else if($request->sendEmailWithAttach == 'Y'){
            (new MailController())->sendEmailWithAttach($request);
        }
    }
}
