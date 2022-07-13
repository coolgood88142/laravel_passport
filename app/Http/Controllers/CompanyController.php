<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\CompanyPermission;
use App\Models\Product;
use App\User;
use App\Models\UserPermission;
use App\Http\Controllers\GoogleSheetsController;
use App\Http\Controllers\MailController;
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
        $errorMsg = '';

        if($queryCompany != '' && $queryCompanyPermission == 'Y'){
            $this->companyId = $queryCompany;
            $company = Company::where('id', $queryCompany)->first();
            $this->queryCompanyName = $company->name;
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
        }



        return view('editCompany', [
            'company' => Company::all(),
            'product' => Product::all(),
            'errorMsg' => $errorMsg,
            'companyId' => $this->companyId,
            'productId' => $this->productId,
            'amount' => $this->amount,
            'startDateTime' => $request->startDateTime,
            'endDateTime' => $request->endDateTime,
        ]);
    }

    public function queryCompany(Request $request){
        return view('queryCompany', [
            'company' => Company::all(),
        ]);
    }

    public function createCompanyData($queryCompany){
        $companyPermission = [];
        $userPermission = [];
        $productData = [];
        $userPermissionLog = [];
        $userPermissionLogGroup = [];

        if($queryCompany != ''){
            $companyPermissionData = DB::table('company_permission')
                ->join('company', 'company_permission.company_id' , '=' , 'company.id')
                ->join('product', 'company_permission.product_id' , '=' , 'product.id')
                ->select('company_permission.company_id', 'company.name as company_name',
                    'company_permission.product_id', 'product.name as product_name',
                    'company_permission.amount', 'company_permission.start_datetime',
                    'company_permission.end_datetime', 'company_permission.created_at',
                    'company_permission.updated_at'
                )
                ->where('company_id', $queryCompany)->get();

            // dd($companyPermissionData[0]->company_id);
            foreach($companyPermissionData as $data){
                $userData = User::select('id')->where('company_id', $data->company_id)->get();
                $startDatetime = $data->start_datetime;
                $endDatetime = $data->end_datetime;
                $userArray = [];
                foreach($userData as $user){
                    array_push($userArray, $user->id);
                }

                $permission = DB::table('user_permission')
                    ->join('users', 'user_permission.user_id' , '=' , 'users.id')
                    ->join('product', 'user_permission.product_id' , '=' , 'product.id')
                    ->select('user_permission.user_id', 'users.name as user_name',
                        'user_permission.product_id', 'product.name as product_name',
                        'user_permission.start_datetime', 'user_permission.end_datetime',
                        'user_permission.created_at', 'user_permission.updated_at'
                    )
                    ->where('product_id', $data->product_id)
                    ->where('start_datetime', $startDatetime)
                    ->where('end_datetime', $endDatetime)
                    ->whereIn('user_id', $userArray)
                    ->get();

                if($permission->count() > 0){
                    foreach($permission as $data){
                        $permissionDataArray = [
                            'user_id' => $data->user_id,
                            'user_name' => $data->user_name,
                            'product_id' => $data->product_id,
                            'product_name' => $data->product_name,
                            'start_datetime' => $data->start_datetime,
                            'end_datetime' => $data->end_datetime,
                            'created_at' => $data->created_at,
                            'updated_at' => $data->updated_at,
                        ];
                        array_push($userPermission, $permissionDataArray);
                    }
                }

                $permissionLog = DB::table('user_permission_log')
                    ->join('users', 'user_permission_log.user_id' , '=' , 'users.id')
                    ->join('product', 'user_permission_log.product_id' , '=' , 'product.id')
                    ->select('user_permission_log.user_id', 'users.name as user_name',
                        'user_permission_log.product_id', 'product.name as product_name',
                        'user_permission_log.start_datetime', 'user_permission_log.end_datetime',
                        'user_permission_log.created_at', 'user_permission_log.updated_at'
                    )
                    ->where('product_id', $data->product_id)
                    ->where('start_datetime', $startDatetime)
                    ->where('end_datetime', $endDatetime)
                    ->whereIn('user_id', $userArray)
                    ->orderBy('created_at')
                    ->get();

                if($permissionLog->count() > 0){
                    foreach($permissionLog as $data){
                        $permissionLogDataArray = [
                            'user_id' => $data->user_id,
                            'user_name' => $data->user_name,
                            'product_id' => $data->product_id,
                            'product_name' => $data->product_name,
                            'start_datetime' => $data->start_datetime,
                            'end_datetime' => $data->end_datetime,
                            'created_at' => $data->created_at,
                            'updated_at' => $data->updated_at,
                        ];
                        array_push($userPermissionLog, $permissionLogDataArray);

                        // $userPermissionLogGroupArray = [
                        //     $data->created_at,
                        // ];
                        array_push($userPermissionLogGroup, $data->created_at);
                    }
                }
            }
        }

        $userPermissionLogGroupArray = array_unique($userPermissionLogGroup);

        return [
            'companyPermission' => $companyPermissionData,
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
