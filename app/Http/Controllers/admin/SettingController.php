<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller{
  
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(){
    return redirect(action('admin\SettingController@update', ['id' => 1]));
  }
  
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create(){
    return view('admin.setting.setting-add');
  }
  
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request){
    
    if($request->file('image_upload') != null){
      $fileName = time().'.'.request()->image_upload->getClientOriginalExtension();
      $request->image_upload->storeAs('public/uploads', $fileName);
      $request->request->add(['image' => $fileName]);
    }
    
    (Auth::check()) ? $request->request->add(['user_id' => Auth::user()->id]) : '';
    
    $status = Setting::create($request->except(['_token', 'files', 0, 1, 'image_upload']));
    
    if($status->save()){
      return redirect(action("admin\SettingController@index"));
    }else{
      return abort(404);
    }
  }
  
  /**
   * Display the specified resource.
   *
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function show($id){
    $data = Setting::firstOrCreate(['user_id' => Auth::user()->id]);
    return view('admin.setting.setting-add', compact('data'));
  }
  
  /**
   * Show the form for editing the specified resource.
   *
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id){
    $data = Setting::where('id', $id)->firstOrFail();
    return view('admin.setting.setting-add', compact('data'));
  }
  
  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request $request
   * @param  int                      $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id){
    if($request->file('image_upload') != null){
      /*unlink previous file*/
      $preval = Setting::where('id', $id)->get();
      if($preval[ 0 ]->image !== null) Storage::delete('public\uploads\\'.$preval[ 0 ]->image);
      /*new file uploading */
      $fileName = time().'.'.request()->image_upload->getClientOriginalExtension();
      $request->image_upload->storeAs('public\uploads', $fileName);
      $request->request->add(['logo' => $fileName]);
    }
    
    $status = Setting::where('id', $id)->update($request->except(['_method', '_token', 'files', 0, 1, 'image_upload']));
    if($status){
      return redirect(action("admin\SettingController@index"));
    }else{
      return abort(404);
    }
  }
  
  /**
   * Remove the specified resource from storage.
   *
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  
  public function destroy($id){
    $delUser = Setting::where('id', $id)->firstOrFail();
    $delUser->delete();
    return redirect(action("admin\SettingController@index"));
  }
}
