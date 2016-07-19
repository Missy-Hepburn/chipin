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

        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\UpdateResourceFailedException('Could not update profile user.', $validator->errors());
        }

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

    public function search(Request $request){

        $firstName = $request->get('first_name');
        $lastName = $request->get('last_name');

        if(empty($firstName) && empty($lastName))
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException();

        return $this->response->array(Profile::where('first_name', $firstName)
            ->where('last_name', $lastName)
            ->get()->toArray());
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
            'first_name' => 'required|min:2',
            'last_name' => 'required|min:2',
            'nationality' => 'required|size:2',
            'country' => 'required|size:2',
            'birthday' => 'date|after:28.11.1899',
            'photo_id' => 'exists:'.with(new \App\Models\File)->getTable().',id',
        ]);
    }

}