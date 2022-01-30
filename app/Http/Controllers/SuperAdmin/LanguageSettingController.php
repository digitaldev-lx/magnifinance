<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Controllers\SuperAdminBaseController;
use App\Http\Requests\Language\StoreLanguage;
use App\Language;
use Barryvdh\TranslationManager\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class LanguageSettingController extends SuperAdminBaseController
{

    public function __construct()
    {
        parent::__construct();

        $this->langPath = base_path().'/resources/lang';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax())
        {
            $languages = Language::all();

            return datatables()->of($languages)
                ->addColumn('action', function ($row) {
                    $action = '<div class="text-right">';

                    if ($row->language_code !== 'en') {
                        $action .= '<a href="javascript:;" data-row-id="' . $row->id . '" class="btn btn-primary btn-circle edit-language"
                          data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

                        $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle delete-language-row"
                            data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
                    }
                    else {
                        $action .= '<span class="text-danger">'.__('modules.settings.language.defaultLanguageCannotBeModified').'</span>';
                    }

                    $action .= '</div>';

                    return $action;
                })
                ->editColumn('code', function ($row) {
                    return $row->language_code;
                })
                ->editColumn('name', function ($row) {
                    return ucfirst($row->language_name);
                })
                ->addColumn('status', function ($row) {
                    $checked = $row->status == 'enabled' ? 'checked' : '';
                    $disabled = ($this->settings->locale == $row->language_code) ? 'disabled' : '';
                    $disabledNote = ($this->settings->locale == $row->language_code) ? 'data-toggle="tooltip" data-original-title="'.__('modules.settings.language.statusDisabledNote').'"' : '';
                    return '<label class="switch">
                                <input '.$disabled.' class="lang_status" type="checkbox" ' . $checked
                    . ' value="active" data-lang-id="' . $row->id . '">
                                <span '.$disabledNote.' class="slider round"></span>
                            </label>';
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'status'])
                ->toJson();
        }

        return view('superadmin.language.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('superadmin.language.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreLanguage $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLanguage $request)
    {
        // check and create lang folder
        $langExists = File::exists($this->langPath.'/'.strtolower($request->language_code));

        if (!$langExists) {
            File::makeDirectory($this->langPath.'/'.strtolower($request->language_code));
        }

        $language = new Language();

        $language->language_name = ucfirst(strtolower($request->language_name));

        if($request->language_code === 'pt-BR' || $request->language_code === 'sr-Cryl' || $request->language_code === 'zh-CN' || $request->language_code === 'zh-TW'){
            $language->language_code = $request->language_code;
        }
        else {
            $language->language_code = strtolower($request->language_code);
        }

        $language->status = $request->status;

        $language->save();

        return Reply::success(__('messages.createdSuccessfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $language = Language::where('id', $id)->firstOrFail();
        return view('superadmin.language.edit', compact('language'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  StoreLanguage $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreLanguage $request, $id)
    {
        $language = Language::findOrFail($id);

        if ($language->language_code === 'en') {
            return Reply::error(__('modules.settings.language.defaultLanguageCannotBeModified'));
        }

        // check and create lang folder
        $langExists = File::exists($this->langPath.'/'.strtolower($request->language_code));

        if (!$langExists) {
            // update lang folder name
            File::move($this->langPath.'/'.$language->language_code, $this->langPath.'/'.strtolower($request->language_code));

            Translation::where('locale', $language->language_code)->get()->map(function ($translation) {
                $translation->delete();
            });
        }

        if ($language->language_code === $this->settings->locale) {
            $this->settings->locale = strtolower($request->language_code);
            $this->settings->save();

            $language->status = 'enabled';
        }
        else {
            $language->status = $request->status;
        }

        $language->language_name = ucfirst(strtolower($request->language_name));

        if($request->language_code === 'pt-BR' || $request->language_code === 'sr-Cryl' || $request->language_code === 'zh-CN' || $request->language_code === 'zh-TW'){
            $language->language_code = $request->language_code;
        }
        else {
            $language->language_code = strtolower($request->language_code);
        }

        $language->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $languages = Language::select('id', 'language_code', 'status')->get();

        $language = $languages->first(function ($language, $key) use ($id) {
            return $language->id == $id;
        });

        if ($language->language_code === 'en') {
            return Reply::error(__('modules.settings.language.defaultLanguageCannotBeModified'));
        }

        // change locale to default
        if ($this->settings->locale == $language->language_code) {
            $this->settings->locale = 'en';
            // enable status of english language
            $language = $languages->first(function ($language, $key) {
                return $language->language_code == 'en';
            });

            $language->status = 'enabled';
            $language->save();
        }

        $this->settings->save();

        Language::destroy($id);

        return Reply::success(__('messages.recordDeleted'));
    }

    public function changeStatus(Request $request, $id)
    {
        if (!$request->has('status')) {
            $request->request->add(['status' => 'disabled']);
        }

        $language = Language::findOrFail($id);

        $language->status = $request->status;

        if ($request->status == 'disabled' && $language->language_code == $this->settings->locale) {
            $this->settings->locale = 'en';
        }

        $language->save();
        $this->settings->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

}
