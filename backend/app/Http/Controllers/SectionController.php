<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Student;
use App\Models\Classes;
use Illuminate\Support\Facades\Validator;
class SectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function addSection(Request $request)
    {  
        $validated=Validator::make($request->all(), [
            'name' => 'required|string',
            'capacity' => 'required|numeric',
            "class_id" => "required|numeric"
        ]);
        if($validated->fails()){
            return response()->json(["message"=>$validated->errors()], 422);
        }
        $Section = new Section;
        $name = $request->input('name');
        $class_id = $request->input('class_id');
        $class = Classes::find($class_id);
        if(empty($class)){
            return response()->json([
                'message' => "Class doesn't exists",
            ], 400);
        }
        $capacity = $request->input('capacity');
        $Section->name = $name;
        $Section->capacity = $capacity;
        $Section->Class()->associate($class);
        $Section->save();
        return response()->json([
            'message' => 'Section created successfully!',
        ]);
    }


    public function getSection($id)
    {
        $Section =  Section::where('id', $id)->with(['Class'])->get();
        if($Section->isEmpty()){
            return response()->json([
                'message' => "Section doesn't exists",
         
            ], 400);
        }
        return response()->json([
            'message' => $Section,
        ]);
    }

    public function getSectionByname($name)
    {
        $Section = Section::where('name', $name)->with(['Class'])->paginate(10);
        if($Section->isEmpty()){
            return response()->json([
                'message' => "Section doesn't exists",
         
            ], 400);
        }
        return response()->json([
            'message' => $Section,
            'inpute' => $name,
        ]);
    }

    public function getAllSection(Request $request)
    {
        $Section =  Section::with(["Class"])->paginate(5);
        return response()->json([
            'message' => $Section,
        ]);
    }

    public function deleteSection(Request $request, $id)
    {
        $Section = Section::where("id",$id)->get();
        $Section2 = Section::find($id);
        if(empty($Section2)){
            return response()->json([
                'message' => "Section doesn't exists",
            ], 400);
        }
        $Sections = Student::where("section_id",$id)->get();
        if($Sections->isEmpty()){
            $Section2->delete();
        return response()->json([
            'message' => 'Section deleted Successfully || batata!',
        ]);
        }else{

            return response()->json([
                'message' => "You can't delete a section that contains students",
            ], 410);
        }
        
    }

    public function getSectionSortByName(Request $request){
        $Section =  Section::with(["Class"])->orderBy("name")->paginate(5);
        return response()->json([
            'message' => $Section,
        ]);
    }
    public function getSectionSortByClass(Request $request){
        $Section =  Section::with(["Class"])->orderBy("class.name")->paginate(5);
        return response()->json([
            'message' => $Section,
        ]);
    }
    public function getSectionSortByCapacity(Request $request){
        $Section =  Section::with(["Class"])->orderBy("capacity")->paginate(5);
        return response()->json([
            'message' => $Section,
        ]);
    }
    public function editSection(Request $request, $id)
    {
        $validated=Validator::make($request->all(), [
            'name' => 'string',
            'capacity' => 'numeric',
            "class_id" => "numeric"
        ]);
        if($validated->fails()){
            return response()->json(["message"=>$validated->errors()]);
        }
        $Section =  Section::find($id);
        if(empty($Section)){
            return response()->json([
                'message' => "Section doesn't exists",
            ], 400);
        }
        $inputs = $request->except('_method');
        $Section->update($inputs);
        return response()->json([
            'message' => 'Section edited successfully!',
            'Section' => $Section,
        ]);
    }
}
