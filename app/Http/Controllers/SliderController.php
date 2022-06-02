<?php

namespace App\Http\Controllers;
use App\Slider;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;




class SliderController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return Factory|Application|RedirectResponse|Response|View
     */


    public function index()
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
            return View('slider.index')->with("slides", Slider::all());
        }else{
            return redirect()->back();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|View
     */
    public function create()
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
        return View('slider.create');
    }else{
            return redirect()->back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
            $this->validate($request, [
                "name" => "required",
                "image" => 'required|image'
            ]);


            if (Slider::all()->isEmpty()) {
                $slide_last_id = 1;
            } else {
                $slide_last_id = intval(Slider::latest()->first()->id) + 1;
            }


            $image = $request->image;
            $new_name_image = $slide_last_id . '.' . $image->getClientOriginalExtension();
            $image->move("images/slider_image", $new_name_image);

            $slide = new Slider;
            $slide->name = $request->name;
            $slide->path = "/images/slider_image/" . $new_name_image;
            $slide->slug = str_slug($request->name);
            $slide->save();

            return View('slider.index')->with("slides", Slider::all());

        }else{
            return redirect()->back();
        }





    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
        $slide= Slider::find($id);
        return View('slider.edit')->with('slide',$slide);
    }else{
            return redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
            $this->validate($request, [
                "name" => "required"
            ]);

            $slide = Slider::find($id);

            if ($request->hasFile('image')) {


                $slide_id = intval(Slider::latest()->first()->id);

                $image = $request->image;
                $new_name_image = $slide_id . '.' . $image->getClientOriginalExtension();
                $image->move("images/slider_image", $new_name_image);
                $slide->path = "/images/slider_image/" . $new_name_image;

            }
            $slide->name = $request->name;
            $slide->save();

            return View('slider.index')->with("slides", Slider::all());
        }else{
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
        $slide= Slider::find($id);
        $slide->delete();
       // $trashedSlides= Slider::onlyTrashed()->get();
        //return(View('slider.trashed',['trashedSlides'=>$trashedSlides]));
        return View('slider.index')->with("slides", Slider::all());
    }else{
        }
    }
    public function trashed()
    {if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
        $slides = Slider::onlyTrashed()->get();
        return (View('slider.trashed', ['trashedSlides' => $slides]));
    }else{
        return redirect()->back();
    }
    }
    public function restore($id)
    {if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
        $slide = Slider::withTrashed()->where('id', $id)->first();
        $slide->restore();
        return redirect()->back();
    }else{
        return redirect()->back();
    }
    }
    public function hdelete($id)
    {if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
        $slide= Slider::withTrashed()->where('id',$id)->first();
        $slide_path=$slide->path;
        Storage::delete($slide_path);
        $slide->forceDelete();
        return View('slider.index')->with("slides",Slider::all());

    }else{
        return redirect()->back();
    }
    }
} 
