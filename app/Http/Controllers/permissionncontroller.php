<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use  Spatie\Permission\Models\Role as Role;
use Illuminate\Support\Facades\Validator;
use App\Models\user;
use Illuminate\Support\Facades\DB;
class permissionncontroller extends Controller
{ 
    public function __construct(){

    }
public function createpermission(request $request){
    // dd($request);
    $validator= validator :: make($request->all(),[
        'permission'=>'required',
        'role'=>'required',
       
    ]);
    if($validator->fails()){
        $response = [
            'success' =>false,
            'message'  => $validator->errors()
        ];
        return response()->json($response,400);
    }
    else{
    $user_permission=$request->permission;

    $user_role=$request->role;
    $user=$role = role::create(['name'=>$user_role]);
    foreach($user_permission as $user_permission){
    $permission = permission::create(['name'=>$user_permission]);


$role->givepermissionTo([      
    $permission,
]);
    }
    $response=[
        'success'=> true,
        'roles' =>$user,
        
        'message'=> 'permission and role added suceefully'
    ];
    return response()->json($response,200);
}


}
         public function updatepermission($id,request $request){
            $i=0;
          $role_details= Role::find($id);
    $permission_id=DB::table('role_has_permissions')->where('role_id',$id)->pluck('permission_id');
  
          $permission_details= Permission::find($permission_id[$i]);
        //   dd($permissions);
    //     dd($permission_details->name);
    //   dd($role_details->name);
        //   dd($request->permission);
          if(!$role_details){
            return response()->json(["message"=>'role not found'],404);
          }
          if(!$permission_details){
            return response()->json(["message"=>'permission not found'],404);
          }
      
        //   dd($role);
        // dd( $permissions->name);
          $role_details->name =$request->role;
          $role_details->save();
       
          foreach($request->permission as $permission){
       if(isset( $permission_id[$i])){
          $permission_details->name = $request->permission;
          $permission_details->save();
       }
       else{
        $user_permission=$request->permission;
        foreach($user_permission as $user_permission){
            $permission = Permission::create(['name'=>$user_permission]);
        $role_details->givepermissionTo([      
            $permission,
        ]);
            }
       }
          }
        //   $permission_details->save();
        //   dd($permission_details);
        //   dd($permission);

$data=$permission_details;
          
        //   $permissions->save();
        return response()->json([
            'success' => true,
             'role' =>$role_details,
             'permission' => $data,
            //  'token'=> $token,
            'message'=>'role and permission data updated in table successfully'
           
        ]); 
       
}
public function destroy($id){
    $roles=role::find($id);
    // dd($roles);
        $roles->delete();
$permissions=DB::table('permissions')->where('id',$id)->delete();
return response()->json([
    'success' => true,
  
    'message' => 'Role and permission has been deleted',
    
]);
}
}

