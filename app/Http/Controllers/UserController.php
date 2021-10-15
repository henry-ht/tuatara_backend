<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $credentials = $request->only([
            'search',
            'total',
            'order_by',
        ]);

        $validation = Validator::make($credentials,[
            'search'        => 'sometimes|required|min:2|max:150',
            'total'         => 'sometimes|required|integer',
            'order_by'      => 'sometimes|required|in:ASC,DESC',
        ]);

        if (!$validation->fails()) {

            $response  = User::query();

            if (isset($credentials['search'])) {
                $search = '%'.$credentials['search'].'%';
                $response
                ->where('name', 'LIKE', $search);
            }

            if (isset($credentials['order_by'])) {
                $response->orderBy('name', $credentials['order_by']);
            }

            if (isset($credentials['total'])) {
                $response = $response->paginate($credentials['total']);
            }else{
                $response = $response->get();
            }

            $message    = ['message' => [__('List'), ]];
            $status     = 'success';
            $data       = $response;

        }else{
            $message    = $validation->messages();
            $status     = 'warning';
            $data       = false;

        }

        return response([
            'data'          => $data,
            'status'        => $status,
            'message'       => $message
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $credentials = $request->only([
            'name',
            'email',
        ]);

        $validation = Validator::make($credentials,[
            'name'              => 'required|max:150|min:10|string',
            'email'             => 'required|max:250|email|unique:users,email',
        ]);

        if (!$validation->fails()) {

                $credentials['password']    = bcrypt('password');
                $credentials['email_verified_token'] = Str::random(60);

                $newUser   = User::create($credentials);

                $message    = ['message' => [__('Successful registration')]];
                $status     = 'success';
                $data       = true;
        }else{
            $message    = $validation->messages();
            $status     = 'warning';
            $data       = false;
        }

        return response([
            'data'          => $data,
            'status'        => $status,
            'message'       => $message
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $message    = ['message' => [__('user'), ]];
        $status     = 'success';
        $data       = $user;

        return response([
            'data'          => $data,
            'status'        => $status,
            'message'       => $message
        ],200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $message    = ['message' => [__('Something is not right')]];
        $status     = 'warning';
        $data       = false;
        $notify     = true;

        $credentials = $request->only([
            'name',
            'email',
            'disabled'
        ]);

        $validation = Validator::make($credentials,[
            'name'      => 'sometimes|required|max:150|min:5|string',
            'email'     => 'sometimes|required|max:250|email|unique:users,email,'. $user->id,
            'disabled'  => 'sometimes|required|boolean',
        ]);

        if (!$validation->fails()) {
            $okUpdate   = false;

            foreach ($credentials as $key => $value) {
                if ($credentials[$key] == $user[$key]) {
                    unset($credentials[$key]);
                }
            }

            if(count($credentials)){
                $okUpdate = $user->fill($credentials)->save();
            }

            if ($okUpdate) {
                $message    = ['message' => [__('Update item')]];
                $status     = 'success';
                $data       = true;
            } else {

                if(count($credentials) && !$okUpdate){
                    $message    = ['message' => [__('Something is not right')]];
                }else{
                    $message    = ['message' => [__('Nothing new to update')]];
                }

                $status     = 'warning';
                $data       = false;
            }
        }else{
            $message    = $validation->messages();
            $status     = 'warning';
            $data       = false;

        }

        return response([
            'data'      => $data,
            'status'    => $status,
            'message'   => $message
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $okDelete = $user->delete();

        if ($okDelete) {
            $message    = ['message' => [__('Deleted item')]];
            $status     = 'success';
            $data       = false;
        } else {
            $message    = ['message' => [__('Something is not right')]];
            $status     = 'warning';
            $data       = false;
        }

        return response([
            'data'          => $data,
            'status'        => $status,
            'message'       => $message
        ],200);
    }
}
