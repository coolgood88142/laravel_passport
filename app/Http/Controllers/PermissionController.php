<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanyPermission;
use App\Models\Product;
use App\User;
use App\Models\UserPermission;
use App\Models\UserPermissionLog;
use App\Presenters\UserPermissionPresenter;
use Carbon\Carbon;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class PermissionController extends Controller
{
    protected $product;
    protected $permission;
    protected $companyPermission;
    protected $companyPermissionGroup;
    public function __construct()
    {
        $this->product = [];
        $this->permission = [];
        $this->companyPermission = [];
        $this->companyPermissionGroup = [];
        $this->showText = '';

        foreach(Product::all() as $data){
            $this->product[$data->id]['id'] = $data->id;
            $this->product[$data->id]['name'] = $data->name;
        }

        if(Auth::check()){
            $userId = Auth::user()->id;
            $companyId = Auth::user()->company_id;
            $this->permission = UserPermission::where('user_id', $userId)->orderBy('product_id', 'asc')->get();

            foreach($this->product as $id => $value){
                $companyPermissionData = CompanyPermission::where('company_id', $companyId)
                    ->where('product_id', $id)->get();

                if($companyPermissionData->count() > 0){
                    $this->companyPermission[$id] = $companyPermissionData;
                    $this->companyPermissionGroup[$id] = $companyPermissionData->first();
                }
            }
        }
    }

    public function getDefaultData(){
        foreach(Product::all() as $data){
            $this->product[$data->id]['id'] = $data->id;
            $this->product[$data->id]['name'] = $data->name;
        }

        if(Auth::check()){
            $userId = Auth::user()->id;
            $companyId = Auth::user()->company_id;
            $this->permission = UserPermission::where('user_id', $userId)->orderBy('product_id', 'asc')->get();

            foreach($this->product as $id => $value){
                $companyPermissionData = CompanyPermission::where('company_id', $companyId)
                    ->where('product_id', $id)->get();

                if($companyPermissionData->count() > 0){
                    $this->companyPermission[$id] = $companyPermissionData;
                    $this->companyPermissionGroup[$id] = $companyPermissionData->first();

                    foreach($companyPermissionData as $companyPermission){
                        $startDatetime = $companyPermission->start_datetime;
                        $endDatetime = $companyPermission->end_datetime;
                        $user = User::select('id')->where('company_id', $companyId)->get()->modelKeys();

                        $userPermission = UserPermission::where('product_id', $companyPermission->product_id)
                                ->where('start_datetime', $startDatetime)
                                ->where('end_datetime', $endDatetime)
                                ->whereIn('user_id', $user)
                                ->get();

                        $userPermissionCount = $userPermission->count();
                        $now = Carbon::now('Asia/Taipei')->toDateTimeString();

                        //沒有分配給學員可能是新的公司權益資料要做搬檔案
                        if($userPermissionCount == 0){
                            $UserPermissionPresenter = new UserPermissionPresenter();
                            $matchNowTime = $UserPermissionPresenter->matchNowTime($startDatetime, $endDatetime);

                            //如果沒有分配的公司權益資料，今天時間有在產品時段的範圍內，代表要做搬移
                            if($matchNowTime){
                                //拿目前user最新學員權益資料的產品
                                $userPermission = UserPermission::where('product_id', $id)->whereIn('user_id', $user)->orderBy('id', 'desc')->first();
                                $oldStartDatetime = Carbon::parse($userPermission->start_datetime);
                                $oldEndDatetime = Carbon::parse($userPermission->end_datetime);
                                $newStartDatetime = Carbon::parse($startDatetime);
                                $newEndDatetime = Carbon::parse($endDatetime);

                                //判斷有大於產品時段的起始時間和截止時間，有的話要分配的學員權益資料
                                if($newStartDatetime->gt($oldStartDatetime) && $newEndDatetime->gt($oldEndDatetime)){
                                    $oldUserPermissionData =  UserPermission::where('product_id', $id)
                                        ->where('start_datetime', $oldStartDatetime)
                                        ->where('end_datetime', $oldEndDatetime)
                                        ->get();

                                        foreach($oldUserPermissionData as $old){
                                            $data = UserPermission::where('user_id', $old->user_id)
                                                ->where('product_id', $id)
                                                ->where('start_datetime', $startDatetime)
                                                ->where('end_datetime', $endDatetime)
                                                ->first();

                                            if($data == null){
                                                $newUserPermission = new UserPermission();
                                                $newUserPermission->user_id = $old->user_id;
                                                $newUserPermission->product_id = $id;
                                                $newUserPermission->start_datetime = $startDatetime;
                                                $newUserPermission->end_datetime = $endDatetime;
                                                $newUserPermission->save();
                                            }
                                        }
                                }
                            }
                        }
                    }
                }
            }
        }else{
            $this->showText = '請先登入後再選擇權益!';
        }

        return view('permission', [
            'product' => $this->product,
            'user_permission' => $this->permission,
            'company_permission' => $this->companyPermission,
            'company_permission_group' => $this->companyPermissionGroup,
            'show_text' => $this->showText,
            'has_login' => Auth::check()
        ]);
    }

    public function savePermission(Request $request){
        $permission = [];
        $nowPermission = [];
        $showText = '';
        $nowProduct = $request->product!= null ? $request->product : [];
        if(Auth::check()){
            $userId = Auth::user()->id;
            $companyId = Auth::user()->company_id;
            $permission = UserPermission::where('user_id', $userId);
            $count = $permission->count();

            $del = [];
            foreach($this->product as $index => $data){
                $companyPermission = CompanyPermission::where('company_id', $companyId)
                    ->where('product_id', $data['id'])->get();

                if($companyPermission->count() > 0){
                    foreach($companyPermission as $value){
                        $permissionData = UserPermission::where('user_id', $userId)
                            ->where('product_id', $value->product_id)
                            ->where('start_datetime', $value->start_datetime)
                            ->where('end_datetime', $value->end_datetime)
                            ->first();
                        if($count > 0 || $permissionData == null){
                            if($permissionData != null && $nowProduct == []){
                                $this->deleteUserPermission($permissionData->id);
                                $showText = $this->saveUserPermissionLog($userId, $value, $permissionData, $showText);
                            }else if($permissionData != null && !in_array($value->id, $nowProduct)){
                                $this->deleteUserPermission($permissionData->id);
                                $showText = $this->saveUserPermissionLog($userId, $value, $permissionData, $showText);
                            }else if($permissionData == null && in_array($value->id, $nowProduct)){
                                $showText = $this->saveUserPermission($userId, $value, $showText);
                            }
                        }else if(in_array($value->id, $nowProduct)){
                            $showText = $this->saveUserPermission($userId, $value, $showText);
                        }
                    }
                }
            }

            if($showText != ''){
                $showText = substr($showText, 0, -1);
                $permission = UserPermission::where('user_id', $userId)->orderBy('product_id', 'asc')->get();
            }
        }else if(!Auth::check() && $nowProduct == null){
            $showText = '請選擇權益!';
        }else if(!Auth::check()){
            $showText = '請先登入後再選擇權益!';
        }

        $productData = [];
        if(Auth::check()){
            $userId = Auth::user()->id;
            $permission = UserPermission::where('user_id', $userId)->orderBy('product_id', 'asc')->get();
        }

        foreach(Product::all() as $data){
            $this->product[$data->id]['id'] = $data->id;
            $this->product[$data->id]['name'] = $data->name;
        }

        if(Auth::check()){
            $userId = Auth::user()->id;
            $companyId = Auth::user()->company_id;
            $this->permission = UserPermission::where('user_id', $userId)->orderBy('product_id', 'asc')->get();

            foreach($this->product as $id => $value){
                $companyPermissionData = CompanyPermission::where('company_id', $companyId)
                    ->where('product_id', $id)->get();

                if($companyPermissionData->count() > 0){
                    $this->companyPermission[$id] = $companyPermissionData;
                    $this->companyPermissionGroup[$id] = $companyPermissionData->first();
                }
            }
        }

        // dd([
        //     'product' => $this->product,
        //     'user_permission' => $this->permission,
        //     'company_permission' => $this->companyPermission,
        //     'company_permission_group' => $this->companyPermissionGroup,
        //     'show_text' => $showText,
        //     'has_login' => Auth::check()
        // ]);

        return view('permission', [
            'product' => $this->product,
            'user_permission' => $this->permission,
            'company_permission' => $this->companyPermission,
            'company_permission_group' => $this->companyPermissionGroup,
            'show_text' => $showText,
            'has_login' => Auth::check()
        ]);

    }

    public function saveUserPermission($userId, $value, $showText){
        $UserPermission = new UserPermission();
        $UserPermission->user_id = $userId;
        $UserPermission->product_id = $value->product_id;
        $UserPermission->start_datetime = $value->start_datetime;
        $UserPermission->end_datetime = $value->end_datetime;
        $UserPermission->save();
        $showText = $showText . '新增' . $this->product[$value->product_id]['name'] . '(' . $value->start_datetime . '-' . $value->end_datetime . ')' . ',';
        return $showText;
    }

    public function saveUserPermissionLog($userId, $value, $permissionData, $showText){
        $UserPermission = new UserPermissionLog();
        $UserPermission->user_id = $userId;
        $UserPermission->product_id = $value->product_id;
        $UserPermission->start_datetime = $permissionData->start_datetime;
        $UserPermission->end_datetime = $permissionData->end_datetime;
        $UserPermission->save();
        $showText = $showText . '移除' . $this->product[$value->product_id]['name'] . '(' . $permissionData->start_datetime . '-' . $permissionData->end_datetime . ')' . ',';
        return $showText;
    }

    public function deleteUserPermission($id){
        UserPermission::where('id', $id)->delete();
    }

    public function showUserPermissionBlade(){
        $productData = [];
        if(Auth::check()){
            $userId = Auth::user()->id;
            $permission = UserPermission::where('user_id', $userId)->orderBy('product_id', 'asc')->get();
        }else{
            return '請先登入後再選擇權益!';
        }

        // $UserPermissionPresenter = new UserPermissionPresenter();

        // $data = $UserPermissionPresenter->matchProductId($permission, $this->product);


        // dd($UserPermissionPresenter->checkHeader($data, 'A'));

        // @if($presenter->checkHeader($presenter->matchProductId($permission, $product), 'A')

        return view('mainPermission', ['product' => $this->product, 'permission' => $permission]);

    }

    public function getUserPermission(Request $request){
        $length = $request->input('length');
        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValue = $request->input('search');
        $pageValue = $request->input('page');

        $dataArray = [];
        $meta = [
            'from' => 1,
            'to' => 10,
            'total' => 10,
        ];
        $links = [
            'prev' => false,
            'next' => false,
        ];

        if($pageValue == 1){
            $meta['from'] = $pageValue;
            $meta['to'] = $length;
        }else{
            $meta['from'] = ($pageValue-1) * $length + 1;
            $meta['to'] = ($pageValue+1) * $length;
            $links['prev'] = true;
            $links['next'] = true;
        }

        $companyId = $request->companyId == null ? '' : $request->companyId;
        $productId = $request->productId == null ? '' : $request->productId;
        $startDatetime = $request->startDatetime == null ? '' : $request->startDatetime;
        $endDatetime = $request->endDatetime == null ? '' : $request->endDatetime;


        // $userName = $request->user_name == null ? '' : $request->user_name;
        $userData = User::where('company_id', '=', $companyId)->get();

        $userArray = [];
        foreach($userData as $user){
            array_push($userArray, $user->id);
        }

        $userPermission = UserPermission::where('product_id', $productId)
                            ->where('start_datetime', $startDatetime)
                            ->where('end_datetime', $endDatetime)
                            ->whereIn('user_id', $userArray)
                            ->get();

        $dataArray = [];
        if($userPermission != null){
            foreach($userPermission as $permission){
                $array = [];
                $userName = User::where('id', $permission->user_id)->first()->name;
                $array['id'] = $permission->user_id;
                $array['name'] = $userName;
                $array['createDate'] = $permission->created_at->toDateTimeString();

                array_push($dataArray, (object)$array);
            }
        }

        $collection = collect($dataArray);

        if($searchValue != ''){
            $searchArray = [];
            foreach($dataArray as $data){
                if($data->id == $searchValue || $data->name == $searchValue || $data->createDate == $searchValue){
                    array_push($searchArray, $data);
                }
            }

            if(count($searchArray) > 0){
                $collection = collect($searchArray);
            }else{
                return [
                    'data' => [],
                    'meta' => [
                        'from' => 0,
                        'to' => 0,
                        'total' => 0,
                    ],
                    'links' => [
                        'prev' => false,
                        'next' => false,
                    ]
                ];
            }
        }

        $meta['total'] = $collection->count();
        if($pageValue == 1 && $meta['total'] > $length){
            $links['next'] = true;
        }

        if($sortBy != '' && $orderBy != ''){
            if($orderBy == 'asc'){
                $collection = $collection->sortBy($sortBy);
            }else if($orderBy == 'desc'){
                $collection = $collection->sortByDesc($sortBy);
            }
        }

        if($length != ''){
            $collection = $collection->forPage($pageValue, $length);
        }

        if($meta['to'] > $meta['total'] ){
            $meta['to'] = ($meta['from'] + $collection->count()) - 1;
            $links['next'] = false;
        }

        return [
                'data' => $collection->values()->all(),
                'meta' => $meta,
                'links' => $links
            ];
    }

    public function getUserPermissionDeatils(Request $request){
        $queryUserId = $request->queryUserId == null ? '' : $request->queryUserId;
        $userPermissionData = [];

        if($queryUserId != ''){
            $userData = User::where('id', '=', $queryUserId)->first();
            $userPermission = UserPermission::where('user_id', $userData->id)->get();
        }else{
            $userPermission = UserPermission::all();
        }

        foreach($userPermission as $permission){
            $user = User::where('id', '=', $permission->user_id)->first();
            $data = [
                'user_id' => $permission->user_id,
                'user_name' => $user->name,
                'product_name' => $this->product[$permission->product_id]['name'],
                'start_datetime' => $permission->start_datetime,
                'end_datetime' => $permission->end_datetime,
                'created_at' => $permission->created_at,
            ];

            array_push($userPermissionData, $data);
        }

        return view('userPermission', [
            'queryUserId' => $queryUserId,
            'userPermission' => $userPermissionData,
        ]);
    }

    public function editCompanyPermission(Request $request){

        $editCompanyPermissionId = $request->editCompanyPermissionId  == null ? '' : $request->editCompanyPermissionId;
        $companyPermission = CompanyPermission::where('id', '=', $editCompanyPermissionId)->first();

        return [
            'companyId' => $companyPermission->company_id,
            'productId' => $companyPermission->product_id,
            'amount' => $companyPermission->amount,
            'startDateTime' => $companyPermission->start_datetime,
            'endDateTime' => $companyPermission->end_datetime,
        ];
    }
}
