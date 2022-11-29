<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Mail\OtpMail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Mail;
use Validator;

class UserController extends Controller
{
    public $successStatus = 200;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fname' => 'required',
            'lname' => 'required',
            'phone' => 'required',
            'dob' => 'required',
            'img' => 'required',
            'designation' => 'required',
            'category' => 'required',
            'address' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $input = $request->all();
        if ($request->hasfile('img')) {
            $image1 = $request->file('img');
            $name = time() . 'img' . '.' . $image1->getClientOriginalExtension();
            $destinationPath = 'images/';
            $image1->move($destinationPath, $name);
            $input['img'] = 'images/' . $name;
        }
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->accessToken;
        $success['name'] = $user->fname . " " . $user->lname;
        return response()->json(['success' => $success], $this->successStatus);
    }

    public function details()
    {
        $user = Auth::user();
        $success = new UserCollection($user);
        return response()->json(['success' => $success], $this->successStatus);
    }

    public function sendOtpEmail(Request $request)
    {
        $otp = rand(1000, 9999);
        $user = User::where('email', '=', $request->email)->first();
        if ($user) {
            $user->otp = $otp;
            $user->update();
            $dataa = array(
                'otp' => $otp,
                'email' => $user->email,
            );
            Mail::to($user->email)->send(new OtpMail($dataa));
            return response()->json(['success' => 'Otp send'], 200);
        } else {
            return response()->json(['error' => 'User not exist'], 404);
        }
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $user->fname = $request->fname;
        $user->lname = $request->lname;
        $user->dob = $request->dob;
        $user->designation = $request->designation;
        $user->update();
        return response()->json(['success' => 'Successfully Updated']);
    }

    public function otpVerifyEmail(Request $request)
    {
        $user = User::where('email', $request->email)->where('otp', $request->otp)->first();
        if ($user) {
            $user->otp = null;
            $user->update();
            return response()->json(['success' => 'Otp Verify'], 200);
        } else {
            return response()->json(['success' => 'Otp Not match'], 404);
        }
    }

    public function forgetPassword(Request $request)
    {
        $user = User::where('email', '=', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->update();
        auth()->login($user, true);
        $success['token'] = $user->createToken('MyApp')->accessToken;
        $success['id'] = $user->id;
        return response()->json(['success' => $success], $this->successStatus);
    }

    public function login()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->accessToken;
            $success['id'] = $user->id;
            $success['name'] = $user->fname . " " . $user->lname;
            return response()->json(['success' => $success], $this->successStatus);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    public function profileImage(Request $request)
    {
        $user = Auth::user();
        if ($request->hasfile('img')) {
            $image1 = $request->file('img');
            $name = time() . 'img' . '.' . $image1->getClientOriginalExtension();
            $destinationPath = 'images/';
            $image1->move($destinationPath, $name);
            $user->img = 'images/' . $name;
        }
        if ($user->update()) {
            return response()->json(['success' => 'Successfully Updated']);
        } else {
            return response()->json(['error' => 'Something Happend Wrong'], 400);
        }
    }

    public function passwordUpdate(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $user->password = Hash::make($request->password);
        $user->update();
        return response()->json(['success' => 'Successfully Updated']);
    }

    public function employee()
    {
        $emp = User::all();
        $success = UserCollection::collection($emp);
        return response()->json(['success' => $success], $this->successStatus);
    }

    public function singleEmployee($id)
    {
        $user = User::find($id);
        if ($user) {
            $success = new UserCollection($user);
        } else {
            return response()->json(['error' => 'User not exist'], 404);
        }
        return response()->json(['success' => $success], $this->successStatus);
    }
    public function employeeSearch(Request $request)
    {
        $data = User::where('category', 'LIKE', '%' . $request->category . '%')
                ->where('address', 'LIKE', '%' . $request->location . '%')
                ->Where('lname', 'LIKE', '%' . $request->name . '%')
                ->orWhere('fname', 'LIKE', '%' . $request->name . '%')->get();
        $success = UserCollection::collection($data);
        return response()->json(['success' => $success], $this->successStatus);
    }

}
