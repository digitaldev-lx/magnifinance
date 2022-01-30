<?php

namespace App\Http\Controllers\SuperAdmin;

use App\FrontFaq;
use App\Helper\Reply;
use App\Http\Controllers\SuperAdminBaseController;
use App\Language;
use Illuminate\Http\Request;

class FrontFaqSettingController extends SuperAdminBaseController
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->languages = Language::where('status', 'enabled')->get();
        return view('superadmin.front-faq-settings.create-modal', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $frontFaq = new FrontFaq();

        $frontFaq->language_id = $request->language;
        $frontFaq->question = $request->question;
        $frontFaq->answer   = clean($request->answer);
        $frontFaq->save();

        return Reply::success(__('messages.frontFaq.addedSuccess'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->faq = FrontFaq::find($id);
        $this->languages = Language::where('status', 'enabled')->get();
        return view('superadmin.front-faq-settings.edit-modal', $this->data);
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
        $frontFaq = FrontFaq::findOrFail($id);

        $frontFaq->language_id = $request->language;
        $frontFaq->question = $request->question;
        $frontFaq->answer   = clean($request->answer);
        $frontFaq->save();

        return Reply::success(__('messages.frontFaq.updatedSuccess'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        FrontFaq::destroy($id);
        return Reply::success(__('messages.frontFaq.deletedSuccess'));
    }

} /* end of class */
