<?php

namespace App\Http\Controllers;

use App\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Repository\Score;
class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        return Member::with(['purchases','cheques'])->get();
        return Member::with('purchases.cheques')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
//        return ;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $score = new Score('',$request);

        $member = new Member;
        $member->name       = $request->name;
        $member->last_name  = $request->last_name;
        $member->mobile     = $request->mobile;
        $member->password   = bcrypt(Member::generate_password());
        $member->birth_date = $request->birth_date;
        $member->score      = $score->total;

        $member->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Member::with('purchases.cheques')
                        ->where('id', $id)->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return Member::with('purchases.cheques')
            ->where('id', $id)->get();
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
        $score = new Score($id,$request);
        $member = Member::find($id);
        $member->name = $request->name;
        $member->last_name = $request->last_name;
        $member->mobile = $request->mobile;
        $member->password = bcrypt(Member::generate_password());
        $member->birth_date = $request->birth_date;
        $member->score = $score->total;
        $member->installed = 0;

        $member->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $member = Member::find($id);
        $member->delete();
    }

    public function restore($id)
    {
        Member::onlyTrashed()
            ->where('id', $id)
            ->restore();
    }


}
