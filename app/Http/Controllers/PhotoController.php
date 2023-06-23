<?php

namespace App\Http\Controllers;

use App\Mail\MadeSaleMail;
use App\Mail\ReadyMail;
use App\Mail\ReferralsPayMail;
use App\Meta;
use App\Models\Employee;
use App\Models\PhotoEdit;
use App\Models\Photos;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Feature\Type;
use Jenssegers\Agent\Agent;

// putenv('GOOGLE_APPLICATION_CREDENTIALS=' . env('GOOGLE_APPLICATION_CREDENTIALS'));
// putenv('GOOGLE_APPLICATION_CREDENTIALS=E:\Project\Julian\Prophotos\prophotos-384402-ad19dbf596ad.json');
putenv('GOOGLE_APPLICATION_CREDENTIALS=/home/app.prophoto.ai/prophotos-384402-ad19dbf596ad.json');

class PhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $en_id = $request->en_id;
        Meta::addMeta('title', env('APP_NAME') . ' | HOME');
        return Inertia::render('AiPhoto/Create', [
            'csrfToken' => csrf_token(),
            'en_id' => $en_id
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $photos = $request->photos;
        $en_id = $request->en_id;
        $type = $request->type ?? "man";
        $eye = $request->eye ?? "brown";
        $retouch = $request->retouch ?? "0";
        $errPhotos = $request->errPhotos;

        $photosArr = json_decode($photos);

        if (count($photosArr) < 4 || count($photosArr) > 50) {
            redirect()->route('home')->withErrors(["message" => __('home.required', ['min' => 4, 'max' => 50])]);
        }

        $plan_id = auth()->user()->plan_id ?? 1;
        if (isset($en_id)) {
            $plan_id = 1;
        }

        $photo = Photos::create([
            'user_id' => auth()->user()->id,
            'image_urls' => $photos,
            'rejected_urls' => $errPhotos,
            'type' => $type,
            'eye' => $eye,
            'retouch' => $retouch,
            'plan_id' => $plan_id,
            'isFree' => auth()->user()->isFree,
            'employee_id' => $en_id
        ]);
        if (isset($en_id)) {
            return redirect()->route('photo.enterprise.processing', ['id' => $photo->id, 'en_id' => $en_id]);
        }
        return redirect()->route('photo.processing', $photo->id);
    }

    /**
     * Store a newly created resource in storage.
     * 
     */
    public function store(Request $request)
    {
        return Inertia::render('AiPhoto/Home');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, string $type = "result")
    {
        $photo = Photos::where('user_id', auth()->user()->id)->find($id);
        if (!isset($photo)) {

            if (auth()->user()->isAdmin()) {
                $photo = Photos::find($id);
            }
            if (auth()->user()->isEnterprise()) {
                $photo = Photos::find($id);
                $employee = Employee::find($photo->employee_id);
                if (!isset($employee) || $employee->enterprise_id != auth()->user()->id) {
                    $photo = null;
                }

            }
        }
        if (!isset($photo)) {
            if (auth()->user()->isEnterprise()) {
                return redirect()->route('enterprise.employees');
            }
            return redirect()->route('home');
        }
        if (!isset($photo->request_id)) {
            if (auth()->user()->isEnterprise()) {
                return redirect()->route('enterprise.employees');
            }
            return redirect()->route('photo.processing', $photo->id);
        }
        // if (!isset($photo->result_urls)) {
        //     return redirect()->route('photo.processing', $photo->id);
        // }
        if ($type == "result") {

            if(!auth()->user()->isAdmin() && ($photo->status == '4' || $photo->status == '5' || $photo->status == '6')) {
                return redirect()->back();
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('ASTRIA_KEY')
            ])->get(env('ASTRIA_DOMAIN') . '/tunes/' . $photo->request_id . '/prompts?offset=0');

            $jsonResponse = json_decode($response->body(), true);

            $result_urls = [];
            foreach ($jsonResponse as $key => $item) {
                $result_urls[] = $item['images'];
            }
            $result_urls = Arr::collapse($result_urls);

            if (count($result_urls) > 0) {
                foreach ($result_urls as $key => $value) {
                    // $result_urls[$key] = Str::replace('https://api.astria.ai/rails/active_storage/blobs/redirect/', 'http://localhost:3000/image/', $value);
                    $result_urls[$key] = Str::replace('https://api.astria.ai/rails/active_storage/blobs/redirect/', 'https://server.prophotos.ai/image/', $value);
                }
                $photo->result_urls = json_encode($result_urls);
                $photo->save();
            } else {
                return redirect()->route('photo.processing', $photo->id);
            }
            $images = json_decode($photo->result_urls);
        } else {
            if ($type == 'inputs') {
                $images1 = json_decode($photo->image_urls, true);
                $images2 = json_decode($photo->rejected_urls ?? "[]", true);
                $images = array_merge($images1, $images2 ?? []);
            } else if ($type == 'downloaded') {

            } else {
                $images = json_decode($photo->urls, true);
            }
        }
        $agent = new Agent();
        return Inertia::render('AiPhoto/Gallery', [
            'result' => $images,
            'type' => $type,
            'csrfToken' => csrf_token(),
            'id' => $photo->id,
            'canAdd' => $photo->add_more == 0,
            'isMobile' => $agent->isMobile(),
            'highRes' => $photo->highRes == 1,
            'message' => session('message'),
        ]);
    }

    public function showDownloaded(string $id)
    {
        $photos = Photos::where('user_id', $id)->get();
        $images = [];
        if (count($photos) > 0) {
            foreach ($photos as $key => $item) {
                $images[] = json_decode($item['downloaded']) ?? [];
            }
            $images = Arr::collapse($images);
        }
        $agent = new Agent();
        return Inertia::render('AiPhoto/Gallery', [
            'result' => $images,
            'type' => 'downloads',
            'csrfToken' => csrf_token(),
            'isMobile' => $agent->isMobile()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id, string $index)
    {
        $photo = Photos::find($id);
        if (!isset($photo) || !isset($photo->result_urls)) {
            return back();
        }
        $imgs = json_decode($photo->result_urls);
        $original_img = Arr::first($imgs, function ($value, $key) use ($index) {
            return Str::contains($value, $index);
        });
        $editPhoto = PhotoEdit::where('user_id', auth()->user()->id)->where('original_img', 'like', "%$index%")->first();
        if (!isset($editPhoto)) {
            $editPhoto = PhotoEdit::create([
                'user_id' => auth()->user()->id,
                'original_img' => $original_img,
                'bg_custom_status' => 0,
                'photo_id' => $id
            ]);
        }
        return Inertia::render('AiPhoto/Edit', [
            'editPhoto' => $editPhoto,
            'csrfToken' => csrf_token(),
        ]);
    }

    public function edit_photo()
    {
        auth()->user()->increase_click_button();
        return Inertia::render('AiPhoto/RemoveBg', [
            'photos' => auth()->user()->photos,
            'photoEdit' => auth()->user()->photoEdit,
            'csrfToken' => csrf_token(),
        ]);
    }

    public function removeBg(Request $request)
    {
        $id = $request->id;
        $editPhoto = PhotoEdit::where('user_id', auth()->user()->id)->find($id);
        if (!isset($editPhoto)) {
            return response()->json([
                'message' => 'Bad Request!'
            ], 400);
        }
        if (isset($editPhoto->bg_removed_img)) {
            return response()->json([
                'path' => $editPhoto->bg_removed_img
            ], 400);
        }
        $filename = uniqid('', true) . '.jpg';
        $user = auth()->user();
        Storage::disk('local')->put($filename, file_get_contents($editPhoto->original_img));

        $client = new Client([
            'base_uri' => 'https://clipdrop-api.co',
            'headers' => [
                'x-api-key' => env('CLIP_DROP_KEY'),
            ],
        ]);

        $response = $client->request('POST', 'remove-background/v1', [
            'multipart' => [
                [
                    'name' => 'image_file',
                    'contents' => fopen(Storage::path($filename), 'r'),
                ],
            ],
        ]);

        Storage::delete($filename);

        $resultFilename = uniqid('rm_bg_' . $user->id . '_', true) . '.png';
        Storage::makeDirectory('public/removeBG/' . $user->name . $user->id);
        Storage::disk('public')->put('removeBG/' . $user->name . $user->id . '/' . $resultFilename, $response->getBody());
        $editPhoto->bg_removed_img = '/storage/removeBG/' . $user->name . $user->id . '/' . $resultFilename;
        if (!auth()->user()->setCredit()) {
            return response()->json([
                'message' => __('plans.NoEnough'),
            ], 400);
        }
        $editPhoto->save();
        return response()->json([
            'path' => $editPhoto->bg_removed_img,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Upload photo
     */
    public function upload(Request $request)
    {
        $mineType = $request->file('file')->getMimeType();
        if(!in_array($mineType, ['image/jpeg', 'image/png', 'image/webp'])){
            return response()->json([
                "message" => __('res.UnsupportedType'),
            ], 400);
        }
        $imageFile = Image::make($request->file('file')->getRealPath());
        $imageFile->orientate();
        $iw = $imageFile->width();
        $ih = $imageFile->height();

        // Make upload directory.
        Storage::makeDirectory('public/uploads/inputs');
        Storage::makeDirectory('public/uploads/cropped');

        $fileName = 'bezkoder-' . auth()->user()->id . '-' . uniqid() . '-' . time() . '.jpg';
        $path = storage_path('app/public/uploads/inputs/' . $fileName);
        $imageFile->save($path);

        if ($iw < 150 || $ih < 150) {
            return response()->json([
                "message" => __('res.LowQualityError'),
                "path" => $fileName
            ], 400);
        }

        $client = new ImageAnnotatorClient();
        // Call the Cloud Vision API to detect faces in the image
        $result = $client->annotateImage(file_get_contents($path), [Type::FACE_DETECTION]);

        // Get the first detected face
        $faces = $result->getFaceAnnotations();
        if (count($faces) == 1) {
            $face = $faces[0];
        } else if (count($faces) == 0) {
            return response()->json([
                "message" => __('res.LowQualityError'),
                "path" => $fileName
            ], 400);
        } else {
            return response()->json([
                "message" => __('res.TooManyFaceError'),
                "path" => $fileName
            ], 400);
        }
        // Crop the image based on the face bounds
        $vertices = $face->getBoundingPoly()->getVertices();
        ;
        $x1 = $vertices[0]->getX();
        $y1 = $vertices[0]->getY();
        $x2 = $vertices[2]->getX();
        $y2 = $vertices[2]->getY();

        $w = $x2 - $x1;
        $h = $y2 - $y1;
        $x1 -= intval($w / 2);
        $y1 -= intval($h / 4);
        $w = 2 * $w;
        $h = intval(2 * $h);
        if ($x1 < 0)
            $x1 = 0;
        if ($y1 < 0)
            $y1 = 0;
        if ($w + $x1 > $iw)
            $w = $iw - $x1;
        if ($h + $y1 > $ih)
            $h = $ih - $y1;

        $imageFile->crop($w, $h, $x1, $y1);
        // // Save the cropped image to a file

        // $path = $imageFile->storeAs('public/uploads', $fileName);
        $path = storage_path('app/public/uploads/cropped/' . $fileName);
        $imageFile->save($path);

        // $response = Http::withHeaders([
        //     'Content-Type' => 'application/json',
        // ])->post('http://localhost:5001/' . 'vetorembedding', [
        //     'path' => $path,
        // ]);
        // $resData = $response->json();
        $path = $fileName;
        return response()->json(['path' => $path]);
        // , 'embedding' => $resData['embedding']
    }

    public function bg_upload(Request $request)
    {
        $id = $request->id;

        // Make upload directory.
        Storage::makeDirectory('public/uploads/background/');

        $fileName = 'bg-' . auth()->user()->id . '-' . uniqid() . '-' . time() . '.jpg';
        if ($request->file()) {
            $path = $request->file('file')->storeAs('uploads/background', $fileName, 'public');
            $path = '/storage/' . $path;
            $ePhoto = PhotoEdit::where('user_id', auth()->user()->id)->find($id);
            if (isset($ePhoto)) {
                $eixstPath = 'public/uploads/background/' . basename($ePhoto->bg_custom);
                if (Storage::exists($eixstPath ?? 'removed')) {
                    Storage::delete($eixstPath);
                }
                $ePhoto->bg_custom = $path;
                $ePhoto->save();
            }
        }
        return response()->json(['path' => $path]);
    }

    public function bg_status(Request $request)
    {
        $id = $request->id;
        $status = $request->status;

        $ePhoto = PhotoEdit::where('user_id', auth()->user()->id)->find($id);

        if (isset($ePhoto)) {
            $ePhoto->bg_custom_status = $status;
            $ePhoto->save();
        }
        return response()->json(['status' => $status]);
    }

    public function save_edit(Request $request)
    {
        $id = $request->id;
        $json_data = $request->json_data;
        $editPhoto = PhotoEdit::where('user_id', auth()->user()->id)->find($id);
        if (isset($editPhoto)) {
            $editPhoto->edit_img = $json_data;
            $editPhoto->save();
        }
        return response()->json(['status' => 'success']);
    }
    public function delete(Request $request)
    {
        $path = $request->path;
        if (Storage::exists($path)) {
            Storage::delete($path);
        }
        return response()->json(['success' => true]);
    }

    public function processing(Request $request)
    {
        $id = $request->id;
        $en_id = $request->en_id;
        $photo = Photos::where('user_id', auth()->user()->id)->find($id);
        if (!isset($photo)) {
            if (isset($en_id)) {
                return redirect()->route('user.enterprises.upload', $en_id);
            }
            return redirect()->route('home');
        }
        if (!isset($photo->request_id)) {
            $negativePrompt = '(deformed iris, deformed pupils, semi-realistic, cgi, 3d, render, sketch, cartoon, drawing, anime:1.4), text, close up, cropped, out of frame, worst quality, low quality, jpeg artifacts, ugly, duplicate, morbid, mutilated, extra fingers, mutated hands, poorly drawn hands, poorly drawn face, mutation, deformed, blurry, dehydrated, bad anatomy, bad proportions, extra limbs, cloned face, disfigured, gross proportions, malformed limbs, missing arms, missing legs, extra arms, extra legs, fused fingers, too many fingers, long neck';

            $formData = [
                'tune' => [
                    'title' => auth()->user()->name . ' Tune',
                    'branch' => 'sd15',
                    // 'branch' => 'fast',
                    'token' => 'sks',
                    'name' => $photo->type,
                    'base_tune_id' => 444827,
                    'callback' => env('ASTRIA_CALLBACK_URL') . "created-tune/" . $id
                ]
            ];

            $addSetting = "";
            if($photo->eye != 'brown') {
                $addSetting = ", with " . $photo->eye . " eyes";
            }

            $images = json_decode($photo->image_urls);
            $formData['tune']['image_urls'] = [];
            foreach ($images as $image) {
                $formData['tune']['image_urls'][] = config('app.url') . 'storage/uploads/cropped/' . $image;
            }

            $count = 10;
            $num_images = 8;
            if ($photo->plan_id == 3) {
                $count = 15;
            }
            if ($photo->plan_id == 1 && (!auth()->user()->isEnterprise() || !isset(auth()->user()->employee_id) || empty(auth()->user()->employee_id))) {
                $num_images = 4;
            }

            $more = [
                [
                    'text' => 'analog style, professional portrait of sks ' . $photo->type . ', epic (photo, studio lighting, hard light, sony a7, 50 mm, matte skin, pores, colors, hyperdetailed, hyperrealistic)' . $addSetting,
                    'negative_prompt' => $negativePrompt,
                    'num_images' => $num_images,
                    'face_correct' => true,
                    'super_resolution' => true,
                    'hires_fix' => true,
                    'callback' => env('ASTRIA_CALLBACK_URL') . "prompt_created/$id/$count/" . auth()->user()->id
                ],
                [
                    'text' => 'closeup professional portrait of sks ' . $photo->type . ' in Biome reserves, Flash-forward lighting, Vintage atmosphere, round face, wearing professional clothing' . $addSetting,
                    'negative_prompt' => $negativePrompt,
                    'num_images' => $num_images,
                    'face_correct' => true,
                    'super_resolution' => true,
                    'hires_fix' => true,
                    'callback' => env('ASTRIA_CALLBACK_URL') . "prompt_created/$id/$count/" . auth()->user()->id
                ],
                [
                    'text' => 'close up professional shot photo of sks ' . $photo->type . ' in analog style . dramatic . ( sunlight ) lighting highly detailed, sharp focus on eyes, intact eyes, symmetrical eyes, realistic skin texture, sharp face, detailed hair, symmetrical, intricate details, elegant, 8k high definition. Strong bokeh. Wearing professional clothing' . $addSetting,
                    'negative_prompt' => $negativePrompt,
                    'num_images' => $num_images,
                    'face_correct' => true,
                    'super_resolution' => true,
                    'hires_fix' => true,
                    'callback' => env('ASTRIA_CALLBACK_URL') . "prompt_created/$id/$count/" . auth()->user()->id
                ],
                [
                    'text' => 'close up studio portrait of a sks ' . $photo->type . ', wearing a suit, color, Martin Schoeller, serious eyes, perfect eyes, cinematic, 80mm portrait photography, dramatic lighting photography, national geographic, portrait, photo, photography, Stoic, cinematic, 4k, epic, detailed photograph, shot on kodak detailed, bokeh, cinematic, hbo, dark moody, volumetric fog' . $addSetting,
                    'negative_prompt' => $negativePrompt,
                    'num_images' => $num_images,
                    'face_correct' => true,
                    'super_resolution' => true,
                    'hires_fix' => true,
                    'callback' => env('ASTRIA_CALLBACK_URL') . "prompt_created/$id/$count/" . auth()->user()->id
                ],
                [
                    'text' => 'Close up portrait photo of a sks ' . $photo->type . ', wearing a suit, color, Martin Schoeller, serious eyes, perfect eyes, cinematic, 80mm portrait photography, dramatic lighting photography, national geographic, portrait, photo, photography, Stoic, cinematic, 4k, epic, detailed photograph, shot on kodak detailed, bokeh, cinematic, hbo, dark moody, volumetric fog' . $addSetting,
                    'negative_prompt' => $negativePrompt,
                    'num_images' => $num_images,
                    'face_correct' => true,
                    'super_resolution' => true,
                    'hires_fix' => true,
                    'callback' => env('ASTRIA_CALLBACK_URL') . "prompt_created/$id/$count/" . auth()->user()->id
                ],
            ];

            $formData['tune']['prompts_attributes'] = [
                [
                    'text' => 'portrait photo of a sks ' . $photo->type . ', color, Martin Schoeller, cinematic, 80mm portrait photography, national geographic, portrait, photo, photography, cinematic, 4k, epic, detailed photograph, shot on kodak detailed, bokeh, cinematic, hbo, volumetric fog' . $addSetting,
                    'negative_prompt' => $negativePrompt,
                    'num_images' => $num_images,
                    'face_correct' => true,
                    'super_resolution' => true,
                    'hires_fix' => true,
                    'callback' => env('ASTRIA_CALLBACK_URL') . "prompt_created/$id/$count/" . auth()->user()->id
                ], // #3
                [
                    'text' => 'RAW photo, a close up professional portrait photo of sks ' . $photo->type . ', background is an office, wearing a blazer, (high detailed skin:1.2), 8k uhd, dslr, soft lighting, high quality, film grain, Fujifilm XT3' . $addSetting,
                    'negative_prompt' => $negativePrompt,
                    'num_images' => $num_images,
                    'face_correct' => false,
                    'super_resolution' => true,
                    'hires_fix' => true,
                    'callback' => env('ASTRIA_CALLBACK_URL') . "prompt_created/$id/$count/" . auth()->user()->id
                ], // #9
                [
                    'text' => 'RAW photo, a close up professional portrait photo of sks ' . $photo->type . ', background is office, wearing a suit, (high detailed skin:1.2), 8k uhd, dslr, soft lighting, high quality, film grain, Fujifilm XT3' . $addSetting,
                    'negative_prompt' => $negativePrompt,
                    'num_images' => $num_images,
                    'face_correct' => false,
                    'super_resolution' => true,
                    'hires_fix' => false,
                    'callback' => env('ASTRIA_CALLBACK_URL') . "prompt_created/$id/$count/" . auth()->user()->id
                ], // #8
                [
                    'text' => 'Face of sks ' . $photo->type . ' portrait, head and shoulders portrait, symmetrical face, 4k resolution, business attire, white background, soft lighting, head and shoulders, pretty, studio photo, sharp focus, highly detailed, looking into camera, detailed hair, passport, soft smooth skin, perfect composition, framing, photorealistic, art by aleksei vinogradov, close up portrait photo by Annie Leibovitz' . $addSetting,
                    'negative_prompt' => $negativePrompt,
                    'num_images' => $num_images,
                    'face_correct' => true,
                    'super_resolution' => true,
                    'hires_fix' => true,
                    'callback' => env('ASTRIA_CALLBACK_URL') . "prompt_created/$id/$count/" . auth()->user()->id
                ], // #5
                [
                    'text' => 'portrait photo of a sks ' . $photo->type . ', wearing a peacoat, color, Martin Schoeller, cinematic, 80mm portrait photography, national geographic, portrait, photo, photography, cinematic, 4k, epic, detailed photograph, shot on kodak detailed, bokeh, cinematic, hbo, volumetric fog' . $addSetting,
                    'negative_prompt' => $negativePrompt,
                    'num_images' => $num_images,
                    'face_correct' => true,
                    'super_resolution' => true,
                    'hires_fix' => true,
                    'callback' => env('ASTRIA_CALLBACK_URL') . "prompt_created/$id/$count/" . auth()->user()->id
                ], // #4
                [
                    'text' => 'A studio portrait of sks ' . $photo->type . ' wearing a blazer, headshot, intricate, elegant, HDR, UHD, 64k, highly detailed, studio lighting, professional, sharp focus, highest quality, trending on pinterest, Photograph by Ansel Adams' . $addSetting,
                    'negative_prompt' => $negativePrompt,
                    'num_images' => $num_images,
                    'face_correct' => true,
                    'super_resolution' => true,
                    'hires_fix' => true,
                    'callback' => env('ASTRIA_CALLBACK_URL') . "prompt_created/$id/$count/" . auth()->user()->id
                ], // #2
                [
                    'text' => 'A studio portrait of sks ' . $photo->type . ' wearing a peacoat, Wide Angle, intricate, elegant, HDR, UHD, 64k, highly detailed, studio lighting, professional, sharp focus, highest quality' . $addSetting,
                    'negative_prompt' => $negativePrompt,
                    'num_images' => $num_images,
                    'face_correct' => true,
                    'super_resolution' => true,
                    'hires_fix' => true,
                    'callback' => env('ASTRIA_CALLBACK_URL') . "prompt_created/$id/$count/" . auth()->user()->id
                ], // #1
                [
                    'text' => 'RAW photo, a close up professional portrait photo of sks ' . $photo->type . ', background is a photo studio, wearing a blazer, (high detailed skin:1.2), 8k uhd, dslr, soft lighting, high quality, film grain, Fujifilm XT3' . $addSetting,
                    'negative_prompt' => $negativePrompt,
                    'num_images' => $num_images,
                    'face_correct' => false,
                    'super_resolution' => true,
                    'hires_fix' => true,
                    'callback' => env('ASTRIA_CALLBACK_URL') . "prompt_created/$id/$count/" . auth()->user()->id
                ], // #7
                [
                    'text' => 'RAW photo, a close up professional portrait photo of sks ' . $photo->type . ', background is a photo studio, wearing a peacoat, (high detailed skin:1.2), 8k uhd, dslr, soft lighting, high quality, film grain, Fujifilm XT3' . $addSetting,
                    'negative_prompt' => $negativePrompt,
                    'num_images' => $num_images,
                    'face_correct' => false,
                    'super_resolution' => true,
                    'hires_fix' => false,
                    'callback' => env('ASTRIA_CALLBACK_URL') . "prompt_created/$id/$count/" . auth()->user()->id
                ], // #6
                [
                    'text' => 'sks ' . $photo->type . ' professional portrait, symmetrical face, headshot,intricate,elegant,highly detailed,8k, sharp focus, studio lighting, photo realistic style, full color, successful, professional' . $addSetting,
                    'negative_prompt' => $negativePrompt,
                    'num_images' => $num_images,
                    'face_correct' => true,
                    'super_resolution' => true,
                    'hires_fix' => true,
                    'callback' => env('ASTRIA_CALLBACK_URL') . "prompt_created/$id/$count/" . auth()->user()->id
                ], // #10
            ];

            if ($photo->plan_id == '3') {
                $formData['tune']['prompts_attributes'] = array_merge($more, $formData['tune']['prompts_attributes']);
                $photo->add_more = 1;
                $photo->save();
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('ASTRIA_KEY')
            ])->post(env('ASTRIA_DOMAIN') . '/tunes', $formData);

            $jsonResponse = json_decode($response->body(), true);
            // dd($jsonResponse);
            // Log::info($jsonResponse);
            if (!isset($jsonResponse['id'])) {
                Photos::find($photo->id)->delete();
                return redirect()->route('home')->withErrors(["message" => "Please use correct images."]);
            }
            $photo->request_id = $jsonResponse['id'];
            $photo->urls = $jsonResponse['orig_images'];
            $photo->save();
            $croppedImages = json_decode($photo->image_urls);
            $junkFiles = [];
            foreach ($croppedImages as $img) {
                $junkFiles[] = 'public/uploads/cropped/' . $img;
            }
            Storage::delete($junkFiles);
            if (isset($photo->employee_id)) {
                $enterprise = Employee::find($photo->employee_id);
                if ($enterprise->credit > 0) {
                    $enterprise->credit -= 1;
                    $enterprise->generated += 1;
                    $enterprise->save();
                } else {
                    return redirect()->route('user.enterprises');
                }
            } else {
                if (isset(auth()->user()->plan)) {
                    $price = auth()->user()->plan->price;
                } else {
                    $price = 25;
                }
                if (auth()->user()->isDiscount()) {
                    $price *= 0.8;
                    // $photo->use_promo = 1;
                    // $photo->save();
                }
                auth()->user()->spent += $price;
                if (isset(auth()->user()->plan_id)) {
                    auth()->user()->plan_id = null;
                    auth()->user()->isFree = 0;
                } else if (auth()->user()->available_employee > 0) {
                    auth()->user()->available_employee -= 1;
                }
            }
            auth()->user()->count += 1;
            auth()->user()->save();
            // Log::info(env('ASTRIA_CALLBACK_URL') . "prompt_created/" . $id);
        }
        return Inertia::render('AiPhoto/Loading', [
            'photo' => $photo
        ]);
    }

    public function allPhotos(Request $request)
    {
        $photos = auth()->user()->photos()->with('plan')->orderBy('created_at', 'DESC')->get();
        return Inertia::render('AiPhoto/Lists', compact('photos'));
    }

    public function allUserPhotos(Request $request)
    {
        $user = User::find($request->id);
        $photos = Photos::where('user_id', $request->id)->with('plan')->orderBy('created_at', 'DESC')->get();
        return Inertia::render('AiPhoto/Lists', compact('photos', 'user'));
    }
    public function allEmployeePhotos(Request $request)
    {
        $employee = Employee::where('status', 1)->with('user')->find($request->id);
        if (!isset($employee)) {
            return redirect()->route('enterprise.employees');
        }
        $photos = Photos::with('plan')->where('user_id', $employee->user->id)->where('employee_id', $employee->id)->orderBy('created_at', 'DESC')->get();
        $user = $employee->user;
        return Inertia::render('AiPhoto/Lists', compact('photos', 'user'));
    }

    public function ready(Request $request)
    {
        $id = $request->id;
        $flag = $request->add ?? 0;
        $photo = Photos::with('user')->find($id);
        if (!isset($photo)) {
            return response()->json(['message' => 'Error']);
        }

        Mail::to($photo->user)->send(new ReadyMail($id, $flag));
        if (!isset($photo->user->utm_medium) && !isset($photo->user->utm_campaign) && isset($photo->user->utm_content) && isset($photo->user->utm_source)) {
            $refUser = User::whereRaw("REPLACE(name, ' ', '') = '{$photo->user->utm_source}'")->find($photo->user->utm_content);
            if (isset($refUser)) {
                Mail::to($refUser)->send(new MadeSaleMail($refUser));
            }
        }
    }

    public function downloaded(Request $request)
    {
        $id = $request->id;
        $url = $request->url;
        $arr = explode("/", $url);
        $len = count($arr);
        $user = auth()->user();
        $photo = Photos::where('user_id', $user->id)->find($id);
        if (isset($photo)) {
            $downs = json_decode($photo->downloaded) ?? [];
            $filename = 'ai-' . $arr[$len - 2] . '-' . $arr[$len - 1];
            $path = 'storage/downloads/' . $user->name . $user->id . '/' . $filename;
            if (!in_array($path, $downs)) {
                if ($photo->highRes == 0) {
                    Storage::makeDirectory('public/downloads/' . $user->name . $user->id);
                    Storage::disk('public')->put('downloads/' . $user->name . $user->id . '/' . $filename, file_get_contents($url));
                } else {
                    $path = $url;
                }
                $downs[] = $path;
                $photo->downloaded = json_encode($downs);
                $photo->save();
            }
        }
        return response()->json(['message' => 'Successful']);
    }

    public function referrals(Request $request)
    {
        $user = auth()->user();
        $totalSale = 0;
        $totalPay = 0;
        $pendingPay = 0;
        $earningPay = $user->ref_earn;
        $currentPay = 0;
        $refundedPay = 0;

        $total1 = User::with('photos.plan')->where('id', '!=', $user->id)->where('utm_content', $user->id)->where('utm_source', str_replace(' ', '', $user->name))->whereNull(['utm_medium', 'utm_campaign'])->where('id', '>', '2151')->get();
        $total2 = User::with('photos.plan')->where('id', '!=', $user->id)->where('utm_content', $user->id)->where('utm_source', '!=', 'enterprise')->where('id', '<', '2151')->get();
        $total = array_merge($total1->toArray(), $total2->toArray());
        $totalCount = count($total);
        foreach ($total as $item) {
            foreach ($item['photos'] as $photo) {
                if($photo['isFree'] == 1) continue;
                $totalSale++;
                $totalPay += $photo['plan']['price'];
                if ($photo['status'] == 1) {
                    $currentPay += $photo['plan']['price'] * 0.2;
                } else if ($photo['status'] == 2) {
                    $pendingPay += $photo['plan']['price'] * 0.2;
                } else if ($photo['status'] == 3) {
                    // $earningPay += $photo['plan']['price'] * 0.2;
                } else if ($photo['status'] == 4 || $photo['status'] == 5 || $photo['status'] == 6) {
                    $refundedPay += $photo['plan']['price'];
                }
                if ($photo['status'] == 5) {
                    $currentPay -= $photo['plan']['price'] * 0.2;
                }
                if ($photo['status'] == 6) {
                    $pendingPay -= $photo['plan']['price'] * 0.2;
                }
            }
        }

        return Inertia::render('Referrals/Dashboard', compact('totalCount', 'totalSale', 'totalPay', 'pendingPay', 'earningPay', 'currentPay', 'refundedPay'));
    }

    public function requestReferralPay(Request $request)
    {
        $user = auth()->user();
        $user_Ids1 = User::where('id', '!=', $user->id)->where('utm_content', $user->id)->where('utm_source', str_replace(' ', '', $user->name))->whereNull(['utm_medium', 'utm_campaign'])->where('id', '>', '2151')->pluck('id');
        $user_Ids2 = User::where('id', '!=', $user->id)->where('utm_content', $user->id)->where('utm_source', '!=', 'enterprise')->where('id', '<', '2151')->pluck('id');
        $user_Ids = array_merge($user_Ids1->toArray(), $user_Ids2->toArray());
        Photos::whereIn('user_id', $user_Ids)->where('status', 1)->update(['status' => 2]);
        Photos::whereIn('user_id', $user_Ids)->where('status', 5)->update(['status' => 6]);
        Mail::to('hello@prophotos.ai')->send(new ReferralsPayMail($user));
        return redirect()->route('referrals');
    }

    public function setHighRes(Request $request)
    {
        $id = $request->id;
        $photo = Photos::find($id);
        $photo->highRes = ($photo->highRes == 0) ? 1 : 0;
        $photo->save();
        return redirect()->back();
    }
}