<?php

namespace App\Http\Controllers;

use \Illuminate\Http\Request;

use App\NiceAction;
use App\NiceActionLog;
use DB;

class NiceActionController extends Controller{

	public function getHome(){
		$actions = NiceAction::all();
//		$actions = NiceAction::orderBy('niceness', 'desc')->get();
//		$actions = DB::table('nice_actions')->get();	//serves the same functionality as the line above this
		$logged_actions = NiceActionLog::all();
//		$logged_actions = NiceActionLog::whereHas('nice_action', function($query){
//                        $query->where('name', '=', 'Kiss');
//                })->get();
//		$query = DB::table('nice_action_logs')->join('nice_actions', 'nice_action_logs.nice_action_id', '=', 'nice_actions.id')->where('nice_actions.name', '=', 'Kiss')->get();
		$query = "";
//		$query = DB::table('nice_action_logs')
//				->insertGetId([
//					'nice_action_id' => DB::table('nice_actions')->select('id')->where('name', 'Hug')->first()->id
//				]);
		$hug = NiceAction::where('name', 'Hug')->first();
		if($hug){
			$hug->name = "Smile";
			$hug->update;
		}

		$wave = NiceAction::where('name', 'Wave')->first();
		if($wave){
			$wave->delete();
		}
//		$query = DB::table('nice_action_logs')->count();
		return view('home', ['actions' => $actions, 'logged_actions' => $logged_actions, 'db' => $query]);
	}

	public function getNiceAction($action, $name = null){
		if($name === null){
			$name = 'youu';
		}
		$nice_action = NiceAction::where('name', $action)->first();
		$nice_action_log = new NiceActionLog();
		$nice_action->logged_actions()->save($nice_action_log);
		return view('actions.nice', ['action' => $action, 'name' => $name]);
	}

	public function postNiceAction(Request $request){
		$this->validate($request, [
			'action' => 'required',
			'name' => 'required|alpha'
		]);
		return view('actions.nice', ['action' => $request['action'], 'name' => $this->transformName($request['name'])]);
	}

	public function postInsertNiceAction(Request $request){
                $this->validate($request, [
                        'name' => 'required|alpha|unique:nice_actions',
			'niceness' => 'required|numeric'
                ]);

		$action = new NiceAction();
		$action->name = ucfirst(strtolower($request['name']));
		$action->niceness = $request['niceness'];
		$action->save();

		$actions = NiceAction::all();

		return redirect()->route('home');
		return view('home', ['actions' => $actions]);
        }

	private function transformName($name){
		$prefix = 'KING';
		return $prefix.' '.strtoupper($name);
	}

}

?>
