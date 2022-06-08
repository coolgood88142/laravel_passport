<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trial;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TrialController extends Controller
{
    protected $source;
    protected $purpose;
    public function __construct()
    {
        $this->source =  [
            1 => "fb",
            2 => "google",
            3 => "朋友介紹",
            4 => "其他"
        ];

        $this->purpose = [
            1 => "公司使用",
            2 => "個人使用"
        ];

        $this->ortherSourceId = 4;
    }

    public function queryTrialData(Request $request){
        $queryCompany = $request->queryCompany;
        $queryUserName = $request->queryUserName;
        $queryEmail = $request->queryEmail;
        $queryPurpose = $request->queryPurpose;
        $querySource = $request->querySource;

        $where = [];
        $whereIn = [];
        $trail = [];

        if($queryCompany != null){
            array_push($where, ['company_name', 'like', $queryCompany]);
        }

        if($queryUserName != null){
            array_push($where, ['user_name', 'like', $queryUserName]);
        }

        if($queryEmail != null){
            array_push($where, ['email', 'like', $queryEmail]);
        }

        if($queryCompany != null){
            array_push($where, ['purpose_id', '=', $queryPurpose]);
        }

        if(count($where) > 0){
            $trail = Trial::where($where);
        }

        // $querySource = [
        //     1, 2
        // ];

        // dd($querySource);

        if($querySource != null){
            $trail = $trail->whereIn('source', $querySource)->get();
            dd($trail);
        }

        if(count($trail) == 0){
            $trail = Trial::all();
        }

        $newTrial = [];

        foreach($trail as $data){
            $now = [];
            $purposeText = '';
            if(array_key_exists($data->purpose_id, $this->purpose)){
                $purposeText = $this->purpose[$data->purpose_id];
            }

            $sourceText = [];
            $source = explode(',', $data->source);
            if(count($source) > 0){
                foreach ($this->source as $index => $value){
                    $text = '';
                    if(in_array($index, $source)){
                        if($index == $this->ortherSourceId){
                            $text = $value . ':' . $data->other_text;
                        }else{
                            $text = $value;
                        }
                        array_push($sourceText, $text);
                        continue;
                    }
                }
            }

            $now = [
                'company_name' => $data->company_name,
                'user_name' => $data->user_name,
                'email' => $data->email,
                'purpose_text' => $purposeText,
                'source_text' => $sourceText,
            ];

            array_push($newTrial, $now);
        }

        return view('queryTrial', [
            'trialData' => $newTrial,
            'purposeData' => $this->purpose,
            'sourceData' => $this->source,
        ]);
    }

    public function editTrialData(){
        return view('editTrial', [
            'sourceData' => $this->source,
            'ortherSourceId' => $this->ortherSourceId
        ]);
    }

    public function saveTrialData(Request $request){
        $validatedData = Validator::make($request->all(), [
            'inputCompany' => 'required',
            'inputUserName' => 'required',
            'inputEmail' => 'required',
            'purposeRadios' => 'required',
            'sourceCheckBoxs' => 'required',
            'otherText' => Rule::requiredIf(function () use ($request) {
                return $request->sourceCheckBoxs != null && in_array($this->ortherSourceId, $request->sourceCheckBoxs);
            }),
        ],
        [
            'inputCompany.required' => '請輸入公司名稱!',
            'inputUserName.required' => '請輸入姓名!',
            'inputEmail.required' => '請輸入Email!',
            'purposeRadios.required' => '至少選擇一個目的!',
            'sourceCheckBoxs.required' => '至少勾選一個消息來源!',
            'otherText.required' => '請輸入其他原因!',
        ]);

        if(count($validatedData->errors()->all()) == 0){
            $trial = new Trial();
            $trial->company_name = $request->inputCompany;
            $trial->user_name = $request->inputUserName;
            $trial->email = $request->inputEmail;
            $trial->purpose_id = $request->purposeRadios;

            $sourceData = '';
            foreach($request->sourceCheckBoxs as $source){
                $sourceData = $sourceData . $source . ',';
            }

            if($sourceData != ''){
                $trial->source = substr($sourceData, 0, -1);
            }

            $trial->other_text = $request->otherText;

            $trial->save();
            return redirect('queryTrial');
        }else{
            return redirect('/editTrial')->withErrors($validatedData)
            ->withInput();
        }
    }
}
