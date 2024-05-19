<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFeedbackRequest;
use App\Http\Requests\ResponseFeedbackRequest;
use App\Models\Feedback;
use http\Env\Response;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(CreateFeedbackRequest $request)
    {
        $data = $request->all();
        $data['customer_id'] = auth()->user()->customer->id;
        $feedback = Feedback::create($data);

        if($feedback){
            return response()->json($feedback, 201);
        }else{
            return response()->json($feedback, 400);
        }
    }

    public function index()
    {
        return response()->json(Feedback::with('product')->get());
    }

    public function postResponse(ResponseFeedbackRequest $request, $feedback_id)
    {
        $data = $request->all();
        $feedback = Feedback::where(['id' => $feedback_id])->update($data);
        if(isset($feedback)){
            return response()->json([
                'message'=>'response updated successfully '
            ]);
        }
        else{
            return \response()->json([
                'message'=>'failed to update '
            ]);
        }
        return response()->json($feedback);
    }

    public function toggleFeedback($id)
    {
        $feedback = Feedback::find($id);
        $feedback->status = !$feedback->status;
        $feedback->save();
        return response()->json(['status' => $feedback->status]);
    }
}
