<?php
/**
 * Created by PhpStorm.
 * User: morty
 * Date: 12.07.16
 * Time: 10:46
 */

namespace App\Api\V1\Controllers;

use App\Api\V1\Controllers\BaseController as Controller;
use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller{

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $user = $this->auth->user();
        $resp = $user->toArray();
        if(!empty($user->profile))
            $resp = array_merge($resp, $user->profile->toArray());
        return $resp;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $this->auth->user();

        if(!empty($user->profile)){
            $user->profile->fill($request->all());
            $user->push();
        }else{
            $profile = new Profile($request->all());
            $user->profile()->save($profile);
        }
    }

    public function destroy(){
        $user = $this->auth->user();
        return $user->delete();
    }

    public function search($firstName, $lastName){

        return Profile::where('first_name', $firstName)
            ->where('last_name', $lastName)
            ->get();
    }

}