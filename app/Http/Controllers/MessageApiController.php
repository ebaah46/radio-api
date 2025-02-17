<?php

namespace App\Http\Controllers;

use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Matrix\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
//use Aws\AwsClient;
use Illuminate\Support\Facades\Response as Download;

//require('vendor/autoload.php');
//require()


class MessageApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

//$s3 =  new AwsClient (['version'  => '2006-03-01', 'region'   => 'us-east-1',]);
    public function index()
    {
        // Retrieve all  messages in the DB
//        $audio_files =[];
//        $picture_files =[];
        try{
            $messages = Message::all();
            return response()->json([
                'status_code'=>200,
                'data'=>$messages,
                'error'=>null
            ],200);
        }catch (\Exception $exception){
            return response()->json([
                'status_code'=>500,
                'data'=>null,
                'error'=>$exception
            ],500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Store message files in db
        try{

            $details = $request->except('message_file','message_picture');
            $message = Message::query()->create($details);
            $audio = $request->message_file;

            $audio_name = $request->title . '_' . date('d:m:h') .'.mp3';
            $audio_name = str_replace(' ','_',$audio_name);

            $photo = $request->message_picture;
            $photo_name = $request->title . '_' . date('d:m:h') .'.jpg';
            $photo_name = str_replace(' ','_',$photo_name);
            $mp3= Storage::disk('s3')->putFileAs('audios',$audio,$audio_name);
            Storage::disk('s3')->setVisibility($mp3,'public');
            $pic = Storage::disk('s3')->putFileAs('photos',$photo,$photo_name);
            Storage::disk('s3')->setVisibility($pic,'public');
            $details['message_file']=Storage::disk('s3')->url($mp3);
            $details['message_picture']=Storage::disk('s3')->url($pic);
            $message->update($details);
            return  response()->json([
                'status_code'=>201,
                'data'=>'message successfully created'
            ],201);
        }catch (\Exception $exception){
            return response()->json([
                'status_code'=>500,
                'data'=>null,
                'error'=>$exception,
            ],500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        //Play a particular message
        $headers = array();
        $headers['Content-Type'] = 'audio/mpeg, audio/x-mpeg, audio/x-mpeg-3, audio/mpeg3';
//        $headers['Content-Length'] = $file->getSize();
        $headers['Content-Transfer-Encoding'] = 'binary';
        $headers['Accept-Range'] = 'bytes';
        $headers['Cache-Control'] = 'must-revalidate, post-check=0, pre-check=0';
        $headers['Connection'] = 'Keep-Alive';

        try {
                $message = Message::findOrFail($id);
                $file = Storage::disk('s3')->url($message->message_file);
                $play = new BinaryFileResponse($file);
                BinaryFileResponse::trustXSendfileTypeHeader();
                return $play;
        }catch (\Exception $exception){
            return response()->json([
                'status_code'=>206,
                'data'=>null,
                'error'=>$exception
            ],206);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public  function download($id)
    {
        // Download a particular message unto device

        try {
            $message = Message::findOrFail($id);


             $filename = $message->title.'.mp3';
             $tempMessage = tempnam(sys_get_temp_dir(), $filename);
             copy($message->message_file, $tempMessage);

            return response()->download($tempMessage, $filename)->deleteFileAfterSend(true);


        }catch (\Exception $exception){
            return response()->json([
                'status_code'=>500,
                'data'=>null,
                'error'=>$exception
            ],500);
        }
    }
//    Function to check PHP info
    public function checkInfo(){
//        Run the php info function
        $result = phpinfo();
        echo $result;
    }
}
