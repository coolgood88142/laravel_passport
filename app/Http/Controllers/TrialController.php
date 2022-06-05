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

    public function queryTrialData(){
        return view('queryTrial', [
            'trialData' => Trial::all(),
            'sourceData' => $this->source,
            'purposeData' => $this->purpose,
            'ortherSourceId' => $this->ortherSourceId
        ]);
    }

    public function editTrialData(){
        return view('editTrial', [
            // 'inputCompany' => '',
            // 'inputUserName' => '',
            // 'inputEmail' => '',
            // 'purposeRadios' => [],
            // 'sourceCheckBoxs' => [],
            // 'otherText' => '',
            // 'errorCompany' => false,
            // 'errorUserName' => false,
            // 'errorEmail' => false,
            // 'errorPurposeRadios' => false,
            // 'errorSourceCheckBoxs' => false,
            // 'errorOtherText' => false
        ]);
    }

    public function saveTrialData(Request $request){
        $trial = new Trial();
        $errorEdit = false;

        $validatedData = Validator::make($request->all(), [
            'inputCompany' => 'required',
            'inputUserName' => 'required',
            'inputEmail' => 'required',
            'purposeRadios' => 'required',
            'sourceCheckBoxs' => 'required',
            // 'otherText' => 'required_if:sourceCheckBoxs.4'
            // 'otherText' => [
            //     'sourceCheckBoxs.4' => 'required',
            //     'text' => 'required'
            // ]
            'otherText' => Rule::requiredIf(function () use ($request) {
                return in_array($this->ortherSourceId, $request->sourceCheckBoxs);
            }),

            // 'otherText' =>  array(
            //     'sourceCheckBoxs' => 'required',
            //     'stext' => 'required_if:sourceCheckBoxs,4',
            // )

            //   'required_if:sourceCheckBoxs,4'
        ]);


        // $request->validate([
        //     'inputCompany' => ['required'],
        //     'inputUserName' => ['required'],
        //     'inputEmail' => ['required'],
        //     'purposeRadios' => ['required'],
        //     'sourceCheckBoxs' => ['required'],
        //     // 'otherText' => 'required_if:sourceCheckBoxs,4'
        // ]);

        // dd($validatedData);

        if($request->inputCompany != ''){
            $trial->company_name = $request->inputCompany;
        }else{
            $errorEdit = true;
        }

        if($request->inputUserName != ''){
            $trial->company_name = $request->inputUserName;
        }else{
            $errorEdit = true;
        }

        if($request->inputEmail != ''){
            $trial->user_name = $request->inputEmail;
        }else{
            $errorEdit = true;
        }

        if($request->purposeRadios != ''){
            $trial->purpose_id = $request->purposeRadios;
        }else{
            $request->purposeRadios = [];
            $errorEdit = true;
        }

        $sourceData = '';
        $errorOtherText = false;
        if($request->sourceCheckBoxs != '' && count($request->sourceCheckBoxs) > 0){
            foreach($request->sourceCheckBoxs as $source){
                $sourceData = $sourceData . $source . ',';

                if($source == $this->ortherSourceId && $request->otherText == ''){
                    $errorOtherText = true;
                    $errorEdit = true;
                }else{
                    $trial->other_text = $request->otherText;
                }
            }

            if($sourceData != ''){
                $sourceData = substr($sourceData, 0, -1);
                $trial->source = $request->sourceData;
            }
        }else{
            $request->sourceCheckBoxs = [];
            $errorEdit = true;
        }

        if(!$errorEdit){
            $trial->save();
        }else{
            return view('editTrial', [
                // 'inputCompany' => $request->inputCompany,
                // 'inputUserName' => $request->inputUserName,
                // 'inputEmail' => $request->inputEmail,
                // 'purposeRadios' => $request->purposeRadios,
                // 'sourceCheckBoxs' => $request->sourceCheckBoxs,
                // 'otherText' => $request->otherText,
                // 'errorCompany' => $request->inputCompany == '',
                // 'errorUserName' => $request->inputUserName == '',
                // 'errorEmail' => $request->inputEmail == '',
                // 'errorPurposeRadios' => $request->purposeRadios == [],
                // 'errorSourceCheckBoxs' => $request->sourceCheckBoxs == [],
                // 'errorOtherText' => $errorOtherText
            ]);
        }

        return route('queryTrial');
    }
}
