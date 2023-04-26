<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Validator;

class UsersService extends AppService
{
    
    /**
     * get pagination data
     */
    public function getPagination(){
        // Get list
        $data = User::orderBy('created_at', 'DESC')->paginate(config('dashboard_constant.PAGINATION_LIMIT')); 
        return $this->paginationDataFormat($data->toArray());
    }
    /**
     * save data
     * @param array request
     */
    public function save($request){

        Validator::make($request->all(),[
            'first_name' => 'required|string|max:30',
            'last_name' => 'required|string|max:30',
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|string|email|max:60|unique:users',
            'password' => 'required|string|min:6|max:32|confirmed',

        ])->validate();
        

        // Create or Update 
        $dataObj =  new User;
        
        // $dataObj->id = strrev(strtotime(date("Y-m-d H:i:s")));
        $dataObj->id = $this->genUserId(); 
        $dataObj->first_name = $request->input('first_name');
        $dataObj->last_name = $request->input('last_name');
        $dataObj->username = $request->input('username');
        $dataObj->email = $request->input('email');
        $dataObj->designation = $request->input('designation');
        $dataObj->password = Hash::make($request->input('password'));
        $dataObj->status = 'A';
        $dataObj->type = 'AU';

        if($dataObj->save()) {
            return [
                config('msg_label.MSG_RESULT') => true,
                config('msg_label.MSG_MESSAGE') => "User Added Successfully!",
                config('msg_label.MSG_DATA') => $dataObj
            ];
           
        }
        return [
            config('msg_label.MSG_RESULT') => false,
            config('msg_label.MSG_MESSAGE') => "User Added Failed!",
            config('msg_label.MSG_DATA') => $dataObj
        ];
    }
    /**
     * GENERATE RANDOM USER ID
     */
    public function genUserId(){
        $id = $this->genRandNum(9);
        $usrExists = User::find($id); 
        if($usrExists){
            return $this->genUserId();
        }
        return $id;
    }

    /**
     * get details
     * $param int $id
     */
    public function getDetail($id){
        //Get detail
        return User::findOrFail($id); 

    }

    /**
     * update data
     * @param array request
     */
    public function updateData($request){
        Validator::make($request->all(),[
            'first_name' => 'required|string|max:30',
            'last_name' => 'required|string|max:30',
            'username' => 'required|string|max:50|unique:users,username,'.$request->id,
            'email' => 'required|string|email|max:60|unique:users,email,'.$request->id,
            'password' => 'string|min:6|max:32|confirmed',

        ])->validate();
        
        // get detail
        $dataObj = $this->getDetail($request->id);
        
        $dataObj->first_name = $request->input('first_name');
        $dataObj->last_name = $request->input('last_name');
        $dataObj->username = $request->input('username');
        $dataObj->email = $request->input('email');
        $dataObj->designation = $request->input('designation');
        $dataObj->type = $request->input('type');
        $dataObj->status = $request->input('status');
        if(!empty($request->input('password'))){
            $dataObj->password = Hash::make($request->input('password'));
        }

        if($dataObj->save()) {
            return [
                config('msg_label.MSG_RESULT') => true,
                config('msg_label.MSG_MESSAGE') => "User Update Successfully!",
                config('msg_label.MSG_DATA') => $dataObj
            ];
           
        }
        return [
            config('msg_label.MSG_RESULT') => false,
            config('msg_label.MSG_MESSAGE') => "User Update Failed!",
            config('msg_label.MSG_DATA') => $dataObj
        ];

    }

    /**
     * delete data
     * @param int $id
     */
    public function delete($id){
        $dataObj = $this->getDetail($id);
       
        if($dataObj->delete()) {
            return [
                config('msg_label.MSG_RESULT') => true,
                config('msg_label.MSG_MESSAGE') => "User Deleted Successfully!",
                config('msg_label.MSG_DATA') => $dataObj
            ];
        }
        return [
            config('msg_label.MSG_RESULT') => false,
            config('msg_label.MSG_MESSAGE') => "User Deleted Failed!",
            config('msg_label.MSG_DATA') => $dataObj
        ];
    }
    



}
